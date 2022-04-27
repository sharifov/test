<?php

use common\models\Setting;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use yii\db\Migration;

/**
 * Class m211102_124144_correction_setting_active_quote_change_statuses
 */
class m211102_124144_correction_setting_active_quote_change_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::findOne(['s_key' => 'active_quote_change_statuses']);
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
        echo "m211102_124144_correction_setting_active_quote_change_statuses cannot be reverted.\n";
    }
}
