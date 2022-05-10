<?php

namespace modules\objectSegment\controllers;

use frontend\controllers\FController;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentRule;
use modules\objectSegment\src\entities\search\ObjectSegmentListSearch;
use modules\objectSegment\src\forms\ObjectSegmentListForm;
use modules\objectSegment\src\forms\ObjectSegmentRuleForm;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\NotFoundHttpException;

class ObjectSegmentListController extends FController
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

    public function actionIndex()
    {
        $searchModel    = new ObjectSegmentListSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $objectTypeList = Yii::$app->objectSegment->getObjectTypes();

        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $dataProvider,
            'objectTypeList' => $objectTypeList
        ]);
    }

    /**
     * Creates a new ObjectSegmentList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ObjectSegmentListForm();

        $osl = new ObjectSegmentList();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $osl->osl_ost_id      = $model->osl_ost_id;
            $osl->osl_title       = $model->osl_title;
            $osl->osl_key         = $model->osl_key;
            $osl->osl_description = $model->osl_description;
            $osl->osl_enabled     = $model->osl_enabled;
            if ($osl->save()) {
                return $this->redirect(['view', 'id' => $osl->osl_id]);
            } else {
                $model->addErrors($osl->errors);
            }
        } else {
            $model->osl_enabled = true;
        }
        return $this->render('create', [
            'model' => $model,
            'osl'   => $osl
        ]);
    }

    /**
     * Updates an existing ObjectSegmentList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $osl           = $this->findModel($id);
        $model         = new ObjectSegmentListForm();
        $model->osl_id = $osl->osl_id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $osl->osl_title   = $model->osl_title;
            $osl->osl_enabled = $model->osl_enabled;

            if ($osl->save()) {
                return $this->redirect(['view', 'id' => $osl->osl_id]);
            } else {
                $model->addErrors($osl->errors);
            }
        } else {
            $model->osl_title   = $osl->osl_title;
            $model->osl_enabled = $osl->osl_enabled;
            $model->osl_ost_id  = $osl->osl_ost_id;
        }


        return $this->render('update', [
            'model' => $model,
            'osl'   => $osl,
        ]);
    }

    /**
     * Displays a single ObjectSegmentList model.
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
     * Deletes an existing ObjectSegmentList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the ObjectSegmentRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectSegmentList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectSegmentList::findOne($id)) !== null) {
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
