<?php

use modules\featureFlag\components\FeatureFlagComponent;

return [
    'class' => FeatureFlagComponent::class,
    'cacheEnable' => true,
//    'modules' => [
//        'order' => \modules\order\src\abac\OrderAbacObject::class,
//        'case' => \modules\cases\src\abac\CasesAbacObject::class,
//        'lead' => \modules\lead\src\abac\LeadAbacObject::class,
//        'email' => \modules\email\src\abac\EmailAbacObject::class,
//        'qa-task' => \modules\qaTask\src\abac\QaTaskAbacObject::class,
//        'client-chat' => \src\model\clientChat\entity\abac\ClientChatAbacObject::class,
//        'client' => \src\model\client\abac\ClientAbacObject::class,
//        'product-quote' => \modules\product\src\abac\ProductQuoteAbacObject::class,
//        'call' => \src\model\call\abac\CallAbacObject::class,
//        'user-flow-widget' => \frontend\widgets\frontendWidgetList\userflow\abac\UserFlowWidgetObject::class,
//        'product-quote-change' => \modules\product\src\abac\ProductQuoteChangeAbacObject::class,
//        'product-quote-refund' => \modules\product\src\abac\ProductQuoteRefundAbacObject::class,
//        'related-product-quote' => \modules\product\src\abac\RelatedProductQuoteAbacObject::class,
//        'notification' => \modules\notification\src\abac\NotificationAbacObject::class,
//        'leadData' => \src\model\leadData\abac\LeadDataAbacObject::class,
//        'user' => \modules\user\src\abac\UserAbacObject::class
//    ],
    'scanDirs' => [
        '/modules/',
        '/frontend/',
        '/console/',
        '/webapi/',
        '/common/',
        '/sales/',
        '/src/',
    ],
    'scanExtMask' => ['*.php'],
];
