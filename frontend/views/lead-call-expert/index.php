<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadCallExpertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Call Experts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-call-expert-index">

    <h1><span class="fa fa-bell-o"></span> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Call Expert', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'lce_id',
            'lce_lead_id',
            'lce_request_text:ntext',
            'lce_request_dt',
            'lce_response_text:ntext',
            'lce_response_lead_quotes:ntext',
            'lce_response_dt',
            'lce_status_id',
            'lce_agent_user_id',
            'lce_expert_user_id',
            'lce_expert_username',
            'lce_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
