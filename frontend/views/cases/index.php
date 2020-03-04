<?php

use common\models\Employee;
use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\CasesCategory;
use sales\yii\grid\cases\CasesSourceTypeColumn;
use sales\yii\grid\cases\CasesStatusColumn;
use yii\helpers\Html;
use kartik\grid\GridView;
use sales\entities\cases\Cases;
use \sales\entities\cases\CasesStatus;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var Employee $user */

$this->title = 'Search Cases';
$this->params['breadcrumbs'][] = $this->title;

if ($user->isAdmin()) {
    $userFilter = Employee::getList();
} elseif ($user->isSupSuper() || $user->isExSuper()) {
    $userFilter = Employee::getListByUserId($user->id);
} else {
    $userFilter = false;
}

$gridId = 'cases-grid-id';
?>
<div class="cases-search">
    <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

    <div class="card multiple-update-summary" style="margin-bottom: 10px; display: none">
        <div class="card-header">
            <span class="pull-right clickable close-icon"><i class="fa fa-times"></i></span>
            Processing result log:
        </div>
        <div class="card-body"></div>
    </div>

    <?php Pjax::begin(['id' => 'cases-pjax-list', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?php
            if ($user->isAdmin()) {
                $searchTpl = '_search';
            } else {
                $searchTpl = '_search_agents';
            }
            ?>
            <?= $this->render($searchTpl, ['model' => $searchModel]); ?>
        </div>
    </div>


    <?php if ($user->isAdmin() || $user->isExSuper() || $user->isSupSuper()): ?>
        <?= MultipleUpdateButtonWidget::widget([
            'modalId' => 'modal-df',
            'showUrl' => Url::to(['/cases-multiple-update/show']),
            'gridId' => $gridId,
        ]) ?>
    <?php endif;?>



        <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'visible' => $user->isAdmin() || $user->isExSuper() || $user->isSupSuper(),
            ],
            [
                'attribute' => 'cs_id',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            'cs_gid',
            [
                'attribute' => 'cs_project_id',
                'value' => static function (Cases $model) {
                    return $model->project ? '<span class="badge badge-info">' . Html::encode($model->project->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => EmployeeProjectAccess::getProjects()
            ],
            [
                'attribute' => 'cs_dep_id',
                'value' => static function (Cases $model) {
                    return $model->department ? $model->department->dep_name : '';
                },
                'filter' => EmployeeDepartmentAccess::getDepartments()
            ],
            [
                'attribute' => 'cs_category',
                'value' => static function (Cases $model) {
                    return $model->category ? $model->category->cc_name : '';
                },
                'filter' => CasesCategory::getList(array_keys(EmployeeDepartmentAccess::getDepartments()))
            ],
            [
                    'class' => CasesStatusColumn::class,
            ],
            [
                'class' => CasesSourceTypeColumn::class,
                'attribute' => 'cs_source_type_id',
            ],
            [
                'class' => \sales\yii\grid\cases\NeedActionColumn::class,
                'attribute' => 'cs_need_action',
            ],
            'cs_subject',
            [
                'attribute' => 'cs_user_id',
                'value' => static function (Cases $model) {
                    return $model->owner ? '<i class="fa fa-user"></i> ' .Html::encode($model->owner->username) : '-';
                },
                'format' => 'raw',
                'filter' => $userFilter
            ],
            [
                'attribute' => 'cs_lead_id',
                'value' => static function (Cases $model) {
                    return $model->lead ? $model->lead->uid : '-';
                },
                'filter' => false
            ],
            [
                'attribute' => 'cs_created_dt',
                'value' => static function (Cases $model) {
                    return $model->cs_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'cs_last_action_dt',
                'value' => static function (Cases $model) {
                    return $model->cs_last_action_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_last_action_dt)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, Cases $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to([
                            'cases/view',
                            'gid' => $model->cs_gid
                        ]));
                    }
                ]
            ]

        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>

<?php
$js = <<<JS
$('.close-icon').on('click', function(){    
    $('.multiple-update-summary').slideUp();
})
JS;
$this->registerJs($js);

?>