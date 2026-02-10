<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Immigrants_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate PrimaryID: Surname + First initial + Middle initial + 4 digit number e.g. MADIDEBP0001
     */
    public function generatePrimaryId($surname, $first_name, $middle_name = '')
    {
        $s = strtoupper(preg_replace('/[^A-Za-z]/', '', $surname));
        $f = $first_name ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $first_name), 0, 1)) : '';
        $m = $middle_name ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $middle_name), 0, 1)) : '';
        $prefix = $s . $f . $m;
        if (strlen($prefix) < 2) {
            $prefix = $prefix . 'X';
        }
        $next = $this->getNextSequenceNumber($prefix);
        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get next sequence number for a given prefix
     */
    protected function getNextSequenceNumber($prefix)
    {
        $this->db->select('primary_id');
        $this->db->from('immigrants');
        $this->db->like('primary_id', $prefix, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $last = $q->row()->primary_id;
            $num = (int) substr($last, strlen($prefix));
            return $num + 1;
        }
        return 1;
    }

    public function getImmigrantByID($id)
    {
        $q = $this->db->get_where('immigrants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addImmigrant($data = array())
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($this->db->insert('immigrants', $data)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    public function updateImmigrant($id, $data = array())
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        if ($this->db->update('immigrants', $data)) {
            return TRUE;
        }
        return FALSE;
    }

    public function deleteImmigrant($id)
    {
        if ($this->db->delete('immigrants', array('id' => $id))) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Get all immigrants for export (XLS/PDF)
     */
    public function getAllImmigrants()
    {
        $this->db->order_by('surname', 'ASC');
        $this->db->order_by('first_name', 'ASC');
        $q = $this->db->get('immigrants');
        return $q->num_rows() > 0 ? $q->result() : array();
    }
}
