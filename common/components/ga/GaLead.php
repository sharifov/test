<?php

namespace common\components\ga;

use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\lead\LeadHelper;
use Yii;
use yii\httpclient\Response;

/**
 * Class GaLead
 *
 * @property string $cid
 * @property string $tid
 * @property array $postData
 */
class GaLead
{
    private $cid; // Client ID
    private $tid; // Tracking ID
    private array $postData = [];

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

    private function setTid(): GaLead
    {
        if ($this->tid = GaHelper::getTrackingIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Tracking ID not found.', -4);
    }

    private function setCid(): GaLead
    {
        if ($this->cid = GaHelper::getClientIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Client Id not found.', -2);
    }

    /**
     * @return GaLead
     */
    private function setPostData(): GaLead
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
                'cd10' => '',
                'cd11' => '',
            ];

            $this->postData['cd3'] = self::getOriginByLead($this->lead);
            $this->postData['cd4'] = self::getDestinationByLead($this->lead);
            $this->postData['cd5'] = self::getDateDeparture($this->lead);
            $this->postData['cd6'] = self::getDateDepartureRoundTrip($this->lead);
            $this->postData['cd8'] = implode('-', LeadHelper::getIataByLead($this->lead));
            $this->postData['cd9'] = $this->lead->getFlightTypeName();

            $this->postData = GaHelper::preparePostData($this->postData, $this->lead);

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaLead:prepareData:Throwable');
            $this->postData = [];
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
            AppHelper::throwableLogger($throwable, 'GaLead:prepareData:Throwable');
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

    /**
     * @param Lead $lead
     * @return string|null
     */
    public static function getOriginByLead(Lead $lead): ?string
    {
        if ($lead->isMultiDestination()) {
            return implode(',', LeadHelper::getAllOriginsByLead($lead));
        }
        $allOrigins = LeadHelper::getAllOriginsByLead($lead);
        return current($allOrigins);
    }

    /**
     * @param Lead $lead
     * @return string
     */
    public static function getDestinationByLead(Lead $lead): string
    {
        if ($lead->isMultiDestination()) {
            return implode(',', LeadHelper::getAllDestinationByLead($lead));
        }
        if ($firstSegment = $lead->getFirstFlightSegment()) {
            if ($destination = $firstSegment->destination) {
                return $destination;
            }
        }
        return '';
    }

    /**
     * @param Lead $lead
     * @return string
     */
    private static function getDateDepartureRoundTrip(Lead $lead): string
    {
        if ($lead->isRoundTrip() && $lastSegment = $lead->getLastFlightSegment()) {
            if ($lastDateDeparture = date('Y-m-d', strtotime($lastSegment->departure))) {
                return $lastDateDeparture;
            }
        }
        return '';
    }

    /**
     * @param Lead $lead
     * @return string
     */
    private static function getDateDeparture(Lead $lead): string
    {
        if ($firstSegment = $lead->getFirstFlightSegment()) {
            if ($dateDeparture = date('Y-m-d', strtotime($firstSegment->departure))) {
                return $dateDeparture;
            }
        }
        return '';
    }

    /**
     * @return array
     */
    public function getPostData(): array
    {
        return $this->postData;
    }
}