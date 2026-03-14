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
        .page-back { min-height: 100vh; background:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.14), transparent 30%),
            radial-gradient(circle at 85% 15%, rgba(255,214,153,0.18), transparent 22%),
            linear-gradient(135deg, #0f2740 0%, #143b5c 42%, #1d5f88 100%); padding: 38px 16px 54px; }
        .auth-shell { max-width: 1080px; margin: 0 auto; }
        .auth-logo { display: flex; align-items: center; justify-content: center; margin-bottom: 22px; }
        .auth-logo img { max-width: 300px; width: auto; height: auto; border-radius: 18px; box-shadow: 0 18px 38px rgba(8, 24, 41, 0.32); background: #fff; padding: 8px; }
        .auth-stage { display: grid; grid-template-columns: 0.9fr 1.1fr; background: rgba(244, 249, 253, 0.98); border-radius: 28px; overflow: hidden; box-shadow: 0 34px 70px rgba(8, 24, 41, 0.34); border: 1px solid rgba(255,255,255,0.18); }
        .auth-showcase { position: relative; padding: 42px 40px; background:
            linear-gradient(160deg, rgba(7, 27, 46, 0.92) 0%, rgba(16, 56, 84, 0.9) 58%, rgba(24, 92, 132, 0.84) 100%),
            linear-gradient(135deg, #102b44 0%, #20597d 100%); color: #fff; }
        .auth-showcase:after { content: ""; position: absolute; right: -70px; bottom: -70px; width: 220px; height: 220px; background: rgba(255,255,255,0.08); border-radius: 36px; transform: rotate(24deg); }
        .auth-kicker { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; color: rgba(255,255,255,0.7); margin-bottom: 14px; }
        .auth-showcase h1 { margin: 0 0 14px; font-size: 36px; line-height: 1.08; font-weight: 800; }
        .auth-showcase p { margin: 0; max-width: 360px; font-size: 15px; line-height: 1.7; color: rgba(255,255,255,0.82); }
        .auth-footnote { margin-top: 22px; font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase; color: rgba(255,255,255,0.58); }
        .auth-panel { padding: 34px 34px 30px; background: #f7fbfe; }
        .auth-panel-head { margin-bottom: 22px; }
        .auth-panel-head h2 { margin: 0 0 8px; color: #173550; font-size: 30px; font-weight: 800; }
        .auth-panel-head p { margin: 0; color: #58718a; font-size: 15px; line-height: 1.6; }
        .auth-alert { border: 0; border-radius: 16px; padding: 16px 18px; margin-bottom: 20px; box-shadow: none; }
        .auth-alert .close { opacity: .55; }
        .auth-alert ul { margin: 0; padding-left: 18px; }
        .auth-fields { display: grid; gap: 16px; }
        .auth-field label, .auth-captcha label { display: block; margin-bottom: 8px; color: #264763; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .auth-field .input-group, .auth-captcha .input-group { width: 100%; border: 1px solid #d6e4f0; border-radius: 16px; overflow: hidden; background: #fff; box-shadow: 0 12px 28px rgba(19, 52, 80, 0.06); }
        .auth-field .input-group-addon, .auth-captcha .input-group-addon { min-width: 54px; background: #edf5fb; color: #1e5e8a; border: 0; }
        .auth-field .form-control, .auth-captcha .form-control { border: 0; height: 54px; box-shadow: none; font-size: 16px; padding: 12px 16px; color: #173550; }
        .auth-field .form-control::placeholder, .auth-captcha .form-control::placeholder { color: #8aa0b5; }
        .auth-captcha-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 6px; }
        .auth-captcha-preview { border: 1px solid #d6e4f0; border-radius: 16px; min-height: 54px; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 12px 28px rgba(19, 52, 80, 0.06); }
        .auth-captcha .input-group-addon a { color: #1e5e8a; }
        .auth-row { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-top: 26px; }
        .auth-remember { display: flex; align-items: center; gap: 10px; color: #49637c; }
        .auth-remember label { margin: 0; font-weight: 700; }
        .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { border-radius: 14px; padding: 13px 22px; font-size: 15px; font-weight: 800; border: 0; box-shadow: none; }
        .btn-auth-primary { background: linear-gradient(135deg, #15466c 0%, #2572a5 100%); color: #fff; }
        .btn-auth-secondary { background: #e9f2f9; color: #1b5c88; }
        .auth-links { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 22px; padding-top: 18px; border-top: 1px solid #dbe8f2; }
        .auth-links a { font-weight: 800; color: #1b5c88; }
        .auth-links span { color: #698198; font-size: 14px; }
        .auth-note { margin: 0 0 18px; color: #5b738b; font-size: 15px; line-height: 1.65; }
        .auth-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .auth-register { margin-top: 22px; padding-top: 18px; border-top: 1px solid #dbe8f2; color: #5b738b; font-size: 14px; }
        .auth-register strong { display: block; margin-bottom: 6px; color: #1f3c58; font-size: 15px; }
        .auth-register a { font-weight: 800; color: #1b5c88; }
        .registration-form-div.reg-content { background: #f7fbfe; border-radius: 28px; box-shadow: 0 34px 70px rgba(8, 24, 41, 0.34); padding: 28px 24px 24px; border: 1px solid rgba(255,255,255,0.16); }
        .auth-register-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; }
        .auth-powered { margin-top: 16px; color: #86a0b5; font-size: 12px; text-align: center; letter-spacing: 0.04em; }
        @media (max-width: 920px) { .auth-stage { grid-template-columns: 1fr; } .auth-showcase { padding-bottom: 30px; } .auth-showcase h1 { font-size: 32px; } }
        @media (max-width: 640px) { .auth-panel, .auth-showcase { padding-left: 22px; padding-right: 22px; } .auth-showcase h1 { font-size: 28px; } .auth-captcha-row, .auth-row, .auth-links, .auth-actions, .auth-register-actions { flex-direction: column; align-items: stretch; grid-template-columns: 1fr; } .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { width: 100%; } .auth-links span { text-align: left; } }
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
            <div class="auth-stage">
                <div class="auth-showcase">
                    <span class="auth-kicker">Gamma Workspace</span>
                    <h1>Secure staff access.</h1>
                    <p>Sign in to continue to the Gamma admin workspace.</p>
                    <div class="auth-footnote">Secure administrative access for <?= html_escape($Settings->site_name); ?></div>
                </div>
                <div class="auth-panel">
                    <div class="auth-panel-head">
                        <h2>Sign in</h2>
                        <p>Use your staff account to continue to the Gamma admin workspace.</p>
                    </div>
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
                    <div class="auth-powered">Designed for a professional internal workspace</div>
                </div>
            </div>
        </div>
        <div id="forgot_password" style="display: none;">
            <div class="auth-stage">
                <div class="auth-showcase">
                    <span class="auth-kicker">Account Recovery</span>
                    <h1>Reset your password.</h1>
                    <p>Enter your account email and we will send you a reset link.</p>
                </div>
                <div class="auth-panel">
                    <div class="auth-panel-head">
                        <h2>Reset password</h2>
                        <p>Recover your account using the email assigned to your profile.</p>
                    </div>
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
                    <div class="auth-powered">Password recovery is limited to authorized staff accounts</div>
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
