<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Immigrants extends MY_Controller
{
    protected $upload_path = 'files/immigrants/';

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('immigrants', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('immigrants_model');
        if (!is_dir(FCPATH . $this->upload_path)) {
            @mkdir(FCPATH . $this->upload_path, 0755, true);
        }
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('immigrants')));
        $meta = array('page_title' => lang('immigrants'), 'bc' => $bc);
        $this->page_construct('immigrants/index', $meta, $this->data);
    }

    function getImmigrants()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select("immigrants.id as id, primary_id, surname, first_name, middle_name, phone_number, country_of_origin, work_status")
            ->from("immigrants");
        if (!$this->Owner && !$this->Admin) {
            $this->datatables->where('immigrants.created_by', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . lang("view_details") . "' href='" . admin_url('immigrants/view/$1') . "'><i class=\"fa fa-file-text-o\"></i></a> <a class=\"tip\" title='" . lang("edit_immigrant") . "' href='" . admin_url('immigrants/edit/$1') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_immigrant") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('immigrants/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        echo $this->datatables->generate();
    }

    function add()
    {
        $this->sma->checkPermissions(false, true);
        $this->form_validation->set_rules('surname', lang('surname'), 'trim|required');
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $primary_id = $this->immigrants_model->generatePrimaryId(
                $this->input->post('surname'),
                $this->input->post('first_name'),
                $this->input->post('middle_name')
            );
            $data = array(
                'primary_id' => $primary_id,
                'surname' => $this->input->post('surname'),
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'passport_number' => $this->input->post('passport_number'),
                'id_number' => $this->input->post('id_number'),
                'asylum_seeker_number' => $this->input->post('asylum_seeker_number'),
                'work_permit_number' => $this->input->post('work_permit_number'),
                'phone_number' => $this->input->post('phone_number'),
                'country_of_origin' => $this->input->post('country_of_origin'),
                'last_date_of_entry' => $this->input->post('last_date_of_entry') ? $this->sma->fld($this->input->post('last_date_of_entry')) : null,
                'validity_of_stay' => $this->input->post('validity_of_stay') ? $this->sma->fld($this->input->post('validity_of_stay')) : null,
                'municipality' => $this->input->post('municipality'),
                'town' => $this->input->post('town'),
                'area' => $this->input->post('area'),
                'narrative_direction' => $this->input->post('narrative_direction'),
                'work_status' => $this->input->post('work_status'),
                'line_of_business' => $this->input->post('line_of_business'),
                'created_by' => $this->session->userdata('user_id'),
            );
            $data = $this->handleDocumentUploads($data);

            if ($id = $this->immigrants_model->addImmigrant($data)) {
                $this->session->set_flashdata('message', lang('immigrant_added'));
                admin_redirect('immigrants');
            }
        } elseif ($this->input->post('add_immigrant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('immigrants');
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('immigrants'), 'page' => lang('immigrants')), array('link' => '#', 'page' => lang('add_immigrant')));
        $meta = array('page_title' => lang('add_immigrant'), 'bc' => $bc);
        $this->page_construct('immigrants/add', $meta, $this->data);
    }

    function edit($id = NULL)
    {
        $this->sma->checkPermissions(false, true);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $immigrant = $this->immigrants_model->getImmigrantByID($id);
        if (!$immigrant) {
            $this->session->set_flashdata('error', lang('immigrant_not_found'));
            admin_redirect('immigrants');
        }
        if (!$this->Owner && !$this->Admin && (int) $immigrant->created_by !== (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('immigrants');
        }

        $this->form_validation->set_rules('surname', lang('surname'), 'trim|required');
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'surname' => $this->input->post('surname'),
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'passport_number' => $this->input->post('passport_number'),
                'id_number' => $this->input->post('id_number'),
                'asylum_seeker_number' => $this->input->post('asylum_seeker_number'),
                'work_permit_number' => $this->input->post('work_permit_number'),
                'phone_number' => $this->input->post('phone_number'),
                'country_of_origin' => $this->input->post('country_of_origin'),
                'last_date_of_entry' => $this->input->post('last_date_of_entry') ? $this->sma->fld($this->input->post('last_date_of_entry')) : null,
                'validity_of_stay' => $this->input->post('validity_of_stay') ? $this->sma->fld($this->input->post('validity_of_stay')) : null,
                'municipality' => $this->input->post('municipality'),
                'town' => $this->input->post('town'),
                'area' => $this->input->post('area'),
                'narrative_direction' => $this->input->post('narrative_direction'),
                'work_status' => $this->input->post('work_status'),
                'line_of_business' => $this->input->post('line_of_business'),
                'updated_by' => $this->session->userdata('user_id'),
            );
            $data = $this->handleDocumentUploads($data, $immigrant);

            if ($this->immigrants_model->updateImmigrant($id, $data)) {
                $this->session->set_flashdata('message', lang('immigrant_updated'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } elseif ($this->input->post('edit_immigrant')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['immigrant'] = $immigrant;
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('immigrants'), 'page' => lang('immigrants')), array('link' => '#', 'page' => lang('edit_immigrant')));
        $meta = array('page_title' => lang('edit_immigrant'), 'bc' => $bc);
        $this->page_construct('immigrants/edit', $meta, $this->data);
    }

    function view($id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $immigrant = $this->immigrants_model->getImmigrantByID($id);
        if (!$immigrant) {
            $this->session->set_flashdata('error', lang('immigrant_not_found'));
            admin_redirect('immigrants');
        }
        if (!$this->Owner && !$this->Admin && (int) $immigrant->created_by !== (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('immigrants');
        }
        $this->data['immigrant'] = $immigrant;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('immigrants'), 'page' => lang('immigrants')), array('link' => '#', 'page' => lang('view_details')));
        $meta = array('page_title' => lang('immigrant_details') . ' — ' . $immigrant->primary_id, 'bc' => $bc);
        $this->page_construct('immigrants/view', $meta, $this->data);
    }

    /**
     * Download single immigrant details as PDF (from view page).
     */
    function pdf($id = NULL)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $immigrant = $this->immigrants_model->getImmigrantByID($id);
        if (!$immigrant) {
            $this->session->set_flashdata('error', lang('immigrant_not_found'));
            admin_redirect('immigrants');
        }
        if (!$this->Owner && !$this->Admin && (int) $immigrant->created_by !== (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('immigrants');
        }
        $this->data['immigrant'] = $immigrant;
        $this->data['dateFormats'] = $this->dateFormats;
        $html = $this->load->view($this->theme . 'immigrants/view_pdf', $this->data, true);
        $name = 'immigrant_' . $immigrant->primary_id . '_' . date('Y-m-d') . '.pdf';
        $this->sma->generate_pdf($html, $name);
    }

    function delete($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $immigrant = $this->immigrants_model->getImmigrantByID($id);
        if ($immigrant && !$this->Owner && !$this->Admin && (int) $immigrant->created_by !== (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('immigrants');
        }
        if ($this->immigrants_model->deleteImmigrant($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang('immigrant_deleted')));
        } else {
            $this->sma->send_json(array('error' => 1, 'msg' => lang('immigrant_x_deleted')));
        }
    }

    /**
     * Export immigrants list to Excel (XLSX)
     */
    function export_xls()
    {
        $this->sma->checkPermissions('index');
        $rows = $this->immigrants_model->getAllImmigrants();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $sheet = $this->excel->getActiveSheet();
        $sheet->setTitle(lang('immigrants'));
        $sheet->setCellValue('A1', lang('primary_id'));
        $sheet->setCellValue('B1', lang('surname'));
        $sheet->setCellValue('C1', lang('first_name'));
        $sheet->setCellValue('D1', lang('middle_name'));
        $sheet->setCellValue('E1', lang('phone_number'));
        $sheet->setCellValue('F1', lang('country_of_origin'));
        $sheet->setCellValue('G1', lang('last_date_of_entry'));
        $sheet->setCellValue('H1', lang('validity_of_stay'));
        $sheet->setCellValue('I1', lang('work_status'));
        $sheet->setCellValue('J1', lang('municipality'));
        $sheet->setCellValue('K1', lang('town'));
        $sheet->setCellValue('L1', lang('area'));
        $row = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue('A' . $row, $r->primary_id);
            $sheet->setCellValue('B' . $row, $r->surname);
            $sheet->setCellValue('C' . $row, $r->first_name);
            $sheet->setCellValue('D' . $row, $r->middle_name);
            $sheet->setCellValue('E' . $row, $r->phone_number);
            $sheet->setCellValue('F' . $row, $r->country_of_origin);
            $sheet->setCellValue('G' . $row, $r->last_date_of_entry ? date($this->dateFormats['php_sdate'], strtotime($r->last_date_of_entry)) : '');
            $sheet->setCellValue('H' . $row, $r->validity_of_stay ? date($this->dateFormats['php_sdate'], strtotime($r->validity_of_stay)) : '');
            $sheet->setCellValue('I' . $row, $r->work_status);
            $sheet->setCellValue('J' . $row, $r->municipality);
            $sheet->setCellValue('K' . $row, $r->town);
            $sheet->setCellValue('L' . $row, $r->area);
            $row++;
        }
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(20);
        $filename = 'immigrants_' . date('Y_m_d_H_i_s');
        $this->load->helper('excel');
        create_excel($this->excel, $filename);
    }

    /**
     * Export immigrants list to PDF
     */
    function export_pdf()
    {
        $this->sma->checkPermissions('index');
        $this->data['immigrants'] = $this->immigrants_model->getAllImmigrants();
        $this->data['Settings'] = $this->Settings;
        $this->data['dateFormats'] = $this->dateFormats;
        $html = $this->load->view($this->theme . 'immigrants/pdf_export', $this->data, true);
        $name = 'immigrants_' . date('Y-m-d_H-i-s') . '.pdf';
        $this->sma->generate_pdf($html, $name);
    }

    /**
     * Handle file uploads for document fields. Returns data array with doc_* keys set.
     */
    protected function handleDocumentUploads($data, $existing = null)
    {
        $fields = array('doc_head_shot', 'doc_passport', 'doc_asylum_permit', 'doc_work_permit', 'doc_other');
        $this->load->library('upload');
        foreach ($fields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $config = array(
                    'upload_path' => FCPATH . $this->upload_path,
                    'allowed_types' => 'gif|jpeg|jpg|png|pdf',
                    'max_size' => 5120,
                    'overwrite' => false,
                    'encrypt_name' => true,
                );
                $this->upload->initialize($config);
                if ($this->upload->do_upload($field)) {
                    $data[$field] = $this->upload_path . $this->upload->data('file_name');
                }
            } elseif ($existing && isset($existing->$field) && $existing->$field) {
                $data[$field] = $existing->$field;
            }
        }
        return $data;
    }
}
