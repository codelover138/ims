<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-folder-open"></i><span class="break"></span>Gamma Workspace</h2>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-md-8">
                        <p style="margin-bottom: 5px;"><strong>Logged in as:</strong> <?= htmlspecialchars(trim(($logged_in_user->first_name ?? '') . ' ' . ($logged_in_user->last_name ?? '')) ?: $logged_in_user->username, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p style="margin-bottom: 0;"><strong>Workspace:</strong> <?= htmlspecialchars($gamma_root_relative, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="<?= admin_url('document'); ?>" class="btn btn-default"><i class="fa fa-folder-open"></i> File Manager</a>
                        <a href="<?= admin_url('logout'); ?>" class="btn btn-danger"><i class="fa fa-sign-out"></i> Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-list"></i><span class="break"></span>Available Forms</h2>
            </div>
            <div class="box-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Form ID</th>
                                <th>Description</th>
                                <th style="width: 180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gamma_forms)) { ?>
                                <?php foreach ($gamma_forms as $form) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars(gamma_pad_form_id($form->form_id), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($form->description ?: $form->form_title, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" disabled="disabled">
                                                <?= htmlspecialchars($form->button_label ?: 'Open Form', ENT_QUOTES, 'UTF-8'); ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="3" class="text-center">No Gamma forms are assigned to this user yet.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
