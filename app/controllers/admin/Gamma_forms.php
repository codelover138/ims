<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_forms extends MY_Controller
{
    protected $recent_form_session_key = 'gamma_recent_form_id';

    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            admin_redirect('login');
        }

        if (!$this->Owner && !$this->Admin) {
            $gp = $this->site->checkPermissions();
            $p  = $gp[0] ?? array();
            if (empty($p['gamma-forms'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                admin_redirect('welcome');
            }
            $this->gamma_forms_permissions = $p;
        } else {
            $this->gamma_forms_permissions = null; // Owner/Admin — all allowed
        }

        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->library('gamma_path_service');
        $this->load->library('gamma_input_form_builder');
        $this->load->admin_model('gamma_form_model');
    }

    protected function normalizeCsvHeader($header)
    {
        $header = strtolower(trim((string) $header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        return trim($header, '_');
    }

    protected function uploadCsv($field_name)
    {
        $upload_path = 'assets/uploads/csv/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'csv',
            'max_size' => 2048,
            'encrypt_name' => true,
        );

        $this->upload->initialize($config);
        if (!$this->upload->do_upload($field_name)) {
            throw new RuntimeException($this->upload->display_errors('', ''));
        }

        $data = $this->upload->data();
        return $data['full_path'];
    }

    protected function readCsv($path)
    {
        if (($handle = fopen($path, 'r')) === false) {
            throw new RuntimeException('Unable to open uploaded CSV.');
        }

        $raw_rows = array();
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $raw_rows[] = $row;
        }
        fclose($handle);

        if (empty($raw_rows)) {
            throw new RuntimeException('The uploaded CSV is empty.');
        }

        if ($this->isVerticalKeyValueCsv($raw_rows)) {
            return array($this->parseVerticalKeyValueCsv($raw_rows));
        }

        $rows = array();
        $headers = array_shift($raw_rows);
        if (!$headers) {
            fclose($handle);
            throw new RuntimeException('The uploaded CSV is empty.');
        }

        $headers = array_map(array($this, 'normalizeCsvHeader'), $headers);
        foreach ($raw_rows as $row) {
            if (count(array_filter($row, function ($value) {
                return trim((string) $value) !== '';
            })) === 0) {
                continue;
            }

            $record = array();
            foreach ($headers as $index => $header) {
                $record[$header] = $row[$index] ?? '';
            }
            $rows[] = $record;
        }

        return $rows;
    }

    protected function isVerticalKeyValueCsv(array $rows)
    {
        if (count($rows) < 2) {
            return false;
        }

        foreach ($rows as $row) {
            $cells = array_values(array_filter($row, function ($value) {
                return trim((string) $value) !== '';
            }));

            if (empty($cells)) {
                continue;
            }

            if (count($cells) !== 2) {
                return false;
            }
        }

        return true;
    }

    protected function parseVerticalKeyValueCsv(array $rows)
    {
        $record = array();
        foreach ($rows as $row) {
            $cells = array_values(array_filter($row, function ($value) {
                return trim((string) $value) !== '';
            }));

            if (count($cells) !== 2) {
                continue;
            }

            $record[$this->normalizeCsvHeader($cells[0])] = trim((string) $cells[1]);
        }

        return $record;
    }

    protected function createSupportFiles($form)
    {
        $user = $this->site->getUserById($form->user_id);
        if (!$user) {
            return;
        }

        $files = array(
            $form->precedent_clause_location => "<!-- Precedent clauses for form {$form->form_id} -->\n",
            $form->document_creation_location => "<?php\n// Custom document generation logic for FormID {$form->form_id}.\n// Available variables: \$gamma_form, \$gamma_output_file_id, \$gamma_output_file_path, \$gamma_input_values.\n",
        );

        foreach ($files as $relative_path => $contents) {
            if (trim((string) $relative_path) === '') {
                continue;
            }

            $absolute_path = $this->gamma_path_service->resolvePath($relative_path, $user->username);
            if (!is_file($absolute_path)) {
                $this->gamma_path_service->ensureParentDirectory($absolute_path);
                file_put_contents($absolute_path, $contents);
            }
        }
    }

    protected function setRecentFormId($form_id)
    {
        $this->session->set_userdata($this->recent_form_session_key, (int) $form_id);
    }

    protected function getRecentFormId()
    {
        $form_id = (int) $this->session->userdata($this->recent_form_session_key);
        return $form_id > 0 ? $form_id : null;
    }

    public function index()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['gamma_forms'] = $this->gamma_form_model->getAllForms();
        $this->data['gfp'] = $this->gamma_forms_permissions;
        $bc = array(
            array('link' => admin_url('gamma'), 'page' => 'Gamma'),
            array('link' => admin_url('gamma_forms'), 'page' => 'Gamma Forms'),
        );
        $meta = array('page_title' => 'Gamma Forms', 'bc' => $bc);
        $this->page_construct('gamma/forms_index', $meta, $this->data);
    }

    protected function checkFormPermission($key)
    {
        if ($this->gamma_forms_permissions !== null && empty($this->gamma_forms_permissions[$key])) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            admin_redirect('gamma_forms');
        }
    }

    public function build_form()
    {
        $this->checkFormPermission('gamma-forms-import-form');

        if ($this->input->method() === 'post') {
            try {
                $form_title = $this->input->post('form_title', true);
                if (!$form_title) {
                    throw new RuntimeException('Form title is required.');
                }

                $fields = $this->input->post('fields', true);
                if (empty($fields) || !is_array($fields)) {
                    throw new RuntimeException('At least one form field is required.');
                }

                $user = $this->site->getUser();
                if (!$user) {
                    throw new RuntimeException('Unable to resolve user.');
                }

                // Auto-generate safe slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $form_title)));
                $unique_id = time(); // ensure uniqueness
                
                $form_data = [
                    'user_id' => $user->id,
                    'form_title' => $form_title,
                    'description' => $this->input->post('description', true),
                    'button_label' => 'Submit',
                    'input_form_location' => 'gamma_forms/inputs/' . $slug . '_' . $unique_id . '.php',
                    'precedent_clause_location' => 'gamma_forms/precedents/' . $slug . '_' . $unique_id . '.php',
                    'document_creation_location' => 'gamma_forms/generators/' . $slug . '_' . $unique_id . '.php',
                    'output_filename_base' => $slug,
                    'output_file_location' => 'documents/' . $slug . '_' . $unique_id . '/'
                ];

                // Create the form
                $form_id = $this->gamma_form_model->createForm($form_data);
                $form = $this->gamma_form_model->getFormById($form_id);

                // Create support files
                $this->createSupportFiles($form);

                // Insert fields
                $order = 1;
                foreach ($fields as $field) {
                    if (empty($field['input_label'])) continue;
                    
                    $input_data = [
                        'form_id' => $form_id,
                        'input_name' => strtolower(trim(preg_replace('/[^A-Za-z0-9_]+/', '_', $field['input_name'] ?? $field['input_label']))),
                        'input_label' => $field['input_label'],
                        'input_type' => $field['input_type'],
                        'allowed_input' => !empty($field['allowed_input']) ? $field['allowed_input'] : null,
                        'default_value' => !empty($field['default_value']) ? $field['default_value'] : null,
                        'table_number' => 0,
                        'table_row' => $order++,
                        'table_column' => 1
                    ];
                    $this->gamma_form_model->createFormInput($input_data);
                }

                // Generate input form PHP
                $inputs = $this->gamma_form_model->getInputsForForm($form_id);
                $html = $this->gamma_input_form_builder->build($form, $inputs, admin_url('gamma/submit_form/' . $form->form_id));
                $absolute_path = $this->gamma_path_service->resolvePath($form->input_form_location, $user->username);
                $this->gamma_path_service->ensureParentDirectory($absolute_path);
                file_put_contents($absolute_path, $html);

                $this->session->set_flashdata('message', 'Form built and generated successfully!');
                admin_redirect('gamma_forms');

            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                admin_redirect('gamma_forms/build_form');
            }
        } else {
            $this->data['error'] = $this->session->flashdata('error');
        }

        $bc = array(
            array('link' => admin_url('gamma_forms'), 'page' => 'Gamma Forms'),
            array('link' => '#', 'page' => 'Build Form'),
        );
        $meta = array('page_title' => 'Build Gamma Form', 'bc' => $bc);
        $this->page_construct('gamma/build_form', $meta, $this->data);
    }

    public function import_forms_csv()
    {
        $this->checkFormPermission('gamma-forms-import-form');
        if ($this->input->method() === 'post') {
            try {
                $csv_path = $this->uploadCsv('csv_file');
                $rows = $this->readCsv($csv_path);
                if (empty($rows)) {
                    throw new RuntimeException('No form rows were found in the CSV.');
                }

                $created = 0;
                $last_form_id = null;
                foreach ($rows as $row) {
                    $form_id = $this->gamma_form_model->createFormFromCsvRow($row);
                    $form = $this->gamma_form_model->getFormById($form_id);
                    $this->createSupportFiles($form);
                    $last_form_id = $form_id;
                    $created++;
                }

                if ($last_form_id) {
                    $this->setRecentFormId($last_form_id);
                }

                $this->session->set_flashdata('message', $created . ' form record(s) imported successfully.');
                admin_redirect('gamma_forms');
            } catch (Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        } else {
            $this->data['error'] = $this->session->flashdata('error');
        }

        $bc = array(
            array('link' => admin_url('gamma_forms'), 'page' => 'Gamma Forms'),
            array('link' => '#', 'page' => 'Import Forms CSV'),
        );
        $meta = array('page_title' => 'Import Forms CSV', 'bc' => $bc);
        $this->page_construct('gamma/import_form', $meta, $this->data);
    }

    public function import_inputs_csv()
    {
        $this->checkFormPermission('gamma-forms-import-inputs');
        if ($this->input->method() === 'post') {
            try {
                $default_form_id = $this->input->post('form_id') ? (int) $this->input->post('form_id') : $this->getRecentFormId();
                $csv_path = $this->uploadCsv('csv_file');
                $rows = $this->readCsv($csv_path);
                if (empty($rows)) {
                    throw new RuntimeException('No form input rows were found in the CSV.');
                }

                $created = 0;
                foreach ($rows as $row) {
                    $row_form_id = null;
                    foreach (array('formid', 'form_id', 'form id') as $key) {
                        if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                            $row_form_id = (int) $row[$key];
                            break;
                        }
                    }

                    if (!$row_form_id && !$default_form_id) {
                        throw new RuntimeException('FormID is missing. Import a form first or enter a Default Form ID.');
                    }

                    $this->gamma_form_model->createFormInputFromCsvRow($row, $default_form_id);
                    $created++;
                }

                if ($default_form_id) {
                    $this->setRecentFormId($default_form_id);
                }

                $message = $created . ' input row(s) imported successfully.';
                if ($default_form_id) {
                    $message .= ' Default Form ID used: ' . $default_form_id . '.';
                }
                $this->session->set_flashdata('message', $message);
                admin_redirect('gamma_forms');
            } catch (Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        } else {
            $this->data['error'] = $this->session->flashdata('error');
        }

        $this->data['default_form_id'] = $this->getRecentFormId();

        $bc = array(
            array('link' => admin_url('gamma_forms'), 'page' => 'Gamma Forms'),
            array('link' => '#', 'page' => 'Import Inputs CSV'),
        );
        $meta = array('page_title' => 'Import Inputs CSV', 'bc' => $bc);
        $this->page_construct('gamma/import_form_inputs', $meta, $this->data);
    }

    public function generate_input_form($form_id = null)
    {
        $this->checkFormPermission('gamma-forms-generate');
        $form_id = (int) $form_id;
        $form = $this->gamma_form_model->getFormById($form_id);
        if (!$form) {
            $this->session->set_flashdata('error', 'Form not found.');
            admin_redirect('gamma_forms');
        }

        $user = $this->site->getUserById($form->user_id);
        if (!$user) {
            $this->session->set_flashdata('error', 'Assigned user for the form could not be found.');
            admin_redirect('gamma_forms');
        }

        $inputs = $this->gamma_form_model->getInputsForForm($form_id);
        if (empty($inputs)) {
            $this->session->set_flashdata('error', 'No form inputs are available for this FormID.');
            admin_redirect('gamma_forms');
        }

        if (trim((string) $form->input_form_location) === '') {
            $this->session->set_flashdata('error', 'InputFormLocation is missing for this form.');
            admin_redirect('gamma_forms');
        }

        $html = $this->gamma_input_form_builder->build($form, $inputs, admin_url('gamma/submit_form/' . $form->form_id));
        $absolute_path = $this->gamma_path_service->resolvePath($form->input_form_location, $user->username);
        $this->gamma_path_service->ensureParentDirectory($absolute_path);
        file_put_contents($absolute_path, $html);

        $this->session->set_flashdata('message', 'Input form generated at ' . $form->input_form_location);
        admin_redirect('gamma_forms');
    }

    public function delete_form($form_id = null)
    {
        $this->checkFormPermission('gamma-forms-delete');
        $form_id = (int) $form_id;
        $form = $this->gamma_form_model->getFormById($form_id);
        if (!$form) {
            $this->session->set_flashdata('error', 'Form not found.');
            admin_redirect('gamma_forms');
        }

        $this->gamma_form_model->deleteForm($form_id);
        $this->session->set_flashdata('message', 'Form #' . $form_id . ' and its inputs have been deleted.');
        admin_redirect('gamma_forms');
    }
}
