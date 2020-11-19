<?php

use sales\auth\Auth;
use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Api Logs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-api-log';
?>
<div class="api-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?php //= Html::a('Create Api Log', ['create'], ['class' => 'btn btn-success'])?>
        <?= Html::a('Delete All', ['delete-all'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all items?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if (Auth::can('global/clean/table')): ?>
        <div class="col-md-6" style="margin-left: -10px;">
            <?php echo $this->render('../clean/_clean_table_form', [
                'modelCleaner' => $modelCleaner,
                'pjaxIdForReload' => $pjaxListId,
            ]); ?>
        </div>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'al_id',

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
                    return '<b>'.Html::encode($model->al_action).'</b>';
                },
                'format' => 'raw',
                'filter' => \common\models\ApiLog::getActionFilter()
            ],

            [
                'label' => 'Relative Time',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '' . Yii::$app->formatter->asRelativeTime(strtotime($model->al_request_dt)) : '-';
                },
                //'format' => 'raw'
            ],
            [
                'attribute' => 'al_request_data',
                'format' => 'raw',
                'value' => static function (\common\models\ApiLog $model) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->al_request_data, true, 512, JSON_THROW_ON_ERROR)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            1600,
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
                'class' => DateTimeColumn::class,
                'attribute' => 'al_request_dt'
            ],

            /*[
                'attribute' => 'al_request_dt',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_request_dt), 'php:Y-m-d [H:i:s]') : '-';
                },
                'format' => 'raw'
            ],*/

            //'al_response_data:ntext',
//            [
//                'attribute' => 'al_response_data',
//                'format' => 'html',
//                'value' => function(\common\models\ApiLog $model) {
//                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
//                    //if($data) $data = end($data);
//                    return $data ? '<pre style="font-size: 10px">'.(\yii\helpers\StringHelper::truncate($data, 500, '...', null, true)).'</pre>' : '-';
//                },
//            ],

            [
                'attribute' => 'al_response_data',
                'format' => 'raw',
                'value' => function (\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize(mb_strlen($model->al_response_data), 1);
                //$data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
                    //if($data) $data = end($data);
                    //return $data ? '<small>'.\yii\helpers\StringHelper::truncate(Html::encode($data), 500, '...', null, false).'</small>' : '-';
                },
            ],

            //'al_response_dt',
//            [
//                'attribute' => 'al_response_dt',
//                'value' => static function (\common\models\ApiLog $model) {
//                    return $model->al_response_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_response_dt), 'php:Y-m-d [H:i:s]') : '-';
//                },
//                'format' => 'raw'
//            ],

            [
                'attribute' => 'al_execution_time',
                //'format' => 'html',
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

            //'al_user_id',

            [
                'attribute' => 'al_user_id',
                //'format' => 'html',
                'value' => function (\common\models\ApiLog $model) {
                    $apiUser = \common\models\ApiUser::findOne($model->al_user_id);
                    return $apiUser ? $apiUser->au_name . ' ('.$model->al_user_id.')' : $model->al_user_id;
                },
                'filter' => \common\models\ApiUser::getList()
            ],

            'al_ip_address',

            ['class' => 'yii\grid\ActionColumn'],
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
