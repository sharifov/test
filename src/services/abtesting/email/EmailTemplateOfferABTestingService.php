<?php

namespace src\services\abtesting\email;

use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Lead;
use common\models\Project;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\services\abtesting\ABTestingBaseEntity;
use src\services\abtesting\ABTestingBaseService;
use yii\helpers\ArrayHelper;

class EmailTemplateOfferABTestingService extends ABTestingBaseService
{
    private const EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_KEY = 'email_template_offer_ab_testing_template_id_%s_project_id_%s';
    private const LEAD_EMAIL_OFFER_TEMPLATE_CACHE_KEY     = 'lead_email_offer_template_ab_testing_lead_id_%s';

    private const EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_DURATION = 60 * 60 * 24 * 365;
    private const LEAD_EMAIL_OFFER_TEMPLATE_CACHE_DURATION     = 60 * 60 * 24 * 15;


    private ?Project $project;

    public function assignEmailOfferTemplateToLead(Lead $lead): ?int
    {
        try {
            $cacheKey   = sprintf(self::LEAD_EMAIL_OFFER_TEMPLATE_CACHE_KEY, $lead->id);
            $cacheValue = \Yii::$app->cache->get($cacheKey);
            if ($cacheValue) {
                return $cacheValue;
            }
            $this->project = $lead->project;
            if (empty($this->project)) {
                throw new \DomainException('Lead has no project assigned');
            }
            /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates value */
            $settingsValue            = \Yii::$app->ff->val(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES);
            if (!is_array($settingsValue)) {
                throw new \DomainException('Params must be array');
            }
            if (empty($settingsValue['projects']) || !is_array($settingsValue['projects'])) {
                throw new \DomainException('Projects params must be array');
            }
            $projectsParams = $settingsValue['projects'];
            if (empty($projectsParams[$this->project->project_key]) || !is_array($projectsParams[$this->project->project_key])) {
                if (empty($projectsParams['*']) || !is_array($projectsParams['*'])) {
                    return null;
                }
                $expectedPercentagesArray =  $projectsParams['*'];
            } else {
                $expectedPercentagesArray = $projectsParams[$this->project->project_key];
            }
            $startingDateTime = ArrayHelper::getValue($settingsValue, 'startingDateTime');
            if ($lead->created < $startingDateTime) {
                return null;
            }
            $testingEntitiesArray = $this->populateTestingEntitiesArray($expectedPercentagesArray, $startingDateTime);
            $emailTemplateKey     = $this->getExpectedKey(...$testingEntitiesArray);
            $emailTemplateId      = self::getEmailOfferTemplateIdByKey($emailTemplateKey);
            \Yii::$app->cache->set($cacheKey, $emailTemplateId, self::LEAD_EMAIL_OFFER_TEMPLATE_CACHE_DURATION);
            return $emailTemplateId;
        } catch (\DomainException | \RuntimeException $e) {
            $message = AppHelper::throwableLog($e);
            \Yii::warning($message, 'EmailTemplateOfferABTestingService:assignEmailOfferTemplateToLead:Exception');
            return null;
        } catch (\Throwable $e) {
            $message = AppHelper::throwableLog($e);
            \Yii::error($message, 'EmailTemplateOfferABTestingService:assignEmailOfferTemplateToLead:Throwable');
            return null;
        }
    }

    /**
     * @return ABTestingBaseEntity[]
     */
    private function populateTestingEntitiesArray(array $expectedPercentagesArray, string $startingDateTime): array
    {
        $resultArray = [];
        foreach ($expectedPercentagesArray as $templateKey => $expectedPercentage) {
            $templateId = self::getEmailOfferTemplateIdByKey($templateKey);
            $cacheKey   = sprintf(self::EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_KEY, $templateId, $this->project->id);
            $counter    = \Yii::$app->cache->get($cacheKey);
            if (!$counter) {
                $counter = self::setCacheValForEmailTemplate(
                    $cacheKey,
                    $startingDateTime,
                    $templateId,
                    $this->project->id
                );
            }
            $item          = new ABTestingBaseEntity($templateKey, $expectedPercentage, $counter);
            $resultArray[] = $item;
        }
        return $resultArray;
    }

    /**
     * @param string $cacheKey
     * @param string $dateFrom
     * @param int $templateTypeId
     * @param int $projectId
     * @return int
     */
    private static function setCacheValForEmailTemplate(
        string $cacheKey,
        string $dateFrom,
        int $templateTypeId,
        int $projectId
    ): int {
        $count = self::getEmailOfferTemplatesCountFromDate($dateFrom, $templateTypeId, $projectId);
        \Yii::$app->cache->set($cacheKey, $count, self::EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_DURATION);
        return $count;
    }

    /**
     * @param string $dateFrom
     * @param int $templateTypeId
     * @param int $projectId
     * @return int
     */
    private static function getEmailOfferTemplatesCountFromDate(
        string $dateFrom,
        int $templateTypeId,
        int $projectId
    ): int {
        return Email
            ::find()
            ->where(['e_template_type_id' => $templateTypeId])
            ->andWhere(['e_project_id' => $projectId])
            ->andWhere(['e_status_id' => Email::STATUS_DONE])
            ->andWhere(['>=', 'e_status_done_dt', $dateFrom])
            ->count();
    }

    /**
     * @param string $key
     * @return int
     */
    private static function getEmailOfferTemplateIdByKey(string $key): int
    {
        $template = EmailTemplateType
            ::find()
            ->where(['etp_key' => $key])
            ->one();
        if (empty($template)) {
            throw new \DomainException('Template key not found' . $key);
        }
        return $template->etp_id;
    }

    /**
     * @param int $templateId
     * @param int $projectId
     * @return void
     */
    public static function incrementCounterByTemplateAndProjectIds(int $templateId, int $projectId): void
    {
        try {
            $cacheKey   = sprintf(self::EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_KEY, $templateId, $projectId);
            $cacheValue = \Yii::$app->cache->get($cacheKey);
            if ($cacheValue) {
                $cacheValue++;
                \Yii::$app->cache->set($cacheKey, $cacheValue, self::EMAIL_OFFER_TEMPLATES_COUNTER_CACHE_DURATION);
                return;
            }
            /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates value */
            $settingsValue            = \Yii::$app->ff->val(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES);
            if (!is_array($settingsValue)) {
                throw new \DomainException('Params must be array');
            }
            if (empty($settingsValue['projects']) || !is_array($settingsValue['projects'])) {
                throw new \DomainException('Projects params must be array');
            }
            $projectsParams = $settingsValue['projects'];
            $project = Project::findOne($projectId);
            if (empty($project)) {
                return;
            }
            if (empty($projectsParams[$project->project_key]) || !is_array($projectsParams[$project->project_key])) {
                if (empty($projectsParams['*']) || !is_array($projectsParams['*'])) {
                    return;
                }
                $expectedPercentagesArray =  $projectsParams['*'];
            } else {
                $expectedPercentagesArray = $projectsParams[$project->project_key];
            }
            $emailTemplateKey = EmailTemplateType::findOne($templateId)->etp_key;
            if (!isset($expectedPercentagesArray[$emailTemplateKey])) {
                return;
            }
            $startingDateTime = ArrayHelper::getValue($settingsValue, 'startingDateTime');
            self::setCacheValForEmailTemplate($cacheKey, $startingDateTime, $templateId, $projectId);
        } catch (\DomainException | \RuntimeException $e) {
            $message = AppHelper::throwableLog($e);
            \Yii::warning(
                $message,
                'EmailTemplateOfferABTestingService:incrementCounterByTemplateAndProjectIds:Exception'
            );
        } catch (\Throwable $e) {
            $message = AppHelper::throwableLog($e);
            \Yii::error(
                $message,
                'EmailTemplateOfferABTestingService:incrementCounterByTemplateAndProjectIds:Throwable'
            );
        }
    }

    /**
     * @return int
     */
    public function getDefaultOfferTemplateId(): int
    {
        $settingsValue            = \Yii::$app->ff->val(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES);
        if (!is_array($settingsValue)) {
            throw new \DomainException('Params must be array');
        }
        if (!isset($settingsValue['defaultOfferTemplateKey'])) {
            throw new \DomainException('Default Offer key should be set');
        }
        return $this->getEmailOfferTemplateIdByKey($settingsValue['defaultOfferTemplateKey']);
    }
}
