<?php

/* @var \common\models\Call $call  */
/* @var \common\models\CallUserAccess $callUserAccess  */



\frontend\assets\CallBoxAsset::register($this);
\frontend\assets\TimerAsset::register($this);

use yii\widgets\Pjax;

?>

<style>
#incoming-call-widget {
    position: fixed;
    width: 100%;
    max-width: 800px;

    padding: 1px;
    top: 1px;
    border: 2px solid #a3b3bd;
    /*margin-left: 50%;*/
    /*box-shadow: 3px 3px 3px rgba(0, 0, 0, .3);*/
    z-index: 999;
    display: none;
    /*height: 600px;*/
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 4px;
    /*margin-left: -50px;*/
    /*margin-left: calc(100% - calc(width / 2));*/
}

@keyframes blinking {
    0%{
        background-color: rgba(255, 48, 0, 0.34);
    }
    100%{
        background-color: rgba(255,255,255,.3);
    }
}
#incoming-call-widget{
    /*font-size: 1.3em;
    font-weight: bold;
    padding: 10px;*/

    animation: blinking 1s infinite;
}

</style>

<?php Pjax::begin(['id' => 'incoming-call-pjax', 'timeout' => 10000, 'enablePushState' => false, 'enableReplaceState' => false, 'options' => []])?>
    <?php if ($call):?>
    <div id="incoming-call-widget" style="background-color: rgba(255,255,255,.3);">
        <div class="row" style="margin-top: 4px;  margin-right: 0px; margin-left: 1px">
            <div class="col-md-3" style="padding-top: 5px;">
                <?php if($call->cProject):?> <span class="badge badge-info"><?=\yii\helpers\Html::encode($call->cProject->name)?></span> <?php endif; ?>
                <?php if($call->cDep):?> <span class="badge badge-info"><?=\yii\helpers\Html::encode($call->cDep->dep_name)?></span> <?php endif; ?>
                <?php if($call->c_source_type_id):?> <span class="label label-warning"><?=\yii\helpers\Html::encode($call->getSourceName())?></span> <?php endif; ?>
            </div>
            <div class="col-md-5 text-right" style="padding-top: 3px; padding-bottom: 4px; ">
                <span class="badge badge-awake" style="font-size: 14px"><span class="fa fa-phone fa-spin"></span> <?=\yii\helpers\Html::encode($call->c_from)?></span>
                <?php
                    $durationSec =  $callUserAccess->cua_created_dt ? (time() - strtotime($callUserAccess->cua_created_dt)) : 0;
                ?>
                <span class="badge badge-warning"><i class="fa fa-clock-o"></i> <span class="timer" data-sec="<?=$durationSec?>" data-control="start" data-format="%M:%S" title="<?=Yii::$app->formatter->asDuration($durationSec)?>"> 00:00 </span></span>

            </div>
            <div class="col-md-4 text-right">

                <?=\yii\helpers\Html::a('<i class="fa fa-check"></i> Accept ' . date('i:s'), ['call/incoming-call-widget', 'act' => 'accept', 'call_id' => $call->c_id], ['class' => 'btn btn-sm btn-success', 'id' => 'btn-incoming-call-success'])?>
                <?//=\yii\helpers\Html::a('<i class="fa fa-angle-double-right"></i> Skip', ['call/incoming-call-widget', 'act' => 'skip', 'call_id' => $call->c_id], ['class' => 'btn btn-sm btn-info', 'id' => 'btn-incoming-call-skip'])?>
                <?=\yii\helpers\Html::a('<i class="fa fa-close"></i> Busy', ['call/incoming-call-widget', 'act' => 'busy', 'call_id' => $call->c_id], ['class' => 'btn btn-sm btn-danger', 'id' => 'btn-incoming-call-busy'])?>
            </div>
        </div>
        <?/*<table class="table" style="margin: 0; background-color: rgba(255,255,255,.3);">
            <tr>
                <td style="width:100px"><i class="fa fa-user"></i> <span></span></td>
                <td style="width:120px">
                    <div class="text-right">
                        <?=\yii\helpers\Html::button('<i class="fa fa-close"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-incoming-call-close'])?>
                    </div>
                </td>
            </tr>
        </table>
        */?>
    </div>
    <?php
        $this->registerJs('ion.sound.play("incoming_call", {loop: 10});', \yii\web\View::POS_READY);
    ?>

    <?php else: ?>

    <?php
        $this->registerJs('ion.sound.stop("incoming_call");', \yii\web\View::POS_READY);
    ?>
    <?php endif; ?>
<?php Pjax::end() ?>


<?php
    $incomingCallUrl = \yii\helpers\Url::to(['/call/incoming-call-widget']);
?>

<script>
    const incomingCallUrl = '<?=$incomingCallUrl?>';
    function refreshInboxCallWidget(obj)
    {
        // console.log(obj);
        $.pjax.reload({url: incomingCallUrl, container: '#incoming-call-pjax', push: false, replace: false, 'scrollTo': false, timeout: 10000, async: false}); // , data: {id: obj.id, status: obj.status}
    }
</script>

<?php
$clientInfoUrl = \yii\helpers\Url::to(['client/ajax-get-info']);
$userId = Yii::$app->user->id;

$js = <<<JS
   
   
    /*$(document).on('change', '#user-call-status-select', function(e) {
        e.preventDefault();
        var type_id = $(this).val();
                
        $.ajax({
            type: 'post',
            data: {'type_id': type_id},
            url: callStatusUrl,
            success: function (data) {
                // //console.log(data);
                // $('#preloader').addClass('hidden');
                // modal.find('.modal-body').html(data);
                // modal.modal('show');
            },
            error: function (error) {
                console.error('Error: ' + error);
            }
        });

    });
   */

    /*$("#call-box-pjax").on("pjax:start", function() {
        $('.prime').addClass('fa-recycle fa-spin');
    });
    
    $("#call-box-pjax").on("pjax:end", function() {
        $('.prime').removeClass('fa-recycle fa-spin');
    });*/
    
    
    
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
    
    $("#incoming-call-pjax").on("pjax:end", function() {
        initIncomingCallWidget();
    });
    
    function initIncomingCallWidget()
    {
        $('#incoming-call-widget').css({left:'50%', 'margin-left':'-'+($('#incoming-call-widget').width() / 2)+'px'}); //.slideDown();
        startTimers();
        $('#incoming-call-widget').slideDown();
    }
    
    initIncomingCallWidget();

JS;

$this->registerJs($js, \yii\web\View::POS_READY);
//$this->registerJs("$('#incoming-call-widget').slideDown();", \yii\web\View::POS_READY);