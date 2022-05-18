<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-search-processing">

    <?php

    $form = ActiveForm::begin([
        'id' => 'lead-search-follow-up-form',
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            //'class' => 'form-inline'
        ]
    ]);

    $statusList = \common\models\Lead::getProcessingStatuses();

    ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'email_status')->dropDownList([1 => 'WithOut email', 2 => 'With email'], ['prompt' => '-']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'quote_status')->dropDownList([1 => 'Not send quotes', 2 => 'Send quotes'], ['prompt' => '-']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?php $showAll = Yii::$app->request->cookies->getValue(\common\models\Lead::getCookiesKey(), true);
                $btnClass = $showAll ? 'btn-success' : 'btn-warning';
                $btnText = $showAll ? 'Show Unprocessed' : 'Show All';

                echo Html::a('<i class="fa fa-list"></i> ' . $btnText, ['lead/unprocessed', 'show' => !$showAll], [
                    'class' => 'btn ' . $btnClass,
                    'style' => 'margin-left: 10px;'
                ]);?>

                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fa fa-close"></i> Reset form', ['follow-up'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
