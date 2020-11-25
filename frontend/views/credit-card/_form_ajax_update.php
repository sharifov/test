<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CreditCard */
/* @var $form yii\widgets\ActiveForm */
/* @var $isAjax bool */

\frontend\assets\CreditCardAsset::register($this);

$isAjax = isset($isAjax);
$colMd = $isAjax ? 'col-md-12' : 'col-md-4';
$pjaxId = 'pjax-create-credit-card'
?>

    <script>pjaxOffFormSubmit('#<?= $pjaxId ?>');</script>
    <div class="credit-card-form">

        <div class="<?= $colMd ?>">

            <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]) ?>


            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'credit-card-create-form']); ?>

            <?php echo $form->errorSummary($model); ?>


            <div class="clearfix"></div>

            <div class="row">

            <div class="col-md-12">
                <?= $form->field($model, 'cc_holder_name')->textInput(['id' => 'cc_holder_name', 'maxlength' => true]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'cc_type_id')->dropDownList(\common\models\CreditCard::getTypeList(), ['prompt' => '---']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>
<?php