<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var string $url
 * @var string $case_id
 */
?>
<div id="event-grid"> <!-- loaded grid --> </div>

<?php
$gridFilterJs = <<<JS

$.ajax({
        url: '$url',
        type: 'get',
        dataType: 'html',
        data: {'case_id': '$case_id'},
        beforeSend: function () {
          //$('#some-id').html(spinner).prop('disabled', true).toggleClass('disabled');
        },
        success: function (data) {            
            if (!data.error) {
                $('#event-grid').html(data);
            } 
        },
        error: function (error) {            
            createNotifyByObject({
                title: 'Error',
                text: 'Event Log: Internal Server Error',
                type: 'error'                
            });
            //$('#some-id').html('Internal Server Error. Try again letter.');
        },
        complete: function () {
            //$('#some-id-btn').html(submitBtnHtml).removeAttr('disabled').toggleClass('disabled');
        }
    });

JS;
$this->registerJs($gridFilterJs, \yii\web\View::POS_READY);
?>



