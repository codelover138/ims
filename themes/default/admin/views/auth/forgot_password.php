<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('forgot_password') ?> - <?= SITE_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url() ?>assets/styles/helpers/bootstrap.o.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/styles/style.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="<?= base_url() ?>assets/js/respond.min.js"></script>
    <![endif]-->
    <style>
        body { min-height: 100vh; background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.18), transparent 34%), linear-gradient(135deg, #1f6aa5 0%, #2877b8 45%, #4f95cf 100%); padding: 34px 16px 48px; }
        .forgot-shell { max-width: 560px; margin: 0 auto; }
        .forgot-logo { text-align: center; margin-bottom: 18px; }
        .forgot-logo img { max-width: 340px; width: auto; height: auto; border-radius: 16px; box-shadow: 0 16px 34px rgba(10, 37, 64, 0.22); background: #fff; padding: 8px; }
        .forgot-card { background: #f7fbff; border-radius: 24px; box-shadow: 0 26px 54px rgba(10, 37, 64, 0.24); overflow: hidden; }
        .forgot-hero { background: linear-gradient(135deg, #0f3d66 0%, #1f6aa5 100%); color: #fff; padding: 28px 32px 22px; }
        .forgot-kicker { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255, 255, 255, 0.72); margin-bottom: 10px; }
        .forgot-hero h1 { margin: 0 0 8px; font-size: 30px; font-weight: 700; }
        .forgot-hero p { margin: 0; color: rgba(255, 255, 255, 0.82); font-size: 15px; line-height: 1.55; }
        .forgot-body { padding: 28px 32px 32px; }
        .forgot-alert { border: 0; border-radius: 16px; padding: 16px 18px; margin-bottom: 22px; box-shadow: none; }
        .forgot-field { margin-bottom: 16px; }
        .forgot-field label { display: block; margin-bottom: 8px; color: #2a3f57; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .forgot-field .input-group { width: 100%; border: 1px solid #d7e6f4; border-radius: 16px; overflow: hidden; background: #fff; box-shadow: 0 10px 24px rgba(18, 44, 74, 0.06); }
        .forgot-field .input-group-addon { min-width: 52px; background: #f3f8fd; color: #1f6aa5; border: 0; }
        .forgot-field .form-control { border: 0; height: 52px; box-shadow: none; font-size: 16px; padding: 12px 16px; }
        .forgot-captcha-preview { border: 1px solid #d7e6f4; border-radius: 16px; min-height: 54px; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 24px rgba(18, 44, 74, 0.06); margin-bottom: 14px; position: relative; padding: 10px; }
        .forgot-captcha-preview .reload-captcha { position: absolute; top: 10px; right: 12px; color: #1f6aa5; }
        .forgot-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .forgot-actions .btn { border-radius: 14px; padding: 12px 20px; font-size: 15px; font-weight: 700; border: 0; box-shadow: none; }
        .btn-auth-primary { background: linear-gradient(135deg, #1f6aa5 0%, #2d8bd3 100%); color: #fff; }
        .btn-auth-secondary { background: #eef5fb; color: #1f6aa5; }
        .forgot-extra { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; padding-top: 18px; border-top: 1px solid #deebf6; }
        .forgot-extra a { font-weight: 700; color: #1f6aa5; }
        @media (max-width: 640px) { .forgot-hero, .forgot-body { padding-left: 20px; padding-right: 20px; } .forgot-actions, .forgot-extra { flex-direction: column; } .forgot-actions .btn { width: 100%; } }
    </style>
</head>
<body>
<div class="forgot-shell">
    <div class="forgot-logo">
        <?php if (LOGO) { echo '<img src="' . admin_url() . 'assets/images/' . LOGO . '" alt="' . SITE_NAME . '" />'; } ?>
    </div>
    <div class="forgot-card">
        <div class="forgot-hero">
            <span class="forgot-kicker">Account Recovery</span>
            <h1><?= $this->lang->line('forgot_password_heading') ?></h1>
            <p><?= sprintf(lang('forgot_password_subheading'), $identity_label); ?></p>
        </div>
        <div class="forgot-body">
            <?php if ($message) { ?><div class="alert alert-danger forgot-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><?= $message; ?></div><?php } ?>
            <?= admin_form_open("auth/forgot_password", 'class="login"'); ?>
            <div class="forgot-field">
                <label for="<?= $email['id']; ?>"><?= lang('email_address') ?></label>
                <div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope"></i></span><?= form_input($email); ?></div>
            </div>
            <div class="forgot-captcha-preview"><a href="#" class="reload-captcha"><i class="fa fa-refresh"></i></a><span class="captcha-image"><?= $image; ?></span></div>
            <div class="forgot-field">
                <label for="<?= $captcha['id']; ?>">Verification</label>
                <div class="input-group"><span class="input-group-addon"><i class="fa fa-shield"></i></span><?= form_input($captcha); ?></div>
            </div>
            <div class="forgot-actions">
                <a href="<?= admin_url('auth/login') ?>" class="btn btn-auth-secondary"><?= lang('back_to_login'); ?></a>
                <?= form_submit('submit', lang('submit'), 'class="btn btn-auth-primary"'); ?>
            </div>
            <?= form_close(); ?>
            <div class="forgot-extra">
                <a href="<?= admin_url('auth/login') ?>">Return to sign in</a>
                <a href="<?= admin_url('login#forgot_username') ?>">Forgot username</a>
                <a href="register"><?= lang('register') ?></a>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url() ?>assets/js/jquery-2.0.3.min.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('.reload-captcha').click(function (event) {
            event.preventDefault();
            $.ajax({
                url: '<?= base_url(); ?>auth/reload_captcha',
                success: function (data) {
                    $('.captcha-image').html(data);
                }
            });
        });
    });
</script>
</body>
</html>
