<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeService;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class QaTaskActionController
 *
 * @property QaTaskTakeService $qaTaskTakeService
 */
class QaTaskActionController extends FController
{
    private $qaTaskTakeService;

    public function __construct(
        $id,
        $module,
        QaTaskTakeService $qaTaskTakeService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->qaTaskTakeService = $qaTaskTakeService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => ['take'],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param $gid
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionTake($gid): Response
    {
        $task = $this->findModel((string)$gid);
        QaTaskTakeService::permissionGuard($task);
        try {
            $this->qaTaskTakeService->take($task->t_id, Auth::id());
            \Yii::$app->session->addFlash('success', 'Success');
        } catch (\DomainException $e) {
            \Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
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
