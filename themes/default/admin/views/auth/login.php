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
        .page-back {
            min-height: 100vh;
            padding: 34px 16px 48px;
            background:
                radial-gradient(circle at top left, rgba(60, 141, 188, 0.16), transparent 26%),
                radial-gradient(circle at 80% 18%, rgba(255, 255, 255, 0.12), transparent 18%),
                linear-gradient(135deg, #1f2d3a 0%, #2e3f50 36%, #3c8dbc 100%);
        }
        .auth-shell { max-width: 1120px; margin: 0 auto; }
        .auth-logo { display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
        .auth-logo img { max-width: 280px; width: auto; height: auto; border-radius: 18px; box-shadow: 0 20px 40px rgba(16, 29, 40, 0.24); background: #fff; padding: 8px; }
        .auth-stage {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            background: rgba(250, 252, 253, 0.98);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 38px 72px rgba(15, 27, 38, 0.28);
            border: 1px solid rgba(255,255,255,0.18);
        }
        .auth-showcase {
            position: relative;
            padding: 44px 42px 40px;
            background:
                radial-gradient(circle at 18% 18%, rgba(255,255,255,0.12), transparent 20%),
                linear-gradient(160deg, rgba(31, 45, 58, 0.96) 0%, rgba(46, 63, 80, 0.94) 54%, rgba(60, 141, 188, 0.92) 100%);
            color: #fff;
        }
        .auth-showcase:before {
            content: "";
            position: absolute;
            inset: auto -64px -72px auto;
            width: 230px;
            height: 230px;
            background: rgba(255,255,255,0.08);
            border-radius: 42px;
            transform: rotate(18deg);
        }
        .auth-showcase:after {
            content: "";
            position: absolute;
            inset: 28px auto auto 28px;
            width: 74px;
            height: 74px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 22px;
        }
        .auth-kicker { display: inline-block; font-size: 11px; font-weight: 800; letter-spacing: 0.22em; text-transform: uppercase; color: rgba(255,255,255,0.68); margin-bottom: 16px; }
        .auth-showcase h1 { margin: 0 0 14px; font-size: 38px; line-height: 1.04; font-weight: 800; }
        .auth-showcase p { margin: 0; max-width: 380px; font-size: 15px; line-height: 1.75; color: rgba(255,255,255,0.82); }
        .auth-footnote { margin-top: 22px; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.58); }
        .auth-feature-list { display: grid; gap: 12px; margin-top: 28px; position: relative; z-index: 1; }
        .auth-feature {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(3px);
        }
        .auth-feature i {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
        }
        .auth-feature strong {
            display: block;
            margin-bottom: 3px;
            font-size: 14px;
            color: #fff;
        }
        .auth-feature span {
            display: block;
            color: rgba(255,255,255,0.72);
            font-size: 13px;
            line-height: 1.55;
        }
        .auth-panel { padding: 36px 36px 30px; background: linear-gradient(180deg, #fbfdfe 0%, #f2f7fb 100%); }
        .auth-panel-head { margin-bottom: 22px; }
        .auth-panel-head h2 { margin: 0 0 8px; color: #1f2d3a; font-size: 30px; font-weight: 800; }
        .auth-panel-head p { margin: 0; color: #66798b; font-size: 15px; line-height: 1.6; }
        .auth-alert { border: 0; border-radius: 16px; padding: 16px 18px; margin-bottom: 20px; box-shadow: none; }
        .auth-alert .close { opacity: .55; }
        .auth-alert ul { margin: 0; padding-left: 18px; }
        .auth-fields { display: grid; gap: 16px; }
        .auth-field label, .auth-captcha label { display: block; margin-bottom: 8px; color: #3b4f61; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .auth-field .input-group, .auth-captcha .input-group {
            width: 100%;
            border: 1px solid #d8e3ec;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 14px 28px rgba(31, 45, 58, 0.05);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        .auth-field .input-group:focus-within, .auth-captcha .input-group:focus-within {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 4px rgba(60, 141, 188, 0.12);
            transform: translateY(-1px);
        }
        .auth-field .input-group-addon, .auth-captcha .input-group-addon { min-width: 54px; background: #eef4f8; color: #3c8dbc; border: 0; }
        .auth-field .form-control, .auth-captcha .form-control { border: 0; height: 54px; box-shadow: none; font-size: 16px; padding: 12px 16px; color: #1f2d3a; }
        .auth-field .form-control::placeholder, .auth-captcha .form-control::placeholder { color: #8aa0b5; }
        .auth-captcha-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 6px; }
        .auth-captcha-preview { border: 1px solid #d8e3ec; border-radius: 16px; min-height: 54px; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 14px 28px rgba(31, 45, 58, 0.05); }
        .auth-captcha .input-group-addon a { color: #3c8dbc; }
        .auth-row { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-top: 26px; }
        .auth-remember { display: flex; align-items: center; gap: 10px; color: #5a6f82; }
        .auth-remember label { margin: 0; font-weight: 700; }
        .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { border-radius: 14px; padding: 13px 22px; font-size: 15px; font-weight: 800; border: 0; box-shadow: none; }
        .btn-auth-primary { background: linear-gradient(135deg, #3c8dbc 0%, #5fa7d3 100%); color: #fff; box-shadow: 0 16px 28px rgba(60, 141, 188, 0.22); }
        .btn-auth-primary:hover, .btn-auth-primary:focus { color: #fff; background: linear-gradient(135deg, #337aa8 0%, #5298c2 100%); }
        .btn-auth-secondary { background: #eaf2f8; color: #3c8dbc; }
        .btn-auth-secondary:hover, .btn-auth-secondary:focus { background: #ddeaf4; color: #337aa8; }
        .auth-links { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 22px; padding-top: 18px; border-top: 1px solid #dbe6ee; }
        .auth-links a { font-weight: 800; color: #3c8dbc; }
        .auth-links span { color: #698198; font-size: 14px; }
        .auth-note { margin: 0 0 18px; color: #5b738b; font-size: 15px; line-height: 1.65; }
        .auth-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 24px; }
        .auth-register { margin-top: 22px; padding-top: 18px; border-top: 1px solid #dbe6ee; color: #5b738b; font-size: 14px; }
        .auth-register strong { display: block; margin-bottom: 6px; color: #1f3c58; font-size: 15px; }
        .auth-register a { font-weight: 800; color: #3c8dbc; }
        .registration-form-div.reg-content { background: #f7fbfe; border-radius: 28px; box-shadow: 0 34px 70px rgba(15, 27, 38, 0.24); padding: 28px 24px 24px; border: 1px solid rgba(255,255,255,0.16); }
        .auth-register-actions { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; }
        .auth-powered { margin-top: 16px; color: #86a0b5; font-size: 12px; text-align: center; letter-spacing: 0.04em; }
        @media (max-width: 920px) { .auth-stage { grid-template-columns: 1fr; } .auth-showcase { padding-bottom: 30px; } .auth-showcase h1 { font-size: 32px; } }
        @media (max-width: 640px) { .auth-panel, .auth-showcase { padding-left: 22px; padding-right: 22px; } .auth-showcase h1 { font-size: 28px; } .auth-captcha-row, .auth-row, .auth-links, .auth-actions, .auth-register-actions { flex-direction: column; align-items: stretch; grid-template-columns: 1fr; } .auth-row .btn, .auth-actions .btn, .auth-register-actions .btn { width: 100%; } .auth-links span { text-align: left; } .auth-feature-list { gap: 10px; } }
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
                    <span class="auth-kicker"><?= html_escape($Settings->site_name); ?></span>
                    <h1>Secure staff access.</h1>
                    <p>Sign in to continue to the <?= html_escape($Settings->site_name); ?> admin workspace.</p>
                    <div class="auth-feature-list">
                        <div class="auth-feature">
                            <i class="fa fa-shield"></i>
                            <div>
                                <strong>Protected access</strong>
                                <span>Administrative entry point for authorized staff and owners.</span>
                            </div>
                        </div>
                        <div class="auth-feature">
                            <i class="fa fa-line-chart"></i>
                            <div>
                                <strong>Operational workspace</strong>
                                <span>Inventory, sales, reporting, and system tools in one place.</span>
                            </div>
                        </div>
                        <div class="auth-feature">
                            <i class="fa fa-clock-o"></i>
                            <div>
                                <strong>Fast sign-in flow</strong>
                                <span>Clean access page designed for daily internal use.</span>
                            </div>
                        </div>
                    </div>
                    <div class="auth-footnote">Secure administrative access for <?= html_escape($Settings->site_name); ?></div>
                </div>
                <div class="auth-panel">
                    <div class="auth-panel-head">
                        <h2>Sign in</h2>
                        <p>Use your staff account to continue to the <?= html_escape($Settings->site_name); ?> admin workspace.</p>
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
                    <div class="auth-feature-list">
                        <div class="auth-feature">
                            <i class="fa fa-envelope-o"></i>
                            <div>
                                <strong>Email-based recovery</strong>
                                <span>Password reset links are sent only to registered staff accounts.</span>
                            </div>
                        </div>
                    </div>
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
