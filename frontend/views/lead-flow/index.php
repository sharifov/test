<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadFlowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Status History';
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

?>
<div class="lead-flow-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create Lead Flow', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'options' => ['style' => 'width:100px'],
            ],

            [
                'attribute' => 'lf_from_status_id',
                'value' => function(\common\models\LeadFlow $model) {
                    return '<span class="label label-info">'.\common\models\Lead::getStatus($model->lf_from_status_id).'</span></h5>';
                },
                'format' => 'raw',
                'filter' => \common\models\Lead::STATUS_LIST,
                'options' => ['style' => 'width:180px'],
            ],

            [
                    'label' => 'To Status',
                'attribute' => 'status',
                'value' => function(\common\models\LeadFlow $model) {
                    return '<span class="label label-info">'.\common\models\Lead::getStatus($model->status).'</span></h5>';
                },
                'format' => 'raw',
                'filter' => \common\models\Lead::STATUS_LIST,
                'options' => ['style' => 'width:180px'],
            ],
            //'lead_id',
            [
                //'label' => 'Lead UID',
                'attribute' => 'lead_id',
                'value' => function(\common\models\LeadFlow $model) {
                    return Html::a('' . $model->lead_id, ['lead/view', 'gid' => $model->lead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'options' => ['style' => 'width:140px'],
                //'filter' => false
            ],
            //'created',
            [
                'label' => 'Status start date',
                'attribute' => 'created',
                'value' => function(\common\models\LeadFlow $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            [
                'label' => 'Status end date',
                'attribute' => 'lf_end_dt',
                'value' => function(\common\models\LeadFlow $model) {
                    return $model->lf_end_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lf_end_dt)) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'lf_end_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

            [
                //'label' => 'Status end date',
                'attribute' => 'lf_time_duration',
                'value' => function(\common\models\LeadFlow $model) {
                    return $model->lf_time_duration ?: '-';
                },
                //'format' => 'raw',

            ],
            [
                    'label' => 'Created by',
                'attribute' => 'employee_id',
                'value' => function(\common\models\LeadFlow $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> '. Html::encode($model->employee->username) : '-';
                },
                'format' => 'raw',
                'filter' => $userList
            ],
            [
                    'label' => 'Owner',
                'attribute' => 'lf_owner_id',
                'value' => function(\common\models\LeadFlow $model) {
                    return $model->owner ? '<i class="fa fa-user"></i> '. Html::encode($model->owner->username) : '-';
                },
                'format' => 'raw',
                'filter' => $userList
            ],
            [
                //'attribute' => 'username',
                'label' => 'Owner Role',
                'value' => function (\common\models\LeadFlow $model) {
                    if($model->owner) {
                        $roles = $model->owner->getRoles();
                    } else {
                        $roles = [];
                    }
                    return $roles ? implode(', ', $roles) : '-';
                },
                'options' => ['style' => 'width:150px'],
                //'format' => 'raw'
            ],
            [
                'label' => 'User Groups',
                //'attribute' => 'user_group_id',
                'value' => function (\common\models\LeadFlow $model) {

                    $groupsValueArr = [];
                    if($model->employee) {
                        $groups = $model->employee->getUserGroupList();
                        $groupsValueArr = [];

                        foreach ($groups as $group) {
                            $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                        }


                    }
                    $groupsValue = implode(' ', $groupsValueArr);

                    return $groupsValue;
                },
                'format' => 'raw',
                //'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
            ],

            'lf_out_calls',

            //'employee_id',
            //'status',
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
