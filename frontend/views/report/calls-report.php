<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use sales\ui\user\ListsAccess;

$this->title = 'Calls Report';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\search\CallSearch;
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$list = new ListsAccess(Yii::$app->user->id);

?>
<div class="calls-report-index">
    <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

    <div class="">
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
                /*            if ($user->isAdmin()) {
                                $searchTpl = '_search';
                            } else {
                                $searchTpl = '_search_agents';
                            }
                            */ ?><!--
            --><? /*= $this->render($searchTpl, ['model' => $searchModel]); */ ?>
            </div>
        </div>
    </div>

    <?php
    $gridColumns = [
        //['class' => 'yii\grid\SerialColumn'],
        /*[
            'attribute' => 'id',
        ],*/
        [
            'attribute' => 'username',
            'filter' => $list->getEmployees(),
            'format' => 'raw'
        ],
        [
            'label' => 'Outgoing Calls Duration',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_OUT, null, null);
                return $data[0]['duration'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Outgoing Calls',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_OUT, null, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Outgoing Calls Completed',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_OUT, \common\models\Call::TW_STATUS_COMPLETED, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Outgoing Calls No-Answer',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_OUT, \common\models\Call::TW_STATUS_NO_ANSWER, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Outgoing Calls Canceled',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_OUT, \common\models\Call::TW_STATUS_CANCELED, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Incoming Calls Duration',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, null, null);
                return $data[0]['duration'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Incoming Calls',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, null, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        /*[
            'label' => 'Incoming Calls Completed',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, \common\models\Call::TW_STATUS_COMPLETED, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Incoming Calls No-Answer',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, \common\models\Call::TW_STATUS_NO_ANSWER, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Incoming Calls Canceled',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, \common\models\Call::TW_STATUS_CANCELED, null);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],*/
        [
            'label' => 'Incoming Direct line',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, null, \common\models\Call::SOURCE_DIRECT_CALL);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Incoming General line',
            'value' => function(\common\models\Employee $model){
                $data = $model->getCallsCount($model->id, \common\models\Call::CALL_TYPE_IN, null, \common\models\Call::SOURCE_GENERAL_LINE);
                return $data[0]['cnt'];
            },
            'format' => 'raw'
        ],
    ];

    $fullExportMenu = \kartik\export\ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'fontAwesome' => true,
        //'stream' => false, // this will automatically save file to a folder on web server
        //'deleteAfterSave' => false, // this will delete the saved web file after it is streamed to browser,
        //'batchSize' => 10,
        'target' => \kartik\export\ExportMenu::TARGET_BLANK,
        'linkPath' => '/assets/',
        //'folder' => '@webroot/assets', // this is default save folder on server
        'dropdownOptions' => [
            'label' => 'Full Export'
        ],
        'columnSelectorOptions' => [
            'label' => 'Export Fields'
        ]
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Calls Data</h3>',
        ],
        'export' => [
                'label' => 'Page'
        ],
        'toolbar' => [
                'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/calls-report'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid']),
                '{export}',
            $fullExportMenu,
        ]
    ]);
    ?>
</div>


