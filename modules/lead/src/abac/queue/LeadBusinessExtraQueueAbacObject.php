<?php

namespace modules\lead\src\abac\queue;

use common\models\Department;
use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;
use yii\helpers\ArrayHelper;

/**
 * Class LeadBusinessExtraQueueAbacObject
 */
class LeadBusinessExtraQueueAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'business_extra_queue/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** UI PERMISSION */
    public const UI_ACCESS  = self::NS . 'ui/access';
    public const PROCESS_ACCESS  = self::NS . 'process/access';

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_TAKE = 'take';
    public const ACTION_PROCESS = 'process';

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACTION_PROCESS => [],
    ];

    /** --------------- ATTRIBUTES --------------------------- */
    public const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadStatusId',
        'field' => 'leadStatusId',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];
    protected const ATTR_LEAD_PROJECT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadProjectId',
        'field' => 'leadProjectId',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];
    protected const ATTR_LEAD_CABIN = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadCabin',
        'field' => 'leadCabin',
        'label' => 'Cabin',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];
    protected const ATTR_LEAD_IS_TEST = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadIsTest',
        'field' => 'leadIsTest',
        'label' => 'Lead Is Test',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];
    protected const ATTR_LEAD_CALL_STATUS_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadCallStatusId',
        'field' => 'leadCallStatusId',
        'label' => 'Call Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];
    protected const ATTR_LEAD_TYPE_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadTypeId',
        'field' => 'leadTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];
    protected const ATTR_LEAD_CREATED = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadCreated',
        'field' => 'leadCreated',
        'label' => 'Lead created Data Time',
        'type' => self::ATTR_TYPE_DATE,
        'input' => self::ATTR_INPUT_TEXT,
        'placeholder' => 'yyyy-mm-dd hh:mm:ss',
        'operators' =>  [self::OP_GREATER, self::OP_LESS]
    ];
    protected const ATTR_LEAD_IS_CLONE = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadIsClone',
        'field' => 'leadIsClone',
        'label' => 'Lead Is Clone',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];
    protected const ATTR_LEAD_HAS_FLIGHT_DETAILS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadHasFlightDetails',
        'field' => 'leadHasFlightDetails',
        'label' => 'Lead Has Flight Details',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' =>  ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];
    public const ATTR_LEAD_DEPARTMENT_NAME = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadDepartment',
        'field' => 'department_name',
        'label' => 'Department',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' => [self::OP_IN],
    ];

    public static function getObjectList(): array
    {
        return [
            self::UI_ACCESS => self::UI_ACCESS,
            self::PROCESS_ACCESS => self::PROCESS_ACCESS,
        ];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::UI_ACCESS => [self::ACTION_ACCESS, self::ACTION_TAKE],
            self::PROCESS_ACCESS => [self::ACTION_PROCESS],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        $attrLeadStatus = self::ATTR_LEAD_STATUS;
        $attrLeadStatus['values'] = Lead::getAllStatuses();
        $attrLeadProject = self::ATTR_LEAD_PROJECT;
        $attrLeadProject['values'] = Project::getList();
        $attrLeadCabin = self::ATTR_LEAD_CABIN;
        $attrLeadCabin['values'] = Lead::CABIN_LIST;
        $attrLeadIsTest = self::ATTR_LEAD_IS_TEST;
        $attrLeadIsTest['values'] = ['true' => 'Yes', 'false' => 'No'];
        $attrLeadCallStatus = self::ATTR_LEAD_CALL_STATUS_ID;
        $attrLeadCallStatus['values'] = Lead::CALL_STATUS_LIST;
        $attrLeadType = self::ATTR_LEAD_TYPE_ID;
        $attrLeadType['values'] = ArrayHelper::merge(Lead::TYPE_LIST, [Lead::TYPE_BASIC => 'Basic']);
        $attrLeadIsClone = self::ATTR_LEAD_IS_CLONE;
        $attrLeadIsClone['values'] = ['true' => 'Yes', 'false' => 'No'];

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadStatus;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadProject;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadCabin;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadIsTest;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadCallStatus;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadType;
        $attributeList[self::PROCESS_ACCESS][] = self::ATTR_LEAD_CREATED;
        $attributeList[self::PROCESS_ACCESS][] = self::ATTR_LEAD_HAS_FLIGHT_DETAILS;
        $attrLeadDepartmentName = self::ATTR_LEAD_DEPARTMENT_NAME;
        $departmentNames = Department::getList();
        $attrLeadDepartmentName['values'] = array_combine($departmentNames, $departmentNames);
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadDepartmentName;
        $attributeList[self::PROCESS_ACCESS][] = $attrLeadIsClone;

        return $attributeList;
    }
}
