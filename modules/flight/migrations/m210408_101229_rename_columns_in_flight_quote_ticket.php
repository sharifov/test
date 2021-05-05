<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210408_101229_rename_columns_in_flight_quote_ticket
 */
class m210408_101229_rename_columns_in_flight_quote_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%flight_quote_ticket}}', 'fqf_created_dt', 'fqt_created_dt');
        $this->renameColumn('{{%flight_quote_ticket}}', 'fqf_updated_dt', 'fqt_updated_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%flight_quote_ticket}}', 'fqt_created_dt', 'fqf_created_dt');
        $this->renameColumn('{{%flight_quote_ticket}}', 'fqt_updated_dt', 'fqf_updated_dt');
    }
}
