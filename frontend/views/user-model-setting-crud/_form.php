<?php

use common\models\Employee;
use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use src\model\userModelSetting\entity\UserModelSetting;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\userModelSetting\entity\UserModelSetting */
/* @var $form ActiveForm */
?>

<div class="user-model-setting-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ums_user_id')->dropDownList(Employee::getList(), ['prompt' => '---'])?>

        <?= $form->field($model, 'ums_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'ums_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ums_type')->dropDownList(UserModelSetting::TYPE_LIST) ?>

        <?= $form->field($model, 'ums_class')->textInput(['maxlength' => true]) ?>

        <?php
        if ($model->isNewRecord) {
            $settings['fields'] = [];
            $model->ums_settings_json = $settings;
        }
        $model->ums_settings_json = JsonHelper::encode($model->ums_settings_json);
        try {
            echo $form->field($model, 'ums_settings_json')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form'],
                        'mode' => 'code',
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            try {
                echo $form->field($model, 'ums_settings_json')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserModelSettingCrudController:SettingsJson:notValidJson');
            }
        }
        ?>

        <?php
        $model->ums_sort_order_json = JsonHelper::encode($model->ums_sort_order_json);
        try {
            echo $form->field($model, 'ums_sort_order_json')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form'],
                        'mode' => 'code',
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            try {
                echo $form->field($model, 'ums_sort_order_json')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserModelSettingCrudController:SortOrderJson:notValidJson');
            }
        }
        ?>

        <?= $form->field($model, 'ums_per_page')->textInput() ?>

        <?= $form->field($model, 'ums_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
