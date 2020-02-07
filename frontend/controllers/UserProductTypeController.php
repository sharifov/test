<?php

namespace frontend\controllers;

use common\models\Employee;
use Throwable;
use Yii;
use common\models\UserProductType;
use common\models\search\UserProductTypeSearch;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserProductTypeController implements the CRUD actions for UserProductType model.
 */
class UserProductTypeController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['user-product-type/list'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'create-ajax'],
                        'roles' => ['user-product-type/create'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'update-ajax'],
                        'roles' => ['user-product-type/update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['user-product-type/delete'],
                    ],
                ]
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

    /**
     * Lists all UserProductType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserProductTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProductType model.
     * @param int $upt_user_id
     * @param int $upt_product_type_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($upt_user_id, $upt_product_type_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($upt_user_id, $upt_product_type_id),
        ]);
    }

    /**
     * Creates a new UserProductType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserProductType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserProductType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $upt_user_id
     * @param int $upt_product_type_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($upt_user_id, $upt_product_type_id)
    {
        $model = $this->findModel($upt_user_id, $upt_product_type_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $upt_user_id
     * @param $upt_product_type_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($upt_user_id, $upt_product_type_id)
    {
        $this->findModel($upt_user_id, $upt_product_type_id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the UserProductType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $upt_user_id
     * @param int $upt_product_type_id
     * @return UserProductType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($upt_user_id, $upt_product_type_id)
    {
        if (($model = UserProductType::findOne(['upt_user_id' => $upt_user_id, 'upt_product_type_id' => $upt_product_type_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax(): string
    {
        $userId = (int) Yii::$app->request->get('user_id', 0);
        if (!$user = Employee::findOne($userId)) {
            throw new BadRequestHttpException('Invalid User Id: ' . $userId, 1);
        }

        $model = new UserProductType();
        $model->upt_user_id = $userId;

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                return 'Success <script>$("#modal-df").modal("hide")</script>';
            }
        }
        return $this->renderAjax('create_ajax', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdateAjax(): string
    {
        $data = Yii::$app->request->get('data');
        $upp_user_id = $data['upt_user_id'] ?? 0;
        $upp_project_id = $data['upt_product_type_id'] ?? 0;

        $model = $this->findModel($upp_user_id, $upp_project_id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                return 'Success <script>$("#modal-df").modal("hide")</script>';
            }
        }
        return $this->renderAjax('update_ajax', [
            'model' => $model,
        ]);
    }
}
