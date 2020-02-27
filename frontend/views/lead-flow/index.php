<?php

use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadFlowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Status History';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

if($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

?>
<div class="lead-flow-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //= Html::a('Create Lead Flow', ['create'], ['class' => 'btn btn-success']) ?>
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

            [
                'attribute' => 'lf_description',
                'value' => static function (\common\models\LeadFlow $model) {
                    return $model->lf_description ?: '-';
                },
                //'options' => ['style' => 'width:80px'],
                'format' => 'text'
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
                    return $model->lf_time_duration ? '<span class="" title="' . Yii::$app->formatter->asDuration($model->lf_time_duration) . '">' . gmdate('H:i:s', $model->lf_time_duration) . '</span>': '-';
                },
                'options' => ['style' => 'width:110px'],
                'format' => 'raw',

            ],
            [
                'label' => 'Created by',
                'attribute' => 'employee_id',
                'value' =>  static function(\common\models\LeadFlow $model) {

                    if ($model->employee) {
                        $roles = $model->employee->getRoles();
                    } else {
                        $roles = [];
                    }

                    return $model->employee ? '<i class="fa fa-user"></i> '. Html::encode($model->employee->username) . ' ('.implode(', ', $roles).')' : '-';
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
//            [
//                //'attribute' => 'username',
//                'label' => 'Created by Role',
//                'value' => static function (\common\models\LeadFlow $model) {
//                    if($model->employee) {
//                        $roles = $model->employee->getRoles();
//                    } else {
//                        $roles = [];
//                    }
//                    return $roles ? implode(', ', $roles) : '-';
//                },
//                'options' => ['style' => 'width:150px'],
//                //'format' => 'raw'
//            ],
            /*[
                'label' => 'User Groups',
                //'attribute' => 'user_group_id',
                'value' => static function (\common\models\LeadFlow $model) {

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
                //'filter' => $user->isAdmin() ? \common\models\UserGroup::getList() : $user->getUserGroupList()
            ],*/

            //'lf_description:text',

            [
                'attribute' => 'lf_out_calls',
                'value' => static function (\common\models\LeadFlow $model) {
                    return $model->lf_out_calls ?: '-';
                },
                'options' => ['style' => 'width:80px'],
                //'format' => 'raw'
            ],

            //'employee_id',
            //'status',
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
