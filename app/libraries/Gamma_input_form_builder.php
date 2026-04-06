<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_input_form_builder
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
    }

    protected function esc($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    protected function parseDivisions($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return array('heading' => '', 'subheading' => '');
        }

        $heading = '';
        $subheading = '';
        $parts = preg_split('/[\r\n;|]+/', $value);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (preg_match('/^Heading\d*\s*=\s*(.+)$/i', $part, $matches)) {
                $heading = trim($matches[1]);
                continue;
            }

            if (preg_match('/^Subheading\d*\s*=\s*(.+)$/i', $part, $matches)) {
                $subheading = trim($matches[1]);
            }
        }

        return array('heading' => $heading, 'subheading' => $subheading);
    }

    protected function parseOptions($input)
    {
        $raw = trim((string) ($input->allowed_input ?: ''));
        if ($raw === '') {
            $raw = trim((string) ($input->default_value ?: ''));
        }

        if ($raw === '') {
            return array();
        }

        $parts = preg_split('/[\r\n|,;]+/', $raw);
        $options = array();
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (strpos($part, '=') !== false) {
                list($key, $label) = array_map('trim', explode('=', $part, 2));
                $options[$key] = $label;
            } else {
                $options[$part] = $part;
            }
        }

        return $options;
    }

    protected function buildField($input, $name_override = null, $value_override = null)
    {
        $field_name = $name_override !== null ? $name_override : $input->input_name;
        $name = $this->esc($field_name);
        $label = $this->esc($input->input_label);
        $default = $value_override !== null ? $value_override : (string) $input->default_value;
        $max = $input->max_size ? ' maxlength="' . (int) $input->max_size . '"' : '';
        $type = strtolower((string) $input->input_type);
        $html = '<div class="form-group gamma-generated-field">';
        $html .= '<label>' . $label . '</label>';

        if ($type === 'checkbox') {
            $checked = in_array(strtolower((string) $default), array('1', 'yes', 'true', 'on'), true) ? ' checked="checked"' : '';
            $html .= '<div class="checkbox"><label><input type="checkbox" name="' . $name . '" value="1"' . $checked . '> ' . $label . '</label></div>';

        } elseif ($type === 'dropbox') {
            $html .= '<select name="' . $name . '" class="form-control">';
            foreach ($this->parseOptions($input) as $value => $option_label) {
                $selected = ((string) $value === $default) ? ' selected="selected"' : '';
                $html .= '<option value="' . $this->esc($value) . '"' . $selected . '>' . $this->esc($option_label) . '</option>';
            }
            $html .= '</select>';

        } elseif ($type === 'multiselect') {
            $defaults = array_map('trim', preg_split('/[\|,;]+/', $default));
            $html .= '<select name="' . $name . '[]" class="form-control" multiple="multiple" size="5">';
            foreach ($this->parseOptions($input) as $value => $option_label) {
                $selected = in_array((string) $value, $defaults, true) ? ' selected="selected"' : '';
                $html .= '<option value="' . $this->esc($value) . '"' . $selected . '>' . $this->esc($option_label) . '</option>';
            }
            $html .= '</select>';
            $html .= '<small class="help-block">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</small>';

        } elseif ($type === 'textarea') {
            $rows = $input->max_size ? max(3, (int) ceil($input->max_size / 80)) : 4;
            $rows = min($rows, 10);
            $html .= '<textarea name="' . $name . '" class="form-control" rows="' . $rows . '">' . $this->esc($default) . '</textarea>';

        } elseif ($type === 'radio') {
            $html .= '<div class="gamma-radio-group">';
            foreach ($this->parseOptions($input) as $value => $option_label) {
                $checked = ((string) $value === $default) ? ' checked="checked"' : '';
                $html .= '<div class="radio"><label>'
                    . '<input type="radio" name="' . $name . '" value="' . $this->esc($value) . '"' . $checked . '> '
                    . $this->esc($option_label)
                    . '</label></div>';
            }
            $html .= '</div>';

        } else {
            // Textbox — default
            $input_type = strtolower((string) $input->data_type);
            if (!in_array($input_type, array('date', 'email', 'number', 'text', 'datetime-local'), true)) {
                $input_type = 'text';
            }
            $html .= '<input type="' . $input_type . '" name="' . $name . '" value="' . $this->esc($default) . '" class="form-control"' . $max . '>';
        }

        $html .= '</div>';
        return $html;
    }

    protected function getTableColumns(array $inputs)
    {
        usort($inputs, function ($a, $b) {
            $column_compare = ((int) $a->table_column) <=> ((int) $b->table_column);
            if ($column_compare !== 0) {
                return $column_compare;
            }

            $row_compare = ((int) $a->table_row) <=> ((int) $b->table_row);
            if ($row_compare !== 0) {
                return $row_compare;
            }

            return ((int) $a->form_input_id) <=> ((int) $b->form_input_id);
        });

        $columns = array();
        $seen = array();
        foreach ($inputs as $input) {
            $key = trim((string) $input->input_name);
            if ($key === '') {
                $key = 'column_' . (int) $input->form_input_id;
            }

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $columns[] = $input;
        }

        return $columns;
    }

    protected function buildTableRow(array $columns, $table_number, $row_index)
    {
        $html = '<tr>';
        foreach ($columns as $column) {
            $name = 'gamma_tables[' . (int) $table_number . '][' . (int) $row_index . '][' . $column->input_name . ']';
            $html .= '<td data-column="' . (int) $column->table_column . '" data-row="' . (int) $column->table_row . '">';
            $html .= $this->buildField($column, $name);
            $html .= '</td>';
        }
        $html .= '</tr>';

        return $html;
    }

    protected function buildTable($table_number, array $inputs)
    {
        $displayed = 1;
        foreach ($inputs as $input) {
            if (!empty($input->table_displayed)) {
                $displayed = max($displayed, (int) $input->table_displayed);
            }
        }

        $columns = $this->getTableColumns($inputs);

        $html = '<div class="gamma-generated-table-wrap">';
        $html .= '<table class="table table-bordered table-striped gamma-generated-table" data-table-number="' . (int) $table_number . '">';
        $html .= '<thead><tr>';
        foreach ($columns as $column) {
            $html .= '<th data-column="' . (int) $column->table_column . '" data-row="' . (int) $column->table_row . '">' . $this->esc($column->input_label) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        for ($row_index = 0; $row_index < $displayed; $row_index++) {
            $html .= $this->buildTableRow($columns, $table_number, $row_index);
        }

        $html .= '</tbody></table>';
        $html .= '<div class="btn-group">';
        $html .= '<button type="button" class="btn btn-default btn-sm gamma-add-row" data-table-number="' . (int) $table_number . '">Add Row</button> ';
        $html .= '<button type="button" class="btn btn-default btn-sm gamma-remove-row" data-table-number="' . (int) $table_number . '">Remove Row</button>';
        $html .= '</div></div>';

        return $html;
    }

    public function build($form, array $inputs, $action_url)
    {
        $sections = array();
        $tables = array();

        foreach ($inputs as $input) {
            $division = $this->parseDivisions($input->input_form_divisions ?? '');
            $section_key = $division['heading'] . '||' . $division['subheading'];
            if (!isset($sections[$section_key])) {
                $sections[$section_key] = array(
                    'heading' => $division['heading'],
                    'subheading' => $division['subheading'],
                    'fields' => array(),
                    'tables' => array(),
                );
            }

            if (!empty($input->table_number)) {
                if (!isset($tables[$input->table_number])) {
                    $tables[$input->table_number] = array();
                }
                $tables[$input->table_number][] = $input;
                $sections[$section_key]['tables'][$input->table_number] = true;
            } else {
                $sections[$section_key]['fields'][] = $input;
            }
        }

        $html = '<div class="gamma-generated-form"><form method="post" action="' . $this->esc($action_url) . '">';
        $html .= '<div class="gamma-generated-header"><h3>' . $this->esc($form->form_title) . '</h3>';
        if (!empty($form->description)) {
            $html .= '<p>' . nl2br($this->esc($form->description)) . '</p>';
        }
        $html .= '</div>';

        foreach ($sections as $section) {
            if ($section['heading'] !== '') {
                $html .= '<h4 style="font-weight:700;">' . $this->esc($section['heading']) . '</h4>';
            }
            if ($section['subheading'] !== '') {
                $html .= '<div style="font-style:italic;margin-bottom:10px;">' . $this->esc($section['subheading']) . '</div>';
            }

            $line_groups = array();
            foreach ($section['fields'] as $field) {
                $line_key = trim((string) $field->line_command);
                if ($line_key === '') {
                    $line_key = 'field_' . $field->form_input_id;
                }
                if (!isset($line_groups[$line_key])) {
                    $line_groups[$line_key] = array();
                }
                $line_groups[$line_key][] = $field;
            }

            foreach ($line_groups as $group) {
                $width = max(1, (int) floor(12 / max(1, count($group))));
                $html .= '<div class="row">';
                foreach ($group as $field) {
                    $html .= '<div class="col-md-' . $width . '">' . $this->buildField($field) . '</div>';
                }
                $html .= '</div>';
            }

            foreach (array_keys($section['tables']) as $table_number) {
                $html .= $this->buildTable($table_number, $tables[$table_number]);
            }
        }

        $button_label = trim((string) ($form->button_label ?: 'Submit'));
        $html .= '<div class="form-group"><button type="submit" class="btn btn-primary">' . $this->esc($button_label) . '</button></div>';
        $html .= '</form></div>';
        $html .= '<script>
document.addEventListener("click", function (event) {
    if (event.target.matches(".gamma-add-row")) {
        var table = document.querySelector("table[data-table-number=\'" + event.target.getAttribute("data-table-number") + "\']");
        if (!table) { return; }
        var body = table.querySelector("tbody");
        var firstRow = body.querySelector("tr");
        if (!firstRow) { return; }
        var clone = firstRow.cloneNode(true);
        var nextIndex = body.querySelectorAll("tr").length;
        Array.prototype.forEach.call(clone.querySelectorAll("input, select, textarea"), function (field) {
            if (!field.name) { return; }
            field.name = field.name.replace(/gamma_tables\[(\d+)\]\[\d+\]/, "gamma_tables[$1][" + nextIndex + "]");
            if (field.type === "checkbox" || field.type === "radio") {
                field.checked = false;
            } else {
                field.value = "";
            }
        });
        body.appendChild(clone);
    }
    if (event.target.matches(".gamma-remove-row")) {
        var table = document.querySelector("table[data-table-number=\'" + event.target.getAttribute("data-table-number") + "\']");
        if (!table) { return; }
        var body = table.querySelector("tbody");
        if (body.children.length > 1) {
            body.removeChild(body.lastElementChild);
        }
    }
});
</script>';

        return $html;
    }
}
