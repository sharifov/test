<?php

use common\models\Project;
use frontend\assets\FormBuilderAsset;
use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use yii\bootstrap4\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var src\model\clientChatForm\entity\ClientChatForm $model */
/* @var ActiveForm $form */

FormBuilderAsset::register($this);
?>

<div class="client-chat-builder-form">

    <div class="col-md-6">

        <?php $form = ActiveForm::begin([
                'id' => 'chatForm',
                'options' => ['data-pjax' => true],
            ]); ?>

        <?php echo $form->errorSummary($model) ?>

        <?php echo $form->field($model, 'ccf_key')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'ccf_name')->textInput(['maxlength' => true]) ?>
        <?php echo $form->field($model, 'ccf_project_id')->dropDownList(Project::getList()) ?>
        <?php echo $form->field($model, 'ccf_enabled')->checkbox() ?>

        <?php echo $form->field($model, 'ccf_dataform_json')
            ->label(false)
            ->textarea(
                [
                    'id' => 'dataFormJson',
                    'class' => 'form-control',
                    'style' => 'display: none;',
                    'value' => ''
                ]
            ) ?>

        <?php ActiveForm::end(); ?>

        <div id="fb-editor"></div>

    </div>
</div>


<?php $preparedData = $model->ccf_dataform_json ? JsonHelper::encode($model->ccf_dataform_json) : '[]'; ?>

<?php
$js = <<<JS

    let preparedData = {$preparedData};
    
    let options = {
        formData: JSON.stringify(preparedData),
        typeUserAttrs : {
            text: {                
                pattern: {
                  label: 'pattern',
                  value: '',
                  placeholder: '[A-Za-z]',
                  type: 'text'
                }
            },
            hidden: {                
                pattern: {
                  label: 'pattern',
                  value: '',
                  placeholder: '[A-Za-z]',
                  type: 'text'
                }
            },
            textarea: {                
                pattern: {
                  label: 'pattern',
                  value: '',
                  placeholder: '[A-Za-z]',
                  type: 'text'
                }
            }
        },
        disabledAttrs: [
            'access',
            'description',
            'other',
            'inline'
        ],
        disableFields: [
            'autocomplete',
            'file',
            'header',
            'paragraph',
            'starRating',
            'number'
        ],
        disabledSubtypes: {
            textarea: ['tinymce', 'quill'],
            text: ['color']
        },
        i18n: {
            locale: 'en-US',
            location: '/js/form-builder/lang/',
            extension: '.lang'        
        },
        onSave: function(evt, formData) {
            let dataFormJsonEl = $('#dataFormJson');                        
            dataFormJsonEl.val('');
            
            if (formData.length) { 
                dataFormJsonEl.val(formData);
            }
            $('#chatForm').submit(); 
        }
    };
    
    let formBuilderObj = $('#fb-editor').formBuilder(options);    
    
JS;

$this->registerJs($js, View::POS_LOAD);


