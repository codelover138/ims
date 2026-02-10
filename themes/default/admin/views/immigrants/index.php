<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.immigrant_details_link {
    cursor: pointer;
}

.immigrant_details_link td:not(:first-child):not(:last-child) {
    cursor: pointer;
}
</style>
<script>
$(document).ready(function() {
    var cTable = $('#ImmData').dataTable({
        "aaSorting": [
            [1, "asc"]
        ],
        "aLengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "<?= lang('all') ?>"]
        ],
        "iDisplayLength": <?= $Settings->rows_per_page ?>,
        'bProcessing': true,
        'bServerSide': true,
        'sAjaxSource': '<?= admin_url('immigrants/getImmigrants') ?>',
        'fnServerData': function(sSource, aoData, fnCallback) {
            aoData.push({
                "name": "<?= $this->security->get_csrf_token_name() ?>",
                "value": "<?= $this->security->get_csrf_hash() ?>"
            });
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        },
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {
            nRow.id = aData[0];
            nRow.className = "immigrant_details_link";
            return nRow;
        },
        "aoColumns": [{
                "mData": "0",
                "bVisible": false
            },
            {
                "mData": "1"
            },
            {
                "mData": "2"
            },
            {
                "mData": "3"
            },
            {
                "mData": "4"
            },
            {
                "mData": "5"
            },
            {
                "mData": "6"
            },
            {
                "mData": "7"
            },
            {
                "mData": "8",
                "bSortable": false
            }
        ]
    });
    $('body').on('click', '.immigrant_details_link td:not(:first-child, :last-child)', function() {
        window.location.href = '<?= admin_url("immigrants/view/"); ?>' + $(this).parent(
            '.immigrant_details_link').attr('id');
    });
});
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-globe"></i><?= lang('immigrants'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu">
                        <li>
                            <a href="<?= admin_url('immigrants/add'); ?>">
                                <i class="fa fa-plus-circle"></i> <?= lang("add_immigrant"); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= admin_url('immigrants/export_xls'); ?>">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel'); ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>
                <div class="table-responsive">
                    <table id="ImmData" cellpadding="0" cellspacing="0" border="0"
                        class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                            <tr class="primary">
                                <th style="display:none;">ID</th>
                                <th><?= lang("primary_id"); ?></th>
                                <th><?= lang("surname"); ?></th>
                                <th><?= lang("first_name"); ?></th>
                                <th><?= lang("middle_name"); ?></th>
                                <th><?= lang("phone_number"); ?></th>
                                <th><?= lang("country_of_origin"); ?></th>
                                <th><?= lang("work_status"); ?></th>
                                <th style="min-width:100px;"><?= lang("actions"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>