<?php

use yii\db\Migration;

/**
 * Class m201026_101411_add_fk_upn_phone_number_with_pl_id
 */
class m201026_101411_add_fk_upn_phone_number_with_pl_id extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user_personal_phone_number}}', 'upn_phone_number', $this->integer());
        $this->addForeignKey(
            'FK-user_personal_phone_number-upn_phone_number',
            '{{%user_personal_phone_number}}',
            'upn_phone_number',
            '{{%phone_list}}',
            'pl_id'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_personal_phone_number}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_personal_phone_number-upn_phone_number', '{{%user_personal_phone_number}}');
        $this->alterColumn('{{%user_personal_phone_number}}', 'upn_phone_number', $this->string(15));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_personal_phone_number}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
