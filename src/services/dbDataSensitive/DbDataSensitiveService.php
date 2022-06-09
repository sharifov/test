<?php

namespace src\services\dbDataSensitive;

use common\models\DbDataSensitive;
use common\models\DbDataSensitiveView;
use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use src\repositories\dbDataSensitive\DbDataSensitiveViewRepository;
use src\services\system\DbViewCryptService;
use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Class DbDataSensitiveService
 * @package src\services\dbDataSensitive
 */
class DbDataSensitiveService
{
    /**
     * @param DbDataSensitive $dbDataSensitive
     * @return void
     */
    public function createViews(DbDataSensitive $dbDataSensitive)
    {
        $sources = JsonHelper::decode($dbDataSensitive->dda_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->createView($db, $dbDataSensitive, $tableName, $columns);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DataSensitiveId' => $dbDataSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DbDataSensitiveService:createViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DataSensitiveId' => $dbDataSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DbDataSensitiveService:createViews:Throwable');
            }
        }
    }

    /**
     * @param DbDataSensitive $dbDataSensitive
     * @return void
     */
    public function dropViews(DbDataSensitive $dbDataSensitive)
    {
        $sources = JsonHelper::decode($dbDataSensitive->dda_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->dropView($db, $tableName, $dbDataSensitive->dda_key);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DataSensitiveId' => $dbDataSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DbDataSensitiveService:dropViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DataSensitiveId' => $dbDataSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DbDataSensitiveService:dropViews:Throwable');
            }
        }
    }

    /**
     * @param Connection $db
     * @param DbDataSensitive $dbDataSensitive
     * @param string $tableName
     * @param array $cryptColumns
     * @return void
     * @throws \yii\db\Exception
     */
    public function createView(Connection $db, DbDataSensitive $dbDataSensitive, string $tableName, array $cryptColumns)
    {
        $dbViewCryptService = new DbViewCryptService($db, $tableName, $dbDataSensitive->dda_key, $cryptColumns);
        $db->createCommand($dbViewCryptService->getReInitSql())->execute();
        $dataSensitiveView = DbDataSensitiveView::create($dbDataSensitive->dda_id, $dbViewCryptService->getViewName(), $tableName);
        (new DbDataSensitiveViewRepository($dataSensitiveView))->save(true);
    }

    /**
     * @param DbDataSensitiveView $dbDataSensitiveView
     * @return void
     * @throws \yii\db\Exception
     */
    public function dropViewByDbDataSensitiveView(DbDataSensitiveView $dbDataSensitiveView)
    {
        $this->dropView(Yii::$app->getDb(), $dbDataSensitiveView->ddv_table_name, $dbDataSensitiveView->dbDateSensitive->dda_key ?? '');
    }

    /**
     * @param Connection $db
     * @param string $tableName
     * @param string $postFix
     * @return void
     * @throws \yii\db\Exception
     */
    public function dropView(Connection $db, string $tableName, string $postFix)
    {
        $dbViewCryptService = new DbViewCryptService($db, $tableName, $postFix, []);
        $db->createCommand($dbViewCryptService->getDropSql())->execute();
    }
}
