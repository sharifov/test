<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220627_085925_add_site_settings_otp_email_settings_remove_otp_life_time
 */
class m220627_085925_add_site_settings_otp_email_settings_remove_otp_life_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'otp_email_code_life_time',
        ]]);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'otp_email_settings',
                's_name' => 'Otp Email Settings',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'code_life_time' => 180,
                    'project_id' => 2,
                    'template_type' => 'two_factor_auth',
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
            'otp_email_settings',
        ]]);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'otp_email_code_life_time',
                's_name' => 'Otp Email Code lifetime in seconds',
                's_type' => Setting::TYPE_INT,
                's_value' => 180,
                's_updated_dt' => date('Y-m-d H:i:s'),
            ]
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
