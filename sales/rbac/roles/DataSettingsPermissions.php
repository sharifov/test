<?php

namespace sales\rbac\roles;

class DataSettingsPermissions
{
    public const PERMISSIONS = [
        '/project/create', '/project/delete', '/project/index', '/project/synchronization', '/project/update', '/project/view',

        '/sources/create', '/sources/delete', '/sources/index', '/sources/set-default', '/sources/update', '/sources/view',

        '/department/create', '/department/delete', '/department/index', '/department/update', '/department/view',

        '/department-email-project/create', '/department-email-project/delete', '/department-email-project/index',
        '/department-email-project/update', '/department-email-project/view',

        '/department-phone-project/create', '/department-phone-project/delete', '/department-phone-project/index',
        '/department-phone-project/update', '/department-phone-project/view',

        '/settings/acl', '/settings/acl-rule', '/settings/airlines', '/settings/airports', '/settings/email-template',
        '/settings/logging', '/settings/project-data', '/settings/projects', '/settings/sync', '/settings/synchronization', '/settings/view-log',

        '/api-user/create', '/api-user/delete', '/api-user/index', '/api-user/update', '/api-user/view',

        '/task/create', '/task/delete', '/task/index', '/task/update', '/task/view',

        '/lead-task/create', '/lead-task/delete', '/lead-task/index', '/lead-task/update', '/lead-task/view',

        '/email-template-type/create', '/email-template-type/delete', '/email-template-type/index',
        '/email-template-type/synchronization', '/email-template-type/update', '/email-template-type/view',

        '/sms-template-type/create', '/sms-template-type/delete', '/sms-template-type/index',
        '/sms-template-type/synchronization', '/sms-template-type/update', '/sms-template-type/view',

        '/case-sale/create', '/case-sale/delete', '/case-sale/index', '/case-sale/update', '/case-sale/view',

        '/case-note/create', '/case-note/delete', '/case-note/index', '/case-note/update', '/case-note/view',

        '/lead-checklist-type/index', '/lead-checklist-type/view', '/lead-checklist-type/create', '/lead-checklist-type/update', '/lead-checklist-type/delete',
        'manageLeadChecklistType',

        '/setting-category/create', '/setting-category/delete', '/setting-category/index', '/setting-category/update', '/setting-category/view',
    ];
}
