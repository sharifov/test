<?php

namespace frontend\controllers;

use common\models\Employee;
use frontend\widgets\multipleUpdate\userFeedback\MultipleUpdateForm;
use frontend\widgets\multipleUpdate\userFeedback\MultipleUpdateService;
use modules\user\src\abac\dto\UserAbacDto;
use modules\user\src\abac\UserAbacObject;
use modules\user\userFeedback\abac\dto\UserFeedbackAbacDto;
use modules\user\userFeedback\abac\UserFeedbackAbacObject;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use modules\user\userFeedback\entity\UserFeedbackData;
use modules\user\userFeedback\entity\UserFeedbackFile;
use modules\user\userFeedback\forms\UserFeedbackBugForm;
use modules\user\userFeedback\forms\UserFeedbackResolutionForm;
use modules\user\userFeedback\forms\UserFeedbackUpdateForm;
use modules\user\userFeedback\service\UserFeedbackService;
use modules\user\userFeedback\UserFeedbackFileRepository;
use modules\user\userFeedback\UserFeedbackRepository;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\app\ReleaseVersionHelper;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserFeedbackCrudController implements the CRUD actions for UserFeedback model.
 */
class UserFeedbackCrudController extends FController
{
    private UserFeedbackRepository $userFeedbackRepository;
    private UserFeedbackService $userFeedbackService;
    private UserFeedbackFileRepository $userFeedbackFileRepository;
    private MultipleUpdateService $multipleUpdateService;

    public function __construct(
        $id,
        $module,
        UserFeedbackRepository $userFeedbackRepository,
        UserFeedbackService $userFeedbackService,
        UserFeedbackFileRepository $userFeedbackFileRepository,
        MultipleUpdateService $multipleUpdateService,
        $config = []
    ) {
        $this->userFeedbackRepository = $userFeedbackRepository;
        $this->userFeedbackService = $userFeedbackService;
        $this->userFeedbackFileRepository = $userFeedbackFileRepository;
        $this->multipleUpdateService = $multipleUpdateService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'create-ajax',
                ],
            ],
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
//        echo $this->uniqueId;
        $this->setViewPath('@frontend/views/user/user-feedback-crud');
    }

    /**
     * Lists all UserFeedback models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserFeedbackSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserFeedback model.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($uf_id, $uf_created_dt)
    {
        $model = $this->findModel($uf_id, $uf_created_dt);
        $images = UserFeedbackFile::find()->where(['uff_uf_id' => $model->uf_id])->all();
        return $this->render('view', [
            'model' => $model,
            'images' => $images
        ]);
    }

    /**
     * Creates a new UserFeedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserFeedback();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                try {
                    $this->userFeedbackRepository->save($model);
                    return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
                } catch (\RuntimeException $e) {
                    Yii::warning([
                        'message' => $e->getMessage(),
                        'trace' => AppHelper::throwableLog($e, true)
                    ], 'UserFeedbackCrudController:actionCreate:RuntimeException');
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @return string
     */
    public function actionCreateAjax(): string
    {
        $userFeedbackAbacDto =  new UserFeedbackAbacDto();
        /** @abac $userFeedbackAbacDto, UserFeedbackAbacObject::OBJ_USER_FEEDBACK, UserFeedbackAbacObject::ACTION_CREATE, Access to create User Feedback*/
        if (!Yii::$app->abac->can($userFeedbackAbacDto, UserFeedbackAbacObject::OBJ_USER_FEEDBACK, UserFeedbackAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $form = new UserFeedbackBugForm();

        $form->title = Yii::$app->request->post('title', null);
        $form->pageUrl = Yii::$app->request->referrer;
        $user = Auth::user();
        try {
            $timezone = new \DateTimeZone($user->timezone);
        } catch (\Throwable $e) {
            $timezone = null;
        }
        $dateNow = new \DateTimeImmutable('now', $timezone);
        $form->date = $dateNow->format('Y-m-d');
        $form->time = $dateNow->format('H:i');
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $dto = new UserFeedbackData($form->getDecodedData() ?? [], [
                    'releaseVersion' => ReleaseVersionHelper::getReleaseVersion(true) ?? '',
                    'host' => Yii::$app->params['appHostname'] ?? '',
                    'pageUrl' => $form->pageUrl,
                    'userIndicatedDate' => $form->date,
                    'userIndicatedTime' => $form->time
                ]);
                $userFeedbackFileId = null;
                if ($form->screenshot) {
                    $userFeedbackFile = UserFeedbackFile::create(
                        $form->getScreenshotMimeType(),
                        $form->getScreenshotSize(),
                        md5($form->title . time()),
                        $form->title,
                        $form->screenshot
                    );
                    /**
                     * @throws \Throwable
                     */
                    $userFeedbackId = $this->userFeedbackService->create($form, $dto);
                    $userFeedbackFile->uff_uf_id = $userFeedbackId;
                    $this->userFeedbackFileRepository->save($userFeedbackFile);
                    $userFeedbackFileId = $userFeedbackFile->uff_id;
                } else {
                    $userFeedbackId = $this->userFeedbackService->create($form, $dto);
                }
                Yii::info([
                    'message' => 'User create an bug report',
                    'userId' => Auth::id(),
                    'userFeedbackEntityId' => $userFeedbackId,
                    'userFeedbackFileId' => $userFeedbackFileId
                ], 'info\UserFeedbackCrudController::actionCreateAjax::bugReport');

                return "<script>$('#modal-lg').modal('hide');";
            } catch (\RuntimeException $e) {
                $form->addError('general', $e->getMessage());
            } catch (\Throwable $e) {
                $form->addError('general', 'Internal Server error');
                Yii::error([
                    'message' => $e->getMessage(),
                    'userId' => $user->id,
                    'trace' => AppHelper::throwableLog($e, true)
                ], 'UserFeedbackCrudController::actionCreateAjax::create');
            }
        }
        return $this->renderAjax('create_bug_ajax', [
            'model' => $form,
        ]);
    }


    /**
     * Updates an existing UserFeedback model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($uf_id, $uf_created_dt)
    {
        try {
            $model = $this->findModel($uf_id, $uf_created_dt);
            if ($this->request->isPost) {
                $form = new UserFeedbackUpdateForm();
                $form->load($this->request->post());
                if (!$form->validate()) {
                    throw new \RuntimeException(implode(', ', $form->getErrorSummary(true)));
                }
                /**
                 * @throws \Throwable
                 */
                $this->userFeedbackService->update($model, $this->request->post());
                return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        } catch (\RuntimeException | \DomainException $e) {
            Yii::warning(AppHelper::throwableFormatter($e), 'UserFeedbackCrudController::actionUpdate:exception');
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserFeedbackCrudController:actionUpdate:Throwable');
            return $this->renderAjax('_error', [
                'error' => 'Server Error'
            ]);
        }
    }

    public function actionResolve($uf_id, $uf_created_dt)
    {
        try {
            $model = $this->findModel($uf_id, $uf_created_dt);
            if ($this->request->isPost) {
                $form = new UserFeedbackResolutionForm();
                $form->load($this->request->post());
                if (!$form->validate()) {
                    throw new \RuntimeException(implode(', ', $form->getErrorSummary(true)));
                }
                /**
                 * @throws \Throwable
                 */
                $this->userFeedbackService->resolve($model, $form->uf_resolution, $form->uf_status_id, Auth::id());
                return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
            }
            return $this->render('resolution', [
                'model' => $model,
            ]);
        } catch (\RuntimeException | \DomainException $e) {
            Yii::warning(AppHelper::throwableFormatter($e), 'UserFeedbackCrudController::actionResolve:exception');
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserFeedbackCrudController:actionResolve:Throwable');
            return $this->renderAjax('_error', [
                'error' => 'Server Error'
            ]);
        }
    }

    public function actionMultipleUpdateShow()
    {
        $userAbacDto = new UserAbacDto('username');
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, Username field view*/
        if (!Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        try {
            return $this->renderAjax('_multiple_update_show', [
                'validationUrl' => ['/user-feedback-crud/validation'],
                'action' => ['/user-feedback-crud/multiple-update'],
                'modalId' => 'modal-df',
                'ids' => Yii::$app->request->post('ids'),
                'pjaxId' => 'feedback-pjax-list',
                'user' => Auth::user()
            ]);
        } catch (\DomainException $e) {
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error($e, 'UserFeedbackCrudController:actionMultipleUpdateShow');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionValidation(): array
    {
        $userAbacDto = new UserAbacDto('username');
        /** @abac new $userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE, Username field view*/
        if (!Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $user = Auth::user();

        $form = new MultipleUpdateForm($user);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionMultipleUpdate(): Response
    {
        $userAbacDto = new UserAbacDto('username');
        /** @abac new $userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE, Username field view*/
        if (!Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FEEDBACK, UserAbacObject::ACTION_MULTIPLE_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $user = Auth::user();

        $form = new MultipleUpdateForm($user);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $report = $this->multipleUpdateService->update($form);
            return $this->asJson([
                'success' => true,
                'message' => count($report) . ' rows affected.' . $this->multipleUpdateService->formatReport($report),
            ]);
        }
        throw new BadRequestHttpException();
    }

    /**
     * Deletes an existing UserFeedback model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($uf_id, $uf_created_dt)
    {
        $this->findModel($uf_id, $uf_created_dt)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $uf_id ID
     * @param string $uf_created_dt Created Dt
     * @return UserFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($uf_id, $uf_created_dt)
    {
        if (($model = UserFeedback::findOne(['uf_id' => $uf_id, 'uf_created_dt' => $uf_created_dt])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
