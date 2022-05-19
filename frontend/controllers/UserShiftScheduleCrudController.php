<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\UserShiftScheduleMultipleUpdateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserShiftScheduleCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        $searchModel = new SearchUserShiftSchedule();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $multipleForm = new UserShiftScheduleMultipleUpdateForm();
        $multipleErrors = [];

        if ($multipleForm->load(Yii::$app->request->post()) && $multipleForm->validate()) {
            if (\is_array($multipleForm->shift_list)) {
                foreach ($multipleForm->shift_list as $shiftId) {
                    $shiftId = (int) $shiftId;
                    $shiftSchedule = UserShiftSchedule::findOne($shiftId);

                    if (!$shiftSchedule) {
                        continue;
                    }

                    if ($multipleForm->uss_sst_id !== null) {
                        $shiftSchedule->uss_sst_id = $multipleForm->uss_sst_id;
                    }

                    if ($multipleForm->uss_start_utc_dt !== null) {
                        $shiftSchedule->uss_start_utc_dt = $multipleForm->uss_start_utc_dt;
                    }

                    if ($multipleForm->uss_end_utc_dt !== null) {
                        $shiftSchedule->uss_end_utc_dt = $multipleForm->uss_end_utc_dt;
                    }

                    if ($multipleForm->uss_start_utc_dt !== null || $shiftSchedule->uss_end_utc_dt !== null) {
                        $shiftSchedule->uss_duration = UserShiftScheduleHelper::getDurationForDates($shiftSchedule);
                    }

                    if ($multipleForm->uss_type_id !== null) {
                        $shiftSchedule->uss_type_id = $multipleForm->uss_type_id;
                    }

                    if ($multipleForm->uss_status_id !== null) {
                        $shiftSchedule->uss_status_id = $multipleForm->uss_status_id;
                    }

                    if ($multipleForm->uss_shift_id !== null) {
                        $shiftSchedule->uss_shift_id = $multipleForm->uss_shift_id;
                    }

                    if ($multipleForm->uss_ssr_id !== null) {
                        $shiftSchedule->uss_ssr_id = $multipleForm->uss_ssr_id;
                    }

                    if ($multipleForm->uss_user_id !== null) {
                        $shiftSchedule->uss_user_id = $multipleForm->uss_user_id;
                    }

                    if ($shiftSchedule->save() === false) {
                        $multipleErrors[$shiftId] = $shiftSchedule->getErrors();
                    }
                }
            }
        }


        if (Yii::$app->request->get('act') === 'select-all') {
            $data = $searchModel->searchIds(Yii::$app->request->queryParams);

            return $this->asJson($data);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'multipleForm' => $multipleForm,
            'multipleErrors' => $multipleErrors
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserShiftSchedule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->uss_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->uss_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return UserShiftSchedule
     * @throws NotFoundHttpException
     */
    protected function findModel($id): UserShiftSchedule
    {
        if (($model = UserShiftSchedule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
