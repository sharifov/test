<?php

use yii\db\Migration;

/**
 * Class m200924_140251_modify_columns_language_id_of_tbls_language_and_client_chat
 */
class m200924_082800_modify_columns_language_id_of_tbls_language_and_client_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = Yii::$app->getDb();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $this->db->createCommand('ALTER TABLE language modify language_id VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->execute();
        $this->db->createCommand('ALTER TABLE client_chat modify cch_language_id VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')->execute();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
