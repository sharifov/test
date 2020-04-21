<?php

use yii\db\Migration;

/**
 * Class m181008_142642_airlines_alter_cabin_classes
 */
class m181008_142642_airlines_alter_cabin_classes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%airlines}}', 'cl_economy', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'cl_premium_economy', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'cl_business', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'cl_premium_business', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'cl_first', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'cl_premium_first', $this->string(255)->null());
        $this->addColumn('{{%airlines}}', 'updated_dt', $this->dateTime()->null());
        $this->createIndex('idx_airline_iata', '{{%airlines}}', ['iata']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_airline_iata', '{{%airlines}}');
        $this->dropColumn('{{%airlines}}', 'cl_economy');
        $this->dropColumn('{{%airlines}}', 'cl_premium_economy');
        $this->dropColumn('{{%airlines}}', 'cl_business');
        $this->dropColumn('{{%airlines}}', 'cl_premium_business');
        $this->dropColumn('{{%airlines}}', 'cl_first');
        $this->dropColumn('{{%airlines}}', 'cl_premium_first');
        $this->dropColumn('{{%airlines}}', 'updated_dt');

        return false;
    }
}
