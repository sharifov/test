<?php

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
/* @var $caseModel sales\entities\cases\Cases */


?>
<div class="sale-credit-card">

    <h2>Credit Card (Optional)</h2>
    <?php if ($caseModel->isProcessing()): ?>
        <p>
            <?php
                echo Html::button('<i class="fa fa-plus"></i> Add Credit Card', [
                    'class' => 'btn-add-sale-cc btn btn-success btn-sm',
                    'data-case-id' => $csId,
                    'data-case-sale-id' => $saleId,
                    'title' => 'Add Credit Card'
                ]);
            ?>
        </p>
        <br>
    <?php endif; ?>

    <?php Pjax::begin(['id' => 'pjax-credit-card-table', 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
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
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->cc_expiration_month . ' / ' . $model->cc_expiration_year;
                },
            ],

            //'cc_cvv',
            [
                'attribute' => 'cc_type_id',
                'value' => static function(\common\models\CreditCard $model) {
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
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->cc_bo_link ? 'Yes' :  '-';
                },
            ],
            'cc_created_dt:ByUserDateTime',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$addCreditCardUrl = \yii\helpers\Url::toRoute(['/credit-card/ajax-add-credit-card', 'caseId' => $csId, 'saleId' => $saleId]);
$js = <<<JS
    $(document).on('click', '.btn-add-sale-cc', function (e) {
        e.preventDefault();
        var modal = $('#modal-df');
            //$('#search-sale-panel').toggle();
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> </div>');
        modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        
        $.get('$addCreditCardUrl', function(data) {
            modal.find('.modal-body').html(data);
        });
            
       return false;
    })
JS;
$this->registerJs($js);

?>
