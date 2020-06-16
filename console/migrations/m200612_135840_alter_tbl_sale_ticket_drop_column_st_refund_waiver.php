<?php

use yii\db\Migration;

/**
 * Class m200612_135840_alter_tbl_sale_ticket_drop_column_st_refund_waiver
 */
class m200612_135840_alter_tbl_sale_ticket_drop_column_st_refund_waiver extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropColumn('{{%sale_ticket}}', 'st_refund_waiver');
		$this->alterColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->string(50));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->addColumn('{{%sale_ticket}}', 'st_refund_waiver', $this->string(50));
		$this->addColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->decimal(8,2));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
