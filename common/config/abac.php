<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'cacheEnable' => true,
    'modules' => [
        'app' => \modules\abac\src\object\AppAbac::class,
        'order' => \modules\order\src\abac\OrderAbacObject::class,
        'case' => \modules\cases\src\abac\CasesAbacObject::class,
        'case-update' => \modules\cases\src\abac\update\UpdateAbacObject::class,
        'case-sale-list' => \modules\cases\src\abac\saleList\SaleListAbacObject::class,
        'case-communication' => \modules\cases\src\abac\communicationBlock\CaseCommunicationBlockAbacObject::class,
        'lead' => \modules\lead\src\abac\LeadAbacObject::class,
        'lead-communication' => \modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject::class,
        'email' => \modules\email\src\abac\EmailAbacObject::class,
        'qa-task' => \modules\qaTask\src\abac\QaTaskAbacObject::class,
        'client-chat' => \src\model\clientChat\entity\abac\ClientChatAbacObject::class,
        'client' => \src\model\client\abac\ClientAbacObject::class,
        'product-quote' => \modules\product\src\abac\ProductQuoteAbacObject::class,
        'call' => \src\model\call\abac\CallAbacObject::class,
        'user-flow-widget' => \frontend\widgets\frontendWidgetList\userflow\abac\UserFlowWidgetObject::class,
        'product-quote-change' => \modules\product\src\abac\ProductQuoteChangeAbacObject::class,
        'product-quote-refund' => \modules\product\src\abac\ProductQuoteRefundAbacObject::class,
        'related-product-quote' => \modules\product\src\abac\RelatedProductQuoteAbacObject::class,
        'notification' => \modules\notification\src\abac\NotificationAbacObject::class,
        'leadData' => \src\model\leadData\abac\LeadDataAbacObject::class,
        'leadTaskList' => \modules\lead\src\abac\taskLIst\LeadTaskListAbacObject::class,
        'user' => \modules\user\src\abac\UserAbacObject::class,
        'lead-rating' => \src\model\leadUserRating\abac\LeadUserRatingAbacObject::class,
        'lead-poor-processing' => \src\model\leadPoorProcessingData\abac\LeadPoorProcessingAbacObject::class,
        'quote' => \src\model\quote\abac\QuoteFlightAbacObject::class,
        'lead-user-conversion' => \src\model\leadUserConversion\abac\LeadUserConversionAbacObject::class,
        'user-feedback' => \modules\user\userFeedback\abac\UserFeedbackAbacObject::class,
        'shift' => \modules\shiftSchedule\src\abac\ShiftAbacObject::class,
        'lead-queue-business' => \modules\lead\src\abac\queue\LeadQueueBusinessInboxAbacObject::class,
        'phoneNumberRedial' => \src\model\phoneNumberRedial\abac\PhoneNumberRedialAbacObject::class,
        'case-sale-search' => \modules\cases\src\abac\saleSearch\CaseSaleSearchAbacObject::class,
        'lead-search' => \modules\lead\src\abac\LeadSearchAbacObject::class,
        'lead-expert-call' => \modules\lead\src\abac\LeadExpertCallObject::class,
        'task-list' => \modules\taskList\abac\TaskListAbacObject::class,
        'two-factor' => \src\useCase\login\twoFactorAuth\abac\TwoFactorAuthAbacObject::class,
        'lead-sale' => modules\lead\src\abac\sale\LeadSaleAbacObject::class,
        'business-extra-queue' => \modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacObject::class
    ],
    'scanDirs' => [
        '/modules/',
        '/frontend/',
        '/common/',
        '/src/',
    ],
    'scanExtMask' => ['*.php'],
];
