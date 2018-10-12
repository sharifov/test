<?php
/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $modelUserParams \common\models\UserParams
 * @var $isProfile boolean
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;
use common\models\EmployeeAcl;
use yii\widgets\MaskedInput;

$formId = sprintf('%s-ID', $model->formName());

if($model->isNewRecord) {
    $this->title = 'Create new User';
} else {
    $this->title ='Update user: ' . $model->username.' (ID:  '.$model->id.')';
}

$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $form = ActiveForm::begin([
    'successCssClass' => '',
    'id' => $formId
]) ?>
<div class="col-sm-6">


            <div class="well">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'username')->textInput() ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'password')->passwordInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'full_name')->textInput() ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'email')->input('email') ?>
                    </div>
                </div>
                <?php if (!$isProfile) : ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'role')->dropDownList(
                                $model::getAllRoles(), [
                                    'prompt' => '',
                                ]
                            ) ?>
                        </div>
                        <?php if (!$model->isNewRecord) : ?>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'deleted', ['template' => '{label}{input}'])->checkbox() ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)):

                            if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                                $data = \common\models\UserGroup::getList();
                            }

                            if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
                                $data = Yii::$app->user->identity->getUserGroupList();
                            }


                            ?>

                            <?php
                            echo $form->field($model, 'user_groups')->widget(\kartik\select2\Select2::class, [
                                'data' => $data,
                                'size' => \kartik\select2\Select2::SMALL,
                                'options' => ['placeholder' => 'Select user groups', 'multiple' => true],
                                'pluginOptions' => ['allowClear' => true],
                            ]);
                            ?>

                        <? else: ?>

                            <label class="control-label">User Groups</label>:
                            <?php
                                $groupsValue = '';
                                if( $groupsModel =  $model->ugsGroups) {
                                    $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');

                                    $groupsValueArr = [];
                                    foreach ($groups as $group) {
                                        $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                                    }
                                    $groupsValue = implode(' ', $groupsValueArr);
                                }
                                echo $groupsValue;
                            ?>

                        <? endif; ?>
                    </div>

                </div>

                <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $form->errorSummary($modelUserParams) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_base_amount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => '1000']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_commission_percent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php
            if (!$model->isNewRecord && !$isProfile) : ?>
                <div class="well form-inline">
                    <div class="form-group">
                        <?= $form->field($model, 'acl_rules_activated', [
                            'template' => '{input}'
                        ])->checkbox() ?>
                        <span>&nbsp;</span>
                        <?= Html::a('Add Extra Rule', null, [
                            'class' => 'btn btn-success',
                            'id' => 'acl-rule-id',
                        ]) ?>
                        <?php
                        $aclModel = new EmployeeAcl();
                        $aclModel->employee_id = $model->id;
                        $idForm = sprintf('%s-ID', $aclModel->formName());
                        $idMaskIP = Html::getInputId($aclModel, 'mask');

                        $js = <<<JS
    $('#acl-rule-id').click(function() {
        $(this).addClass('hidden');
        $('#$idForm').removeClass('hidden');
    });

    $('#close-btn').click(function() {
        $('#acl-rule-id').removeClass('hidden');
        $('#$idForm').addClass('hidden');
    });
    
    $('#submit-btn').click(function() {
        $.post($(this).data('url'), $('#$idForm input').serialize(),function( data ) {
            if (data.success) {
                $('#employee-acl-rule').html(data.body);
                $('#close-btn').trigger('click');
                $('#$idMaskIP').val('');
            } else {
                $('#$idMaskIP').parent().addClass('has-error');
            }
        });
    });
JS;
                        $this->registerJs($js);
                        ?>
                        <div class="form-group hidden" id="<?= $idForm ?>">
                            <?= $form->field($aclModel, 'employee_id', [
                                'options' => [
                                    'tag' => false
                                ],
                                'template' => '{input}'
                            ])->hiddenInput() ?>
                            <?= $form->field($aclModel, 'mask', [
                                'options' => [
                                    'class' => 'form-group'
                                ],
                                'template' => '{label}: {input}',
                                'enableClientValidation' => false
                            ])->widget(MaskedInput::class, [
                                'clientOptions' => [
                                    'alias' => 'ip'
                                ],
                            ]) ?>
                            <span>&nbsp;</span>
                            <?= $form->field($aclModel, 'description', [
                                'options' => [
                                    'class' => 'form-group'
                                ],
                                'template' => '{label}: {input}',
                                'enableClientValidation' => false
                            ])->textInput() ?>
                            <span>&nbsp;</span>
                            <?= Html::button('Save', [
                                'class' => 'btn-success btn',
                                'id' => 'submit-btn',
                                'data-url' => Yii::$app->urlManager->createUrl(['employee/acl-rule', 'id' => 0])
                            ]) ?>
                            <?= Html::button('Close', [
                                'class' => 'btn-danger btn',
                                'id' => 'close-btn'
                            ]) ?>
                        </div>
                    </div>
                    <div class="grid-view" id="employee-acl-rule" style="padding-top: 10px;">
                        <?= $this->render('partial/_aclList', [
                            'models' => $model->employeeAcl
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?= Html::submitButton(($model->isNewRecord ? 'Create User' : 'Update User'), ['class' => 'btn btn-primary']) ?>
            </div>

</div>

<div class="col-sm-6">
    <?php if (!$model->isNewRecord) : ?>
        <div class="panel panel-default">
            <div class="panel-heading collapsing-heading">
                <?= Html::a('Seller Contact Info <i class="collapsing-heading__arrow"></i>', '#seller-contact-info', [
                    'data-toggle' => 'collapse',
                    'class' => 'collapsing-heading__collapse-link'
                ]) ?>
            </div>
            <div class="panel-body panel-collapse collapse in" id="seller-contact-info">
                <?= $this->render('partial/_sellerContactInfo', [
                    'model' => $model
                ]) ?>
            </div>
        </div>
    <?php endif; ?>
    <?= $this->render('partial/_activities', [
        'model' => $model
    ]) ?>
    <?php
    if (!$model->isNewRecord && $model->role != 'admin') {
        echo $this->render('partial/_permissions', [
            'model' => $model,
            'isProfile' => $isProfile
        ]);
    }
    ?>
</div>

<?php ActiveForm::end() ?>
