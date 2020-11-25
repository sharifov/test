<?php

use sales\model\conference\form\DebugForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \sales\model\conference\form\DebugForm $model */
/** @var string $content */

$this->title = 'Conference debug';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-debug">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'conferenceSid')->textInput() ?>

        <?= $form->field($model, 'action')->dropDownList(DebugForm::ACTION_LIST, ['prompt' => 'Select action']) ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-12">
        <?php if ($content) : ?>
            <pre><?= $content ?></pre>
        <?php endif; ?>
    </div>

</div>
