<?php

use common\models\Department;
use common\models\Employee;
use common\models\Language;
use common\models\Project;
use kartik\select2\Select2;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatQaSearch;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var ClientChatQaSearch $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="client-chat-search">

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>

        <?php $display = (Yii::$app->request->isPjax || Yii::$app->request->get('ClientChatQaSearch'))
            ? 'block' : 'none'; ?>
        <div class="x_content" style="display: <?php echo $display ?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'id' => 'qa_search_form',
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_id') ?>

                    <?php echo $form->field($model, 'cch_channel_id')->dropDownList(ClientChatChannel::getListByUserId(Auth::id()), ['prompt' => '-']) ?>

                    <?= $form->field($model, 'createdRangeDate', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => false,
                            'locale' => [
                                'format' => 'd-M-Y',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created From / To') ?>

                </div>
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_rid') ?>
                    <?php echo $form->field($model, 'cch_client_id') ?>
                </div>
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_ccr_id') ?>

                    <?php echo $form->field($model, 'cch_owner_user_id')->widget(Select2::class, [
                            'data' => Employee::getActiveUsersListFromCommonGroups(Auth::id()),
                            'size' => Select2::SMALL,
                            'options' => ['multiple' => false],
                            'pluginOptions' => ['allowClear' => true, 'placeholder' => '', 'id' => 'user_id'],
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_status_id')->dropDownList(ClientChat::getStatusList(), ['prompt' => '-']) ?>
                    <?php echo $form->field($model, 'caseId') ?>
                </div>
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_project_id')->dropDownList(EmployeeProjectAccess::getProjects(), ['prompt' => '-']) ?>
                    <?php echo $form->field($model, 'leadId') ?>

                </div>
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_dep_id')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-']) ?>
                    <?php echo $form->field($model, 'cch_language_id')->dropDownList(Language::getLanguages(), ['prompt' => '-']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $form->field($model, 'messageText') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary search_qa_btn']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    $(document).on('beforeSubmit', '#qa_search_form', function(event) {
        let btn = $(this).find('.search_qa_btn');
        
        btn.html('<i class="fa fa-cog fa-spin"></i>  Loading')            
            .prop("disabled", true);
    });
JS;
$this->registerJs($js, View::POS_READY);