<?php

namespace src\forms\siteSetting;

use yii\base\Model;

/**
 *
 */
class PriceResearchLinkForm extends Model
{
    public const CHILDREN_PARAMETER_TYPE_QUANTITATIVE = 'quantitative';
    public const CHILDREN_PARAMETER_TYPE_ENUMERABLE   = 'enumerable';


    public const CHILDREN_PARAMETER_TYPES = [
        self::CHILDREN_PARAMETER_TYPE_QUANTITATIVE,
        self::CHILDREN_PARAMETER_TYPE_ENUMERABLE
    ];

    public $name;
    public $enabled;

    public $oneTripUrl;
    public $roundTripUrl;
    public $multiCityUrl;
    public $multiCityItineraryPattern;


    public $dateFormat;
    public $multiCityDateFormat;
    public $cabinClassMappings;

    public $childrenParameterType;
    public $childrenSubQueryPart;
    public $childrenParameterSeparator;

    public $childPaxTypeEnumerableParameter;
    public $infantPaxTypeEnumerableParameter;


    public function rules()
    {

        return [
            [['name'], 'string', 'length' => [1, 100]],
            [['enabled'], 'boolean'],

            [['oneTripUrl'], 'string', 'length' => [5, 1000]],
            [['roundTripUrl'], 'string', 'length' => [5, 1000]],
            [['multiCityUrl'], 'string', 'length' => [5, 1000]],
            [['multiCityItineraryPattern'], 'string', 'length' => [5, 1000]],

            [['dateFormat'], 'string', 'length' => [1, 100]],
            [['multiCityDateFormat'], 'string', 'length' => [1, 100]],

            [['cabinClassMappings'], 'isArray', 'message' => 'Cabin Class should be array'],

            [['childrenParameterType'], 'in', 'range' => self::CHILDREN_PARAMETER_TYPES],
            [['childrenParameterSeparator'], 'string', 'length' => [1, 1000]],
            [['childrenSubQueryPart'], 'string', 'length' => [5, 1000]],

            [['childPaxTypeEnumerableParameter'], 'string', 'length' => [1, 100]],
            [['infantPaxTypeEnumerableParameter'], 'string', 'length' => [1, 100]],

            [
                [
                    'name',
                    'oneTripUrl',
                    'roundTripUrl',
                    'multiCityUrl',
                    //'multiCityItineraryPattern',
                    'enabled',
                    'dateFormat',
                    'cabinClassMappings',
                    'childrenParameterType',
                    'childrenSubQueryPart'
                ],
                'required'
            ],


            [
                [
                    'childrenParameterSeparator',
                    'childPaxTypeEnumerableParameter',
                    'infantPaxTypeEnumerableParameter'
                ],
                'required',
                'when' => function (PriceResearchLinkForm $model) {
                    return $model->childrenParameterType == self::CHILDREN_PARAMETER_TYPE_ENUMERABLE;
                }
            ],
        ];
    }

    public function isArray($attribute, $value)
    {
        return is_array($value);
    }

    public function formName()
    {
        return '';
    }
}
