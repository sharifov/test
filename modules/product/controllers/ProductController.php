<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\forms\ProductUpdateForm;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\helpers\app\AppHelper;
use Yii;
use common\models\Lead;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productType\ProductType;
use modules\hotel\models\Hotel;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class ProductController
 *
 * @property ProductCreateService $productCreateService
 * @property ProductRepository $productRepository
 */
class ProductController extends FController
{
    private $productCreateService;
    private $productRepository;

    /**
     * ProductController constructor.
     * @param $id
     * @param $module
     * @param ProductCreateService $productCreateService
     * @param ProductRepository $productRepository
     * @param array $config
     */
    public function __construct($id, $module, ProductCreateService $productCreateService, ProductRepository $productRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->productCreateService = $productCreateService;
        $this->productRepository = $productRepository;
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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $form = new ProductCreateForm();

        if ($form->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            if (!$form->validate()) {
                return ['errors' => \yii\widgets\ActiveForm::validate($form)];
            }

            try {
                $this->productCreateService->create($form);
                return ['message' => 'Successfully added a new product'];
            } catch (\DomainException $e) {
                Yii::error($e, 'ProductController:actionCreateAjax:DomainException');
                return ['errors' => $e->getMessage()];
            } catch (\Throwable $e) {
                Yii::error($e, 'ProductController:actionCreateAjax:Throwable');
                return ['errors' => 'Server error'];
            }

        } else {

            $leadId = (int)Yii::$app->request->get('id');
            $typeId = (int)Yii::$app->request->get('typeId');

            if (!$leadId) {
                throw new BadRequestHttpException('Not found lead identity.');
            }

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                throw new BadRequestHttpException('Not found this lead');
            }

            $form->pr_lead_id = $leadId;
            $form->pr_type_id = $typeId;
        }

        return $this->renderAjax('_ajax_form', [
            'model' => $form,
        ]);
    }

    /**
     * @return array|string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateAjax()
    {
        $id = (int) Yii::$app->request->get('id');
        if (!$id) {
            throw new BadRequestHttpException('Param ID required');
        }
        $form = new ProductUpdateForm();
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            if ($form->load(Yii::$app->request->post())) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                $result = ['status' => 0, 'message' => ''];

                if (!$form->validate()) {
                    $result['message'] = ActiveForm::validate($form);
                    return $result;
                }

                try {
                    $model->setName($form->pr_name)
                          ->setDescription($form->pr_description);

                    $this->productRepository->save($model);

                    $result['status'] = 1;
                    $result['message'] = 'Product updated';
                    return $result;
                } catch (\Throwable $throwable) {
                    Yii::error(AppHelper::throwableFormatter($throwable), 'ProductController:' . __FUNCTION__ );
                    return ['message' => $throwable->getMessage()];
                }
            }
        } else {
            $form->pr_name = $model->pr_name;
            $form->pr_description = $model->pr_description;
        }

        return $this->renderAjax('_update_ajax_form', [
            'formModel' => $form,
            'model' => $model,
        ]);
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
                throw new Exception('Product (' . $id . ') not deleted', 2);
            }

            if ((int)$model->pr_type_id === ProductType::PRODUCT_HOTEL && class_exists('\modules\hotel\HotelModule')) {
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

    protected function findModel($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.', 1);
    }
}
