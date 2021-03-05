<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210305_100809_add_columns_to_rent_car_quote
 */
class m210305_100809_add_columns_to_rent_car_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rent_car_quote}}', 'rcq_contract_request_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_contract_request_json');
    }
}
