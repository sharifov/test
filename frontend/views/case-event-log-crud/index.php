<?php

use yii\helpers\Html;
use yii\grid\GridView;
use src\entities\cases\CaseEventLog;
use frontend\helpers\JsonHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel src\entities\cases\CaseEventLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Event Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-event-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Event Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(['id' => 'pjax-lead-request', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cel_id',
            [
                'attribute' => 'cel_case_id',
                'value' => static function (CaseEventLog $model) {
                    return $model->cel_case_id ? Html::a(
                        $model->cel_case_id  . ' <span class="glyphicon glyphicon-eye-open"></span>',
                        ['/cases/view', 'gid' => $model->celCase->cs_gid],
                        ['target' => '_blank', 'data-pjax' => 0, 'title' => 'View']
                    ) : '-';
                },
                'format' => 'raw',
            ],
            'cel_description',
            [
                'attribute' => 'cel_type_id',
                'value' => static function (CaseEventLog $model) {
                    return $model->cel_type_id ? CaseEventLog::CASE_EVENT_LOG_LIST[$model->cel_type_id] : null;
                },
                'format' => 'raw',
                'filter' => CaseEventLog::getEventLogList()
            ],
            [
                'attribute' => 'cel_category_id',
                'value' => static function (CaseEventLog $model) {
                    return $model->getCategoryNameFormat();
                },
                'format' => 'raw',
                'filter' => CaseEventLog::getCategoryList()
            ],
            //'cel_data_json',
            [
                'attribute' => 'cel_data_json',
                'format' => 'raw',
                'value' => static function (CaseEventLog $model) {
                    $resultStr = '-';
                    if ($decodedData = JsonHelper::decode($model->cel_data_json)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            300,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->cel_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->cel_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cel_created_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
Modal::begin([
    'title' => 'Detail',
    'id' => 'modal',
    'size' => Modal::SIZE_DEFAULT,
]);
Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
