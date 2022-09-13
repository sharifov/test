<?php

namespace src\services\quote;

use common\models\Quote;
use src\auth\Auth;

class QuoteSearchCidService
{
    public static function ffIsEnable(): bool
    {
        /** @fflag FFlag::FF_KEY_SAVE_CID_FOR_QUOTES_FROM_SEARCH_ENABLE, Save cid for quote from search */
        return \Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_SAVE_CID_FOR_QUOTES_FROM_SEARCH_ENABLE);
    }

    public static function userIsCanSeeCid(Quote $quote): bool
    {
        return QuoteSearchCidService::ffIsEnable() === true && Auth::can('/quote-search-cid/index') && $quote->quoteSearchCid !== null;
    }
}
