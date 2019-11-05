<?php

use yii\web\View;

/** @var string $message */
/** @var  View $this */

$js =<<<JS
$("#redial-alert").fadeTo(2000, 500).slideUp(500, function(){
    $("#redial-alert").slideUp(500);
});
JS;

$this->registerJs($js);

?>

<div id="redial-call-box">
    <div class="alert alert-error" id="redial-alert">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong><?= $message ?></strong>
    </div>
</div>

