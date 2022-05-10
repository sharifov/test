<?php

namespace modules\shiftSchedule\controllers;

use common\models\Employee;
use frontend\controllers\FController;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleType\search\ShiftScheduleTypeSearch;
use modules\shiftSchedule\src\entities\userShiftAssign\repository\UserShiftAssignRepository;
use modules\shiftSchedule\src\entities\userShiftAssign\search\UserShiftAssignListSearch;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\forms\UserShiftMultipleAssignForm;
use src\access\ListsAccess;
use src\auth\Auth;
use src\forms\cases\CasesClientUpdateForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class UserShiftAssignController
 */
class UserShiftAssignController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_ACCESS, Access to page user-shift-assign/index */
                    [
                        'actions' => ['index', 'select-all'],
                        'allow' => \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_ACCESS),
                        'roles' => ['@'],
                    ],
                    /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access to page user-shift-assign/assign */
                    [
                        'actions' => ['assign', 'multiple-assign-form', 'multiple-assign-validation', 'multiple-assign'],
                        'allow' => \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE),
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all ShiftScheduleType models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserShiftAssignListSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'listsAccess' => new ListsAccess(Auth::id()),
        ]);
    }

    public function actionAssign($id)
    {
        if (!$employee = Employee::find()->where(['id' => $id])->limit(1)->one()) {
            throw new NotFoundHttpException('Employee not found by ID (' . $id . ')');
        }

        if (\Yii::$app->request->isPost) {
            try {
                $post = (array) \Yii::$app->request->post();
                if (!($employeeData = $post[$employee->formName()] ?? null) || !($userShiftAssignsData = $employeeData['user_shift_assigns'] ?? null)) {
                    throw new \RuntimeException('Post data is corrupted');
                }
                /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access edit UserShiftAssign */
                if (!\Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE)) {
                    throw new \RuntimeException('ABAC access denied');
                }

                UserShiftAssign::deleteAll(['usa_user_id' => $employee->id]);
                if (!empty($userShiftAssignsData)) {
                    foreach ($userShiftAssignsData as $shiftId) {
                        try {
                            $userShiftAssign = UserShiftAssign::create($employee->id, (int) $shiftId);
                            (new UserShiftAssignRepository($userShiftAssign))->save(true);
                        } catch (\RuntimeException | \DomainException $throwable) {
                            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                'userId'  => $employee->id,
                                'shiftId' => $shiftId,
                            ]);
                            \Yii::warning($message, 'UserShiftAssignController:UserShiftAssign:Exception');
                            \Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
                        } catch (\Throwable $throwable) {
                            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                'userId'  => $employee->id,
                                'shiftId' => $shiftId,
                            ]);
                            \Yii::error($message, 'UserShiftAssignController:UserShiftAssign:Throwable');
                            \Yii::$app->getSession()->setFlash('danger', 'UserShiftAssign not saved');
                        }
                    }
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                    'userId' => $id,
                    'post' => \Yii::$app->request->post(),
                ]);
                \Yii::warning($message, 'UserShiftAssignController:actionAssign:Exception');
                \Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                    'userId' => $id,
                    'post' => \Yii::$app->request->post(),
                ]);
                \Yii::error($message, 'UserShiftAssignController:actionAssign:Throwable');
            }
        }

        $employee->user_shift_assigns = ArrayHelper::map($employee->userShiftAssigns, 'usa_sh_id', 'usa_sh_id');

        return $this->renderAjax('assign', [
            'model' => $employee,
        ]);
    }

    public function actionSelectAll(): Response
    {
        if (!\Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }
        return $this->asJson((new UserShiftAssignListSearch())->searchIds(\Yii::$app->request->queryParams));
    }

    public function actionMultipleAssignForm()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        $result = ['message' => '', 'status' => 0, 'data' => ''];
        $userShiftMultipleAssignForm = new UserShiftMultipleAssignForm();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (\Yii::$app->request->isPost) {
            $data = (array) \Yii::$app->request->post();

            try {
                if (!$userShiftMultipleAssignForm->load($data)) {
                    throw new \RuntimeException('UserShiftMultipleAssignForm not loaded');
                }
                if (!$userShiftMultipleAssignForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userShiftMultipleAssignForm));
                }
                $result['status'] = 1;
                $result['data'] = $this->renderAjax('multiple_assign_form', [
                    'userShiftMultipleAssignForm' => $userShiftMultipleAssignForm,
                ]);
            } catch (\RuntimeException | \DomainException $throwable) {
                $result['message'] = $throwable->getMessage();
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::warning($message, 'UserShiftAssignController:actionMultipleAssignForm:Exception');
            } catch (\Throwable $throwable) {
                $result['message'] = 'Internal Server Error';
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::error($message, 'UserShiftAssignController:actionMultipleAssignForm:Throwable');
            }
        }
        return $result;
    }

    public function actionMultipleAssignValidation(): array
    {
        try {
            $userShiftMultipleAssignForm = new UserShiftMultipleAssignForm();
            if (\Yii::$app->request->isAjax && $userShiftMultipleAssignForm->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($userShiftMultipleAssignForm);
            }
        } catch (\Throwable $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'UserShiftAssignController:actionCMultipleAssignValidation');
        }
        throw new BadRequestHttpException();
    }

    public function actionMultipleAssign()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        $result = ['message' => '', 'status' => 0];

        $userShiftMultipleAssignForm = new UserShiftMultipleAssignForm();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (\Yii::$app->request->isPost) {
            $data = (array) \Yii::$app->request->post();

            try {
                if (!$userShiftMultipleAssignForm->load($data)) {
                    throw new \RuntimeException('UserShiftMultipleAssignForm not loaded');
                }
                if (!$userShiftMultipleAssignForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($userShiftMultipleAssignForm));
                }

                foreach ($userShiftMultipleAssignForm->userIds as $userId) {
                    UserShiftAssign::deleteAll(['usa_user_id' => $userId]);
                    if ($userShiftMultipleAssignForm->shftIds) {
                        foreach ($userShiftMultipleAssignForm->shftIds as $shiftId) {
                            $userShiftAssign = UserShiftAssign::create($userId, (int)$shiftId);
                            (new UserShiftAssignRepository($userShiftAssign))->save();
                        }
                    }
                }

                $result['status'] = 1;
                $result['message'] = 'Shifts assigned';
            } catch (\RuntimeException | \DomainException $throwable) {
                $result['message'] = $throwable->getMessage();
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::warning($message, 'UserShiftAssignController:actionMultipleAssignForm:Exception');
            } catch (\Throwable $throwable) {
                $result['message'] = 'Internal Server Error';
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::error($message, 'UserShiftAssignController:actionMultipleAssignForm:Throwable');
            }
        }
        return $result;
    }
}
