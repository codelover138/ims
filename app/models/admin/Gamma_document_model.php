<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_document_model extends CI_Model
{
    protected $logs_table = 'gamma_output_file_logs';
    protected $records_table = 'gamma_input_records';

    public function __construct()
    {
        parent::__construct();
    }

    protected function now()
    {
        return date('Y-m-d H:i:s');
    }

    public function createOutputFileLog($form_id, $output_file)
    {
        $this->db->insert($this->logs_table, array(
            'date_created' => $this->now(),
            'form_id' => (int) $form_id,
            'output_file' => trim((string) $output_file),
        ));

        return (int) $this->db->insert_id();
    }

    public function createInputRecord($output_file_id, $input_name, $input_value)
    {
        $this->db->insert($this->records_table, array(
            'date_created' => $this->now(),
            'output_file_id' => (int) $output_file_id,
            'input_name' => trim((string) $input_name),
            'input_value' => is_scalar($input_value) || $input_value === null ? (string) $input_value : json_encode($input_value),
        ));

        return (int) $this->db->insert_id();
    }

    protected function flattenRecords(array $records, $prefix = '')
    {
        $flattened = array();

        foreach ($records as $name => $value) {
            $key = $prefix === '' ? (string) $name : $prefix . '.' . $name;
            if (is_array($value)) {
                $flattened += $this->flattenRecords($value, $key);
                continue;
            }

            $flattened[$key] = $value;
        }

        return $flattened;
    }

    public function createInputRecords($output_file_id, array $records)
    {
        foreach ($this->flattenRecords($records) as $name => $value) {
            $this->createInputRecord($output_file_id, $name, $value);
        }
    }

    public function getOutputFileLogById($output_file_id)
    {
        $q = $this->db->get_where($this->logs_table, array('output_file_id' => (int) $output_file_id));
        return $q->num_rows() ? $q->row() : null;
    }

    public function getOutputFileLogForUser($output_file_id, $user_id)
    {
        $q = $this->db
            ->select('gol.*, gf.user_id, gf.form_title, gf.output_filename_base')
            ->from($this->logs_table . ' gol')
            ->join('gamma_forms gf', 'gf.form_id = gol.form_id', 'inner')
            ->where('gol.output_file_id', (int) $output_file_id)
            ->where('gf.user_id', (int) $user_id)
            ->get();

        return $q->num_rows() ? $q->row() : null;
    }

    public function getOutputFilesForUser($user_id, $limit = 20)
    {
        $q = $this->db
            ->select('gol.*, gf.form_title, gf.output_filename_base')
            ->from($this->logs_table . ' gol')
            ->join('gamma_forms gf', 'gf.form_id = gol.form_id', 'inner')
            ->where('gf.user_id', (int) $user_id)
            ->order_by('gol.output_file_id', 'desc')
            ->limit((int) $limit)
            ->get();

        return $q->num_rows() ? $q->result() : array();
    }

    public function deleteInputRecordsForOutput($output_file_id)
    {
        $this->db->delete($this->records_table, array('output_file_id' => (int) $output_file_id));
    }

    public function deleteOutputFileLog($output_file_id)
    {
        $this->deleteInputRecordsForOutput($output_file_id);
        $this->db->delete($this->logs_table, array('output_file_id' => (int) $output_file_id));
    }
}
