<?php

use common\models\Setting;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\db\Migration;

/**
 * Class m211115_104906_correction_setting_status_confirm_list
 */
class m211115_104906_correction_setting_status_confirm_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($setting = Setting::findOne(['s_key' => 'exchange_quote_confirm_status_list'])) {
            $setting->s_value = json_encode([
                ProductQuoteStatus::NEW => 'NEW',
                ProductQuoteStatus::PENDING => 'PENDING',
                ProductQuoteStatus::IN_PROGRESS => 'IN_PROGRESS',
                ProductQuoteStatus::ERROR => 'ERROR',
            ]);
            $setting->update(false);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($setting = Setting::findOne(['s_key' => 'exchange_quote_confirm_status_list'])) {
            $setting->s_value = json_encode([
                ProductQuoteStatus::NEW => 'NEW',
                ProductQuoteStatus::PENDING => 'PENDING',
                ProductQuoteStatus::IN_PROGRESS => 'IN_PROGRESS',
                ProductQuoteStatus::APPLIED => 'APPLIED',
                ProductQuoteStatus::ERROR => 'ERROR',
            ]);
            $setting->update(false);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
