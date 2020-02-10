<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogSearch;
use Yii;
use sales\auth\Auth;
use yii\web\NotFoundHttpException;

class QaTaskStatusLogController extends FController
{
    public function actionShow(): string
    {
        $task = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new QaTaskStatusLogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user(), $task->t_id);

        return $this->renderAjax('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($gid): QaTask
    {
        if (($model = QaTask::find()->andWhere(['t_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
