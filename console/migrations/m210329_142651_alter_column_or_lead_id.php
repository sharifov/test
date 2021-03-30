<?php

use yii\db\Migration;

/**
 * Class m210329_142651_alter_column_or_lead_id
 */
class m210329_142651_alter_column_or_lead_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->checkFk('order', 'FK-order-or_lead_id')) {
            $this->dropForeignKey('FK-order-or_lead_id', '{{%order}}');
        }

        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer());

        if (!$this->checkFk('order', 'FK-order-leads')) {
            $this->addForeignKey('FK-order-leads', '{{%order}}', ['or_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->checkFk('order', 'FK-order-leads')) {
            $this->dropForeignKey('FK-order-leads', '{{%order}}');
        }
        if ($this->checkFk('order', 'FK-order-or_lead_id')) {
            $this->dropForeignKey('FK-order-or_lead_id', '{{%order}}');
        }

        $this->alterColumn('{{%order}}', 'or_lead_id', $this->integer()->notNull());

        if (!$this->checkFk('order', 'FK-order-or_lead_id')) {
            $this->addForeignKey('FK-order-or_lead_id', '{{%order}}', ['or_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
        }
    }

    private function checkFk(string $table, string $foreignKey)
    {
        $db = Yii::$app->getDb();
        $schema = $db->createCommand('select database()')->queryScalar();

        $query = $db->createCommand('
            SELECT 
                COUNT(1) AS cnt 
            FROM 
                information_schema.table_constraints 
            WHERE 
                constraint_name=:foreignKey 
            AND 
                table_name=:tableName
            AND 
                constraint_schema=:schema',
            [
                ':foreignKey' => $foreignKey,
                ':tableName' => $table,
                ':schema' => $schema,
            ]
        )->queryOne();

        return (int) $query['cnt'];
    }
}
