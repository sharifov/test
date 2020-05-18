<?php

use yii\db\Migration;

/**
 * Class m191023_073608_alter_column_type_tbl_client_phone
 */
class m191023_073608_alter_column_type_tbl_client_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$sql = 'Alter table client_phone alter column type set default 0';

    	$this->execute($sql);

    	$sql = 'Update client_phone set type = 0 where type is null';

    	$this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
