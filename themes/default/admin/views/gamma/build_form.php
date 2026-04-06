<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i>Build Custom Gamma Form</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext">Please fill in the information below. The field labels are for your reference and are used to name the input in the template.</p>

                <?php $attrib = array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'buildForm');
                echo admin_form_open_multipart("gamma_forms/build_form", $attrib); ?>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <?= lang("form_title", "form_title"); ?> <span class="text-danger">*</span>
                            <?= form_input('form_title', set_value('form_title'), 'class="form-control" id="form_title" required="required" placeholder="e.g. Employee Contract"'); ?>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <?= lang("description", "description"); ?>
                            <?= form_input('description', set_value('description'), 'class="form-control" id="description" placeholder="Optional brief description of this form"'); ?>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Form Fields</h4>
                <p>Add the inputs you want for this form below. We will automatically generate the input variables when the form is built.</p>
                
                <div class="table-responsive">
                    <table id="fieldsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Field Label <span class="text-danger">*</span> (e.g. Employee Name)</th>
                                <th>Field Type <span class="text-danger">*</span></th>
                                <th>Options / Allowed Input (Comma Separated)</th>
                                <th>Default Value</th>
                                <th style="width: 50px; text-align: center;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody id="fieldsContainer">
                            <!-- Field Rows will be dynamically added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" id="addFieldBtn" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Add Field</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group">
                    <?php echo form_submit('build_form', 'Save & Generate Form', 'class="btn btn-primary"'); ?>
                </div>

                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        let fieldCounter = 0;

        function addFieldRow() {
            let tr = `
                <tr id="row_${fieldCounter}">
                    <td>
                        <input type="text" name="fields[${fieldCounter}][input_label]" class="form-control" required="required" placeholder="e.g. Full Name" oninput="generateInputName(this, ${fieldCounter})">
                        <small class="text-muted">Var: $<span id="var_${fieldCounter}">-</span></small>
                        <input type="hidden" name="fields[${fieldCounter}][input_name]" id="name_${fieldCounter}" value="">
                    </td>
                    <td>
                        <select name="fields[${fieldCounter}][input_type]" class="form-control" required="required">
                            <option value="Textbox">Text Input</option>
                            <option value="Textarea">Text Area (Multiline)</option>
                            <option value="Dropbox">Dropdown Select</option>
                            <option value="Checkbox">Checkbox</option>
                            <option value="Radio">Radio Buttons</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="fields[${fieldCounter}][allowed_input]" class="form-control" placeholder="E.g. Option A, Option B (For drops/radios)">
                    </td>
                    <td>
                        <input type="text" name="fields[${fieldCounter}][default_value]" class="form-control" placeholder="Optional Default">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeFieldRow(${fieldCounter})"><i class="fa fa-trash-o"></i></button>
                    </td>
                </tr>
            `;

            $('#fieldsContainer').append(tr);
            fieldCounter++;
        }

        window.removeFieldRow = function(id) {
            $('#row_' + id).remove();
        };

        window.generateInputName = function(element, id) {
            let label = $(element).val();
            // simple slugify
            let name = label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/(^_|_$)/g, '');
            if(!name) { name = 'field_' + id; }
            $('#var_' + id).text(name);
            $('#name_' + id).val(name);
        };

        $('#addFieldBtn').click(function () {
            addFieldRow();
        });

        // Add an initial field
        addFieldRow();
        
        $('#buildForm').submit(function(e) {
            if ($('#fieldsContainer tr').length === 0) {
                e.preventDefault();
                alert('Please add at least one field to your form.');
                return false;
            }
            return true;
        });
    });
</script>
