<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use sales\auth\Auth;
use sales\forms\leadflow\FollowUpReasonForm;
use sales\forms\leadflow\RejectReasonForm;
use sales\forms\leadflow\ReturnReasonForm;
use sales\forms\leadflow\SnoozeReasonForm;
use sales\forms\leadflow\TrashReasonForm;
use sales\guards\lead\FollowUpGuard;
use sales\services\lead\LeadAssignService;
use sales\services\lead\LeadStateService;
use Yii;
use sales\forms\leadflow\TakeOverReasonForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class LeadChangeStateController
 *
 * @property LeadAssignService $assignService
 * @property LeadStateService $stateService
 * @property FollowUpGuard $followUpGuard
 *
 */
class LeadChangeStateController extends FController
{

    private $assignService;
    private $stateService;
    private $followUpGuard;

    /**
     * @param $id
     * @param $module
     * @param LeadAssignService $assignService
     * @param LeadStateService $stateService
     * @param FollowUpGuard $followUpGuard
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        LeadAssignService $assignService,
        LeadStateService $stateService,
        FollowUpGuard $followUpGuard,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->assignService = $assignService;
        $this->stateService = $stateService;
        $this->followUpGuard = $followUpGuard;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'take-over',
                    'validate-take-over',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionTakeOver(): Response
    {
        $lead = $this->getLead();

        if (!Auth::can('lead/view', ['lead' => $lead])) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $form = new TakeOverReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                /** @var Employee $user */
                $user = Yii::$app->user->identity;
                $this->assignService->takeOver($lead, $user, Yii::$app->user->id, $form->description);
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            } catch (\Throwable $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                throw $e;
            }
        } elseif ($form->getErrors()) {
            Yii::$app->getSession()->setFlash('error', 'Error validate form.');
            Yii::warning(VarDumper::dumpAsString($form->getErrors()), 'LeadChangeStateController:TakeOverReasonForm:Validate');
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateTakeOver(): array
    {
        $lead = $this->getLead();

        if (!Auth::can('lead/view', ['lead' => $lead])) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $form = new TakeOverReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionFollowUp()
    {
        $lead = $this->getLead();
        $form = new FollowUpReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                /** @var Employee $user */
                $user = Yii::$app->user->identity;
                $this->followUpGuard->guard($user, $lead);

                $this->stateService->followUp($lead, null, Yii::$app->user->id, $form->description);
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            }
        } else {
            return $this->renderAjax('reason_follow_up', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateFollowUp(): array
    {
        $lead = $this->getLead();
        $form = new FollowUpReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionTrash()
    {
        $lead = $this->getLead();
        $form = new TrashReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                if ($form->isDuplicateReason()) {
                    $this->stateService->duplicate($lead, $lead->employee_id, $form->originId, Yii::$app->user->id, $form->description);
                } else {
                    $this->stateService->trash($lead, $lead->employee_id, Yii::$app->user->id, $form->description);
                }
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            }
        } else {
            return $this->renderAjax('reason_trash', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateTrash(): array
    {
        $lead = $this->getLead();
        $form = new TrashReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionSnooze()
    {
        $lead = $this->getLead();
        $form = new SnoozeReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->stateService->snooze($lead, $lead->employee_id, $form->snoozeFor, Yii::$app->user->id, $form->description);
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            }
        } else {
            return $this->renderAjax('reason_snooze', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateSnooze(): array
    {
        $lead = $this->getLead();
        $form = new SnoozeReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionReturn()
    {
        $lead = $this->getLead();
        $form = new ReturnReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
               if ($form->isReturnToFollowUp()) {
                   $this->stateService->followUp($lead, $lead->employee_id, Yii::$app->user->id, $form->description);
                   Yii::$app->getSession()->setFlash('success', 'Success');
               } elseif ($form->isReturnToProcessing()) {
                   $this->stateService->processing($lead, $form->userId, Yii::$app->user->id, $form->description);
                   Yii::$app->getSession()->setFlash('success', 'Success');
               } else {
                   Yii::$app->getSession()->setFlash('error', 'Error');
               }
            } catch (\DomainException $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            }
        } else {
            return $this->renderAjax('reason_return', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateReturn(): array
    {
        $lead = $this->getLead();
        $form = new ReturnReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionReject()
    {
        $lead = $this->getLead();
        $form = new RejectReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->stateService->reject($lead, $lead->employee_id, Yii::$app->user->id, $form->description);
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                //Yii::$app->errorHandler->logException($e);
                Yii::warning($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            }
        } else {
            return $this->renderAjax('reason_reject', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateReject(): array
    {
        $lead = $this->getLead();
        $form = new RejectReasonForm($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return Lead
     * @throws NotFoundHttpException
     */
    private function getLead(): Lead
    {
        return $this->findLeadByGid(Yii::$app->request->get('gid'));
    }

    /**
     * @param $gid
     * @return Lead
     * @throws NotFoundHttpException
     */
    protected function findLeadByGid($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
