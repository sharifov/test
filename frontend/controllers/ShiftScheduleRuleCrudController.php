<?php

namespace frontend\controllers;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRule\search\SearchShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\forms\ShiftScheduleForm;
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
        $ssrModel = new ShiftScheduleRule();
        $form = new ShiftScheduleForm();

        if ($form->load(Yii::$app->request->post())) {
            $form->setTimeComplete();
            $ssrModel->attributes = $form->attributes;
            if ($form->validate() && $ssrModel->save()) {
                return $this->redirect(['view', 'id' => $ssrModel->ssr_id]);
            }
        } else {
            $form->setDefaultData(Auth::user()->timezone);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $ssrModel = $this->findModel($id);
        $form = new ShiftScheduleForm();
        $form->ssr_id = $ssrModel->ssr_id;

        if ($form->load(Yii::$app->request->post())) {
            $form->setTimeComplete();
            $ssrModel->attributes = $form->attributes;
            if ($form->validate() && $ssrModel->save()) {
                return $this->redirect(['view', 'id' => $ssrModel->ssr_id]);
            }
        } else {
            $form->attributes = $ssrModel->attributes;
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
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
