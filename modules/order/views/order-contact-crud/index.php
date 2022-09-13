<?php

use common\components\grid\DateTimeColumn;
use common\components\i18n\Formatter;
use modules\order\src\entities\orderContact\OrderContact;
use src\helpers\email\MaskEmailHelper;
use src\helpers\phone\MaskPhoneHelper;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderContact\search\OrderContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-contact-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-order-contact', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'oc_id',
            [
                'attribute' => 'oc_order_id',
                'value' => static function (OrderContact $model) {
                    return (new Formatter())->asOrder($model->ocOrder);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oc_first_name',
                'value' => static function (OrderContact $model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['oc_first_name']);
                    return $data['oc_first_name'];
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oc_last_name',
                'value' => static function (OrderContact $model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['oc_last_name']);
                    return $data['oc_last_name'];
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oc_middle_name',
                'value' => static function (OrderContact $model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['oc_middle_name']);
                    return $data['oc_middle_name'];
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oc_email',
                'value' => static function (OrderContact $model) {
                    return MaskEmailHelper::masking($model->oc_email);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oc_phone_number',
                'value' => static function (OrderContact $model) {
                    return MaskPhoneHelper::masking($model->oc_phone_number);
                },
                'format' => 'raw'
            ],
            'oc_client_id:client',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'oc_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'oc_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
