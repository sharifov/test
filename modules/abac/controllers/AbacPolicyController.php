<?php

namespace modules\abac\controllers;

use frontend\controllers\FController;
use modules\abac\src\forms\AbacPolicyForm;
use modules\abac\src\forms\AbacPolicyImportForm;
use sales\auth\Auth;
use Yii;
use modules\abac\src\entities\AbacPolicy;
use modules\abac\src\entities\search\AbacPolicySearch;
use yii\base\BaseObject;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AbacPolicyController implements the CRUD actions for AbacPolicy model.
 */
class AbacPolicyController extends FController
{
    public const SCHEMA_VERSION = '0.1';
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
            $ap->ap_enabled = $model->ap_enabled;
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
            $model->ap_enabled = true;
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
            $ap->ap_enabled = $model->ap_enabled;
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
            $model->ap_enabled = $ap->ap_enabled;
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

    /**
     * @return string
     */
    public function actionListContent()
    {
        $policyListContent = Yii::$app->abac->getPolicyListContent();

        return $this->render('list_content', [
            'policyListContent' => $policyListContent
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionInvalidateCache()
    {
        if (Yii::$app->abac->invalidatePolicyCache()) {
            Yii::$app->session->setFlash('success', 'Success invalidate Policy Cache');
        } else {
            Yii::$app->session->setFlash('warning', 'Policy Cache is disable');
        }

        return $this->redirect(['list-content']);
    }

    public function actionExport()
    {
        $filePath = Yii::getAlias('@runtime/') . 'abac-export.json';

        $dataList = AbacPolicy::find()->all();
        $data = [];
        if ($dataList) {
            foreach ($dataList as $item) {
                $data[] = [
                    'id' => $item->ap_id,
                    'object' => $item->ap_object,
                    'subject' => $item->ap_subject,
                    'subject_json' => $item->ap_subject_json,
                    'action' => $item->ap_action,
                    'action_json' => $item->ap_action_json,
                    'effect' => $item->ap_effect,
                    'sort_order' => $item->ap_sort_order,
                    'title' => $item->ap_title,
                    'enabled' => $item->ap_enabled,
                    'created_dt' => $item->ap_created_dt,
                    'updated_dt' => $item->ap_updated_dt
                ];
            }
        }

        $header['username'] = Auth::user()->username;
        $header['datetime'] = date('Y-m-d H:i:s');
        $header['env'] = YII_ENV;
        $header['app_name'] = Yii::$app->name;
        $header['app_ver'] = Yii::$app->params['release']['version'] ?? '';
        $header['schema_ver'] = self::SCHEMA_VERSION;

        $dataContent['header'] = $header;
        $dataContent['data'] = $data;

        $content = json_encode($dataContent);

        if ($content) {
            file_put_contents($filePath, $content);

            if (file_exists($filePath)) {
                $exportFileName = 'abac-export-' . YII_ENV . '-' . date('Ymd_Hi') . '.json';
                return Yii::$app->response->sendFile($filePath, $exportFileName);
            }
        }
        return false;
    }

    public function actionImport()
    {
        $cache = Yii::$app->cacheFile;
        $model = new AbacPolicyImportForm();
        $header = [];
        $data = [];
        $filePath = '';

        $cacheKey = 'abac-import-' . Yii::$app->user->id;

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($filePath = $model->upload()) {
                if (file_exists($filePath)) {
                    $json = file_get_contents($filePath);
                    $fileData = json_decode($json, true);
                    if ($fileData) {
                        $cache->set($cacheKey, $fileData, 600);
                        $header = $fileData['header'] ?? [];
                        $data = $fileData['data'] ?? [];
                    }
                    unlink($filePath);
                }
            } else {
                $model->addError('importFile', 'Error: Not upload file');
            }
        }

        $fileData = $cache->get($cacheKey);
        if ($fileData !== false) {
            $header = $fileData['header'] ?? [];
            $data = $fileData['data'] ?? [];
        }


        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
                    'enabled' => SORT_DESC,
                ],
                'attributes' => ['sort_order', 'enabled', 'effect', 'created_dt', 'updated_dt', 'object'],
            ],
            'pagination' => [
                'pageSize' => 10000,
            ],
        ]);

        return $this->render('import', [
            'model' => $model,
            'header' => $header,
            'data' => $data,
            'filePath' => $filePath,
            'dataProvider' => $dataProvider
        ]);
    }
}
