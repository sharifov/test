<?php

use yii\db\Migration;

/**
 * Class m191023_081323_alter_column_type_tbl_client_email
 */
class m191023_081323_alter_column_type_tbl_client_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$sql = 'Alter table client_email alter column type set default 0';

		$this->execute($sql);

		$sql = 'Update client_email set type = 0 where type is null';

		$this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
