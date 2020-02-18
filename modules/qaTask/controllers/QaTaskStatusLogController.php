<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTaskStatusLog\search\QaTaskStatusLogSearch;
use modules\qaTask\src\guard\QaTaskGuard;
use Yii;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class QaTaskStatusLogController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'show',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionShow(): string
    {
        if (!Auth::can('/qa-task/qa-task/view')) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $task = $this->findModel((string)Yii::$app->request->get('gid'));

        QaTaskGuard::guard($task->t_project_id, Auth::id());

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
