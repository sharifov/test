<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm \frontend\models\LeadMultipleForm */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
.dropdown-menu {
    z-index: 1010;
}
</style>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) : ?>
        <p>
            <?//= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info', 'data-toggle'=> "modal", 'data-target'=>"#modalUpdate" ])?>
        </p>
    <?php endif; ?>


    <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

    <?php



        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],


            [
                'class'       => '\kartik\grid\CheckboxColumn',
                'name' => 'LeadMultipleForm[lead_list]',
                'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_INFO,
            ],

            /*[
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'LeadMultipleForm[lead_list]'
                    'checkboxOptions' => function(\common\models\Lead $model) {
                        return ['value' => $model->id];
                    },
            ],*/

            /*[

                'header'=>Html::checkbox('selection_all', false, ['class'=>'select-on-check-all', 'value'=>1,
                    'onclick'=>'
                        $(".kv-row-checkbox").prop("checked", $(this).is(":checked"));
                        if($(".kv-row-checkbox").prop("checked") === true) $(".delete_ready").attr("class","delete_ready warning");
                        if($(".kv-row-checkbox").prop("checked") === false) $(".delete_ready").attr("class","delete_ready");


                        ']),
                'contentOptions'=>['class'=>'kv-row-select'],
                'content'=>function($model, $key){


                        return Html::checkbox('id[]', false, ['class'=>'kv-row-checkbox ',
                            'value'=>$key, 'onclick'=>'$(this).closest("tr").toggleClass("warning");']);

                    //return Html::checkbox('selection[]', false, ['class'=>'kv-row-checkbox', 'value'=>$key, 'onclick'=>'$(this).closest("tr").toggleClass("danger");', 'disabled'=> isset($model->stopDelete)&&!($model->stopDelete===1)]);
                },
                'hAlign'=>'center',
                'vAlign'=>'middle',
                'hiddenFromExport'=>true,
                'mergeHeader'=>true,
                'width'=>'50px'
            ],*/

            [
                'attribute' => 'id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'uid',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [   'attribute' => 'client_id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                //'attribute' => 'client_id',
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->client ? '<i class="fa fa-user"></i> ' . Html::encode($model->client->first_name.' '.$model->client->last_name) : '-';
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'header' => 'Client Emails/Phones',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')).'' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> '.implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')).'' : '';

                    return $str ?? '-';
                },
                'options' => ['style' => 'width:180px'],
            ],

            /*[
                'header' => 'Client Phones',
                'value' => function(\common\models\Lead $model) {
                    return $model->client && $model->client->clientPhones ? implode(', ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) : '-';
                },
            ],*/

            //'employee_id',
            //'status',
            [
                'attribute' => 'status',
                'value' => function(\common\models\Lead $model) {
                    return $model->getStatusName(true);
                },
                'format' => 'html',
                'filter' => \common\models\Lead::STATUS_LIST
            ],
            [
                'attribute' => 'project_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],


            //'project_id',
            //'source_id',
            [
                'attribute' => 'source_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->source ? $model->source->name : '-';
                },
                'filter' => \common\models\Source::getList()
            ],

            [
                'attribute' => 'trip_type',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],

            [
                'attribute' => 'cabin',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],

            //'trip_type',
            //'cabin',
            //'adults',

            [
                'attribute' => 'adults',
                'value' => function(\common\models\Lead $model) {
                    return $model->adults ?: 0;
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'children',
                'value' => function(\common\models\Lead $model) {
                    return $model->children ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'infants',
                'value' => function(\common\models\Lead $model) {
                    return $model->infants ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],


            [
                'header' => 'Quotes',
                'value' => function(\common\models\Lead $model) {
                    return $model->quotesCount ? Html::a($model->quotesCount, ['quote/index', "QuoteSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'header' => 'Segments',
                'value' => function(\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1).'. <code>'.Html::a($segment->origin.'->'.$segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return ''.$segmentStr.'';
                    //return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'options' => ['style' => 'width:140px'],
            ],

            //'children',
            //'infants',
            //'notes_for_experts:ntext',

            //'updated',
            //'request_ip',
            //'request_ip_detail:ntext',

            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],

            //'rating',
            //'called_expert',
            /*[
                'attribute' => 'discount_id',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],*/
            //'offset_gmt',
            //'snooze_for',
            //'created',
            [
                'attribute' => 'created',
                'value' => function(\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->created);
                },
                'format' => 'html',
            ],

            /*[
                'attribute' => 'updated',
                'value' => function(\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->updated);
                },
                'format' => 'html',
            ],*/
            //'bo_flight_id',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ];

        /*Yii::$app->state = Yii::$app::STATE_END;



        $fullExportMenu = \kartik\export\ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumnsExport,
            'fontAwesome' => true,
            //'stream' => false, // this will automatically save file to a folder on web server
            //'deleteAfterSave' => false, // this will delete the saved web file after it is streamed to browser,
            //'batchSize' => 10,
            'target' => \kartik\export\ExportMenu::TARGET_BLANK,
            'linkPath' => '/assets/',
            'folder' => '@webroot/assets', // this is default save folder on server
            'dropdownOptions' => [
                'label' => 'Full Export'
            ],
            'columnSelectorOptions' => [
                'label' => 'Export Fields'
            ]
        ]);*/



    /*$fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'batchSize' => 10,
        'columns' => $gridColumns,
        'target' => ExportMenu::TARGET_BLANK,
        'fontAwesome' => true,
        'asDropdown' => false, // this is important for this case so we just need to get a HTML list
        'dropdownOptions' => [
            'label' => '<i class="glyphicon glyphicon-export"></i> Full'
        ],
    ]);*/

?>



<?php

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false

        /*'export' => [
            'label' => 'Page',
            'fontAwesome' => true,
            'itemsAfter'=> [
                '<li role="presentation" class="divider"></li>',
                '<li class="dropdown-header">Export All Data</li>',
                $fullExportMenu
            ]
        ],*/


        'columns' => $gridColumns,

        'toolbar' =>  [
            ['content'=>
                //Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>'Add Lead', 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['leads/index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])

            ],
            //'{export}',
            //$fullExportMenu,
            '{toggleData}'
        ],
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        //'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 20],
        /*'showPageSummary' => true,*/
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads</h3>',
        ],

    ]); ?>


    <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) : ?>
        <p>
            <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info', 'data-toggle'=> "modal",
                'data-target'=>"#modalUpdate",
            ]) ?>
        </p>

        <?= $form->errorSummary($multipleForm); ?>

        <?php \yii\bootstrap\Modal::begin([
                'header' => '<b>Multiple update selected Leads</b>',
                //'toggleButton' => ['label' => 'click me'],
                'id' => 'modalUpdate',
                //'size' => 'modal-lg',
             ]);
        ?>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $form->field($multipleForm, 'status_id')->dropDownList(\common\models\Lead::STATUS_MULTIPLE_UPDATE_LIST, ['prompt' => '-', 'id' => 'status_id']) ?>

                        <div id="reason_id_div" style="display: none">
                            <?= $form->field($multipleForm, 'reason_id')->dropDownList(\common\models\Reason::getReasonListByStatus(\common\models\Lead::STATUS_PROCESSING), ['prompt' => '-', 'id' => 'reason_id']) // \common\models\Lead::STATUS_REASON_LIST ?>

                            <div id="reason_description_div" style="display: none">
                                <?= $form->field($multipleForm, 'reason_description')->textarea(['rows' => '3']) ?>
                            </div>
                        </div>

                        <?php
                            $emplData = \common\models\Employee::getList();
                            $emplData[-1] = '--- REMOVE EMPLOYEE ---';

                            //$emplData = array_merge(['-1' => '--- REMOVE EMPLOYEE ---'], $emplData);
                        ?>
                        <?= $form->field($multipleForm, 'employee_id')->dropDownList($emplData, ['prompt' => '-']) ?>
                        <div class="form-group text-right">
                            <?= Html::submitButton('<i class="fa fa-check-square"></i> Update selected Leads', ['class' => 'btn btn-info']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php \yii\bootstrap\Modal::end(); ?>
    <?php endif; ?>

    <?php \yii\bootstrap\ActiveForm::end(); ?>


    <?php Pjax::end(); ?>


<?php
$ajaxUrl = \yii\helpers\Url::to(["leads/ajax-reason-list"]);
$js = <<<JS
 
    $(document).on('pjax:start', function() {
        $("#modalUpdate .close").click();
    });

    $(document).on('change', '#reason_id', function() {
        if( $(this).val() == '0' ) {
            $('#reason_description_div').show();
        }  else {
            $('#reason_description_div').hide();
        }
    });
    
     $(document).on('change', '#status_id', function() {
         var status_id = $(this).val(); 
        if( status_id > 0 ) {
            $('#reason_id_div').show();
            
           $.post("$ajaxUrl",{status_id: status_id}, function( data ) {
                $("#reason_id").html( data ).trigger('change');
           })
                        
        }  else {
            $('#reason_id_div').hide();
        }
    });


JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>


</div>
