<?php

namespace modules\qaTask\controllers;

use Yii;
use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeService;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverForm;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverService;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class QaTaskActionController
 *
 * @property QaTaskTakeService $takeService
 * @property QaTaskTakeOverService $takeOverService
 */
class QaTaskActionController extends FController
{
    private $takeService;
    private $takeOverService;

    public function __construct(
        $id,
        $module,
        QaTaskTakeService $takeService,
        QaTaskTakeOverService $takeOverService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->takeService = $takeService;
        $this->takeOverService = $takeOverService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => ['take', 'take-over'],
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
            $this->takeService->take($task->t_id, Auth::id(), Auth::id());
            Yii::$app->session->addFlash('success', 'Success');
        } catch (\DomainException $e) {
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionTakeOver($gid)
    {
        $task = $this->findModel((string)$gid);

        QaTaskTakeOverService::permissionGuard($task);

        $form = new QaTaskTakeOverForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->takeOverService->takeOver($form, Auth::id());
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('take-over', [
            'model' => $form
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
