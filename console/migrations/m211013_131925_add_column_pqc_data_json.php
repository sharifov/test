<?php

use common\models\Setting;
use common\models\SettingCategory;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use yii\db\Migration;

/**
 * Class m211013_131925_add_column_pqc_data_json
 */
class m211013_131925_add_column_pqc_data_json extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_data_json', $this->json());

        $settingCategory = SettingCategory::getOrCreateByName('Product Quote Change');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_exchange_processing_status_list',
                's_name' => 'Voluntary Exchange Processing Status List',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ProductQuoteChangeStatus::PENDING => 'PENDING',
                    ProductQuoteChangeStatus::IN_PROGRESS => 'IN_PROGRESS',
                    ProductQuoteChangeStatus::ERROR => 'ERROR',
                    ProductQuoteChangeStatus::PROCESSING => 'PROCESSING',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'ProductQuoteChange Processing Status List for Voluntary Exchange processing flow.',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'voluntary_exchange_processing_status_list'
        ]]);

        $this->dropColumn('{{%product_quote_change}}', 'pqc_data_json');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
