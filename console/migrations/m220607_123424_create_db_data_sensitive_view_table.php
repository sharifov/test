<?php

use common\models\DbDataSensitive;
use src\services\dbDataSensitive\DbDataSensitiveService;
use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation of table `{{%date_sensitive_view}}`.
 */
class m220607_123424_create_db_data_sensitive_view_table extends Migration
{
    private const DEFAULT_KEY = 'view';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%db_data_sensitive_view}}', [
            'ddv_dda_id' => $this->integer(),
            'ddv_view_name' => $this->string(),
            'ddv_table_name' => $this->string(),
            'ddv_created_dt' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('PK-db_data_sensitive_view', '{{%db_data_sensitive_view}}', ['ddv_dda_id', 'ddv_view_name']);

        $this->addForeignKey('FK-db_data_sensitive_view-ddv_dda_id', '{{%db_data_sensitive_view}}', 'ddv_dda_id', '{{%db_data_sensitive}}', 'dda_id', 'CASCADE');

        try {
            /** @var DbDataSensitive $dateSensitive */
            $dateSensitive = DbDataSensitive::find()
                ->andWhere(['dda_key' => self::DEFAULT_KEY])
                ->one();

            if ($dateSensitive) {
                $service = Yii::createObject(DbDataSensitiveService::class);
                $service->createViews($dateSensitive);
            }
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%db_data_sensitive_view}}');
    }
}
