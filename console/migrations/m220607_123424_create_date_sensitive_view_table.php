<?php

use common\models\DateSensitive;
use src\services\dateSensitive\DateSensitiveService;
use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation of table `{{%date_sensitive_view}}`.
 */
class m220607_123424_create_date_sensitive_view_table extends Migration
{
    private const DEFAULT_KEY = 'view';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%date_sensitive_view}}', [
            'dv_da_id' => $this->integer(),
            'dv_view_name' => $this->string(),
            'dv_table_name' => $this->string(),
            'dv_created_dt' => $this->dateTime(),
        ]);
        $this->addPrimaryKey('PK-date_sensitive_view', '{{%date_sensitive_view}}', ['dv_da_id', 'dv_table_name']);

        $this->addForeignKey('FK-date_sensitive_view-dv_da_id', '{{%date_sensitive_view}}', 'dv_da_id', '{{%date_sensitive}}', 'da_id', 'CASCADE');

        try {
            /** @var DateSensitive $dateSensitive */
            $dateSensitive = DateSensitive::find()
                ->andWhere(['da_key' => self::DEFAULT_KEY])
                ->one();

            if ($dateSensitive) {
                $service = Yii::createObject(DateSensitiveService::class);
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
        $this->dropTable('{{%date_sensitive_view}}');
    }
}
