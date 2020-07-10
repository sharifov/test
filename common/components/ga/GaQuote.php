<?php

namespace common\components\ga;

use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\lead\LeadHelper;
use Yii;
use yii\httpclient\Response;

/**
 * Class GaQuote
 *
 * @property string $cid
 * @property string $tid
 * @property array $postData
 */
class GaQuote /* TODO::  */
{
    private $cid; // Client ID
    private $tid; // Tracking ID
    private $postData;

    private Lead $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        GaHelper::checkSettings(GaHelper::TYPE_LEAD);

        $this->lead = $lead;

        $this->setCid()
            ->setTid()
            ->setPostData();
    }

    private function setTid(): GaQuote
    {
        if ($this->lead->project && $this->tid = $this->lead->project->ga_tracking_id) {
            return $this;
        }
        throw new \DomainException('GA Tracking ID not found.', -4);
    }

    private function setCid(): GaQuote
    {
        if (!$visitorLog = GaHelper::getLastGaClientIdByClient($this->lead->client_id)) {
            throw new \DomainException('GA Client Id not found.', -2);
        }
        $this->cid = $visitorLog->vl_ga_client_id;
        return $this;
    }

    /**
     * @return GaQuote
     */
    private function setPostData(): GaQuote
    {
        try {
            $this->postData = [
                'tid' => $this->tid,
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
            $this->postData['cd8'] = implode('-', LeadHelper::getAllIataByLead($this->lead));
            $this->postData['cd9'] = $this->lead->getFlightTypeName();
            $this->postData['cd10'] = '';
            $this->postData['cd11'] = '';
            $this->postData['cd12'] = '';
            $this->postData['cd13'] = $this->lead->source ? $this->lead->source->cid : '';
            $this->postData['cd14'] = '';
            $this->postData['cd15'] = $this->lead->uid;
            $this->postData['cm1'] = $this->lead->adults;
            $this->postData['cm2'] = $this->lead->children;
            $this->postData['cm3'] = $this->lead->infants;

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaQuote:prepareData:Throwable');
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
            AppHelper::throwableLogger($throwable, 'GaQuote:prepareData:Throwable');
        }
        return null;
    }

    /**
     * @return bool
     */
    public function checkPostData(): bool
    {
        if (empty($this->postData)) {
            throw new \RuntimeException('Post data is required.', -3);
        }
        return true;
    }
}