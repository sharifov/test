<?php

namespace modules\lead\src\abac\services;

use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\UserProfile;
use modules\lead\src\abac\dto\LeadExpertCallAbacDto;
use src\helpers\communication\StatisticsHelper;
use src\model\call\useCase\createCall\fromLead\AbacCallFromNumberList;
use src\model\email\useCase\send\fromLead\AbacEmailList;
use src\model\sms\useCase\send\fromLead\AbacSmsFromNumberList;
use src\repositories\quote\QuoteRepository;

class AbacLeadExpertCallService
{
    private LeadExpertCallAbacDto $dto;

    public function __construct(Lead $lead, Employee $user)
    {
        $this->init($lead, $user);
    }

    public function getLeadExpertCallDto(): LeadExpertCallAbacDto
    {
        return $this->dto;
    }

    private function init(Lead $lead, Employee $user)
    {
        $leadStatus = $lead->status;
        $hasFlightSegment = (bool)$lead->leadFlightSegmentsCount;

        $quoteRepository = \Yii::createObject(QuoteRepository::class);

        $quoteCount = $quoteRepository->getAmountQuoteByLeadIdAndStatusesAndCreateTypes(
            $lead->id,
            [Quote::STATUS_SENT, Quote::STATUS_DECLINED, Quote::STATUS_OPENED],
            [Quote::CREATE_TYPE_AUTO, Quote::CREATE_TYPE_AUTO_SELECT, Quote::CREATE_TYPE_SMART_SEARCH]
        );

        $statistics = (new StatisticsHelper($lead->id, StatisticsHelper::TYPE_LEAD))
            ->setCountAll();
        $smsCount = $statistics->smsCount;
        $emailCount = $statistics->emailCount;
        $callCount = $statistics->callCount;

        $call_type = UserProfile::find()->select('up_call_type_id')->where(['up_user_id' => $user->id])->one();
        $canMakeCall = $call_type && $call_type->up_call_type_id && (new AbacCallFromNumberList($user, $lead))->canMakeCall();
        $canSendSms = (new AbacSmsFromNumberList($user, $lead))->canSendSms();
        $canSendEmail = (new AbacEmailList($user, $lead))->canSendEmail();

        $this->dto = new LeadExpertCallAbacDto(
            $leadStatus,
            $hasFlightSegment,
            $quoteCount,
            $smsCount,
            $callCount,
            $emailCount,
            $canMakeCall,
            $canSendEmail,
            $canSendSms
        );
    }
}
