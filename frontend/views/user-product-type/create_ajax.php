<?php

use modules\product\src\entities\productType\ProductType;
use sales\access\EmployeeProjectAccess;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
    pjaxOffFormSubmit('#create-user-product-type-pjax');
</script>

<?php \yii\widgets\Pjax::begin(['id' => 'create-user-product-type-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => ['user-product-type/create-ajax', 'user_id' => $model->upt_user_id],
    'method' => 'post',
]) ?>

<div class="col-md-12">

    <div style="display: none;">
        <?= $form->field($model, 'upt_user_id')->hiddenInput()->label(false)?>
    </div>

    <div class="form-group">
        <label class="control-label">Username</label>
        <?=Html::input('text', 'username', $model->user->username, ['class' => 'form-control', 'readonly' => true, 'disabled' => true]);?>
    </div>

    <?= $form->field($model, 'upt_product_type_id')->dropDownList(ProductType::getList(), ['prompt' => '-']) ?>
    <?= $form->field($model, 'upt_commission_percent')->input('number', ['step' => 0.01]) ?>
    <?= $form->field($model, 'upt_product_enabled')->checkbox()?>

    <div class="form-group text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php \yii\widgets\Pjax::end(); ?>
