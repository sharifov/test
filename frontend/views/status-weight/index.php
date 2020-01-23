<?php

use common\models\Lead;
use common\models\StatusWeight;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StatusWeightSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Status Weight';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-weight-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Status Weight', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'sw_status_id',
                'value' => static function (StatusWeight $model) {
                    return Lead::STATUS_LIST[$model->sw_status_id] ?? 'Undefined';
                },
                'filter' => Lead::STATUS_LIST,
            ],
            'sw_weight',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
