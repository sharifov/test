<?php

namespace modules\objectSegment\controllers;

use common\components\bootstrap4\activeForm\ActiveForm;
use frontend\controllers\FController;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentRule;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\entities\search\ObjectSegmentListSearch;
use modules\objectSegment\src\forms\ObjectSegmentListAssignForm;
use modules\objectSegment\src\forms\ObjectSegmentListForm;
use modules\objectSegment\src\forms\ObjectSegmentRuleForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use yii\db\ActiveRecord;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ObjectSegmentListController extends FController
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
                    'assign-form' => ['POST'],
                    'assign-validation' => ['POST'],
                    'assign' => ['POST'],
                ],
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['assign-form', 'assign-validation', 'assign']
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


    public function actionAssignForm(): Response
    {
        $result = ['message' => '', 'status' => 0, 'data' => ''];
        $modelForm = new ObjectSegmentListAssignForm();

        if ($modelForm->load(Yii::$app->request->post(), '')) {
            $data = (array) Yii::$app->request->post();

            try {
                if (!$modelForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($modelForm));
                }

                $modelForm->taskIds = ObjectSegmentTask::getAssignedTaskIds($modelForm->objectSegmentId);

                $result['status'] = 1;
                $result['data'] = $this->renderAjax('assign_form', [
                    'model' => $modelForm,
                    'objectSegment' => ObjectSegmentList::findOne(['osl_id' => $modelForm->objectSegmentId]),
                ]);
            } catch (\RuntimeException | \DomainException $throwable) {
                $result['message'] = $throwable->getMessage();
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::warning($message, 'ObjectSegmentListController:actionAssignForm:Exception');
            } catch (\Throwable $throwable) {
                $result['message'] = 'Internal Server Error';
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::error($message, 'ObjectSegmentListController:actionAssignForm:Throwable');
            }
        }

        return $this->asJson($result);
    }

    public function actionAssignValidation(): Response
    {
        try {
            $objectSegmentListAssignForm = new ObjectSegmentListAssignForm();

            if ($objectSegmentListAssignForm->load(Yii::$app->request->post())) {
                return $this->asJson(
                    ActiveForm::validate($objectSegmentListAssignForm)
                );
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'ObjectSegmentListController:actionAssignValidation');
        }

        throw new BadRequestHttpException();
    }

    public function actionAssign(): Response
    {
        $result = ['message' => '', 'status' => 0];
        $objectSegmentListAssignForm = new ObjectSegmentListAssignForm();
        $postData = (array) Yii::$app->request->post();

        try {
            if ($objectSegmentListAssignForm->load($postData)) {
                if ($objectSegmentListAssignForm->validate() === false) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($objectSegmentListAssignForm));
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    ObjectSegmentTask::deleteOrAddTasks(
                        $objectSegmentListAssignForm->objectSegmentId,
                        $objectSegmentListAssignForm->taskIds
                    );
                    $transaction->commit();

                    $result['status'] = 1;
                    $result['message'] = 'Tasks assigned';
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    $result['message'] = $e->getMessage();
                    $message = ArrayHelper::merge(AppHelper::throwableLog($e), $postData);
                    Yii::warning($message, 'ObjectSegmentListController:actionAssign:Exception');
                }
            } else {
                throw new \RuntimeException('ObjectSegmentListAssignForm not loaded');
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $result['message'] = $throwable->getMessage();
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $postData);
            Yii::warning($message, 'ObjectSegmentListController:actionAssign:Exception');
        } catch (\Throwable $throwable) {
            $result['message'] = 'Internal Server Error';
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $postData);
            Yii::error($message, 'ObjectSegmentListController:actionAssign:Throwable');
        }

        return $this->asJson($result);
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
                Yii::$app->objectSegment->invalidatePolicyCache();
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
                Yii::$app->objectSegment->invalidatePolicyCache();
                return $this->redirect(['view', 'id' => $osl->osl_id]);
            } else {
                $model->addErrors($osl->errors);
            }
        } else {
            $model->osl_title       = $osl->osl_title;
            $model->osl_enabled     = $osl->osl_enabled;
            $model->osl_ost_id      = $osl->osl_ost_id;
            $model->osl_description = $osl->osl_description;
            $model->osl_key         = $osl->osl_key;
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
        $model = $this->findModel($id);

        if (!$model->osl_is_system) {
            $model->delete();
            Yii::$app->objectSegment->invalidatePolicyCache();
        } else {
            Yii::$app->session->setFlash('error', 'You can\'t delete system item');
        }

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
