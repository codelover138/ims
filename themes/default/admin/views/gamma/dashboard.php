<?php defined('BASEPATH') OR exit('No direct script access allowed');
$can_manage_forms = !empty($can_manage_forms) || !empty($Owner) || !empty($Admin);
$can_submit   = isset($can_submit)   ? $can_submit   : (!empty($Owner) || !empty($Admin));
$can_delete   = isset($can_delete)   ? $can_delete   : (!empty($Owner) || !empty($Admin));
$can_download = isset($can_download) ? $can_download : (!empty($Owner) || !empty($Admin));
?>

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
                        <?php if ($can_manage_forms) { ?>
                            <a href="<?= admin_url('gamma_forms'); ?>" class="btn btn-primary"><i class="fa fa-cogs"></i> Manage Forms</a>
                        <?php } ?>
                        <a href="<?= admin_url('gamma/file_manager'); ?>" class="btn btn-default"><i class="fa fa-folder-open"></i> File Manager</a>
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
                                <?php if ($can_submit) { ?>
                                    <th style="width: 180px;">Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gamma_forms)) { ?>
                                <?php foreach ($gamma_forms as $form) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars(gamma_pad_form_id($form->form_id), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($form->form_title, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <div><?= htmlspecialchars($form->description ?: '', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </td>
                                        <?php if ($can_submit) { ?>
                                            <td>
                                                <a href="<?= admin_url('gamma/open_form/' . $form->form_id); ?>" class="btn btn-primary btn-sm">
                                                    <?= htmlspecialchars($form->button_label ?: 'Open Form', ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                        <?php } ?>
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

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-download"></i><span class="break"></span>Generated Documents</h2>
            </div>
            <div class="box-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Output ID</th>
                                <th>Form</th>
                                <th style="width: 180px;">Created</th>
                                <?php if ($can_download || $can_delete) { ?>
                                    <th style="width: 180px;">Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($generated_documents)) { ?>
                                <?php foreach ($generated_documents as $document) { ?>
                                    <tr>
                                        <td><?= (int) $document->output_file_id; ?></td>
                                        <td><?= htmlspecialchars($document->form_title ?: $document->output_filename_base, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($document->date_created, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <?php if ($can_download || $can_delete) { ?>
                                            <td>
                                                <?php if ($can_download) { ?>
                                                    <a href="<?= admin_url('gamma/download/' . $document->output_file_id); ?>" class="btn btn-default btn-sm">
                                                        Download
                                                    </a>
                                                <?php } ?>
                                                <?php if ($can_delete) { ?>
                                                    <a href="<?= admin_url('gamma/delete_document/' . $document->output_file_id); ?>"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Delete this document? The file will also be removed.');">
                                                        Delete
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center">No documents have been generated yet.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
