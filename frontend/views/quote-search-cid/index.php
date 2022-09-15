<?php

use common\models\QuoteSearchCid;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSearchCidSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Search Cids';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-search-cid-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Quote Search Cid', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'qsc_id',
            [
                'attribute' => 'qsc_q_id',
                'format' => 'raw',
                'value' => static function (QuoteSearchCid $model) {
                    return Html::a($model->qsc_q_id, ['quotes/view', 'id' => $model->qsc_q_id], [
                        'data' => [
                            'pjax' => 0
                        ],
                        'target' => '_blank',
                    ]);
                }
            ],
            'qsc_cid',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, QuoteSearchCid $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qsc_id' => $model->qsc_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
