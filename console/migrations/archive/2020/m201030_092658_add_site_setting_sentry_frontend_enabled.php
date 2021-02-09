<?php

use yii\db\Migration;
use common\models\Setting;

/**
 * Class m201030_092658_add_site_setting_sentry_frontend_enabled
 */
class m201030_092658_add_site_setting_sentry_frontend_enabled extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'sentry_frontend_enabled',
                's_name' => 'Sentry Frontend Enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => 1,
                's_updated_dt' => date('Y-m-d H:i:s'),
            ]
        );

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
            'sentry_frontend_enabled'
        ]]);
    }
}
