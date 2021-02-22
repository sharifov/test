<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use modules\fileStorage\src\services\FileStorageService;
use modules\fileStorage\src\useCase\fileStorage\rename\RenameForm;
use modules\fileStorage\src\useCase\fileStorage\update\EditForm;
use sales\helpers\app\AppHelper;
use Yii;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\search\FileStorageSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;

/**
 * Class FileStorageController
 *
 * @property FileStorageService $fileStorageService
 * @property FileStorageRepository $fileStorageRepository
 */
class FileStorageController extends FController
{
    private FileStorageService $fileStorageService;
    private FileStorageRepository $fileStorageRepository;

    public function __construct($id, $module, FileStorageService $fileStorageService, FileStorageRepository $fileStorageRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileStorageService = $fileStorageService;
        $this->fileStorageRepository = $fileStorageRepository;
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
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionTitleUpdate(): Response
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            try {
                $titleData = Yii::$app->request->post('file_title');
                $fileStorageId = array_key_first($titleData);
                $value = $titleData[$fileStorageId];

                if (is_int($fileStorageId) && $value !== null) {
                    $model = $this->findModel($fileStorageId);
                    $model->fs_title = $value;
                    $this->fileStorageRepository->save($model);

                    return $this->asJson(['output' => $value]);
                }
                throw new \RuntimeException('FileStorageId is required.', -1);
            } catch (\DomainException $e) {
                return $this->asJson(['message' => $e->getMessage()]);
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger($throwable, 'FileStorageController:actionTitleUpdate');
                return $this->asJson(['message' => $throwable->getMessage()]);
            }
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionDeleteAjax(): array
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['status' => 0, 'message' => ''];
            $fileId = (int) Yii::$app->request->post('file_id');
            try {
                $model = $this->findModel($fileId);
                $this->fileStorageService->remove($model->fs_id);
                $result['status'] = 1;
                $result['message'] = 'Success. Item deleted.';
            } catch (\DomainException $e) {
                $result['message'] = $e->getMessage();
            } catch (\Throwable $throwable) {
                $result['message'] = 'Server error. Try again later.';
                AppHelper::throwableLogger($throwable, 'FileStorageController:actionDeleteAjax');
            }
            return $result;
        }
        throw new BadRequestHttpException();
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

        throw new NotFoundHttpException('The FileStorage not found. ID(' . $id . ')');
    }
}
