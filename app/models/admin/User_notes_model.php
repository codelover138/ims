<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_notes_model extends CI_Model
{
    protected $table = 'gamma_user_notes';

    public function __construct()
    {
        parent::__construct();
    }

    public function getNotesByUserId($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1 || !$this->db->table_exists($this->table)) {
            return array();
        }

        return $this->db
            ->from($this->table)
            ->where('user_id', $user_id)
            ->order_by('note_date', 'DESC')
            ->order_by('entry_date', 'DESC')
            ->order_by('user_notes_id', 'DESC')
            ->get()
            ->result();
    }

    public function addNote($data)
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        return (bool) $this->db->insert($this->table, $data);
    }

    public function getNoteById($note_id)
    {
        $note_id = (int) $note_id;
        if ($note_id < 1 || !$this->db->table_exists($this->table)) {
            return null;
        }

        return $this->db
            ->from($this->table)
            ->where('user_notes_id', $note_id)
            ->get()
            ->row();
    }

    public function deleteNote($note_id)
    {
        $note_id = (int) $note_id;
        if ($note_id < 1 || !$this->db->table_exists($this->table)) {
            return false;
        }

        return (bool) $this->db
            ->where('user_notes_id', $note_id)
            ->delete($this->table);
    }
}
