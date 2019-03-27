<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProvider2 yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'User Call Map';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/
$bundle = \frontend\assets\TimerAsset::register($this);
$userId = Yii::$app->user->id;
?>

<style>
    #call-map-page table {margin-bottom: 5px}
</style>

<div id="call-map-page">

<?php Pjax::begin(['id' => 'pjax-call-list']); ?>

    <div class="">

        <?/*<div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()->where(['or', ['c_to' => $phoneList], ['c_from' => $phoneList], ['c_created_user_id' => Yii::$app->user->id]])
                        ->andWhere(['c_is_new' => true, 'c_is_deleted' => false])->count()?>
                </div>
                <h3>New Calls</h3>
                <p>Total new Calls</p>
            </div>
        </div>*/?>

        <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-users"></i></div>
                <div class="count">
                    <?=\common\models\UserConnection::find()->select('uc_user_id')->groupBy(['uc_user_id'])->count()?> /
                    <?=\common\models\UserConnection::find()->count()?>
                </div>
                <h3>Online Agents</h3>
                <p>Current state Online Employees / Connections</p>
            </div>
        </div>

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN, 'DATE(c_created_dt)' => 'DATE(NOW())'])->count()?>
                </div>
                <h3>Today Incoming Calls</h3>
                <p>Today count of incoming Calls</p>
            </div>
        </div>

        <div class="animated flipInY col-md-3 col-sm-2 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT, 'DATE(c_created_dt)' => 'DATE(NOW())'])->count()?>
                </div>
                <h3>Today Outgoing Calls</h3>
                <p>Today count of outgoing Calls</p>
            </div>
        </div>

        <?php /*
            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count"><?=\frontend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
                    <h3>System Logs</h3>
                    <p>Today count of System Logs</p>
                </div>
            </div>
            */ ?>
    </div>

    <div class="clearfix"></div>

<div class="col-md-6">

    <?/*<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>*/?>


    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> User Call Map (<?=Yii::$app->formatter->asTime(time(), 'php:H:i:s')?>)</div>
        <div class="panel-body">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,

                /*'options' => [
                    'tag' => 'div',
                    'class' => 'list-wrapper',
                    'id' => 'list-wrapper',
                ],*/
                'emptyText' => '<div class="text-center">Not found calls</div><br>',
                'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n

                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_list_item',['model' => $model]);
                },

                'itemOptions' => [
                    //'class' => 'item',
                    //'tag' => false,
                ],
                /*'pager' => [
                    'firstPageLabel' => 'first',
                    'lastPageLabel' => 'last',
                    'nextPageLabel' => 'next',
                    'prevPageLabel' => 'previous',
                    'maxButtonCount' => 3,
                ],*/

            ]) ?>
        </div>
    </div>


</div>


<div class="col-md-6">

    <?/*<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>*/?>


    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> Last 10 Calls</div>
        <div class="panel-body">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider2,

                /*'options' => [
                    'tag' => 'div',
                    'class' => 'list-wrapper',
                    'id' => 'list-wrapper',
                ],*/
                'emptyText' => '<div class="text-center">Not found calls</div><br>',
                //'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_list_item',['model' => $model]);
                },

                'itemOptions' => [
                    //'class' => 'item',
                    //'tag' => false,
                ],

                /*'pager' => [
                    'firstPageLabel' => 'first',
                    'lastPageLabel' => 'last',
                    'nextPageLabel' => 'next',
                    'prevPageLabel' => 'previous',
                    'maxButtonCount' => 3,
                ],*/

            ]) ?>
        </div>
    </div>


</div>

<?php Pjax::end(); ?>
    <div class="text-center">
        <?=Html::button('Refresh Data', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-user-call-map-refresh'])?>
    </div>
</div>



<?php
$js = <<<JS

    function startTimers() {
    
        $(".timer").each(function( index ) {
            var sec = $( this ).data('sec');
            var control = $( this ).data('control');
            var format = $( this ).data('format');
            //var id = $( this ).data('id');
            //$( this ).addClass( "foo" );
            $(this).timer({format: format, seconds: sec}).timer(control);
            //console.log( index + ": " + $( this ).text() );
        });
    
        //$('.timer').timer('remove');
        //$('.timer').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    

    $('#btn-user-call-map-refresh').on('click', function () {
        // $('#modal-dialog').find('.modal-content').html('');
        $.pjax.reload({container:'#pjax-call-list'});
    });

    $(document).on('pjax:start', function() {
        //$("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
        startTimers();
        //alert(2);
    });
    
    startTimers();


JS;
$this->registerJs($js);