<?php

use yii\grid\ActionColumn;
use src\auth\Auth;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Api Logs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-api-log';
?>
<div class="api-log-index">

    <h1><i class="fa fa-list"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Truncate ApiLog table', ['delete-all'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all items?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('../clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>

    <?php  echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{pager}\n{summary}\n{items}\n{pager}",
        'summary' => 'Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> items.</br>From <b>' . $searchModel->createTimeStart . ' </b> to <b>' . $searchModel->createTimeEnd . ' </b>',
        'columns' => [
            [
                'attribute' => 'al_id',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_id;
                },
                'options' => ['style' => 'width:100px']
            ],
            [
                'attribute' => 'al_action',
                'value' => function (\common\models\ApiLog $model) {
                    return '<b>' . Html::encode($model->al_action) . '</b>';
                },
                'format' => 'raw',
                'filter' => \common\models\ApiLog::getActionFilter(),
            ],
            [
                'label' => 'Relative Time',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '' . Yii::$app->formatter->asRelativeTime(strtotime($model->al_request_dt)) : '-';
                },
            ],
            [
                'attribute' => 'al_request_data',
                'format' => 'raw',
                'value' => static function (\common\models\ApiLog $model) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->al_request_data, true, 512, JSON_THROW_ON_ERROR)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            1200,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->al_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->al_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'attribute' => 'al_request_dt',
                'value' => static function (\common\models\ApiLog $model) {
                    if (!$model->al_request_dt) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' .
                        Yii::$app->formatter->asDatetime(strtotime($model->al_request_dt), 'php:d-M-Y [H:i]');
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:180px;'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'createTimeRange',
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'createTimeStart',
                    'endAttribute' => 'createTimeEnd',
                    'pluginOptions' => [
                        'maxDate' => date("Y-m-d 23:59"),
                        'applyButtonClasses' => 'applyBtn btn btn-sm btn-success',
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ],
                ]),
            ],
            [
                'attribute' => 'al_response_data',
                'format' => 'raw',
                'value' => function (\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize(mb_strlen($model->al_response_data), 1);
                },
            ],
            [
                'attribute' => 'al_execution_time',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_execution_time;
                },
            ],
            [
                'attribute' => 'al_memory_usage',
                'format' => 'raw',
                'value' => function (\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize($model->al_memory_usage, 2);
                },
            ],
            [
                'attribute' => 'al_db_execution_time',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_db_execution_time;
                },
            ],
            [
                'attribute' => 'al_db_query_count',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_db_query_count;
                },
            ],
            [
                'attribute' => 'al_user_id',
                'value' => function (\common\models\ApiLog $model) {
                    $apiUser = \common\models\ApiUser::findOne($model->al_user_id);
                    return $apiUser ? $apiUser->au_name . ' (' . $model->al_user_id . ')' : $model->al_user_id;
                },
                'filter' => \common\models\ApiUser::getList()
            ],
            'al_ip_address',
            ['class' => ActionColumn::class],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Log detail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail Api Log (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
