<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\fileStorage\src\entity\fileProductQuote\search\FileProductQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Product Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-product-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Product Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-file-product-quote']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fpq_fs_id',
            'fpq_pq_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'fpq_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
