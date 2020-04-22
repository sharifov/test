<?php

use common\models\search\LeadSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\log\Logger;
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php foreach(Yii::$app->session->getAllFlashes() as $type => $messages): ?>
    <?php foreach($messages as $message): ?>
        <div class="alert alert-success">
            <?= $message ?>
        </div>
    <?php endforeach ?>
<?php endforeach ?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'post',
    'options' => [
        'data-pjax' => 1,
        //'class' => 'form-inline'
    ]
]);
?>
<div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'level')->dropDownList([
            Logger::LEVEL_ERROR => Logger::getLevelName(Logger::LEVEL_ERROR),
            Logger::LEVEL_WARNING => Logger::getLevelName(Logger::LEVEL_WARNING),
            Logger::LEVEL_INFO => Logger::getLevelName(Logger::LEVEL_INFO),
            Logger::LEVEL_TRACE => Logger::getLevelName(Logger::LEVEL_TRACE),
            Logger::LEVEL_PROFILE_BEGIN => Logger::getLevelName(Logger::LEVEL_PROFILE_BEGIN),
            Logger::LEVEL_PROFILE_END => Logger::getLevelName(Logger::LEVEL_PROFILE_END),
        ], ['prompt' => '-']) ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'category')->dropDownList(\frontend\models\Log::getCategoryFilter(), ['prompt' => '-']) ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'days')->label("Remove after days") ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <br>
        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-close"></i> Delete logs', [
                'class' => 'btn btn-danger',
                'data' =>[
                    'confirm' =>'You want to remove logs by selected criteria?'
                ]
            ]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

