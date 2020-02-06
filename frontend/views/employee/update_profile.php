<?php
/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $modelUserParams \common\models\UserParams
 * @var $qrcodeData string
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;


$this->title = 'My profile - ' . $model->username;

$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=Html::encode($this->title)?></h1>
<div class="col-sm-6">
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
                        <div class="col-md-12">
                            <label class="control-label">My Product Types</label>:
                            <?php
                                    if ($productTypeList = Yii::$app->user->identity->productType) {
                                        $productTypeValue = '';
                                        foreach ($productTypeList as $productType) {
                                            $productTypeValue .= Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' .
                                                Html::encode($productType->pt_name), ['class' => 'label label-default']) . ' ';
                                        }
                                        echo $productTypeValue;
                                    }
                                ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php /*<div class="col-md-3">
                    <?= $form->field($modelUserParams, 'up_work_start_tm')->widget(
                        \kartik\time\TimePicker::class, [
                        'pluginOptions' => [
                            'showSeconds' => false,
                            'showMeridian' => false,
                        ]])?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelUserParams, 'up_work_minutes')->input('number', ['step' => 10, 'min' => 0])?>
                </div>*/ ?>
                <div class="col-md-6">
                    <?//= $form->field($modelUserParams, 'up_timezone')->dropDownList(Employee::timezoneList(),['prompt' =>'-'])?>

                    <?php
                    echo $form->field($modelUserParams, 'up_timezone')->widget(\kartik\select2\Select2::class, [
                        'data' => Employee::timezoneList(true),
                        'size' => \kartik\select2\Select2::SMALL,
                        'options' => ['placeholder' => 'Select TimeZone', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]);
                    ?>

                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save Profile', ['class' => 'btn btn-primary']) ?>
            </div>
    <?php ActiveForm::end() ?>





</div>

<div class="col-sm-4">

    <?= \yii\widgets\DetailView::widget([
        'model' => $modelUserParams,
        'attributes' => [
            [
                'attribute' => 'up_base_amount',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
            ],
            [
                'attribute' => 'up_commission_percent',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
                },

            ],
            'up_bonus_active:boolean',
            'up_timezone',
            'up_work_start_tm',
            'up_work_minutes',
            //'up_inbox_show_limit_leads',
            'up_default_take_limit_leads',
            'up_min_percent_for_take_leads',
            /*[
                'attribute' => 'up_updated_dt',
                'value' => function(\common\models\UserParams $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                },
                'format' => 'raw',
            ],*/
            //'upUpdatedUser.username'
        ],
    ]) ?>

    <?= \yii\widgets\DetailView::widget([
        'model' => $model->userProfile,
        'attributes' => [
            /*[
                'attribute' => 'up_base_amount',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
            ],*/
            'up_telegram',
            'up_telegram_enable:boolean',

           /* @property int $up_user_id
 * @property int $up_call_type_id
 * @property string $up_sip
 * @property string $up_telegram
 * @property int $up_telegram_enable
 * @property string $up_updated_dt
 * @property boolean $up_auto_redial
 * @property boolean $up_kpi_enable
 * @property int $up_skill*/

        ],
    ]) ?>

</div>

<div class="col-sm-2">
    <h3>Telegram Auth</h3>
    <?php echo '<img src="' . $qrcodeData . '">'; ?>
</div>
