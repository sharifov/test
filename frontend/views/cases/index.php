<?php

use common\models\CaseSale;
use common\models\Department;
use common\models\Employee;
use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\CaseCategory;
use common\components\grid\cases\CasesSourceTypeColumn;
use common\components\grid\cases\CasesStatusColumn;
use sales\helpers\communication\StatisticsHelper;
use sales\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use kartik\grid\GridView;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use sales\auth\Auth;

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
            <?= $this->render($searchTpl, ['model' => $searchModel, 'dataProvider' => $dataProvider]); ?>
        </div>
    </div>


    <?php if ($user->isAdmin() || $user->isExSuper() || $user->isSupSuper()) : ?>
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
                'value' => static function (Cases $case) {
                    if (Auth::can('cases/view', ['case' => $case])) {
                        return Html::a($case->cs_id, [
                            'cases/view',
                            'gid' => $case->cs_gid
                        ], [
                            'target' => '_blank',
                            'title' => 'View',
                            'data-pjax' => 0,
                        ]);
                    } else {
                        return $case->cs_id;
                    }
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'label' => 'Client ID',
                'attribute' => 'cs_client_id',
                'format' => 'client'
            ],
            [
                'header' => 'Client Info',
                'format' => 'raw',
                'value' => static function (Cases $case) use ($user) {

                    if ($case->client) {
                        $clientName = $case->client->first_name . ' ' . $case->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }
                        if ($case->client->isExcluded()) {
                            $clientName = ClientFormatter::formatExclude($case->client)  . $clientName;
                        }
                    } else {
                        $clientName = '-';
                    }

                    $str = $clientName . '<br>';

                    if ($user->isAgent() && $case->isOwner($user->id)) {
                        $str .= '- // - // - // -';
                    } elseif ($case->client) {
                        $str .= $case->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', ArrayHelper::map($case->client->clientEmails, 'email', 'email')) . '' : '';
                        $str .= $case->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($case->client->clientPhones, 'phone', 'phone')) . '' : '';
                    }

                    $statistics = new StatisticsHelper($case->cs_id, StatisticsHelper::TYPE_CASE);
                    $str .= '<br /><br />';
                    $str .= Yii::$app->getView()->render(
                        '/partial/_communication_statistic_list',
                        ['statistics' => $statistics->setCountAll()]
                    );

                    return $str ?? '-';
                },
                'options' => [
                    'style' => 'width:180px'
                ]
            ],
            //'cs_gid',

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cs_project_id',
                'relation' => 'project'
            ],

            [
                'attribute' => 'cs_dep_id',
                'value' => static function (Cases $model) {
                    return $model->department ? $model->department->dep_name : '';
                },
//                'filter' => EmployeeDepartmentAccess::getDepartments()
                'filter' => Department::getList(),
            ],
            [
                'attribute' => 'cs_category_id',
                'value' => static function (Cases $model) {
                    return $model->category ? $model->category->cc_name : '';
                },
                'filter' => CaseCategory::getList(array_keys(EmployeeDepartmentAccess::getDepartments()))
            ],
            [
                'class' => CasesStatusColumn::class,
            ],
            [
                'class' => CasesSourceTypeColumn::class,
                'attribute' => 'cs_source_type_id',
            ],
            [
                'class' => \common\components\grid\cases\NeedActionColumn::class,
                'attribute' => 'cs_need_action',
            ],
            [
                'attribute' => 'cs_subject',
                'contentOptions' => [
                    'style' => 'word-break: break-all; white-space:normal'
                ]
            ],
            /*[
                'attribute' => 'cs_user_id',
                'value' => static function (Cases $model) {
                    return $model->owner ? '<i class="fa fa-user"></i> ' .Html::encode($model->owner->username) : '-';
                },
                'format' => 'raw',
                'filter' => $userFilter
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'cs_user_id',
                'relation' => 'owner',
                'placeholder' => 'Select User',
            ],

            [
                'attribute' => 'cs_lead_id',
                'value' => static function (Cases $model) {
                    return $model->lead ? $model->lead->uid : '-';
                },
                'filter' => false
            ],
            [
                'attribute' => 'cs_order_uid',
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
                'label' => 'Sale info',
                'value' => static function (Cases $model) {
                    $out = '';
                    if ($model->caseSale) {
                        foreach ($model->caseSale as $caseSale) {
                            /** @var CaseSale $caseSale */
                            $out .= Html::a(
                                '[' . $caseSale->css_sale_id . ']',
                                ['sale/view', 'h' => base64_encode($caseSale->css_sale_book_id . '|' . $caseSale->css_sale_id)]
                            ) . '<br />';
                            $out .= $caseSale->css_charged ? 'Selling price: ' . $caseSale->css_charged . '<br />' : '';
                            $out .= $caseSale->css_profit ? 'Profit: ' . $caseSale->css_profit . '<br />' : '';
                            $out .= $caseSale->css_out_date ? 'Out : <i class="fa fa-calendar"></i> ' .
                                Yii::$app->formatter->asDatetime(strtotime($caseSale->css_out_date)) . '<br />' : '';
                            $out .= $caseSale->css_in_date ? 'In : <i class="fa fa-calendar"></i> ' .
                                Yii::$app->formatter->asDatetime(strtotime($caseSale->css_in_date)) . '<br />' : '';
                            $out .= '<hr />';
                        }
                    }
                    return $out;
                },
                'format' => 'raw',
                'visible' => $searchModel->showFields && in_array('sale_info', $searchModel->showFields, true),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{take}',
                'visibleButtons' => [
                    'take' => static function (Cases $model, $key, $index) {
                        return Auth::can('cases/take', ['case' => $model]);
                    },
                ],
                'buttons' => [
                    'take' => static function ($url, Cases $model) {
                        return Html::a('<i class="fa fa-download"></i> Take', ['cases/take', 'gid' => $model->cs_gid], [
                            'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'data-pjax' => 0,
                            /*'data' => [
                                'confirm' => 'Are you sure you want to take this Case?',
                                //'method' => 'post',
                            ],*/
                        ]);
                    }
                ],
            ]

            /*[
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
            ]*/
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