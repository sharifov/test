<?php

use common\models\Call;
use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\search\CallSearch;
use sales\auth\Auth;
use yii\bootstrap4\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\models\CallUserAccess;

/* @var $this yii\web\View */

/* @var $salesOnline yii\data\ActiveDataProvider */
/* @var $exchangeOnline yii\data\ActiveDataProvider */
/* @var $supportOnline yii\data\ActiveDataProvider */
/* @var $scheduleChangeOnline yii\data\ActiveDataProvider */
/* @var $withoutDepartmentOnline yii\data\ActiveDataProvider */

/* @var $historyCalls yii\data\ActiveDataProvider */
/* @var $activeCalls yii\data\ActiveDataProvider */
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
function renderChildCallsRecursive($calls): void
{
    ?>

            <table class="table table-condensed">
                <?php foreach ($calls as $callItem) :?>
                    <tr>
                        <td style="width:70px; border: none">
                            <u><?=Html::a($callItem->c_id, ['call/view', 'id' => $callItem->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u><br>
                            <?php if ($callItem->isIn()) : ?>
                                <span class="badge badge-danger">In</span>
                            <?php elseif ($callItem->isOut()) : ?>
                                <span class="badge badge-danger">Out</span>
                            <?php elseif ($callItem->isReturn()) : ?>
                                <span class="badge badge-danger">Return</span>
                            <?php endif; ?>
                        </td>
                        <td style="width: 50px">
                            <?php if ($callItem->c_source_type_id) :?>
                                <span class="label label-info"><?=$callItem->getShortSourceName()?></span>
                            <?php endif; ?>

                            <span class="label label-warning"><?= Department::DEPARTMENT_LIST[$callItem->c_dep_id] ?? '-' ?></span>

                        </td>
                        <td style="width: 120px">
                            <?=$callItem->getStatusIcon()?> <?=$callItem->getStatusName()?>
                        </td>
                        <td style="width: 80px" class="text-left">
                            <?php if ($callItem->c_updated_dt) : ?>
                                <?php if ($callItem->isEnded()) :?>
                                    <?php $sec = $callItem->c_call_duration ?: strtotime($callItem->c_updated_dt) - strtotime($callItem->c_created_dt); ?>
                                    <span class="badge badge-primary timer" data-sec="<?=$sec?>" data-control="pause" data-format="%M:%S" style="font-size: 10px"><?=gmdate('i:s', $sec)?></span>
                                <?php else : ?>
                                    <?php $sec = time() - strtotime($callItem->c_updated_dt); ?>
                                    <span class="badge badge-warning timer" data-sec="<?=$sec?>" data-control="start" data-format="%M:%S"><?=gmdate('i:s', $sec)?></span>
                                <?php endif;?>
                            <?php endif;?>
                            <?php if ($callItem->c_recording_sid) :?>
                                <small><i class="fa fa-play-circle-o"></i></small>
                            <?php endif;?>
                        </td>

                        <td class="text-left">
                            <?php if ($callItem->cuaUsers) :?>
                                <?php foreach ($callItem->callUserAccesses as $cua) :
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
                            <?php if ($callItem->c_updated_dt) : ?>
                                <small>
                                    <?php if ($callItem->isEnded()) :?>
                                        <?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->c_created_dt))?>
                                    <?php endif;?>
                                </small>
                            <?php endif;?>
                        </td>
                        <td class="text-left" style="width:130px">
                            <?php if ($callItem->isIn()) :?>
                                <div>
                                    <?php if ($callItem->c_created_user_id) :?>
                                        <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                                    <?php else : ?>
                                        <i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->c_to)?>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
                                <div>

                                    <?php if ($callItem->c_created_user_id) :?>
                                        <i class="fa fa-user fa-border"></i> <?=Html::encode($callItem->cCreatedUser->username)?>
                                    <?php else : ?>
                                        <i class="fa fa-phone fa-border"></i> <?=Html::encode($callItem->c_to)?>
                                    <?php endif; ?>

                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php getJoinTemplate($callItem); ?>
                        </td>
                    </tr>

                    <?php

                    if ($children = getChildrenForRecursiveRender($callItem->c_id)) :?>
                            <tr>
                                <td colspan="8">
                                    <?php renderChildCallsRecursive($children)?>
                                </td>
                            </tr>

                    <?php endif;?>

                <?php endforeach;?>
            </table>


    <?php
}?>

<?php

function getChildrenForRecursiveRender($parentId)
{
    return CallSearch::find()
        ->select('*')
        ->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN])
        ->andWhere(['c_parent_id' => $parentId])
        ->leftJoin(ConferenceParticipant::tableName(), 'cp_call_id = c_id AND cp_type_id = ' . ConferenceParticipant::TYPE_AGENT . ' AND cp_status_id <> ' . ConferenceParticipant::STATUS_LEAVE . ' AND cp_status_id IS NOT NULL')
        ->all();
}

function getJoinTemplate($model)
{
    /** @var CallSearch $model */
    $callIsTypeAgent = (isset($model->cp_type_id) && ((int)$model->cp_type_id === ConferenceParticipant::TYPE_AGENT));
    if (
        ((bool)(Yii::$app->params['settings']['voip_conference_base'] ?? false) && Auth::can('/phone/ajax-join-to-conference'))
        && $callIsTypeAgent
        && ($model->isIn() || $model->isOut() || $model->isReturn())
        && $model->isStatusInProgress()
    ) {
        ?>
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-phone"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item conference-coach" href="#" onclick="joinListen('<?= $model->c_call_sid ?>');">Listen</a>
                <a class="dropdown-item conference-coach" href="#" onclick="joinCoach('<?= $model->c_call_sid ?>');">Coach</a>
                <a class="dropdown-item conference-coach" href="#" onclick="joinBarge('<?= $model->c_call_sid ?>');">Barge</a>
            </div>
        </div>
        <?php
    }
}

?>

<div id="call-map-page" class="col-md-12">

    <?php Pjax::begin(['id' => 'pjax-call-list', 'timeout' => 4000]); ?>

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

                <?php if ($salesOnline) : ?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"> </i> OnLine - Department SALES (<?= $salesOnline->totalCount?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $salesOnline,
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

                <?php if ($exchangeOnline) : ?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department EXCHANGE (<?= $exchangeOnline->totalCount?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $exchangeOnline,
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

                <?php if ($supportOnline) : ?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SUPPORT (<?= $supportOnline->totalCount ?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $supportOnline,
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

                <?php if ($scheduleChangeOnline) : ?>
                <div class="card card-default" style="margin-bottom: 20px;">
                    <div class="card-header"><i class="fa fa-users"></i> OnLine - Department Schedule Change (<?= $scheduleChangeOnline->totalCount ?>)</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $scheduleChangeOnline,
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

                <?php if ($withoutDepartmentOnline) : ?>
                <div class="card card-default" style="margin-bottom: 20px;">
<!--                    <div class="card-header"><i class="fa fa-users"></i> OnLine Users - W/O Department (--><?php //=$withoutDepartmentOnline->totalCount?><!--)</div>-->
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $withoutDepartmentOnline,
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
                            'dataProvider' => $activeCalls,
                            'emptyText' => '<div class="text-center">Not found calls</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_list_item', ['model' => $model]);
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
                    <div class="card-header"><i class="fa fa-list"></i> Last <?= $historyCalls->count ?> ended Calls</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $historyCalls,

                            /*'options' => [
                                'tag' => 'div',
                                'class' => 'list-wrapper',
                                'id' => 'list-wrapper',
                            ],*/
                            'emptyText' => '<div class="text-center">Not found calls</div><br>',
                            //'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model) {
                                return $this->render('_list_item', ['model' => $model]);
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

    $(document).on('click', '.add_users_btn', function (e) {
        e.preventDefault();
        let callId = $(this).data('call-id');
        let modal = $('#modal-df');
        modal.find('.modal-title').html('Add users');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.modal('show');
       
        $.get('/call/get-users-for-call?id=' + callId)
            .done(function( data ) {
                modal.find('.modal-body').html(data);
            }
        );       
        
    });

JS;
$this->registerJs($js);

echo Modal::widget([
    'id' => 'modal-df',
    'title' => '',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_DEFAULT
]);