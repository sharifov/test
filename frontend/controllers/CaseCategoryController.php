<?php

namespace frontend\controllers;

use src\forms\cases\CaseCategoryCreateForm;
use Yii;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseCategorySearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class CaseCategoryController
 */
class CaseCategoryController extends FController
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CaseCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
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

        $form = new CaseCategoryCreateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $model = new CaseCategory();
            $form->mapAttributes($model);
            if (!is_numeric($form->parentCategoryId)) {
                $model->makeRoot();
            } else {
                $parent = CaseCategory::findNestedSets()->andWhere(['cc_id' => $form->parentCategoryId])->one();
                if ($parent) {
                    $model->appendTo($parent);
                }
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->cc_id]);
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionReport()
    {
        $searchModel = new CaseCategorySearch();
        $dataProvider = $searchModel->prepareReportData(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return CaseCategory
     * @throws NotFoundHttpException
     */
    protected function findModel($id): CaseCategory
    {
        if (($model = CaseCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
