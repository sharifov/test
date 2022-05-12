<?php

use yii\db\Migration;

/**
 * Class m220511_090734_add_expiration_date_for_product_quote_refund
 */
class m220511_090734_add_expiration_date_for_product_quote_refund extends Migration
{
    private const TABLE = '{{%product_quote_refund%}}';
    private const COLUM = 'pqr_expiration_dt';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            self::TABLE,
            self::COLUM,
            $this->dateTime()->defaultValue(null)->after('pqr_updated_dt')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, self::COLUM);
    }
}
