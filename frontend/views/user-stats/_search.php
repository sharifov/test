<?php

use common\models\Employee;
use kartik\select2\Select2;
use src\model\user\entity\userStats\UserStatsSearch;
use src\model\userModelSetting\service\UserModelSettingDictionary;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var UserStatsSearch $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="user-stats-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
        'id' => 'userStatsForm',
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'dateTimeType')->dropDownList(UserModelSettingDictionary::DT_TYPE_LIST) ?>
        </div>

        <div class="col-md-10">
            <label class="control-label"><?php echo $model->getAttributeLabel('id') ?></label>
            <?php echo
            $form->field($model, 'id')->widget(Select2::class, [
                'data' => Employee::getListByUserId($model->getCurrentUser()->getId()),
                'size' => Select2::SIZE_SMALL,
                'pluginOptions' => [
                    'closeOnSelect' => false,
                    'allowClear' => true,
                ],
                'options' => [
                    'placeholder' => 'Choose users...',
                    'multiple' => true,
                    'id' => 'selectUsers',
                ],
            ])->label(false) ?>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12">
            <?php echo
            $form->field($model, 'fields')->widget(Select2::class, [
                'data' => UserModelSettingDictionary::FIELD_LIST,
                'size' => Select2::SIZE_SMALL,
                'pluginOptions' => [
                    'closeOnSelect' => false,
                    'allowClear' => true,
                ],
                'options' => [
                    'placeholder' => 'Choose additional fields...',
                    'multiple' => true,
                    'id' => 'selectFields',
                ],
            ])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary js-user-stats-btn']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
