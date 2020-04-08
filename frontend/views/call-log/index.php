<?php

use sales\model\callLog\entity\callLog\CallLog;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLog\search\CallLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Logs';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="call-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cl_id',
            'cl_parent_id',
            'cl_call_sid',
            ['class' => \sales\model\callLog\grid\columns\CallLogTypeColumn::class],
            ['class' => \sales\model\callLog\grid\columns\CallLogCategoryColumn::class],
            ['class' => BooleanColumn::class, 'attribute' => 'cl_is_transfer'],
            [
                'label' => 'Lead Id',
                'attribute' => 'lead_id',
                'value' => static function (CallLog $log) {
                    return $log->callLogLead ? $log->callLogLead->lead : null;
                },
                'format' => 'lead'
            ],
            [
                'label' => 'Case Id',
                'attribute' => 'case_id',
                'value' => static function (CallLog $log) {
                    return $log->callLogCase ? $log->callLogCase->case : null;
                },
                'format' => 'case'
            ],
            'cl_duration',
            'cl_phone_from',
            'cl_phone_to',
            [
                'class' => \common\components\grid\PhoneSelect2Column::class,
                'attribute' => 'cl_phone_list_id',
                'relation' => 'phoneList',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cl_user_id',
                'relation' => 'user',
            ],
            [
                'class' => \common\components\grid\department\DepartmentColumn::class,
                'attribute' => 'cl_department_id',
                'relation' => 'department',
            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cl_project_id',
                'relation' => 'project',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'cl_call_created_dt', 'format' => 'byUserDateTimeWithSeconds'],
            ['class' => DateTimeColumn::class, 'attribute' => 'cl_call_finished_dt'],
            ['class' => \sales\model\callLog\grid\columns\CallLogStatusColumn::class],
            'cl_client_id:client',
            'cl_price',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
