<?php
/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $modelUserParams \common\models\UserParams
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;


$this->title = 'Profile - ' . $model->username;

//$this->params['breadcrumbs'][] = ['label' => 'User List', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-sm-5">
    <?php $form = ActiveForm::begin() ?>
            <div class="well">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'username')->textInput(['autocomplete' => "new-user", "readonly" => "readonly"]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => "new-password"]) ?>
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

                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-md-12">
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
                        </div>

                        <div class="col-md-12">
                            <label class="control-label">Projects access</label>:
                            <?php
                                $projectsValueArr = [];

                                if($projects = $model->projects) {
                                    foreach ($projects as $project) {
                                        $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-info']);
                                    }
                                }

                                $projectsValue = implode(' ', $projectsValueArr);
                                echo $projectsValue;
                            ?>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_base_amount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => '1000']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_commission_percent')->input('number', ['step' => 1, 'max' => 100, 'min' => 0]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelUserParams, 'up_bonus_active')->checkbox() ?>
                    </div>
                </div>


            </div>


            <div class="form-group">
                <?= Html::submitButton('Update Profile', ['class' => 'btn btn-primary']) ?>
            </div>
    <?php ActiveForm::end() ?>
</div>