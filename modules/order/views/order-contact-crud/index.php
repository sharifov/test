<?php

use common\components\i18n\Formatter;
use modules\order\src\entities\orderContact\OrderContact;
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

    <?php Pjax::begin(['id' => 'pjax-order-contact']); ?>
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
            'oc_first_name',
            'oc_last_name',
            'oc_middle_name',
            'oc_email:email',
            'oc_phone_number',
            'oc_created_dt:byUserDateTime',
            'oc_updated_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
