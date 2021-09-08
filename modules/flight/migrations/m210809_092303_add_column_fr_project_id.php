<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210809_092303_add_column_fr_project_id
 */
class m210809_092303_add_column_fr_project_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_request}}', 'fr_project_id', $this->integer());
        $this->createIndex('IND-flight_request-fr_project_id', '{{%flight_request}}', ['fr_project_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-flight_request-fr_project_id', '{{%flight_request}}');
        $this->dropColumn('{{%flight_request}}', 'fr_project_id');
    }
}
