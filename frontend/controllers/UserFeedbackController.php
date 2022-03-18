<?php

namespace frontend\controllers;

use modules\user\userFeedback\abac\dto\UserFeedbackAbacDto;
use modules\user\userFeedback\abac\UserFeedbackAbacObject;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\UserFeedbackFile;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UserFeedbackController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'view'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
        $this->setViewPath('@frontend/views/user/user-feedback');
    }

    /**
     * Lists all UserFeedback models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userFeedbackAbacDto = new UserFeedbackAbacDto();
        /** @abac $userFeedbackAbacDto, UserFeedbackAbacObject::ACT_USER_FEEDBACK_INDEX, UserFeedbackAbacObject::ACTION_ACCESS, Access to view list of  User Feedback*/
        $canViewUserFeedback = \Yii::$app->abac->can(
            $userFeedbackAbacDto,
            UserFeedbackAbacObject::ACT_USER_FEEDBACK_INDEX,
            UserFeedbackAbacObject::ACTION_ACCESS
        );
        if (!$canViewUserFeedback) {
            throw new ForbiddenHttpException('Access denied.');
        }
        $searchModel   = new UserFeedbackSearch();
        $currentUserId = Auth::id();
        $dataProvider  = $searchModel->search($this->request->queryParams, $currentUserId);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserFeedback model.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($uf_id, $uf_created_dt)
    {

        $userFeedbackModel = $this->findModel($uf_id, $uf_created_dt);
        $userFeedbackAbacDto = new UserFeedbackAbacDto($userFeedbackModel, Auth::id());
        /** @abac $userFeedbackAbacDto, UserFeedbackAbacObject::OBJ_USER_FEEDBACK, UserFeedbackAbacObject::ACTION_READ, Access to read user feedback */
        $canViewUserFeedback = \Yii::$app->abac->can(
            $userFeedbackAbacDto,
            UserFeedbackAbacObject::OBJ_USER_FEEDBACK,
            UserFeedbackAbacObject::ACTION_READ
        );
        if (!$canViewUserFeedback) {
            throw new ForbiddenHttpException('Access denied.');
        }
        try {
            $images = UserFeedbackFile::find()->where(['uff_uf_id' => $userFeedbackModel->uf_id])->all();
            return $this->render('view', [
                'model'  => $userFeedbackModel,
                'images' => $images
            ]);
        } catch (\RuntimeException | \DomainException $e) {
            \Yii::warning(AppHelper::throwableFormatter($e), 'UserFeedbackController::actionView:exception');
            return $this->render('_error', [
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'UserFeedbackController:actionView:Throwable');
            return $this->render('_error', [
                'error' => 'Server Error'
            ]);
        }
    }

    /**
     * Finds the UserFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return UserFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($uf_id, $uf_created_dt)
    {
        if (($model = UserFeedback::findOne(['uf_id' => $uf_id, 'uf_created_dt' => $uf_created_dt])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
