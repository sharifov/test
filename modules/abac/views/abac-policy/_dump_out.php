<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */
?>
<div class="abac-policy-view">
    <h2>Policy Id: <?= Html::encode($model->ap_id) ?> - <?= Html::encode($model->ap_object) ?> - <?= Html::encode($model->ap_action) ?></h2>
    <?php echo Html::textarea('dump', $model->getDump(), ['id' => 'input-dump', 'rows' => 11, 'style' => 'width: 100%',
        'readonly' => 'readonly',
        ]) ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            HashCode: <?= Html::encode($model->ap_hash_code) ?>
        </div>
        <div class="col-md-12 text-center">
            <?= Html::button(
                '<i class="fa fa-copy"></i> Copy to Clipboard',
                ['class' => 'btn btn-success',
                    'id' => 'btn-copy-clipboard',
                    'data-toggle' => 'tooltip',
                    'title' => 'Copy to Clipboard'
                ]
            ) ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).on('click', '#btn-copy-clipboard', function (e) {
    copyToClipboard();
});

function copyToClipboard() {
    var copyText = document.getElementById("input-dump");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    $('#btn-copy-clipboard').html('<i class="fa fa-check"></i> Copied OK');
    //alert(copyText.value);
    //var tooltip = document.getElementById("myTooltip");
    //tooltip.innerHTML = "Copied: " + copyText.value;
}
JS;
$this->registerJs($js);