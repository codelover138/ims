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
        $this->load->library('gamma_path_service');
    }

    public function index()
    {
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

        $bc = array(
            array('link' => admin_url('gamma'), 'page' => 'Gamma'),
        );
        $meta = array('page_title' => 'Gamma Workspace', 'bc' => $bc);
        $this->page_construct('gamma/dashboard', $meta, $this->data);
    }
}
