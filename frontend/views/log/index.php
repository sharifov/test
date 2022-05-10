<?php

use kartik\select2\Select2;
use src\auth\Auth;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'System Logs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-log';
?>
<div class="log-index">

    <h1><i class="fa fa-bug"></i> <?= Html::encode($this->title) ?></h1>

    <?php if (Auth::can('global/clean/table')) : ?>
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 12px;">
                <?php echo Html::a('<i class="fas fa-remove"></i> Truncate Log table', null, ['class' => 'btn btn-danger js_truncate_btn'])?>
            </div>

            <div class="col-md-12" >
                <?php echo $this->render(
                    '_clean_table_form',
                    [
                        'modelCleaner' => $modelCleaner,
                        'pjaxIdForReload' => $pjaxListId,
                    ]
                ) ?>
            </div>
        </div>
    <?php endif ?>

    <?php \yii\widgets\Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>

    <div class="row">
        <div class="col-md-12">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{errors}\n{pager}\n{summary}\n{items}\n{pager}",
                'columns' => [
                    //'id',
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['style' => 'width: 70px;text-align:center;']
                    ],
                    //'level',
                    [
                        'attribute' => 'level',
                        'value' => static function (\frontend\models\Log $model) {
                            return \yii\log\Logger::getLevelName($model->level) . ' (' . $model->level . ')';
                        },
                        'filter' => [
                            \yii\log\Logger::LEVEL_ERROR            => 'error',
                            \yii\log\Logger::LEVEL_WARNING          => 'warning',
                            \yii\log\Logger::LEVEL_INFO             => 'info',
                            \yii\log\Logger::LEVEL_TRACE            => 'trace',
                            \yii\log\Logger::LEVEL_PROFILE_BEGIN    => 'prof begin',
                            \yii\log\Logger::LEVEL_PROFILE_END      => 'prof end'
                        ],
                        'contentOptions' => ['style' => 'width: 100px;text-align:center;']
                    ],

                    //'category',

                    [
                        'attribute' => 'category',
                        'filter' =>  Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'category',
                            'data' => \frontend\models\Log::getCategoryFilter(is_numeric($searchModel->level) ? $searchModel->level : null, Yii::$app->request->isPjax),
                            //'value' => 'category',

                            //'name' => Html::getInputName($filter, 'channelId'),
                            'size' => Select2::SIZE_SMALL,

                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'Select category'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'selectOnClose' => false,
                            ]
                        ]),

                        //'format' => 'username',
                        'options' => [
                            'width' => '300px'
                        ]
                    ],



//                    [
//                        'attribute' => 'category',
//                        'filter' => \frontend\models\Log::getCategoryFilter(is_numeric($searchModel->level) ? $searchModel->level : null, Yii::$app->request->isPjax),
//                        'contentOptions' => ['style' => 'width: 200px;text-align:center;']
//                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'raw',
                        'value' => static function (\frontend\models\Log $model) {
                            $str = '<pre><small>' . (\yii\helpers\StringHelper::truncate($model->message, 400, '...', null, true)) . '</small></pre> 
                            <a href="' . \yii\helpers\Url::to(['log/view', 'id' => $model->id]) . '" title="Log ' . $model->id . '" class="btn btn-sm btn-success showModalButton" data-pjax="0"><i class="fas fa-eye"></i> details</a>';
                            return ($str);
                        },
                        //'contentOptions'=>['style'=>'width: 100px;text-align:left;']
                    ],

                    [
                        'attribute' => 'log_time',
                        'value' => static function (\frontend\models\Log $model) {
                            return '<span title="' . date("d-M-Y [H:i:s]", $model->log_time) . ' UTC">' . Yii::$app->formatter->asDatetime($model->log_time, 'php:d-M-Y [H:i:s]') . '</span>';
                        },
                        'format' => 'raw',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'log_time',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy'
                            ]
                        ]),
                        'contentOptions' => ['style' => 'width: 180px;text-align:center;']
                    ],

                    [
                        'attribute' => 'prefix',
                        'format' => 'html',
                        'value' => static function (\frontend\models\Log $model) {
                            return '<small>' . ($model->prefix) . '</small>';
                        },
                        'contentOptions' => ['style' => 'width: 100px;text-align:left;']
                    ],

                    //'log_time:datetime',
                    //'prefix:ntext',
        //             'message:ntext',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}'],
                    //['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
                ],
            ]); ?>

        </div>
    </div>

    <?php \yii\widgets\Pjax::end(); ?>
</div>

<div id="confirm_html" style="display: none;">
    <p>Host: <b class="text-danger"><?php echo strtoupper(Yii::$app->params['appHostname'] ?? '') ?></b></p>
    <p>Remove all records from logs?</p>
    <?php echo Html::a('<i class="fa fa-trash-o"></i> Truncate Log table', ['log/clear'], ['class' => 'btn btn-danger']) ?>
    <?php echo Html::a('Close', null, ['class' => 'btn btn-secondary js_close_modal']) ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
        'title' => 'Log detail',
        'id' => 'modal',
        'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    ]);
yii\bootstrap4\Modal::end();

yii\bootstrap4\Modal::begin([
        'title' => '<i class="fa fa-exclamation-triangle text-warning"></i> Are you sure?',
        'id' => 'modal_small',
        'size' => \yii\bootstrap4\Modal::SIZE_SMALL,
    ]);
yii\bootstrap4\Modal::end();


$ajaxUrl = \yii\helpers\Url::to(['/log/ajax-category-list']);
$categoryValue = $searchModel->category ? md5($searchModel->category) : '';

$jsCode = <<<JS

    $(document).on('click', '.js_close_modal', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#modal_small').modal('hide');
        return false;
    });

    $(document).on('click', '.js_truncate_btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let htmlData = $('#confirm_html').html();
        $('#modal_small .modal-body').html(htmlData);
        $('#modal_small').modal('show');
        return false;
    });

    let ajaxUrlCategoryList = '$ajaxUrl';
    let categoryValue = '$categoryValue';

    function updateCategoryList() {
        $.getJSON(ajaxUrlCategoryList, function(response) {
            let obj = $( "select[name='LogSearch[category]']" );
        
            obj.html('');
            obj.append('<option value=""></option>');
            
            //$( response.data).each(function( item ) {
            $.each(response.data, function(){
                let selected = '';
                
                if (categoryValue === this.hash) {
                    selected = 'selected';
                }
                obj.append('<option value="'+ this.name +'" ' + selected + '>'+ this.name +' - ['+ this.cnt +']</option>')
            });

        });
    }

    setTimeout(updateCategoryList, 2000);
    
    $(document).on('click', '.showModalButton', function(){
        $('#modal').modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modal-title').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
          $('#modal .modal-body').html(data);
        });
       return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
