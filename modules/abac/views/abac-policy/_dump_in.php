<?php

use modules\abac\src\forms\AbacPolicyImportDumpForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model AbacPolicyImportDumpForm */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="abac-policy-import-dump">
    <?php Pjax::begin(['id' => 'pjax_abac-policy-import-dump', 'enableReplaceState' => false, 'enablePushState' => false]) ?>
        <?php $form = ActiveForm::begin([
            'action' => ['/abac/abac-policy/dump-in'],
            'method' => 'post',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <?= $form->errorSummary($model) ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'dump')->textarea(['rows' => 12]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'enabled')->checkbox() ?>
            </div>
            <div class="col-md-12 text-center">
                <div class="form-group">
                    <?= Html::submitButton(
                        '<i class="fa fa-download"></i> Import Policy',
                        ['class' => 'btn btn-success', 'id' => 'btn-submit']
                    ) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>

<?php
//$js = <<<JS
//$(document).on('click', '.btn-coupon-cancel-preview', function (e) {
//    pjaxReload({container: '#pjax-case-coupons-table'});
//});
//JS;
// $this->registerJs($js);