<?php

use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\forms\RentCarUpdateRequestForm;
use modules\rentCar\src\helpers\RentCarHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $modelForm RentCarUpdateRequestForm */
/** @var RentCar $rentCar */

$pjaxId = 'pjax-rent-car-update';

$pluginOptions = [
    'width' => '100%',
    'allowClear' => true,
    'minimumInputLength' => 1,
    'language' => [
        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
    ],
    'ajax' => [
        'url' => ['/airport/get-list'],
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {term:params.term}; }'),
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('formatRepo'),
    'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
];
?>
<div class="rent-car-update-ajax">
    <div class="rent-car-form">
        <script>
            pjaxOffFormSubmit('#<?php echo $pjaxId?>');
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/rent-car/rent-car/update-ajax', 'id' => $rentCar->prc_id],
            'method' => 'post',
            'enableClientValidation' => false
        ]);
        ?>

            <?php echo $form->errorSummary($modelForm) ?>

            <?= $form->field($modelForm, 'pick_up_code')->widget(Select2::class, [
                'options' => [
                    'placeholder' => $modelForm->getAttributeLabel('pick_up_code')
                ],
                'pluginOptions' => $pluginOptions,
            ]) ?>

            <?= $form->field($modelForm, 'drop_off_code')->widget(Select2::class, [
                'options' => [
                    'placeholder' => $modelForm->getAttributeLabel('drop_off_code')
                ],
                'pluginOptions' => $pluginOptions,
            ]) ?>

            <?php echo $form->field($modelForm, 'pick_up_date')->widget(
                DatePicker::class,
                [
                    'inline' => false,
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]
            )?>

            <?php echo $form->field($modelForm, 'drop_off_date')->widget(
                DatePicker::class,
                [
                    'inline' => false,
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]
            )?>

            <?= $form->field($modelForm, 'pick_up_time')->dropdownList(RentCarHelper::listTime()) ?>

            <?= $form->field($modelForm, 'drop_off_time')->dropdownList(RentCarHelper::listTime()) ?>

            <div class="form-group text-center">
                <?php echo Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
function formatRepo( repo ) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.text + "</div>";
    markup +=	"</div></div>";

    return markup;
}
JS;
$this->registerJs($js, View::POS_HEAD);
