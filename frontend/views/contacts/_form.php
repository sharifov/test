<?php

use common\models\Client;
use common\models\Project;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var common\models\Client $model */
/* @var yii\widgets\ActiveForm $form */

$this->title = 'Create contact';
$this->params['breadcrumbs'][] = $this->title;

$projectList = Project::getList();
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'is_company')->checkbox(['class' => 'is_company']) ?>
    <div class="user_elements">
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
        <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
    </div>
    
    <div class="company_elements" style="display: none;">
        <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
    </div>
    
    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'style' => 'width: 320px', 'class' => 'form-control']) ?>
    <?= $form->field($model, 'is_public')->checkbox() ?>
    <?= $form->field($model, 'disabled')->checkbox() ?>

    <!--<div class="col-md-12">
        <label class="control-label">Projects:</label>:
        <?php
/*            $projectsValueArr = [];
            if($projects = $model->projects) {
                foreach ($projects as $project) {
                    $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-info']);
                }
            }
            $projectsValue = implode(' ', $projectsValueArr);
            echo $projectsValue;
        */?>
    </div>-->

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js =<<<JS

    $(document).on('click', '.is_company', function(e) {                
        let isCompany = $(this).prop("checked") ? 1 : 0;
        
        if (isCompany === 1) {
            $('.user_elements').hide();
            $('.company_elements').show();
        } else {
            $('.user_elements').show();
            $('.company_elements').hide();
        }
    });    
JS;
$this->registerJs($js);
?>
