<?php

namespace common\components\ga;

use common\models\Lead;
use sales\helpers\app\AppHelper;
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
}