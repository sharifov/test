<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BillingInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Billing Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="billing-info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Billing Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'bi_id',
            'bi_first_name',
            'bi_last_name',
            'bi_middle_name',
            'bi_company_name',
            //'bi_address_line1',
            //'bi_address_line2',
            //'bi_city',
            //'bi_state',
            //'bi_country',
            //'bi_zip',
            //'bi_contact_phone',
            //'bi_contact_email:email',
            //'bi_contact_name',
            //'bi_payment_method_id',
            //'bi_cc_id',
            //'bi_order_id',
            //'bi_status_id',
            //'bi_created_user_id',
            //'bi_updated_user_id',
            //'bi_created_dt',
            //'bi_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
