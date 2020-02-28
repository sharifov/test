<?php

use sales\yii\grid\project\ProjectColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\VisitorLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitor Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Visitor Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'vl_id',
            [
                'class' => ProjectColumn::class,
                'attribute' => 'vl_project_id',
                'relation' => 'vlProject',
            ],
            'vl_source_cid',
            'vl_ga_client_id',
            'vl_ga_user_id',
            //'vl_user_id',
            //'vl_client_id',
            //'vl_lead_id',
            //'vl_gclid',
            //'vl_dclid',
            //'vl_utm_source',
            //'vl_utm_medium',
            //'vl_utm_campaign',
            //'vl_utm_term',
            //'vl_utm_content',
            //'vl_referral_url:url',
            //'vl_location_url:url',
            //'vl_user_agent',
            //'vl_ip_address',
            //'vl_visit_dt',
            //'vl_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
