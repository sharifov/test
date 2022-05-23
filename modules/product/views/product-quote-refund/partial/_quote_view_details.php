<?php

/**
 * @var ActiveDataProvider $dataProvider
 * @var ActiveDataProvider $objectsRefundProvider
 * @var ActiveDataProvider $optionsRefundProvider
 */

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundStatus;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<div class="row">
    <div class="col-md-12">
        <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
//                'pqr_id',
                'pqr_order_refund_id',
                'pqr_case_id',
                'pqr_product_quote_id',
                'pqr_selling_price',
                'pqr_penalty_amount',
                'pqr_processing_fee_amount',
                'pqr_refund_amount',
                [
                    'attribute' => 'pqr_status_id',
                    'value' => static function (ProductQuoteRefund $model) {
                        return ProductQuoteRefundStatus::asFormat($model->pqr_status_id);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'pqr_client_currency',
                ],
                [
                    'attribute' => 'pqr_type_id',
                    'value' => static function (ProductQuoteRefund $model) {
                        return $model->getTypeName();
                    },
                    'format' => 'raw',
                ],
                'pqr_client_currency_rate',
                'pqr_client_selling_price',
                'pqr_client_refund_amount',
                [
                    'class' => \common\components\grid\UserColumn::class,
                    'relation' => 'createdUser',
                    'attribute' => 'pqr_created_user_id',
                ],
                [
                    'class' => \common\components\grid\UserColumn::class,
                    'relation' => 'updatedUser',
                    'attribute' => 'pqr_updated_user_id',
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'pqr_expiration_dt',
                    'options' => [
                        'width' => '200px;'
                    ]
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'pqr_created_dt',
                    'options' => [
                        'width' => '200px;'
                    ]
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'pqr_updated_dt',
                    'options' => [
                        'width' => '200px;'
                    ]
                ],
            ],
            'summary' => ''
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>

<div class="row">
  <div class="col-md-12">
    <h3>Product Quote Options Refund</h3>
      <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

      <?= GridView::widget([
          'dataProvider' => $optionsRefundProvider,
          'columns' => [
              'pqor_id',
              'pqor_product_quote_refund_id',
              'pqor_product_quote_option_id',
              'pqor_selling_price',
              'pqor_penalty_amount',
              'pqor_processing_fee_amount',
              'pqor_refund_amount',
              [
                  'attribute' => 'pqor_status_id',
                  'value' => static function (ProductQuoteOptionRefund $model) {
                      return ProductQuoteOptionRefundStatus::asFormat($model->pqor_status_id);
                  },
                  'format' => 'raw',
              ],
              [
                  'attribute' => 'pqor_client_currency',
              ],
              'pqor_client_currency_rate',
              'pqor_client_selling_price',
              'pqor_client_refund_amount',
              [
                  'class' => UserSelect2Column::class,
                  'attribute' => 'pqor_created_user_id',
                  'relation' => 'createdUser',
                  'placeholder' => 'Select User',
              ],
              [
                  'class' => UserSelect2Column::class,
                  'attribute' => 'pqor_updated_user_id',
                  'relation' => 'updatedUser',
                  'placeholder' => 'Select User',
              ],
              [
                  'class' => DateTimeColumn::class,
                  'attribute' => 'pqor_created_dt',
                  'options' => [
                      'width' => '200px;'
                  ]
              ],
              [
                  'class' => DateTimeColumn::class,
                  'attribute' => 'pqor_updated_dt',
                  'options' => [
                      'width' => '200px;'
                  ]
              ],
          ],
      ]); ?>

      <?php Pjax::end(); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
      <h3>Product Quote Objects Refund</h3>
      <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

      <?= GridView::widget([
          'dataProvider' => $objectsRefundProvider,
          'columns' => [
              'pqor_id',
              'pqor_product_quote_refund_id',
              'pqor_quote_object_id',
              'pqor_title',
              'pqor_selling_price',
              'pqor_penalty_amount',
              'pqor_processing_fee_amount',
              'pqor_refund_amount',
              [
                  'attribute' => 'pqor_status_id',
                  'value' => static function (ProductQuoteObjectRefund $model) {
                      return ProductQuoteObjectRefundStatus::asFormat($model->pqor_status_id);
                  },
                  'format' => 'raw',
              ],
              [
                  'attribute' => 'pqor_client_currency',
              ],
              'pqor_client_currency_rate',
              'pqor_client_selling_price',
              'pqor_client_refund_amount',
              [
                  'class' => UserSelect2Column::class,
                  'attribute' => 'pqor_created_user_id',
                  'relation' => 'createdUser',
                  'placeholder' => 'Select User',
              ],
              [
                  'class' => UserSelect2Column::class,
                  'attribute' => 'pqor_updated_user_id',
                  'relation' => 'updatedUser',
                  'placeholder' => 'Select User',
              ],
              [
                  'class' => DateTimeColumn::class,
                  'attribute' => 'pqor_created_dt',
                  'options' => [
                      'width' => '200px;'
                  ]
              ],
              [
                  'class' => DateTimeColumn::class,
                  'attribute' => 'pqor_updated_dt',
                  'options' => [
                      'width' => '200px;'
                  ]
              ],
          ],
      ]); ?>

      <?php Pjax::end(); ?>
  </div>
</div>
