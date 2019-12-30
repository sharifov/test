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
        <?= Html::a('Create Credit Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cc_id',
            'cc_number',
            'cc_display_number',
            'cc_holder_name',
            'cc_expiration_month',
            'cc_expiration_year',
            'cc_cvv',
            'cc_type_id',
            'cc_status_id',
            'cc_is_expired',
            'cc_created_user_id',
            'cc_updated_user_id',
            'cc_created_dt',
            'cc_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
