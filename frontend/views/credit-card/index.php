<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
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

    <?php Pjax::begin(); ?>
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
            'cc_holder_name',
            'cc_expiration_month',
            'cc_expiration_year',
            //'cc_cvv',
            //'cc_type_id',
            [
                'attribute' => 'cc_type_id',
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->typeName;
                },
                'filter' => \common\models\CreditCard::getTypeList()
            ],
            [
                'attribute' => 'cc_status_id',
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->statusName;
                },
                'filter' => \common\models\CreditCard::getStatusList()
            ],
            //'cc_status_id',
            'cc_is_expired:boolean',
            'cc_created_user_id:UserName',
            'cc_updated_user_id:UserName',
            'cc_created_dt:DateTimeByUserDt',
            'cc_updated_dt:DateTimeByUserDt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
