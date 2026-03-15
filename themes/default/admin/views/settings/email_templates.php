<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-envelope"></i><?= lang('email_templates'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <!--<p class="introtext"><?= lang('list_results'); ?></p>-->
                <div class="row">
                    <div class="col-md-8 col-sm-8">
                        <ul id="myTab" class="nav nav-tabs">
                            <li class=""><a href="#credentials"><?= lang('new_user') ?></a></li>
                            <li class=""><a href="#activate_email"><?= lang('activate_email') ?></a></li>
                            <li class=""><a href="#forgot_password"><?= lang('forgot_password') ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="credentials" class="tab-pane fade in">
                                <?= admin_form_open('system_settings/email_templates'); ?>

                                <?php echo form_textarea('mail_body', (isset($_POST['mail_body']) ? html_entity_decode($_POST['mail_body']) : html_entity_decode($credentials)), 'class="form-control" id="comment"'); ?>

                                <input type="submit" name="submit" class="btn btn-primary" value="<?= lang('save'); ?>"
                                       style="margin-top:15px;"/>

                                <?php echo form_close(); ?>
                            </div>

                            <div id="activate_email" class="tab-pane fade">
                                <?= admin_form_open('system_settings/email_templates/activate_email'); ?>

                                <?php echo form_textarea('mail_body', (isset($_POST['mail_body']) ? html_entity_decode($_POST['mail_body']) : html_entity_decode($activate_email)), 'class="form-control" id="comment"'); ?>

                                <input type="submit" name="submit" class="btn btn-primary" value="<?= lang('save'); ?>"
                                       style="margin-top:15px;"/>

                                <?php echo form_close(); ?>
                            </div>

                            <div id="forgot_password" class="tab-pane fade">
                                <?= admin_form_open('system_settings/email_templates/forgot_password'); ?>

                                <?php echo form_textarea('mail_body', (isset($_POST['mail_body']) ? html_entity_decode($_POST['mail_body']) : html_entity_decode($forgot_password)), 'class="form-control" id="comment"'); ?>

                                <input type="submit" name="submit" class="btn btn-primary" value="<?= lang('save'); ?>"
                                       style="margin-top:15px;"/>

                                <?php echo form_close(); ?>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <div class="margin5">
                            <h3 style="font-weight: bold;"><?= $this->lang->line('short_tags'); ?></h3>
                            <pre>{logo} {site_name} {site_link}</pre>
                            <?= lang('new_user') ?>
                            <pre>{client_name} {email} {password} </pre>
                            <?= lang('forgot_password') ?>
                            <pre>{user_name} {email} {reset_password_link}</pre>
                            <?= lang('activate_email') ?>
                            <pre>{user_name} {email} {activation_link}</pre>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>
