<?php

namespace src\model\leadPoorProcessingData\abac;

use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use yii\helpers\ArrayHelper;

/**
 * Class LeadPoorProcessingAbacObject
 */
class LeadPoorProcessingAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead/poor_processing/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** RULE PERMISSION */
    public const OBJ_PERMISSION_RULE = self::NS . 'rule';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::OBJ_PERMISSION_RULE => self::OBJ_PERMISSION_RULE,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_NO_ACTION = 'no_action';
    public const ACTION_LAST_ACTION = 'last_action';
    public const ACTION_EXPERT_IDLE = 'expert_idle';
    public const ACTION_SEND_SMS_OFFER = 'send_sms_offer';
    public const ACTION_SCHEDULED_COMMUNICATION = 'scheduled_communication';
    public const ACTION_EXTRA_TO_PROCESSING_TAKE = 'extra_to_processing_take';
    public const ACTION_EXTRA_TO_PROCESSING_MULTIPLE = 'extra_to_processing_multiple_update';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJ_PERMISSION_RULE => [
            self::ACTION_NO_ACTION,
            self::ACTION_LAST_ACTION,
            self::ACTION_EXPERT_IDLE,
            self::ACTION_SEND_SMS_OFFER,
            self::ACTION_SCHEDULED_COMMUNICATION,
            self::ACTION_EXTRA_TO_PROCESSING_TAKE,
            self::ACTION_EXTRA_TO_PROCESSING_MULTIPLE,
        ],
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
    protected const ATTR_IS_IN_DEPARTMENT = [
        'optgroup' => 'User',
        'id' => self::NS . 'isInDepartment',
        'field' => 'isInDepartment',
        'label' => 'Has Access to Lead`s Department',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];
    protected const ATTR_IS_IN_PROJECT = [
        'optgroup' => 'User',
        'id' => self::NS . 'isInProject',
        'field' => 'isInProject',
        'label' => 'Has Access to Lead`s Project',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
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

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_PERMISSION_RULE => [],
    ];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
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
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadStatus;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadProject;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadCabin;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadIsTest;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadCallStatus;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadType;
        $attributeList[self::OBJ_PERMISSION_RULE][] = self::ATTR_IS_IN_PROJECT;
        $attributeList[self::OBJ_PERMISSION_RULE][] = self::ATTR_IS_IN_DEPARTMENT;
        $attributeList[self::OBJ_PERMISSION_RULE][] = self::ATTR_LEAD_CREATED;
        $attributeList[self::OBJ_PERMISSION_RULE][] = self::ATTR_LEAD_HAS_FLIGHT_DETAILS;
        $attributeList[self::OBJ_PERMISSION_RULE][] = $attrLeadIsClone;

        return $attributeList;
    }
}
