<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'cacheEnable' => true,
    'modules' => [
        'app' => \modules\abac\src\object\AppAbac::class,
        'order' => \modules\order\src\abac\OrderAbacObject::class,
        'case' => \modules\cases\src\abac\CasesAbacObject::class,
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
        'user' => \modules\user\src\abac\UserAbacObject::class,
        'lead-rating' => \src\model\leadUserRating\abac\LeadUserRatingAbacObject::class,
    ],
    'scanDirs' => [
        '/modules/',
        '/frontend/',
        '/common/',
        '/sales/',
        '/src/',
    ],
    'scanExtMask' => ['*.php'],
];
