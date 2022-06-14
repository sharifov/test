<?php

use frontend\helpers\PasswordHelper;
use kartik\password\PasswordInput;
use src\helpers\setting\SettingHelper;
use yii\grid\ActionColumn;
/**
 * @var $this View
 * @var $model Employee
 * @var $modelUserParams UserParams
 * @var $qrcodeData string
 * @var $userCommissionRuleValue int
 * @var $userBonusRuleValue int
 * @var $userProfileForm userProfileForm
 * @var $sourcesDataProvider \yii\data\ActiveDataProvider
 */

use common\models\UserParams;
use frontend\models\form\UserProfileForm;
use frontend\themes\gentelella_v2\widgets\FlashAlert;
use src\model\userAuthClient\entity\UserAuthClient;
use src\model\userAuthClient\entity\UserAuthClientSources;
use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Employee;
use yii\web\View;

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
                        <?= $form->field($userProfileForm, 'username')->textInput(['autocomplete' => "new-user", "readonly" => "readonly"]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($userProfileForm, 'password', [
                            'options' => [
                                'class' => 'form-group'
                            ]
                        ])->widget(PasswordInput::class, [
                            'options' => [
                                'autocomplete' => 'new-password',
                            ],
                        ])->label(
                            PasswordHelper::getLabelWithTooltip($model, 'password')
                        ); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($userProfileForm, 'full_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($userProfileForm, 'email')->input('email') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-md-12">
                            <label class="control-label">User Groups</label>:
                            <?php
                                $groupsValue = '';
                            if ($groupsModel =  $model->ugsGroups) {
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

                            if ($projects = $model->projects) {
                                foreach ($projects as $project) {
                                    $projectsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' . Html::encode($project->name), ['class' => 'label label-info']);
                                }
                            }

                                $projectsValue = implode(' ', $projectsValueArr);
                                echo $projectsValue;
                            ?>
                        </div>
                        <?php if ($productTypeList = Yii::$app->user->identity->productType) :?>
                            <div class="col-md-12">
                                <label class="control-label">My Product Types</label>:
                                <?php
                                    $productTypeValue = '';
                                foreach ($productTypeList as $productType) {
                                    $productTypeValue .= Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-list']) . ' ' .
                                        Html::encode($productType->pt_name), ['class' => 'label label-default']) . ' ';
                                }
                                    echo $productTypeValue;
                                ?>
                            </div>
                        <?php endif ?>
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
                    <?php //= $form->field($modelUserParams, 'up_timezone')->dropDownList(Employee::timezoneList(),['prompt' =>'-'])?>

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

<?php if (SettingHelper::isEnabledAuthClients()) : ?>
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h6><b>Auth clients:</b></h6>
            <div class="d-flex justify-content-between align-items-center align-content-center">
                        <span style="margin-right: 10px;">
<!--                            <i class="fa fa-plus-circle"></i> Assign auth client:-->
                        </span>
                <?php $authChoice = AuthChoice::begin([
                    'baseAuthUrl' => ['/site/auth-assign'],
                    'popupMode' => true,
                    'id' => 'auth-choice',
                    'clientOptions' => [
                        'popup' => [
                            'width' => 450,
                            'height' => 750,
                        ],
                    ],
                ]) ?>
                <div class="d-flex" style>
                    <?php foreach ($authChoice->getClients() as $client) : ?>
                        <?= $authChoice->clientLink(
                            $client,
                            '<button type="button" class="login-with-btn login-with-' . $client->getName() . '-btn">Assign ' . $client->getTitle() . '</button>',
                            [
                                'style' => 'margin-left: 5px'
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </div>
                <?php AuthChoice::end() ?>
            </div>
        </div>
        <div>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $sourcesDataProvider,
                'columns' => [
                    'uac_id',
                    [
                        'attribute' => 'uac_source',
                        'value' => static function (UserAuthClient $model) {
                            return \yii\helpers\Html::encode(UserAuthClientSources::getName($model->uac_source));
                        }
                    ],
                    'uac_email',
                    [
                        'attribute' => 'uac_created_dt',
                        'format' => 'byUserDateTime',
                        'label' => 'When assigned'
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{detach}',
                        'buttons' => [
                            'detach' => static function ($url, UserAuthClient $model) {
                                return Html::a('<i class="fas fa-unlink"></i> Detach', '#', [
                                    'class' => 'detach-btn',
                                    'data-auth-client-id' => $model->uac_id
                                ]);
                            }
                        ]
                    ],
                ],
                'layout' => "{items}",
            ]) ?>
        </div>
    </div>
</div>
    <?php
    $detachUrl = \yii\helpers\Url::to('/user-auth-client/detach');
    $js = <<<JS
$(document).on('click', '.detach-btn', function (e) {
    e.preventDefault();
    let btn = $(this);
    let btnHtml = btn.html();
    let authClientId = btn.attr('data-auth-client-id');
    
    $.ajax({
        url: '$detachUrl',
        type: 'post',
        data: {authClientId: authClientId},
        cache: false,
        beforeSend: function () {
            btn.html('<i class="fa fa-spinner fa-spin"></i>').addClass('disabled').attr('disabled', true);
        },
        success: function (data) {
            if (data.error) {
                createNotify('Error', data.message, 'error');
            } else {
                btn.closest('tr').remove();
                createNotify('Success', data.message, 'success');
            }
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
            btn.html(btnHtml).removeClass('disabled').attr('disabled', false);
        }
    })
});
JS;
    $this->registerJs($js);
    ?>
<?php endif; ?>


</div>

<div class="col-sm-4">

    <?= \yii\widgets\DetailView::widget([
        'model' => $modelUserParams,
        'attributes' => [
            [
                'label' => 'Start of work in the company',
                'value' => static function (UserParams $model) {
                    return $model->upUser->userProfile->up_join_date;
                }
            ],
            [
                'label' => 'Experience',
                'value' => static function (UserParams $model) {
                    return $model->upUser->userProfile->getExperienceMonth() . ' Months';
                }
            ],
            [
                'attribute' => 'up_base_amount',
                'value' => function (UserParams $model) {
                    return $model->up_base_amount ? '$' . number_format($model->up_base_amount, 2) : '-';
                },
            ],
            [
                'attribute' => 'up_commission_percent',
                'value' => function (UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent . '%' : '-';
                },

            ],
            [
                'label' => 'New Commission Percent',
                'value' => $userCommissionRuleValue . ' %'
            ],
            [
                'label' => 'New Bonus Value',
                'value' => '$' . $userBonusRuleValue
            ],
            'up_bonus_active:boolean',
            'up_timezone',
            'up_work_start_tm',
            'up_work_minutes',
            //'up_inbox_show_limit_leads',
            'up_default_take_limit_leads',
            'up_min_percent_for_take_leads'
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
 * @property string $up_telegram
 * @property int $up_telegram_enable
 * @property string $up_updated_dt
 * @property boolean $up_auto_redial
 * @property boolean $up_kpi_enable
 * @property int $up_skill*/

        ],
    ]) ?>

</div>

<?php if ($qrcodeData) : ?>
    <div class="col-sm-2">
        <h3>Telegram Auth</h3>
        <?php echo '<img src="' . $qrcodeData . '" title="Scan">'; ?>
    </div>
<?php endif; ?>
