<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $employees [] */

use yii\bootstrap\Html;
use yii\grid\GridView;

$this->title = 'User List';
$this->params['breadcrumbs'][] = $this->title;

$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-4" style="/*padding-top: 20px;*/">
        {summary}
    </div>
    <div class="col-sm-8" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;

?>
<div class="panel panel-default">
    <div class="panel-heading">Employees</div>
    <div class="panel-body">
        <div class="row mb-20">
            <div class="col-md-6">
                <?= Html::a('Create', 'update', [
                    'class' => 'btn-success btn',
                ]) ?>
            </div>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => $template,
            'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                if ($model->deleted) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                [
                    'attribute' => 'id'
                ],
                [
                    'attribute' => 'username',
                    'value' => function (\common\models\Employee $model) {
                        return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                    },
                    'format' => 'html'
                ],

                [
                    //'attribute' => 'username',
                    'label' => 'Role',
                    'value' => function (\common\models\Employee $model) {
                        $roles = $model->getRoles();
                        return $roles ? implode(', ', $roles) : '-';
                    },
                    'format' => 'raw'
                ],

                'email:email',
                [
                    'attribute' => 'status',
                    'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                    'value' => function (\common\models\Employee $model) {
                        return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                    },
                    'format' => 'html'
                ],

                [
                    'label' => 'User Groups',
                    'attribute' => 'user_group_id',
                    'value' => function (\common\models\Employee $model) {

                        $groups = $model->getUserGroupList();
                        $groupsValueArr = [];

                        foreach ($groups as $group) {
                            $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                        }

                        $groupsValue = implode(' ', $groupsValueArr);

                        return $groupsValue;
                    },
                    'format' => 'html',
                    'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                ],

                //'created_at:datetime',
                //'updated_at:datetime',
                'acl_rules_activated:boolean',
                [
                    'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created_at));
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated_at));
                    },
                    'format' => 'html',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'visibleButtons' => [
                        /*'view' => function ($model, $key, $index) {
                            return User::hasPermission('viewOrder');
                        },*/
                        'update' => function (\common\models\Employee $model, $key, $index) {
                            return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                        },
                    ],
                    /*'buttons' => [
                        'update' => function ($url, $model, $key) {

                            $url = \yii\helpers\Url::to([
                                'employee/update',
                                'id' => $model->id
                            ]);
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                                'title' => 'Edit'
                            ]);

                        },
                    ]*/
                ],
            ]
        ])
        ?>
    </div>
</div>
