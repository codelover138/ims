<?php defined('BASEPATH') OR exit('No direct script access allowed');
$php_sdate = isset($dateFormats['php_sdate']) ? $dateFormats['php_sdate'] : 'd/m/Y';
$fmt_date = function($d) use ($php_sdate) {
    if (empty($d)) return '—';
    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $d)) return date($php_sdate, strtotime($d));
    return $d;
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= lang('immigrants'); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #333; margin: 15px; }
        h1 { font-size: 14px; margin-bottom: 15px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px 6px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
    <h1><?= lang('immigrants'); ?> — <?= date($php_sdate, time()); ?></h1>
    <table>
        <thead>
            <tr>
                <th><?= lang('primary_id'); ?></th>
                <th><?= lang('surname'); ?></th>
                <th><?= lang('first_name'); ?></th>
                <th><?= lang('middle_name'); ?></th>
                <th><?= lang('phone_number'); ?></th>
                <th><?= lang('country_of_origin'); ?></th>
                <th><?= lang('last_date_of_entry'); ?></th>
                <th><?= lang('validity_of_stay'); ?></th>
                <th><?= lang('work_status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($immigrants)) {
                foreach ($immigrants as $i) { ?>
            <tr>
                <td><?= htmlspecialchars($i->primary_id); ?></td>
                <td><?= htmlspecialchars($i->surname); ?></td>
                <td><?= htmlspecialchars($i->first_name); ?></td>
                <td><?= htmlspecialchars($i->middle_name); ?></td>
                <td><?= htmlspecialchars($i->phone_number); ?></td>
                <td><?= htmlspecialchars($i->country_of_origin); ?></td>
                <td><?= $fmt_date($i->last_date_of_entry); ?></td>
                <td><?= $fmt_date($i->validity_of_stay); ?></td>
                <td><?= htmlspecialchars($i->work_status); ?></td>
            </tr>
            <?php }
            } else { ?>
            <tr><td colspan="9"><?= lang('no_data_available'); ?></td></tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
