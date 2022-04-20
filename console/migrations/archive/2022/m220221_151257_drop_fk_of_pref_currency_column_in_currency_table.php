<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%fk_of_pref_currency_column_in_currency}}`.
 */
class m220221_151257_drop_fk_of_pref_currency_column_in_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $existsFk = $this->checkFk('lead_preferences', 'FK-lead_preferences-pref_currency');
        if ($existsFk) {
            $this->dropForeignKey('FK-lead_preferences-pref_currency', '{{%lead_preferences}}');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    private function checkFk(string $table, string $foreignKey)
    {
        $db = Yii::$app->getDb();
        $schema = $db->createCommand('select database()')->queryScalar();

        $query = $db->createCommand(
            '
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
