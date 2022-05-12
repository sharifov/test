<?php

use yii\db\Migration;

/**
 * Class m220512_090716_remove_expiration_date_from_product_quote_change
 */
class m220512_090716_remove_expiration_date_from_product_quote_change extends Migration
{
    private const TABLE = '{{%product_quote_change%}}';
    private const COLUM = 'pqc_expiration_dt';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->hasColumn()) {
            $this->dropColumn(self::TABLE, self::COLUM);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return null;
    }

    /**
     * @return bool
     */
    private function hasColumn(): bool
    {
        $tableSchema = $this->db->getTableSchema(self::TABLE, true);
        return !is_null($tableSchema) && in_array(self::COLUM, $tableSchema->getColumnNames());
    }
}
