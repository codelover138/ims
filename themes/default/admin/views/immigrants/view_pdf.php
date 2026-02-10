<?php defined('BASEPATH') OR exit('No direct script access allowed');
$i = $immigrant;
$php_sdate = isset($dateFormats['php_sdate']) ? $dateFormats['php_sdate'] : 'd/m/Y';
$fmt_date = function($d) use ($php_sdate) {
    if (empty($d)) return '—';
    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $d)) return date($php_sdate, strtotime($d));
    return $d;
};
$h = function($v) { return htmlspecialchars($v ?: '—'); };
$full_name = trim($h($i->surname . ' ' . $i->first_name . ($i->middle_name ? ' ' . $i->middle_name : '')));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= lang('immigrant_details'); ?> — <?= $h($i->primary_id); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a252f; margin: 0; padding: 0; }
        .header-bar {
            background: #2c3e50;
            color: #fff;
            padding: 22px 24px;
            margin-bottom: 22px;
        }
        .header-bar .title { font-size: 22px; font-weight: bold; margin: 0 0 6px 0; }
        .header-bar .subtitle { font-size: 13px; opacity: 0.95; margin: 0; }
        .header-bar .badge {
            display: inline-block;
            background: rgba(255,255,255,0.25);
            padding: 6px 14px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 18px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            overflow: hidden;
        }
        .section-head {
            background: #f0f1f3;
            padding: 14px 18px;
            font-size: 16px;
            font-weight: bold;
            color: #1a252f;
            border-bottom: 1px solid #d0d0d0;
        }
        .section-body { padding: 16px 18px; background: #fff; font-size: 14px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 14px; }
        table.data td { padding: 12px 0; vertical-align: top; }
        table.data tr { border-bottom: 1px solid #e8e8e8; }
        table.data tr:last-child { border-bottom: none; }
        table.data td:first-child { width: 40%; color: #495057; font-size: 13px; font-weight: 600; }
        table.data td:last-child { font-weight: 600; color: #1a252f; font-size: 14px; }
        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 8px 0 0; }
        .two-col td:last-child { padding: 0 0 0 8px; }
        .footer-note { margin-top: 24px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 10px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header-bar">
        <div class="title"><?= $full_name; ?></div>
        <div class="subtitle"><?= lang('immigrant_details'); ?></div>
        <span class="badge"><?= lang('primary_id'); ?>: <?= $h($i->primary_id); ?></span>
    </div>

    <table class="two-col" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="section">
                    <div class="section-head"><?= lang('personal_details'); ?></div>
                    <div class="section-body">
                        <table class="data">
                            <tr><td><?= lang('surname'); ?></td><td><?= $h($i->surname); ?></td></tr>
                            <tr><td><?= lang('first_name'); ?></td><td><?= $h($i->first_name); ?></td></tr>
                            <tr><td><?= lang('middle_name'); ?></td><td><?= $h($i->middle_name); ?></td></tr>
                            <tr><td><?= lang('phone_number'); ?></td><td><?= $h($i->phone_number); ?></td></tr>
                            <tr><td><?= lang('country_of_origin'); ?></td><td><?= $h($i->country_of_origin); ?></td></tr>
                            <tr><td><?= lang('last_date_of_entry'); ?></td><td><?= $fmt_date($i->last_date_of_entry); ?></td></tr>
                            <tr><td><?= lang('validity_of_stay'); ?></td><td><?= $fmt_date($i->validity_of_stay); ?></td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-head"><?= lang('identity_documents'); ?></div>
                    <div class="section-body">
                        <table class="data">
                            <tr><td><?= lang('passport_number'); ?></td><td><?= $h($i->passport_number); ?></td></tr>
                            <tr><td><?= lang('id_number'); ?></td><td><?= $h($i->id_number); ?></td></tr>
                            <tr><td><?= lang('asylum_seeker_number'); ?></td><td><?= $h($i->asylum_seeker_number); ?></td></tr>
                            <tr><td><?= lang('work_permit_number'); ?></td><td><?= $h($i->work_permit_number); ?></td></tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="section">
                    <div class="section-head"><?= lang('residence'); ?></div>
                    <div class="section-body">
                        <table class="data">
                            <tr><td><?= lang('municipality'); ?></td><td><?= $h($i->municipality); ?></td></tr>
                            <tr><td><?= lang('town'); ?></td><td><?= $h($i->town); ?></td></tr>
                            <tr><td><?= lang('area'); ?></td><td><?= $h($i->area); ?></td></tr>
                            <tr><td><?= lang('narrative_direction'); ?></td><td><?= nl2br($h($i->narrative_direction)); ?></td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-head"><?= lang('employment'); ?></div>
                    <div class="section-body">
                        <table class="data">
                            <tr><td><?= lang('work_status'); ?></td><td><?= $h($i->work_status); ?></td></tr>
                            <tr><td><?= lang('line_of_business'); ?></td><td><?= nl2br($h($i->line_of_business)); ?></td></tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-head"><?= lang('documents_upload'); ?></div>
        <div class="section-body">
            <table class="data">
                <tr><td><?= lang('doc_head_shot'); ?></td><td><?= !empty($i->doc_head_shot) ? $h(basename($i->doc_head_shot)) : '—'; ?></td></tr>
                <tr><td><?= lang('doc_passport'); ?></td><td><?= !empty($i->doc_passport) ? $h(basename($i->doc_passport)) : '—'; ?></td></tr>
                <tr><td><?= lang('doc_asylum_permit'); ?></td><td><?= !empty($i->doc_asylum_permit) ? $h(basename($i->doc_asylum_permit)) : '—'; ?></td></tr>
                <tr><td><?= lang('doc_work_permit'); ?></td><td><?= !empty($i->doc_work_permit) ? $h(basename($i->doc_work_permit)) : '—'; ?></td></tr>
                <tr><td><?= lang('doc_other'); ?></td><td><?= !empty($i->doc_other) ? $h(basename($i->doc_other)) : '—'; ?></td></tr>
            </table>
        </div>
    </div>

    <div class="footer-note"><?= lang('immigrant_details'); ?> — <?= $h($i->primary_id); ?> · <?= date($php_sdate, time()); ?></div>
</body>
</html>
