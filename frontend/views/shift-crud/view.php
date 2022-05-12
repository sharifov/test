<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\search\SearchShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\widget\ShiftSelectWidget;
use src\auth\Auth;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Shift */

/* @var $searchModelRule SearchShiftScheduleRule */
/* @var $searchModelUserAssign Employee[] */
/* @var $dataProviderRule yii\data\ActiveDataProvider */
/* @var $dataProviderUserAssign yii\data\ActiveDataProvider */

/* @var $userList Employee[] */

$this->title = $model->sh_name;
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-view">

    <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->sh_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->sh_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'sh_id',
                [
                    'attribute' => 'sh_name',
                    'value' => static function (Shift $model) {
                        return $model->getColorLabel() . ' ' . Html::encode($model->sh_name);
                    },
                    'format' => 'raw'
                ],
                'sh_title',
                [
                    'attribute' => 'sh_category_id',
                    'value' => static function (Shift $model) {
                        return $model->category ? Html::encode($model->category->sc_name ?? '') : null;
                    },
                ],
                'sh_enabled:booleanByLabel',
                'sh_color',
                'sh_sort_order',
                'sh_created_dt:byUserDateTime',
                'sh_updated_dt:byUserDateTime',
                'sh_created_user_id:username',
                'sh_updated_user_id:username',
            ],
        ]) ?>

    </div>
    <div class="col-md-8">


        <?php
        echo Html::a(
            '<h2>Shift Schedule Rules</h2>',
            ['shift-schedule-rule-crud/index', 'SearchShiftScheduleRule[ssr_shift_id]' => $model->sh_id],
            ['data-pjax' => 0, 'target' => '_blank']
        )
        ?>

        <?php Pjax::begin(['id' => 'pjax-shift-schedule-rule', 'scrollTo' => 0]); ?>


        <?= GridView::widget([
            'dataProvider' => $dataProviderRule,
            'filterModel' => $searchModelRule,
            'columns' => [
                ['attribute' => 'ssr_id', 'options' => [
                    'style' => 'width: 60px'
                ]],
//                [
//                    'attribute' => 'ssr_shift_id',
//                    'value' => static function (ShiftScheduleRule $model) {
//                        return $model->shift ? $model->shift->getColorLabel() . '&nbsp; ' . Html::a(
//                            $model->shift->sh_name,
//                            ['shift-schedule-rule-crud/view', 'id' => $model->shift->sh_id],
//                            ['data-pjax' => 0]
//                        ) : '-';
//                    },
//                    'filter' => ShiftSelectWidget::widget(['model' => $searchModelRule, 'attribute' => 'ssr_shift_id']),
//                    'options' => [
//                        'style' => 'width: 120px'
//                    ],
//                    'format' => 'raw'
//                ],
                [
                    'attribute' => 'ssr_sst_id',
                    'value' => static function (
                        ShiftScheduleRule $model
                    ) {
                        return $model->scheduleType ? $model->scheduleType->getColorLabel() . '&nbsp; ' .
                            $model->getScheduleTypeTitle() : '-';
                    },
                    'filter' => ShiftScheduleType::getList(),
                    'format' => 'raw'
                ],
                'ssr_title',
//                'ssr_start_time_utc',
//                'ssr_end_time_utc',


                [
                    'label' => 'Start Loc Time',
                    'value' => static function (
                        ShiftScheduleRule $model
                    ) {
                        return '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asTime(strtotime($model->ssr_start_time_utc));
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'End Loc Time',
                    'value' => static function (
                        ShiftScheduleRule $model
                    ) {
                        return '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asTime(strtotime($model->ssr_end_time_utc));
                    },
                    'format' => 'raw'
                ],
                'ssr_duration_time',

                //'ssr_timezone',
//            'ssr_start_time_loc',
//            'ssr_end_time_loc',

                /*[
                    'class' => DurationColumn::class,
                    'attribute' => 'ssr_duration_time',
                    'startAttribute' => 'ssr_duration_time',
                    'options' => ['style' => 'width:180px'],
                ],*/
                'ssr_cron_expression',
                'ssr_cron_expression_exclude',
                ['class' => BooleanColumn::class, 'attribute' => 'ssr_enabled'],

//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'ssr_created_dt',
//                'format' => 'byUserDateTime'
//            ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'ssr_updated_dt',
                    'format' => 'byUserDateTime'
                ],
//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'ssr_created_user_id',
//                'relation' => 'createdUser',
//                'format' => 'username',
//                'placeholder' => 'Select User'
//            ],
//                [
//                    'class' => UserSelect2Column::class,
//                    'attribute' => 'ssr_updated_user_id',
//                    'relation' => 'updatedUser',
//                    'format' => 'username',
//                    'placeholder' => 'Select User'
//                ],

//                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

        <?php Pjax::end(); ?>




        <?php
        echo Html::a(
            '<h2>Assign User List</h2>',
            ['shift/user-shift-assign/index', 'UserShiftAssignListSearch[shiftId]' => $model->sh_id],
            ['data-pjax' => 0, 'target' => '_blank']
        )
        ?>


        <?php Pjax::begin(['id' => 'pjax-shift-schedule-user']); ?>


        <?= GridView::widget([
            'dataProvider' => $dataProviderUserAssign,
            'filterModel' => $searchModelUserAssign,
            'columns' => [
                ['attribute' => 'id', 'options' => [
                    'style' => 'width: 60px'
                ]],
                'username:userName',
                'full_name',
                'email',
                [
                    'attribute' => 'roles',
                    'label' => 'Roles',
                    'value' => static function (Employee $model) {
                        $roles = $model->getRoles();
                        return $roles ? implode(', ', $roles) : '-';
                    },
                    'format' => 'raw',
                    //'filter' => \common\models\Employee::getAllRoles(Auth::user()),
                    'contentOptions' => ['style' => 'width: 10%; white-space: pre-wrap']
                ],

                [
                    'attribute' => 'status',
                    //'filter' => $searchModel::STATUS_LIST,
                    'value' => static function (Employee $model) {
                        return Yii::$app->formatter->asEmployeeStatusLabel($model->status);
                    },
                    'format' => 'raw'
                ],



                [
                    'label' => 'User Groups',
                    'attribute' => 'user_group_id',
                    'value' => static function (Employee $model) {

                        $groups = $model->getUserGroupList();
                        $groupsValueArr = [];

                        foreach ($groups as $group) {
                            $groupsValueArr[] = '<div class="col-md-4">' .
                                Html::tag('div', Html::encode($group), ['class' => 'label label-info']) . '</div>';
                        }

                        $groupsValue = '<div class="row">' . implode(' ', $groupsValueArr) . '</div>';

                        return $groupsValue;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width: 280px'],
                ],

                [
                    'label' => 'User Departments',
                    'attribute' => 'user_department_id',
                    'value' => static function (Employee $model) {

                        $list = $model->getUserDepartmentList();
                        $valueArr = [];

                        foreach ($list as $item) {
                            $valueArr[] = '<div class="col-md-4">' .
                                Html::tag('div', Html::encode($item), ['class' => 'label label-default'])
                                . '</div>';
                        }

                        $value = '<div class="row">' . implode(' ', $valueArr) . '</div>';

                        return $value;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width: 280px'],
                    //'filter' => \common\models\Department::getList()
                ],


                [
                    'label' => 'Project List',
                    'value' => static function (Employee $model) {
                        if (!$model->projects) {
                            return '';
                        }
                        $projects = [];
                        foreach ($model->projects as $item) {
                            $projects[] = Html::tag('span', Html::encode($item->name), ['class' => 'label label-info']);
                        }
                        return implode(' ', $projects);
                    },
                    'format' => 'raw',
                ],

                [
                    'attribute' => 'online',
                    'filter' => [1 => 'Online', 2 => 'Offline'],
                    'value' => static function (Employee $model) {
                        return $model->userOnline ? '<span class="label label-success">Online</span>'
                            : '<span class="label label-danger">Offline</span>';
                    },
                    'format' => 'raw'
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>
</div>
