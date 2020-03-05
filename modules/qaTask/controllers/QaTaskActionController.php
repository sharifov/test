<?php

namespace modules\qaTask\controllers;

use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelForm;
use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelService;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseForm;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseService;
use modules\qaTask\src\useCases\qaTask\decide\lead\reAssign\QaTaskDecideLeadReAssignForm;
use modules\qaTask\src\useCases\qaTask\decide\lead\reAssign\QaTaskDecideLeadReAssignService;
use modules\qaTask\src\useCases\qaTask\decide\lead\sendToRedialQueue\QaTaskDecideLeadSendToRedialQueue;
use modules\qaTask\src\useCases\qaTask\decide\noAction\QaTaskDecideNoActionForm;
use modules\qaTask\src\useCases\qaTask\decide\noAction\QaTaskDecideNoActionService;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateForm;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateService;
use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateForm;
use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateService;
use modules\qaTask\src\useCases\qaTask\returnTask\toPending\QaTaskReturnToPendingForm;
use modules\qaTask\src\useCases\qaTask\returnTask\toPending\QaTaskReturnToPendingService;
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
 * @property QaTaskEscalateService $escalateService
 * @property QaTaskCloseService $closeService
 * @property QaTaskCancelService $cancelService
 * @property QaTaskReturnToPendingService $returnToPendingService
 * @property QaTaskReturnToEscalateService $returnToEscalateService
 * @property QaTaskDecideLeadSendToRedialQueue $decideLeadSendToRedialQueue
 * @property QaTaskDecideNoActionService $decideNoActionService
 * @property QaTaskDecideLeadReAssignService $decideLeadReAssignService
 * @property QaTaskDecideService $qaTaskDecideService
 */
class QaTaskActionController extends FController
{
    private $takeService;
    private $takeOverService;
    private $escalateService;
    private $closeService;
    private $cancelService;
    private $returnToPendingService;
    private $returnToEscalateService;
    private $decideLeadSendToRedialQueue;
    private $decideNoActionService;
    private $decideLeadReAssignService;
    private $qaTaskDecideService;

    public function __construct(
        $id,
        $module,
        QaTaskTakeService $takeService,
        QaTaskTakeOverService $takeOverService,
        QaTaskEscalateService $escalateService,
        QaTaskCloseService $closeService,
        QaTaskCancelService $cancelService,
        QaTaskReturnToPendingService $returnToPendingService,
        QaTaskReturnToEscalateService $returnToEscalateService,
        QaTaskDecideLeadSendToRedialQueue $decideLeadSendToRedialQueue,
        QaTaskDecideNoActionService $decideNoActionService,
        QaTaskDecideLeadReAssignService $decideLeadReAssignService,
        QaTaskDecideService $qaTaskDecideService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->takeService = $takeService;
        $this->takeOverService = $takeOverService;
        $this->escalateService = $escalateService;
        $this->closeService = $closeService;
        $this->cancelService = $cancelService;
        $this->returnToPendingService = $returnToPendingService;
        $this->returnToEscalateService = $returnToEscalateService;
        $this->decideLeadSendToRedialQueue = $decideLeadSendToRedialQueue;
        $this->decideNoActionService = $decideNoActionService;
        $this->decideLeadReAssignService = $decideLeadReAssignService;
        $this->qaTaskDecideService = $qaTaskDecideService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'take',
                    'take-over',
                    'escalate',
                    'close',
                    'cancel',
                    'return-to-pending',
                    'return-to-escalate',
                    'decide-no-action',
                    'decide-lead-send-to-redial-queue',
                    'decide-lead-re-assign',
                ],
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

        $this->takeService->permissionGuard(Auth::id(), $task);

        try {
            $this->takeService->take($task->t_id, Auth::id());
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

        $this->takeOverService->permissionGuard(Auth::id(), $task);

        $form = new QaTaskTakeOverForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->takeOverService->takeOver($form);
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
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionEscalate($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->escalateService->permissionGuard(Auth::id());

        $form = new QaTaskEscalateForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->escalateService->escalate($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('escalate', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionClose($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->closeService->permissionGuard(Auth::id());

        $form = new QaTaskCloseForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->closeService->close($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('close', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCancel($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->cancelService->permissionGuard(Auth::id(), $task);

        $form = new QaTaskCancelForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->cancelService->cancel($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('cancel', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionReturnToPending($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->returnToPendingService->permissionGuard(Auth::id(), $task);

        $form = new QaTaskReturnToPendingForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->returnToPendingService->return($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('return-to-pending', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionReturnToEscalate($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->returnToEscalateService->permissionGuard(Auth::id(), $task);

        $form = new QaTaskReturnToEscalateForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->returnToEscalateService->return($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('return-to-escalate', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDecideNoAction($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->qaTaskDecideService->permissionGuard(Auth::id());

        $form = new QaTaskDecideNoActionForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->decideNoActionService->handle($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('decide-no-action', [
            'model' => $form
        ]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDecideLeadSendToRedialQueue($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->qaTaskDecideService->permissionGuard(Auth::id());

        try {
            $this->decideLeadSendToRedialQueue->handle($task->t_id, Auth::id());
            \Yii::$app->session->addFlash('success', 'Success');
        } catch (\DomainException $e) {
            \Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
    }

    /**
     * @param $gid
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDecideLeadReAssign($gid)
    {
        $task = $this->findModel((string)$gid);

        $this->qaTaskDecideService->permissionGuard(Auth::id());

        $form = new QaTaskDecideLeadReAssignForm($task, Auth::user());

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->decideLeadReAssignService->handle($form);
                \Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/qa-task/qa-task/view', 'gid' => $task->t_gid]);
        }

        return $this->renderAjax('decide-lead-re-assign', [
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
