<?php

use common\models\Setting;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use yii\db\Migration;

/**
 * Class m211027_122115_update_product_quote_change_status
 */
class m211027_122115_update_product_quote_change_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::findOne(['s_key' => 'voluntary_exchange_processing_status_list']);
        if ($setting) {
            $setting->s_value = \frontend\helpers\JsonHelper::encode([
                ProductQuoteChangeStatus::PENDING => 'PENDING',
                ProductQuoteChangeStatus::IN_PROGRESS => 'IN_PROGRESS',
                ProductQuoteChangeStatus::ERROR => 'ERROR',
                ProductQuoteChangeStatus::PROCESSING => 'PROCESSING',
            ]);
            $setting->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211027_122630_update_product_quote_change_status1 cannot be reverted.\n";
    }
}
