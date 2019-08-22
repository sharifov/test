<?php

/* @var $newCount integer */

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
    <div id="incoming-call-widget" style="background-color: rgba(255,255,255,.3);">
        <div class="row" style="margin-top: 4px;  margin-right: 0px; margin-left: 1px">
            <div class="col-md-3" style="padding-top: 5px;">
                <span class="badge badge-info">Wefare</span>
                <span class="badge label-info">Exchange</span>
                <span class="label label-warning">Direct</span>
            </div>
            <div class="col-md-5 text-right" style="padding-top: 3px; padding-bottom: 4px; " id="demo">

                <span class="badge badge-awake" style="font-size: 14px"><span class="fa fa-phone fa-spin"></span> +37369457896</span>
                <span class="badge badge-warning"><i class="fa fa-clock-o"></i> 18:35</span>
            </div>
            <div class="col-md-4 text-right">

                <?=\yii\helpers\Html::button('<i class="fa fa-check"></i> Accept', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-incoming-call-success'])?>
                <?=\yii\helpers\Html::button('<i class="fa fa-angle-double-right"></i> Skip', ['class' => 'btn btn-sm btn-info', 'id' => 'btn-incoming-call-skip'])?>
                <?=\yii\helpers\Html::button('<i class="fa fa-close"></i> Busy', ['class' => 'btn btn-sm btn-danger', 'id' => 'btn-incoming-call-busy'])?>
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
<?php Pjax::end() ?>


<?php
    $incomingCallUrl = \yii\helpers\Url::to(['/call/incoming-call-widget']);
?>

<script>
    const incomingCallUrl = '<?=$incomingCallUrl?>';
    function refreshInboxCallWidget()
    {
        // console.log(obj);
        $.pjax.reload({url: incomingCallUrl, container: '#incoming-call-pjax', push: false, replace: false, 'scrollTo': false, timeout: 10000, async: false}); // , data: {id: obj.id, status: obj.status}
    }

</script>

<?php
$callStatusUrl = \yii\helpers\Url::to(['user-call-status/update-status']);
$clientInfoUrl = \yii\helpers\Url::to(['client/ajax-get-info']);
$missedCallsUrl = \yii\helpers\Url::to(['call/ajax-missed-calls']);
$callInfoUrl = \yii\helpers\Url::to(['call/ajax-call-info']);

$userId = Yii::$app->user->id;

$js = <<<JS

    var callStatusUrl = '$callStatusUrl';
    var clientInfoUrl = '$clientInfoUrl';
    var missedCallsUrl = '$missedCallsUrl';
    var callInfoUrl = '$callInfoUrl';
    

    /*$(document).on('click', '#btn-client-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        $('#call-box-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#call-box-modal .modal-title').html('Client Details (' + client_id + ')');
        $('#call-box-modal').modal();
        $.post(clientInfoUrl, {client_id: client_id},
            function (data) {
                $('#call-box-modal .modal-body').html(data);
            }
        );
    });
    
    $(document).on('click', '#btn-missed-calls', function(e) {
        e.preventDefault();
        $('#call-box-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#call-box-modal .modal-title').html('Missed Calls');
        $('#call-box-modal').modal();
        $.post(missedCallsUrl, 
            function (data) {
                $('#call-box-modal .modal-body').html(data);
            }
        );
    });
    
    $(document).on('click', '#btn-call-info', function(e) {
        e.preventDefault();
        var callId = $(this).data('call-id');
        $('#call-box-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#call-box-modal .modal-title').html('Call Info');
        $('#call-box-modal').modal();
        $.post(callInfoUrl, {id: callId}, 
            function (data) {
                $('#call-box-modal .modal-body').html(data);
            }
        );
    });*/
    
   
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
    
    $('#incoming-call-widget').css({left:'50%', 'margin-left':'-'+($('#incoming-call-widget').width() / 2)+'px'}); //.slideDown();

JS;

$this->registerJs($js, \yii\web\View::POS_READY);
$this->registerJs("$('#incoming-call-widget').slideDown();", \yii\web\View::POS_READY);