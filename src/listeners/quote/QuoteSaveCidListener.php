<?php

namespace src\listeners\quote;

use common\models\QuoteSearchCid;
use src\events\quote\QuoteSaveEvent;
use src\helpers\app\AppHelper;
use src\repositories\quote\QuoteSearchCidRepository;

class QuoteSaveCidListener
{
    public function handle(QuoteSaveEvent $event): void
    {
        if (empty($event->cid)) {
            return;
        }

        try {
            $model = QuoteSearchCid::create($event->quote->id, $event->cid);
            (new QuoteSearchCidRepository($model))->save();
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSaveCidListener:Throwable');
        }
    }
}
