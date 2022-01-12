<?php

namespace sales\rbac\roles;

class ExchangeSenior
{
    public const EXCLUDE_PERMISSIONS = [
        '/call/auto-redial',
        '/lead-qcall/create', '/lead-qcall/delete', '/lead-qcall/index', '/lead-qcall/list', '/lead-qcall/update', '/lead-qcall/view',
        '/qcall-config/create', '/qcall-config/delete', '/qcall-config/index', '/qcall-config/update', '/qcall-config/view',
        '/project-weight/create', '/project-weight/delete', '/project-weight/index', '/project-weight/update', '/project-weight/view',
        '/status-weight/create', '/status-weight/delete', '/status-weight/index', '/status-weight/update', '/status-weight/view',
        '/leads/export',
        '/report/agents',
        '/kpi/details', '/kpi/index',
        '/user-group-set/create', '/user-group-set/delete', '/user-group-set/index', '/user-group-set/update', '/user-group-set/view',
    ];

    public static function getExcludePermissions(): array
    {
        return array_merge(
            self::EXCLUDE_PERMISSIONS,
            DataSettingsPermissions::PERMISSIONS,
            NewDataPermissions::PERMISSIONS,
            LogsToolsPermissions::PERMISSIONS
        );
    }
}
