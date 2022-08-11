<?php

namespace modules\abac\controllers;

use frontend\controllers\FController;
use modules\abac\src\entities\search\AbacPolicyImportSearch;
use modules\abac\src\forms\AbacPolicyForm;
use modules\abac\src\forms\AbacPolicyImportDumpForm;
use modules\abac\src\forms\AbacPolicyImportForm;
use src\auth\Auth;
use Yii;
use modules\abac\src\entities\AbacPolicy;
use modules\abac\src\entities\search\AbacPolicySearch;
use yii\base\BaseObject;
use yii\caching\TagDependency;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
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
                    //'dump-in' => ['POST'],
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

//        $currentPolicyData = [];
//        $currentPolicyList = AbacPolicy::find()->all();
//        if ($currentPolicyList) {
//            foreach ($currentPolicyList as $item) {
//                $currentPolicyData[$item->ap_object][$item->ap_action][$item->ap_effect][$item->ap_subject][$item->ap_enabled] = true;
//            }
//        }

        $abacObjectList = Yii::$app->abac->getObjectList();

        //VarDumper::dump(array_values($abacObjectList)); exit;

        $searchModel = new AbacPolicySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $importCount = count($this->getImportData());

        $duplicatePolicyIds = AbacPolicy::getDuplicateListId();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'importCount' => $importCount,
            'abacObjectList' => $abacObjectList,
            'duplicatePolicyIds' => $duplicatePolicyIds
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
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDump($id)
    {
        return $this->renderAjax('_dump_out', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionDumpIn()
    {

        $model = new AbacPolicyImportDumpForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $policyModel = $model->getPolicyModel();
                if ($policyModel) {
                    $policyModel->ap_enabled = (bool) $model->enabled;
                    if ($policyModel->save()) {
                        Yii::$app->session->setFlash('success', 'Success Import Policy ID: ' . $policyModel->ap_id);
                        return $this->redirect(['index']);
                    } else {
                        $model->addError('dump', $policyModel->firstErrors);
                    }
                }
            }
        } else {
            $model->enabled = true;
        }

        return $this->renderAjax('_dump_in', [
            'model' => $model
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

            $ap->ap_action_json = Json::encode($actionData);
            $ap->ap_subject_json = Json::encode($subjectData);

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

            $ap->ap_action_json = Json::encode($actionData);
            $ap->ap_subject_json = Json::encode($subjectData);

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
            $model->ap_hash_code = $ap->ap_hash_code;
        }


        return $this->render('update', [
            'model' => $model,
            'ap' => $ap,
        ]);
    }


    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCopy($id)
    {
        $ap = $this->findModel($id);

        $newPolicy = new AbacPolicy();
        $model = new AbacPolicyForm();

        if (Yii::$app->request->isPjax) {
            $object = Yii::$app->request->get('object');
            if (!empty($object)) {
                $newPolicy->ap_object = $object;
                $model->ap_object = $object;
                //echo $object; exit;
            }
        } else {
            if (empty($model->ap_object)) {
                $model->ap_object = $ap->ap_object;
                $newPolicy->ap_object = $ap->ap_object;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $newPolicy->ap_effect = $model->ap_effect;
            $newPolicy->ap_sort_order = $model->ap_sort_order;
            $newPolicy->ap_title = $model->ap_title;
            $newPolicy->ap_enabled = $model->ap_enabled;
            $newPolicy->ap_object = $model->ap_object;

            $actionData = [];
            $subjectData = [];

            if ($model->ap_action_list) {
                $actionList = $model->ap_action_list;

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

            $newPolicy->ap_action_json = Json::encode($actionData);
            $newPolicy->ap_subject_json = Json::encode($subjectData);



            if ($newPolicy->save()) {
                return $this->redirect(['view', 'id' => $newPolicy->ap_id]);
            } else {
                $model->addErrors($newPolicy->errors);
            }
        } else {
            $model->ap_sort_order = $ap->ap_sort_order;
            $model->ap_effect = $ap->ap_effect;
            $model->ap_object = $newPolicy->ap_object;
            $model->ap_title = trim($ap->ap_title . ' (Copy of ID: ' . $ap->ap_id . ')');
            $model->ap_action_list = Json::decode($ap->ap_action_json);
            $model->ap_subject_json = $ap->ap_subject_json;
            $model->ap_enabled = $ap->ap_enabled;
        }

        return $this->render('copy', [
            'model' => $model,
            'ap' => $newPolicy,
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
     * @return Response
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

    public function actionClearCache(): Response
    {
        $cacheTagDependency = Yii::$app->abac->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
        }
        TagDependency::invalidate(Yii::$app->cache, [AbacPolicy::CACHE_KEY]);
        return $this->redirect(['/abac/abac-policy']);
    }

    /**
     * @return Response
     */
    public function actionImportIds()
    {

        $cache = Yii::$app->cacheFile;
        $model = new AbacPolicyImportForm();
        $model->scenario = AbacPolicyImportForm::SCENARIO_IDS;
//        $header = [];
//        $data = [];

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if (!empty($model->ids)) {
                    $fileData = $cache->get($this->getCacheKey());
                    if ($fileData !== false) {
                        //$header = $fileData['header'] ?? [];
                        $data = $fileData['data'] ?? [];

                        $currentPolicyData = [];
                        $currentPolicyList = AbacPolicy::find()->all();
                        if ($currentPolicyList) {
                            foreach ($currentPolicyList as $item) {
                                $currentPolicyData[$item->ap_object][$item->ap_action][$item->ap_effect][$item->ap_subject][$item->ap_enabled] = true;
                            }
                        }

                        $addCount = 0;

                        if ($data) {
                            foreach ($data as $key => $row) {
                                if (!in_array($row['id'], $model->ids)) {
                                    continue;
                                }

                                $exist = $currentPolicyData[$row['object']][$row['action']][$row['effect']][$row['subject']] ?? false;
                                if ($exist) {
                                    continue;
                                }

                                $abac = new AbacPolicy();
                                $abac->ap_object = $row['object'];
                                $abac->ap_action = $row['action'];
                                $abac->ap_action_json = $row['action_json'];
                                $abac->ap_subject = $row['subject'];
                                $abac->ap_subject_json = $row['subject_json'];
                                $abac->ap_effect = $row['effect'];
                                $abac->ap_sort_order = (int) $row['sort_order'];
                                $abac->ap_enabled = (bool) $row['enabled'];
                                $abac->ap_object = $row['object'];
                                $abac->ap_created_dt = $row['created_dt'];
                                $abac->ap_updated_dt = date('Y-m-d H:i:s');
                                if (!$abac->save()) {
                                    Yii::error($abac->errors, 'AbacPolicy:actionImportIds:AbacPolicy:save');
                                } else {
                                    $addCount++;
                                }
                            }
                            \Yii::$app->abac->invalidatePolicyCache();
                        }

                        Yii::$app->session->setFlash('success', 'Success Import data (' . $addCount . ' new items)');
                    } else {
                        Yii::$app->session->setFlash('error', 'Expired or not found Import Data cache file');
                    }
                } else {
                    Yii::$app->session->setFlash('warning', 'Not selected policy items');
                }
            } else {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($model->errors));
            }
        }

        return $this->redirect(['import']);
    }

    public function actionImport()
    {
        $cache = Yii::$app->cacheFile;
        $model = new AbacPolicyImportForm();
        $model->scenario = AbacPolicyImportForm::SCENARIO_FILE;
        $header = [];
        $data = [];
        $filePath = '';
        $isCache = false;


        $headerLocal = [];
        $headerLocal['username'] = Auth::user()->username;
        $headerLocal['datetime'] = date('Y-m-d H:i:s');
        $headerLocal['env'] = YII_ENV;
        $headerLocal['app_name'] = Yii::$app->name;
        $headerLocal['app_ver'] = Yii::$app->params['release']['version'] ?? '';
        $headerLocal['schema_ver'] = self::SCHEMA_VERSION;

        $cacheKey = $this->getCacheKey();

        if ($model->load(Yii::$app->request->post())) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');

            //VarDumper::dump($model->importFile); exit;

            if ($model->validate()) {
//                VarDumper::dump(Yii::$app->request->post(), 10, true); // tempName

                if ($model->importFile) {
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
            }
        }

        $fileData = $cache->get($cacheKey);
        if ($fileData !== false) {
            $isCache = true;
            $header = $fileData['header'] ?? [];
            $data = $fileData['data'] ?? [];
        }

        $currentPolicyData = [];
        $currentPolicyList = AbacPolicy::find()->all();
        if ($currentPolicyList) {
            foreach ($currentPolicyList as $item) {
                $currentPolicyData[$item->ap_object][$item->ap_action][$item->ap_effect][$item->ap_subject][$item->ap_enabled] = true;
            }
        }

        $abacObjectList = Yii::$app->abac->getObjectList();

        //VarDumper::dump($abacObjectList, 10, true);

        if ($data) {
            foreach ($data as $key => $row) {
                $exist = $currentPolicyData[$row['object']][$row['action']][$row['effect']][$row['subject']] ?? false;

                $existObject = in_array($row['object'], $abacObjectList);

                if (!$existObject) {
                    $data[$key]['action_id'] = AbacPolicyImportForm::ACT_ERROR;
                    continue;
                }


                $abacActionList = Yii::$app->abac->getActionListByObject($row['object']);

                $actionList = @json_decode($row['action_json'], true);

                if ($actionList) {
                    foreach ($actionList as $actionItem) {
                        $existAction = in_array($actionItem, $abacActionList);
                        if (!$existAction) {
                            $data[$key]['action_id'] = AbacPolicyImportForm::ACT_ERROR;
                            break;
                        }
                    }
                }

                $data[$key]['abac_action_list'] = $abacActionList;

                if (empty($data[$key]['action_id'])) {
                    $data[$key]['action_id'] = $exist ? AbacPolicyImportForm::ACT_EXISTS : AbacPolicyImportForm::ACT_CREATE;
                }
            }
        }

        $searchModel = new AbacPolicyImportSearch();
        $searchModel->setData($data);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);




        return $this->render('import', [
            'model' => $model,
            'header' => $header,
            'headerLocal' => $headerLocal,
            'data' => $data,
            'filePath' => $filePath,
            'dataProvider' => $dataProvider,
            'isCache' => $isCache,
            'abacObjectList' => $abacObjectList,
            'searchModel' => $searchModel
        ]);
    }


    public function actionImportCancel()
    {
        $cache = Yii::$app->cacheFile;
        $cache->delete($this->getCacheKey());
        return $this->redirect(['import']);
    }

    /**
     * @return string
     */
    private function getCacheKey(): string
    {
        return 'abac-import-' . Yii::$app->user->id;
    }

    /**
     * @return array|mixed
     */
    private function getImportData()
    {
        $cache = Yii::$app->cacheFile;
        $data = [];
        $fileData = $cache->get($this->getCacheKey());
        if ($fileData !== false) {
            // $header = $fileData['header'] ?? [];
            $data = $fileData['data'] ?? [];
        }
        return $data;
    }
}
