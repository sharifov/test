<?php
namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m200224_142142_create_tbl_order_tips
 */
class m200224_142142_create_tbl_order_tips extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/order/order-tips-crud/index',
		'/order/order-tips-crud/create',
		'/order/order-tips-crud/update',
		'/order/order-tips-crud/delete',
		'/order/order-tips-crud/view',
	];

	/**
	 * @return bool|void
	 * @throws \yii\base\Exception
	 * @throws \yii\base\NotSupportedException
	 */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%order_tips}}',[
			'ot_id' => $this->primaryKey(),
			'ot_order_id' => $this->integer(),
			'ot_client_amount' => $this->decimal(8,2 ),
			'ot_amount' => $this->decimal(8, 2),
			'ot_user_profit' => $this->decimal(8,2 ),
			'ot_description' => $this->string(500),
			'ot_created_dt' => $this->dateTime()
		],$tableOptions);

		$this->addForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}', 'ot_order_id','{{%order}}', 'or_id', 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_tips}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

	/**
	 * @return bool|void
	 * @throws \yii\base\NotSupportedException
	 */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}');
    	$this->dropTable('{{%order_tips}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_tips}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
