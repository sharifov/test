<?php

use yii\db\Migration;

/**
 * Class m200522_051111_add_new_site_settings_for_manage_sale_ticket_email_generating
 */
class m200522_051111_add_new_site_settings_for_manage_sale_ticket_email_generating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'case_sale_ticket_email_data',
			's_name' => 'Data for generating email with sale tickets info',
			's_type' => \common\models\Setting::TYPE_ARRAY,
			's_value' => json_encode([
				'sendTo' =>  [
					'refunds@techork.com',
					'nodupe@techork.com'
				],
				'subject' =>  'Refund Request - [bookingId] - [originalFop]',
			]),
			's_updated_dt' => date('Y-m-d H:i:s'),
		]);

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
			'case_sale_ticket_email_data'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

}
