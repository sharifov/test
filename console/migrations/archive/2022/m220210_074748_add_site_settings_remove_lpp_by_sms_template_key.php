<?php

use yii\db\Migration;

/**
 * Class m220210_074748_add_site_settings_remove_lpp_by_sms_template_key
 */
class m220210_074748_add_site_settings_remove_lpp_by_sms_template_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'lpp_remove_by_sms_tpl',
            's_name' => 'Remove LPP by sms template on sending sms from lead communication block',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'sms_client_offer',
                'sms_client_offer_view',
                'sms_product_offer'
            ]),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'lpp_remove_by_sms_tpl',
        ]]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
