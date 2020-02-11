<?php

namespace modules\qaTask\controllers;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class QaTaskController extends Controller
{
    /**
     * @param $gid
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($gid): string
    {
        return $this->render('view', [
            'model' => $this->findModel($gid),
        ]);
    }

    /**
     * @param $gid
     * @return QaTask
     * @throws NotFoundHttpException
     */
    protected function findModel($gid): QaTask
    {
        if (($model = QaTask::find()->andWhere(['t_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
