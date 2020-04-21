<?php

use yii\db\Migration;

/**
 * Class m190811_081538_create_tbl_department
 */
class m190811_081538_create_tbl_department extends Migration
{

    public $departments = [
        0 => ['dep_id' => 1, 'dep_key' => 'sales', 'dep_name' => 'Sales'],
        1 => ['dep_id' => 2, 'dep_key' => 'exchange', 'dep_name' => 'Exchange'],
        2 => ['dep_id' => 3, 'dep_key' => 'support', 'dep_name' => 'Support'],
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

        $this->createTable('{{%department}}', [
            'dep_id' => $this->integer()->unique(),
            'dep_key' => $this->string(20)->unique(),
            'dep_name' => $this->string(20)->notNull(),
            'dep_updated_user_id' => $this->integer(),
            'dep_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-department_dep_id', '{{%department}}', ['dep_id']);
        $this->addForeignKey('FK-department_dep_updated_user_id', '{{%department}}', ['dep_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        foreach ($this->departments as $department) {
            $department['dep_updated_dt'] = date('Y-m-d H:i:s');
            $this->insert('{{%department}}', $department);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-department_dep_updated_user_id', '{{%department}}');
        $this->dropPrimaryKey('PK-department_dep_id', '{{%department}}');
        $this->dropTable('{{%department}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
