<?php

use yii\db\Migration;

/**
 * Class m200917_065737_alter_tbl_client_chat_add_new_fields
 */
class m200917_065737_alter_tbl_client_chat_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


        $this->dropForeignKey('FK-cch_language_id', '{{%client_chat}}');

        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables

        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        // Alter the encoding of each table

        $db->createCommand("ALTER TABLE `client_chat` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")->execute();
        $db->createCommand("ALTER TABLE `language` MODIFY `language_id` VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")->execute();

        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();

        $this->addForeignKey('FK-cch_language_id', '{{%client_chat}}', ['cch_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');

        $this->addColumn('{{%client_chat}}', 'cch_source_type_id', $this->tinyInteger(1));
        $this->addColumn('{{%client_chat}}', 'cch_missed', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat}}', 'cch_source_type_id');
        $this->dropColumn('{{%client_chat}}', 'cch_missed');
    }
}
