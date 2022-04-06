<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \modules\requestControl\models\search\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Request Control';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-control-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'rcr_id',
            'rcr_type',
            'rcr_subject',
            'rcr_local',
            'rcr_global',
            ['class' => ActionColumn::class]
        ],
    ]); ?>

</div>
