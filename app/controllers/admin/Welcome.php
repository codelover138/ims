<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            admin_redirect('login');
        }

        if ($this->Customer || $this->Supplier) {
            redirect('/');
        }

        $this->load->library('form_validation');
        $this->load->admin_model('db_model');
    }

    public function index()
    {
        $is_full_dashboard = $this->Owner || $this->Admin;
        $this->data['is_full_dashboard'] = $is_full_dashboard;
        $this->data['dashboard_metrics'] = $this->getDashboardMetrics($is_full_dashboard);
        $this->data['dashboard_shortcuts'] = $this->getDashboardShortcuts();
        $this->data['recent_logins'] = $this->getRecentLogins($is_full_dashboard);
        $this->data['upcoming_events'] = $this->getUpcomingEvents($is_full_dashboard);
        $this->data['recent_notifications'] = $this->getRecentNotifications($is_full_dashboard);
        $this->data['dashboard_greeting'] = $this->getDashboardGreeting();
        $this->data['dashboard_profile'] = $this->getDashboardProfile($is_full_dashboard);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => '#', 'page' => lang('dashboard')));
        $meta = array('page_title' => lang('dashboard'), 'bc' => $bc);
        $this->page_construct('dashboard', $meta, $this->data);

    }

    protected function getDashboardGreeting()
    {
        $hour = (int) date('H');
        if ($hour < 12) {
            return 'Good morning';
        }
        if ($hour < 18) {
            return 'Good afternoon';
        }

        return 'Good evening';
    }

    protected function countTableRows($table, $where = array())
    {
        if (!$this->db->table_exists($table)) {
            return null;
        }

        if (!empty($where)) {
            $this->db->where($where);
        }

        return (int) $this->db->count_all_results($table);
    }

    protected function countFilteredNotifications()
    {
        if (!$this->db->table_exists('notifications')) {
            return null;
        }

        $date = date('Y-m-d H:i:s');
        $warehouse_id = (int) $this->session->userdata('warehouse_id');

        $this->db->from('notifications');
        $this->db->group_start()
            ->where('from_date IS NULL', null, false)
            ->or_where('from_date <=', $date)
            ->group_end();
        $this->db->group_start()
            ->where('till_date IS NULL', null, false)
            ->or_where('till_date >=', $date)
            ->group_end();
        $this->db->group_start()
            ->where('scope', 2)
            ->or_where('scope', 3)
            ->group_end();

        if ($warehouse_id > 0 && $this->db->field_exists('warehouse_id', 'notifications')) {
            $this->db->group_start()
                ->where('warehouse_id IS NULL', null, false)
                ->or_where('warehouse_id', 0)
                ->or_where('warehouse_id', $warehouse_id)
                ->group_end();
        }

        return (int) $this->db->count_all_results();
    }

    protected function countUserLogins()
    {
        if (!$this->db->table_exists('user_logins')) {
            return null;
        }

        $username = $this->session->userdata('username');
        $email = $this->session->userdata('email');

        if (!$username && !$email) {
            return 0;
        }

        $this->db->from('user_logins');
        $this->db->group_start();
        if ($username) {
            $this->db->where('login', $username);
        }
        if ($email) {
            $this->db->or_where('login', $email);
        }
        $this->db->group_end();

        return (int) $this->db->count_all_results();
    }

    protected function getDashboardMetrics($is_full_dashboard = false)
    {
        if (!$is_full_dashboard) {
            return array(
                array(
                    'label' => 'My Sign-ins',
                    'value' => $this->countUserLogins(),
                    'icon'  => 'fa-sign-in',
                    'tone'  => 'primary',
                    'url'   => admin_url('users/profile/' . $this->session->userdata('user_id')),
                    'note'  => 'Recorded access history',
                ),
                array(
                    'label' => 'My Events',
                    'value' => $this->countTableRows('calendar', array('user_id' => $this->session->userdata('user_id'))),
                    'icon'  => 'fa-calendar',
                    'tone'  => 'warning',
                    'url'   => admin_url('calendar'),
                    'note'  => 'Calendar items linked to you',
                ),
                array(
                    'label' => 'Relevant Notices',
                    'value' => $this->countFilteredNotifications(),
                    'icon'  => 'fa-bell',
                    'tone'  => 'info',
                    'url'   => admin_url('notifications'),
                    'note'  => 'Staff notices for your workspace',
                ),
            );
        }

        $backup_files = glob(FCPATH . 'files/backups/*');
        $metrics = array(
            array(
                'label' => 'Users',
                'value' => $this->countTableRows('users'),
                'icon'  => 'fa-users',
                'tone'  => 'primary',
                'url'   => admin_url('auth/users'),
                'note'  => 'Registered accounts',
            ),
            array(
                'label' => 'Active Users',
                'value' => $this->countTableRows('users', array('active' => 1)),
                'icon'  => 'fa-user',
                'tone'  => 'success',
                'url'   => admin_url('auth/users'),
                'note'  => 'Currently enabled',
            ),
            array(
                'label' => 'Notifications',
                'value' => $this->countTableRows('notifications'),
                'icon'  => 'fa-bell',
                'tone'  => 'info',
                'url'   => admin_url('notifications'),
                'note'  => 'System announcements',
            ),
            array(
                'label' => 'Calendar Events',
                'value' => $this->countTableRows('calendar'),
                'icon'  => 'fa-calendar',
                'tone'  => 'warning',
                'url'   => admin_url('calendar'),
                'note'  => 'Scheduled items',
            ),
            array(
                'label' => 'Service Points',
                'value' => $this->countTableRows('warehouses'),
                'icon'  => 'fa-map-marker',
                'tone'  => 'info',
                'url'   => admin_url('system_settings/service-points'),
                'note'  => 'Configured service locations',
            ),
            array(
                'label' => 'Services',
                'value' => $this->countTableRows('products'),
                'icon'  => 'fa-cube',
                'tone'  => 'primary',
                'url'   => admin_url('products'),
                'note'  => 'Catalog records',
            ),
            array(
                'label' => 'Backups',
                'value' => is_array($backup_files) ? count($backup_files) : 0,
                'icon'  => 'fa-database',
                'tone'  => 'success',
                'url'   => admin_url('system_settings/backups'),
                'note'  => 'Stored in files/backups',
            ),
        );

        return array_values(array_filter($metrics, function ($metric) {
            return $metric['value'] !== null;
        }));
    }

    protected function getDashboardShortcuts()
    {
        $shortcuts = array();

        $can_manage_users = $this->Owner || $this->Admin;
        $can_manage_settings = $this->Owner;
        $can_manage_documents = $this->Owner || $this->Admin || !empty($this->GP['document-file_manager']);

        if ($can_manage_documents) {
            $shortcuts[] = array(
                'label' => lang('File_Manager'),
                'icon' => 'fa-folder-open',
                'url' => admin_url('document/file_manager'),
                'description' => 'Browse and manage stored files.',
            );
        }

        $shortcuts[] = array(
            'label' => lang('notifications'),
            'icon' => 'fa-comments',
            'url' => admin_url('notifications'),
            'description' => 'Post or review internal announcements.',
        );

        $shortcuts[] = array(
            'label' => lang('calendar'),
            'icon' => 'fa-calendar',
            'url' => admin_url('calendar'),
            'description' => 'Check upcoming schedules and events.',
        );

        if ($can_manage_users) {
            $shortcuts[] = array(
                'label' => lang('users'),
                'icon' => 'fa-users',
                'url' => admin_url('auth/users'),
                'description' => 'Manage user accounts and permissions.',
            );
        }

        if ($can_manage_settings) {
            $shortcuts[] = array(
                'label' => lang('settings'),
                'icon' => 'fa-cogs',
                'url' => admin_url('system_settings'),
                'description' => 'Update application and workspace settings.',
            );
        }

        return $shortcuts;
    }

    protected function getRecentLogins($is_full_dashboard = false)
    {
        if (!$this->db->table_exists('user_logins')) {
            return array();
        }

        $this->db
            ->select('login, ip_address, time')
            ->from('user_logins')
            ->order_by('time', 'DESC');

        if (!$is_full_dashboard) {
            $username = $this->session->userdata('username');
            $email = $this->session->userdata('email');
            $this->db->group_start();
            if ($username) {
                $this->db->where('login', $username);
            }
            if ($email) {
                $this->db->or_where('login', $email);
            }
            $this->db->group_end();
        }

        return $this->db->limit(5)->get()->result();
    }

    protected function getUpcomingEvents($is_full_dashboard = false)
    {
        if (!$this->db->table_exists('calendar')) {
            return array();
        }

        $this->db
            ->select('title, description, start')
            ->from('calendar')
            ->where('start >=', date('Y-m-d H:i:s'))
            ->order_by('start', 'ASC');

        if (!$is_full_dashboard && $this->db->field_exists('user_id', 'calendar')) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        return $this->db->limit(5)->get()->result();
    }

    protected function getRecentNotifications($is_full_dashboard = false)
    {
        if (!$this->db->table_exists('notifications')) {
            return array();
        }

        $this->db
            ->select('id, comment, from_date, till_date')
            ->from('notifications')
            ->order_by('id', 'DESC');

        if (!$is_full_dashboard) {
            $warehouse_id = (int) $this->session->userdata('warehouse_id');
            $date = date('Y-m-d H:i:s');
            $this->db->group_start()
                ->where('scope', 2)
                ->or_where('scope', 3)
                ->group_end();
            $this->db->group_start()
                ->where('from_date IS NULL', null, false)
                ->or_where('from_date <=', $date)
                ->group_end();
            $this->db->group_start()
                ->where('till_date IS NULL', null, false)
                ->or_where('till_date >=', $date)
                ->group_end();
            if ($warehouse_id > 0 && $this->db->field_exists('warehouse_id', 'notifications')) {
                $this->db->group_start()
                    ->where('warehouse_id IS NULL', null, false)
                    ->or_where('warehouse_id', 0)
                    ->or_where('warehouse_id', $warehouse_id)
                    ->group_end();
            }
        }

        return $this->db->limit(5)->get()->result();
    }

    protected function getEntityNameById($table, $id)
    {
        $id = (int) $id;
        if ($id < 1 || !$this->db->table_exists($table)) {
            return null;
        }

        $select = array('id');
        if ($this->db->field_exists('name', $table)) {
            $select[] = 'name';
        }
        if ($this->db->field_exists('company', $table)) {
            $select[] = 'company';
        }

        $row = $this->db->select(implode(', ', $select))->get_where($table, array('id' => $id), 1)->row();
        if (!$row) {
            return null;
        }

        if (property_exists($row, 'company') && !empty($row->company) && $row->company !== '-') {
            return $row->company;
        }

        return property_exists($row, 'name') && !empty($row->name) ? $row->name : null;
    }

    protected function getDashboardProfile($is_full_dashboard = false)
    {
        $user_id = (int) $this->session->userdata('user_id');
        $warehouse_id = (int) $this->session->userdata('warehouse_id');
        $biller_id = (int) $this->session->userdata('biller_id');

        return array(
            'role_label' => $this->Owner ? 'Owner access' : ($this->Admin ? 'Admin access' : 'Staff access'),
            'is_full_dashboard' => $is_full_dashboard,
            'username' => $this->session->userdata('username'),
            'email' => $this->session->userdata('email'),
            'warehouse_name' => $this->getEntityNameById('warehouses', $warehouse_id),
            'biller_name' => $this->getEntityNameById('billers', $biller_id),
            'last_login' => $this->session->userdata('last_login'),
            'last_ip_address' => $this->session->userdata('last_ip_address'),
            'profile_url' => admin_url('users/profile/' . $user_id),
        );
    }

    function promotions()
    {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    function image_upload()
    {
        if (DEMO) {
            $error = array('error' => $this->lang->line('disabled_in_demo'));
            $this->sma->send_json($error);
            exit;
        }
        $this->security->csrf_verify();
        if (isset($_FILES['file'])) {
            $this->load->library('upload');
            $config['upload_path'] = 'assets/uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '500';
            $config['max_width'] = $this->Settings->iwidth;
            $config['max_height'] = $this->Settings->iheight;
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['max_filename'] = 25;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $error = $this->upload->display_errors();
                $error = array('error' => $error);
                $this->sma->send_json($error);
                exit;
            }
            $photo = $this->upload->file_name;
            $array = array(
                'filelink' => base_url() . 'assets/uploads/images/' . $photo
            );
            echo stripslashes(json_encode($array));
            exit;

        } else {
            $error = array('error' => 'No file selected to upload!');
            $this->sma->send_json($error);
            exit;
        }
    }

    function set_data($ud, $value)
    {
        $this->session->set_userdata($ud, $value);
        echo true;
    }

    function hideNotification($id = NULL)
    {
        $this->session->set_userdata('hidden' . $id, 1);
        echo true;
    }

    function language($lang = false)
    {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }


      //  $this->load->helper('cookie');
        $folder = 'app/language/';
        $languagefiles = scandir($folder);

        if (in_array($lang, $languagefiles)) {
            $cookie = array(
                'name' => 'language',
                'value' => $lang,
                'expire' => '31536000',
                'prefix' => 'sma_',
                'secure' => false
            );
            $this->input->set_cookie($cookie);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function toggle_rtl()
    {
        $cookie = array(
            'name' => 'rtl_support',
            'value' => $this->Settings->user_rtl == 1 ? 0 : 1,
            'expire' => '31536000',
            'prefix' => 'sma_',
            'secure' => false
        );
        $this->input->set_cookie($cookie);
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function download($file)
    {
        if (file_exists('./files/'.$file)) {
            $this->load->helper('download');
            force_download('./files/'.$file, NULL);
            exit();
        }
        $this->session->set_flashdata('error', lang('file_x_exist'));
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function slug() {
        echo $this->sma->slug($this->input->get('title', TRUE), $this->input->get('type', TRUE));
        exit();
    }

}
