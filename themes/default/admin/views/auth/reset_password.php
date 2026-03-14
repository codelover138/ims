<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?= admin_url() ?>'; }</script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet">
    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet">
    <style>
        .page-back {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.18), transparent 34%),
                linear-gradient(135deg, #1f6aa5 0%, #2877b8 45%, #4f95cf 100%);
            padding: 34px 16px 48px;
        }

        .reset-shell {
            max-width: 560px;
            margin: 0 auto;
        }

        .reset-logo {
            text-align: center;
            margin-bottom: 18px;
        }

        .reset-logo img {
            max-width: 340px;
            width: auto;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 16px 34px rgba(10, 37, 64, 0.22);
            background: #fff;
            padding: 8px;
        }

        .reset-card {
            background: #f7fbff;
            border-radius: 24px;
            box-shadow: 0 26px 54px rgba(10, 37, 64, 0.24);
            overflow: hidden;
        }

        .reset-hero {
            background: linear-gradient(135deg, #0f3d66 0%, #1f6aa5 100%);
            color: #fff;
            padding: 28px 32px 22px;
        }

        .reset-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 10px;
        }

        .reset-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 700;
            line-height: 1.15;
        }

        .reset-hero p {
            margin: 0;
            max-width: 420px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 15px;
        }

        .reset-body {
            padding: 28px 32px 32px;
        }

        .reset-alert {
            border: 0;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 22px;
            box-shadow: none;
        }

        .reset-alert .close {
            opacity: .55;
        }

        .reset-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .reset-fields {
            display: grid;
            gap: 16px;
        }

        .reset-field label {
            display: block;
            margin-bottom: 8px;
            color: #2a3f57;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .reset-field .input-group {
            width: 100%;
            border: 1px solid #d7e6f4;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 10px 24px rgba(18, 44, 74, 0.06);
        }

        .reset-field .input-group-addon {
            min-width: 52px;
            background: #f3f8fd;
            color: #1f6aa5;
            border: 0;
        }

        .reset-field .form-control {
            border: 0;
            height: 52px;
            box-shadow: none;
            font-size: 16px;
            padding: 12px 16px;
        }

        .reset-help {
            margin: 8px 2px 0;
            color: #5b6f82;
            font-size: 13px;
            line-height: 1.45;
        }

        .reset-actions {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-top: 26px;
        }

        .reset-actions .btn {
            border-radius: 14px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 700;
            border: 0;
            box-shadow: none;
        }

        .reset-actions .btn-success {
            background: #eef5fb;
            color: #1f6aa5;
        }

        .reset-actions .btn-primary {
            background: linear-gradient(135deg, #1f6aa5 0%, #2d8bd3 100%);
            min-width: 160px;
        }

        @media (max-width: 640px) {
            .reset-hero,
            .reset-body {
                padding-left: 20px;
                padding-right: 20px;
            }

            .reset-hero h1 {
                font-size: 26px;
            }

            .reset-actions {
                flex-direction: column-reverse;
            }

            .reset-actions .btn {
                width: 100%;
            }
        }
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
    <div class="reset-shell">
        <div class="reset-logo">
            <?php if ($Settings->logo2) {
                echo '<img src="' . base_url('assets/uploads/logos/' . $Settings->logo2) . '" alt="' . $Settings->site_name . '" />';
            } ?>
        </div>
        <div class="reset-card">
            <div class="reset-hero">
                <span class="reset-kicker">Secure Access</span>
                <h1>Reset Your Password</h1>
                <p>Choose a new password for <?= html_escape($identity_label); ?> and return to your Gamma workspace.</p>
            </div>
            <div class="reset-body">
                <?php if ($Settings->mmode) { ?>
                    <div class="alert alert-warning reset-alert">
                        <button data-dismiss="alert" class="close" type="button">&times;</button>
                        <?= lang('site_is_offline') ?>
                    </div>
                <?php } ?>
                <?php if ($error) { ?>
                    <div class="alert alert-danger reset-alert">
                        <button data-dismiss="alert" class="close" type="button">&times;</button>
                        <ul class="list-group"><?= $error; ?></ul>
                    </div>
                <?php } ?>
                <?php if ($message) { ?>
                    <div class="alert alert-success reset-alert">
                        <button data-dismiss="alert" class="close" type="button">&times;</button>
                        <ul class="list-group"><?= $message; ?></ul>
                    </div>
                <?php } ?>

                <?= admin_form_open('auth/reset_password/' . $code, 'class="login" data-toggle="validator"'); ?>
                <div class="reset-fields">
                    <div class="reset-field form-group">
                        <label for="<?= $new_password['id']; ?>">New Password</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            <?= form_input($new_password); ?>
                        </div>
                        <p class="reset-help"><?= lang('pasword_hint') ?></p>
                    </div>
                    <div class="reset-field form-group">
                        <label for="<?= $new_password_confirm['id']; ?>">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-shield"></i></span>
                            <?= form_input($new_password_confirm); ?>
                        </div>
                    </div>
                </div>
                <?= form_input($user_id); ?>
                <?= form_hidden($csrf); ?>

                <div class="reset-actions">
                    <a class="btn btn-success login_link" href="<?= admin_url('login') ?>">
                        <i class="fa fa-chevron-left"></i> <?= lang('back_to_login') ?>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <?= lang('submit') ?> <i class="fa fa-send"></i>
                    </button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script src="<?= $assets ?>js/jquery.js"></script>
<script src="<?= $assets ?>js/bootstrap.min.js"></script>
<script src="<?= $assets ?>js/jquery.cookie.js"></script>
<script src="<?= $assets ?>js/login.js"></script>
</body>
</html>
