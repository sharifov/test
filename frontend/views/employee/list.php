<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel \backend\models\search\EmployeeForm
 * @var $employees []
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

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
            'columns' => [
                [
                    'label' => 'ID',
                    'value' => 'id'
                ],
                [
                    'attribute' => 'username',
                    //'label' => 'Username',
                    'filter' => Html::activeDropDownList($searchModel, 'id', $employees, [
                        'prompt' => '',
                        'class' => 'form-control'
                    ]),
                    'value' => function (\common\models\Employee $model) {
                        /**
                         * @var $model \common\models\Employee
                         */
                        return $model->username;
                    },
                    'format' => 'raw'
                ],
                /*[
                        'attribute' => 'email'
                    //'label' => 'Email',
                    //'value' => 'email'
                ],*/
                'email:email',
                [
                    'label' => 'Deleted',
                    'filter' => Html::activeDropDownList($searchModel, 'status', [
                        $searchModel::STATUS_ACTIVE => 'No',
                        $searchModel::STATUS_DELETED => 'Yes'
                    ], [
                        'prompt' => '',
                        'class' => 'form-control'
                    ]),
                    'value' => function (\common\models\Employee $model) {
                        return ($model->status === $model::STATUS_DELETED)
                            ? 'Yes'
                            : 'No';
                    }
                ],
                //'created_at:datetime',
                //'updated_at:datetime',
                [
                    'attribute' => 'created_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->created_at);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => function(\common\models\Employee $model) {
                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->updated_at);
                    },
                    'format' => 'html',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            /**
                             * @var $model \common\models\Employee
                             */
                            $url = \yii\helpers\Url::to([
                                'employee/update',
                                'id' => $model->id
                            ]);
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                                'title' => 'Edit'
                            ]);

                        },
                    ]
                ],
            ]
        ])
        ?>
    </div>
</div>
