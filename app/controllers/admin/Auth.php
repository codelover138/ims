<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->lang->admin_load('auth', $this->Settings->user_language);
        $this->config->load('gamma', true);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->admin_model('auth_model');
        $this->load->admin_model('user_notes_model');
        $this->load->library('ion_auth');
    }

    protected function parseGammaDateInput($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $formats = array(
            $this->dateFormats['php_sdate'],
            'Y-m-d',
            'Y-m-d H:i:s',
        );

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat('!' . $format, $value);
            $errors = DateTime::getLastErrors();
            if ($errors === false) {
                $errors = array('warning_count' => 0, 'error_count' => 0);
            }
            if ($date instanceof DateTime && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                return $date;
            }
        }

        return null;
    }

    protected function formatGammaDateForInput($value)
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        return $this->sma->hrsd($value);
    }

    protected function normalizeGammaDateForDatabase($value)
    {
        $date = $this->parseGammaDateInput($value);

        return $date ? $date->format('Y-m-d 00:00:00') : null;
    }

    protected function parseGammaDateTimeInput($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $formats = array(
            array('format' => 'Y-m-d\TH:i', 'reset' => false),
            array('format' => 'Y-m-d H:i:s', 'reset' => false),
            array('format' => 'Y-m-d H:i', 'reset' => false),
            array('format' => $this->dateFormats['php_sdate'], 'reset' => true),
            array('format' => 'Y-m-d', 'reset' => true),
        );

        foreach ($formats as $format) {
            $parser_format = ($format['reset'] ? '!' : '') . $format['format'];
            $date = DateTime::createFromFormat($parser_format, $value);
            $errors = DateTime::getLastErrors();
            if ($errors === false) {
                $errors = array('warning_count' => 0, 'error_count' => 0);
            }
            if ($date instanceof DateTime && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                return $date;
            }
        }

        $timestamp = strtotime($value);
        return $timestamp ? (new DateTime())->setTimestamp($timestamp) : null;
    }

    protected function normalizeGammaDateTimeForDatabase($value)
    {
        $date = $this->parseGammaDateTimeInput($value);

        return $date ? $date->format('Y-m-d H:i:s') : null;
    }

    protected function buildUserNoteDataFromPost($user_id)
    {
        $narrative = trim((string) $this->input->post('note_narrative'));
        $entry_date = $this->normalizeGammaDateTimeForDatabase($this->input->post('note_entry_date'));
        $note_date = $this->normalizeGammaDateTimeForDatabase($this->input->post('note_note_date'));

        if ($narrative === '' && !$entry_date && !$note_date) {
            return null;
        }

        $now = date('Y-m-d H:i:s');

        return array(
            'user_id' => (int) $user_id,
            'date_created' => $now,
            'entry_date' => $entry_date ?: $now,
            'note_date' => $note_date ?: $now,
            'narrative' => $narrative,
            'last_updated' => $now,
        );
    }

    protected function createUserNoteIfProvided($user_id)
    {
        $note_data = $this->buildUserNoteDataFromPost($user_id);
        if ($note_data) {
            $this->user_notes_model->addNote($note_data);
        }
    }

    protected function findUserForUsernameReminder($email)
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return null;
        }

        $this->db->from('users');
        $this->db->group_start()->where('LOWER(email) =', $email);
        if ($this->db->field_exists('email2', 'users')) {
            $this->db->or_where('LOWER(email2) =', $email);
        }
        $this->db->group_end();

        $query = $this->db->limit(1)->get();

        return $query->num_rows() ? $query->row() : null;
    }

    protected function sendUsernameReminderEmail($user)
    {
        $this->load->library('tec_mail');

        $recipient_name = trim(((string) ($user->first_name ?? '')) . ' ' . ((string) ($user->last_name ?? '')));
        if ($recipient_name === '') {
            $recipient_name = $user->username;
        }

        $subject = $this->Settings->site_name . ' username reminder';
        $login_url = admin_url('login');
        $body = '<p>Hello ' . html_escape($recipient_name) . ',</p>'
            . '<p>Your username for ' . html_escape($this->Settings->site_name) . ' is:</p>'
            . '<p><strong>' . html_escape($user->username) . '</strong></p>'
            . '<p>You can sign in here: <a href="' . html_escape($login_url) . '">Admin Login</a></p>';

        $this->tec_mail->send_mail($user->email, $subject, $body);
    }

    protected function setGammaUserProfileValidationRules()
    {
        $this->form_validation->set_rules('email2', 'Secondary Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|callback_valid_phone_number');
        $this->form_validation->set_rules('mobile_phone', 'Mobile Phone', 'trim|callback_valid_optional_phone_number');
        $this->form_validation->set_rules('business_phone', 'Business Phone', 'trim|callback_valid_optional_phone_number');
        $this->form_validation->set_rules('birth_date', 'Birth Date', 'trim|callback_valid_profile_date');
        $this->form_validation->set_rules('departure_date', 'Departure Date', 'trim|callback_valid_profile_date');
    }

    public function valid_phone_number($value)
    {
        $value = trim((string) $value);
        $digits = preg_replace('/\D+/', '', $value);

        if ($value === '' || !preg_match('/^\+?[0-9()\-\s.]+$/', $value) || strlen($digits) < 7 || strlen($digits) > 15) {
            $this->form_validation->set_message('valid_phone_number', '{field} must contain a valid phone number.');
            return false;
        }

        return true;
    }

    protected function createCaptchaPayload($width = 150, $height = 34, $img_url = null)
    {
        $this->load->helper('captcha');

        $vals = array(
            'img_path' => FCPATH . 'assets/captcha/',
            'img_url' => $img_url ?: base_url('assets/captcha/'),
            'img_width' => $width,
            'img_height' => $height,
            'word_length' => 5,
            'colors' => array(
                'background' => array(255, 255, 255),
                'border' => array(204, 204, 204),
                'text' => array(102, 102, 102),
                'grid' => array(204, 204, 204),
            ),
        );

        $cap = create_captcha($vals);
        if ($cap === false || !isset($cap['time'], $cap['word'], $cap['image'])) {
            log_message('error', 'Captcha generation failed. Check that ' . FCPATH . 'assets/captcha/ exists, is writable by the web server user, and PHP GD is enabled.');
            return false;
        }

        $capdata = array(
            'captcha_time' => $cap['time'],
            'ip_address' => $this->input->ip_address(),
            'word' => $cap['word']
        );

        $query = $this->db->insert_string('captcha', $capdata);
        $this->db->query($query);

        return $cap;
    }

    public function valid_optional_phone_number($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return true;
        }

        return $this->valid_phone_number($value);
    }

    public function valid_profile_date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return true;
        }

        if (!$this->parseGammaDateInput($value)) {
            $this->form_validation->set_message('valid_profile_date', '{field} must match the date format ' . $this->dateFormats['php_sdate'] . '.');
            return false;
        }

        return true;
    }

    protected function getGammaUserProfileDataFromPost()
    {
        return array(
            'middle_name' => $this->input->post('middle_name'),
            'birth_date' => $this->normalizeGammaDateForDatabase($this->input->post('birth_date')),
            'business_name' => $this->input->post('business_name'),
            'unit_number' => $this->input->post('unit_number'),
            'street_number' => $this->input->post('street_number'),
            'street_name' => $this->input->post('street_name'),
            'street_type' => $this->input->post('street_type'),
            'suburb' => $this->input->post('suburb'),
            'state' => $this->input->post('state'),
            'country' => $this->input->post('country'),
            'postcode' => $this->input->post('postcode'),
            'email2' => $this->input->post('email2'),
            'mobile_phone' => $this->input->post('mobile_phone'),
            'business_phone' => $this->input->post('business_phone'),
            'security_question' => $this->input->post('security_question'),
            'security_answer' => $this->input->post('security_answer'),
            'departure_date' => $this->normalizeGammaDateForDatabase($this->input->post('departure_date')),
            'departure_reason' => $this->input->post('departure_reason'),
        );
    }

    protected function normalizeWarehouseIdFromPost($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (!ctype_digit($value)) {
            return false;
        }

        $warehouse = $this->site->getWarehouseByID((int) $value);

        return $warehouse ? (int) $warehouse->id : false;
    }

    public function valid_warehouse($value)
    {
        $warehouse_id = $this->normalizeWarehouseIdFromPost($value);
        if ($warehouse_id === false) {
            $this->form_validation->set_message('valid_warehouse', 'The selected warehouse is invalid.');
            return false;
        }

        return true;
    }

    function index()
    {

        if (!$this->loggedIn) {
            admin_redirect('login');
        } else {
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function users()
    {
        if ( ! $this->loggedIn) {
            admin_redirect('login');
        }
        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('users')));
        $meta = array('page_title' => lang('users'), 'bc' => $bc);
        $this->page_construct('auth/index', $meta, $this->data);
    }

    function getUsers()
    {
        if ( ! $this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $select = $this->db->dbprefix('users') . ".id as id, " . $this->db->dbprefix('users') . ".id as user_id, first_name, last_name, email, company";
        if ($this->db->field_exists('award_points', 'users')) {
            $select .= ", award_points";
        }
        $select .= ", " . $this->db->dbprefix('groups') . ".name, active";
        $this->datatables
            ->select($select)
            ->from("users")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', NULL)
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('auth/profile/$1') . "' class='tip' title='" . lang("edit_user") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
        echo $this->datatables->generate();
    }

    function getUserLogins($id = NULL)
    {
        if (!$this->ion_auth->in_group(array('owner', 'admin'))) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            admin_redirect('welcome');
        }
        $this->load->library('datatables');
        $this->datatables
            ->select("login, ip_address, time")
            ->from("user_logins")
            ->where('user_id', $id);

        echo $this->datatables->generate();
    }

    function delete_avatar($id = NULL, $avatar = NULL)
    {

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('owner') && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . $_SERVER["HTTP_REFERER"] . "'; }, 0);</script>");
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            unlink('assets/uploads/avatars/' . $avatar);
            unlink('assets/uploads/avatars/thumbs/' . $avatar);
            if ($id == $this->session->userdata('user_id')) {
                $this->session->unset_userdata('avatar');
            }
            $this->db->update('users', array('avatar' => NULL), array('id' => $id));
            $this->session->set_flashdata('message', lang("avatar_deleted"));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . $_SERVER["HTTP_REFERER"] . "'; }, 0);</script>");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function profile($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group('owner') && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin');
        }
        if (!$id || empty($id)) {
            admin_redirect('auth');
        }

        $user = $this->ion_auth->user($id)->row();
        $this->renderProfilePage($id, $user);
    }

    protected function hydrateProfileUserFromPost($user)
    {
        $field_map = array(
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'middle_name' => 'middle_name',
            'company' => 'company',
            'phone' => 'phone',
            'gender' => 'gender',
            'username' => 'username',
            'email' => 'email',
            'business_name' => 'business_name',
            'email2' => 'email2',
            'mobile_phone' => 'mobile_phone',
            'business_phone' => 'business_phone',
            'unit_number' => 'unit_number',
            'street_number' => 'street_number',
            'street_name' => 'street_name',
            'street_type' => 'street_type',
            'suburb' => 'suburb',
            'state' => 'state',
            'country' => 'country',
            'postcode' => 'postcode',
            'security_question' => 'security_question',
            'security_answer' => 'security_answer',
            'departure_reason' => 'departure_reason',
            'status' => 'active',
            'group' => 'group_id',
            'warehouse' => 'warehouse_id',
            'view_right' => 'view_right',
            'edit_right' => 'edit_right',
            'award_points' => 'award_points',
        );

        foreach ($field_map as $post_key => $property) {
            $value = $this->input->post($post_key, false);
            if ($value !== null) {
                $user->{$property} = $value;
            }
        }

        return $user;
    }

    protected function renderProfilePage($id, $user, $error_message = null)
    {
        $this->data['title'] = lang('profile');
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['user'] = $user;
        $this->data['groups'] = $this->ion_auth->groups()->result_array();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['user_notes'] = $this->user_notes_model->getNotesByUserId($id);
        $this->data['gamma_note_entry_date'] = set_value('note_entry_date');
        $this->data['gamma_note_note_date'] = set_value('note_note_date');
        $this->data['gamma_note_narrative'] = set_value('note_narrative');
        $this->data['error'] = $error_message !== null ? $error_message : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'class' => 'form-control',
            'type' => 'password',
            'value' => ''
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'class' => 'form-control',
            'type' => 'password',
            'value' => ''
        );
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->data['old_password'] = array(
            'name' => 'old',
            'id' => 'old',
            'class' => 'form-control',
            'type' => 'password',
        );
        $this->data['new_password'] = array(
            'name' => 'new',
            'id' => 'new',
            'type' => 'password',
            'class' => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        );
        $this->data['new_password_confirm'] = array(
            'name' => 'new_confirm',
            'id' => 'new_confirm',
            'type' => 'password',
            'class' => 'form-control',
            'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        );
        $this->data['user_id'] = array(
            'name' => 'user_id',
            'id' => 'user_id',
            'type' => 'hidden',
            'value' => $user->id,
        );
        $this->data['gamma_google_places_api_key'] = $this->config->item('gamma_google_places_api_key', 'gamma');
        $this->data['gamma_google_places_country'] = $this->config->item('gamma_google_places_country', 'gamma');
        $this->data['id'] = $id;

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('auth/users'), 'page' => lang('users')), array('link' => '#', 'page' => lang('profile')));
        $meta = array('page_title' => lang('profile'), 'bc' => $bc);
        $this->page_construct('auth/profile', $meta, $this->data);
    }

    function delete_user_note($user_id = NULL, $note_id = NULL)
    {
        $user_id = (int) $user_id;
        $note_id = (int) $note_id;

        if (!$this->loggedIn || (!$this->Owner && !$this->Admin)) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : admin_url('welcome'));
        }

        if ($user_id < 1 || $note_id < 1) {
            $this->session->set_flashdata('error', 'Invalid user note request.');
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : admin_url('auth/users'));
        }

        $note = $this->user_notes_model->getNoteById($note_id);
        if (!$note || (int) $note->user_id !== $user_id) {
            $this->session->set_flashdata('error', 'User note not found.');
            admin_redirect('auth/profile/' . $user_id);
        }

        if ($this->user_notes_model->deleteNote($note_id)) {
            $this->session->set_flashdata('message', 'User note deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Unable to delete the selected user note.');
        }

        redirect(admin_url('auth/profile/' . $user_id . '#notes'));
    }

    function add_user_note($user_id = NULL)
    {
        $user_id = (int) $user_id;

        if (!$this->loggedIn || (!$this->Owner && !$this->Admin)) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : admin_url('welcome'));
        }

        if ($user_id < 1 || !$this->ion_auth->user($user_id)->row()) {
            $this->session->set_flashdata('error', 'Invalid user note request.');
            admin_redirect('auth/users');
        }

        $this->createUserNoteIfProvided($user_id);
        $this->session->set_flashdata('message', 'User note saved successfully.');
        redirect(admin_url('auth/profile/' . $user_id . '#notes'));
    }

    public function captcha_check($cap)
    {
        $expiration = time() - 300; // 5 minutes limit
        $this->db->delete('captcha', array('captcha_time <' => $expiration));

        $this->db->select('COUNT(*) AS count')
            ->where('word', $cap)
            ->where('ip_address', $this->input->ip_address())
            ->where('captcha_time >', $expiration);

        if ($this->db->count_all_results('captcha')) {
            return true;
        } else {
            $this->form_validation->set_message('captcha_check', lang('captcha_wrong'));
            return FALSE;
        }
    }


    function login($m = NULL)
    {
        if ($this->loggedIn) {
            // If customer is logged in, redirect to dashboard instead of admin welcome
            if ($this->Customer) {
                redirect('dashboard');
            }
            // If already logged in as admin/staff, redirect to admin welcome
            if ($this->Staff || $this->Owner || $this->Admin) {
                admin_redirect('welcome');
            }
            $this->session->set_flashdata('error', $this->session->flashdata('error'));
            admin_redirect('welcome');
        }
        
        // If customer tries to access admin login, redirect them to frontend login
        if ($this->input->get('customer') || $this->input->post('customer')) {
            redirect('login');
        }
        $this->data['title'] = lang('login');

        if ($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }

        if ($this->form_validation->run() == true) {

            $remember = (bool)$this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                if ($this->Settings->mmode) {
                    if (!$this->ion_auth->in_group('owner')) {
                        $this->session->set_flashdata('error', lang('site_is_offline_plz_try_later'));
                        admin_redirect('auth/logout');
                    }
                }
                // Check user groups after login
                $is_customer = $this->ion_auth->in_group('customer');
                $is_supplier = $this->ion_auth->in_group('supplier');
                $is_staff = !$is_customer && !$is_supplier;
                
                if ($is_customer || $is_supplier) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    // Redirect customer users to dashboard, supplier to home
                    if ($is_customer) {
                        redirect('dashboard');
                    } else {
                        redirect(base_url());
                    }
                }
                
                // For admin/staff users, redirect to admin welcome
                if ($is_staff) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $referrer = ($this->session->userdata('requested_page') && $this->session->userdata('requested_page') != 'admin') ? $this->session->userdata('requested_page') : 'welcome';
                    admin_redirect($referrer);
                }
                
                // Fallback redirect
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                admin_redirect('welcome');
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                admin_redirect('login');
            }
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['message'] = $this->session->flashdata('message');
            if ($this->Settings->captcha) {
                $cap = $this->createCaptchaPayload(150, 34);
                if ($cap) {
                    $this->data['image'] = $cap['image'];
                    $this->data['captcha'] = array('name' => 'captcha',
                        'id' => 'captcha',
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => 'required',
                        'placeholder' => lang('type_captcha')
                    );
                } else {
                    $this->data['error'] = trim($this->data['error'] . ' CAPTCHA is unavailable on this server right now.');
                }
            }

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => lang('email'),
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => lang('password'),
            );
            $this->data['allow_reg'] = $this->Settings->allow_reg;
            if ($m == 'db') {
                $this->data['message'] = lang('db_restored');
            } elseif ($m && $m != '1') {
                // Only show under development for specific error cases, not for logout redirects
                // '1' is used for logout redirects, so skip showing the message for that
                $this->data['error'] = lang('we_are_sorry_as_this_sction_is_still_under_development.');
            }

            $this->load->view($this->theme . 'auth/login', $this->data);
        }
    }

    function reload_captcha()
    {
        $cap = $this->createCaptchaPayload(150, 34);
        echo $cap ? $cap['image'] : '';
    }

    function logout($m = NULL)
    {

        $logout = $this->ion_auth->logout();
        $this->session->set_flashdata('message', $this->ion_auth->messages());

        admin_redirect('login/' . $m);
    }

    function change_password()
    {
        if (!$this->ion_auth->logged_in()) {
            admin_redirect('login');
        }
        $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length[8]|max_length[25]');
        $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(admin_url('auth/profile/' . $user->id . '#cpassword'));
        } else {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                redirect(admin_url('auth/profile/' . $user->id . '#cpassword'));
            }

            $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

            $change = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

            if ($change) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect(admin_url('auth/profile/' . $user->id . '#cpassword'));
            }
        }
    }

    function forgot_password()
    {
        $this->form_validation->set_rules('forgot_email', lang('email_address'), 'required|valid_email');

        if ($this->form_validation->run() == false) {
            $error = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $error);
            admin_redirect("login#forgot_password");
        } else {

            $identity = $this->ion_auth->where('email', strtolower($this->input->post('forgot_email')))->users()->row();
            if (empty($identity)) {
                $this->ion_auth->set_message('forgot_password_email_not_found');
                $this->session->set_flashdata('error', $this->ion_auth->messages());
                admin_redirect("login#forgot_password");
            }

            $forgotten = $this->ion_auth->forgotten_password($identity->email);

            if ($forgotten) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                admin_redirect("login#forgot_password");
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                admin_redirect("login#forgot_password");
            }
        }
    }

    public function forgot_username()
    {
        if (strtolower($this->input->method()) !== 'post') {
            admin_redirect('login#forgot_username');
        }

        $this->form_validation->set_rules('recovery_email', lang('email_address'), 'required|valid_email');

        if ($this->form_validation->run() == false) {
            $error = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $error);
            admin_redirect('login#forgot_username');
        }

        $user = $this->findUserForUsernameReminder($this->input->post('recovery_email'));
        if (!$user || empty($user->email) || empty($user->username)) {
            $this->session->set_flashdata('error', 'We could not find an account for that email address.');
            admin_redirect('login#forgot_username');
        }

        try {
            $this->sendUsernameReminderEmail($user);
            $this->session->set_flashdata('message', 'Your username has been sent to your email address.');
        } catch (Exception $e) {
            log_message('error', 'Username reminder email failed: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'We could not send the username reminder email right now.');
        }

        admin_redirect('login#forgot_username');
    }

    public function send_reset_password($id = NULL)
    {
        if (!$this->loggedIn || (!$this->Owner && !$this->Admin)) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : admin_url('welcome'));
        }

        if (!$id) {
            show_404();
        }

        $user = $this->ion_auth->user($id)->row();
        if (!$user || empty($user->email)) {
            $this->session->set_flashdata('error', 'User account or email address was not found.');
            admin_redirect('auth/profile/' . $id);
        }

        $forgotten = $this->ion_auth->forgotten_password($user->email);
        if ($forgotten) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
        }

        admin_redirect('auth/profile/' . $id);
    }

    public function reset_password($code = NULL)
    {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {

            $this->form_validation->set_rules('new', lang('password'), 'required|min_length[8]|max_length[25]|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', lang('confirm_password'), 'required');

            if ($this->form_validation->run() == false) {

                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['message'] = $this->session->flashdata('message');
                $this->data['title'] = lang('reset_password');
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'class' => 'form-control',
                    'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
                    'data-bv-regexp-message' => lang('pasword_hint'),
                    'placeholder' => lang('new_password')
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'class' => 'form-control',
                    'data-bv-identical' => 'true',
                    'data-bv-identical-field' => 'new',
                    'data-bv-identical-message' => lang('pw_not_same'),
                    'placeholder' => lang('confirm_password')
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;
                $this->data['identity_label'] = $user->email;
                //render
                $this->load->view($this->theme . 'auth/reset_password', $this->data);
            } else {
                // do we have a valid request?
                if ($user->id != $this->input->post('user_id')) {

                    //something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);
                    show_error(lang('error_csrf'));

                } else {
                    // finally change the password
                    $identity = $user->email;

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        //if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        //$this->logout();
                        admin_redirect('login');
                    } else {
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        admin_redirect('auth/reset_password/' . $code);
                    }
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            admin_redirect("login#forgot_password");
        }
    }

    function activate($id, $code = false)
    {

        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } else if ($this->Owner) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            if ($this->Owner) {
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                admin_redirect("auth/login");
            }
        } else {
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            admin_redirect("forgot_password");
        }
    }

    function deactivate($id = NULL)
    {
        $this->sma->checkPermissions('users', TRUE);
        $id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;
        $this->form_validation->set_rules('confirm', lang("confirm"), 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('deactivate')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            } else {
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['user'] = $this->ion_auth->user($id)->row();
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'auth/deactivate_user', $this->data);
            }
        } else {

            if ($this->input->post('confirm') == 'yes') {
                if ($id != $this->input->post('id')) {
                    show_error(lang('error_csrf'));
                }

                if ($this->ion_auth->logged_in() && $this->Owner) {
                    $this->ion_auth->deactivate($id);
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                }
            }

            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function create_user()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['title'] = "Create User";
        $this->setGammaUserProfileValidationRules();
        $this->form_validation->set_rules('username', lang("username"), 'trim|is_unique[users.username]');
        $this->form_validation->set_rules('email', lang("email"), 'required|trim|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('status', lang("status"), 'trim|required');
        $this->form_validation->set_rules('group', lang("group"), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'trim|callback_valid_warehouse');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', lang('confirm_password'), 'required');

        $is_valid = ($this->form_validation->run() == true);
        if ($is_valid) {

            $username = strtolower($this->input->post('username'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'gender' => $this->input->post('gender'),
                'group_id' => $this->input->post('group') ? $this->input->post('group') : '3',
                'warehouse_id' => $this->normalizeWarehouseIdFromPost($this->input->post('warehouse')),
                'view_right' =>  $this->input->post('view_right'),
                'edit_right' => $this->input->post('edit_right'),
                'last_updated' => date('Y-m-d H:i:s'),
            );
            $additional_data = array_merge($additional_data, $this->getGammaUserProfileDataFromPost());
            $active = $this->input->post('status');
        }

        $new_user_id = false;
        if ($is_valid) {
            $new_user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $active, false);
        }

        if ($is_valid && $new_user_id) {
            $username = $this->input->post('username');
            $basePath = FCPATH . 'assets/document/';
            $directoryPath = $basePath . $username;
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }
            $this->load->library('gamma_path_service');
            $this->gamma_path_service->ensureUserFolders($username);
            $this->createUserNoteIfProvided($new_user_id);
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            admin_redirect("auth/users");

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
            $this->data['groups'] = $this->ion_auth->groups()->result_array();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['gamma_google_places_api_key'] = $this->config->item('gamma_google_places_api_key', 'gamma');
            $this->data['gamma_google_places_country'] = $this->config->item('gamma_google_places_country', 'gamma');
            $this->data['gamma_note_entry_date'] = set_value('note_entry_date');
            $this->data['gamma_note_note_date'] = set_value('note_note_date');
            $this->data['gamma_note_narrative'] = set_value('note_narrative');
            $bc = array(array('link' => admin_url('home'), 'page' => lang('home')), array('link' => admin_url('auth/users'), 'page' => lang('users')), array('link' => '#', 'page' => lang('create_user')));
            $meta = array('page_title' => lang('users'), 'bc' => $bc);
            $this->page_construct('auth/create_user', $meta, $this->data);
        }
    }

    function edit_user($id = NULL)
    {

        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $this->data['title'] = lang("edit_user");

        if (!$this->loggedIn || !$this->Owner && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $user = $this->ion_auth->user($id)->row();

        $this->setGammaUserProfileValidationRules();
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'trim|callback_valid_warehouse');

        if ($user->username != $this->input->post('username')) {
            $this->form_validation->set_rules('username', lang("username"), 'trim|is_unique[users.username]');
        }
        if ($user->email != $this->input->post('email')) {
            $this->form_validation->set_rules('email', lang("email"), 'trim|valid_email|is_unique[users.email]');
        } elseif ($this->input->post('email') !== null && $this->input->post('email') !== '') {
            $this->form_validation->set_rules('email', lang("email"), 'trim|valid_email');
        }
        if ($this->Owner && $this->input->post('password')) {
            $this->form_validation->set_rules('password', lang('edit_user_validation_password_label'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', lang('edit_user_validation_password_confirm_label'), 'required');
        }

        $is_valid = ($this->form_validation->run() === TRUE);
        if ($is_valid) {

            if ($this->Owner) {
                if ($id == $this->session->userdata('user_id')) {
                    $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'company' => $this->input->post('company'),
                        'phone' => $this->input->post('phone'),
                        'gender' => $this->input->post('gender'),
                        'last_updated' => date('Y-m-d H:i:s'),
                    );
                    $data = array_merge($data, $this->getGammaUserProfileDataFromPost());
                } elseif ($this->ion_auth->in_group('customer', $id) || $this->ion_auth->in_group('supplier', $id)) {
                    $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'company' => $this->input->post('company'),
                        'phone' => $this->input->post('phone'),
                        'gender' => $this->input->post('gender'),
                        'last_updated' => date('Y-m-d H:i:s'),
                    );
                    $data = array_merge($data, $this->getGammaUserProfileDataFromPost());
                } else {
                    $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'company' => $this->input->post('company'),
                        'username' => $this->input->post('username'),
                        'email' => $this->input->post('email'),
                        'phone' => $this->input->post('phone'),
                        'gender' => $this->input->post('gender'),
                        'active' => $this->input->post('status'),
                        'group_id' => $this->input->post('group'),
                        'warehouse_id' => $this->normalizeWarehouseIdFromPost($this->input->post('warehouse')),
                        'view_right' =>  $this->input->post('view_right'),
                        'edit_right' => $this->input->post('edit_right'),
                        'last_updated' => date('Y-m-d H:i:s'),
                    );
                    if ($this->db->field_exists('award_points', 'users')) {
                        $data['award_points'] = $this->input->post('award_points');
                    }
                    $data = array_merge($data, $this->getGammaUserProfileDataFromPost());
                }

            } elseif ($this->Admin) {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'gender' => $this->input->post('gender'),
                    'active' => $this->input->post('status'),
                    'last_updated' => date('Y-m-d H:i:s'),
                );
                if ($this->db->field_exists('award_points', 'users')) {
                    $data['award_points'] = $this->input->post('award_points');
                }
                $data = array_merge($data, $this->getGammaUserProfileDataFromPost());
            } else {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'gender' => $this->input->post('gender'),
                    'last_updated' => date('Y-m-d H:i:s'),
                );
                $data = array_merge($data, $this->getGammaUserProfileDataFromPost());
            }

            if ($this->Owner) {
                if ($this->input->post('password')) {
                    if (DEMO) {
                        $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                    $data['password'] = $this->input->post('password');
                }
            }
            //$this->sma->print_arrays($data);

        }
        if ($is_valid && $this->ion_auth->update($user->id, $data)) {
            $this->createUserNoteIfProvided($user->id);
            $this->session->set_flashdata('message', lang('user_updated'));
            admin_redirect("auth/profile/" . $id);
        } else {
            $user = $this->hydrateProfileUserFromPost($user);
            $error_message = validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error'));
            $this->renderProfilePage($id, $user, $error_message);
        }
    }


    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function _render_page($view, $data = null, $render = false)
    {

        $this->viewdata = (empty($data)) ? $this->data : $data;
        $view_html = $this->load->view('header', $this->viewdata, $render);
        $view_html .= $this->load->view($view, $this->viewdata, $render);
        $view_html = $this->load->view('footer', $this->viewdata, $render);

        if (!$render)
            return $view_html;
    }

    /**
     * @param null $id
     */
    function update_avatar($id = NULL)
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        if (!$this->ion_auth->logged_in() || !$this->Owner && $id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang("access_denied"));
            redirect(admin_url('auth/profile/' . $id . '#avatar'));
        }

        //validate form input
        $this->form_validation->set_rules('avatar', lang("avatar"), 'trim');

        if ($this->form_validation->run() == true) {

            if ($_FILES['avatar']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'assets/uploads/avatars';
                $config['allowed_types'] = 'gif|jpg|png';
                //$config['max_size'] = '500';
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('avatar')) {

                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect(admin_url('auth/profile/' . $id . '#avatar'));
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'assets/uploads/avatars/' . $photo;
                $config['new_image'] = 'assets/uploads/avatars/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 150;
                $config['height'] = 150;;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $user = $this->ion_auth->user($id)->row();
            } else {
                $this->form_validation->set_rules('avatar', lang("avatar"), 'required');
            }
        }

        if ($this->form_validation->run() == true && $this->auth_model->updateAvatar($id, $photo)) {
            unlink('assets/uploads/avatars/' . $user->avatar);
            unlink('assets/uploads/avatars/thumbs/' . $user->avatar);
            $this->session->set_userdata('avatar', $photo);
            $this->session->set_flashdata('message', lang("avatar_updated"));
            redirect(admin_url('auth/profile/' . $id . '#avatar'));
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(admin_url('auth/profile/' . $id . '#avatar'));
        }
    }

    function register()
    {
        $this->data['title'] = "Register";
        if (!$this->allow_reg) {
            $this->session->set_flashdata('error', lang('registration_is_disabled'));
            admin_redirect("login");
        }

        $this->form_validation->set_message('is_unique', lang('account_exists'));
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        $this->form_validation->set_rules('email', lang('email_address'), 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('usernam', lang('usernam'), 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');
        if ($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }

        if ($this->form_validation->run() == true) {
            $username = strtolower($this->input->post('username'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {

            $this->session->set_flashdata('message', $this->ion_auth->messages());
            admin_redirect("login");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
            $this->data['groups'] = $this->ion_auth->groups()->result_array();

            $cap = $this->createCaptchaPayload(150, 34, admin_url() . 'assets/captcha/');
            if ($cap) {
                $this->data['image'] = $cap['image'];
                $this->data['captcha'] = array('name' => 'captcha',
                    'id' => 'captcha',
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => lang('type_captcha')
                );
            } else {
                $this->data['error'] = trim($this->data['error'] . ' CAPTCHA is unavailable on this server right now.');
            }

            $this->data['first_name'] = array(
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'class' => 'form-control',
                'required' => 'required',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['company'] = array(
                'name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('company'),
            );
            $this->data['phone'] = array(
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'required' => 'required',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->load->view('auth/register', $this->data);
        }
    }

    function user_actions()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        if ($id != $this->session->userdata('user_id')) {
                            $this->auth_model->delete_user($id);
                        }
                    }
                    $this->session->set_flashdata('message', lang("users_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('sales'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('first_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('last_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('email'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('company'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('group'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $user = $this->site->getUser($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $user->first_name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $user->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $user->email);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $user->company);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->group);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $user->status);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'users_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_user_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function delete($id = NULL)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if ($this->input->get('id')) { $id = $this->input->get('id'); }

        if ( ! $this->Owner || $id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin/welcome');
        }

        if ($this->auth_model->delete_user($id)) {
            //echo lang("user_deleted");
            $this->session->set_flashdata('message', 'user_deleted');
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}
