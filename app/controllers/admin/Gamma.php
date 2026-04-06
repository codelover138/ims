<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            admin_redirect('login');
        }

        $this->load->admin_model('gamma_form_model');
        $this->load->admin_model('gamma_document_model');
        $this->load->library('gamma_path_service');
        $this->load->library('gamma_document_runner');
        if (!$this->Owner && !$this->Admin) {
            $this->permission_details = $this->site->checkPermissions();
        }
    }

    protected function normalizeSubmittedValues(array $submitted)
    {
        unset($submitted['submit']);

        if (isset($submitted['gamma_tables']) && is_array($submitted['gamma_tables'])) {
            foreach ($submitted['gamma_tables'] as $table_number => $rows) {
                if (!is_array($rows)) {
                    unset($submitted['gamma_tables'][$table_number]);
                    continue;
                }

                $normalized_rows = array();
                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $normalized_rows[] = $row;
                }
                $submitted['gamma_tables'][$table_number] = $normalized_rows;
            }
        }

        return $submitted;
    }

    public function index()
    {
        if (!$this->Owner && !$this->Admin) {
            $p = $this->permission_details[0] ?? array();
            if (empty($p['gamma-workspace'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                admin_redirect('welcome');
            }
        }

        $user = $this->site->getUser();
        if (!$user) {
            $this->session->set_flashdata('error', 'Unable to load the logged in user.');
            admin_redirect('logout');
        }

        $this->gamma_path_service->ensureUserFolders($user->username);

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['logged_in_user'] = $user;
        $this->data['gamma_root_relative'] = $this->gamma_path_service->getUserRootRelativePath($user->username);
        $this->data['gamma_forms'] = $this->gamma_form_model->getFormsForUser($user->id);
        $this->data['generated_documents'] = $this->gamma_document_model->getOutputFilesForUser($user->id);

        if ($this->Owner || $this->Admin) {
            $this->data['can_manage_forms'] = true;
        } else {
            $p = $this->permission_details[0] ?? array();
            $this->data['can_manage_forms'] = !empty($p['gamma-forms']);
            $this->data['can_submit']   = !empty($p['gamma-submit']);
            $this->data['can_delete']   = !empty($p['gamma-delete']);
            $this->data['can_download'] = !empty($p['gamma-download']);
        }

        $bc = array(
            array('link' => admin_url('gamma'), 'page' => 'Gamma'),
        );
        $meta = array('page_title' => 'Gamma Workspace', 'bc' => $bc);
        $this->page_construct('gamma/dashboard', $meta, $this->data);
    }

    public function open_form($form_id = null)
    {
        $form_id = (int) $form_id;
        $user = $this->site->getUser();
        $form = $this->gamma_form_model->getFormById($form_id, $user->id);
        if (!$form) {
            $this->session->set_flashdata('error', 'Form not found for this user.');
            admin_redirect('gamma');
        }

        $absolute_path = $this->gamma_path_service->resolvePath($form->input_form_location, $user->username);
        if (!is_file($absolute_path)) {
            $this->session->set_flashdata('error', 'The input form file has not been generated yet.');
            admin_redirect('gamma');
        }

        $generated_form_html = (string) file_get_contents($absolute_path);
        $csrf_field = '<input type="hidden" name="'
            . $this->security->get_csrf_token_name()
            . '" value="'
            . $this->security->get_csrf_hash()
            . '">';
        $generated_form_html = preg_replace('/<form\b[^>]*>/i', '$0' . $csrf_field, $generated_form_html, 1);

        $this->data['form'] = $form;
        $this->data['generated_form_html'] = $generated_form_html;
        $bc = array(
            array('link' => admin_url('gamma'), 'page' => 'Gamma'),
            array('link' => '#', 'page' => $form->form_title),
        );
        $meta = array('page_title' => $form->form_title, 'bc' => $bc);
        $this->page_construct('gamma/open_form', $meta, $this->data);
    }

    public function submit_form($form_id = null)
    {
        if (!$this->Owner && !$this->Admin) {
            $p = $this->permission_details[0] ?? array();
            if (empty($p['gamma-submit'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                admin_redirect('gamma');
            }
        }

        $form_id = (int) $form_id;
        $user = $this->site->getUser();
        $form = $this->gamma_form_model->getFormById($form_id, $user->id);
        if (!$form) {
            $this->session->set_flashdata('error', 'Form not found for this user.');
            admin_redirect('gamma');
        }

        if (strtolower($this->input->server('REQUEST_METHOD')) !== 'post') {
            $this->session->set_flashdata('error', 'Invalid request method.');
            admin_redirect('gamma/open_form/' . $form_id);
        }

        $submitted = $this->normalizeSubmittedValues((array) $this->input->post(null, true));

        if (empty($submitted)) {
            $this->session->set_flashdata('error', 'No form values were submitted.');
            admin_redirect('gamma/open_form/' . $form_id);
        }

        $output_file_id = $this->gamma_document_model->createOutputFileLog($form->form_id, '');
        $this->gamma_document_model->createInputRecords($output_file_id, $submitted);
        $output = $this->gamma_document_runner->run($form, $user->username, $output_file_id, $submitted);
        $this->db->update('gamma_output_file_logs', array('output_file' => $output['relative_path']), array('output_file_id' => $output_file_id));

        $this->session->set_flashdata('message', 'Document generated successfully. <a href="' . admin_url('gamma/download/' . $output_file_id) . '">Download file</a>');
        admin_redirect('gamma');
    }

    public function download($output_file_id = null)
    {
        if (!$this->Owner && !$this->Admin) {
            $p = $this->permission_details[0] ?? array();
            if (empty($p['gamma-download'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                admin_redirect('gamma');
            }
        }

        $output_file_id = (int) $output_file_id;
        $user = $this->site->getUser();
        $log = ($this->Owner || $this->Admin)
            ? $this->gamma_document_model->getOutputFileLogById($output_file_id)
            : $this->gamma_document_model->getOutputFileLogForUser($output_file_id, $user->id);
        if (!$log) {
            show_404();
        }

        $absolute_path = $this->gamma_path_service->resolvePath($log->output_file);
        if (!is_file($absolute_path)) {
            show_404();
        }

        $this->load->helper('download');
        force_download($absolute_path, null);
    }

    public function delete_document($output_file_id = null)
    {
        if (!$this->Owner && !$this->Admin) {
            $p = $this->permission_details[0] ?? array();
            if (empty($p['gamma-delete'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                admin_redirect('gamma');
            }
        }

        $output_file_id = (int) $output_file_id;
        $user = $this->site->getUser();
        $log = ($this->Owner || $this->Admin)
            ? $this->gamma_document_model->getOutputFileLogById($output_file_id)
            : $this->gamma_document_model->getOutputFileLogForUser($output_file_id, $user->id);

        if (!$log) {
            $this->session->set_flashdata('error', 'Document not found.');
            admin_redirect('gamma');
        }

        if (!empty($log->output_file)) {
            $absolute_path = $this->gamma_path_service->resolvePath($log->output_file);
            if (is_file($absolute_path)) {
                unlink($absolute_path);
            }
        }

        $this->gamma_document_model->deleteOutputFileLog($output_file_id);
        $this->session->set_flashdata('message', 'Document deleted.');
        admin_redirect('gamma');
    }

    public function file_manager()
    {
        $bc = array(
            array('link' => admin_url('gamma'), 'page' => 'Gamma'),
            array('link' => '#', 'page' => 'File Manager'),
        );
        $meta = array('page_title' => 'Gamma File Manager', 'bc' => $bc);
        $this->page_construct('gamma/gamma_filemanager', $meta, $this->data);
    }
}
