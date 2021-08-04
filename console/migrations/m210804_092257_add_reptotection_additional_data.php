<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210804_092257_add_reptotection_additional_data
 */
class m210804_092257_add_reptotection_additional_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'is_automate', $this->boolean()->defaultValue(false));
        $this->createIndex('IND-cases-is_automate', '{{%cases}}', ['is_automate']);

        $settingCategory = SettingCategory::getOrCreateByName('Enable');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'enable_order_from_sale',
                's_name' => 'Enable Import Order from Sale',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'this setting enables/disables import of sales into orders, on the case/view page when adding sales, as well as in the console script - sync/sales',
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
        $this->dropIndex('IND-cases-is_automate', '{{%cases}}');
        $this->dropColumn('{{%cases}}', 'is_automate');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_order_from_sale',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
