<?php

namespace frontend\controllers;

use frontend\helpers\JsonHelper;
use sales\forms\segment\SegmentBaggageForm;
use sales\helpers\app\AppHelper;
use sales\model\call\entity\callCommand\types\CommandList;
use sales\model\call\services\CallCommandTypeService;
use sales\model\call\services\CommandListService;
use Yii;
use sales\model\call\entity\callCommand\CallCommand;
use sales\model\call\entity\callCommand\search\CallCommandSearch;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * CallCommandController implements the CRUD actions for CallCommand model.
 */
class CallCommandController extends FController
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
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all CallCommand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallCommandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CallCommand model.
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
     * Creates a new CallCommand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CallCommand();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ((int) $model->ccom_type_id === CallCommand::TYPE_COMMAND_LIST) {
                CommandListService::childrenSaver($model);
            }

            return $this->redirect(['view', 'id' => $model->ccom_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CallCommand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ((int) $model->ccom_type_id === CallCommand::TYPE_COMMAND_LIST) {
                CallCommand::deleteAll(['ccom_parent_id' => $id]);
                CommandListService::childrenSaver($model);
            }

            return $this->redirect(['view', 'id' => $model->ccom_id]);
        }

        $model->ccom_params_json = JsonHelper::encode($model->ccom_params_json);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CallCommand model.
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

    public function actionGetTypeForm(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['status' => 0, 'message' => '', 'template' => ''];
            $typeId = Yii::$app->request->post('type_id', 0);
            $modelId = Yii::$app->request->get('model_id', null);
            $index = Yii::$app->request->post('index', '');

            try {
                $callCommandService = new CallCommandTypeService($typeId);
                $typeObj = $callCommandService->initTypeCommandClass();

                if ($modelId && $model = $this->findModel($modelId)) {

                    if ((int) $typeId === CallCommand::TYPE_COMMAND_LIST) {
                        $typeObj = $callCommandService::fillCommandList($typeObj, $model->ccom_params_json);
                    } else {
                        $typeObj = $callCommandService::fillObject($typeObj, $model->ccom_params_json);
                    }
                }

                $callCommandService->checkTemplateFileExist();

                $result['template'] = $this->renderAjax(
                    $callCommandService->getPathToTemplateFile(),
                    ['model' => $typeObj, 'typeId' => $typeId, 'index' => $index]
                );
                $result['status'] = 1;

            } catch (\Throwable $throwable) {
                $result['message'] = $throwable->getMessage();
                AppHelper::throwableLogger($throwable,
                'CallCommandController:actionGetTypeForm:throwable', false);
                ob_clean();
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    public function actionGetListSubForms(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $template = '';
            $result = ['status' => 0, 'message' => '', 'template' => $template];
            $modelId = Yii::$app->request->get('parent_id', 0);

            try {
                if ($parentModel = $this->findModel($modelId)) {

                    if (count($parentModel->callCommands)) {
                        foreach ($parentModel->callCommands as $key => $childModel) {
                            $callCommandService = new CallCommandTypeService($childModel->ccom_type_id);
                            $typeObj = $callCommandService->initTypeCommandClass();
                            $typeObj = $callCommandService::fillObject($typeObj, $childModel->ccom_params_json);
                            $callCommandService->checkTemplateFileExist();

                            $sortIdentifier = $childModel->ccom_sort_order;

                            $template .= '<div id="box_' . $sortIdentifier . '" data-sort="' . $sortIdentifier . '" class="sub_forms">';
                            $template .= '<div id="content_' . $sortIdentifier . '">';
                            $template .= $this->renderAjax(
                                $callCommandService->getPathToTemplateFile(),
                                [
                                    'model' => $typeObj,
                                    'typeId' => $childModel->ccom_type_id,
                                    'index' => $sortIdentifier
                                ]
                            );
                            $template .= '</div></div><hr />';
                        }
                    }
                }

                $result['template'] = $template;
                $result['status'] = 1;

            } catch (\Throwable $throwable) {
                $result['message'] = $throwable->getMessage();
                AppHelper::throwableLogger($throwable,
                'CallCommandController:actionGetListSubForms:throwable', false);
                ob_clean();
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    public function actionValidateTypeForm(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $typeId = Yii::$app->request->get('type_id', 0);

            try {
                $callCommandService = new CallCommandTypeService($typeId);
                $model = $callCommandService->initTypeCommandClass();

                if ($model->load(Yii::$app->request->post())) {
                    return ActiveForm::validate($model);
                }
                throw new \DomainException('Model "' . $typeId . '" not loaded post data');

            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger($throwable,
                'CallCommandController:actionCheckTypeForm:throwable', true);
            }
        }
        throw new BadRequestHttpException();
    }

    public function actionValidateCommandListForm(): array
    {
        try {
            if ($typeId = Yii::$app->request->get('type_id')) {

                $callCommandService = new CallCommandTypeService($typeId);
                /** @var CommandList $mainModel */
                $mainModel = $callCommandService->initTypeCommandClass();
                $formName = $mainModel->formName();
                $post = Yii::$app->request->post();

                if (!empty($post[$formName]['multipleFormData'])) {
                    $models = [];
                    foreach ($post[$formName]['multipleFormData'] as $key => $value) {
                        $model = $callCommandService->initTypeCommandClass();
                        $model->setAttributes($value);
                        $models[$key] = $model;
                    }

                    Yii::$app->response->format = Response::FORMAT_JSON;

                    Model::loadMultiple($models, Yii::$app->request->post());
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validateMultiple($models);
                }
                throw new \DomainException('MultipleFormData Undefined.', -1);
            }
            throw new \InvalidArgumentException('TypeId is required.', -2);

        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable,
            'CallCommandController:actionValidateCommandListForm', true);
        }
        return [];
    }

    public function actionValidateCommandForm()
    {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new CallCommand(); /* TODO:: add ID if update */

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                return ['success' => true];
            }

            $result = [];
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }
            return ['validation' => $result];
        }
        throw new BadRequestHttpException();
    }

    /**
     * Finds the CallCommand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CallCommand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CallCommand::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
