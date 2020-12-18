<?php

namespace frontend\controllers;

use Yii;
use sales\model\project\entity\projectLocale\ProjectLocale;
use sales\model\project\entity\projectLocale\search\ProjectLocaleSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectLocaleController implements the CRUD actions for ProjectLocale model.
 */
class ProjectLocaleController extends FController
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all ProjectLocale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectLocaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectLocale model.
     * @param integer $pl_project_id
     * @param string $pl_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pl_project_id, $pl_language_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($pl_project_id, $pl_language_id),
        ]);
    }

    /**
     * Creates a new ProjectLocale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectLocale();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pl_project_id' => $model->pl_project_id, 'pl_language_id' => $model->pl_language_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectLocale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $pl_project_id
     * @param string $pl_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($pl_project_id, $pl_language_id)
    {
        $model = $this->findModel($pl_project_id, $pl_language_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pl_project_id' => $model->pl_project_id, 'pl_language_id' => $model->pl_language_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectLocale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $pl_project_id
     * @param string $pl_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDefault($pl_project_id, $pl_language_id)
    {
        $model = $this->findModel($pl_project_id, $pl_language_id);
        $model->pl_default = true;

        if ($model->save()) {
            [$lang, $country] = explode('-', $model->pl_language_id);

            //VarDumper::dump($lang); exit;

            if (!empty($lang)) {
                $locales = ProjectLocale::find()->where([
                    'pl_default' => true,
                    'pl_project_id' => $model->pl_project_id
                ])
                    ->andWhere(['<>', 'pl_language_id', $model->pl_language_id])
                    ->andWhere(['like', 'pl_language_id', $lang . '-'])
                    ->all();

                //VarDumper::dump($locales); exit;

                if ($locales) {
                    foreach ($locales as $locale) {
                        $locale->pl_default = false;
                        if (!$locale->update()) {
                            Yii::error($locale->errors, 'ProjectLocale:default:save');
                        }
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'Set default Locale ' . $model->pl_language_id);
           // return $this->redirect(['view', 'pl_project_id' => $model->pl_project_id, 'pl_language_id' => $model->pl_language_id]);
        } else {
            Yii::error($model->errors, 'ProjectLocale:save');
            Yii::$app->session->setFlash('error', 'Error: not set default Locale ' . $model->pl_language_id);
        }

        return $this->redirect(['index']);

//        return $this->render('update', [
//            'model' => $model,
//        ]);
    }

    /**
     * Deletes an existing ProjectLocale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $pl_project_id
     * @param string $pl_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($pl_project_id, $pl_language_id)
    {
        $this->findModel($pl_project_id, $pl_language_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectLocale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $pl_project_id
     * @param string $pl_language_id
     * @return ProjectLocale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($pl_project_id, $pl_language_id)
    {
        if (($model = ProjectLocale::findOne(['pl_project_id' => $pl_project_id, 'pl_language_id' => $pl_language_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
