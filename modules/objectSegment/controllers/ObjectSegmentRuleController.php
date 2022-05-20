<?php

namespace modules\objectSegment\controllers;

use frontend\controllers\FController;
use modules\abac\src\entities\AbacPolicy;
use modules\objectSegment\src\entities\ObjectSegmentListQuery;
use modules\objectSegment\src\entities\ObjectSegmentRule;
use modules\objectSegment\src\entities\search\ObjectSegmentRuleSearch;
use modules\objectSegment\src\forms\ObjectSegmentRuleForm;
use src\auth\Auth;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class ObjectSegmentRuleController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all ObjectSegmentRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new ObjectSegmentRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $objectList   = Yii::$app->objectSegment->getObjectList();
        $objectSegmentList = ObjectSegmentListQuery::getObjectList();

        return $this->render('index', [
            'searchModel'       => $searchModel,
            'dataProvider'      => $dataProvider,
            'objectSegmentList' => $objectSegmentList,
            'objectList'        => $objectList
        ]);
    }

    /**
     * Displays a single AbacPolicy model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ObjectSegmentRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ObjectSegmentRuleForm();

        if (Yii::$app->request->isPjax) {
            $osr_osl_id = Yii::$app->request->get('osr_osl_id');
            if (!empty($osr_osl_id)) {
                $model->osr_osl_id = $osr_osl_id;
            }
        }
        $osr               = new ObjectSegmentRule();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $osr->osr_osl_id  = $model->osr_osl_id;
            $osr->osr_title   = $model->osr_title;
            $osr->osr_enabled = $model->osr_enabled;
            if ($model->osr_rule_condition_json) {
                $rulesData                    = @json_decode($model->osr_rule_condition_json);
                $osr->osr_rule_condition_json = \yii\helpers\Json::encode($rulesData);
            }

            if ($osr->save()) {
                Yii::$app->objectSegment->invalidatePolicyCache();
                return $this->redirect(['view', 'id' => $osr->osr_id]);
            } else {
                $model->addErrors($osr->errors);
            }
        } else {
            $model->osr_enabled = true;
        }

        if (Yii::$app->request->get('osr_parent_id')) {
            $parent                         = $this->findModel(Yii::$app->request->get('osr_parent_id'));
            $model->osr_osl_id              = $parent->osr_osl_id;
            $model->osr_title               = $parent->osr_title;
            $model->osr_rule_condition_json = $parent->osr_rule_condition_json;
        }
        return $this->render('create', [
            'model' => $model,
            'osr'   => $osr
        ]);
    }

    /**
     * Updates an existing ObjectSegmentRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $osr           = $this->findModel($id);
        $model         = new ObjectSegmentRuleForm();
        $model->osr_id = $osr->osr_id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $osr->osr_title   = $model->osr_title;
            $osr->osr_enabled = $model->osr_enabled;
            if ($model->osr_rule_condition_json) {
                $rulesData = @json_decode($model->osr_rule_condition_json);
            }

            $osr->osr_rule_condition_json = \yii\helpers\Json::encode($rulesData);

            if ($osr->save()) {
                Yii::$app->objectSegment->invalidatePolicyCache();
                return $this->redirect(['view', 'id' => $osr->osr_id]);
            } else {
                $model->addErrors($osr->errors);
            }
        } else {
            $model->osr_title               = $osr->osr_title;
            $model->osr_rule_condition_json = $osr->osr_rule_condition_json;
            $model->osr_enabled             = $osr->osr_enabled;
            $model->osr_osl_id              = $osr->osr_osl_id;
        }


        return $this->render('update', [
            'model' => $model,
            'osr'   => $osr,
        ]);
    }

    /**
     * Deletes an existing ObjectSegmentRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->objectSegment->invalidatePolicyCache();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectSegmentRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectSegmentRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectSegmentRule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionInvalidateCache()
    {
        if (Yii::$app->objectSegment->invalidatePolicyCache()) {
            Yii::$app->session->setFlash('success', 'Success invalidate Policy Cache');
        } else {
            Yii::$app->session->setFlash('warning', 'Policy Cache is disable');
        }

        return $this->redirect(['index']);
    }
}
