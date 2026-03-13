<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_form_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFormsForUser($user_id)
    {
        $q = $this->db
            ->order_by('form_title', 'asc')
            ->get_where('gamma_forms', array('user_id' => $user_id));

        if ($q->num_rows() > 0) {
            return $q->result();
        }

        return array();
    }
}
