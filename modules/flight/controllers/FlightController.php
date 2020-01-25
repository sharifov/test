<?php

namespace modules\flight\controllers;

use common\models\Product;
use common\models\ProductType;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\services\flight\FlightManageService;
use modules\hotel\models\Hotel;
use sales\forms\CompositeFormHelper;
use Yii;
use modules\flight\models\Flight;
use modules\flight\models\search\FlightSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * FlightController implements the CRUD actions for Flight model.
 *
 * @property FlightManageService $service
 * @property FlightRepository $flightRepository
 */
class FlightController extends FController
{
	private $service;
	/**
	 * @var FlightRepository
	 */
	private $flightRepository;

	/**
	 * FlightController constructor.
	 * @param $id
	 * @param $module
	 * @param FlightManageService $flightManageService
	 * @param FlightRepository $flightRepository
	 * @param array $config
	 */
	public function __construct($id, $module, FlightManageService $flightManageService, FlightRepository $flightRepository, $config = [])
	{
		parent::__construct($id, $module, $config);

		$this->service = $flightManageService;
		$this->flightRepository = $flightRepository;
	}

	/**
     * {@inheritdoc}
     */
    public function behaviors()
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
     * Lists all Flight models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Flight model.
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
     * Creates a new Flight model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Flight();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Flight model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * @return string
	 * @throws ForbiddenHttpException
	 * @throws NotFoundHttpException
	 */
    public function actionAjaxUpdateItineraryView(): string
	{
		$id = Yii::$app->request->get('id');
		$pjaxIdWrap = Yii::$app->request->post('pjaxIdWrap');

		$flight = Flight::findOne($id);

		if (!$flight) {
			throw new NotFoundHttpException();
		}

		$form = new ItineraryEditForm($flight);
		return $this->renderAjax('update_ajax', [
			'itineraryForm' => $form,
			'pjaxIdWrap' => $pjaxIdWrap
		]);
	}

	/**
	 * @return string
	 * @throws ForbiddenHttpException
	 * @throws NotFoundHttpException
	 */
    public function actionAjaxUpdateItinerary(): string
    {
		$id = Yii::$app->request->post('flightId');
		$pjaxIdWrap = Yii::$app->request->post('pjaxIdWrap');
		$flight = $this->findModel($id);

		$data = CompositeFormHelper::prepareDataForMultiInput(
			Yii::$app->request->post(),
			'ItineraryEditForm',
			['segments' => 'FlightSegmentEditForm']
		);
		$form = new ItineraryEditForm($flight, count($data['post']['FlightSegmentEditForm']));

		if ($form->load($data['post']) && $form->validate()) {
			try {
				$this->service->editItinerary($id, $form);
				Yii::$app->session->setFlash('success', 'Segments saved.');

				return '<script>$("#modal-md").modal("hide"); $.pjax.reload({container: "#'.$pjaxIdWrap.'", url: "/flight/flight/pjax-flight-request-view?pr_id='.$flight->fl_product_id.'", push: false, replace: false})</script>';
			} catch (\Exception | \Throwable $e) {
				Yii::$app->errorHandler->logException($e);
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
		}

        return $this->renderAjax('update_ajax', [
            'itineraryForm' => $form,
			'pjaxIdWrap' => $pjaxIdWrap
        ]);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionPjaxFlightRequestView(): string
	{
		$id = Yii::$app->request->get('pr_id');
		$product = Product::findOne($id);
		return $this->renderAjax('partial/_product_flight', ['product' => $product, 'pjaxRequest' => true]);
	}

    /**
     * Deletes an existing Flight model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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
                throw new Exception('Product ('.$id.') not deleted', 2);
            }

            if ((int) $model->pr_type_id === ProductType::PRODUCT_HOTEL && class_exists('\modules\hotel\HotelModule')) {
                $modelHotel = Hotel::findOne(['ph_product_id' => $model->pr_id]);
                if ($modelHotel) {
                    if (!$modelHotel->delete()) {
                        throw new Exception('Hotel (' . $modelHotel->ph_id . ') not deleted', 3);
                    }
                }
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product (' . $model->pr_id . ')'];
    }

    /**
     * Finds the Flight model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Flight the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Flight::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
