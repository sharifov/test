<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200519_135754_create_tbl_sale_credit_card
 */
class m200519_135754_create_tbl_sale_credit_card extends Migration
{
    public $route = [
        '/sale-credit-card/*',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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

        $this->createTable('{{%sale_credit_card}}', [
            'scc_sale_id'               => $this->integer()->notNull(),
            'scc_cc_id'                 => $this->integer()->notNull(),
            'scc_created_dt'            => $this->dateTime(),
            'scc_created_user_id'       => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-sale_credit_card', '{{%sale_credit_card}}', ['scc_sale_id', 'scc_cc_id']);
        $this->addForeignKey('FK-sale_credit_card-scc_cc_id', '{{%sale_credit_card}}', ['scc_cc_id'], '{{%credit_card}}', ['cc_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-sale_credit_card-scc_created_user_id', '{{%sale_credit_card}}', ['scc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new RbacMigrationService())->up($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sale_credit_card}}');
        (new RbacMigrationService())->down($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
