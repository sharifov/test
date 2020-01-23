<?php

namespace frontend\controllers;

use modules\product\src\useCases\create\ProductCreateForm;
use modules\product\src\useCases\create\ProductCreateService;
use Yii;
use common\models\Lead;
use common\models\Product;
use common\models\ProductType;
use modules\hotel\models\Hotel;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductManageController
 *
 * @property ProductCreateService $productCreateService
 */
class ProductManageController extends FController
{
    private $productCreateService;

    public function __construct($id, $module, ProductCreateService $productCreateService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->productCreateService = $productCreateService;
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
                Yii::error($e, 'ProductManageController:actionCreateAjax:DomainException');
                return ['errors' => $e->getMessage()];
            } catch (\Throwable $e) {
                Yii::error($e, 'ProductManageController:actionCreateAjax:Throwable');
                return ['errors' => 'Server error'];
            }

        } else {

            $leadId = (int)Yii::$app->request->get('id');

            if (!$leadId) {
                throw new BadRequestHttpException('Not found lead identity.');
            }

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                throw new BadRequestHttpException('Not found this lead');
            }

            $form->pr_lead_id = $leadId;
        }

        return $this->renderAjax('_ajax_form', [
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
