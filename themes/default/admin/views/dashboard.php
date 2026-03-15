<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$current_user = trim(($this->session->userdata('first_name') ?: '') . ' ' . ($this->session->userdata('last_name') ?: ''));
if ($current_user === '') {
    $current_user = $this->session->userdata('username') ?: 'Team';
}

$dashboard_trim_text = function ($text, $limit = 80) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 3) . '...' : $text;
    }

    return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
};
?>

<div class="dashboard-shell">
    <div class="dashboard-hero">
        <div class="dashboard-hero-copy">
            <span class="dashboard-kicker"><?= $is_full_dashboard ? 'Admin Workspace' : 'My Workspace'; ?></span>
            <h1><?= html_escape($dashboard_greeting); ?>, <?= html_escape($current_user); ?></h1>
            <p>
                <?php if ($is_full_dashboard) { ?>
                    Here is a live operational snapshot of <?= html_escape($Settings->site_name); ?>, with the tools and recent activity most useful to your day.
                <?php } else { ?>
                    Here is your personalized dashboard for <?= html_escape($Settings->site_name); ?>, focused on your access, assignments, and day-to-day activity.
                <?php } ?>
            </p>
        </div>
        <div class="dashboard-hero-aside">
            <div class="dashboard-status-card">
                <span class="dashboard-status-label">Today</span>
                <strong><?= date('d M Y'); ?></strong>
                <span><?= date('l'); ?></span>
            </div>
            <div class="dashboard-status-card">
                <span class="dashboard-status-label"><?= $is_full_dashboard ? 'Workspace' : 'Access'; ?></span>
                <strong><?= html_escape($Settings->site_name); ?></strong>
                <span><?= html_escape($dashboard_profile['role_label']); ?></span>
            </div>
        </div>
    </div>

    <div class="dashboard-metrics">
        <?php foreach ($dashboard_metrics as $metric) { ?>
            <a class="dashboard-metric-card tone-<?= $metric['tone']; ?>" href="<?= $metric['url']; ?>">
                <div class="dashboard-metric-icon"><i class="fa <?= $metric['icon']; ?>"></i></div>
                <div class="dashboard-metric-content">
                    <span class="dashboard-metric-label"><?= html_escape($metric['label']); ?></span>
                    <strong><?= (int) $metric['value']; ?></strong>
                    <span class="dashboard-metric-note"><?= html_escape($metric['note']); ?></span>
                </div>
            </a>
        <?php } ?>
    </div>

    <div class="row dashboard-grid">
        <div class="col-lg-8">
            <div class="dashboard-panel">
                <div class="dashboard-panel-head">
                    <div>
                        <span class="dashboard-panel-kicker">Shortcuts</span>
                        <h3><?= $is_full_dashboard ? 'Quick Actions' : 'My Shortcuts'; ?></h3>
                    </div>
                    <span class="dashboard-panel-meta"><?= count($dashboard_shortcuts); ?> available</span>
                </div>
                <div class="dashboard-shortcuts">
                    <?php foreach ($dashboard_shortcuts as $shortcut) { ?>
                        <a class="dashboard-shortcut" href="<?= $shortcut['url']; ?>">
                            <div class="dashboard-shortcut-icon"><i class="fa <?= $shortcut['icon']; ?>"></i></div>
                            <div class="dashboard-shortcut-copy">
                                <strong><?= html_escape($shortcut['label']); ?></strong>
                                <span><?= html_escape($shortcut['description']); ?></span>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="dashboard-panel-head">
                    <div>
                        <span class="dashboard-panel-kicker"><?= $is_full_dashboard ? 'Activity' : 'Access History'; ?></span>
                        <h3><?= $is_full_dashboard ? 'Recent Sign-ins' : 'My Recent Sign-ins'; ?></h3>
                    </div>
                    <span class="dashboard-panel-meta"><?= count($recent_logins); ?> entries</span>
                </div>
                <?php if (!empty($recent_logins)) { ?>
                    <div class="dashboard-list">
                        <?php foreach ($recent_logins as $login) { ?>
                            <div class="dashboard-list-item">
                                <div class="dashboard-list-icon"><i class="fa fa-sign-in"></i></div>
                                <div class="dashboard-list-copy">
                                    <strong><?= html_escape($login->login); ?></strong>
                                    <span>IP <?= html_escape($login->ip_address); ?></span>
                                </div>
                                <div class="dashboard-list-meta"><?= $this->sma->hrld($login->time); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="dashboard-empty"><?= $is_full_dashboard ? 'No recent login activity is available yet.' : 'No recent sign-in history is available for your account yet.'; ?></div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if (!$is_full_dashboard) { ?>
                <div class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Profile</span>
                            <h3>My Workspace</h3>
                        </div>
                        <a href="<?= $dashboard_profile['profile_url']; ?>" class="dashboard-panel-link">Open profile</a>
                    </div>
                    <div class="dashboard-stack compact">
                        <div class="dashboard-stack-card compact">
                            <strong>Username</strong>
                            <span><?= html_escape($dashboard_profile['username'] ?: 'Not available'); ?></span>
                        </div>
                        <div class="dashboard-stack-card compact">
                            <strong>Email</strong>
                            <span><?= html_escape($dashboard_profile['email'] ?: 'Not available'); ?></span>
                        </div>
                        <div class="dashboard-stack-card compact">
                            <strong>Assigned Service Point</strong>
                            <span><?= html_escape($dashboard_profile['warehouse_name'] ?: 'Not assigned'); ?></span>
                        </div>
                        <div class="dashboard-stack-card compact">
                            <strong>Last Access</strong>
                            <span>
                                <?php if (!empty($dashboard_profile['last_login'])) { ?>
                                    <?= $this->sma->hrld($dashboard_profile['last_login']); ?>
                                <?php } else { ?>
                                    First sign-in not recorded
                                <?php } ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="dashboard-panel">
                <div class="dashboard-panel-head">
                    <div>
                        <span class="dashboard-panel-kicker">Schedule</span>
                        <h3><?= $is_full_dashboard ? 'Upcoming Events' : 'My Upcoming Events'; ?></h3>
                    </div>
                    <a href="<?= admin_url('calendar'); ?>" class="dashboard-panel-link">Open calendar</a>
                </div>
                <?php if (!empty($upcoming_events)) { ?>
                    <div class="dashboard-stack">
                        <?php foreach ($upcoming_events as $event) { ?>
                            <div class="dashboard-stack-card">
                                <strong><?= html_escape($event->title); ?></strong>
                                <span><?= $this->sma->hrld($event->start); ?></span>
                                <p><?= html_escape($dashboard_trim_text($event->description, 90)); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="dashboard-empty"><?= $is_full_dashboard ? 'No upcoming events are scheduled.' : 'No upcoming events are linked to your account yet.'; ?></div>
                <?php } ?>
            </div>

            <div class="dashboard-panel">
                <div class="dashboard-panel-head">
                    <div>
                        <span class="dashboard-panel-kicker">Notices</span>
                        <h3><?= $is_full_dashboard ? 'Latest Notifications' : 'Relevant Notifications'; ?></h3>
                    </div>
                    <a href="<?= admin_url('notifications'); ?>" class="dashboard-panel-link"><?= $is_full_dashboard ? 'Manage' : 'View'; ?></a>
                </div>
                <?php if (!empty($recent_notifications)) { ?>
                    <div class="dashboard-stack compact">
                        <?php foreach ($recent_notifications as $notice) { ?>
                            <div class="dashboard-stack-card compact">
                                <strong><?= html_escape($dashboard_trim_text($notice->comment, 72)); ?></strong>
                                <span>
                                    <?php if (!empty($notice->from_date)) { ?>
                                        <?= $this->sma->hrld($notice->from_date); ?>
                                    <?php } else { ?>
                                        Notification #<?= (int) $notice->id; ?>
                                    <?php } ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="dashboard-empty"><?= $is_full_dashboard ? 'No notifications have been posted yet.' : 'No notifications are currently targeted to your workspace.'; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-shell {
        padding: 4px 0 8px;
    }

    .dashboard-hero {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 28px 30px;
        margin-bottom: 22px;
        border: 1px solid #d8e2e8;
        border-radius: 18px;
        background:
            radial-gradient(circle at top right, rgba(60, 141, 188, 0.12), transparent 30%),
            linear-gradient(135deg, #ffffff 0%, #f4f8fb 55%, #eef4f8 100%);
        box-shadow: 0 18px 38px rgba(31, 45, 58, 0.06);
    }

    .dashboard-kicker,
    .dashboard-panel-kicker,
    .dashboard-status-label {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #7a8f9f;
    }

    .dashboard-hero-copy h1 {
        margin: 8px 0 10px;
        font-size: 34px;
        line-height: 1.08;
        color: #1f2d3a;
        font-weight: 800;
    }

    .dashboard-hero-copy p {
        max-width: 640px;
        margin: 0;
        color: #66798b;
        font-size: 15px;
        line-height: 1.7;
    }

    .dashboard-hero-aside {
        display: grid;
        grid-template-columns: repeat(2, minmax(170px, 1fr));
        gap: 14px;
        min-width: 360px;
    }

    .dashboard-status-card {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 18px;
        border: 1px solid #dce6ed;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.86);
        box-shadow: 0 12px 28px rgba(31, 45, 58, 0.05);
    }

    .dashboard-status-card strong {
        color: #243746;
        font-size: 18px;
        font-weight: 800;
    }

    .dashboard-status-card span:last-child {
        color: #73889a;
        line-height: 1.5;
    }

    .dashboard-metrics {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .dashboard-metric-card {
        display: flex;
        gap: 14px;
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #dbe4ea;
        background: #fff;
        box-shadow: 0 14px 28px rgba(31, 45, 58, 0.05);
        text-decoration: none;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .dashboard-metric-card:hover,
    .dashboard-metric-card:focus {
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 18px 32px rgba(31, 45, 58, 0.08);
    }

    .dashboard-metric-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 18px;
        flex-shrink: 0;
    }

    .dashboard-metric-card.tone-primary .dashboard-metric-icon {
        background: rgba(60, 141, 188, 0.12);
        color: #3c8dbc;
    }

    .dashboard-metric-card.tone-success .dashboard-metric-icon {
        background: rgba(92, 184, 92, 0.12);
        color: #5cb85c;
    }

    .dashboard-metric-card.tone-warning .dashboard-metric-icon {
        background: rgba(240, 173, 78, 0.14);
        color: #f0ad4e;
    }

    .dashboard-metric-card.tone-info .dashboard-metric-icon {
        background: rgba(91, 192, 222, 0.14);
        color: #5bc0de;
    }

    .dashboard-metric-content {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }

    .dashboard-metric-label {
        color: #6f8394;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-metric-content strong {
        color: #1f2d3a;
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
    }

    .dashboard-metric-note {
        color: #7a8f9f;
        font-size: 12px;
        line-height: 1.5;
    }

    .dashboard-grid .col-lg-8,
    .dashboard-grid .col-lg-4 {
        margin-bottom: 18px;
    }

    .dashboard-panel {
        padding: 22px;
        margin-bottom: 18px;
        border: 1px solid #dbe4ea;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
        box-shadow: 0 16px 32px rgba(31, 45, 58, 0.05);
    }

    .dashboard-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .dashboard-panel-head h3 {
        margin: 6px 0 0;
        font-size: 22px;
        color: #223545;
        font-weight: 800;
    }

    .dashboard-panel-meta,
    .dashboard-panel-link {
        color: #7a8f9f;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .dashboard-panel-link {
        color: #3c8dbc;
    }

    .dashboard-shortcuts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .dashboard-shortcut {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 16px;
        border: 1px solid #dde7ed;
        border-radius: 16px;
        background: #fff;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
    }

    .dashboard-shortcut:hover,
    .dashboard-shortcut:focus {
        text-decoration: none;
        border-color: #c7d9e5;
        transform: translateY(-1px);
        box-shadow: 0 14px 24px rgba(31, 45, 58, 0.05);
    }

    .dashboard-shortcut-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: rgba(60, 141, 188, 0.12);
        color: #3c8dbc;
        font-size: 18px;
        flex-shrink: 0;
    }

    .dashboard-shortcut-copy strong {
        display: block;
        margin-bottom: 4px;
        color: #223545;
        font-size: 15px;
    }

    .dashboard-shortcut-copy span {
        display: block;
        color: #708596;
        line-height: 1.55;
        font-size: 13px;
    }

    .dashboard-list {
        display: grid;
        gap: 12px;
    }

    .dashboard-list-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 14px;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid #dee7ed;
        border-radius: 14px;
        background: #fff;
    }

    .dashboard-list-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eef4f8;
        color: #3c8dbc;
    }

    .dashboard-list-copy strong {
        display: block;
        color: #233746;
        font-size: 14px;
    }

    .dashboard-list-copy span,
    .dashboard-list-meta {
        color: #7990a0;
        font-size: 12px;
        line-height: 1.5;
    }

    .dashboard-stack {
        display: grid;
        gap: 12px;
    }

    .dashboard-stack-card {
        padding: 16px;
        border: 1px solid #dee7ed;
        border-radius: 14px;
        background: #fff;
    }

    .dashboard-stack-card strong {
        display: block;
        margin-bottom: 4px;
        color: #233746;
        font-size: 15px;
    }

    .dashboard-stack-card span {
        display: block;
        margin-bottom: 8px;
        color: #3c8dbc;
        font-size: 12px;
        font-weight: 700;
    }

    .dashboard-stack-card p {
        margin: 0;
        color: #758b9b;
        line-height: 1.6;
        font-size: 13px;
    }

    .dashboard-stack.compact .dashboard-stack-card.compact {
        padding: 14px 16px;
    }

    .dashboard-empty {
        padding: 18px;
        border: 1px dashed #d7e2e9;
        border-radius: 14px;
        background: #fbfdfe;
        color: #7b90a0;
        line-height: 1.6;
    }

    @media (max-width: 1199px) {
        .dashboard-metrics {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .dashboard-hero {
            flex-direction: column;
        }

        .dashboard-hero-aside {
            min-width: 0;
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-shortcuts {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .dashboard-hero {
            padding: 22px 20px;
        }

        .dashboard-hero-copy h1 {
            font-size: 28px;
        }

        .dashboard-hero-aside,
        .dashboard-metrics {
            grid-template-columns: 1fr;
        }

        .dashboard-list-item {
            grid-template-columns: auto 1fr;
        }

        .dashboard-list-meta {
            grid-column: 2;
        }
    }
</style>
