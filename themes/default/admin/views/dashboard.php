<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if ($Owner || $Admin) { ?>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('quick_links') ?></h2>
            </div>
            <div class="box-content">
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="blightBlue white quick-button small" href="<?= admin_url('document') ?>">
                        <i class="fa fa-folder-open"></i>
                        <p><?= lang('File_Manager') ?></p>
                    </a>
                </div>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="bgrey white quick-button small" href="<?= admin_url('notifications') ?>">
                        <i class="fa fa-comments"></i>
                        <p><?= lang('notifications') ?></p>
                    </a>
                </div>
                <?php if ($Owner) { ?>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="bblue white quick-button small" href="<?= admin_url('auth/users') ?>">
                        <i class="fa fa-group"></i>
                        <p><?= lang('users') ?></p>
                    </a>
                </div>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="bblue white quick-button small" href="<?= admin_url('system_settings') ?>">
                        <i class="fa fa-cogs"></i>
                        <p><?= lang('settings') ?></p>
                    </a>
                </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('quick_links') ?></h2>
            </div>
            <div class="box-content">
            <?php if (!empty($GP['document-file_manager'])) { ?>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="blightBlue white quick-button small" href="<?= admin_url('document') ?>">
                        <i class="fa fa-folder-open"></i>
                        <p><?= lang('File_Manager') ?></p>
                    </a>
                </div>
            <?php } ?>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="bgrey white quick-button small" href="<?= admin_url('notifications') ?>">
                        <i class="fa fa-comments"></i>
                        <p><?= lang('notifications') ?></p>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
