<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.immigrant-form .panel {
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.immigrant-form .panel-heading {
    font-weight: 600;
    padding: 12px 16px;
    border-radius: 6px 6px 0 0;
}

.immigrant-form .panel-heading i {
    margin-right: 8px;
    opacity: 0.9;
}

.immigrant-form .panel-body {
    padding: 20px 18px;
}

.immigrant-form .form-group {
    margin-bottom: 18px;
}

.immigrant-form .form-group label,
.immigrant-form .form-group .control-label {
    font-weight: 600;
    color: #444;
}

.immigrant-form .form-control {
    border-radius: 4px;
    padding: 8px 12px;
}

.immigrant-form .form-actions {
    padding: 20px 0 10px;
    border-top: 1px solid #eee;
    margin-top: 10px;
}
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i> <?= lang('add_immigrant'); ?></h2>
        <div class="box-icon">
            <a href="<?= admin_url('immigrants'); ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i>
                <?= lang('back'); ?></a>
        </div>
    </div>
    <div class="box-content">
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-immigrant-form');
        echo admin_form_open_multipart("immigrants/add", $attrib); ?>
        <div class="immigrant-form">
            <div class="row">
                <div class="col-lg-12">
                    <?php if ($error) { ?>
                    <div class="alert alert-danger"><?= $error; ?></div>
                    <?php } ?>
                    <p class="introtext"><?= lang('enter_info'); ?></p>

                    <!-- Personal Details -->
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><i class="fa fa-user"></i> <?= lang('personal_details'); ?></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("surname", "surname"); ?> <span class="text-danger">*</span>
                                            <?= form_input('surname', set_value('surname'), 'class="form-control input-tip" id="surname" required'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("first_name", "first_name"); ?> <span class="text-danger">*</span>
                                            <?= form_input('first_name', set_value('first_name'), 'class="form-control input-tip" id="first_name" required'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("middle_name", "middle_name"); ?>
                                            <?= form_input('middle_name', set_value('middle_name'), 'class="form-control input-tip" id="middle_name"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("phone_number", "phone_number"); ?>
                                            <?= form_input('phone_number', set_value('phone_number'), 'class="form-control input-tip" id="phone_number" placeholder="+27..."'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("country_of_origin", "country_of_origin"); ?>
                                            <?= form_input('country_of_origin', set_value('country_of_origin'), 'class="form-control input-tip" id="country_of_origin"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("last_date_of_entry", "last_date_of_entry"); ?>
                                            <?= form_input('last_date_of_entry', set_value('last_date_of_entry'), 'class="form-control input-tip date" id="last_date_of_entry"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("validity_of_stay", "validity_of_stay"); ?>
                                            <?= form_input('validity_of_stay', set_value('validity_of_stay'), 'class="form-control input-tip date" id="validity_of_stay"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <!-- Identity & Documents (IDs/permits) -->
                    <div class="col-md-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading"><i class="fa fa-id-card"></i> <?= lang('identity_documents'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("passport_number", "passport_number"); ?>
                                            <?= form_input('passport_number', set_value('passport_number'), 'class="form-control input-tip" id="passport_number"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("id_number", "id_number"); ?>
                                            <?= form_input('id_number', set_value('id_number'), 'class="form-control input-tip" id="id_number"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("asylum_seeker_number", "asylum_seeker_number"); ?>
                                            <?= form_input('asylum_seeker_number', set_value('asylum_seeker_number'), 'class="form-control input-tip" id="asylum_seeker_number"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("work_permit_number", "work_permit_number"); ?>
                                            <?= form_input('work_permit_number', set_value('work_permit_number'), 'class="form-control input-tip" id="work_permit_number"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>

                <!-- Documents (uploaded with form on save) -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa fa-paperclip"></i> <?= lang('documents_upload'); ?>
                        </div>
                        <div class="panel-body">
                            <p class="text-muted" style="margin-bottom:12px;"><?= lang('documents_saved_with_form'); ?>
                            </p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("doc_head_shot", "doc_head_shot"); ?>
                                        <input type="file" name="doc_head_shot" id="doc_head_shot"
                                            class="form-control file" accept="image/*" capture="user"
                                            data-browse-label="<?= lang('browse'); ?>" data-show-upload="false"
                                            data-show-preview="false" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("doc_passport", "doc_passport"); ?>
                                        <input type="file" name="doc_passport" id="doc_passport"
                                            class="form-control file" accept="image/*,application/pdf"
                                            data-browse-label="<?= lang('browse'); ?>" data-show-upload="false"
                                            data-show-preview="false" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("doc_asylum_permit", "doc_asylum_permit"); ?>
                                        <input type="file" name="doc_asylum_permit" id="doc_asylum_permit"
                                            class="form-control file" accept="image/*,application/pdf"
                                            data-browse-label="<?= lang('browse'); ?>" data-show-upload="false"
                                            data-show-preview="false" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("doc_work_permit", "doc_work_permit"); ?>
                                        <input type="file" name="doc_work_permit" id="doc_work_permit"
                                            class="form-control file" accept="image/*,application/pdf"
                                            data-browse-label="<?= lang('browse'); ?>" data-show-upload="false"
                                            data-show-preview="false" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("doc_other", "doc_other"); ?>
                                        <input type="file" name="doc_other" id="doc_other" class="form-control file"
                                            accept="image/*,application/pdf" data-browse-label="<?= lang('browse'); ?>"
                                            data-show-upload="false" data-show-preview="false" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <!-- Where They Stay -->
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading"><i class="fa fa-map-marker"></i> <?= lang('residence'); ?></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("municipality", "municipality"); ?>
                                            <?= form_input('municipality', set_value('municipality'), 'class="form-control input-tip" id="municipality"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("town", "town"); ?>
                                            <?= form_input('town', set_value('town'), 'class="form-control input-tip" id="town"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("area", "area"); ?>
                                            <?= form_input('area', set_value('area'), 'class="form-control input-tip" id="area"'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= lang("narrative_direction", "narrative_direction"); ?>
                                            <?= form_textarea('narrative_direction', set_value('narrative_direction'), 'class="form-control input-tip" id="narrative_direction" rows="2" placeholder="Narrative or direction to where they stay"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <!-- Employment -->
                    <div class="col-md-12">
                        <div class="panel panel-success">
                            <div class="panel-heading"><i class="fa fa-briefcase"></i> <?= lang('employment'); ?></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("work_status", "work_status"); ?>
                                            <?php
                                        $ws = array('' => lang('select') . ' ' . lang('work_status'), 'Employed' => 'Employed', 'Street Vendor' => 'Street Vendor', 'Business' => 'Business', 'Unemployed' => 'Unemployed');
                                        echo form_dropdown('work_status', $ws, set_value('work_status'), 'class="form-control input-tip select" id="work_status" style="width:100%;"');
                                        ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= lang("line_of_business", "line_of_business"); ?>
                                            <?= form_textarea('line_of_business', set_value('line_of_business'), 'class="form-control input-tip" id="line_of_business" rows="3" placeholder="Narrative description of line of business"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-12 form-actions">
                        <?= form_submit('add_immigrant', lang('add_immigrant'), 'class="btn btn-primary"'); ?>
                        <a href="<?= admin_url('immigrants'); ?>" class="btn btn-default"><?= lang('cancel'); ?></a>
                    </div>
                    <!-- </div> -->
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#last_date_of_entry, #validity_of_stay').datetimepicker({
            format: site.dateFormats.js_sdate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
        $('select.select').select2({
            minimumResultsForSearch: 7
        });
    });
    </script>