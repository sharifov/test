<?php

namespace frontend\controllers;

use common\helpers\LogHelper;
use common\models\Employee;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use modules\user\userFeedback\entity\UserFeedbackData;
use modules\user\userFeedback\entity\UserFeedbackFile;
use modules\user\userFeedback\forms\UserFeedbackBugForm;
use modules\user\userFeedback\UserFeedbackFileRepository;
use modules\user\userFeedback\UserFeedbackRepository;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\app\ReleaseVersionHelper;
use Yii;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * UserFeedbackCrudController implements the CRUD actions for UserFeedback model.
 */
class UserFeedbackCrudController extends FController
{
    private UserFeedbackRepository $userFeedbackRepository;
    private UserFeedbackFileRepository $userFeedbackFileRepository;

    public function __construct(
        $id,
        $module,
        UserFeedbackRepository $userFeedbackRepository,
        UserFeedbackFileRepository $userFeedbackFileRepository,
        $config = []
    ) {
        $this->userFeedbackRepository = $userFeedbackRepository;
        $this->userFeedbackFileRepository = $userFeedbackFileRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => [
                            Employee::ROLE_ADMIN,
                            Employee::ROLE_AGENT,
                        ],
                        'allow' => true,
                    ],
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
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
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
            $transaction = new Transaction(['db' => UserFeedback::getDb()]);
            try {
                $dto = new UserFeedbackData($form->getDecodedData(), [
                    'releaseVersion' => ReleaseVersionHelper::getReleaseVersion(true) ?? '',
                    'host' => Yii::$app->params['appHostname'] ?? '',
                    'pageUrl' => $form->pageUrl,
                    'userIndicatedDate' => $form->date,
                    'userIndicatedTime' => $form->time
                ]);
                $userFeedback = UserFeedback::createNewBug($form->title, $form->message, $dto->toArray());
                $userFeedbackFileId = null;
                if ($form->screenshot) {
                    $userFeedbackFile = UserFeedbackFile::create(
                        $form->getScreenshotMimeType(),
                        $form->getScreenshotSize(),
                        md5($form->title . time()),
                        $form->title,
                        $form->screenshot
                    );
                    $transaction->begin();
                    $userFeedbackId = $this->userFeedbackRepository->save($userFeedback);
                    $userFeedbackFile->uff_uf_id = $userFeedbackId;
                    $this->userFeedbackFileRepository->save($userFeedbackFile);
                    $userFeedbackFileId = $userFeedbackFile->uff_id;
                    $transaction->commit();
                } else {
                    $userFeedbackId = $this->userFeedbackRepository->save($userFeedback);
                }
                Yii::info([
                    'message' => 'User create an bug report',
                    'userId' => Auth::id(),
                    'userFeedbackEntityId' => $userFeedbackId,
                    'userFeedbackFileId' => $userFeedbackFileId
                ], 'info\UserFeedbackCrudController::actionCreateAjax::bugReport');

                return "<script>$('#modal-lg').modal('hide'); createNotify('Success', 'Bug report created successfully', 'success')</script>";
            } catch (\RuntimeException $e) {
                $transaction->rollBack();
                $form->addError('general', $e->getMessage());
            } catch (\Throwable $e) {
                $transaction->rollBack();
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
        $model = $this->findModel($uf_id, $uf_created_dt);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            $model->uf_data_json = @json_decode($model->uf_data_json);
            if ($model->save()) {
                return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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
