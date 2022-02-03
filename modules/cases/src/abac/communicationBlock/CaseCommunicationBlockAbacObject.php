<?php

namespace modules\cases\src\abac\communicationBlock;

use common\models\Department;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\cases\src\abac\CasesAbacObject;
use src\entities\cases\CasesStatus;

class CaseCommunicationBlockAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = CasesAbacObject::NS . 'communication_block';

    public const ACTION_VIEW = 'view';
    public const ACTION_SEND_SMS = 'sendSms';
    public const ACTION_SEND_EMAIL = 'sendEmail';
    public const ACTION_MAKE_CALL = 'makeCall';

    private const ATTR_CALL_FROM_PERSONAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/call_from_personal',
        'field' => 'call_from_personal',
        'label' => 'Call From Personal',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_CALL_FROM_GENERAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/call_from_general',
        'field' => 'call_from_general',
        'label' => 'Call From General',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_SMS_FROM_PERSONAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/sms_from_personal',
        'field' => 'sms_from_personal',
        'label' => 'Sms From Personal',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_SMS_FROM_GENERAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/sms_from_general',
        'field' => 'sms_from_general',
        'label' => 'Sms From General',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_EMAIL_FROM_PERSONAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/email_from_personal',
        'field' => 'email_from_personal',
        'label' => 'Email From Personal',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_EMAIL_FROM_GENERAL = [
        'optgroup' => 'CASE',
        'id' => self::NS . '/email_from_general',
        'field' => 'email_from_general',
        'label' => 'Email From General',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    private const ATTR_PROJECT_SMS_ENABLE = [
        'optgroup' => 'Project',
        'id' => self::NS . '/project_sms_enable',
        'field' => 'project_sms_enable',
        'label' => 'Project SMS enable',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2],
    ];


    public static function getObjectList(): array
    {
        return [self::NS => self::NS];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::NS => [
                self::ACTION_VIEW,
                self::ACTION_SEND_SMS,
                self::ACTION_SEND_EMAIL,
                self::ACTION_MAKE_CALL,
            ],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        $attributes = [
            self::NS => [
                CasesAbacObject::ATTR_CASE_IS_OWNER,
                CasesAbacObject::ATTR_CASE_HAS_OWNER,
                CasesAbacObject::ATTR_CLIENT_IS_EXCLUDED,
                CasesAbacObject::ATTR_CLIENT_IS_UNSUBSCRIBE,
                self::ATTR_CALL_FROM_PERSONAL,
                self::ATTR_CALL_FROM_GENERAL,
                self::ATTR_SMS_FROM_PERSONAL,
                self::ATTR_SMS_FROM_GENERAL,
                self::ATTR_EMAIL_FROM_PERSONAL,
                self::ATTR_EMAIL_FROM_GENERAL,
                self::ATTR_PROJECT_SMS_ENABLE,
            ],
        ];

        $caseStatuses = CasesStatus::STATUS_LIST;
        $attrCaseStatusName = CasesAbacObject::ATTR_CASE_STATUS_NAME;
        $attrCaseStatusName['values'] = array_combine($caseStatuses, $caseStatuses);
        $attributes[self::NS][] = $attrCaseStatusName;

        $attrCaseProjectName = CasesAbacObject::ATTR_CASE_PROJECT_NAME;
        $projectNames = Project::getList();
        $attrCaseProjectName['values'] = array_combine($projectNames, $projectNames);
        $attributes[self::NS][] = $attrCaseProjectName;

        $attrCaseDepartmentName = CasesAbacObject::ATTR_CASE_DEPARTMENT_NAME;
        $departmentNames = Department::getList();
        $attrCaseDepartmentName['values'] = array_combine($departmentNames, $departmentNames);
        $attributes[self::NS][] = $attrCaseDepartmentName;

        return $attributes;
    }
}
