<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191104_125603_rename_setting_name_for_qcall_count_last_dialed_leads
 */
class m191104_125603_rename_setting_name_for_qcall_count_last_dialed_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Setting::updateAll(['s_name' => 'Qcall count last dialed leads'], ['s_key' => 'qcall_count_last_dialed_leads']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
