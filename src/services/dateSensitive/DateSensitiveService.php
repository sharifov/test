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
     * @throws \yii\db\Exception
     */
    public function createViews(DateSensitive $dateSensitive)
    {
        $sources = Json::decode($dateSensitive->da_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->createView($db, $dateSensitive, $tableName, $columns);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dateSensitive->da_id]);
                \Yii::warning($message, 'DateSensitiveController:actionCreateView:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dateSensitive->da_id]);
                \Yii::error($message, 'DateSensitiveController:actionCreateView:Throwable');
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

        if (!$db->createCommand("SELECT 1 FROM {$dbViewCryptService->getViewName()}")->execute()) {
            throw new \RuntimeException($tableName . ' View created, but is empty');
        }
        $dateSensitiveView = DateSensitiveView::create($dateSensitive->da_id, $dbViewCryptService->getViewName(), $tableName);
        (new DateSensitiveViewRepository($dateSensitiveView))->save(true);
    }

    /**
     * @param DateSensitive $dateSensitive
     * @return void
     * @throws \yii\db\Exception
     */
    public function dropViews(DateSensitive $dateSensitive)
    {
        $sources = Json::decode($dateSensitive->da_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->dropView($db, $tableName, $dateSensitive->da_key);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dateSensitive->da_id]);
                \Yii::warning($message, 'DateSensitiveController:actionDropView:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dateSensitive->da_id]);
                \Yii::error($message, 'DateSensitiveController:actionDropView:Throwable');
            }
        }
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
