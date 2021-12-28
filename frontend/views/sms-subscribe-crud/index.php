<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use sales\model\smsSubscribe\entity\SmsSubscribe;
use sales\model\smsSubscribe\entity\SmsSubscribeStatus;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\smsSubscribe\entity\SmsSubscribeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Subscribes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-subscribe-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sms Subscribe', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-sms-subscribe']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ss_id',
            'ss_cpl_id',
            'ss_sms_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'ss_project_id',
                'relation' => 'ssProject'
            ],
            [
                'attribute' => 'ss_status_id',
                'value' => static function (SmsSubscribe $model) {
                    return SmsSubscribeStatus::getStatusName($model->ss_status_id);
                },
                'format' => 'raw',
                'filter' =>  SmsSubscribeStatus::STATUS_LIST
            ],

            ['class' => DateTimeColumn::class, 'attribute' => 'ss_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ss_updated_dt'],

            [
                'class' => UserColumn::class,
                'relation' => 'ssCreatedUser',
                'attribute' => 'ss_created_user_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'ssUpdatedUser',
                'attribute' => 'ss_updated_user_id',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'ss_deadline_dt',
                'limitEndDay' => false,
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
