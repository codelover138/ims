<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_form_model extends CI_Model
{
    protected $forms_table = 'gamma_forms';
    protected $inputs_table = 'gamma_form_inputs';

    public function __construct()
    {
        parent::__construct();
    }

    protected function now()
    {
        return date('Y-m-d H:i:s');
    }

    protected function getValue(array $row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return is_string($row[$key]) ? trim($row[$key]) : $row[$key];
            }
        }

        return $default;
    }

    protected function normalizeNullableValue($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if (strtolower($value) === 'null') {
            return null;
        }
        return $value === '' ? null : $value;
    }

    protected function normalizeIntegerValue($value)
    {
        $value = $this->normalizeNullableValue($value);
        return ($value !== null && is_numeric($value)) ? (int) $value : null;
    }

    protected function getInputDivisionsColumn()
    {
        return $this->db->field_exists('input_form_divisions', $this->db->dbprefix($this->inputs_table))
            ? 'input_form_divisions'
            : 'input_form_instructions';
    }

    protected function normalizeInputType($value)
    {
        $value = strtolower(trim((string) $value));
        $map = array(
            'textbox'     => 'Textbox',
            'text_box'    => 'Textbox',
            'text box'    => 'Textbox',
            'text'        => 'Textbox',
            'dropbox'     => 'Dropbox',
            'dropdown'    => 'Dropbox',
            'drop_down'   => 'Dropbox',
            'drop down'   => 'Dropbox',
            'select'      => 'Dropbox',
            'checkbox'    => 'Checkbox',
            'check_box'   => 'Checkbox',
            'check box'   => 'Checkbox',
            'textarea'    => 'Textarea',
            'text_area'   => 'Textarea',
            'text area'   => 'Textarea',
            'multiline'   => 'Textarea',
            'radio'       => 'Radio',
            'radiobutton' => 'Radio',
            'radio_button'=> 'Radio',
            'radio button'=> 'Radio',
            'multiselect' => 'Multiselect',
            'multi_select'=> 'Multiselect',
            'multi select'=> 'Multiselect',
            'multiple'    => 'Multiselect',
        );

        return $map[$value] ?? 'Textbox';
    }

    public function getFormsForUser($user_id)
    {
        $q = $this->db
            ->order_by('form_title', 'asc')
            ->get_where($this->forms_table, array('user_id' => $user_id));

        if ($q->num_rows() > 0) {
            return $q->result();
        }

        return array();
    }

    public function getAllForms()
    {
        $q = $this->db
            ->select('gf.*, CONCAT(COALESCE(u.first_name, \'\'), \' \', COALESCE(u.last_name, \'\')) AS assigned_name, u.username')
            ->from($this->forms_table . ' gf')
            ->join('users u', 'u.id = gf.user_id', 'left')
            ->order_by('gf.form_id', 'desc')
            ->get();

        return $q->num_rows() > 0 ? $q->result() : array();
    }

    public function getFormById($form_id, $user_id = null)
    {
        $this->db->from($this->forms_table)->where('form_id', (int) $form_id);
        if ($user_id !== null) {
            $this->db->where('user_id', (int) $user_id);
        }
        $q = $this->db->get();

        return $q->num_rows() ? $q->row() : null;
    }

    public function createForm(array $data)
    {
        $payload = array(
            'date_created' => $data['date_created'] ?? $this->now(),
            'user_id' => (int) $data['user_id'],
            'form_title' => trim((string) $data['form_title']),
            'description' => $this->normalizeNullableValue($data['description'] ?? null),
            'button_label' => $this->normalizeNullableValue($data['button_label'] ?? null),
            'input_form_location' => $this->normalizeNullableValue($data['input_form_location'] ?? null),
            'precedent_clause_location' => $this->normalizeNullableValue($data['precedent_clause_location'] ?? null),
            'document_creation_location' => $this->normalizeNullableValue($data['document_creation_location'] ?? null),
            'output_filename_base' => $this->normalizeNullableValue($data['output_filename_base'] ?? null),
            'output_file_location' => $this->normalizeNullableValue($data['output_file_location'] ?? null),
            'last_updated' => $data['last_updated'] ?? $this->now(),
        );

        $this->db->insert($this->forms_table, $payload);
        return (int) $this->db->insert_id();
    }

    public function createFormFromCsvRow(array $row)
    {
        $data = array(
            'user_id' => $this->getValue($row, array('userid', 'user_id', 'user id')),
            'form_title' => $this->getValue($row, array('formtitle', 'form_title', 'form title', 'title')),
            'description' => $this->getValue($row, array('description')),
            'button_label' => $this->getValue($row, array('buttonlabel', 'button_label', 'button label')),
            'input_form_location' => $this->getValue($row, array('inputformlocation', 'input_form_location', 'input form location')),
            'precedent_clause_location' => $this->getValue($row, array('precedentclauselocation', 'precedent_clause_location', 'precedent clause location')),
            'document_creation_location' => $this->getValue($row, array('documentcreationlocation', 'document_creation_location', 'document creation location')),
            'output_filename_base' => $this->getValue($row, array('outputfilenamebase', 'output_filename_base', 'output filename base')),
            'output_file_location' => $this->getValue($row, array('outputfilelocation', 'output_file_location', 'output file location')),
        );

        return $this->createForm($data);
    }

    public function getInputsForForm($form_id)
    {
        $q = $this->db
            ->select($this->inputs_table . '.*, ' . $this->getInputDivisionsColumn() . ' AS input_form_divisions')
            ->order_by('table_number', 'asc')
            ->order_by('table_row', 'asc')
            ->order_by('table_column', 'asc')
            ->order_by('line_command', 'asc')
            ->order_by('form_input_id', 'asc')
            ->get_where($this->inputs_table, array('form_id' => (int) $form_id));

        return $q->num_rows() ? $q->result() : array();
    }

    public function createFormInput(array $data)
    {
        $divisions_column = $this->getInputDivisionsColumn();
        $payload = array(
            'date_created' => $data['date_created'] ?? $this->now(),
            'form_id' => (int) $data['form_id'],
            'input_name' => trim((string) $data['input_name']),
            'input_label' => trim((string) $data['input_label']),
            'input_type' => $this->normalizeInputType($data['input_type'] ?? ''),
            'data_type' => $this->normalizeNullableValue($data['data_type'] ?? null),
            'default_value' => $this->normalizeNullableValue($data['default_value'] ?? null),
            'allowed_input' => $this->normalizeNullableValue($data['allowed_input'] ?? null),
            'max_size' => $this->normalizeIntegerValue($data['max_size'] ?? null),
            'line_command' => $this->normalizeNullableValue($data['line_command'] ?? null),
            'table_number' => $this->normalizeIntegerValue($data['table_number'] ?? null),
            'table_displayed' => $this->normalizeIntegerValue($data['table_displayed'] ?? null),
            'table_row' => $this->normalizeIntegerValue($data['table_row'] ?? null),
            'table_column' => $this->normalizeIntegerValue($data['table_column'] ?? null),
        );
        $payload[$divisions_column] = $this->normalizeNullableValue($data['input_form_divisions'] ?? ($data['input_form_instructions'] ?? null));

        $this->db->insert($this->inputs_table, $payload);
        return (int) $this->db->insert_id();
    }

    public function createFormInputFromCsvRow(array $row, $default_form_id = null)
    {
        $data = array(
            'form_id' => $this->getValue($row, array('formid', 'form_id', 'form id'), $default_form_id),
            'input_name' => $this->getValue($row, array('inputname', 'input_name', 'input name')),
            'input_label' => $this->getValue($row, array('inputlabel', 'input_label', 'input label')),
            'input_type' => $this->getValue($row, array('inputtype', 'input_type', 'input type')),
            'data_type' => $this->getValue($row, array('datatype', 'data_type', 'data type')),
            'default_value' => $this->getValue($row, array('defaultvalue', 'default_value', 'default value')),
            'allowed_input' => $this->getValue($row, array('allowedinput', 'allowed_input', 'allowed input')),
            'max_size' => $this->getValue($row, array('maxsize', 'max_size', 'max size')),
            'input_form_divisions' => $this->getValue($row, array('inputformdivisions', 'input_form_divisions', 'input form divisions', 'inputforminstructions', 'input_form_instructions', 'input form instructions')),
            'line_command' => $this->getValue($row, array('linecommand', 'line_command', 'line command')),
            'table_number' => $this->getValue($row, array('tablenumber', 'table_number', 'table number')),
            'table_displayed' => $this->getValue($row, array('tabledisplayed', 'table_displayed', 'table displayed')),
            'table_row' => $this->getValue($row, array('tablerow', 'table_row', 'table row')),
            'table_column' => $this->getValue($row, array('tablecolumn', 'table_column', 'table column')),
        );

        return $this->createFormInput($data);
    }

    public function deleteForm($form_id)
    {
        $this->db->delete($this->inputs_table, array('form_id' => (int) $form_id));
        $this->db->delete($this->forms_table, array('form_id' => (int) $form_id));
        return $this->db->affected_rows() > 0;
    }
}
