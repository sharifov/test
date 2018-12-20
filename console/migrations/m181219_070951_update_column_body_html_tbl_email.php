<?php

use yii\db\Migration;

/**
 * Class m181219_070951_update_column_body_html_tbl_email
 */
class m181219_070951_update_column_body_html_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        if ($this->db->driverName === 'mysql') {

            $schema = $this->getDb()->getSchema();
            $columnBase = $schema->createColumnSchemaBuilder('mediumtext');
            $columnExtension = $schema->createColumnSchemaBuilder('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            //$columnExtension->notNull();
            $type = $columnBase . ' ' . $columnExtension;
        } else {
            $type = $this->text();
        }


        $this->alterColumn('{{%email}}', 'e_email_body_html', $type);
        $this->alterColumn('{{%email}}', 'e_email_body_text', $type);
        $this->alterColumn('{{%email}}', 'e_email_subject', $this->string(255)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%email}}', 'e_email_body_html', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('{{%email}}', 'e_email_body_text', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('{{%email}}', 'e_email_subject', $this->string(255)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }


}
