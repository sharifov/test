<?php

namespace modules\smartLeadDistribution\src\objects\lead;

use common\models\Lead;
use common\models\Sources;
use modules\smartLeadDistribution\src\objects\BaseLeadRatingObject;
use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;
use yii\helpers\ArrayHelper;

class LeadLeadRatingObject extends BaseLeadRatingObject implements LeadRatingObjectInterface
{
    private const NS = 'lead/';

    public const DTO = LeadLeadRatingDto::class;

    public const OPTGROUP_CALL = 'Lead';

    public const OBJ = 'lead';

    public const FIELD_SOURCE = self::OBJ . '.' . 'source_cid';
    public const FIELD_TRIP_TYPE = self::OBJ . '.' . 'trip_type';

    protected const ATTR_SOURCE = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_SOURCE,
        'field' => self::FIELD_SOURCE,
        'label' => 'Marketing Source',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_TRIP_TYPE = [
        'optgroup' => self::OPTGROUP_CALL,
        'id' => self::NS . self::FIELD_TRIP_TYPE,
        'field' => self::FIELD_TRIP_TYPE,
        'label' => 'Trip type',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];

    public const ATTRIBUTE_LIST = [
        self::ATTR_SOURCE,
        self::ATTR_TRIP_TYPE,
    ];

    public static function getAttributeList(): array
    {
        $attributeList = [];

        $s = self::ATTR_SOURCE;
        $tt = self::ATTR_TRIP_TYPE;

        $s['values'] = self::getSources();
        $tt['values'] = Lead::TRIP_TYPE_LIST;

        $attributeList[] = $s;
        $attributeList[] = $tt;

        return $attributeList;
    }

    public static function getSources(): array
    {
        $sources = Sources::find()
            ->where(['hidden' => false])
            ->orderBy(['cid' => SORT_ASC])
            ->asArray()
            ->all();

        return ArrayHelper::map($sources, 'cid', static function ($item) {
            return "{$item['cid']} - {$item['name']}";
        });
    }
}
