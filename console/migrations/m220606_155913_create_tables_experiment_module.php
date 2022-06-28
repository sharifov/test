<?php

use yii\db\Migration;

/**
 * Class m220606_155913_create_tables_experiment_module
 */
class m220606_155913_create_tables_experiment_module extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableExists = $this->db->getTableSchema('{{%experiment}}', true);
        if (is_null($tableExists)) {
            $this->createTable('{{%experiment}}', [
                'ex_id' => $this->bigPrimaryKey(),
                'ex_code' => $this->string()->unique()->notNull(),
            ]);
        }
        $tableExists = $this->db->getTableSchema('{{%experiment_target}}', true);
        if (is_null($tableExists)) {
            $this->createTable('{{%experiment_target}}', [
                'ext_id' => $this->bigPrimaryKey(),
                'ext_target_id' => $this->bigInteger()->notNull(),
                'ext_target_type_id' => $this->tinyInteger()->notNull(),
                'ext_experiment_id' => $this->bigInteger()->notNull()
            ]);
            $this->addForeignKey(
                'fk-experiment_target-experiment_id',
                '{{%experiment_target}}',
                'ext_experiment_id',
                '{{%experiment}}',
                'ex_id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableExists = $this->db->getTableSchema('{{%experiment_target}}', true);
        if (!is_null($tableExists)) {
            $this->dropForeignKey('fk-experiment_target-experiment_id', '{{%experiment_target}}');
            $this->dropTable('experiment_target');
        }
        $tableExists = $this->db->getTableSchema('{{%experiment}}', true);
        if (!is_null($tableExists)) {
            $this->dropTable('{{%experiment}}');
        }
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220606_155913_create_tables_experiment_module cannot be reverted.\n";

        return false;
    }
    */
}
