<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\flightQuoteLabelList\entity\FlightQuoteLabelListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Label List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-label-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Label', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-label', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fqll_label_key',
            'fqll_origin_description',
            'fqll_description',
            [
                'class' => UserColumn::class,
                'relation' => 'fqllCreatedUser',
                'attribute' => 'fqll_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'fqll_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
