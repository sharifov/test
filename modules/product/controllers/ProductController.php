<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\useCases\product\update\ProductUpdateForm;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use modules\product\src\useCases\product\update\ProductUpdateService;
use sales\helpers\app\AppHelper;
use sales\yii\bootstrap4\ActiveForm;
use Yii;
use common\models\Lead;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productType\ProductType;
use modules\hotel\models\Hotel;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductController
 *
 * @property ProductCreateService $productCreateService
 * @property ProductRepository $productRepository
 * @property ProductUpdateService $productUpdateService
 */
class ProductController extends FController
{
    private $productCreateService;
    private $productRepository;
    private $productUpdateService;

    public function __construct(
        $id,
        $module,
        ProductCreateService $productCreateService,
        ProductRepository $productRepository,
        ProductUpdateService $productUpdateService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->productCreateService = $productCreateService;
        $this->productRepository = $productRepository;
        $this->productUpdateService = $productUpdateService;
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
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateAjax($id)
    {
        $model = $this->findModel((int)$id);

        $form = new ProductUpdateForm($model);

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                try {
                    $this->productUpdateService->update($form);
                    return $this->asJson(['success' => true, 'message' => 'Product updated']);
                } catch (\DomainException $e) {
                    return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
                } catch (\Throwable $e) {
                    Yii::error(AppHelper::throwableFormatter($e), 'ProductController:' . __FUNCTION__ );
                    return $this->asJson(['success' => false, 'message' => 'Server error']);
                }
            }
            return $this->asJson(ActiveForm::formatError($form));
        }

        return $this->renderAjax('_update_ajax_form', [
            'model' => $form,
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
