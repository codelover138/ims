<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$i = $immigrant;
$fmt_date = function($d) use ($dateFormats) {
    if (empty($d)) return '—';
    if (strlen($d) >= 10 && preg_match('/^\d{4}-\d{2}-\d{2}/', $d)) {
        return date($dateFormats['php_sdate'], strtotime($d));
    }
    return $d;
};
$full_name = trim(htmlspecialchars($i->surname . ' ' . $i->first_name . ($i->middle_name ? ' ' . $i->middle_name : '')));
?>
<style>
.immigrant-view .profile-hero {
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    border-radius: 8px;
    padding: 32px 28px;
    margin-bottom: 26px;
    color: #fff;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.25);
}
.immigrant-view .profile-hero .avatar-wrap {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
}
.immigrant-view .profile-hero h1 {
    margin: 0 0 8px 0;
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.immigrant-view .profile-hero .primary-id-badge {
    display: inline-block;
    background: rgba(255,255,255,0.25);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 600;
    margin-top: 10px;
}
.immigrant-view .profile-hero .meta {
    font-size: 1.05rem;
    opacity: 0.95;
    margin-top: 12px;
}
.immigrant-view .profile-hero .meta span { margin-right: 16px; }
.immigrant-view .card-section {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e8ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    margin-bottom: 20px;
    overflow: hidden;
}
.immigrant-view .card-section .card-head {
    padding: 16px 22px;
    font-size: 1.35rem;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 1px solid #e8ecef;
    background: #f8f9fa;
}
.immigrant-view .card-section .card-head i { margin-right: 10px; opacity: 0.85; font-size: 1.2em; }
.immigrant-view .card-section .card-body { padding: 20px 22px; font-size: 1.15rem; }
.immigrant-view .info-table { width: 100%; border-collapse: collapse; font-size: 1.2rem; }
.immigrant-view .info-table tr { border-bottom: 1px solid #eee; }
.immigrant-view .info-table tr:last-child { border-bottom: none; }
.immigrant-view .info-table td { padding: 14px 0; vertical-align: top; }
.immigrant-view .info-table td:first-child { width: 42%; color: #495057; font-size: 1.15rem; font-weight: 500; }
.immigrant-view .info-table td:last-child { font-weight: 600; color: #1a252f; font-size: 1.2rem; }
.immigrant-view .doc-link {
    display: inline-block;
    padding: 8px 14px;
    background: #e8f4fc;
    color: #2980b9;
    border-radius: 4px;
    text-decoration: none;
    font-size: 1.15rem;
    margin-bottom: 6px;
}
.immigrant-view .doc-link:hover { background: #d0e8f7; color: #1a5276; }
</style>
<div class="box immigrant-view">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-file-text-o"></i> <?= lang('immigrant_details'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions'); ?>"></i></a>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="<?= admin_url('immigrants/pdf/' . $i->id); ?>"><i class="fa fa-file-pdf-o"></i> <?= lang('download_pdf'); ?></a></li>
                        <li><a href="<?= admin_url('immigrants/edit/' . $i->id); ?>"><i class="fa fa-edit"></i> <?= lang('edit_immigrant'); ?></a></li>
                        <li><a href="<?= admin_url('immigrants'); ?>"><i class="fa fa-arrow-left"></i> <?= lang('back'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="profile-hero">
                    <div class="row">
                        <div class="col-xs-2 col-sm-1">
                            <div class="avatar-wrap"><i class="fa fa-user"></i></div>
                        </div>
                        <div class="col-xs-10 col-sm-11">
                            <h1><?= $full_name; ?></h1>
                            <span class="primary-id-badge"><?= lang('primary_id'); ?>: <?= htmlspecialchars($i->primary_id); ?></span>
                            <div class="meta">
                                <?php if ($i->phone_number) { ?><span><i class="fa fa-phone"></i> <?= htmlspecialchars($i->phone_number); ?></span><?php } ?>
                                <?php if ($i->country_of_origin) { ?><span><i class="fa fa-globe"></i> <?= htmlspecialchars($i->country_of_origin); ?></span><?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-section">
                            <div class="card-head"><i class="fa fa-user"></i> <?= lang('personal_details'); ?></div>
                            <div class="card-body">
                                <table class="info-table">
                                    <tr><td><?= lang('surname'); ?></td><td><?= htmlspecialchars($i->surname); ?></td></tr>
                                    <tr><td><?= lang('first_name'); ?></td><td><?= htmlspecialchars($i->first_name); ?></td></tr>
                                    <tr><td><?= lang('middle_name'); ?></td><td><?= htmlspecialchars($i->middle_name ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('phone_number'); ?></td><td><?= htmlspecialchars($i->phone_number ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('country_of_origin'); ?></td><td><?= htmlspecialchars($i->country_of_origin ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('last_date_of_entry'); ?></td><td><?= $fmt_date($i->last_date_of_entry); ?></td></tr>
                                    <tr><td><?= lang('validity_of_stay'); ?></td><td><?= $fmt_date($i->validity_of_stay); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-section">
                            <div class="card-head"><i class="fa fa-id-card"></i> <?= lang('identity_documents'); ?></div>
                            <div class="card-body">
                                <table class="info-table">
                                    <tr><td><?= lang('passport_number'); ?></td><td><?= htmlspecialchars($i->passport_number ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('id_number'); ?></td><td><?= htmlspecialchars($i->id_number ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('asylum_seeker_number'); ?></td><td><?= htmlspecialchars($i->asylum_seeker_number ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('work_permit_number'); ?></td><td><?= htmlspecialchars($i->work_permit_number ?: '—'); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-section">
                            <div class="card-head"><i class="fa fa-map-marker"></i> <?= lang('residence'); ?></div>
                            <div class="card-body">
                                <table class="info-table">
                                    <tr><td><?= lang('municipality'); ?></td><td><?= htmlspecialchars($i->municipality ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('town'); ?></td><td><?= htmlspecialchars($i->town ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('area'); ?></td><td><?= htmlspecialchars($i->area ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('narrative_direction'); ?></td><td><?= nl2br(htmlspecialchars($i->narrative_direction ?: '—')); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-section">
                            <div class="card-head"><i class="fa fa-briefcase"></i> <?= lang('employment'); ?></div>
                            <div class="card-body">
                                <table class="info-table">
                                    <tr><td><?= lang('work_status'); ?></td><td><?= htmlspecialchars($i->work_status ?: '—'); ?></td></tr>
                                    <tr><td><?= lang('line_of_business'); ?></td><td><?= nl2br(htmlspecialchars($i->line_of_business ?: '—')); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-section">
                    <div class="card-head"><i class="fa fa-paperclip"></i> <?= lang('documents_upload'); ?></div>
                    <div class="card-body">
                        <table class="info-table">
                            <tr>
                                <td><?= lang('doc_head_shot'); ?></td>
                                <td><?php if (!empty($i->doc_head_shot)) { ?><a class="doc-link" href="<?= base_url($i->doc_head_shot); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= basename($i->doc_head_shot); ?></a><?php } else { ?>—<?php } ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('doc_passport'); ?></td>
                                <td><?php if (!empty($i->doc_passport)) { ?><a class="doc-link" href="<?= base_url($i->doc_passport); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= basename($i->doc_passport); ?></a><?php } else { ?>—<?php } ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('doc_asylum_permit'); ?></td>
                                <td><?php if (!empty($i->doc_asylum_permit)) { ?><a class="doc-link" href="<?= base_url($i->doc_asylum_permit); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= basename($i->doc_asylum_permit); ?></a><?php } else { ?>—<?php } ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('doc_work_permit'); ?></td>
                                <td><?php if (!empty($i->doc_work_permit)) { ?><a class="doc-link" href="<?= base_url($i->doc_work_permit); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= basename($i->doc_work_permit); ?></a><?php } else { ?>—<?php } ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('doc_other'); ?></td>
                                <td><?php if (!empty($i->doc_other)) { ?><a class="doc-link" href="<?= base_url($i->doc_other); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= basename($i->doc_other); ?></a><?php } else { ?>—<?php } ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
