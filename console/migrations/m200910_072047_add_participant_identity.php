<?php

use yii\db\Migration;

/**
 * Class m200910_072047_add_participant_identity
 */
class m200910_072047_add_participant_identity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference_participant}}', 'cp_identity', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%conference_participant}}', 'cp_identity');
    }
}
