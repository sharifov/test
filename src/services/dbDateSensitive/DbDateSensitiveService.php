<?php

namespace src\services\dbDateSensitive;

use common\models\DbDateSensitive;
use common\models\DbDateSensitiveView;
use src\helpers\app\AppHelper;
use src\repositories\dbDateSensitive\DbDateSensitiveViewRepository;
use src\services\system\DbViewCryptService;
use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Class DbDateSensitiveService
 * @package src\services\dbDateSensitive
 */
class DbDateSensitiveService
{
    /**
     * @param DbDateSensitive $dbDateSensitive
     * @return void
     */
    public function createViews(DbDateSensitive $dbDateSensitive)
    {
        $sources = Json::decode($dbDateSensitive->dda_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->createView($db, $dbDateSensitive, $tableName, $columns);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dbDateSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DbDateSensitiveService:createViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dbDateSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DbDateSensitiveService:createViews:Throwable');
            }
        }
    }

    /**
     * @param DbDateSensitive $dbDateSensitive
     * @return void
     */
    public function dropViews(DbDateSensitive $dbDateSensitive)
    {
        $sources = Json::decode($dbDateSensitive->dda_source);
        $db = Yii::$app->getDb();

        foreach ($sources as $tableName => $columns) {
            try {
                $this->dropView($db, $tableName, $dbDateSensitive->dda_key);
            } catch (\RuntimeException | \DomainException $e) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($e), ['DateSensitiveId' => $dbDateSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::warning($message, 'DbDateSensitiveService:dropViews:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['DateSensitiveId' => $dbDateSensitive->dda_id, 'tableName' => $tableName]);
                \Yii::error($message, 'DbDateSensitiveService:dropViews:Throwable');
            }
        }
    }

    /**
     * @param Connection $db
     * @param DbDateSensitive $dbDateSensitive
     * @param string $tableName
     * @param array $cryptColumns
     * @return void
     * @throws \yii\db\Exception
     */
    public function createView(Connection $db, DbDateSensitive $dbDateSensitive, string $tableName, array $cryptColumns)
    {
        $dbViewCryptService = new DbViewCryptService($db, $tableName, $dbDateSensitive->dda_key, $cryptColumns);
        $db->createCommand($dbViewCryptService->getReInitSql())->execute();
        $dateSensitiveView = DbDateSensitiveView::create($dbDateSensitive->dda_id, $dbViewCryptService->getViewName(), $tableName);
        (new DbDateSensitiveViewRepository($dateSensitiveView))->save(true);
    }

    /**
     * @param DbDateSensitiveView $dateSensitiveView
     * @return void
     * @throws \yii\db\Exception
     */
    public function dropViewByDbDateSensitiveView(DbDateSensitiveView $dbDateSensitiveView)
    {
        $this->dropView(Yii::$app->getDb(), $dbDateSensitiveView->ddv_table_name, $dbDateSensitiveView->dbDateSensitive->dda_key ?? '');
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
