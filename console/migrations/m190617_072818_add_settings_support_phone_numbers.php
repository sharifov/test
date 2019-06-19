<?php

use yii\db\Migration;

/**
 * Class m190617_072818_add_settings_support_phone_numbers
 */
class m190617_072818_add_settings_support_phone_numbers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'support_phone_numbers',
            's_name' => 'Support phone number list',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'Ovago' =>  '+1​8884574490',
                'WOWFARE' =>  '+18887385190',
                'Arangrant' =>  '+18888183963',
            ]),
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
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
            'support_phone_numbers'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
