<?php

namespace modules\product\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m220512_090616_add_expiration_date_for_product_quote
 */
class m220512_090616_add_expiration_date_for_product_quote extends Migration
{
    private const TABLE = '{{%product_quote%}}';
    private const COLUM = 'pq_expiration_dt';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->hasColumn()) {
            $this->addColumn(
                self::TABLE,
                self::COLUM,
                $this->dateTime()->defaultValue(null)->after('pq_updated_dt')
            );
            Yii::$app->db->getSchema()->refreshTableSchema(self::TABLE);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, self::COLUM);
        Yii::$app->db->getSchema()->refreshTableSchema(self::TABLE);
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
