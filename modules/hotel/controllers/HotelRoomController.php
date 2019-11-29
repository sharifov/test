<?php

namespace modules\hotel\controllers;

use modules\hotel\models\forms\HotelRoomForm;
use modules\hotel\models\Hotel;
use Yii;
use modules\hotel\models\HotelRoom;
use modules\hotel\models\search\HotelRoomSearch;
use frontend\controllers\FController;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * HotelRoomController implements the CRUD actions for HotelRoom model.
 */
class HotelRoomController extends FController
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
     * Lists all HotelRoom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HotelRoomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HotelRoom model.
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
     * Creates a new HotelRoom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HotelRoom();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->hr_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $model = new HotelRoomForm(); //new Product();


        if ($model->load(Yii::$app->request->post())) {

            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                $modelRoom = new HotelRoom();
                $modelRoom->attributes = $model->attributes;

                if ($modelRoom->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-search-' . $modelRoom->hrHotel->ph_product_id . '"});</script>';
                    //return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-hotel-rooms-' . $modelRoom->hr_hotel_id . '"});</script>';
                }
            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {

            $hotelId = (int) Yii::$app->request->get('id');

            if (!$hotelId) {
                throw new BadRequestHttpException('Not found Hotel identity.');
            }

            $hotel = Hotel::findOne($hotelId);
            if (!$hotel) {
                throw new BadRequestHttpException('Not found this hotel');
            }

            $model->hr_hotel_id = $hotelId;
        }

        return $this->renderAjax('create_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HotelRoom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->hr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionUpdateAjax()
    {
        $roomId = (int) Yii::$app->request->get('id');

        try {
            $modelRoom = $this->findModel($roomId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $model = new HotelRoomForm();
        $model->hr_hotel_id = $modelRoom->hr_hotel_id;
        $model->hr_id = $modelRoom->hr_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                //echo 123; exit;
                $modelRoom->hr_room_name = $model->hr_room_name;

                if ($modelRoom->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-search-' . $modelRoom->hrHotel->ph_product_id . '"});</script>';
                }

                Yii::error(VarDumper::dumpAsString($modelRoom->errors), 'HotelRoomController:actionUpdateAjax:HotelRoom:save');
            } /*else {
                VarDumper::dump($model->errors); exit;
            }*/
        } else {
            $model->attributes = $modelRoom->attributes;
        }



        return $this->renderAjax('update_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HotelRoom model.
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
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Hotel Room ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed room (' . $model->hr_id . ')'];
    }

    /**
     * Finds the HotelRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HotelRoom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HotelRoom::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
