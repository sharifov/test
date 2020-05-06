<?php
use yii\helpers\Html;
?>


<div class="btn-group" id="btn-group-id-mute" >
                            <?=Html::button('Mute', ['id' => 'btn-mute-microphone', 'class' => 'btn btn-sm btn-warning'])?>
                        </div>



<?php
$js = <<<JS
    $(document).on('click', '#btn-mute-microphone', function(e) {
        let mute = $(this).html();
        if (mute === 'Mute') {
            $(this).html('Unmute').removeClass('btn-warning').addClass('btn-success');
        } else {
            $(this).html('Mute').removeClass('btn-success').addClass('btn-warning');
        }
    });

JS;
$this->registerJs($js);
