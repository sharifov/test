<?php

namespace modules\abac\controllers;

use frontend\controllers\FController;
use modules\abac\src\forms\AbacPolicyForm;
use Yii;
use modules\abac\src\entities\AbacPolicy;
use modules\abac\src\entities\search\AbacPolicySearch;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AbacPolicyController implements the CRUD actions for AbacPolicy model.
 */
class AbacPolicyController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all AbacPolicy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AbacPolicySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
     * Creates a new AbacPolicy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new AbacPolicyForm();

        if (Yii::$app->request->isPjax) {
            $object = Yii::$app->request->get('object');
            if (!empty($object)) {
                $model->ap_object = $object;
                //echo $object; exit;
            }
        }

        $ap = new AbacPolicy();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $ap->ap_object = $model->ap_object;
            $ap->ap_effect = $model->ap_effect;
            $ap->ap_sort_order = $model->ap_sort_order;
            $ap->ap_title = $model->ap_title;
            //VarDumper::dump($model->ap_action_list, 10, true); exit;

            $actionData = [];
            $subjectData = [];

            if ($model->ap_action_list) {
                $actionList = $model->ap_action_list; //@json_encode($model->ap_action_list);
                //VarDumper::dump($actionList, 10, true); exit;
                if ($actionList && is_array($actionList)) {
                    foreach ($actionList as $actionId) {
                        $actionData[] = $actionId;
                    }
                }
                //$ap->ap_action_json = @json_encode($model->ap_action_list);
            }

            if ($model->ap_subject_json) {
                $subjectData = @json_decode($model->ap_subject_json);
            }

            $ap->ap_action_json = \yii\helpers\Json::encode($actionData);
            $ap->ap_subject_json = \yii\helpers\Json::encode($subjectData);

            //VarDumper::dump($model->ap_action_list, 10, true); exit;


            if ($ap->save()) {
                return $this->redirect(['view', 'id' => $ap->ap_id]);
            } else {
                $model->addErrors($ap->errors);
            }
        } else {
            $model->ap_sort_order = 50;
            $model->ap_effect = AbacPolicy::EFFECT_ALLOW;
        }

        return $this->render('create', [
            'model' => $model,
            'ap' => $ap
        ]);
    }

    /**
     * Updates an existing AbacPolicy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {

        $ap = $this->findModel($id);
        $model = new AbacPolicyForm();
        $model->ap_id = $ap->ap_id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //$ap->ap_object = $model->ap_object;
            $ap->ap_effect = $model->ap_effect;
            $ap->ap_sort_order = $model->ap_sort_order;
            $ap->ap_title = $model->ap_title;
            //VarDumper::dump($model->ap_action_list, 10, true); exit;

            $actionData = [];
            $subjectData = [];

            if ($model->ap_action_list) {
                $actionList = $model->ap_action_list; //@json_encode($model->ap_action_list);
                //VarDumper::dump($actionList, 10, true); exit;
                if ($actionList && is_array($actionList)) {
                    foreach ($actionList as $actionId) {
                        $actionData[] = $actionId;
                    }
                }
                //$ap->ap_action_json = @json_encode($model->ap_action_list);
            }

            if ($model->ap_subject_json) {
                $subjectData = @json_decode($model->ap_subject_json);
            }

            $ap->ap_action_json = \yii\helpers\Json::encode($actionData);
            $ap->ap_subject_json = \yii\helpers\Json::encode($subjectData);

            //VarDumper::dump($model->ap_action_list, 10, true); exit;


            if ($ap->save()) {
                return $this->redirect(['view', 'id' => $ap->ap_id]);
            } else {
                $model->addErrors($ap->errors);
            }
        } else {
            $model->ap_sort_order = $ap->ap_sort_order;
            $model->ap_effect = $ap->ap_effect;
            $model->ap_object = $ap->ap_object;
            $model->ap_title = $ap->ap_title;
            $model->ap_action_list = @json_decode($ap->ap_action_json);
            $model->ap_subject_json = $ap->ap_subject_json;
        }


        return $this->render('update', [
            'model' => $model,
            'ap' => $ap,
        ]);
    }

    /**
     * Deletes an existing AbacPolicy model.
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
     * Finds the AbacPolicy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AbacPolicy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AbacPolicy::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
