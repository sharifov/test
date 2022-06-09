<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220608_143358_increase_column_size_ad_subject
 */
class m220608_143358_increase_column_size_ad_subject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand('ALTER TABLE abac_doc MODIFY ad_subject VARCHAR(255)')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand('ALTER TABLE abac_doc MODIFY ad_subject VARCHAR(50)')->execute();
    }
}
