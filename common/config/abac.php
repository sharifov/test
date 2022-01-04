<?php

use modules\abac\components\AbacComponent;

return [
    'class' => AbacComponent::class,
    'cacheEnable' => true,
    'modules' => [
        'order' => \modules\order\src\abac\OrderAbacObject::class,
        'case' => \modules\cases\src\abac\CasesAbacObject::class,
        'lead' => \modules\lead\src\abac\LeadAbacObject::class,
        'email' => \modules\email\src\abac\EmailAbacObject::class,
        'qa-task' => \modules\qaTask\src\abac\QaTaskAbacObject::class,
        'client-chat' => \sales\model\clientChat\entity\abac\ClientChatAbacObject::class,
        'client' => \sales\model\client\abac\ClientAbacObject::class,
        'product-quote' => \modules\product\src\abac\ProductQuoteAbacObject::class,
        'call' => \sales\model\call\abac\CallAbacObject::class,
        'user-flow-widget' => \frontend\widgets\frontendWidgetList\userflow\abac\UserFlowWidgetObject::class,
        'product-quote-change' => \modules\product\src\abac\ProductQuoteChangeAbacObject::class,
        'product-quote-refund' => \modules\product\src\abac\ProductQuoteRefundAbacObject::class,
        'related-product-quote' => \modules\product\src\abac\RelatedProductQuoteAbacObject::class,
        'notification' => \modules\notification\src\abac\NotificationAbacObject::class,
        'leadData' => \sales\model\leadData\abac\LeadDataAbacObject::class,
    ],
    'scanDirs' => [
        '/modules/',
        '/frontend/',
        '/common/',
        '/sales/',
    ],
    'scanExtMask' => ['*.php'],
];
