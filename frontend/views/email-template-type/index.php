<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailTemplateTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Template Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Email Template Type', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Email Template Types from Communication', ['synchronization '], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all Email Template Types from Communication Services?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'etp_id',
            'etp_key',
            'etp_name',
            [
                'attribute' => 'etp_updated_user_id',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return ($model->etpUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->etpUpdatedUser->username) : $model->etp_updated_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'etp_updated_dt',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_updated_dt));
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'etp_created_user_id',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return  ($model->etpCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->etpCreatedUser->username) : $model->etp_created_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'etp_created_dt',
                'value' => function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_created_dt));
                },
                'format' => 'raw'
            ],
            /*'etp_created_user_id',
            'etp_updated_user_id',
            'etp_created_dt',
            'etp_updated_dt',*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
