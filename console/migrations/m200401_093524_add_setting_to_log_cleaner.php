<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200401_093524_add_setting_to_log_cleaner
 */
class m200401_093524_add_setting_to_log_cleaner extends Migration
{
     /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = new SettingCategory();
        $settingCategory->sc_name = 'Console';
        $settingCategory->save();

        $this->insert('{{%setting}}', [
            's_key' => 'console_log_cleaner_enable',
            's_name' => 'Console log cleaner enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'console_log_cleaner_params',
            's_name' => 'Console log params',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode(
                [
                    'days' => 90,
                    'limitIteration' => 2000,
                ],
                JSON_THROW_ON_ERROR
            ),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'console_call_terminator_enable',
            's_name' => 'Console call terminator enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'console_call_terminator_params',
            's_name' => 'Console call terminator params',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode(
                [
                    'ringing_minutes' => 5,
                    'queue_minutes' => 60,
                    'in_progress_minutes' => 90,
                ],JSON_THROW_ON_ERROR),
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->createIndex('IND-call_c_status_id-c_created_dt', '{{%call}}', ['c_status_id', 'c_created_dt']);

        $this->alterColumn('{{%global_log}}', 'gl_id', $this->bigInteger() . ' NOT NULL AUTO_INCREMENT');
        $this->alterColumn('{{%api_log}}', 'al_id', $this->bigInteger() . ' NOT NULL AUTO_INCREMENT');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'console_log_cleaner_enable', 'console_log_cleaner_params',
            'console_call_terminator_enable', 'console_call_terminator_params',
        ]]);

        $this->dropIndex('IND-call_c_status_id-c_created_dt', '{{%call}}');

        $this->alterColumn('{{%global_log}}', 'gl_id', $this->integer(11) . ' NOT NULL AUTO_INCREMENT');
        $this->alterColumn('{{%api_log}}', 'al_id', $this->integer(11) . ' NOT NULL AUTO_INCREMENT');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }
}
