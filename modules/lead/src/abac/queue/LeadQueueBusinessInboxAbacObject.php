<?php

namespace modules\lead\src\abac\queue;

use common\models\Lead;
use common\models\Project;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;

/**
 * Class LeadQueueBusinessInboxAbacObject
 */
class LeadQueueBusinessInboxAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'queue/business_inbox/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** UI PERMISSION */
    public const UI_QUEUE_COLUMN  = self::NS . 'ui/queue_column';
    public const UI_BUTTON_VIEW  = self::NS . 'ui/button/view';

    /** QUERY PERMISSIONS */
    public const QUERY_LISTING = self::NS . 'query/listing';

    /** ACTIONS */
    public const ACTION_READ = 'read';
    public const ACTION_COLUMN_DEPART = 'column_depart';
    public const ACTION_COLUMN_SEGMENTS = 'column_segments';
    public const ACTION_COLUMN_LOCATION = 'column_location';
    public const ACTION_COLUMN_SOURCE_ID = 'column_source_id';
    public const ACTION_COLUMN_REQUEST_IP = 'column_request_ip';
    public const ACTION_COLUMN_CLIENT_TIME = 'column_client_time';
    public const ACTION_COLUMN_PENDING_TIME = 'column_pending_time';
    public const ACTION_COLUMN_LEAD_ID = 'column_lead_id';
    public const ACTION_READ_WT_USER_RESTRICTION = 'read_wt_user_restriction';

    public const ATTR_LEAD_PROJECT_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'project_id',
        'field' => 'project_id',
        'label' => 'Project',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
    ];

    public const ATTR_LEAD_STATUS_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'status_id',
        'field' => 'status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' => [self::OP_IN, self::OP_NOT_IN],
    ];

    public static function getObjectList(): array
    {
        return [
            self::UI_BUTTON_VIEW => self::UI_BUTTON_VIEW,
            self::UI_QUEUE_COLUMN => self::UI_QUEUE_COLUMN,
            self::QUERY_LISTING => self::QUERY_LISTING,
        ];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::UI_QUEUE_COLUMN => [
                self::ACTION_COLUMN_DEPART,
                self::ACTION_COLUMN_SEGMENTS,
                self::ACTION_COLUMN_LOCATION,
                self::ACTION_COLUMN_SOURCE_ID,
                self::ACTION_COLUMN_REQUEST_IP,
                self::ACTION_COLUMN_CLIENT_TIME,
                self::ACTION_COLUMN_PENDING_TIME,
                self::ACTION_COLUMN_LEAD_ID,
            ],
            self::UI_BUTTON_VIEW => [self::ACTION_READ],
            self::QUERY_LISTING => [self::ACTION_READ_WT_USER_RESTRICTION],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        $attrLeadProject = self::ATTR_LEAD_PROJECT_ID;
        $attrLeadProject['values'] = Project::getList();
        $attrStatus = self::ATTR_LEAD_STATUS_ID;
        $attrStatus['values'] = Lead::getAllStatuses();

        $attributeList = [
            self::UI_BUTTON_VIEW => [
                LeadAbacObject::ATTR_LEAD_IS_OWNER,
                LeadAbacObject::ATTR_LEAD_HAS_OWNER,
                LeadAbacObject::ATTR_IS_IN_PROJECT,
                LeadAbacObject::ATTR_IS_IN_DEPARTMENT,
            ],
        ];

        $attributeList[self::UI_BUTTON_VIEW][] = $attrLeadProject;
        $attributeList[self::UI_BUTTON_VIEW][] = $attrStatus;

        return $attributeList;
    }
}
