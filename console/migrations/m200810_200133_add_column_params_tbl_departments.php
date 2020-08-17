<?php

use common\models\Department;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Class m200810_200133_add_column_params_tbl_departments
 */
class m200810_200133_add_column_params_tbl_departments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department}}', 'dep_params', $this->text());
        $this->update('{{%department}}', ['dep_params' => Json::encode(['default_phone_type' => 'Only personal'])]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%department}}', 'dep_params');
    }
}
