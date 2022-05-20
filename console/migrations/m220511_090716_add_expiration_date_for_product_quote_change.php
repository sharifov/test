<?php

use yii\db\Migration;

/**
 * Class m220511_090716_add_expiration_date_for_product_quote_change
 */
class m220511_090716_add_expiration_date_for_product_quote_change extends Migration
{
    private const TABLE = '{{%product_quote_change%}}';
    private const COLUM = 'pqc_expiration_dt';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            self::TABLE,
            self::COLUM,
            $this->dateTime()->defaultValue(null)->after('pqc_decision_dt')
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
