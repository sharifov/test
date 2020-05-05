<?php

use common\models\Client;
use common\models\ClientEmail;
use common\models\Project;
use common\models\UserContactList;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use sales\forms\lead\EmailCreateForm;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var common\models\Client $model */
/* @var yii\widgets\ActiveForm $form */
/* @var ClientEmail $clientEmail */
/* @var EmailCreateForm $emailCreateForm */

$this->title = 'Create contact';

$projectList = EmployeeProjectAccess::getProjects(Auth::id());
?>

<div class="col-md-12 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <div class="x_panel">
        <div class="client-form col-md-4">


            <?= $form->field($model, 'is_company')->checkbox(['class' => 'is_company']) ?>
            <div class="user_elements" <?php echo $model->is_company === 1 ? 'style="display: none;"' : '' ?> >
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
            </div>

            <div class="company_elements" <?php echo $model->is_company !== 1 ? 'style="display: none;"' : '' ?> >
                <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
            </div>

            <?= $form->field($model, 'description')->textarea(['rows' => 4, 'style' => 'width: 320px', 'class' => 'form-control']) ?>
            <?= $form->field($model, 'is_public')->checkbox() ?>
            <?= $form->field($model, 'disabled')->checkbox() ?>

            <?php
                $favorite = false;
                if ($model->id !== null && $contact = UserContactList::getUserContact(Auth::id(), $model->id)) {
                    $favorite = $contact->ucl_favorite;
                }
            ?>

            <?php echo Html::checkbox('ucl_favorite', 0,
                ['id' => 'ucl_favorite', ]) ?> Favorite

            <!--<div style="width: 320px;">
                <?php
                /*  echo $form->field($model, 'projects')->widget(\kartik\select2\Select2::class, [
                        'data' => $projectList,
                        'size' => \kartik\select2\Select2::SMALL,
                        'options' => ['placeholder' => 'Select projects', 'multiple' => true,],
                        'pluginOptions' => ['allowClear' => true, ],
                    ]);
                */?>
            </div>-->
        </div>

        <div class="client-form col-md-3">

        </div>
    </div>


        <div class="form-group" style="margin-top: 12px;">
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
