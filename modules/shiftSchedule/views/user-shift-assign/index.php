<?php

use common\components\grid\Select2Column;
use common\models\Employee;
use src\access\ListsAccess;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var \modules\shiftSchedule\src\entities\userShiftAssign\search\SearchUserShiftAssign $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var ListsAccess $listsAccess */

$this->title = 'User Shift Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-assign-index">

    <h1><i class="fa fa-user-plus"></i> <?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-user-shift-assign', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'label' => '',
                'format' => 'raw',
                'value' => static function (Employee $model) {
                    $gravUrl = $model->getGravatarUrl(25);
                    return \yii\helpers\Html::img($gravUrl, ['class' => 'img-circle img-thumbnail']);
                },
                'options' => ['width' => '50px'],
            ],
            [
                'label' => 'User',
                'class' => Select2Column::class,
                'attribute' => 'userId',
                'format' => 'raw',
                'value' => static function (Employee $model) {
                    return '<span style="white-space: nowrap;"><i class="fa fa-user"></i> ' . Html::encode($model->username) . '</span>';
                },
                'data' => $listsAccess->getEmployees() ?: [],
                'filter' => true,
                'id' => 'employee-filter',
                'options' => ['min-width' => '280px'],
                'pluginOptions' => ['allowClear' => true],
            ],
            [
                'label' => 'Shift',
                'attribute' => 'shiftId',
                'value' => static function (Employee $model) {
                    if (!$model->userShiftAssigns) {
                        return '';
                    }
                    $shifts = [];
                    foreach ($model->userShiftAssigns as $item) {
                        $shifts[] = Html::tag('span', Html::encode($item->shift->sh_name), ['class' => 'label label-default']);
                    }
                    return implode(' ', $shifts);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left', 'style' => 'min-width: 320px'],
                'filter' => \modules\shiftSchedule\src\entities\shift\Shift::getList(),
            ],
            [
                'label' => 'User Groups',
                'attribute' => 'userGroupId',
                'class' => Select2Column::class,
                'value' => static function (Employee $model) {
                    $groups = $model->getUserGroupList();
                    $groupsValueArr = [];
                    foreach ($groups as $group) {
                        $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-success']);
                    }
                    return implode(' ', $groupsValueArr);
                },
                'data' => \common\models\UserGroup::getList(),
                'filter' => true,
                'id' => 'group-filter',
                'options' => ['min-width' => '320px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
            [
                'label' => 'Project',
                'attribute' => 'projectId',
                'class' => Select2Column::class,
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
                'data' => \common\models\Project::getList(),
                'filter' => true,
                'id' => 'project-filter',
                'options' => ['min-width' => '320px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{assign}',
                'buttons' => [
                    'assign' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-user-plus"></span>',
                            ['assign', 'id' => $model->id],
                            ['title' => 'Assign to Shift', 'target' => '_blank', 'data-pjax' => 0,]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
