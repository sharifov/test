<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginStepTwoForm */
/* @var $viewHelper TwoFactorViewHelperInterface */
/* @var $user \common\models\Employee */

use common\models\LoginStepTwoForm;
use src\useCase\login\twoFactorAuth\TwoFactorAuthFactory;
use src\useCase\login\twoFactorAuth\viewHelper\TwoFactorViewHelperInterface;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Login Step Two';
?>
<?php Pjax::begin(['id' => 'stepTwoPjax', 'enablePushState' => false, 'enableReplaceState' => false]) ?>
<div class="login_wrapper">
    <div class="animate form login_form">
        <section class="login_content">
            <?php $form = ActiveForm::begin(['id' => 'step-two-form']); ?>
                <h1><?= $this->title ?></h1>

                <?= $form->errorSummary($model) ?>

                <?= $form->field($model, 'twoFactorMethod')->dropDownList(TwoFactorAuthFactory::getValidatedList($user), [
                    'id' => 'twoFactorMethod',
                ]) ?>

                <?= $viewHelper->renderView($form, $user) ?>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                </div>

                <div class="clearfix"></div>
                <div class="separator">
                    <div class="clearfix"></div>
                    <br />
                    <div>
                        <h1><i class="fa fa-dollar"></i>ales</h1>
                        <p>©2017-<?=date('Y')?> All Rights Reserved.</p>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </section>
    </div>
</div>

<?php

$js = <<<JS
$('#twoFactorMethod').on('change', function (e) {
    e.preventDefault();
    $.pjax.reload({
        container: '#stepTwoPjax',
        type: 'post',
        data: $('#step-two-form').serialize(),
        timeout: 10000,
        push: false, 
        replace: false
    });
});
JS;
$this->registerJs($js);

Pjax::end();
