<?php

namespace sales\rbac\roles;

class LogsToolsPermissions
{
    public const PERMISSIONS = [
        '/api-log/create', '/api-log/delete', '/api-log/delete-all', '/api-log/index', '/api-log/update', '/api-log/view',
        '/stats/agent-ratings', '/stats/ajax-get-total-chart', '/stats/api-graph',
        '/log/clear', '/log/create', '/log/delete', '/log/index', '/log/view',
        '/clean/assets', '/clean/cache', '/clean/index', '/clean/runtime',
        '/setting/create', '/setting/delete', '/setting/index', '/setting/update', '/setting/view',
        '/user-site-activity/clear-logs', '/user-site-activity/create', '/user-site-activity/delete', '/user-site-activity/index',
        '/user-site-activity/report', '/user-site-activity/update', '/user-site-activity/view',
        '/global-log/ajax-view-general-lead-log', '/global-log/create', '/global-log/index', '/global-log/view',
    ];
}
