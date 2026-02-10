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
                    <a class="bdarkGreen white quick-button small" href="<?= admin_url('immigrants') ?>">
                        <i class="fa fa-user-plus"></i>
                        <p><?= lang('immigrants') ?></p>
                    </a>
                </div>
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
            <?php if (!empty($GP['immigrants-index'])) { ?>
                <div class="col-lg-1 col-md-2 col-xs-6">
                    <a class="bdarkGreen white quick-button small" href="<?= admin_url('immigrants') ?>">
                        <i class="fa fa-user-plus"></i>
                        <p><?= lang('immigrants') ?></p>
                    </a>
                </div>
            <?php } if (!empty($GP['document-file_manager'])) { ?>
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

<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-user-plus"></i> <?= lang('immigrants') ?> &ndash; <?= lang('latest_five') ?></h2>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" style="margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th style="width:30px !important;">#</th>
                                    <th><?= lang('primary_id'); ?></th>
                                    <th><?= lang('surname'); ?></th>
                                    <th><?= lang('first_name'); ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($latest_immigrants)) {
                                    $r = 1;
                                    foreach ($latest_immigrants as $row) {
                                        $name = trim(($row->surname ?? '') . ' ' . ($row->first_name ?? ''));
                                        echo '<tr class="pointer" onclick="window.location=\'' . admin_url('immigrants/view/' . $row->id) . '\'"><td>' . $r . '</td>
                                            <td>' . htmlspecialchars($row->primary_id ?? '') . '</td>
                                            <td>' . htmlspecialchars($row->surname ?? '') . '</td>
                                            <td>' . htmlspecialchars($row->first_name ?? '') . '</td>
                                            <td><a href="' . admin_url('immigrants/view/' . $row->id) . '" class="btn btn-xs btn-primary">' . lang('view') . '</a></td>
                                        </tr>';
                                        $r++;
                                    }
                                } else { ?>
                                    <tr>
                                        <td colspan="5" class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($Owner || $Admin || !empty($GP['immigrants-add'])) { ?>
                        <p style="margin-top: 10px;">
                            <a href="<?= admin_url('immigrants/add') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?= lang('add_immigrant') ?></a>
                            <a href="<?= admin_url('immigrants') ?>" class="btn btn-default"><?= lang('view') ?> <?= lang('immigrants') ?></a>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
