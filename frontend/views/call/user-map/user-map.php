<?php

use common\models\Department;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use \common\models\CallUserAccess;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */

/* @var $dataProviderOnlineDep1 yii\data\ActiveDataProvider */
/* @var $dataProviderOnlineDep2 yii\data\ActiveDataProvider */
/* @var $dataProviderOnlineDep3 yii\data\ActiveDataProvider */
/* @var $dataProviderOnline yii\data\ActiveDataProvider */

/* @var $dataProvider2 yii\data\ActiveDataProvider */
/* @var $dataProvider3 yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'User Call Map';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/
$bundle = \frontend\assets\TimerAsset::register($this);
$userId = Yii::$app->user->id;
$dtNow = date('Y-m-d H:i:s');
?>

<style>
    #call-map-page table {margin-bottom: 5px}
</style>


<?php
/**
 * @param \common\models\Call[] $calls
 * @throws \yii\base\InvalidConfigException
 */
function renderChildCallsRecursive($calls): void {
    ?>

            <table class="table table-condensed">
                <?php foreach ($calls as $callItem):?>
                    <tr>
                        <td style="width:70px; border: none">
                            <u><?=Html::a($callItem->c_id, ['call/view', 'id' => $callItem->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u><br>
                            <?php if ($callItem->isIn()): ?>
                                <span class="badge badge-danger">In</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Out</span>
                            <?php endif; ?>
                        </td>
                        <td style="width: 50px">
                            <?php if ($callItem->c_source_type_id):?>
                                <span class="label label-info"><?=$callItem->getShortSourceName()?></span>
                            <?php endif; ?>

                            <span class="label label-warning"><?= Department::DEPARTMENT_LIST[$callItem->c_dep_id] ?? '-' ?></span>

                        </td>
                        <td style="width: 120px">
                            <?=$callItem->getStatusIcon()?> <?=$callItem->getStatusName()?>
                        </td>
                        <td style="width: 80px" class="text-left">
                            <?php if($callItem->c_updated_dt): ?>
                                <?php if ($callItem->isEnded()):?>
                                    <?php $sec = $callItem->c_call_duration ?: strtotime($callItem->c_updated_dt) - strtotime($callItem->c_created_dt); ?>
                                    <span class="badge badge-primary timer" data-sec="<?=$sec?>" data-control="pause" data-format="%M:%S" style="font-size: 10px"><?=gmdate('i:s', $sec)?></span>
                                <?php else: ?>
                                    <?php $sec = time() - strtotime($callItem->c_updated_dt); ?>
                                    <span class="badge badge-warning timer" data-sec="<?=$sec?>" data-control="start" data-format="%M:%S"><?=gmdate('i:s', $sec)?></span>
                                <?php endif;?>
                            <?php endif;?>
                            <?php if ($callItem->c_recording_url):?>
                                <small><i class="fa fa-play-circle-o"></i></small>
                            <?php endif;?>
                        </td>

                        <td class="text-left">
                            <?php if($callItem->cuaUsers):?>
                                <?php foreach ($callItem->callUserAccesses as $cua):

                                    switch ((int) $cua->cua_status_id) {
                                        case CallUserAccess::STATUS_TYPE_PENDING:
                                            $label = 'warning';
                                            break;
                                        case CallUserAccess::STATUS_TYPE_ACCEPT:
                                            $label = 'success';
                                            break;
                                        case CallUserAccess::STATUS_TYPE_BUSY:
                                            $label = 'danger';
                                            break;
                                        default:
                                            $label = 'default';
                                    }

                                    ?>
                                    <span class="label label-<?=$label?>"><i class="fa fa-user"></i> <?=Html::encode($cua->cuaUser->username)?></span>&nbsp;
                                <?php endforeach;?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="width:90px">
                            <i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asDatetime(strtotime($callItem->c_created_dt), 'php:H:i')?>
                        </td>
                        <td class="text-center" style="width:180px">
                            <?php if($callItem->c_updated_dt): ?>
                                <small>
                                    <?php if ($callItem->isEnded()):?>
                                        <?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->c_created_dt))?>
                                    <?php endif;?>
                                </small>
                            <?php endif;?>
                        </td>
                        <td class="text-left" style="width:130px">
                            <?php if($callItem->isIn()):?>
                                <div>
                                    <?php if($callItem->c_created_user_id):?>
                                        <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                                    <?php else: ?>
                                        <i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->c_to)?>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div>

                                    <?php if($callItem->c_created_user_id):?>
                                        <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                                    <?php else: ?>
                                        <i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->c_to)?>
                                    <?php endif; ?>

                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php if ($callItem->calls):?>
                            <tr>
                                <td colspan="8">
                                <?php renderChildCallsRecursive($callItem->calls)?>
                                </td>
                            </tr>
                    <?php endif;?>

                <?php endforeach;?>
            </table>


    <?php
}?>

<div id="call-map-page" class="col-md-12">

    <?php Pjax::begin(['id' => 'pjax-call-list']); ?>

        <div class="row">
            <?php /*<div class="animated flipInY col-md-4 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <div class="count">
                        <?=\common\models\UserConnection::find()->select('uc_user_id')
                            //->andWhere(['and', ['<>', 'uc_controller_id', 'call'], ['<>', 'uc_action_id', 'user-map']])
                            ->andWhere(['<>', 'uc_user_id', Yii::$app->user->id])
                            ->groupBy(['uc_user_id'])->count()?>
                    </div>
                    <h3>Agents Online</h3>
                    <p>Current state Online Employees</p>
                </div>
            </div>*/?>


            <?php /*<div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-phone"></i></div>
                    <div class="count">
                        <?=\common\models\Call::find()
                            ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN, 'c_call_status' => \common\models\Call::CALL_STATUS_QUEUE])->count()?>
                    </div>
                    <h3>On HOLD</h3>
                    <p>Calls in HOLD</p>
                </div>
            </div>

            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-phone"></i></div>
                    <div class="count">
                        <?=\common\models\Call::find()
                            ->andWhere(['c_call_status' => \common\models\Call::CALL_STATUS_IN_PROGRESS])->count()?>
                    </div>
                    <h3>In PROGRESS</h3>
                    <p>Calls in PROGRESS</p>
                </div>
            </div>*/?>

            <?php /*<div class="animated flipInY col-md-4 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 1 HOUR)')])->count()?> /
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 1 HOUR)')])->count()?>
                    </div>
                    <h3>In / Out Calls: 1 Hour</h3>
                    <p>Incoming / Outgoing Calls : 1 Hour</p>
                </div>
            </div>*/?>

            <?php /*<div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 6 HOUR)')])->count()?> /
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 6 HOUR)')])->count()?>
                    </div>
                    <h3>In / Out Calls: 6 Hours</h3>
                    <p>Incoming / Outgoing Calls : 6 Hours</p>
                </div>
            </div>*/?>


            <?php /*<div class="animated flipInY col-md-4 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 24 HOUR)')])->count()?> /
                        <?=\common\models\Call::find()->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT])->andWhere(['>=', 'c_created_dt', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 24 HOUR)')])->count()?>
                    </div>
                    <h3>In / Out Calls: 24 Hours</h3>
                    <p>Incoming / Outgoing Calls : 24 Hours</p>
                </div>
            </div>*/?>


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


        <div class="row">
            <div class="col-md-2">
                <?php /*<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>*/?>




                <?php if($dataProviderOnlineDep1):?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SALES (<?=$dataProviderOnlineDep1->totalCount?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProviderOnlineDep1,
                            'emptyText' => '<div class="text-center">Not found online users</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item_online', ['model' => $model, 'index' => $index]);
                            },
                            'itemOptions' => [
                                //'class' => 'item',
                                //'tag' => false,
                            ],
                        ])?>
                    </div>
                </div>
                <?php endif;?>


                <?php if($dataProviderOnlineDep2):?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department EXCHANGE (<?=$dataProviderOnlineDep2->totalCount?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProviderOnlineDep2,
                            'emptyText' => '<div class="text-center">Not found online users</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item_online', ['model' => $model, 'index' => $index]);
                            },
                            'itemOptions' => [
                                //'class' => 'item',
                                //'tag' => false,
                            ],
                        ])?>
                    </div>
                </div>
                <?php endif;?>

                <?php if($dataProviderOnlineDep3):?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SUPPORT (<?=$dataProviderOnlineDep3->totalCount?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProviderOnlineDep3,
                            'emptyText' => '<div class="text-center">Not found online users</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item_online', ['model' => $model, 'index' => $index]);
                            },
                            'itemOptions' => [
                                //'class' => 'item',
                                //'tag' => false,
                            ],
                        ])?>
                    </div>
                </div>
                <?php endif;?>

                <?php if($dataProviderOnline):?>
                <div class="card card-default" style="margin-bottom: 20px;">
<!--                    <div class="card-header"><i class="fa fa-users"></i> OnLine Users - W/O Department (--><?php //=$dataProviderOnline->totalCount?><!--)</div>-->
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProviderOnline,
                            'emptyText' => '<div class="text-center">Not found online users</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item_online', ['model' => $model, 'index' => $index]);
                            },
                            'itemOptions' => [
                                //'class' => 'item',
                                //'tag' => false,
                            ],
                        ])?>
                    </div>
                </div>
                <?php endif;?>

            </div>

            <div class="col-md-5">

                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-list"></i> Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS (Updated: <i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asTime(time(), 'php:H:i:s') ?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $dataProvider3,
                            'emptyText' => '<div class="text-center">Not found calls</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item',['model' => $model]);
                            },
                            'itemOptions' => [
                                //'class' => 'item',
                                //'tag' => false,
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <?php /*<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>*/?>
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-list"></i> Last <?=$dataProvider2->count?> ended Calls</div>
                    <div class="card-body">
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
                            'itemView' => function ($model) {
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
        </div>


    <?php Pjax::end(); ?>

    <div class="text-center hidden">
        <?=Html::button('Refresh Data', ['class' => 'btn btn-sm btn-success hidden', 'id' => 'btn-user-call-map-refresh'])?>
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
    });
    
    startTimers();
        
    /*setTimeout(function runTimerRefresh() {
       $('#btn-user-call-map-refresh').click();
      setTimeout(runTimerRefresh, 30000);
    }, 30000);*/


JS;
$this->registerJs($js);