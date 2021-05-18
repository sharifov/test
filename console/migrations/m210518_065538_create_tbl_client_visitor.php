<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use yii\db\Migration;

/**
 * Class m210518_065538_create_tbl_client_visitor
 */
class m210518_065538_create_tbl_client_visitor extends Migration
{
    private $routes = [
        '/client-visitor-crud/view',
        '/client-visitor-crud/index',
        '/client-visitor-crud/create',
        '/client-visitor-crud/updated',
        '/client-visitor-crud/delete',
    ];

    private $roles = [
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

        $this->createTable('{{%client_visitor}}', [
            'cv_id' => $this->primaryKey(),
            'cv_client_id' => $this->integer()->notNull(),
            'cv_visitor_id' => $this->string(50)->notNull(),
            'cv_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-client_visitor-cv_visitor_id', '{{%client_visitor}}', ['cv_visitor_id']);
        $this->addForeignKey(
            'FK-client_visitor-cv_client_id',
            '{{%client_visitor}}',
            ['cv_client_id'],
            '{{%clients}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $importData = ClientChatVisitor::find()
            ->select(['ccv_client_id', 'cvd_visitor_rc_id'])
            ->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id')
            ->where(['IS NOT', 'ccv_client_id', null])
            ->distinct()
            ->asArray()
            ->all();

        Yii::$app->db->createCommand()->batchInsert('{{%client_visitor}}', ['cv_client_id', 'cv_visitor_id'], $importData)->execute();

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_visitor-cv_client_id', '{{%client_visitor}}');

        $this->dropTable('{{%client_visitor}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
