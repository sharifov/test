<?php

namespace frontend\controllers;

use common\models\Employee;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use modules\user\userFeedback\forms\UserFeedbackBugForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserFeedbackCrudController implements the CRUD actions for UserFeedback model.
 */
class UserFeedbackCrudController extends FController
{
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
        return $this->render('view', [
            'model' => $this->findModel($uf_id, $uf_created_dt),
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
//        $userId = (int) Yii::$app->request->get('user_id', 0);
//        if (!$user = Employee::findOne($userId)) {
//            throw new BadRequestHttpException('Invalid User Id: ' . $userId, 1);
//        }

        $form = new UserFeedbackBugForm();
        $model = new UserFeedback();

        //$model->uf_created_user_id = $userId;

        if ($form->load(Yii::$app->request->post())) {
            //return 'Success <script>$("#modal-lg").modal("hide")</script>';
            /*if ($model->save()) {
                return 'Success <script>$("#modal-lg").modal("hide")</script>';
            }*/
        }
        return $this->renderAjax('create_ajax', [
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
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
