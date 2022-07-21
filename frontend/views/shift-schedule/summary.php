<?php

use common\components\grid\Select2Column;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \modules\shiftSchedule\src\entities\userShiftSchedule\search\AgentShiftSummaryReportSearch $searchModel */
/** @var ShiftScheduleType[] $scheduleTypeList */
/** @var array $subtypeTotalData */
/** @var array $totalCountData */

$tdWidth = 180;
$this->title = 'Shift Summary Report';
$this->params['breadcrumbs'][] = $this->title;
$pjaxContainerId = 'pjax-summary';
?>
<style>
    table.inner-summary-table tbody td {
        border: 0;
        width: 180px;
        border-right: 1px solid #dee2e6;
    }

    table.inner-summary-table tbody tr, .inner-summary-table.table thead tr {
        border: 0;
        background: none!important;
    }

    .summary-report.table thead th:nth-child(1), .summary-report.table thead th:nth-child(2) {
        vertical-align: middle;
        text-align: center;
    }

    .summary-report.table thead th:nth-child(4) {
        padding: 0;
    }

    .summary-report.table tfoot td:nth-child(4) {
        padding: 0;
    }

    .summary-report.table tfoot td:nth-child(4) {
        padding: 0;
    }

    .summary-report.table tfoot th:nth-child(4) table tbody td {
        border: 0;
    }

    .summary-report.table thead th:nth-child(4) table thead td {
        border: 0;
    }

    .summary-report.table tbody td:nth-child(4) {
        padding: 0;
    }
</style>
<div class="user-shift-assign-index">

    <h1><i class="fa fa-user-plus"></i> <?= Html::encode($this->title) ?></h1>

    <?php $theadTableData = '<table class="inner-summary-table"><thead><tr><td colspan="8" style="border-bottom: 1px solid #dee2e6;" class="text-center font-weight-bold">Event Type</td></tr><tr>'; ?>

    <?php $tfootTableData = ''; ?>

    <?php foreach ($scheduleTypeList as $type) : ?>
        <?php
        $theadTableData .= "<td style=\"width: {$tdWidth}px; border-right: 1px solid #dee2e6;\" class=\"text-center\">{$type->getIconLabel()} {$type->sst_name}</td>";
        $tfootTableData .= '<td style="width: ' . $tdWidth . 'px;" class="text-center font-weight-bold">' . $this->render('partial/__summary_format_duration', [
            'duration' => $totalCountData[$type->sst_id]['uss_duration'] ?? null,
            'count' => $totalCountData[$type->sst_id]['uss_count'] ?? null,
        ]) . '</td>';
        ?>
    <?php endforeach; ?>

    <?php $theadTableData .= '</tr><thead></table>'; ?>


    <?php Pjax::begin(['id' => $pjaxContainerId, 'timeout' => 7000, 'scrollTo' => 0]); ?>
    <?= $this->render('partial/_search-summary', ['model' => $searchModel]); ?>
    <h3>
        Report Interval <?= $searchModel->startDateRange ?>
    </h3>
    <?php $columns = [
        [
            'label' => 'User',
            'attribute' => 'username',
            'filter' => \src\widgets\UserSelect2Widget::widget([
                'model' => $searchModel,
                'attribute' => 'userId'
            ]),
            'format' => 'username',
            'options' => [
                'width' => '150px',
                'class' => 'text-center align-middle',
            ],
            'enableSorting' => false,
        ],

        [
            'label' => 'User Groups',
            'attribute' => 'userGroupId',
            'class' => Select2Column::class,
            'value' => static function (Employee $model) {
                $groups = $model->getUserGroupList();
                $groupsValueArr = [];
                foreach ($groups as $group) {
                    $groupsValueArr[] = Html::tag(
                        'span',
                        Html::encode($group),
                        ['class' => 'label label-success', 'style' => 'font-size: 11px;']
                    );
                }
                return implode(' ', $groupsValueArr);
            },
            'data' => \common\models\UserGroup::getList(),
            'filter' => true,
            'id' => 'group-filter',
            'options' => ['class' => 'text-center align-middle'],
            'pluginOptions' => ['allowClear' => true],
            'format' => 'raw',
        ],
        [
            'attribute' => 'role',
            'label' => 'Roles',
            'class' => Select2Column::class,
            'value' => static function (Employee $model) {
                $items = $model->getRoles();
                $itemsData = [];
                foreach ($items as $item) {
                    $itemsData[] = Html::tag(
                        'span',
                        Html::encode($item),
                        ['class' => 'label bg-light text-dark shadow', 'style' => 'font-size: 11px;']
                    );
                }
                return implode(' ', $itemsData);
            },

            'data' => \common\models\Employee::getAllRoles(\src\auth\Auth::user()),
            'filter' => true,
            'id' => 'role-filter',
            'options' => ['min-width' => '320px'],
            'pluginOptions' => ['allowClear' => true],
            'format' => 'raw',
            'contentOptions' => ['style' => 'width: 10%; white-space: pre-wrap'],
            'footer' => 'Total hours',
            'footerOptions' => [
                'class' => 'text-right font-weight-bold align-middle'
            ],
        ],
        [
        'header' => $theadTableData,
            'format' => 'html',
            'options' => [
                'width' => ($tdWidth * count($scheduleTypeList)) . 'px',
                'style' => 'padding: 0'
            ],
            'value' => function (\modules\shiftSchedule\src\reports\AgentShiftSummaryReport $model) use ($searchModel, $scheduleTypeList, $tdWidth) {
                $tbodyTableData = '';
                $list = $model->getTypes($searchModel);
                foreach ($scheduleTypeList as $scheduleType) {
                    $tbodyTableData .= '<td style="width: ' . $tdWidth . 'px;" class="text-center">';
                    if (array_key_exists($scheduleType->sst_id, $list)) {
                        $tbodyTableData .= $this->render('partial/__summary_format_duration', [
                            'duration' => $list[$scheduleType->sst_id]['uss_duration'],
                            'count' => $list[$scheduleType->sst_id]['uss_count'],
                        ]);
                    } else {
                        $tbodyTableData .= ' - ';
                    }

                    $tbodyTableData .= '</td>';
                }

                return $this->render('partial/__summary_inner_table', [
                    'thead' => false,
                    'data' => $tbodyTableData
                ]);
            },
            'footer' => $this->render('partial/__summary_inner_table', [
                'thead' => false,
                'data' => $tfootTableData
            ])
        ],
    ]; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => $columns,
        'showFooter' => true,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered summary-report'
        ],
    ]); ?>


    <?php Pjax::end(); ?>

</div>