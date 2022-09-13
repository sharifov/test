<?php

use common\components\grid\DateTimeColumn;
use src\model\leadRequest\entity\LeadRequest;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var src\model\leadRequest\entity\LeadRequestSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lead Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-request', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'lr_id',
            [
                'attribute' => 'lr_type',
                'value' => static function (LeadRequest $model) {
                    return LeadRequest::TYPE_LIST[$model->lr_type] ?? '-';
                },
                'filter' => LeadRequest::TYPE_LIST
            ],
            'lr_job_id',
            [
                'attribute' => 'lr_lead_id',
                'value' => static function (LeadRequest $model) {
                    return Yii::$app->formatter->asLead($model->lead, 'fa-cubes');
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lr_json_data',
                'format' => 'raw',
                'value' => static function (LeadRequest $model) {
                    $resultStr = '-';
                    if ($decodedData = \frontend\helpers\JsonHelper::encode($model->lr_json_data)) {
                        $decodedData = \common\helpers\LogHelper::replaceSource($decodedData);
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            1200,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->lr_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->lr_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lr_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Detail',
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
        $('#modal-label').html('Detail (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
