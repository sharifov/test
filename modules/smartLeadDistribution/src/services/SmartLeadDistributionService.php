<?php

namespace modules\smartLeadDistribution\src\services;

use common\models\Employee;
use common\models\Lead;
use common\models\UserProfile;
use modules\featureFlag\FFlag;
use modules\smartLeadDistribution\src\entities\LeadRatingParameter;
use modules\smartLeadDistribution\src\entities\LeadRatingProcessingLog;
use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\access\ConditionExpressionService;
use src\auth\Auth;
use src\helpers\ErrorsToStringHelper;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\repositories\lead\LeadBadgesRepository;
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

    /**
     * @param Employee|null $employee
     * @return null|array{from: int, to: int}
     */
    public static function getAllowedPointIntervalForBusinessInbox(?Employee $employee = null): ?array
    {
        $setting = Yii::$app->params['settings']['smart_lead_distribution_by_agent_skill_and_points'];
        $employee = $employee ?? Auth::user();
        $employeeSkillID = $employee->userProfile->up_skill;
        $allowedPoints = null;

        if (isset($setting['business']) && !empty($employeeSkillID)) {
            $leadBadgeRepository = new LeadBadgesRepository();
            $leadsBySkill = $leadBadgeRepository->countBusinessLeadsByAgentSkill();

            if ((int)$employeeSkillID === UserProfile::SKILL_TYPE_JUNIOR && $leadsBySkill[$employeeSkillID] === 0) {
                $employeeSkillID = UserProfile::SKILL_TYPE_MIDDLE;
            }

            /** @var array{from: int, to: int} $pointInterval */
            foreach ($setting['business'] as $skillID => $pointInterval) {
                if ((int)$employeeSkillID === (int)$skillID) {
                    $allowedPoints = $pointInterval;

                    break;
                }
            }
        }

        return $allowedPoints;
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

    public static function getCategoryByPoints(int $points): ?int
    {
        $categories = Yii::$app->params['settings']['smart_lead_distribution_rating_categories'];

        foreach ($categories as $index => $category) {
            if ($points >= $category['points']['from'] && $points <= $category['points']['to']) {
                return $index;
            }
        }

        return null;
    }

    public static function recalculateRatingForLead(Lead $lead): bool
    {
        $points = SmartLeadDistributionService::countPoints($lead);
        $category = SmartLeadDistributionService::getCategoryByPoints($points);
        $needUpdate = false;

        $oldValuePoints = LeadData::find()
            ->where([
                'ld_lead_id' => $lead->id,
                'ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS_DYNAMIC,
            ])
            ->limit(1)
            ->one();

        if ($oldValuePoints === null || (int)$oldValuePoints->ld_field_value !== $points) {
            $needUpdate = true;
        }

        $oldValueCategory = LeadData::find()
            ->where([
                'ld_lead_id' => $lead->id,
                'ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY_DYNAMIC,
            ])
            ->limit(1)
            ->one();

        if ($oldValueCategory === null || (int)$oldValueCategory->ld_field_value !== $category) {
            $needUpdate = true;
        }

        if ($needUpdate === true) {
            try {
                $leadDataRepository = \Yii::createObject(LeadDataRepository::class);

                if ($oldValuePoints !== null) {
                    $oldValuePoints->ld_field_value = (string)$points;
                    $leadDataPointsDynamic = $oldValuePoints;
                } else {
                    $leadDataPointsDynamic = LeadData::create($lead->id, LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS_DYNAMIC, $points);
                }

                $leadDataRepository->save($leadDataPointsDynamic);

                if ($category !== null) {
                    if ($oldValueCategory !== null) {
                        $oldValueCategory->ld_field_value = (string)$category;
                        $leadDataCategoryDynamic = $oldValueCategory;
                    } else {
                        $leadDataCategoryDynamic = LeadData::create($lead->id, LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY_DYNAMIC, (string)$category);
                    }

                    $leadDataRepository->save($leadDataCategoryDynamic);
                }

                return true;
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'leadId' => $lead->id,
                    'points' => $points,
                    'category' => $category,
                ], 'SmartLeadDistributionService::recalculateRatingForLead');
            }
        }

        return false;
    }

    public static function getCategoriesAsIdName(): array
    {
        $setting = Yii::$app->params['settings']['smart_lead_distribution_rating_categories'];
        $data = [];

        if (!empty($setting)) {
            foreach ($setting as $id => $category) {
                $data[$id] = $category['name'];
            }
        }

        return $data;
    }

    public static function getCategoryById(int $id): ?array
    {
        $setting = Yii::$app->params['settings']['smart_lead_distribution_rating_categories'];

        return $setting[$id] ?? null;
    }

    public static function ffIsEnable(): bool
    {
        /** @fflag FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE, Smart Lead Distribution Enable */
        return Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE);
    }
}
