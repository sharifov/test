<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\qaTask\src\entities\qaTaskRules\QaTaskRules;
use modules\qaTask\src\useCases\qaTask\create\lead\trashCheck\QaTaskCreateLeadTrashCheckService;
use modules\qaTask\src\useCases\qaTask\create\lead\trashCheck\Rule;
use modules\qaTask\src\useCases\qaTask\create\lead\trashCheck\RuleException;
use src\auth\Auth;
use src\forms\leadflow\CloseReasonForm;
use src\forms\leadflow\FollowUpReasonForm;
use src\forms\leadflow\RejectReasonForm;
use src\forms\leadflow\ReturnReasonForm;
use src\forms\leadflow\SnoozeReasonForm;
use src\forms\leadflow\TrashReasonForm;
use src\guards\lead\FollowUpGuard;
use src\helpers\app\AppHelper;
use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\leadUserConversion\repository\LeadUserConversionRepository;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use src\repositories\NotFoundException;
use src\services\lead\LeadAssignService;
use src\services\lead\LeadStateService;
use Yii;
use src\forms\leadflow\TakeOverReasonForm;
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
 * @property QaTaskCreateLeadTrashCheckService $leadCheckTrashService
 *
 */
class LeadChangeStateController extends FController
{
    private $assignService;
    private $stateService;
    private $followUpGuard;
    private QaTaskCreateLeadTrashCheckService $leadCheckTrashService;

    /**
     * @param $id
     * @param $module
     * @param LeadAssignService $assignService
     * @param LeadStateService $stateService
     * @param FollowUpGuard $followUpGuard
     * @param QaTaskCreateLeadTrashCheckService $leadCheckTrashService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        LeadAssignService $assignService,
        LeadStateService $stateService,
        FollowUpGuard $followUpGuard,
        QaTaskCreateLeadTrashCheckService $leadCheckTrashService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->assignService = $assignService;
        $this->stateService = $stateService;
        $this->followUpGuard = $followUpGuard;
        $this->leadCheckTrashService = $leadCheckTrashService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'take-over',
                    'validate-take-over',
                    'close',
                    'ajax-changed-close-reason',
                    'trash',
                    'validate-trash'
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

                $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                $leadUserConversionService->addAutomate(
                    $lead->id,
                    $user->getId(),
                    LeadUserConversionDictionary::DESCRIPTION_TAKE_OVER,
                    $user->getId()
                );

                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            } catch (\RuntimeException $e) {
                Yii::warning(AppHelper::throwableLog($e), 'LeadChangeStateController:actionTakeOver:RuntimeException');
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error($e, __CLASS__ . ':' . __FUNCTION__);
                Yii::$app->getSession()->setFlash('error', 'Server error');
            }
        } elseif ($form->getErrors()) {
            Yii::$app->getSession()->setFlash('error', 'Error validate form.');
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

        /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_TRASH, Access to move lead to trash */
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_TRASH)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $form = new TrashReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                if ($form->isDuplicateReason()) {
                    $this->stateService->duplicate($lead, $lead->employee_id, $form->originId, Yii::$app->user->id, $form->description);
                } else {
                    $this->stateService->trash($lead, $lead->employee_id, Yii::$app->user->id, $form->description);
                }

                Yii::$app->getSession()->setFlash('success', 'Success');

                if (($parameters = QaTaskRules::getRule(QaTaskCreateLeadTrashCheckService::CATEGORY_KEY)) && $parameters->isEnabled()) {
                    $rule = new Rule($parameters->getValue());
                    if ($rule->guard($lead->lDep->dep_key ?? null, $lead->project->project_key ?? null, $form->reason)) {
                        $this->leadCheckTrashService->handle($rule, $lead);
                    }
                }
            } catch (RuleException $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'LeadChangeStateController::actionTrash::RuleException');
            } catch (\DomainException $e) {
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'LeadChangeStateController::actionTrash::Throwable');
                Yii::$app->getSession()->setFlash('danger', 'Server error occurred');
            }
        } else {
            return $this->renderAjax('reason_trash', [
                'reasonForm' => $form
            ]);
        }
        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    public function actionClose()
    {
        $lead = $this->getLead();
        /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLOSE, Access to close lead */
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLOSE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $form = new CloseReasonForm($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->stateService->close($lead, $form->reasonKey, Auth::id(), $form->reason, (int) $form->originLeadId);
                Yii::$app->getSession()->setFlash('success', 'Success');

                return $this->redirect(['lead/view', 'gid' => $lead->gid]);
            } catch (RuleException | \RuntimeException | \DomainException $e) {
                $form->addError('general', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'LeadChangeStateController::actionClose::Throwable');
                Yii::$app->getSession()->setFlash('danger', 'Server error occurred');
            }
        }
        $leadReasonStatues = LeadStatusReasonQuery::getAllEnabledAsArray();

        $reasonStatues = ArrayHelper::map($leadReasonStatues, 'lsr_key', 'lsr_name');
        if (!empty($reasonStatues)) {
            $n = 1;
            foreach ($reasonStatues as $key => $name) {
                $reasonStatues[$key] = $n++ . '. ' . $name;
            }
        }

        $reasonStatuesCommentRequired = ArrayHelper::map($leadReasonStatues, 'lsr_key', 'lsr_comment_required');

        return $this->renderAjax('reason_close', [
            'reasonForm' => $form,
            'reasonStatuses' => $reasonStatues,
            'reasonStatuesCommentRequired' => $reasonStatuesCommentRequired
        ]);
    }

    public function actionAjaxChangedCloseReason()
    {
        $isMultipleUpdate = Yii::$app->request->get('multipleUpdate', false);
        if (!$isMultipleUpdate) {
            $lead = $this->getLead();
            /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLOSE, Access to close lead */
            $leadAbacDto = new LeadAbacDto($lead, Auth::id());
            if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLOSE)) {
                throw new ForbiddenHttpException('Access Denied');
            }
        }

        $reasonKey = Yii::$app->request->get('reasonKey');
        if (empty($reasonKey)) {
            $commentRequired = 0;
            $description = '';
            $originRequired = false;
        } else {
            $reason = LeadStatusReasonQuery::getLeadStatusReasonByKey($reasonKey);
            if (!$reason) {
                throw new NotFoundException('LeadStatusReason not found');
            }
            $commentRequired = $reason->lsr_comment_required;
            $description = nl2br($reason->lsr_description);
            $originRequired = $reason->lsr_key === LeadStatusReason::REASON_KEY_DUPLICATED;
        }

        return $this->asJson([
            'commentRequired' => $commentRequired,
            'description' => $description,
            'originRequired' => $originRequired
         ]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionValidateTrash(): array
    {
        $lead = $this->getLead();
        /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_TRASH, Access to move lead to trash */
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_TRASH)) {
            throw new ForbiddenHttpException('Access Denied');
        }
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
        /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_SNOOZE, Access to change */
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_SNOOZE)) {
            throw new ForbiddenHttpException('Snooze action is not available due to business restrictions. Please contact administrator for more info.');
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->stateService->snooze($lead, $lead->employee_id, $form->snoozeFor, Yii::$app->user->id, $form->description);
                Yii::$app->getSession()->setFlash('success', 'Success');
            } catch (\DomainException $e) {
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
