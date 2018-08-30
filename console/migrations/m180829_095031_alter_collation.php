<?php

use yii\db\Migration;

/**
 * Class m180829_095031_alter_collation
 */
class m180829_095031_alter_collation extends Migration
{

    // ALTER DATABASE sales CHARACTER SET utf8 COLLATE utf8_unicode_ci;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT table_name FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE"', [
            ':schema' => $schema
        ])->queryAll();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        // Alter the encoding of each table
        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
            echo "tbl: ".$tableName. "\r\n";
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //echo "m180829_095031_alter_collation cannot be reverted.\n";

        return true;
    }


}
