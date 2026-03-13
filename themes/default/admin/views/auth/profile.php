<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">

    <div class="col-sm-2">
        <div class="row">
            <div class="col-sm-12 text-center">
                <div style="max-width:200px; margin: 0 auto;">
                    <?=
                    $user->avatar ? '<img alt="" src="' . base_url() . 'assets/uploads/avatars/thumbs/' . $user->avatar . '" class="avatar">' :
                        '<img alt="" src="' . base_url() . 'assets/images/' . $user->gender . '.png" class="avatar">';
                    ?>
                </div>
                <h4 class="gamma-profile-name"><?= htmlspecialchars(trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username, ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="gamma-profile-role"><?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><i class="fa fa-envelope"></i> <?= $user->email; ?></p>
            </div>
        </div>
    </div>

    <div class="col-sm-10">

        <ul id="myTab" class="nav nav-tabs">
            <li class=""><a href="#edit" class="tab-grey"><?= lang('edit') ?></a></li>
            <li class=""><a href="#cpassword" class="tab-grey"><?= lang('change_password') ?></a></li>
            <li class=""><a href="#avatar" class="tab-grey"><?= lang('avatar') ?></a></li>
        </ul>

        <div class="tab-content">
            <div id="edit" class="tab-pane fade in">

                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-edit nb"></i><?= lang('edit_profile'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">

                                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                                echo admin_form_open('auth/edit_user/' . $user->id, $attrib);
                                ?>
                                <div class="gamma-form-hero gamma-form-hero-sm">
                                    <div>
                                        <div class="gamma-form-eyebrow">Gamma Workspace</div>
                                        <h3 class="gamma-form-title gamma-form-title-sm">Edit User Profile</h3>
                                        <p class="gamma-form-subtitle">Maintain identity, contact, address, recovery, and access details from a single workspace.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-5">
                                            <div class="gamma-section-label"><i class="fa fa-id-card-o"></i> Identity and Contact</div>
                                            <div class="form-group">
                                                <?php echo lang('first_name', 'first_name'); ?>
                                                <div class="controls">
                                                    <?php echo form_input('first_name', $user->first_name, 'class="form-control" id="first_name" required="required"'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <?php echo lang('last_name', 'last_name'); ?>

                                                <div class="controls">
                                                    <?php echo form_input('last_name', $user->last_name, 'class="form-control" id="last_name" required="required"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="middle_name">Middle Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('middle_name', $user->middle_name, 'class="form-control" id="middle_name"'); ?>
                                                </div>
                                            </div>
                                            <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) { ?>
                                                <div class="form-group">
                                                    <?php echo lang('company', 'company'); ?>
                                                    <div class="controls">
                                                        <?php echo form_input('company', $user->company, 'class="form-control" id="company" required="required"'); ?>
                                                    </div>
                                                </div>
                                            <?php } else {
                                                echo form_hidden('company', $user->company);
                                            } ?>
                                            <div class="form-group">

                                                <?php echo lang('phone', 'phone'); ?>
                                                <div class="controls">
                                                    <input type="tel" name="phone" class="form-control" id="phone"
                                                           required="required" value="<?= $user->phone ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="birth_date">Birth Date</label>
                                                <div class="controls">
                                                    <?php echo form_input('birth_date', $user->birth_date, 'class="form-control" id="birth_date" placeholder="YYYY-MM-DD HH:MM:SS"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="business_name">Business Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('business_name', $user->business_name, 'class="form-control" id="business_name"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email2">Secondary Email</label>
                                                <div class="controls">
                                                    <input type="email" name="email2" class="form-control" id="email2" value="<?= htmlspecialchars($user->email2 ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="mobile_phone">Mobile Phone</label>
                                                <div class="controls">
                                                    <?php echo form_input('mobile_phone', $user->mobile_phone, 'class="form-control" id="mobile_phone"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="business_phone">Business Phone</label>
                                                <div class="controls">
                                                    <?php echo form_input('business_phone', $user->business_phone, 'class="form-control" id="business_phone"'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <?= lang('gender', 'gender'); ?>
                                                <div class="controls">  <?php
                                                    $ge[''] = array('male' => lang('male'), 'female' => lang('female'));
                                                    echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : $user->gender), 'class="tip form-control" id="gender" required="required"');
                                                    ?>
                                                </div>
                                            </div>
                                            <?php if (($Owner || $Admin) && $id != $this->session->userdata('user_id')) { ?>
                                            <div class="form-group">
                                                <?= lang('award_points', 'award_points'); ?>
                                                <?= form_input('award_points', set_value('award_points', $user->award_points), 'class="form-control tip" id="award_points"  required="required"'); ?>
                                            </div>
                                            <?php } ?>

                                            <?php if ($Owner && $id != $this->session->userdata('user_id')) { ?>
                                                <div class="form-group">
                                                    <?php echo lang('username', 'username'); ?>
                                                    <input type="text" name="username" class="form-control" readonly="readonly"
                                                           id="username" value="<?= $user->username ?>"
                                                           required="required"/>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo lang('email', 'email'); ?>

                                                    <input type="email" name="email" class="form-control" id="email"
                                                           value="<?= $user->email ?>" required="required"/>
                                                </div>
                                                <div class="row">
                                                    <div class="panel panel-warning">
                                                        <div
                                                            class="panel-heading"><?= lang('if_you_need_to_rest_password_for_user') ?></div>
                                                        <div class="panel-body" style="padding: 5px;">
                                                            <div class="col-md-12">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <?php echo lang('password', 'password'); ?>
                                                                        <?php echo form_input($password); ?>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <?php echo lang('confirm_password', 'password_confirm'); ?>
                                                                        <?php echo form_input($password_confirm); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                        </div>
                                        <div class="col-md-6 col-md-offset-1">
                                            <div class="gamma-section-label"><i class="fa fa-map-marker"></i> Address and Recovery</div>
                                            <div class="form-group">
                                                <label for="address_search">Address Search</label>
                                                <input type="text" id="address_search" class="form-control" placeholder="Search address with Google Places">
                                                <?php if (empty($gamma_google_places_api_key)) { ?>
                                                    <span class="help-block">Google Places is scaffolded. Add a real API key in app/config/gamma.php to enable autocomplete.</span>
                                                <?php } else { ?>
                                                    <span class="help-block">Select an address to auto-fill the fields below.</span>
                                                <?php } ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="unit_number">Unit Number</label>
                                                <div class="controls">
                                                    <?php echo form_input('unit_number', $user->unit_number, 'class="form-control" id="unit_number"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="street_number">Street Number</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_number', $user->street_number, 'class="form-control" id="street_number"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="street_name">Street Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_name', $user->street_name, 'class="form-control" id="street_name"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="street_type">Street Type</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_type', $user->street_type, 'class="form-control" id="street_type"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="suburb">Suburb</label>
                                                <div class="controls">
                                                    <?php echo form_input('suburb', $user->suburb, 'class="form-control" id="suburb"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="state">State</label>
                                                <div class="controls">
                                                    <?php echo form_input('state', $user->state, 'class="form-control" id="state"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="country">Country</label>
                                                <div class="controls">
                                                    <?php echo form_input('country', $user->country, 'class="form-control" id="country"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="postcode">Postcode</label>
                                                <div class="controls">
                                                    <?php echo form_input('postcode', $user->postcode, 'class="form-control" id="postcode"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="security_question">Security Question</label>
                                                <div class="controls">
                                                    <?php echo form_input('security_question', $user->security_question, 'class="form-control" id="security_question"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="security_answer">Security Answer</label>
                                                <div class="controls">
                                                    <?php echo form_input('security_answer', $user->security_answer, 'class="form-control" id="security_answer"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="departure_date">Departure Date</label>
                                                <div class="controls">
                                                    <?php echo form_input('departure_date', $user->departure_date, 'class="form-control" id="departure_date" placeholder="YYYY-MM-DD HH:MM:SS"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="departure_reason">Departure Reason</label>
                                                <div class="controls">
                                                    <?php echo form_input('departure_reason', $user->departure_reason, 'class="form-control" id="departure_reason"'); ?>
                                                </div>
                                            </div>
                                            <?php if ($Owner && $id != $this->session->userdata('user_id')) { ?>
                                                    <div style="margin-bottom: 16px;">
                                                        <a href="<?= admin_url('auth/send_reset_password/' . $id); ?>" class="btn btn-info">
                                                            <i class="fa fa-envelope"></i> Send Reset Password Email
                                                        </a>
                                                    </div>
                                                    <div class="gamma-section-label"><i class="fa fa-shield"></i> Access Setup</div>

                                                    <div class="row">
                                                        <div class="panel panel-warning">
                                                            <div class="panel-heading"><?= lang('user_options') ?></div>
                                                            <div class="panel-body" style="padding: 5px;">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <?= lang('status', 'status'); ?>
                                                                            <?php
                                                                            $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                                                            echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : $user->active), 'id="status" required="required" class="form-control input-tip select" style="width:100%;"');
                                                                            ?>
                                                                        </div>
                                                                        <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) { ?>
                                                                        <div class="form-group">
                                                                            <?= lang("group", "group"); ?>
                                                                            <?php
                                                                            $gp[""] = "";
                                                                            foreach ($groups as $group) {
                                                                                if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                                                                                    $gp[$group['id']] = $group['name'];
                                                                                }
                                                                            }
                                                                            echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : $user->group_id), 'id="group" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("group") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                                                            ?>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="no">
                                                                            <div class="form-group">
                                                                                <?= lang("biller", "biller"); ?>
                                                                                <?php
                                                                                $bl[""] = lang('select').' '.lang('biller');
                                                                                foreach ($billers as $biller) {
                                                                                    $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                                                                }
                                                                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $user->biller_id), 'id="biller" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("biller") . '" class="form-control select" style="width:100%;"');
                                                                                ?>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <?= lang("warehouse", "warehouse"); ?>
                                                                                <?php
                                                                                $wh[''] = lang('select').' '.lang('warehouse');
                                                                                foreach ($warehouses as $warehouse) {
                                                                                    $wh[$warehouse->id] = $warehouse->name;
                                                                                }
                                                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $user->warehouse_id), 'id="warehouse" class="form-control select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" style="width:100%;" ');
                                                                                ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang("view_right", "view_right"); ?>
                                                                                <?php
                                                                                $vropts = array(1 => lang('all_records'),2 => lang('Assign_Data_Only'));
                                                                                echo form_dropdown('view_right', $vropts, (isset($_POST['view_right']) ? $_POST['view_right'] : $user->view_right), 'id="view_right" class="form-control select" style="width:100%;"');
                                                                                ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang("edit_right", "edit_right"); ?>
                                                                                <?php
                                                                                $opts = array(1 => lang('yes'), 0 => lang('no'));
                                                                                echo form_dropdown('edit_right', $opts, (isset($_POST['edit_right']) ? $_POST['edit_right'] : $user->edit_right), 'id="edit_right" class="form-control select" style="width:100%;"');
                                                                                ?>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <?= lang("allow_discount", "allow_discount"); ?>
                                                                                <?= form_dropdown('allow_discount', $opts, (isset($_POST['allow_discount']) ? $_POST['allow_discount'] : $user->allow_discount), 'id="allow_discount" class="form-control select" style="width:100%;"'); ?>
                                                                            </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php } ?>
                                            <?php echo form_hidden('id', $id); ?>
                                            <?php echo form_hidden($csrf); ?>
                                        </div>
                                    </div>
                                </div>
                                <p><?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?></p>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cpassword" class="tab-pane fade">
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-key nb"></i><?= lang('change_password'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo admin_form_open("auth/change_password", 'id="change-password-form"'); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <?php echo lang('old_password', 'curr_password'); ?> <br/>
                                                <?php echo form_password('old_password', '', 'class="form-control" id="curr_password" required="required"'); ?>
                                            </div>

                                            <div class="form-group">
                                                <label
                                                    for="new_password"><?php echo sprintf(lang('new_password'), $min_password_length); ?></label>
                                                <br/>
                                                <?php echo form_password('new_password', '', 'class="form-control" id="new_password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?>
                                                <span class="help-block"><?= lang('pasword_hint') ?></span>
                                            </div>

                                            <div class="form-group">
                                                <?php echo lang('confirm_password', 'new_password_confirm'); ?> <br/>
                                                <?php echo form_password('new_password_confirm', '', 'class="form-control" id="new_password_confirm" required="required" data-bv-identical="true" data-bv-identical-field="new_password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>

                                            </div>
                                            <?php echo form_input($user_id); ?>
                                            <p><?php echo form_submit('change_password', lang('change_password'), 'class="btn btn-primary"'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="avatar" class="tab-pane fade">
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-file-picture-o nb"></i><?= lang('change_avatar'); ?></h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-md-5">
                                    <div style="position: relative;">
                                        <?php if ($user->avatar) { ?>
                                            <img alt=""
                                                 src="<?= base_url() ?>assets/uploads/avatars/<?= $user->avatar ?>"
                                                 class="profile-image img-thumbnail">
                                            <a href="#" class="btn btn-danger btn-xs po"
                                               style="position: absolute; top: 0;" title="<?= lang('delete_avatar') ?>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-block btn-danger po-delete' href='<?= admin_url('auth/delete_avatar/' . $id . '/' . $user->avatar) ?>'> <?= lang('i_m_sure') ?></a> <button class='btn btn-block po-close'> <?= lang('no') ?></button>"
                                               data-html="true" rel="popover"><i class="fa fa-trash-o"></i></a><br>
                                            <br><?php } ?>
                                    </div>
                                    <?php echo admin_form_open_multipart("auth/update_avatar"); ?>
                                    <div class="form-group">
                                        <?= lang("change_avatar", "change_avatar"); ?>
                                        <input type="file" data-browse-label="<?= lang('browse'); ?>" name="avatar" id="product_image" required="required"
                                               data-show-upload="false" data-show-preview="false" accept="image/*"
                                               class="form-control file"/>
                                    </div>
                                    <div class="form-group">
                                        <?php echo form_hidden('id', $id); ?>
                                        <?php echo form_hidden($csrf); ?>
                                        <?php echo form_submit('update_avatar', lang('update_avatar'), 'class="btn btn-primary"'); ?>
                                        <?php echo form_close(); ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#change-password-form').bootstrapValidator({
                message: 'Please enter/select a value',
                submitButtons: 'input[type="submit"]'
            });
        });
    </script>
    <?php if ($Owner && $id != $this->session->userdata('user_id')) { ?>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            $('#group').change(function (event) {
                var group = $(this).val();
                if (group == 1 || group == 2) {
                    $('.no').slideUp();
                } else {
                    $('.no').slideDown();
                }
            });
            var group = <?=$user->group_id?>;
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
    </script>
<?php } ?>
<style>
    .gamma-profile-name {
        margin: 16px 0 4px;
        font-size: 20px;
        font-weight: 700;
        color: #243847;
    }

    .gamma-profile-role {
        margin: 0 0 10px;
        color: #6d7f8a;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .gamma-form-hero {
        padding: 18px 22px;
        margin-bottom: 18px;
        border: 1px solid #dbe4ea;
        border-radius: 8px;
        background: linear-gradient(135deg, #f7fafc 0%, #eef5f9 100%);
    }

    .gamma-form-hero-sm {
        margin-top: 4px;
    }

    .gamma-form-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #6f8795;
        margin-bottom: 4px;
    }

    .gamma-form-title {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #233746;
    }

    .gamma-form-title-sm {
        font-size: 22px;
    }

    .gamma-form-subtitle {
        margin: 0;
        color: #5d707c;
    }

    .gamma-section-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 16px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #edf4f8;
        color: #335164;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    #edit .form-control {
        height: 40px;
        border-radius: 6px;
        border-color: #ccd7de;
        box-shadow: none;
    }

    #edit input.form-control:focus,
    #edit select.form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.12);
    }

    #myTab > li > a {
        border-radius: 6px 6px 0 0;
        font-weight: 600;
    }

    #edit .panel {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(36, 56, 70, 0.06);
    }

    #edit .panel-heading {
        font-weight: 700;
    }
</style>
<script type="text/javascript">
    function initGammaAddressAutocomplete() {
        var input = document.getElementById('address_search');
        if (!input || typeof google === 'undefined' || !google.maps || !google.maps.places) {
            return;
        }

        var options = {};
        <?php if (!empty($gamma_google_places_country)) { ?>
        options.componentRestrictions = { country: '<?= addslashes($gamma_google_places_country); ?>' };
        <?php } ?>

        var autocomplete = new google.maps.places.Autocomplete(input, options);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place || !place.address_components) {
                return;
            }

            var map = {
                street_number: '',
                route: '',
                subpremise: '',
                locality: '',
                postal_town: '',
                sublocality: '',
                sublocality_level_1: '',
                administrative_area_level_2: '',
                administrative_area_level_1: '',
                administrative_area_level_1_short: '',
                country: '',
                country_short: '',
                postal_code: ''
            };

            place.address_components.forEach(function (component) {
                component.types.forEach(function (type) {
                    if (Object.prototype.hasOwnProperty.call(map, type)) {
                        map[type] = component.long_name;
                    }
                    if (type === 'administrative_area_level_1') {
                        map.administrative_area_level_1_short = component.short_name || component.long_name;
                    }
                    if (type === 'country') {
                        map.country_short = component.short_name || component.long_name;
                    }
                });
            });

            var suburb = map.locality || map.postal_town || map.sublocality || map.sublocality_level_1 || map.administrative_area_level_2;
            var state = map.administrative_area_level_1_short || map.administrative_area_level_1;
            var country = map.country || map.country_short;

            var setValue = function (id, value) {
                var field = document.getElementById(id);
                if (field && value) {
                    field.value = value;
                }
            };

            setValue('unit_number', map.subpremise);
            setValue('street_number', map.street_number);
            setValue('street_name', map.route);
            setValue('suburb', suburb);
            setValue('state', state);
            setValue('country', country);
            setValue('postcode', map.postal_code);
        });
    }
</script>
<?php if (!empty($gamma_google_places_api_key)) { ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($gamma_google_places_api_key); ?>&libraries=places&callback=initGammaAddressAutocomplete"></script>
<?php } ?>
