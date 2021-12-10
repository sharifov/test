<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211210_111930_alter_tbl_abac_doc_modify_column_ad_object
 */
class m211210_111930_alter_tbl_abac_doc_modify_column_ad_object extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%abac_doc}}', 'ad_object', $this->string(100));
        $this->alterColumn('{{%abac_doc}}', 'ad_action', $this->string(100));
        $this->alterColumn('{{%abac_doc}}', 'ad_description', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%abac_doc}}', 'ad_object', $this->string(50));
        $this->alterColumn('{{%abac_doc}}', 'ad_action', $this->string(50));
        $this->alterColumn('{{%abac_doc}}', 'ad_description', $this->string(50));
    }
}
