<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use modules\fileStorage\src\services\FileStorageService;
use modules\fileStorage\src\useCase\fileStorage\rename\RenameForm;
use modules\fileStorage\src\useCase\fileStorage\update\EditForm;
use Yii;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\search\FileStorageSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class FileStorageController
 *
 * @property FileStorageService $fileStorageService
 */
class FileStorageController extends FController
{
    private FileStorageService $fileStorageService;

    public function __construct($id, $module, FileStorageService $fileStorageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileStorageService = $fileStorageService;
    }

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
        $searchModel = new FileStorageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
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
    public function create()
    {
        $model = new FileStorage();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fs_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $form = new EditForm($model);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->fileStorageService->edit($form);
                Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                Yii::$app->session->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error([
                    'message' => 'File storage edit error',
                    'error' => $e->getMessage(),
                ], 'FileStorageController::actionUpdate');
                Yii::$app->session->addFlash('error', 'Server error. Try again.');
            }
            return $this->redirect(['view', 'id' => $model->fs_id]);
        }

        return $this->render('edit', [
            'form' => $form,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $model = $this->findModel($id);

        try {
            $this->fileStorageService->remove($model->fs_id);
            Yii::$app->session->addFlash('success', 'Success');
        } catch (\DomainException $e) {
            Yii::$app->session->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::$app->session->addFlash('error', 'Server error. Try again later.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionRename($id)
    {
        $model = $this->findModel($id);
        $form = new RenameForm($model);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->fileStorageService->rename($form->fs_id, $form->getName());
                Yii::$app->session->addFlash('success', 'Success');
            } catch (\DomainException $e) {
                Yii::$app->session->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::error([
                    'message' => 'File storage rename error',
                    'error' => $e->getMessage(),
                ], 'FileStorageController::actionRename');
                Yii::$app->session->addFlash('error', 'Server error. Try again.');
            }
            return $this->redirect(['view', 'id' => $model->fs_id]);
        }

        return $this->render('rename', [
            'form' => $form,
        ]);
    }

    /**
     * @param integer $id
     * @return FileStorage
     * @throws NotFoundHttpException
     */
    protected function findModel($id): FileStorage
    {
        if (($model = FileStorage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
