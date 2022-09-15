<?php

use common\models\CreditCard;
use src\helpers\text\MaskStringHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CreditCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Credit Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-card-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add Credit Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cc_id',
            //'cc_number',
            'cc_display_number',
//            [
//                'label' => 'Display',
//                'value' => static function(\common\models\CreditCard $model) {
//                    return $model->initNumber;
//                },
//                'filter' => false
//            ],
//            [
//                'label' => 'Display CVV',
//                'value' => static function(\common\models\CreditCard $model) {
//                    return $model->initCvv;
//                },
//                'filter' => false
//            ],
            [
                'attribute' => 'cc_holder_name',
                'value' => static function (CreditCard $model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['cc_holder_name']);
                    return $data['cc_holder_name'];
                }
            ],
            'cc_expiration_month',
            'cc_expiration_year',
            'cc_security_hash',
            //'cc_cvv',
            //'cc_type_id',
            [
                'attribute' => 'cc_type_id',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->typeName;
                },
                'filter' => \common\models\CreditCard::getTypeList()
            ],
            [
                'attribute' => 'cc_status_id',
                'value' => static function (\common\models\CreditCard $model) {
                    return $model->statusName;
                },
                'filter' => \common\models\CreditCard::getStatusList()
            ],
            //'cc_status_id',
            'cc_is_expired:boolean',
            'cc_bo_link',
            'cc_is_sync_bo:boolean',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cc_created_user_id',
                'relation' => 'ccCreatedUser',
                'placeholder' => 'Select User',
            ],

//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'cc_updated_user_id',
//                'relation' => 'ccCreatedUser',
//                'placeholder' => 'Select User',
//            ],

            //'cc_created_dt:ByUserDateTime',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cc_created_dt'
            ],
//            'cc_updated_dt:ByUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
