<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Sales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-sale-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Sale', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'css_cs_id',
            'css_sale_id',
            'css_sale_book_id',
            'css_sale_pnr',
            'css_sale_pax',
            'css_sale_created_dt',
            //'css_sale_data',
            'css_created_user_id',
            'css_updated_user_id',
            'css_created_dt',
            'css_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
