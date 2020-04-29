<?php

use yii\db\Migration;

/**
 * Class m200429_052325_add_columns_to_contacts
 */
class m200429_052325_add_columns_to_contacts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_type_id', $this->tinyInteger()->defaultValue(1)->comment('1 - Client, 2 - Contact'));
        $this->addColumn('{{%user_contact_list}}', 'ucl_favorite', $this->boolean()->defaultValue(false) );

        $this->createIndex('IND-clients-cl_type_id', '{{%clients}}', ['cl_type_id']);
        $this->createIndex('IND-user_contact_list-ucl_favorite', '{{%user_contact_list}}', ['ucl_favorite']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-clients-cl_type_id', '{{%clients}}');
        $this->dropIndex('IND-user_contact_list-ucl_favorite', '{{%user_contact_list}}');

        $this->dropColumn('{{%clients}}', 'cl_type_id');
        $this->dropColumn('{{%user_contact_list}}', 'ucl_favorite');
    }
}
