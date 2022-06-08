<?php

namespace src\services\dateSensitive;

use common\models\DateSensitive;
use common\models\DateSensitiveView;
use src\helpers\app\AppHelper;
use src\repositories\dateSensitive\DateSensitiveViewRepository;
use src\services\system\DbViewCryptService;
use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Class DateSensitiveService
 * @package src\services\dateSensitive
 */
class DateSensitiveService
{
    /**
     * @param DateSensitive $dateSensitive
     * @return void
     */
    public function createViews(DateSensitive $dateSensitive)
    {
        $sources = Json::decode($dateSensitive->da_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->createView($db, $dateSensitive, $tableName, $columns);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dateSensitive->da_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DateSensitiveService:createViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dateSensitive->da_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DateSensitiveService:createViews:Throwable');
            }
        }
    }

    /**
     * @param DateSensitive $dateSensitive
     * @return void
     */
    public function dropViews(DateSensitive $dateSensitive)
    {
        $sources = Json::decode($dateSensitive->da_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->dropView($db, $tableName, $dateSensitive->da_key);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dateSensitive->da_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DateSensitiveService:dropViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dateSensitive->da_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DateSensitiveService:dropViews:Throwable');
            }
        }
    }

    /**
     * @param Connection $db
     * @param string $tableName
     * @param string $postFix
     * @param array $cryptColumns
     * @return void
     * @throws \yii\db\Exception
     */
    public function createView(Connection $db, DateSensitive $dateSensitive, string $tableName, array $cryptColumns)
    {
        $dbViewCryptService = new DbViewCryptService($db, $tableName, $dateSensitive->da_key, $cryptColumns);
        $db->createCommand($dbViewCryptService->getReInitSql())->execute();
        $dateSensitiveView = DateSensitiveView::create($dateSensitive->da_id, $dbViewCryptService->getViewName(), $tableName);
        (new DateSensitiveViewRepository($dateSensitiveView))->save(true);
    }

    /**
     * @param DateSensitiveView $dateSensitiveView
     * @return void
     * @throws \yii\db\Exception
     */
    public function dropViewByDateSensitiveView(DateSensitiveView $dateSensitiveView)
    {
        $this->dropView(Yii::$app->getDb(), $dateSensitiveView->dv_table_name, $dateSensitiveView->dateSensitive->da_key ?? '');
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
