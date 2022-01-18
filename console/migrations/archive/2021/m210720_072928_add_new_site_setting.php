<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210720_072928_add_new_site_setting
 */
class m210720_072928_add_new_site_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'limit_user_connection',
                's_name' => 'Limit user connection',
                's_description' => 'The maximum number of tabs that the user can open in the browser',
                's_type' => Setting::TYPE_INT,
                's_value' => 10,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
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
            'limit_user_connection',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
