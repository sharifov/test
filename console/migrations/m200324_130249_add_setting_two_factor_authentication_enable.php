<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200324_130249_add_setting_two_factor_authentication_enable
 */
class m200324_130249_add_setting_two_factor_authentication_enable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_2fa_enable', $this->boolean()->defaultValue(0));

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_authentication_enable',
            's_name' => 'Enable two factor authentication',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
        $this->dropColumn('{{%user_profile}}', 'up_2fa_enable');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'two_factor_authentication_enable'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
