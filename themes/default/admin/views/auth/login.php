<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?= admin_url() ?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->
    <style>
        .page-back { min-height: 100vh; background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.18), transparent 34%), linear-gradient(135deg, #1f6aa5 0%, #2877b8 45%, #4f95cf 100%); padding: 34px 16px 48px; }
        .auth-shell { max-width: 580px; margin: 0 auto; }
        .auth-logo { text-align: center; margin-bottom: 18px; }
        .auth-logo img { max-width: 340px; width: auto; height: auto; border-radius: 16px; box-shadow: 0 16px 34px rgba(10, 37, 64, 0.22); background: #fff; padding: 8px; }
        .auth-panel { background: #f7fbff; border-radius: 24px; box-shadow: 0 26px 54px rgba(10, 37, 64, 0.24); overflow: hidden; }
        .auth-hero { background: linear-gradient(135deg, #0f3d66 0%, #1f6aa5 100%); color: #fff; padding: 28px 32px 22px; }
        .auth-kicker { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255, 255, 255, 0.72); margin-bottom: 10px; }
        .auth-hero h1 { margin: 0 0 8px; font-size: 30px; font-weight: 700; line-height: 1.15; }
        .auth-hero p { margin: 0; max-width: 440px; color: rgba(255, 255, 255, 0.82); font-size: 15px; }
        .auth-body { padding: 28px 32px 32px; }
        .auth-alert { border: 0; border-radius: 16px; padding: 16px 18px; margin-bottom: 22px; box-shadow: none; }
        .auth-alert .close { opacity: .55; }
        .auth-alert ul { margin: 0; padding-left: 18px; }
        .auth-fields { display: grid; gap: 16px; }
        .auth-field label, .auth-captcha label { display: block; margin-bottom: 8px; color: #2a3f57; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .auth-field .input-group, .auth-captcha .input-group { width: 100%; border: 1px solid #d7e6f4; border-radius: 16px; overflow: hidden; background: #fff; box-shadow: 0 10px 24px rgba(18, 44, 74, 0.06); }
        .auth-field .input-group-addon, .auth-captcha .input-group-addon { min-width: 52px; background: #f3f8fd; color: #1f6aa5; border: 0; }
        .auth-field .form-control, .auth-captcha .form-control { border: 0; height: 52px; box-shadow: none; font-size: 16px; padding: 12px 16px; }
        .auth-captcha-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 6px; }
        .auth-captcha-preview { border: 1px solid #d7e6f4; border-radius: 16px; min-height: 54px; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 24px rgba(18, 44, 74, 0.06); }
        .auth-captcha .input-group-addon a { color: #1f6aa5; }
        .auth-row { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-top: 24px; }
        .auth-remember { display: flex; align-items: center; gap: 10px; color: #3c556e; }
        .auth-remember label { margin: 0; font-weight: 600; }
        .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { border-radius: 14px; padding: 12px 20px; font-size: 15px; font-weight: 700; border: 0; box-shadow: none; }
        .btn-auth-primary { background: linear-gradient(135deg, #1f6aa5 0%, #2d8bd3 100%); color: #fff; }
        .btn-auth-secondary { background: #eef5fb; color: #1f6aa5; }
        .auth-links { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 20px; padding-top: 18px; border-top: 1px solid #deebf6; }
        .auth-links a { font-weight: 700; color: #1f6aa5; }
        .auth-links span { color: #5b6f82; font-size: 14px; }
        .auth-note { margin: 0 0 18px; color: #5b6f82; font-size: 15px; line-height: 1.55; }
        .auth-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .auth-register { margin-top: 22px; padding-top: 18px; border-top: 1px solid #deebf6; color: #5b6f82; font-size: 14px; }
        .auth-register strong { display: block; margin-bottom: 6px; color: #23425f; font-size: 15px; }
        .auth-register a { font-weight: 700; color: #1f6aa5; }
        .registration-form-div.reg-content { background: #f7fbff; border-radius: 24px; box-shadow: 0 26px 54px rgba(10, 37, 64, 0.24); padding: 28px 24px 24px; }
        .auth-register-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; }
        @media (max-width: 640px) { .auth-hero, .auth-body { padding-left: 20px; padding-right: 20px; } .auth-hero h1 { font-size: 26px; } .auth-captcha-row, .auth-row, .auth-links, .auth-actions, .auth-register-actions { flex-direction: column; align-items: stretch; grid-template-columns: 1fr; } .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { width: 100%; } }
    </style>
</head>
<body class="login-page">
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div class="page-back">
    <div class="auth-shell">
        <div class="auth-logo">
            <?php if ($Settings->logo2) { echo '<img src="' . base_url('assets/uploads/logos/' . $Settings->logo2) . '" alt="' . $Settings->site_name . '" />'; } ?>
        </div>
        <div id="login">
            <div class="auth-panel">
                <div class="auth-hero">
                    <span class="auth-kicker">Gamma Workspace</span>
                    <h1>Welcome Back</h1>
                    <p>Sign in to manage clients, forms, and document workflows from one secure workspace.</p>
                </div>
                <div class="auth-body">
                    <?php if ($Settings->mmode) { ?><div class="alert alert-warning auth-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><?= lang('site_offline') ?></div><?php } ?>
                    <?php if ($error) { ?><div class="alert alert-danger auth-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><ul class="list-group"><?= $error; ?></ul></div><?php } ?>
                    <?php if ($message) { ?><div class="alert alert-success auth-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><ul class="list-group"><?= $message; ?></ul></div><?php } ?>
                    <?= admin_form_open("auth/login", 'class="login" data-toggle="validator"'); ?>
                    <div class="auth-fields">
                        <div class="auth-field form-group">
                            <label for="identity">Username</label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span><input type="text" id="identity" value="<?= DEMO ? 'codelover138@gmail.com' : ''; ?>" required="required" class="form-control" name="identity" placeholder="<?= lang('username') ?>"/></div>
                        </div>
                        <div class="auth-field form-group">
                            <label for="password">Password</label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-key"></i></span><input type="password" id="password" value="<?= DEMO ? '12345678' : ''; ?>" required="required" class="form-control" name="password" placeholder="<?= lang('pw') ?>"/></div>
                        </div>
                    </div>
                    <?php if ($Settings->captcha) { ?>
                        <div class="auth-captcha form-group">
                            <label>Verification</label>
                            <div class="auth-captcha-row">
                                <div class="auth-captcha-preview"><span class="captcha-image"><?= $image; ?></span></div>
                                <div class="input-group"><span class="input-group-addon"><a href="<?= admin_url('auth/reload_captcha'); ?>" class="reload-captcha"><i class="fa fa-refresh"></i></a></span><?= form_input($captcha); ?></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="auth-row">
                        <div class="auth-remember"><div class="custom-checkbox"><?= form_checkbox('remember', '1', FALSE, 'id="remember"'); ?></div><label for="remember"><?= lang('remember_me') ?></label></div>
                        <button type="submit" class="btn btn-auth-primary"><?= lang('login') ?> <i class="fa fa-sign-in"></i></button>
                    </div>
                    <div class="auth-links">
                        <a href="#forgot_password" class="forgot_password_link"><i class="fa fa-life-ring"></i> <?= lang('forgot_password') ?></a>
                        <span>Use your account email to request a secure reset link.</span>
                    </div>
                    <?= form_close(); ?>
                    <?php if ($Settings->allow_reg) { ?>
                        <div class="auth-register"><strong><?= lang('dont_have_account') ?></strong><span><?= lang('no_worry') ?></span> <a href="#register" class="register_link"><?= lang('click_here') ?></a> <span><?= lang('to_register') ?></span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div id="forgot_password" style="display: none;">
            <div class="auth-panel">
                <div class="auth-hero">
                    <span class="auth-kicker">Account Recovery</span>
                    <h1>Forgot Your Password?</h1>
                    <p>Enter the email address linked to your account and we will send you a secure reset link.</p>
                </div>
                <div class="auth-body">
                    <?php if ($error) { ?><div class="alert alert-danger auth-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><ul class="list-group"><?= $error; ?></ul></div><?php } ?>
                    <?php if ($message) { ?><div class="alert alert-success auth-alert"><button data-dismiss="alert" class="close" type="button">&times;</button><ul class="list-group"><?= $message; ?></ul></div><?php } ?>
                    <?= admin_form_open("auth/forgot_password", 'class="login" data-toggle="validator"'); ?>
                    <p class="auth-note"><?= lang('type_email_to_reset'); ?></p>
                    <div class="auth-fields">
                        <div class="auth-field form-group">
                            <label for="forgot_email">Email Address</label>
                            <div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope"></i></span><input type="email" id="forgot_email" name="forgot_email" class="form-control" placeholder="<?= lang('email_address') ?>" required="required"/></div>
                        </div>
                    </div>
                    <div class="auth-actions">
                        <a class="btn btn-auth-secondary login_link" href="#login"><i class="fa fa-chevron-left"></i> <?= lang('back') ?></a>
                        <button type="submit" class="btn btn-auth-primary"><?= lang('submit') ?> <i class="fa fa-envelope"></i></button>
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
        <?php if ($Settings->allow_reg) { ?>
            <div id="register">
                <div class="container">
                    <div class="registration-form-div reg-content">
                        <?php echo admin_form_open("auth/register", 'class="login" data-toggle="validator"'); ?>
                        <div class="div-title col-sm-12">
                            <h3 class="text-primary"><?= lang('register_account_heading') ?></h3>
                        </div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('first_name', 'first_name'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-user"></i></span><input type="text" name="first_name" class="form-control " placeholder="<?= lang('first_name') ?>" required="required"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('last_name', 'last_name'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-user"></i></span><input type="text" name="last_name" class="form-control " placeholder="<?= lang('last_name') ?>" required="required"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('company', 'company'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-building"></i></span><input type="text" name="company" class="form-control " placeholder="<?= lang('company') ?>"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('phone', 'phone'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-phone-square"></i></span><input type="text" name="phone" class="form-control " placeholder="<?= lang('phone') ?>" required="required"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('username', 'username'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-user"></i></span><input type="text" name="username" class="form-control " placeholder="<?= lang('username') ?>" required="required"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?= lang('email', 'email'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-envelope"></i></span><input type="email" name="email" class="form-control " placeholder="<?= lang('email_address') ?>" required="required"/></div></div></div>
                        <div class="col-sm-6"><div class="form-group"><?php echo lang('password', 'password1'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-key"></i></span><?php echo form_password('password', '', 'class="form-control tip" id="password1" required="required" pattern="(?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?></div><span class="help-block"><?= lang('pasword_hint') ?></span></div></div>
                        <div class="col-sm-6"><div class="form-group"><?php echo lang('confirm_password', 'confirm_password'); ?><div class="input-group"><span class="input-group-addon "><i class="fa fa-key"></i></span><?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?></div></div></div>
                        <div class="col-sm-12 auth-register-actions"><a href="#login" class="btn btn-auth-secondary login_link"><i class="fa fa-chevron-left"></i> <?= lang('back') ?></a><button type="submit" class="btn btn-auth-primary"><?= lang('register_now') ?> <i class="fa fa-user"></i></button></div>
                        <?php echo form_close(); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (DEMO) { ?>
            <div style="color:#fff;padding-top:30px;text-align:center;">
                <h4 style="color:#f4f5f6;font-size:18px;line-height:24px;">Check out our latest item<br>Simple Business Manager &nbsp; [ <a href="https://tecdiary.com/products/simple-business-manager" target="_top" style="color:#fff;">Details</a> ] &nbsp; [ <a href="https://sbm.tecdiary.com/" target="_top" style="color:#fff;">Demo</a> ]</h4>
                <a href="https://sbm.tecdiary.com/" target="_top"><img src="https://tecdiary.net/images/hotlink-ok/sbm.png" alt="Simple Business Manager" style="border-radius:10px !important;max-width:100%;"></a>
            </div>
        <?php } ?>
    </div>
</div>
<script src="<?= $assets ?>js/jquery.js"></script>
<script src="<?= $assets ?>js/bootstrap.min.js"></script>
<script src="<?= $assets ?>js/jquery.cookie.js"></script>
<script src="<?= $assets ?>js/login.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        localStorage.clear();
        var hash = window.location.hash;
        if (hash && hash != '') {
            $("#login").hide();
            $(hash).show();
        }
    });
</script>
</body>
</html>
