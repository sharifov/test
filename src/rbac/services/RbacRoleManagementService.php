<?php

namespace src\rbac\services;

use src\helpers\app\AppHelper;
use yii\db\Query;
use yii\rbac\DbManager;

class RbacRoleManagementService
{
    /**
     * @param string $donorRoleName
     * @param string $recipientRoleName
     * @return bool
     */
    public static function clonePermissions(string $donorRoleName, string $recipientRoleName): bool
    {
        try {
            $transaction = \Yii::$app
                ->db->beginTransaction();
            \Yii::$app
                ->db
                ->createCommand()
                ->delete('auth_item_child', ['parent' => $recipientRoleName])
                ->execute();
            $donorRows = (new Query())
                ->select('a.child')
                ->from(['a' => 'auth_item_child'])
                ->where(['a.parent' => $donorRoleName])
                ->column();
            array_walk($donorRows, function (&$item) use ($recipientRoleName) {
                $newItem = [];
                $newItem['child']  = $item;
                $newItem['parent'] = $recipientRoleName;
                $item = $newItem;
            });
            \Yii::$app
                ->db
                ->createCommand()
                ->batchInsert('auth_item_child', ['child', 'parent'], $donorRows)
                ->execute();
            $transaction->commit();
            $authManager = \Yii::$app->getAuthManager();
            $authManager->invalidateCache();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $logMessage = AppHelper::throwableLog($e);
            \Yii::error(
                $logMessage,
                'RbacRoleManagementService:clonePermissions'
            );
            return false;
        }
    }

    /**
     * @param string $donorRoleName
     * @param string $recipientRoleName
     * @return bool
     */
    public static function mergePermissions(string $donorRoleName, string $recipientRoleName): bool
    {
        try {
            $transaction = \Yii::$app
                ->db->beginTransaction();
            $recipientRows = (new Query())
                ->select('a.child')
                ->from(['a' => 'auth_item_child'])
                ->where(['a.parent' => $recipientRoleName])
                ->column();
            $donorRows = (new Query())
                ->select('a.child')
                ->from(['a' => 'auth_item_child'])
                ->where(['a.parent' => $donorRoleName])
                ->andWhere(['NOT IN','a.child', $recipientRows])
                ->column();
            array_walk($donorRows, function (&$item) use ($recipientRoleName) {
                $newItem = [];
                $newItem['child']  = $item;
                $newItem['parent'] = $recipientRoleName;
                $item = $newItem;
            });
            \Yii::$app
                ->db
                ->createCommand()
                ->batchInsert('auth_item_child', ['child', 'parent'], $donorRows)
                ->execute();
            $transaction->commit();
            $authManager = \Yii::$app->getAuthManager();
            $authManager->invalidateCache();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $logMessage = AppHelper::throwableLog($e);
            \Yii::error(
                $logMessage,
                'RbacRoleManagementService:mergePermissions'
            );
            return false;
        }
    }

    /**
     * @param string $donorRoleName
     * @param string $recipientRoleName
     * @return bool
     */
    public static function excludePermissions(string $donorRoleName, string $recipientRoleName): bool
    {
        try {
            $transaction = \Yii::$app
                ->db->beginTransaction();
            $donorRows = (new Query())
                ->select('a.child')
                ->from(['a' => 'auth_item_child'])
                ->where(['a.parent' => $donorRoleName])
                ->column();
            \Yii::$app
                ->db
                ->createCommand()
                ->delete('auth_item_child', ['parent' => $recipientRoleName, 'child' => $donorRows])
                ->execute();
            $transaction->commit();
            $authManager = \Yii::$app->getAuthManager();
            $authManager->invalidateCache();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $logMessage = AppHelper::throwableLog($e);
            \Yii::error(
                $logMessage,
                'RbacRoleManagementService:excludePermissions'
            );
            return false;
        }
    }
}
