<?php

use sales\model\callLog\entity\callLogLead\CallLogLead;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLogLead\search\CallLogLeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Log Leads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log Lead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cll_cl_id:callLog',
            [
                'attribute' => 'cll_lead_id',
                'format' => 'lead',
                'value' => static function (CallLogLead $model) {
                    return $model->lead ?: null;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
