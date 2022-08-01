<?php

namespace modules\smartLeadDistribution\src\services;

use common\models\Employee;
use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\smartLeadDistribution\abac\dto\SmartLeadDistributionAbacDto;
use modules\smartLeadDistribution\abac\SmartLeadDistributionAbacObject;
use modules\smartLeadDistribution\src\entities\LeadRatingParameter;
use modules\smartLeadDistribution\src\entities\LeadRatingProcessingLog;
use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\access\ConditionExpressionService;
use src\auth\Auth;
use src\helpers\ErrorsToStringHelper;
use Yii;

class SmartLeadDistributionService
{
    public static function addParameter(
        string $object,
        string $attribute,
        int $points,
        string $conditionJson
    ): void {
        $model = new LeadRatingParameter();
        $model->lrp_object = $object;
        $model->lrp_attribute = $attribute;
        $model->lrp_point = $points;
        $model->lrp_condition_json = $conditionJson;

        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
    }

    public static function deleteParameter(
        string $object,
        string $attribute,
        int $points,
    ): int {
        return LeadRatingParameter::deleteAll([
            'lrp_object' => $object,
            'lrp_attribute' => $attribute,
            'lrp_point' => $points
        ]);
    }

    public static function getAllowedCategories(?Employee $employee = null): array
    {
        $employee = $employee ?? Auth::user();
        $allowedCategory = [];
        $dto = new SmartLeadDistributionAbacDto();

        /** @abac SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_FIRST_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, Access to first category business lead */
        if (Yii::$app->abac->can($dto, SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_FIRST_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, $employee)) {
            $allowedCategory[] = SmartLeadDistribution::CATEGORY_FIRST;
        }

        /** @abac SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_FIRST_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, Access to second category business lead */
        if (Yii::$app->abac->can($dto, SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_SECOND_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, $employee)) {
            $allowedCategory[] = SmartLeadDistribution::CATEGORY_SECOND;
        }

        /** @abac SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_FIRST_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, Access to third category business lead */
        if (Yii::$app->abac->can($dto, SmartLeadDistributionAbacObject::QUERY_BUSINESS_LEAD_THIRD_CATEGORY, SmartLeadDistributionAbacObject::ACTION_ACCESS, $employee)) {
            $allowedCategory[] = SmartLeadDistribution::CATEGORY_THIRD;
        }

        return $allowedCategory;
    }

    /**
     * @param string $objectName
     * @return LeadRatingObjectInterface|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getByName(string $objectName): ?LeadRatingObjectInterface
    {
        $obj = null;

        if (isset(SmartLeadDistribution::OBJ_CLASS_LIST[$objectName])) {
            $obj = \Yii::createObject(SmartLeadDistribution::OBJ_CLASS_LIST[$objectName]);
        }

        return $obj;
    }

    public static function getAttributesByObject(?string $objectName = null): array
    {
        $list = [];

        if (!empty($objectName)) {
            $object = self::getByName($objectName);
            if ($object) {
                $list = $object::getAttributes();
            }
        }

        return $list;
    }

    public static function getDataForField(?string $objectName = null, ?string $attribute = null): array
    {
        $data = [];

        if (!empty($objectName) && !empty($attribute)) {
            $object = self::getByName($objectName);

            if ($object) {
                $data[] = $object::getDataForField($attribute);
            }
        }

        return $data;
    }

    private static function leadRatingProcessing(Lead $lead, ?callable $callback = null): int
    {
        $leadRatingParameters = LeadRatingParameter::find()->all();
        $points = 0;

        foreach ($leadRatingParameters as $leadRatingParameter) {
            $obj = self::getByName($leadRatingParameter->lrp_object);

            if ($obj === null) {
                continue;
            }

            $dto = Yii::createObject($obj::getDTO(), [
                'lead' => $lead
            ]);

            $bool = ConditionExpressionService::isValidCondition(
                $leadRatingParameter->lrp_condition,
                [$obj::OBJ => $dto]
            );

            if ($bool === true) {
                $points += $leadRatingParameter->lrp_point;
            }

            if ($callback !== null) {
                $callback($leadRatingParameter, $dto, $bool);
            }
        }

        return $points;
    }

    public static function countPoints(Lead $lead): int
    {
        return self::leadRatingProcessing($lead);
    }

    /**
     * @return array{points: integer, log:array, category: integer}
     */
    public static function countPointsWithExtraData(Lead $lead): array
    {
        $result = [
            'points' => 0,
            'category' => 3,
            'log' => []
        ];

        $result['points'] = self::leadRatingProcessing($lead, function (LeadRatingParameter $leadRatingParameter, $dto, $bool) use (&$result) {
            if ($bool === true) {
                $result['log'][] = new LeadRatingProcessingLog($leadRatingParameter, $dto);
            }
        });

        $result['category'] = self::getCategoryByPoints($result['points']);

        return $result;
    }

    public static function getCategoryByPoints(int $points): int
    {
        foreach (SmartLeadDistribution::CATEGORY_TOTAL_SCORE_LIST as $index => $category) {
            if ($points > $category['from'] && $points < $category['to']) {
                return $index;
            }
        }

        return SmartLeadDistribution::CATEGORY_THIRD;
    }

    public static function ffIsEnable(): bool
    {
        /** @fflag FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE, Smart Lead Distribution Enable */
        return Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE);
    }
}
