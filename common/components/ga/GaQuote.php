<?php

namespace common\components\ga;

use common\models\Lead;
use common\models\Quote;
use sales\helpers\app\AppHelper;
use Yii;
use yii\httpclient\Response;

/**
 * Class GaQuote
 *
 * @property string $cid
 * @property string $tid
 * @property array $postData
 */
class GaQuote /* TODO:: add interface */
{
    private $cid; // Client ID
    private $tid; // Tracking ID
    private array $postData = [];

    private Quote $quote;
    private Lead $lead;

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        GaHelper::checkSettings(GaHelper::TYPE_QUOTE);

        $this->quote = $quote;
        $this->lead = $quote->lead;

        $this->setCid()
            ->setTid()
            ->setPostData();
    }

    private function setTid(): GaQuote
    {
        if ($this->tid = GaHelper::getTrackingIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Tracking ID not found.', -4);
    }

    private function setCid(): GaQuote
    {
        if ($this->cid = GaHelper::getClientIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Client Id not found.', -2);
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
                'el' => 'mail-sent',
                'cd1' => $this->cid,
                'cd2' => '',
            ];

            $this->postData['cd10'] = implode(',', $this->getOperatingAirlines());
            $this->postData['cd11'] = implode(',', $this->getMarketingAirlines());

            $this->postData = GaHelper::preparePostData($this->postData, $this->lead);

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaQuote:prepareData:Throwable');
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

    /**
     * @return array
     */
    private function getOperatingAirlines(): array   /* TODO:: test is */
    {
        $result = [];
        foreach ($this->quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_operating_airline)) {
                    continue;
                }
                $result[$segment->qs_operating_airline] = $segment->qs_operating_airline;
            }
        }
        return $result;
    }

    private function getMarketingAirlines(): array   /* TODO:: test is */
    {
        $result = [];
        foreach ($this->quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_marketing_airline)) {
                    continue;
                }
                $result[$segment->qs_marketing_airline] = $segment->qs_marketing_airline;
            }
        }
        return $result;
    }
}