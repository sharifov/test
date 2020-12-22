<?php

use common\models\Language;
use common\models\Project;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectLocale\ProjectLocale */
/* @var $copyModel sales\model\project\entity\projectLocale\ProjectLocale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-locale-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?php //= $form->field($model, 'pl_project_id')->dropDownList(Project::getList(), ['prompt' => '-'])?>

            <?= $form->field($model, 'pl_project_id')->widget(Select2::class, [
                'data' => Project::getList(),
                'size' => Select2::SMALL,
                'options' => [
                    'placeholder' => 'Select project',
                    'multiple' => false,
                ],
                'pluginOptions' => ['allowClear' => true],
            ]);
?>

            <div class="row">
                <div class="col-md-6">

                <?= $form->field($model, 'pl_language_id')->widget(Select2::class, [
                    'data' => Language::getLocaleList(false),
                    'size' => Select2::SMALL,
                    'options' => [
                        'placeholder' => 'Select locale',
                        'multiple' => false,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])

?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'pl_market_country')->widget(Select2::class, [
                        'data' => Language::getCountryNames(),
                        'size' => Select2::SMALL,
                        'options' => [
                            'placeholder' => 'Select country',
                            'multiple' => false,
                        ],
                        'pluginOptions' => ['allowClear' => true],
                    ])

?>
                </div>
            </div>

            <?= $form->field($model, 'pl_default')->checkbox() ?>

            <?= $form->field($model, 'pl_enabled')->checkbox() ?>
        </div>

        <div class="col-md-8">
            <?php

            try {
                echo $form->field($model, 'pl_params')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree'], //'text', , 'view'
                            'mode' => ($model->isNewRecord && !$copyModel) ? 'code' : 'form'
                        ],
                        'collapseAll' => ['form'],
                        'expandAll' => ['tree', 'form'],
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'pl_params')->textarea(['rows' => 30]);
            }

            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Project Locale', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
