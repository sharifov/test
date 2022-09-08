<?php

namespace frontend\controllers;

use src\forms\cases\CaseCategoryManageForm;
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
        $params       = Yii::$app->request->queryParams;
        $searchModel  = new CaseCategorySearch();
        $dataProvider = $searchModel->search($params);

        $parentCategoryId = $params['CaseCategoryManageForm']['parentCategoryId'] ?? 0;

        return $this->render('index', [
          'searchModel'      => $searchModel,
          'dataProvider'     => $dataProvider,
          'parentCategoryId' => $parentCategoryId,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $model                                  = $this->findModel($id);
        $parent                                 = $model->parents(1)->one();
        $this->view->params['parentCategoryId'] = null;
        if ($parent) {
            $this->view->params['parentCategoryId'] = $parent->cc_id;
        }

        return $this->render('view', [
          'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $form           = new CaseCategoryManageForm();
        $form->scenario = CaseCategoryManageForm::SCENARIO_CREATE;
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $model = new CaseCategory();
            $form->mapAttributesToModel($model);
            $this->moveNestedSetModel($model, $form);
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

        $form           = new CaseCategoryManageForm();
        $form->scenario = CaseCategoryManageForm::SCENARIO_UPDATE;

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->mapAttributesToModel($model);
            $this->moveNestedSetModel($model, $form);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->cc_id]);
            }
        }
        $form->mapAttributesFromModel($model);
        $parent = $model->parents(1)->one();
        if ($parent !== null) {
            $form->parentCategoryId = $parent->cc_id;
        }

        return $this->render('update', [
          'model' => $form,
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
        $searchModel  = new CaseCategorySearch();
        $dataProvider = $searchModel->prepareReportData(Yii::$app->request->queryParams);

        return $this->render('report', [
          'searchModel'  => $searchModel,
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

    /**
     * Move the Model to the root of the new tree or attach to the parent
     * @param  \src\entities\cases\CaseCategory  $model
     * @param  \src\forms\cases\CaseCategoryManageForm  $form
     * @return void
     */
    private function moveNestedSetModel(CaseCategory $model, CaseCategoryManageForm $form): void
    {
        /*check if model is not a root already(the root has left attribute equal 1) and parent category id is selected*/
        if (!is_numeric($form->parentCategoryId)) {
            if ($model->cc_lft !== 1) {
                $model->makeRoot();
            }
        } else {
            $parent = CaseCategory::findNestedSets()->andWhere(['cc_id' => $form->parentCategoryId])->one();
            if ($parent) {
                $nestedModel = CaseCategory::findNestedSets()->andWhere(['cc_id' => $model->cc_id])->one();
                $this->appendChildren($nestedModel, $parent);
            }
        }
    }

    private function appendChildren($model, $parent): void
    {
        $children = $model->children(1)->all();
        $model->appendTo($parent);
        if ($children) {
            foreach ($children as $child) {
                $this->appendChildren($child, $model);
            }
        }
    }
}
