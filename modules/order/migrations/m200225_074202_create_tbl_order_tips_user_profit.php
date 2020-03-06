<?php
namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m200225_074202_create_tbl_order_tips_user_profit
 */
class m200225_074202_create_tbl_order_tips_user_profit extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/order/order-tips-user-profit-crud/index',
		'/order/order-tips-user-profit-crud/create',
		'/order/order-tips-user-profit-crud/update',
		'/order/order-tips-user-profit-crud/delete',
		'/order/order-tips-user-profit-crud/view',
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%order_tips_user_profit}}', [
			'otup_order_id' => $this->integer()->notNull(),
			'otup_user_id' => $this->integer()->notNull(),
			'otup_percent' => $this->tinyInteger()->notNull(),
			'otup_amount' => $this->decimal(8, 2),
			'otup_created_dt' => $this->dateTime(),
			'otup_updated_dt' => $this->dateTime(),
			'otup_created_user_id' => $this->integer(),
			'otup_updated_user_id' => $this->integer()
		], $tableOptions);

		$this->addPrimaryKey('pk-order_tips_user_profit-otup_order_id-otup_user_id', '{{%order_tips_user_profit}}', ['otup_order_id', 'otup_user_id']);

		$this->addForeignKey('fk-order_tips_user_profit-otup_order_id', '{{%order_tips_user_profit}}', 'otup_order_id', '{{%order}}', 'or_id');

		$this->addForeignKey('fk-order_tips_user_profit-otup_user_id', '{{%order_tips_user_profit}}', 'otup_user_id', '{{%employees}}', 'id');

		$this->addForeignKey('fk-order_tips_user_profit-otup_created_user_id', '{{%order_tips_user_profit}}', 'otup_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->addForeignKey('fk-order_tips_user_profit-otup_updated_user_id', '{{%order_tips_user_profit}}', 'otup_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_tips_user_profit}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('fk-order_tips_user_profit-otup_order_id', '{{%order_tips_user_profit}}');
		$this->dropForeignKey('fk-order_tips_user_profit-otup_user_id', '{{%order_tips_user_profit}}');
		$this->dropForeignKey('fk-order_tips_user_profit-otup_created_user_id', '{{%order_tips_user_profit}}');
		$this->dropForeignKey('fk-order_tips_user_profit-otup_updated_user_id', '{{%order_tips_user_profit}}');

		$this->dropTable('{{%order_tips_user_profit}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_tips_user_profit}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
