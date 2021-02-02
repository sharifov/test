<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileUser\FileUser;
use modules\fileStorage\src\entity\fileUser\search\FileUserSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use Exception;

class FileUserController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new FileUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fus_fs_id
     * @param integer $fus_user_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fus_fs_id, $fus_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fus_fs_id, $fus_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileUser();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fus_fs_id' => $model->fus_fs_id, 'fus_user_id' => $model->fus_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fus_fs_id
     * @param integer $fus_user_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fus_fs_id, $fus_user_id)
    {
        $model = $this->findModel($fus_fs_id, $fus_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fus_fs_id' => $model->fus_fs_id, 'fus_user_id' => $model->fus_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fus_fs_id
     * @param integer $fus_user_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fus_fs_id, $fus_user_id): Response
    {
        $this->findModel($fus_fs_id, $fus_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fus_fs_id
     * @param integer $fus_user_id
     * @return FileUser
     * @throws NotFoundHttpException
     */
    protected function findModel($fus_fs_id, $fus_user_id): FileUser
    {
        try {
            if (($model = FileUser::findOne(['fus_fs_id' => $fus_fs_id, 'fus_user_id' => $fus_user_id])) !== null) {
                return $model;
            }
            throw new NotFoundHttpException('The requested page does not exist');
        } catch (Exception $exception) {
            throw new NotFoundHttpException('The requested page does not exist');
        }
    }
}
