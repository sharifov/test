<?php

namespace modules\lead\src\abac\communicationBlock;

use common\models\Department;
use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;

class LeadCommunicationBlockAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'communication_block';

    public const ACTION_VIEW = 'view';
    public const ACTION_SEND_SMS = 'sendSms';
    public const ACTION_SEND_EMAIL = 'sendEmail';
    public const ACTION_MAKE_CALL = 'makeCall';

    private const ATTR_PHONE_FROM_PERSONAL = [
        'optgroup' => 'Lead',
        'id' => self::NS . '/phone_from_personal',
        'field' => 'phone_from_personal',
        'label' => 'Phone From Personal',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    private const ATTR_PHONE_FROM_GENERAL = [
        'optgroup' => 'Lead',
        'id' => self::NS . '/phone_from_general',
        'field' => 'phone_from_general',
        'label' => 'Phone From General',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
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
        'operators' =>  [self::OP_EQUAL2]
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
                LeadAbacObject::ATTR_LEAD_IS_OWNER,
                LeadAbacObject::ATTR_LEAD_HAS_OWNER,
                LeadAbacObject::ATTR_CLIENT_IS_EXCLUDED,
                self::ATTR_PHONE_FROM_PERSONAL,
                self::ATTR_PHONE_FROM_GENERAL,
                self::ATTR_PROJECT_SMS_ENABLE,
            ],
        ];

        $leadStatuses = Lead::getAllStatuses();

        $attrLeadStatusName = LeadAbacObject::ATTR_LEAD_STATUS_NAME;
        $attrLeadStatusName['values'] = array_combine($leadStatuses, $leadStatuses);
        $attributes[self::NS][] = $attrLeadStatusName;

        $attrLeadProjectName = LeadAbacObject::ATTR_LEAD_PROJECT_NAME;
        $projectNames = Project::getList();
        $attrLeadProjectName['values'] = array_combine($projectNames, $projectNames);
        $attributes[self::NS][] = $attrLeadProjectName;

        $attrLeadDepartmentName = LeadAbacObject::ATTR_LEAD_DEPARTMENT_NAME;
        $departmentNames = Department::getList();
        $attrLeadDepartmentName['values'] = array_combine($departmentNames, $departmentNames);
        $attributes[self::NS][] = $attrLeadDepartmentName;

        return $attributes;
    }
}
