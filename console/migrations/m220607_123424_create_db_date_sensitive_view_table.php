<?php

use common\models\DbDateSensitive;
use src\services\dbDateSensitive\DbDateSensitiveService;
use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation of table `{{%date_sensitive_view}}`.
 */
class m220607_123424_create_db_date_sensitive_view_table extends Migration
{
    private const DEFAULT_KEY = 'view';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%db_date_sensitive_view}}', [
            'ddv_dda_id' => $this->integer(),
            'ddv_view_name' => $this->string(),
            'ddv_table_name' => $this->string(),
            'ddv_created_dt' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('PK-db_date_sensitive_view', '{{%db_date_sensitive_view}}', ['ddv_dda_id', 'ddv_view_name']);

        $this->addForeignKey('FK-db_date_sensitive_view-ddv_dda_id', '{{%db_date_sensitive_view}}', 'ddv_dda_id', '{{%db_date_sensitive}}', 'dda_id', 'CASCADE');

        try {
            /** @var DbDateSensitive $dateSensitive */
            $dateSensitive = DbDateSensitive::find()
                ->andWhere(['dda_key' => self::DEFAULT_KEY])
                ->one();

            if ($dateSensitive) {
                $service = Yii::createObject(DbDateSensitiveService::class);
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
        $this->dropTable('{{%db_date_sensitive_view}}');
    }
}
