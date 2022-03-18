<?php

use modules\cases\src\abac\saleList\SaleListAbacDto;
use modules\cases\src\abac\saleList\SaleListAbacObject;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;

/* @var $this yii\web\View */
/* @var $csId int */
/* @var $saleId int */
///* @var $searchModel common\models\search\CreditCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $caseSaleModel \common\models\CaseSale */
/* @var $caseModel src\entities\cases\Cases */

$addCreditCardBtnClass = 'btn-add-sale-cc-' . $saleId;
$editCreditCardBtnClass = 'btn-edit-credit-card-' . $saleId;
$deleteCreditCardBtnClass = 'btn-delete-credit-card-' . $saleId;
$pjaxCreditCardTable = 'pjax-credit-card-table' . $saleId;
$sendCcInfoBtnClass = 'btn-send-cc-info-' . $saleId;

$userId = \Yii::$app->user->id;
$caseAbacDto = new SaleListAbacDto($caseModel, $userId);
/** @abac $caseAbacDto, CasesAbacObject::UI_BLOCK_SALE_LIST, CasesAbacObject::ACTION_ADD_CREDIT_CARD, Restrict access to add credit card */
$canAdd = Yii::$app->abac->can($caseAbacDto, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_ADD_CREDIT_CARD);

/** @abac $caseAbacDto, CasesAbacObject::UI_BLOCK_SALE_LIST, CasesAbacObject::ACTION_SEND_CC_INFO, Restrict access to send credit card */
$canSend = Yii::$app->abac->can($caseAbacDto, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_SEND_CC_INFO);
?>
<div class="sale-credit-card">

    <h2>Credit Card (Optional)</h2>
    <?php if ($canAdd || $canSend) : ?>
        <p>
            <?php
            if ($canAdd) {
                echo Html::button('<i class="fa fa-plus"></i> Add Credit Card', [
                    'class' => $addCreditCardBtnClass . ' btn btn-success btn-sm',
                    'data-case-id' => $csId,
                    'data-case-sale-id' => $saleId,
                    'title' => 'Add Credit Card'
                ]);
            }
            if ($canSend) {
                echo Html::button('<i class="fa fa-envelope"></i> Send CC Info', [
                    'class' => 'btn btn-success btn-sm ' . $sendCcInfoBtnClass,
                    'data-case-id' => $csId,
                    'data-case-sale-id' => $saleId,
                    'title' => 'Send CC Info'
                ]);
            }
            ?>
        </p>
        <br>
    <?php endif; ?>

    <?php Pjax::begin(['id' => $pjaxCreditCardTable, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'cc_display_number',
            'cc_holder_name',
            //'cc_expiration_month',
            //'cc_expiration_year',

            [
                'label' => 'Expired',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->cc_expiration_month . ' / ' . $model->cc_expiration_year;
                },
            ],

            //'cc_cvv',
            [
                'attribute' => 'cc_type_id',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->typeName;
                },
            ],
//                    [
//                        'attribute' => 'cc_status_id',
//                        'value' => static function(\common\models\CreditCard $model) {
//                            return $model->statusName;
//                        },
//                    ],
//                    'cc_is_expired:boolean',
            //'cc_bo_link',
            [
                'attribute' => 'cc_bo_link',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->cc_bo_link ? 'Yes' :  '-';
                },
            ],
            [
                'attribute' => 'cc_is_sync_bo',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->cc_is_sync_bo ? 'Yes' :  '-';
                },
            ],
            'cc_created_dt:ByUserDateTime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {delete} {sync}',

                'buttons' => [
                    'edit' => static function ($url, $model) use ($editCreditCardBtnClass, $pjaxCreditCardTable) {
                        /** @var $model \common\models\CreditCard*/
                        $editCreditCardUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-update', 'id' => $model->cc_id, 'pjaxId' => $pjaxCreditCardTable]);
                        return Html::a('<i class="fa fa-pencil"></i>', $editCreditCardUrl, [
                            'title' => 'Edit Credit Card', 'data-pjax' => 0, 'class' => $editCreditCardBtnClass
                        ]);
                    },
                    'delete' => static function ($url, $model) use ($saleId, $deleteCreditCardBtnClass) {
                        /** @var $model \common\models\CreditCard*/
                        $deleteCreditCardUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-delete', 'id' => $model->cc_id, 'saleId' => $saleId]);
                        return Html::a('<i class="fa fa-trash"></i>', $deleteCreditCardUrl, [
                            'title' => 'Delete Credit Card', 'data-pjax' => 0, 'class' => $deleteCreditCardBtnClass
                        ]);
                    },
                    'sync' => static function ($url, $model) {
                        /** @var $model \common\models\CreditCard*/
//                      $deleteCreditCardUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-sync', 'id' => $model->cc_id]);
//                      return Html::a('<i class="fa fa-trash"></i>', $deleteCreditCardUrl, [
//                          'title' => 'Delete Credit Card', 'data-pjax' => 0, 'class' => 'btn-delete-credit-card'
//                      ]);
                        return '';
                    }
                ]
            ],
        ],
    ]); ?>

    <script>
        if (window.jQuery) {
            $('.report-send-email-<?= $saleId ?>').attr('data-credit-card-exist', '<?= $dataProvider->totalCount ?>');
        }
    </script>

    <?php Pjax::end(); ?>

</div>

<?php
$addCreditCardUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-add-credit-card', 'caseId' => $csId, 'saleId' => $saleId, 'pjaxId' => $pjaxCreditCardTable]);
$sendCcInfoUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-send-cc-info', 'caseId' => $csId, 'saleId' => $saleId]);
$js = <<<JS
    $(document).on('click', '.{$sendCcInfoBtnClass}', function (e) {
        e.preventDefault();
        var modal = $('#modal-sm');
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> </div>');
        modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        
        $.get('$sendCcInfoUrl', function(data) {
            modal.find('.modal-body').html(data);
        });
            
       return false;
    });

    $(document).on('click', '.{$addCreditCardBtnClass}', function (e) {
        e.preventDefault();
        var modal = $('#modal-df');
            //$('#search-sale-panel').toggle();
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> </div>');
        modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        
        $.get('$addCreditCardUrl', function(data) {
            modal.find('.modal-body').html(data);
        });
            
       return false;
    });
    
    $(document).on('click', '.{$editCreditCardBtnClass}', function (e) {
        e.preventDefault();
        var modal = $('#modal-sm');
        var url = $(this).attr('href');
            //$('#search-sale-panel').toggle();
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> </div>');
        modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        
        $.get(url, function(data) {
            modal.find('.modal-body').html(data);
        });
            
       return false;
    });
    
    $(document).on('click', '.{$deleteCreditCardBtnClass}', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this item?')) {
            var modal = $('#modal-df');
            var url = $(this).attr('href');
            
            $.get(url, function(data) {
                if (data.error) {
                    createNotify("Deletion Failed", data.message, "error")
                } else {
                    pjaxReload({container: "#{$pjaxCreditCardTable}"}); 
                    createNotify("Success Deleted", "Credit Card Successfully deleted", "success")
                }
            });
        }
            
       return false;
    });
    
    $('.sale-ticket-generate-email-btn').attr('data-credit-card-exist', '{$dataProvider->totalCount}');
JS;
$this->registerJs($js);

?>
