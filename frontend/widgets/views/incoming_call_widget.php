<?php

/* @var $newCount integer */
/* @var $lastCall \common\models\Call */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $countMissedCalls integer*/

//\frontend\assets\CallBoxAsset::register($this);
\frontend\assets\TimerAsset::register($this);

use yii\widgets\Pjax;

?>

<?php Pjax::begin(['id' => 'incoming-call-pjax', 'timeout' => 10000, 'enablePushState' => false, 'enableReplaceState' => false, 'options' => []])?>
    <?=date('Y-m-d H:i:s')?>
<?php Pjax::end() ?>


<?php \yii\bootstrap\Modal::begin([
    'id' => 'call-box-modal',
    'header' => '<h4 class="modal-title">Missed Calls</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE
]); ?>
<?php \yii\bootstrap\Modal::end(); ?>


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

JS;

$this->registerJs($js, \yii\web\View::POS_READY);