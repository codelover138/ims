<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i>User Onboarding</h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="gamma-form-hero">
                    <div>
                        <div class="gamma-form-eyebrow">Gamma Workspace</div>
                        <h3 class="gamma-form-title">Build a complete user profile</h3>
                        <p class="gamma-form-subtitle">Capture contact details, structured address data, recovery information, and access permissions from one polished workspace.</p>
                    </div>
                    <div class="gamma-form-badge">
                        <i class="fa fa-user-plus"></i>
                        <span>Ready for setup</span>
                    </div>
                </div>

                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open("auth/create_user", $attrib);

                $ge[''] = array('male' => lang('male'), 'female' => lang('female'));
                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                $opts = array(1 => lang('yes'), 0 => lang('no'));
                $vropts = array(1 => lang('all_records'), 2 => lang('Assign_Data_Only'));

                foreach ($groups as $group) {
                    if ($group['name'] != 'customer' && $group['name'] != 'supplier') {
                        $gp[$group['id']] = $group['name'];
                    }
                }

                $bl[""] = lang('select') . ' ' . lang('biller');
                foreach ($billers as $biller) {
                    $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                }

                $wh[''] = lang('select') . ' ' . lang('warehouse');
                foreach ($warehouses as $warehouse) {
                    $wh[$warehouse->id] = $warehouse->name;
                }
                ?>

                <div class="row create-user-page">
                    <div class="col-md-7">
                        <div class="panel panel-default gamma-panel gamma-panel-soft">
                            <div class="panel-heading">
                                <strong><i class="fa fa-id-card-o"></i> Identity and Contact</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="first_name"><?php echo lang('first_name'); ?></label>
                                            <?php echo form_input('first_name', set_value('first_name'), 'class="form-control" id="first_name" required="required" pattern=".{3,10}"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="last_name"><?php echo lang('last_name'); ?></label>
                                            <?php echo form_input('last_name', set_value('last_name'), 'class="form-control" id="last_name" required="required"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <?php echo form_input('middle_name', set_value('middle_name'), 'class="form-control" id="middle_name"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="gender"><?= lang('gender'); ?></label>
                                            <?php echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="company"><?php echo lang('company'); ?></label>
                                    <?php echo form_input('company', set_value('company'), 'class="form-control" id="company" required="required"'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="business_name">Business Name</label>
                                            <?php echo form_input('business_name', set_value('business_name'), 'class="form-control" id="business_name"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="birth_date">Birth Date</label>
                                            <?php echo form_input('birth_date', set_value('birth_date'), 'class="form-control" id="birth_date" placeholder="YYYY-MM-DD HH:MM:SS"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="phone"><?php echo lang('phone'); ?></label>
                                            <?php echo form_input('phone', set_value('phone'), 'class="form-control" id="phone" required="required"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mobile_phone">Mobile Phone</label>
                                            <?php echo form_input('mobile_phone', set_value('mobile_phone'), 'class="form-control" id="mobile_phone"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="business_phone">Business Phone</label>
                                            <?php echo form_input('business_phone', set_value('business_phone'), 'class="form-control" id="business_phone"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email"><?php echo lang('email'); ?></label>
                                            <input type="email" id="email" name="email" class="form-control" value="<?= set_value('email'); ?>" required="required"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email2">Secondary Email</label>
                                            <input type="email" id="email2" name="email2" class="form-control" value="<?= set_value('email2'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="username"><?php echo lang('username'); ?></label>
                                            <input type="text" id="username" name="username" class="form-control" value="<?= set_value('username'); ?>" required="required" pattern=".{4,20}"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="password"><?php echo lang('password'); ?></label>
                                            <?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?>
                                            <span class="help-block"><?= lang('pasword_hint') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="confirm_password"><?php echo lang('confirm_password'); ?></label>
                                            <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default gamma-panel gamma-panel-soft">
                            <div class="panel-heading">
                                <strong><i class="fa fa-map-marker"></i> Address and Recovery</strong>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="address_search">Address Search</label>
                                    <input type="text" id="address_search" class="form-control" placeholder="Search address with Google Places">
                                    <?php if (empty($gamma_google_places_api_key)) { ?>
                                        <span class="help-block">Google Places is scaffolded. Add a real API key in app/config/gamma.php to enable autocomplete.</span>
                                    <?php } else { ?>
                                        <span class="help-block">Select an address to auto-fill the fields below.</span>
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="unit_number">Unit Number</label>
                                            <?php echo form_input('unit_number', set_value('unit_number'), 'class="form-control" id="unit_number"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="street_number">Street Number</label>
                                            <?php echo form_input('street_number', set_value('street_number'), 'class="form-control" id="street_number"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="street_type">Street Type</label>
                                            <?php echo form_input('street_type', set_value('street_type'), 'class="form-control" id="street_type"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="street_name">Street Name</label>
                                    <?php echo form_input('street_name', set_value('street_name'), 'class="form-control" id="street_name"'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="suburb">Suburb</label>
                                            <?php echo form_input('suburb', set_value('suburb'), 'class="form-control" id="suburb"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <?php echo form_input('state', set_value('state'), 'class="form-control" id="state"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="country">Country</label>
                                            <?php echo form_input('country', set_value('country'), 'class="form-control" id="country"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="postcode">Postcode</label>
                                            <?php echo form_input('postcode', set_value('postcode'), 'class="form-control" id="postcode"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="security_question">Security Question</label>
                                    <?php echo form_input('security_question', set_value('security_question'), 'class="form-control" id="security_question"'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="security_answer">Security Answer</label>
                                    <?php echo form_input('security_answer', set_value('security_answer'), 'class="form-control" id="security_answer"'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="departure_date">Departure Date</label>
                                            <?php echo form_input('departure_date', set_value('departure_date'), 'class="form-control" id="departure_date" placeholder="YYYY-MM-DD HH:MM:SS"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="departure_reason">Departure Reason</label>
                                            <?php echo form_input('departure_reason', set_value('departure_reason'), 'class="form-control" id="departure_reason"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="panel panel-primary gamma-panel gamma-panel-accent">
                            <div class="panel-heading">
                                <strong><i class="fa fa-shield"></i> Access Setup</strong>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted gamma-panel-copy">Configure permissions, assignments, and notification settings before creating the account.</p>

                                <div class="form-group">
                                    <label for="status"><?= lang('status'); ?></label>
                                    <?= form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" required="required" class="form-control select" style="width:100%;"'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="group"><?= lang('group'); ?></label>
                                    <?= form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : ''), 'id="group" required="required" class="form-control select" style="width:100%;"'); ?>
                                </div>

                                <div class="no">
                                    <div class="form-group">
                                        <label for="biller"><?= lang('biller'); ?></label>
                                        <?= form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ''), 'id="biller" class="form-control select" style="width:100%;"'); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="warehouse"><?= lang('warehouse'); ?></label>
                                        <?= form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ''), 'id="warehouse" class="form-control select" style="width:100%;"'); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="view_right"><?= lang('view_right'); ?></label>
                                        <?= form_dropdown('view_right', $vropts, (isset($_POST['view_right']) ? $_POST['view_right'] : 1), 'id="view_right" class="form-control select" style="width:100%;"'); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_right"><?= lang('edit_right'); ?></label>
                                        <?= form_dropdown('edit_right', $opts, (isset($_POST['edit_right']) ? $_POST['edit_right'] : 0), 'id="edit_right" class="form-control select" style="width:100%;"'); ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="allow_discount"><?= lang('allow_discount'); ?></label>
                                        <?= form_dropdown('allow_discount', $opts, (isset($_POST['allow_discount']) ? $_POST['allow_discount'] : 0), 'id="allow_discount" class="form-control select" style="width:100%;"'); ?>
                                    </div>
                                </div>

                                <div class="well well-sm gamma-check-well" style="margin-top: 20px;">
                                    <label class="checkbox" for="notify" style="margin: 0;">
                                        <input type="checkbox" name="notify" value="1" id="notify" checked="checked"/>
                                        <?= lang('notify_user_by_email') ?>
                                    </label>
                                </div>

                                <p style="margin: 0;">
                                    <?php echo form_submit('add_user', lang('add_user'), 'class="btn btn-primary btn-block btn-lg gamma-submit-btn"'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<style>
    .gamma-form-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        padding: 24px 26px;
        margin-bottom: 18px;
        border: 1px solid #cfe0e8;
        border-radius: 14px;
        background:
            radial-gradient(circle at top right, rgba(37, 114, 146, 0.16), transparent 28%),
            linear-gradient(135deg, #f8fbfc 0%, #edf5f8 52%, #fdfefe 100%);
        overflow: hidden;
    }

    .gamma-form-hero:before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 7px;
        background: linear-gradient(180deg, #1f6f8b 0%, #2d8cb3 100%);
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
        font-size: 30px;
        font-weight: 700;
        line-height: 1.08;
        color: #1f3342;
    }

    .gamma-form-subtitle {
        margin: 0;
        max-width: 760px;
        color: #55707f;
        font-size: 15px;
        line-height: 1.55;
    }

    .gamma-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 18px;
        border-radius: 999px;
        background: linear-gradient(135deg, #1f6f8b 0%, #2a87a9 100%);
        color: #fff;
        box-shadow: 0 14px 28px rgba(31, 111, 139, 0.18);
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .create-user-page .gamma-panel {
        border: 1px solid #d9e3e8;
        border-radius: 14px;
        box-shadow: 0 12px 34px rgba(36, 56, 70, 0.07);
        overflow: hidden;
        margin-bottom: 22px;
    }

    .create-user-page .gamma-panel-soft {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdfe 100%);
    }

    .create-user-page .gamma-panel-accent {
        border-color: #2b86a8;
        box-shadow: 0 16px 36px rgba(43, 134, 168, 0.16);
    }

    .create-user-page .panel-heading {
        padding: 15px 18px;
        font-size: 14px;
        letter-spacing: 0.2px;
    }

    .create-user-page .panel-heading i {
        margin-right: 8px;
        opacity: 0.85;
    }

    .create-user-page .gamma-panel-soft > .panel-heading {
        background: linear-gradient(180deg, #f6fafc 0%, #edf4f7 100%);
        border-bottom: 1px solid #dce8ee;
        color: #274252;
    }

    .create-user-page .gamma-panel-accent > .panel-heading {
        background: linear-gradient(135deg, #1f6f8b 0%, #2c86a7 100%);
        border-bottom: none;
        color: #fff;
    }

    .create-user-page .panel-body {
        padding: 20px;
    }

    .create-user-page .form-group {
        margin-bottom: 16px;
    }

    .create-user-page label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .create-user-page .help-block {
        margin-bottom: 0;
    }

    .create-user-page .form-control {
        height: 40px;
        border-radius: 9px;
        border-color: #ccd7de;
        background: #fff;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .create-user-page input.form-control:focus,
    .create-user-page select.form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.12);
    }

    .create-user-page .well {
        border-radius: 10px;
        background: #f8fbfd;
        border: 1px solid #dbe5eb;
    }

    .create-user-page .gamma-check-well {
        padding: 14px 16px;
    }

    .create-user-page .btn-lg {
        height: 48px;
        border-radius: 10px;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .create-user-page .gamma-submit-btn {
        background: linear-gradient(135deg, #1f6f8b 0%, #2a89ac 100%);
        border-color: #1f6f8b;
        box-shadow: 0 14px 30px rgba(31, 111, 139, 0.22);
    }

    .create-user-page .gamma-submit-btn:hover,
    .create-user-page .gamma-submit-btn:focus {
        background: linear-gradient(135deg, #195d74 0%, #237693 100%);
        border-color: #195d74;
    }

    .create-user-page .gamma-panel-copy {
        margin-bottom: 20px;
        line-height: 1.55;
    }

    @media (max-width: 991px) {
        .gamma-form-hero {
            flex-direction: column;
        }

        .gamma-form-badge {
            align-self: flex-start;
        }
    }
</style>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.no').slideUp();
        $('#group').change(function () {
            var group = $(this).val();
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
    });
</script>
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
