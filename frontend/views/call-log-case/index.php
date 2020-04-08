<?php

use sales\model\callLog\entity\callLogCase\CallLogCase;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLogCase\search\CallLogCaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Log Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-case-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log Case', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'clc_cl_id:callLog',
            [
                'attribute' => 'clc_case_id',
                'format' => 'case',
                'value' => static function (CallLogCase $model) {
                    return $model->case ?: null;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
