<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-upload"></i><span class="break"></span>Import Forms CSV</h2>
            </div>
            <div class="box-content">
                <p>Upload a CSV that inserts one or more rows into the Gamma form table and creates the precedent and document creation files for each imported form.</p>
                <?php echo admin_form_open_multipart('gamma_forms/import_forms_csv', array('role' => 'form')); ?>
                <div class="form-group">
                    <label for="csv_file">CSV File</label>
                    <div class="input-group">
                        <label class="input-group-btn">
                            <span class="btn btn-primary">
                                <i class="fa fa-folder-open"></i> Browse&hellip;
                                <input type="file" name="csv_file" id="csv_file" accept=".csv" required="required" style="display:none;">
                            </span>
                        </label>
                        <input type="text" class="form-control" id="csv_file_name" placeholder="No file chosen" readonly>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Import Forms</button>
                <a href="<?= admin_url('gamma_forms'); ?>" class="btn btn-default">Back</a>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('csv_file').addEventListener('change', function () {
    var name = this.files.length ? this.files[0].name : 'No file chosen';
    document.getElementById('csv_file_name').value = name;
});
</script>
