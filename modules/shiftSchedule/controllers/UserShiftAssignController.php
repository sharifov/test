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
use src\access\ListsAccess;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
                        'actions' => ['index'],
                        'allow' => \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_ACCESS),
                        'roles' => ['@'],
                    ],
                    /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access to page user-shift-assign/assign */
                    [
                        'actions' => ['assign'],
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

        return $this->render('assign', [
            'model' => $employee,
        ]);
    }
}
