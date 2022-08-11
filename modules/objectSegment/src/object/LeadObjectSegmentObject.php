<?php

namespace modules\objectSegment\src\object;

use common\models\Lead;
use modules\objectSegment\components\ObjectSegmentBaseModel;
use modules\objectSegment\src\contracts\ObjectSegmentObjectInterface;

class LeadObjectSegmentObject extends ObjectSegmentBaseModel implements ObjectSegmentObjectInterface
{
    protected const NS = 'lead';


    protected const OBJECT_ATTRIBUTE_LIST = [
        self::NS => [
            self::ATTR_ORIGIN,
            self::ATTR_ITINERARY_DURATION,
            self::ATTR_PAX_ADT_COUNT,
            self::ATTR_PAX_CHD_COUNT,
            self::ATTR_PAX_INF_COUNT,
            self::ATTR_CREATED_DT,
            self::ATTR_FLIGHT_SEGMENTS_COUNT
        ],
    ];

    protected const ATTR_ORIGIN = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'origin',
        'field'     => 'origin',
        'label'     => 'Origin',
        'type'      => self::ATTR_TYPE_STRING,
        'input'     => self::ATTR_INPUT_TEXT,
        'multiple'  => false,
        'operators' => [self::OP_EQUAL2]
    ];

    protected const ATTR_ITINERARY_DURATION = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'itinerary_duration',
        'field'     => 'itinerary_duration',
        'label'     => 'Itinerary Duration (hours)',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];

    public const ATTR_FLIGHT_SEGMENTS_COUNT = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'flight_segments_count',
        'field'     => 'flight_segments_count',
        'label'     => 'Flights Segments Count',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];

    public const ATTR_PAX_ADT_COUNT = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'pax_adt_count',
        'field'     => 'pax_adt_count',
        'label'     => 'Adults Count',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];
    public const ATTR_PAX_CHD_COUNT = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'pax_chd_count',
        'field'     => 'pax_chd_count',
        'label'     => 'Children Count',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];
    public const ATTR_PAX_INF_COUNT = [
        'optgroup'  => 'Flight Request',
        'id'        => self::NS . 'pax_inf_count',
        'field'     => 'pax_inf_count',
        'label'     => 'Infants Count',
        'type'      => self::ATTR_TYPE_INTEGER,
        'input'     => self::ATTR_INPUT_NUMBER,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];

    protected const ATTR_LEAD_PROJECT_NAME = [
        'optgroup'  => 'Lead Info and Preferences',
        'id'        => self::NS . 'lead_project_name',
        'field'     => 'lead_project_name',
        'label'     => 'Lead Project',
        'type'      => self::ATTR_TYPE_STRING,
        'input'     => self::ATTR_INPUT_SELECT,
        'multiple' => false,
        'values'    => [],
        'operators' => [self::OP_EQUAL, self::OP_NOT_EQUAL]
    ];

    protected const ATTR_LEAD_DEPARTMENT_NAME = [
        'optgroup'  => 'Lead Info and Preferences',
        'id'        => self::NS . 'lead_department_name',
        'field' => 'lead_department_name',
        'label' => 'Lead Department',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => false,
        'values' => [],
        'operators' => [self::OP_EQUAL, self::OP_NOT_EQUAL]
    ];

    protected const ATTR_LEAD_CABIN_TYPE = [
        'optgroup'  => 'Lead Cabin Type',
        'id'        => self::NS . 'cabin_type',
        'field' => 'cabin_type',
        'label' => 'Lead Cabin Type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => false,
        'values' => [],
        'operators' => [self::OP_EQUAL, self::OP_NOT_EQUAL]
    ];

    protected const ATTR_CREATED_DT = [
        'optgroup'  => 'Lead Info and Preferences',
        'id'        => self::NS . 'created_dt',
        'field'     => 'created_dt',
        'label'     => 'Lead Created Date',
        'type'      => self::ATTR_TYPE_DATE,
        'input'     => self::ATTR_INPUT_TEXT,
        'multiple'  => false,
        'operators' => [
            self::OP_LESS_OR_EQUAL,
            self::OP_GREATER_OR_EQUAL,
            self::OP_EQUAL,
            self::OP_GREATER,
            self::OP_LESS
        ]
    ];

    public static function getObjectAttributeList(): array
    {
        $attrList                     = self::OBJECT_ATTRIBUTE_LIST;
        $attrLeadProject              = self::ATTR_LEAD_PROJECT_NAME;
        $attrLeadProject['values']    = self::getProjectList();
        $attrList[self::NS][]         = $attrLeadProject;
        $attrLeadDepartment           = self::ATTR_LEAD_DEPARTMENT_NAME;
        $attrLeadDepartment['values'] = self::getDepartmentList();
        $attrList[self::NS][]         = $attrLeadDepartment;
        $attrLeadCabinType            = self::ATTR_LEAD_CABIN_TYPE;
        $attrLeadCabinType['values']  = Lead::CABIN_LIST;
        $attrList[self::NS][]         = $attrLeadCabinType;
        return $attrList;
    }
}
