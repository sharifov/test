<?php

use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\project\ProjectColumn;
use common\models\Call;
use sales\model\callTerminateLog\entity\CallTerminateLog;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callTerminateLog\entity\CallTerminateLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Terminate Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-terminate-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Terminate Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-call-terminate-log']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'ctl_id',
            'ctl_call_phone_number',
            [
                'attribute' => 'ctl_call_status_id',
                'value' => static function (CallTerminateLog $model) {
                    return Call::getStatusNameById($model->ctl_call_status_id);
                },
                'filter' => Call::STATUS_LIST
            ],
            [
                'class' => ProjectColumn::class,
                'attribute' => 'ctl_project_id',
                'relation' => 'ctlProject'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ctl_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
