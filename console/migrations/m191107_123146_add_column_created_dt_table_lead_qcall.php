<?php

use common\models\LeadQcall;
use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191107_123146_add_column_created_dt_table_lead_qcall
 */
class m191107_123146_add_column_created_dt_table_lead_qcall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_qcall}}', 'lqc_created_dt', $this->dateTime());

//        foreach (LeadQcall::find()->all() as $item) {
//            $item->lqc_created_dt = $item->lqc_dt_from;
//            $item->save();
//        }

        $this->insert('{{%setting}}', [
            's_key' => 'redial_fresh_time',
            's_name' => 'Redial fresh time',
            's_type' => Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_redial_default_take_limit',
        ]]);


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_qcall}}', 'lqc_created_dt');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'redial_fresh_time',
        ]]);

        $this->insert('{{%setting}}', [
            's_key' => 'enable_redial_default_take_limit',
            's_name' => 'Enable Redial Default take limit',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
