<?php

namespace frontend\widgets;

use Yii;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Project;
use yii\base\Widget;

/**
 * Class LeadRedialWidget
 *
 * @property Lead $lead
 * @property string $phoneFrom
 * @property string $phoneTo
 */
class LeadRedialWidget extends Widget
{

    public $lead;

    public $phoneFrom;

    public $phoneTo;

    public $redialAutoTakeSeconds;

    public function init(): void
    {
        parent::init();
        if (!$this->lead instanceof Lead) {
            throw new \InvalidArgumentException('lead property must be Lead');
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('lead_redial', [
            'lead' => $this->lead,
            'phoneFrom' => $this->findPhoneFrom(),
            'phoneTo' => $this->findPhoneTo(),
            'projectId' => $this->findProjectId(),
            'redialAutoTakeSeconds' => $this->findRedialAutoTakeSeconds(),
        ]);
    }

    /**
     * @return int
     */
    private function findRedialAutoTakeSeconds(): int
    {
        return Yii::$app->params['redial_auto_take_seconds'] ?? 10;
    }

    /**
     * @return string
     */
    private function findPhoneFrom(): string
    {
        if ($this->phoneFrom) {
            return $this->phoneFrom;
        }
        if ($phone = Project::findOne($this->lead->project_id)) {
            if ($phone->contactInfo->phone) {
                return $phone->contactInfo->phone;
            }
        }
        throw new \DomainException('Not found phoneFrom for LeadId: ' . $this->lead->id);
    }

    /**
     * @return string
     */
    private function findPhoneTo(): string
    {
        if ($this->phoneTo) {
            return $this->phoneTo;
        }
        if ($this->lead->client) {
            /** @var ClientPhone $phone */
            $phone = $this->lead->client->getClientPhones()->
                andWhere(['or',
                    ['type' => [ClientPhone::PHONE_FAVORITE, ClientPhone::PHONE_VALID, ClientPhone::PHONE_NOT_SET]],
                    ['IS', 'type', NULL]
                ])
                ->orderBy(['type' => SORT_DESC])->asArray()->limit(1)->one();
            if ($phone) {
                return $phone['phone'];
            }
        }
        throw new \DomainException('Not found phoneTo for LeadId: ' . $this->lead->id);
    }

    /**
     * @return int
     */
    private function findProjectId(): int
    {
        if ($this->lead->project_id) {
            return $this->lead->project_id;
        }
        throw new \DomainException('Not found projectId for LeadId: ' . $this->lead->id);
    }

}
