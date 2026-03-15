<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$date_placeholder = $dateFormats['php_sdate'];
$phone_pattern = '^[0-9\\s\\-\\+\\(\\)\\.]{7,20}$';
$birth_date_value = !empty($user->birth_date) && $user->birth_date !== '0000-00-00 00:00:00' ? date($dateFormats['php_sdate'], strtotime($user->birth_date)) : '';
$departure_date_value = !empty($user->departure_date) && $user->departure_date !== '0000-00-00 00:00:00' ? date($dateFormats['php_sdate'], strtotime($user->departure_date)) : '';
$note_entry_value = $gamma_note_entry_date ?? '';
$note_note_value = $gamma_note_note_date ?? '';
$note_narrative_value = $gamma_note_narrative ?? '';
$can_delete_user_notes = !empty($Owner) || !empty($Admin);
?>
<div class="row gamma-profile-shell">

    <div class="col-sm-2 gamma-profile-sidebar">
        <div class="row">
            <div class="col-sm-12 text-center">
                <div class="gamma-profile-card">
                    <div style="max-width:200px; margin: 0 auto;">
                        <?=
                        $user->avatar ? '<img alt="" src="' . base_url() . 'assets/uploads/avatars/thumbs/' . $user->avatar . '" class="avatar">' :
                            '<img alt="" src="' . base_url() . 'assets/images/' . $user->gender . '.png" class="avatar">';
                        ?>
                    </div>
                    <h4 class="gamma-profile-name"><?= htmlspecialchars(trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="gamma-profile-role"><?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="gamma-profile-contact"><i class="fa fa-envelope"></i> <?= $user->email; ?></p>
                    <div class="gamma-profile-meta">
                        <span><i class="fa fa-circle"></i> <?= !empty($user->active) ? lang('active') : lang('inactive'); ?></span>
                        <span><i class="fa fa-users"></i> <?= isset($groups) ? count($groups) . ' groups loaded' : 'User profile'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-10 gamma-profile-main">

        <ul id="myTab" class="nav nav-tabs gamma-profile-tabs">
            <li class=""><a href="#edit" class="tab-grey"><?= lang('edit') ?></a></li>
            <li class=""><a href="#notes" class="tab-grey">User Notes</a></li>
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
                                    <div class="gamma-profile-hero-badges">
                                        <span class="gamma-profile-badge"><i class="fa fa-shield"></i> Secure admin edit</span>
                                        <span class="gamma-profile-badge"><i class="fa fa-calendar"></i> Structured data</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-5 gamma-profile-column gamma-profile-column-main">
                                            <div class="gamma-section-label"><i class="fa fa-id-card-o"></i> Identity and Contact</div>
                                            <div class="form-group gamma-form-group">
                                                <label for="first_name"><i class="fa fa-user"></i> <?php echo lang('first_name'); ?></label>
                                                <div class="controls">
                                                    <?php echo form_input('first_name', $user->first_name, 'class="form-control gamma-input" id="first_name" required="required"'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group gamma-form-group">
                                                <label for="last_name"><i class="fa fa-user"></i> <?php echo lang('last_name'); ?></label>
                                                <div class="controls">
                                                    <?php echo form_input('last_name', $user->last_name, 'class="form-control gamma-input" id="last_name" required="required"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="middle_name"><i class="fa fa-user"></i> Middle Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('middle_name', $user->middle_name, 'class="form-control gamma-input" id="middle_name"'); ?>
                                                </div>
                                            </div>
                                            <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) { ?>
                                                <div class="form-group gamma-form-group">
                                                    <label for="company"><i class="fa fa-building"></i> <?php echo lang('company'); ?></label>
                                                    <div class="controls">
                                                        <?php echo form_input('company', $user->company, 'class="form-control gamma-input" id="company" required="required"'); ?>
                                                    </div>
                                                </div>
                                            <?php } else {
                                                echo form_hidden('company', $user->company);
                                            } ?>
                                            <div class="form-group gamma-form-group">
                                                <label for="phone"><i class="fa fa-phone"></i> <?php echo lang('phone'); ?></label>
                                                <div class="controls">
                                                    <input type="tel" name="phone" class="form-control gamma-input" id="phone"
                                                           required="required" value="<?= $user->phone ?>" pattern="<?= $phone_pattern; ?>" inputmode="tel"/>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="birth_date"><i class="fa fa-calendar"></i> Birth Date</label>
                                                <div class="controls">
                                                    <?php echo form_input('birth_date', set_value('birth_date', $birth_date_value), 'class="form-control gamma-input user-date-picker" id="birth_date" placeholder="' . $date_placeholder . '" autocomplete="off"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="business_name"><i class="fa fa-briefcase"></i> Business Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('business_name', $user->business_name, 'class="form-control gamma-input" id="business_name"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="email2"><i class="fa fa-envelope-o"></i> Secondary Email</label>
                                                <div class="controls">
                                                    <input type="email" name="email2" class="form-control gamma-input" id="email2" value="<?= htmlspecialchars($user->email2 ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="mobile_phone"><i class="fa fa-mobile"></i> Mobile Phone</label>
                                                <div class="controls">
                                                    <?php echo form_input('mobile_phone', $user->mobile_phone, 'type="tel" class="form-control gamma-input" id="mobile_phone" pattern="' . $phone_pattern . '" inputmode="tel"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="business_phone"><i class="fa fa-phone-square"></i> Business Phone</label>
                                                <div class="controls">
                                                    <?php echo form_input('business_phone', $user->business_phone, 'type="tel" class="form-control gamma-input" id="business_phone" pattern="' . $phone_pattern . '" inputmode="tel"'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group gamma-form-group">
                                                <label for="gender"><i class="fa fa-venus-mars"></i> <?= lang('gender'); ?></label>
                                                <div class="controls">  <?php
                                                    $ge[''] = array('male' => lang('male'), 'female' => lang('female'));
                                                    echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : $user->gender), 'class="tip form-control gamma-input" id="gender" required="required"');
                                                    ?>
                                                </div>
                                            </div>
                                            <?php if (($Owner || $Admin) && $id != $this->session->userdata('user_id') && $this->db->field_exists('award_points', 'users')) { ?>
                                            <div class="form-group gamma-form-group">
                                                <label for="award_points"><i class="fa fa-star"></i> <?= lang('award_points'); ?></label>
                                                <?= form_input('award_points', set_value('award_points', $user->award_points), 'class="form-control tip gamma-input" id="award_points"  required="required"'); ?>
                                            </div>
                                            <?php } ?>

                                            <?php if ($Owner && $id != $this->session->userdata('user_id')) { ?>
                                                <div class="form-group gamma-form-group">
                                                    <label for="username"><i class="fa fa-user-circle"></i> <?php echo lang('username'); ?></label>
                                                    <input type="text" name="username" class="form-control gamma-input" readonly="readonly"
                                                           id="username" value="<?= $user->username ?>"
                                                           required="required"/>
                                                </div>
                                                <div class="form-group gamma-form-group">
                                                    <label for="email"><i class="fa fa-envelope"></i> <?php echo lang('email'); ?></label>
                                                    <input type="email" name="email" class="form-control gamma-input" id="email"
                                                           value="<?= $user->email ?>" required="required"/>
                                                </div>
                                                <div class="row">
                                                    <div class="panel panel-warning">
                                                        <div
                                                            class="panel-heading"><?= lang('if_you_need_to_rest_password_for_user') ?></div>
                                                        <div class="panel-body" style="padding: 5px;">
                                                            <div class="col-md-12">
                                                                <div class="col-md-12">
                                                                    <div class="form-group gamma-form-group">
                                                                        <label for="password"><i class="fa fa-lock"></i> <?php echo lang('password'); ?></label>
                                                                        <?php echo form_input($password); ?>
                                                                    </div>

                                                                    <div class="form-group gamma-form-group">
                                                                        <label for="password_confirm"><i class="fa fa-lock"></i> <?php echo lang('confirm_password'); ?></label>
                                                                        <?php echo form_input($password_confirm); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                        </div>
                                        <div class="col-md-6 col-md-offset-1 gamma-profile-column gamma-profile-column-side">
                                            <div class="gamma-section-label"><i class="fa fa-map-marker"></i> Address and Recovery</div>
                                            <div class="form-group gamma-form-group">
                                                <label for="address_search"><i class="fa fa-search"></i> Address Search</label>
                                                <input type="text" id="address_search" class="form-control gamma-input" placeholder="Search address with Google Places">
                                                <?php if (empty($gamma_google_places_api_key)) { ?>
                                                    <span class="help-block">Google Places is scaffolded. Add a real API key in app/config/gamma.php to enable autocomplete.</span>
                                                <?php } else { ?>
                                                    <span class="help-block">Select an address to auto-fill the fields below.</span>
                                                <?php } ?>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="unit_number"><i class="fa fa-home"></i> Unit Number</label>
                                                <div class="controls">
                                                    <?php echo form_input('unit_number', $user->unit_number, 'class="form-control gamma-input" id="unit_number"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="street_number"><i class="fa fa-road"></i> Street Number</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_number', $user->street_number, 'class="form-control gamma-input" id="street_number"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="street_name"><i class="fa fa-road"></i> Street Name</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_name', $user->street_name, 'class="form-control gamma-input" id="street_name"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="street_type"><i class="fa fa-road"></i> Street Type</label>
                                                <div class="controls">
                                                    <?php echo form_input('street_type', $user->street_type, 'class="form-control gamma-input" id="street_type"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="suburb"><i class="fa fa-map-marker"></i> Suburb</label>
                                                <div class="controls">
                                                    <?php echo form_input('suburb', $user->suburb, 'class="form-control gamma-input" id="suburb"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="state"><i class="fa fa-flag"></i> State</label>
                                                <div class="controls">
                                                    <?php echo form_input('state', $user->state, 'class="form-control gamma-input" id="state"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="country"><i class="fa fa-globe"></i> Country</label>
                                                <div class="controls">
                                                    <?php echo form_input('country', $user->country, 'class="form-control gamma-input" id="country"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="postcode"><i class="fa fa-envelope"></i> Postcode</label>
                                                <div class="controls">
                                                    <?php echo form_input('postcode', $user->postcode, 'class="form-control gamma-input" id="postcode"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="security_question"><i class="fa fa-question-circle"></i> Security Question</label>
                                                <div class="controls">
                                                    <?php echo form_input('security_question', $user->security_question, 'class="form-control gamma-input" id="security_question"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="security_answer"><i class="fa fa-key"></i> Security Answer</label>
                                                <div class="controls">
                                                    <?php echo form_input('security_answer', $user->security_answer, 'class="form-control gamma-input" id="security_answer"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="departure_date"><i class="fa fa-calendar-times-o"></i> Departure Date</label>
                                                <div class="controls">
                                                    <?php echo form_input('departure_date', set_value('departure_date', $departure_date_value), 'class="form-control gamma-input user-date-picker" id="departure_date" placeholder="' . $date_placeholder . '" autocomplete="off"'); ?>
                                                </div>
                                            </div>
                                            <div class="form-group gamma-form-group">
                                                <label for="departure_reason"><i class="fa fa-sign-out"></i> Departure Reason</label>
                                                <div class="controls">
                                                    <?php echo form_input('departure_reason', $user->departure_reason, 'class="form-control gamma-input" id="departure_reason"'); ?>
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
<div class="form-group gamma-form-group">
                                                                        <label for="status"><i class="fa fa-toggle-on"></i> <?= lang('status'); ?></label>
                                                                        <?php
                                                                        $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                                                        echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : $user->active), 'id="status" required="required" class="form-control input-tip select gamma-input" style="width:100%;"');
                                                                        ?>
                                                                    </div>
                                                                    <?php if (!$this->ion_auth->in_group('customer', $id) && !$this->ion_auth->in_group('supplier', $id)) { ?>
                                                                    <div class="form-group gamma-form-group">
                                                                        <label for="group"><i class="fa fa-users"></i> <?= lang("group"); ?></label>
                                                                        <?php
                                                                        $gp[""] = "";
                                                                        foreach ($groups as $group) {
                                                                            if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                                                                                $gp[$group['id']] = $group['name'];
                                                                            }
                                                                        }
                                                                        echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : $user->group_id), 'id="group" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("group") . '" required="required" class="form-control input-tip select gamma-input" style="width:100%;"');
                                                                        ?>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <div class="no">
                                                                            <div class="form-group gamma-form-group">
                                                                                <label for="warehouse"><i class="fa fa-warehouse"></i> <?= lang("warehouse"); ?></label>
                                                                                <?php
                                                                                $wh[''] = lang('select').' '.lang('warehouse');
                                                                                foreach ($warehouses as $warehouse) {
                                                                                    $wh[$warehouse->id] = $warehouse->name;
                                                                                }
                                                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $user->warehouse_id), 'id="warehouse" class="form-control select gamma-input" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" style="width:100%;" ');
                                                                                ?>
                                                                            </div>
                                                                            <div class="form-group gamma-form-group">
                                                                                <label for="view_right"><i class="fa fa-eye"></i> <?= lang("view_right"); ?></label>
                                                                                <?php
                                                                                $vropts = array(1 => lang('all_records'),2 => lang('Assign_Data_Only'));
                                                                                echo form_dropdown('view_right', $vropts, (isset($_POST['view_right']) ? $_POST['view_right'] : $user->view_right), 'id="view_right" class="form-control select gamma-input" style="width:100%;"');
                                                                                ?>
                                                                            </div>
                                                                            <div class="form-group gamma-form-group">
                                                                                <label for="edit_right"><i class="fa fa-edit"></i> <?= lang("edit_right"); ?></label>
                                                                                <?php
                                                                                $opts = array(1 => lang('yes'), 0 => lang('no'));
                                                                                echo form_dropdown('edit_right', $opts, (isset($_POST['edit_right']) ? $_POST['edit_right'] : $user->edit_right), 'id="edit_right" class="form-control select gamma-input" style="width:100%;"');
                                                                                ?>
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
                                <div class="gamma-edit-actions">
                                    <span class="gamma-edit-actions-copy">Changes are saved immediately to the user profile after validation.</span>
                                    <p><?php echo form_submit('update', lang('update'), 'class="btn btn-primary gamma-primary-action"'); ?></p>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="notes" class="tab-pane fade">
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i class="fa-fw fa fa-sticky-note-o nb"></i>User Notes</h2>
                    </div>
                    <div class="box-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo admin_form_open('auth/add_user_note/' . $id, 'class="form-horizontal" role="form"'); ?>
                                <div class="gamma-form-hero gamma-form-hero-sm">
                                    <div>
                                        <div class="gamma-form-eyebrow">Gamma Workspace</div>
                                        <h3 class="gamma-form-title gamma-form-title-sm">User Notes</h3>
                                        <p class="gamma-form-subtitle">Keep dated internal notes, follow-up comments, and history for this user profile in one place.</p>
                                    </div>
                                </div>
                                <div class="gamma-profile-column gamma-profile-column-main">
                                    <div class="gamma-section-label"><i class="fa fa-plus-circle"></i> Add New Note</div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group gamma-form-group">
                                                <label for="note_entry_date"><i class="fa fa-clock-o"></i> Entry Date</label>
                                                <?php echo form_input('note_entry_date', $note_entry_value, 'class="form-control gamma-input user-date-picker" id="note_entry_date" placeholder="' . $date_placeholder . '" autocomplete="off"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group gamma-form-group">
                                                <label for="note_note_date"><i class="fa fa-calendar"></i> Note Date</label>
                                                <?php echo form_input('note_note_date', $note_note_value, 'class="form-control gamma-input user-date-picker" id="note_note_date" placeholder="' . $date_placeholder . '" autocomplete="off"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group gamma-form-group">
                                        <label for="note_narrative"><i class="fa fa-file-text-o"></i> Narrative</label>
                                        <textarea name="note_narrative" class="form-control gamma-input" id="note_narrative" rows="4" placeholder="Write a note for this user profile."><?= html_escape($note_narrative_value); ?></textarea>
                                    </div>
                                    <div class="gamma-edit-actions">
                                        <span class="gamma-edit-actions-copy">Save a dated note without editing the rest of the user profile.</span>
                                        <p><?php echo form_submit('save_user_note', 'Save Note', 'class="btn btn-primary gamma-primary-action"'); ?></p>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>

                                <div class="gamma-profile-column gamma-profile-column-side" style="margin-top: 22px;">
                                    <div class="gamma-section-label"><i class="fa fa-history"></i> Note History</div>
                                    <div class="gamma-user-notes-list">
                                        <?php if (!empty($user_notes)) { ?>
                                            <?php foreach ($user_notes as $note) { ?>
                                                <div class="gamma-user-note-item">
                                                    <div class="gamma-user-note-meta">
                                                        <span><i class="fa fa-calendar"></i> Note: <?= !empty($note->note_date) ? $this->sma->hrld($note->note_date) : '-'; ?></span>
                                                        <span><i class="fa fa-clock-o"></i> Entry: <?= !empty($note->entry_date) ? $this->sma->hrld($note->entry_date) : '-'; ?></span>
                                                        <?php if ($can_delete_user_notes) { ?>
                                                            <a href="<?= admin_url('auth/delete_user_note/' . $id . '/' . $note->user_notes_id); ?>" class="gamma-user-note-delete gamma-user-note-delete-trigger" data-href="<?= admin_url('auth/delete_user_note/' . $id . '/' . $note->user_notes_id); ?>">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="gamma-user-note-body"><?= nl2br(html_escape(strip_tags((string) ($note->narrative ?? '')))); ?></div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="gamma-user-note-empty">No notes have been added for this user yet.</div>
                                        <?php } ?>
                                    </div>
                                </div>
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
    <div id="user-note-delete-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete User Note</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this note?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirm-user-note-delete" class="btn btn-danger">Delete</a>
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

            function activateProfileTab(hash) {
                if (!hash || $('#myTab a[href="' + hash + '"]').length === 0) {
                    hash = '#edit';
                }

                $('#myTab a[href="' + hash + '"]').tab('show');
            }

            activateProfileTab(window.location.hash);

            $('#myTab a').on('shown.bs.tab', function (e) {
                if (history.replaceState) {
                    history.replaceState(null, null, $(e.target).attr('href'));
                } else {
                    window.location.hash = $(e.target).attr('href');
                }
            });

            $('.gamma-user-note-delete-trigger').on('click', function (e) {
                e.preventDefault();
                $('#confirm-user-note-delete').attr('href', $(this).data('href'));
                $('#user-note-delete-modal').modal('show');
            });

            $('.user-date-picker').datetimepicker({
                format: site.dateFormats.js_sdate,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2
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
    .gamma-profile-shell {
        margin-top: 8px;
    }

    .gamma-profile-sidebar {
        padding-right: 8px;
        position: relative;
    }

    .gamma-profile-main {
        padding-left: 8px;
    }

    .gamma-profile-sidebar .row {
        position: sticky;
        top: 18px;
    }

    .gamma-profile-card {
        padding: 22px 18px;
        border: 1px solid #d9e4ea;
        border-radius: 18px;
        background:
            radial-gradient(circle at top center, rgba(60, 141, 188, 0.1), transparent 34%),
            linear-gradient(180deg, #ffffff 0%, #f5fafc 100%);
        box-shadow: 0 16px 34px rgba(32, 57, 72, 0.08);
        position: relative;
        overflow: hidden;
    }

    .gamma-profile-card:before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, #3c8dbc 0%, #6aa9ce 100%);
    }

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

    .gamma-profile-contact {
        margin-bottom: 14px;
        color: #486270;
    }

    .gamma-profile-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-top: 14px;
        border-top: 1px solid #deeaef;
        font-size: 12px;
        font-weight: 700;
        color: #68808d;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .gamma-profile-meta span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .gamma-form-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 22px;
        margin-bottom: 18px;
        border: 1px solid #dbe4ea;
        border-radius: 16px;
        background:
            radial-gradient(circle at top right, rgba(60, 141, 188, 0.1), transparent 30%),
            linear-gradient(135deg, #f7fafc 0%, #eef5f9 100%);
        box-shadow: 0 12px 28px rgba(33, 57, 72, 0.06);
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

    .gamma-profile-hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .gamma-profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #dbe7ed;
        color: #33596d;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(34, 56, 70, 0.05);
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

    .gamma-profile-column {
        padding: 22px 20px 18px;
        border: 1px solid #dbe5eb;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
        box-shadow: 0 14px 32px rgba(36, 56, 70, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .gamma-profile-column:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(36, 56, 70, 0.08);
    }

    .gamma-profile-column-main,
    .gamma-profile-column-side {
        min-height: 100%;
    }

    #edit .form-control {
        height: 42px;
        border-radius: 10px;
        border-color: #ccd7de;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    #edit input.form-control:focus,
    #edit select.form-control:focus,
    #edit textarea.form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.12);
        transform: translateY(-1px);
    }

    #edit textarea.form-control {
        min-height: 110px;
        resize: vertical;
    }

    .gamma-profile-tabs {
        margin-bottom: 18px;
        border-bottom: none;
    }

    #myTab > li {
        margin-right: 8px;
    }

    #myTab > li > a {
        border-radius: 999px;
        font-weight: 700;
        border: 1px solid #d6e2e9;
        background: #f7fafb;
        color: #45606e;
        padding: 11px 16px;
        transition: all 0.2s ease;
    }

    #myTab > li.active > a,
    #myTab > li.active > a:hover,
    #myTab > li.active > a:focus {
        background: linear-gradient(135deg, #3c8dbc 0%, #5fa7d3 100%);
        border-color: #3c8dbc;
        color: #fff;
        box-shadow: 0 12px 24px rgba(60, 141, 188, 0.16);
    }

    #myTab > li > a:hover,
    #myTab > li > a:focus {
        background: #ffffff;
        border-color: #bfd5df;
        color: #294b5c;
    }

    #edit .panel {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(36, 56, 70, 0.06);
    }

    #edit .panel-heading {
        font-weight: 700;
    }

    .gamma-edit-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 22px;
        padding: 18px 20px;
        border: 1px solid #d9e5eb;
        border-radius: 16px;
        background: linear-gradient(180deg, #f9fcfd 0%, #f0f7fa 100%);
    }

    .gamma-edit-actions-copy {
        color: #607884;
        line-height: 1.5;
    }

    .gamma-primary-action {
        min-width: 140px;
        height: 44px;
        border-radius: 10px;
        padding: 0 22px;
        font-weight: 700;
        letter-spacing: 0.03em;
        box-shadow: 0 12px 24px rgba(60, 141, 188, 0.16);
    }

    .gamma-user-notes-list {
        margin-top: 14px;
        padding-top: 6px;
    }

    .gamma-user-note-item {
        padding: 14px 16px;
        margin-bottom: 12px;
        border: 1px solid #d9e4ea;
        border-radius: 14px;
        background: #fbfdfe;
    }

    .gamma-user-note-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        margin-bottom: 8px;
        color: #5f7784;
        font-size: 12px;
        font-weight: 700;
    }

    .gamma-user-note-body {
        color: #314c5c;
        line-height: 1.6;
    }

    .gamma-user-note-delete {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #c35656;
        text-decoration: none;
    }

    .gamma-user-note-delete:hover,
    .gamma-user-note-delete:focus {
        color: #a63d3d;
        text-decoration: none;
    }

    .gamma-user-note-empty {
        padding: 14px 16px;
        border: 1px dashed #ccd8df;
        border-radius: 14px;
        color: #6a818d;
        background: #fbfdfe;
    }

    @media (max-width: 991px) {
        .gamma-profile-sidebar,
        .gamma-profile-main {
            padding-left: 15px;
            padding-right: 15px;
        }

        .gamma-profile-sidebar .row {
            position: static;
        }

        .gamma-form-hero {
            flex-direction: column;
        }

        .gamma-profile-hero-badges {
            justify-content: flex-start;
        }

        .gamma-profile-column {
            margin-bottom: 18px;
        }

        .gamma-edit-actions {
            flex-direction: column;
            align-items: stretch;
        }
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
