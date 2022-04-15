<?php

namespace frontend\controllers;

use common\models\Employee;
use modules\shiftSchedule\forms\ShiftScheduleForm;
use modules\shiftSchedule\src\entities\shiftScheduleRule\search\SearchShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use src\auth\Auth;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ShiftScheduleRuleCrudController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new SearchShiftScheduleRule();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $model = new ShiftScheduleRule();
        $modelForm = new ShiftScheduleForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->ssr_end_time_loc = date('H:i', strtotime($model->ssr_start_time_loc) +
                ($model->ssr_duration_time * 60));

            if ($model->ssr_timezone) {
                $model->ssr_start_time_utc = Employee::convertToUTC(
                    strtotime($model->ssr_start_time_loc),
                    $model->ssr_timezone
                );
                $model->ssr_end_time_utc = Employee::convertToUTC(
                    strtotime($model->ssr_end_time_utc),
                    $model->ssr_timezone
                );
            } else {
                $model->ssr_start_time_utc = $model->ssr_start_time_loc;
                $model->ssr_end_time_utc = $model->ssr_end_time_loc;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->ssr_id]);
            }
        } else {
            $model->ssr_timezone = Auth::user()->timezone;
            $model->ssr_duration_time = 60;
            $model->ssr_title = 'Schedule Rule ';
            $model->ssr_enabled = true;
            //$model->ssr_cron_expression_exclude = '';
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
            return $this->redirect(['view', 'id' => $model->ssr_id]);
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
     * @return ShiftScheduleRule
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ShiftScheduleRule
    {
        if (($model = ShiftScheduleRule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
