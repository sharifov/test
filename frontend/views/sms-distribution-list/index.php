<?php

use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use sales\yii\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\sms\entity\smsDistributionList\search\SmsDistributionListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Distribution List';
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
?>
<div class="sms-distribution-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Sms Distribution', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-plus"></i> Add Multiple Sms', ['create-multiple'], ['class' => 'btn btn-warning']) ?>
    </p>

    <div class="col-md-12">
        Site settings "<b>sms_distribution_count</b>":  <b><?=(Yii::$app->params['settings']['sms_distribution_count'] ?? 'none')?></b>
        <div class="text-warning"> SMS are sent only in the status of Pending. With a frequency of once every 4 minutes. </div>
    </div>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'sdl_id',
//            'sdl_project_id',
            [
                'attribute' => 'sdl_project_id',
                'value' => static function (SmsDistributionList $model) {
                    return $model->sdlProject ? '<span class="label label-default">' . Html::encode($model->sdlProject->name) . '</span>' : '';
                },
                'format' => 'raw',
                'filter' => \common\models\Project::getList()
            ],
            [
                'attribute' => 'sdl_status_id',
                'value' => static function (SmsDistributionList $model) {
                    $text = $model->getStatusLabel();
                    if ((int) $model->sdl_status_id === SmsDistributionList::STATUS_ERROR) {
                        $text .= ' <span class="fa fa-info" title="' . Html::encode($model->sdl_error_message) . '"></span>';
                    }
                    return $text;
                },
                'format' => 'raw',
                'filter' => SmsDistributionList::getStatusList()
            ],
            'sdl_phone_from',
            'sdl_phone_to',
            //'sdl_client_id',
            [
                'attribute' => 'sdl_client_id',
                'value' => static function (SmsDistributionList $model) {
                    return $model->sdlClient ? Html::a($model->sdlClient->full_name . ' ('.$model->sdl_client_id.')', ['/client/view', 'id' => $model->sdl_client_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                },
                'format' => 'raw',

            ],
            [
                'attribute' => 'sdl_text',
                'value' => static function (SmsDistributionList $model) {
                    return '<span class="fa fa-info" title="' . Html::encode($model->sdl_text) . '"> Message </span>';
                },
                'format' => 'raw',

            ],
            //'sdl_text:ntext',
            'sdl_start_dt',
            'sdl_end_dt',
            //'sdl_status_id',
            'sdl_priority',
            //'sdl_num_segments',
            //'sdl_price',
            //'sdl_error_message:ntext',
            //'sdl_message_sid',
            //'sdl_created_user_id',
            //'sdl_updated_user_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sdl_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sdl_updated_dt',
            ],
            [
                'label' => 'Created User',
                'attribute' => 'sdlCreatedUser.username',
            ],
            [
                'label' => 'Updated User',
                'attribute' => 'sdlUpdatedUser.username',
            ],
            'sdl_com_id',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {send}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'delete' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'send' => static function (SmsDistributionList $model, $key, $index) use ($user) {
                        return $user->isAdmin() && (int) $model->sdl_status_id === SmsDistributionList::STATUS_PENDING;
                    },
                ],
                'buttons' => [
                    'send' => static function ($url, SmsDistributionList $model) {
                        return Html::a('<i class="fa fa-send text-warning"></i>', ['send', 'id' => $model->sdl_id], [
                            //'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'title' => 'Send SMS',
                            'data-pjax' => 0,
                            'data' => [
                                'confirm' => 'Are you sure you want send this SMS?',
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
