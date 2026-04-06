<?php defined('BASEPATH') OR exit('No direct script access allowed');
// $gfp is null for Owner/Admin (all allowed), or an array of permissions for others.
$can_import_form   = ($gfp === null || !empty($gfp['gamma-forms-import-form']));
$can_import_inputs = ($gfp === null || !empty($gfp['gamma-forms-import-inputs']));
$can_generate      = ($gfp === null || !empty($gfp['gamma-forms-generate']));
$can_delete        = ($gfp === null || !empty($gfp['gamma-forms-delete']));
?>

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-list-alt"></i><span class="break"></span>Gamma Forms</h2>
            </div>
            <div class="box-content">
                <div class="btn-group" style="margin-bottom: 15px;">
                    <?php if ($can_import_form) { ?>
                        <a href="<?= admin_url('gamma_forms/import_forms_csv'); ?>" class="btn btn-primary">Import Forms CSV</a>
                    <?php } ?>
                    <?php if ($can_import_inputs) { ?>
                        <a href="<?= admin_url('gamma_forms/import_inputs_csv'); ?>" class="btn btn-default">Import Inputs CSV</a>
                    <?php } ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 100px;">Form ID</th>
                            <th>Title</th>
                            <th style="width: 180px;">Assigned User</th>
                            <?php if ($can_generate || $can_delete) { ?>
                                <th style="width: 280px;">Actions</th>
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
                                    <td><?= htmlspecialchars(trim(($form->assigned_name ?? '') ?: ($form->username ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <?php if ($can_generate || $can_delete) { ?>
                                        <td>
                                            <?php if ($can_generate) { ?>
                                                <a href="<?= admin_url('gamma_forms/generate_input_form/' . $form->form_id); ?>" class="btn btn-primary btn-sm">Generate Input Form</a>
                                            <?php } ?>
                                            <?php if ($can_delete) { ?>
                                                <a href="<?= admin_url('gamma_forms/delete_form/' . $form->form_id); ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Delete form #<?= (int) $form->form_id; ?> and all its inputs? This cannot be undone.');">
                                                    Delete
                                                </a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center">No Gamma forms have been imported yet.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
