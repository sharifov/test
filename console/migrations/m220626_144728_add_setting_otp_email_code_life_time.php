<?php

use yii\db\Migration;

/**
 * Class m220626_144728_add_setting_otp_email_code_life_time
 */
class m220626_144728_add_setting_otp_email_code_life_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'otp_email_code_life_time',
                's_name' => 'Otp Email Code lifetime in seconds',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 180,
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
            'otp_email_code_life_time',
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
