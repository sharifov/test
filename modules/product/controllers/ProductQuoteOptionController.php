<?php

namespace modules\product\controllers;

use Yii;
use frontend\controllers\FController;
use modules\product\src\forms\ProductQuoteOptionForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProductQuoteOptionController extends FController
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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax(): string
    {
        $form = new ProductQuoteOptionForm();

        $productTypeId = null;

        if ($form->load(Yii::$app->request->post())) {

            $form->pqo_status_id = ProductQuoteOptionStatus::PENDING;

            if ($form->validate()) {
                $model = new ProductQuoteOption();
                $model->attributes = $form->attributes;
                if ($model->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
                }
                Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:CreateAjax:ProductQuoteOption:save');
            }
        } else {
            $productQuoteId = (int) Yii::$app->request->get('id');

            if (!$productQuoteId) {
                throw new BadRequestHttpException('Not found Product Quote ID', 1);
            }

            $pQuote = ProductQuote::findOne($productQuoteId);
            if (!$pQuote) {
                throw new BadRequestHttpException('Not found Product Quote', 2);
            }

            $form->pqo_product_quote_id = $productQuoteId;

            $productTypeId = $pQuote->pqProduct ? $pQuote->pqProduct->pr_type_id : 0;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $form,
            'productTypeId' => $productTypeId
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateAjax(): string
    {
        $productQuoteId = (int) Yii::$app->request->get('id');

        if (!$productQuoteId) {
            throw new BadRequestHttpException('Not found Product Quote ID', 1);
        }

        try {
            $model = $this->findModel($productQuoteId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $form = new ProductQuoteOptionForm();
        $form->pqo_id = $model->pqo_id;
        $form->pqo_product_quote_id = $model->pqo_product_quote_id;

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $model->attributes = $form->attributes;
                if ($model->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
                }
                Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:UpdateAjax:ProductQuoteOption:save');
            }
        } else {
            $form->attributes = $model->attributes;
            //$form->pqo_product_quote_id = $productQuoteId;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $form,
            'productTypeId' => $model->pqoProductQuote->pqProduct ? $model->pqoProductQuote->pqProduct->pr_type_id : 0
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
                throw new Exception('Product Quote Option ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed Quote Option (' . $model->pqo_id . ')'];
    }

    /**
     * @param $id
     * @return ProductQuoteOption
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuoteOption
    {
        if (($model = ProductQuoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
