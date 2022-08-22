<?php

namespace modules\objectTask\src\scenarios\statements;

use common\models\Lead;
use common\models\Project;
use src\forms\leadflow\FollowUpReasonForm;
use src\forms\leadflow\ProcessingReasonForm;
use src\forms\leadflow\RejectReasonForm;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;

class NoAnswerObject extends BaseObject
{
    private const NS = 'noAnswer/';

    public const DTO = NoAnswerDto::class;

    public const OPTGROUP_CALL = 'No Answer';

    public const OBJ = 'lead';

    public const FIELD_PROJECT = self::OBJ . '.' . 'project';
    public const FIELD_STATUS = self::OBJ . '.' . 'status';
    public const FIELD_REASON = self::OBJ . '.' . 'reason';
    public const FIELD_CABIN = self::OBJ . '.' . 'cabin';

    protected const ATTR_PROJECT = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_PROJECT,
        'field' => self::FIELD_PROJECT,
        'label' => 'Project',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
    ];

    protected const ATTR_STATUS = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_STATUS,
        'field' => self::FIELD_STATUS,
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
    ];

    protected const ATTR_REASON = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_REASON,
        'field' => self::FIELD_REASON,
        'label' => 'Reason',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
    ];

    protected const ATTR_CABIN = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_CABIN,
        'field' => self::FIELD_CABIN,
        'label' => 'Cabin',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN],
    ];

    public const ATTRIBUTE_LIST = [
        self::ATTR_REASON,
        self::ATTR_STATUS,
    ];

    public static function getAttributes(): array
    {
        $attributes = [];

        foreach (static::ATTRIBUTE_LIST as $item) {
            $attributes[$item['field']] = $item['label'];
        }

        return $attributes;
    }

    public static function getAttributeList(): array
    {
        $attributeList = [];

        $p = self::ATTR_PROJECT;
        $s = self::ATTR_STATUS;
        $r = self::ATTR_REASON;
        $c = self::ATTR_CABIN;

        $p['values'] = Project::getList();
        $s['values'] = Lead::STATUS_LIST;
        $c['values'] = Lead::CABIN_LIST;

        $statusReasonList = [
            Lead::STATUS_PROCESSING => ProcessingReasonForm::REASON_LIST,
            Lead::STATUS_FOLLOW_UP => FollowUpReasonForm::REASON_LIST,
            Lead::STATUS_REJECT => RejectReasonForm::REASON_LIST,
            Lead::STATUS_CLOSED => LeadStatusReasonQuery::getList('lsr_name'),
        ];

        foreach ($statusReasonList as $status => $reasons) {
            foreach ($reasons as $reason) {
                $r['values'][] = [
                    'value' => $reason,
                    'label' => $reason,
                    'optgroup' => Lead::STATUS_LIST[$status]
                ];
            }
        }

        $attributeList[] = $p;
        $attributeList[] = $s;
        $attributeList[] = $r;
        $attributeList[] = $c;

        return $attributeList;
    }
}
