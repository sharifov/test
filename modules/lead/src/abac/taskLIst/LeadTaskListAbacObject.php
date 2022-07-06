<?php

namespace modules\lead\src\abac\taskLIst;

use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;

/**
 * Class LeadTaskListAbacObject
 */
class LeadTaskListAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'task_list/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** PERMISSIONS */
    public const ASSIGN_TASK  = self::NS . 'assign_task';

    /** ACTIONS */
    public const ACTION_ACCESS  = 'access';

    public const ATTR_LEAD_PROJECT_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'projectId',
        'field' => 'projectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
    ];

    public const ATTR_LEAD_STATUS_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'statusId',
        'field' => 'statusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
    ];

    public const ATTR_LEAD_HAS_LEAD_OBJECT_SEGMENT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'hasActiveLeadObjectSegment',
        'field' => 'hasActiveLeadObjectSegment',
        'label' => 'Has Active LeadObjectSegment',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2],
    ];

    public static function getObjectList(): array
    {
        return [
            self::ASSIGN_TASK => self::ASSIGN_TASK,
        ];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::ASSIGN_TASK => [self::ACTION_ACCESS],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        $attrLeadProject = self::ATTR_LEAD_PROJECT_ID;
        $attrLeadProject['values'] = Project::getList();
        $attrStatus = self::ATTR_LEAD_STATUS_ID;
        $attrStatus['values'] = Lead::getAllStatuses();

        $attributeList = [
            self::ASSIGN_TASK => [
                LeadAbacObject::ATTR_LEAD_IS_OWNER,
                LeadAbacObject::ATTR_LEAD_HAS_OWNER,
                LeadAbacObject::ATTR_IS_IN_PROJECT,
                LeadAbacObject::ATTR_IS_IN_DEPARTMENT,
                self::ATTR_LEAD_HAS_LEAD_OBJECT_SEGMENT,
            ],
        ];

        $attributeList[self::ASSIGN_TASK][] = $attrLeadProject;
        $attributeList[self::ASSIGN_TASK][] = $attrStatus;

        return $attributeList;
    }
}
