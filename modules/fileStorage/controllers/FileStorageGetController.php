<?php

namespace modules\fileStorage\controllers;

use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class FileStorageGetController
 */
class FileStorageGetController extends Controller
{
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            if (!FileStorageSettings::canDownload()) {
                throw new NotFoundHttpException();
            }
            return true;
        }
        return false;
    }

    public function actionView()
    {
        $uid = (string)\Yii::$app->request->get('uid');
        $file = FileStorage::find()->byUid($uid)->one();
        if (!$file) {
            throw new NotFoundHttpException();
        }
    }
}
