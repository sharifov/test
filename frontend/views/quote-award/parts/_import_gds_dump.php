<?php

use src\services\parsingDump\lib\ParsingDump;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \modules\quoteAward\src\forms\ImportGdsDumpForm $model */

$form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['quote-award/import-gds-dump']),
    'id' => 'award-import-gds'
]) ?>


<?= $form->field($model, 'reservationDump')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'gds')->dropDownList(ParsingDump::QUOTE_GDS_TYPE_MAP, ['prompt' => '---']) ?>

<?= $form->field($model, 'tripId', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput()->label(false) ?>

<div class="d-flex justify-content-center">
    <?= Html::submitButton('<i class="fa fa-recycle" aria-hidden="true"></i> Import', [
        'id' => 'btn-award-import-gds',
        'class' => 'btn btn-success',
        'data-inner' => '<i class="fa fa-recycle" aria-hidden="true"></i> Import',
        'data-class' => 'btn btn-success'
    ]) ?>
</div>


<?php ActiveForm::end() ?>
