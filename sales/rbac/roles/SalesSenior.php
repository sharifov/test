<?php

namespace sales\rbac\roles;

class SalesSenior
{
    public const EXCLUDE_PERMISSIONS = [
        '/call/auto-redial',

        '/qcall-config/create', '/qcall-config/delete', '/qcall-config/index', '/qcall-config/update', '/qcall-config/view',

        '/project-weight/create', '/project-weight/delete', '/project-weight/index', '/project-weight/update', '/project-weight/view',

        '/status-weight/create', '/status-weight/delete', '/status-weight/index', '/status-weight/update', '/status-weight/view',

        '/user-group-set/create', '/user-group-set/delete', '/user-group-set/index', '/user-group-set/update', '/user-group-set/view',

        '/leads/export',

        '/currency/create', '/currency/delete', '/currency/index', '/currency/synchronization', '/currency/update', '/currency/view',
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
