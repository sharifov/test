<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220621_070902_add_site_settings_email_component
 */
class m220621_070902_add_site_settings_email_component extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'email_component',
                's_name' => 'Data for email component',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'email_from' => 'no-reply@travel-dev.com',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'email_component',
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
