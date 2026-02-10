<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    .table td:first-child {
        font-weight: bold;
    }

    label {
        margin-right: 10px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('group_permissions'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang("set_permissions"); ?></p>

                <?php if (!empty($p)) {
                    if ($p->group_id != 1) {

                        echo admin_form_open("system_settings/permissions/" . $id); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                                <tr>
                                    <th colspan="6"
                                        class="text-center"><?php echo $group->description . ' ( ' . $group->name . ' ) ' . $this->lang->line("group_permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center"><?= lang("module_name"); ?>
                                    </th>
                                    <th colspan="5" class="text-center"><?= lang("permissions"); ?></th>
                                </tr>
                                <tr>
                                    <th class="text-center"><?= lang("view"); ?></th>
                                    <th class="text-center"><?= lang("add"); ?></th>
                                    <th class="text-center"><?= lang("edit"); ?></th>
                                    <th class="text-center"><?= lang("delete"); ?></th>
                                    <th class="text-center"><?= lang("misc"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= lang("immigrants"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="immigrants-index" <?php echo (isset($p->{'immigrants-index'}) && $p->{'immigrants-index'}) ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="immigrants-add" <?php echo (isset($p->{'immigrants-add'}) && $p->{'immigrants-add'}) ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="immigrants-edit" <?php echo (isset($p->{'immigrants-edit'}) && $p->{'immigrants-edit'}) ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" class="checkbox" name="immigrants-delete" <?php echo (isset($p->{'immigrants-delete'}) && $p->{'immigrants-delete'}) ? "checked" : ''; ?>>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?= lang("File_Manager"); ?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" id="document-file_manager" class="checkbox" name="document-file_manager" <?php echo (isset($p->{'document-file_manager'}) && $p->{'document-file_manager'}) ? "checked" : ''; ?>>
                                    </td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td>
                                        <input type="checkbox" value="1" id="document-folder_create" class="checkbox" name="document-folder_create" <?php echo (isset($p->{'document-folder_create'}) && $p->{'document-folder_create'}) ? "checked" : ''; ?>>
                                        <label for="document-folder_create" class="padding05"><?= lang('Folder_Create') ?></label>
                                        <input type="checkbox" value="1" id="document-folder_download" class="checkbox" name="document-folder_download" <?php echo (isset($p->{'document-folder_download'}) && $p->{'document-folder_download'}) ? "checked" : ''; ?>>
                                        <label for="document-folder_download" class="padding05"><?= lang('Folder_Download') ?></label>
                                        <input type="checkbox" value="1" id="document-upload" class="checkbox" name="document-upload" <?php echo (isset($p->{'document-upload'}) && $p->{'document-upload'}) ? "checked" : ''; ?>>
                                        <label for="document-upload" class="padding05"><?= lang('Upload') ?></label>
                                        <input type="checkbox" value="1" id="document-file_delete" class="checkbox" name="document-file_delete" <?php echo (isset($p->{'document-file_delete'}) && $p->{'document-file_delete'}) ? "checked" : ''; ?>>
                                        <label for="document-file_delete" class="padding05"><?= lang('File_Delete') ?></label>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><?=lang('update')?></button>
                        </div>
                        <?php echo form_close();
                    } else {
                        echo $this->lang->line("group_x_allowed");
                    }
                } else {
                    echo $this->lang->line("group_x_allowed");
                } ?>


            </div>
        </div>
    </div>
</div>
