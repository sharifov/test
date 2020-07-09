<?php

namespace common\components\ga;

use common\models\Lead;
use common\models\VisitorLog;
use sales\helpers\app\AppHelper;
use sales\helpers\lead\LeadHelper;
use Yii;
use yii\httpclient\Response;

/**
 * Class GaRequestService
 *
 * @property string $cid
 */
class GaLead
{
    private $cid;
    private Lead $lead;
    private array $postData;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;

        $this->checkSettings();

        $this->setCid()
            ->setPostData();
    }

    private function checkSettings(): void
    {
        $gaEnable = (bool) (Yii::$app->params['settings']['ga_enable'] ?? false);
        $gaCreateLead = (bool) (Yii::$app->params['settings']['ga_create_lead'] ?? false);

        if (!$gaEnable || !$gaCreateLead) {
            throw new \RuntimeException('Service disabled. Please, check GA settings.');
        }
    }

    private function setCid(): GaLead
    {
        if (!$visitorLog = VisitorLog::getLastGaClientIdByClient($this->lead->client_id)) {
            throw new \RuntimeException('Ga Client Id not found.');
        }
        $this->cid = $visitorLog->vl_ga_client_id;
        return $this;
    }

    /**
     * @return GaLead
     */
    private function setPostData(): GaLead
    {
        try {
            $this->postData = [
                'cid' => $this->cid,
                't' => 'event',
                'ec' => 'leads',
                'ea' => 'phone-to-book',
                'el' => 'crm-generated-lead',
                'cd1' => $this->cid,
                'cd2' => '',
            ];

            $firstSegment = $this->lead->getFirstFlightSegment();
            $lastSegment = $this->lead->getLastFlightSegment();

            $this->postData['cd3'] = implode(',', LeadHelper::getAllOriginsByLead($this->lead));
            $this->postData['cd4'] = implode(',', LeadHelper::getAllDestinationByLead($this->lead));
            $this->postData['cd5'] = date('Y-m-d', strtotime($firstSegment->departure));
            $this->postData['cd6'] = $this->lead->isRoundTrip() ? date('Y-m-d', strtotime($lastSegment->departure)) : '';
            $this->postData['cd7'] = $this->lead->getCabinClassName();
            $this->postData['cd8'] = /* TODO:: уточнить */ ''; //маршрут пользователя. Направления указываем через дефис. Пример: LAX-RNO-LAX
            $this->postData['cd9'] = $this->lead->getFlightTypeName();
            $this->postData['cd10'] = /* TODO:: уточнить */ ''; //operating airline. Если несколько - указываем через запятую
            $this->postData['cd11'] = /* TODO::  уточнить */ ''; // marketing airline. Если несколько - указываем через запятую
            $this->postData['cd12'] = '';
            $this->postData['cd13'] = /* TODO::  уточнить */ ''; // Lead Source CID
            $this->postData['cd14'] = '';
            $this->postData['cd15'] = $this->lead->uid;
            $this->postData['cm1'] = $this->lead->adults;
            $this->postData['cm2'] = $this->lead->children;
            $this->postData['cm3'] = $this->lead->infants;

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaLead:prepareData:Throwable');
        }
        return $this;
    }

    /**
     * @return Response|null
     */
    public function send(): ?Response
    {
        try {
            $this->checkPostData();
            return \Yii::$app->gaRequestService->sendRequest($this->postData);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaLead:prepareData:Throwable');
        }
        return null;
    }

    /**
     * @return bool
     */
    public function checkPostData(): bool
    {
        if (empty($this->postData)) {
            throw new \RuntimeException('Post data is required.');
        }
        return true;
    }
}