<?php

use yii\db\Migration;

/**
 * Class m200117_071005_alter_table_case_sale_drop_trigger
 */
class m200117_071005_alter_table_case_sale_drop_trigger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$db = Yii::$app->getDb();

		$db->createCommand('DROP TRIGGER if exists case_sale_BEFORE_INSERT')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
