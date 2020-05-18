<?php

use yii\db\Migration;

/**
 * Class m191022_133652_drop_column_status_tbl_client_phone
 */
class m191022_133652_drop_column_status_tbl_client_phone extends Migration
{
	/**
	 * @var string
	 */
	private $tableName = 'client_phone';

	/**
	 * @var string
	 */
	private $columnName = 'status';

	/**
	 * {@inheritdoc}
	 * @throws \yii\db\Exception
	 */
    public function safeUp()
    {
		$databaseName = Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();

		$command = Yii::$app->db->createCommand("select count(*) from information_schema.COLUMNS where TABLE_SCHEMA = :databaseName and TABLE_NAME = :table_name and COLUMN_NAME = :columnName;", [
			':databaseName' => $databaseName,
			':table_name' => $this->tableName,
			':columnName' => $this->columnName
		]);

		$existsColumn = (bool)$command->queryScalar();

		if ($existsColumn) {
			$this->dropColumn('client_phone', 'status');
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
