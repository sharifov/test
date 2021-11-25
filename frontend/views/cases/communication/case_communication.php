<?php

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm CaseCommunicationForm
 * @var $model Cases
 * @var $previewEmailForm CasePreviewEmailForm
 * @var $previewSmsForm CasePreviewSmsForm
 * @var $isAdmin bool
 * @var $isCommunicationLogEnabled bool
 * @var $fromPhoneNumbers array
 * @var bool $smsEnabled
 * @var array $unsubscribedEmails
 * @var $disableMasking bool
 */

use common\models\Call;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageEmailSendListWidget;
use sales\entities\cases\Cases;
use sales\helpers\communication\StatisticsHelper;
use sales\helpers\projectLocale\ProjectLocaleHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\helpers\VarDumper;

$c_type_id = $comForm->c_type_id;

// <script src="https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.9.0/timer.jquery.min.js"></script>

$pjaxContainerId = $isCommunicationLogEnabled ? 'pjax-case-communication-log' : 'pjax-case-communication';
$pjaxContainerIdForm = $isCommunicationLogEnabled ? 'pjax-case-communication-log-form' : 'pjax-case-communication-form';
$listItemView = $isCommunicationLogEnabled ? '_list_item_log' : '/lead/communication/_list_item';
$unsubscribedEmails =  @json_encode($unsubscribedEmails);
$emailTemplateTypes = \common\models\EmailTemplateType::getEmailTemplateTypesList(false, $model->cs_dep_id);
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-comments"></i> Communication</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <div class="panel">
            <div class="chat__list">

                <div class="communication-block-scroll">
                    <?php yii\widgets\Pjax::begin(['id' => $pjaxContainerId, 'timeout' => 6000,'enablePushState' => false]) ?>

                    <?php $statistics = new StatisticsHelper($model->cs_id, StatisticsHelper::TYPE_CASE) ?>
                    <?php echo $this->render('/partial/_communication_statistic', ['statistics' => $statistics->setCountAll()]) ?>


                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $dataProvider,

                        'options' => [
                            'tag' => 'div',
                            'class' => 'list-wrapper',
                            'id' => 'list-wrapper',
                        ],
                        'emptyText' => '<div class="text-center">Not found communication messages</div><br>',
                        'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                        'itemView' => function ($model, $key, $index, $widget) use ($dataProvider, $listItemView, $disableMasking) {
                            return $this->render($listItemView, ['model' => $model, 'dataProvider' => $dataProvider, 'disableMasking' => $disableMasking]);
                        },

                        'itemOptions' => [
                            //'class' => 'item',
                            'tag' => false,
                        ],

                        /*'pager' => [
                            'firstPageLabel' => 'first',
                            'lastPageLabel' => 'last',
                            'nextPageLabel' => 'next',
                            'prevPageLabel' => 'previous',
                            'maxButtonCount' => 3,
                        ],*/

                    ]) ?>

                    <?php yii\widgets\Pjax::end() ?>
                </div>

                <?php yii\widgets\Pjax::begin(['id' => $pjaxContainerIdForm, 'timeout' => 6000, 'enablePushState' => false]) ?>

                <?php if ($model->isProcessing() || $model->isSolved()) :?>
                     <div class="chat__form panel">

                    <?php Modal::begin(['id' => 'modal-email-preview',
                        'title' => 'Email preview',
                        'size' => Modal::SIZE_LARGE
                    ])?>

                    <?php $form2 = \yii\bootstrap\ActiveForm::begin([
                        //'action' => ['index'],
                        'id' => 'email-preview-form',
                        'method' => 'post',
                        'options' => [
                            'data-pjax' => 1,
                            'class' => 'panel-body',
                        ],
                    ]);

                        echo $form2->errorSummary($previewEmailForm);
                    ?>

                    <?php /*<div class="modal fade" id="modal-email-preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Email preview</h4>
                        </div>
                        <div class="modal-body">*/ ?>

                    <div class="row">
                        <div class="col-sm-4 form-group">

                            <?= $form2->field($previewEmailForm, 'e_email_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                            <?= $form2->field($previewEmailForm, 'e_email_from_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>


                            <?= $form2->field($previewEmailForm, 'e_case_id')->hiddenInput()->label(false); ?>
                            <?= $form2->field($previewEmailForm, 'e_language_id')->hiddenInput()->label(false); ?>
                            <?= $form2->field($previewEmailForm, 'e_email_tpl_id')->hiddenInput()->label(false); ?>
                            <?= $form2->field($previewEmailForm, 'e_quote_list')->hiddenInput()->label(false); ?>
                        </div>
                        <div class="col-sm-4 form-group">
                            <?= $form2->field($previewEmailForm, 'e_email_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                            <?= $form2->field($previewEmailForm, 'e_email_to_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <?= $form2->field($previewEmailForm, 'e_email_subject')->textInput(['class' => 'form-control', 'maxlength' => true]) ?>
                        </div>
                    </div>
                     <?php if (FileStorageSettings::canEmailAttach()) : ?>
                         <div class="row">
                             <div class="col-sm-6 form-group">
                                 <?= FileStorageEmailSendListWidget::byCase($previewEmailForm->getFileList()) ?>
                             </div>
                         </div>
                     <?php endif; ?>
                    <div class="form-group">

                        <?php echo $form2
                            ->field($previewEmailForm, 'e_email_message')
                            ->textarea([
                                'style' => 'display:none',
                                'id' => 'e_email_message'
                            ])
                        ?>

                        <?php if ($previewEmailForm->keyCache) : ?>
                            <div style="max-height: 800px; overflow-x: auto;">
                                <iframe
                                    id="email_view"
                                    src="<?php echo Url::to(['/lead/get-template', 'key_cache' => $previewEmailForm->keyCache]) ?>"
                                    style="width: 100%; height: 800px; border: 0;"></iframe>
                            </div>
                        <?php endif ?>

                    </div>
                    <?php if ($isAdmin) :?>
                    <div class="row" style="display: none" id="email-data-content-div">
                        <pre><?php
                            //\yii\helpers\VarDumper::dump($previewEmailForm->e_content_data, 10, true);
                            echo json_encode($previewEmailForm->e_content_data);
                        ?>
                        </pre>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <?php $messageSize = mb_strlen($previewEmailForm->e_email_message) ?>
                            <b>Content size: <?=Yii::$app->formatter->asShortSize($messageSize, 1) ?></b>
                            <?php if ($messageSize > 102 * 1024) : ?>
                                    &nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
                            <?php endif; ?>

                            <hr>
                        </div>
                    </div>

                    <div class="btn-wrapper text-right">
                        <?= Html::submitButton(
                            '<i class="fa fa-envelope-o"></i> Send Email',
                            ['class' => 'btn btn-lg btn-primary', 'id' => 'send_email_btn']
                        ) ?>
                        <?php if ($isAdmin) :?>
                            <?= Html::button('<i class="fa fa-list"></i> Show Email data (for Admins)', ['class' => 'btn btn-lg btn-warning', 'onclick' => '$("#email-data-content-div").toggle()']) ?>
                        <?php endif; ?>
                    </div>
                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                    <?php Modal::end()?>



                    <?php Modal::begin(['id' => 'modal-sms-preview',
                        'title' => 'SMS preview',
                        'size' => Modal::SIZE_DEFAULT
                    ])?>

                        <?php $form3 = \yii\bootstrap\ActiveForm::begin([
                                //'action' => ['index'],
                                //'id' => 'sms-preview-form',
                                'method' => 'post',
                                'options' => [
                                    'data-pjax' => 1,
                                    'class' => 'panel-body',
                                ],
                            ]);

                            echo $form3->errorSummary($previewSmsForm);

                        ?>


                        <div class="row">
                            <div class="alert alert-info alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Attention!</strong> Check the length of messages and try to use the minimum number of characters.
                            </div>

                            <div class="col-sm-6 form-group">
                                <?= $form3->field($previewSmsForm, 's_phone_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                                <?php //= $form3->field($previewSmsForm, 's_case_id')->hiddenInput()->label(false);?>
                                <?= $form3->field($previewSmsForm, 's_language_id')->hiddenInput()->label(false); ?>
                                <?= $form3->field($previewSmsForm, 's_sms_tpl_id')->hiddenInput()->label(false); ?>
                                <?= $form3->field($previewSmsForm, 's_quote_list')->hiddenInput()->label(false) ?>
                            </div>
                            <div class="col-sm-6 form-group">
                                <?= $form3->field($previewSmsForm, 's_phone_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form3->field($previewSmsForm, 's_sms_message')->textarea(['rows' => 6, 'class' => 'form-control', 'id' => 'preview-sms-message']) ?>
                            <table class="table table-condensed table-responsive table-bordered" id="preview-sms-counter">
                                <tr>
                                    <td>Length: <span class="length"></span></td>
                                    <td>Messages: <span class="messages"></span></td>
                                    <td>Per Message: <span class="per_message"></span></td>
                                    <td>Remaining: <span class="remaining"></span></td>
                                    <td>Encoding: <span class="encoding"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="btn-wrapper text-center">
                            <?= Html::submitButton('Send SMS <i class="fa fa-paper-plane"></i>', ['class' => 'btn btn-lg btn-primary']) ?>
                        </div>

                        <?php \yii\bootstrap\ActiveForm::end(); ?>

                    <?php Modal::end()?>





                    <?php $form = \yii\bootstrap\ActiveForm::begin([
                        //'action' => ['index'],
                        'id' => 'communication-form',
                        'method' => 'post',
                        'options' => [
                            'data-pjax' => 1,
                        ],
                    ]);


//                        $clientEmails = ['chalpet@mail.com' => 'chalpet@mail.com']; //\yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email');
//                        $clientEmails[Yii::$app->user->identity->email] = Yii::$app->user->identity->email;

                        $clientEmails = $model->client ? $model->client->getEmailList() : [];
                    foreach ($clientEmails as $key => $element) {
                        $clientEmails[$key] = \sales\helpers\email\MaskEmailHelper::masking($element, $disableMasking);
                    }
                        $clientPhones = $model->client ? $model->client->getPhoneNumbersSms($disableMasking) : [];

                    if (Yii::$app->session->hasFlash('send-success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        echo Yii::$app->session->getFlash('send-success');
                        echo '</div>';
                        $this->registerJs('$("body").removeClass("modal-open"); $(".modal-backdrop").remove();updateCommunication();');
                    }

                    if (Yii::$app->session->hasFlash('sms-send-success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        echo Yii::$app->session->getFlash('sms-send-success');
                        echo '</div>';
                        $this->registerJs('$("body").removeClass("modal-open"); $(".modal-backdrop").remove();updateCommunication();');
                    }

                    if (Yii::$app->session->hasFlash('send-error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        echo Yii::$app->session->getFlash('send-error');
                        echo '</div>';
                        $this->registerJs('$("body").removeClass("modal-open"); $(".modal-backdrop").remove();updateCommunication();');
                    }

                        echo $form->errorSummary($comForm);

                    ?>


                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <?php
                                    $typeList = [];
                                    $agentParams = \common\models\UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->withEmailList()->withPhoneList()->limit(1)->one();

                                    /** @var \common\models\Employee $userModel */
                                    $userModel = Yii::$app->user->identity;


                                    $call_type = \common\models\UserProfile::find()->select('up_call_type_id')->where(['up_user_id' => Yii::$app->user->id])->one();


                                if ($call_type && $call_type->up_call_type_id) {
                                    $call_type_id = $call_type->up_call_type_id;
                                } else {
                                    $call_type_id = \common\models\UserProfile::CALL_TYPE_OFF;
                                }


                                if ($agentParams) {
                                    foreach (\frontend\models\CommunicationForm::TYPE_LIST as $tk => $itemName) {
                                        if ($tk == \frontend\models\CommunicationForm::TYPE_EMAIL) {
                                            if ($model->isDepartmentSupport()) {
                                                $typeList[$tk] = $itemName;
//                                              } else if ($agentParams->upp_email) {
//                                                    $typeList[$tk] = $itemName . ' (' . $agentParams->upp_email . ')';
//                                              }
                                            } elseif ($agentParams->getEmail()) {
                                                $typeList[$tk] = $itemName . ' (' . $agentParams->getEmail() . ')';
                                            }
                                        }

                                        //if ($agentParams->upp_tw_phone_number) {
                                        if ($tk == \frontend\models\CommunicationForm::TYPE_SMS && $smsEnabled) {
                                            if ($model->isDepartmentSupport()) {
                                                $typeList[$tk] = $itemName;
//                                                    } elseif ($agentParams->upp_tw_phone_number){
//                                                        $typeList[$tk] = $itemName . ' (' . $agentParams->upp_tw_phone_number . ')';
                                            } elseif ($agentParams->getPhone()) {
                                                $typeList[$tk] = $itemName . ' (' . $agentParams->getPhone() . ')';
                                            }
                                        }


//                                                if($call_type_id) {
//
//                                                    $callTypeName = \common\models\UserProfile::CALL_TYPE_LIST[$call_type_id] ?? '-';
//
//                                                    if($call_type_id == \common\models\UserProfile::CALL_TYPE_SIP && $userModel->userProfile && !$userModel->userProfile->up_sip) {
//                                                        $callTypeName .= ' [empty account]';
//                                                    }
//
//                                                    if ($tk == \frontend\models\CommunicationForm::TYPE_VOICE) {
//                                                        //if ($userModel->userProfile->up_sip) {
//                                                        $typeList[$tk] = $itemName . ' ('.$callTypeName.')';
//                                                        //}
//                                                    }
//                                                }
                                            //}
                                    }
                                }

                                if ($call_type_id) {
                                    $callTypeName = \common\models\UserProfile::CALL_TYPE_LIST[$call_type_id] ?? '-';

                                    if ($call_type_id == \common\models\UserProfile::CALL_TYPE_SIP && $userModel->userProfile && !$userModel->userProfile->up_sip) {
                                        $callTypeName .= ' [empty account]';
                                    }

                                    //if ($userModel->userProfile->up_sip) {
                                    $typeList[\frontend\models\CommunicationForm::TYPE_VOICE] = \frontend\models\CommunicationForm::TYPE_LIST[\frontend\models\CommunicationForm::TYPE_VOICE] . ' (' . $callTypeName . ')';
                                    //}
                                }


                                ?>


                                <?= $form->field($comForm, 'c_type_id')->dropDownList($typeList, ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_type_id']) ?>
                                <?= $form->field($comForm, 'c_quotes')->hiddenInput(['id' => 'c_quotes'])->label(false); ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-sms" id="sms-template-group">
                                <?php //= $form->field($comForm, 'c_sms_tpl_id')->dropDownList(\common\models\SmsTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_tpl_id'])?>
                                <?= $form->field($comForm, 'c_sms_tpl_key')->dropDownList(\common\models\SmsTemplateType::getKeyList(false, $model->cs_dep_id), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_tpl_key']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-email" id="email-address" style="display: none;">
                                <?= $form->field($comForm, 'c_email_to')->dropDownList($clientEmails, ['prompt' => '---', 'class' => 'form-control', 'id' => 'email']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-email" id="email-template-group" style="display: none;">
                                <?php //= $form->field($comForm, 'c_email_tpl_id')->dropDownList(\common\models\EmailTemplateType::getList(false, \common\models\Department::DEPARTMENT_SALES), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_tpl_id'])?>
                                <?= $form->field($comForm, 'c_email_tpl_key')->dropDownList([], ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_tpl_key']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-sms message-field-email" id="language-group" style="display: block;">

                                <?php
                                    $localeList = ProjectLocale::getLocaleListByProject((int) $model->cs_project_id);
                                    $clientLocale = $model->client ? (string) $model->client->cl_locale : '';
                                    $projectDefaultLocale = ProjectLocale::getDefaultLocaleByProject((int) $model->cs_project_id);
                                    $defaultLocale = ProjectLocaleHelper::getSelectedLocale($localeList, $clientLocale, $projectDefaultLocale);
                                ?>

                                <?php echo $form->field($comForm, 'c_language_id')
                                    ->dropDownList(
                                        $localeList,
                                        [
                                            'class' => 'form-control',
                                            'id' => 'language',
                                            'options' => [
                                                $defaultLocale => ['selected' => true]
                                            ],
                                        ]
                                    ) ?>
                            </div>

                            <?php if ($model->isDepartmentSupport()) : ?>
                                <div class="col-md-3 form-group message-field-email" id="department-emails">
                                    <?php
                                    $departmentEmailsList = [];
                                    /** @var DepartmentEmailProject[] $departmentEmails */
                                    $departmentEmails = $model->getDepartmentEmailsByProjectAndDepartment()->where(['dep_default' => \common\models\DepartmentPhoneProject::DEP_DEFAULT_TRUE])->withEmailList()->all();
                                    foreach ($departmentEmails as $departmentEmail) {
                                        if ($departmentEmail->getEmail()) {
                                            $departmentEmailsList[$departmentEmail->dep_id] = $departmentEmail->getEmail();
                                        }
                                    }
                                    ?>
                                    <?php
                                        $optionsEmail = ['class' => 'form-control'];
                                    if (count($departmentEmailsList) > 1) {
                                        $optionsEmail['prompt'] = '---';
                                    }
                                    ?>
                                    <?php //= $form->field($comForm,'dep_email_id')->dropDownList(\yii\helpers\ArrayHelper::map($departmentEmails, 'dep_id', 'dep_email'), $optionsEmail)?>
                                    <?= $form->field($comForm, 'dep_email_id')->dropDownList($departmentEmailsList, $optionsEmail) ?>
                                </div>
                            <?php endif; ?>

                            <div class="col-sm-12 form-group message-field-email" id="email-subtitle-group" style="display: none;">
                                <?= $form->field($comForm, 'c_email_subject')->textInput(['class' => 'form-control', 'id' => 'email-subtitle', 'maxlength' => true]) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-phone message-field-sms" id="phone-numbers-group" style="display: block;">
                                <?= $form->field($comForm, 'c_phone_number')->dropDownList($clientPhones, ['prompt' => '---', 'class' => 'form-control', 'id' => 'call-to-number']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-phone" style="display: block;">
                                <?= Html::label('Phone from', null, ['class' => 'control-label']) ?>
                                <?= Html::dropDownList('call-from-number', null, $fromPhoneNumbers, ['prompt' => '---', 'id' => 'call-from-number', 'class' => 'form-control', 'label'])?>
                            </div>
                            <div class="col-sm-3 form-group message-field-phone" style="display: block;">
                                <?= Html::button('<i class="fa fa-phone-square"></i> Make Call', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-make-call-case-communication-block', 'style' => 'margin-top: 28px'])?>
                            </div>
                            <?=Html::hiddenInput('call-case-id', $model->cs_id, ['id' => 'call-case-id'])?>
                            <?=Html::hiddenInput('call-client-name', ($model->cs_client_id ? $model->client->getShortName() : ''), ['id' => 'call-client-name'])?>

                            <?php if ($model->isDepartmentSupport()) : ?>
                                <div class="col-md-3 form-group message-field-sms" id="sms-phone-numbers">
                                    <?php
                                        $departmentPhonesList = [];
                                        /** @var DepartmentPhoneProject[] $departmentPhones */
                                        $departmentPhones = $model->getDepartmentPhonesByProjectAndDepartment()->where(['dpp_default' => \common\models\DepartmentPhoneProject::DPP_DEFAULT_TRUE])->withPhoneList()->all();
                                    foreach ($departmentPhones as $departmentPhone) {
                                        if ($departmentPhone->getPhone()) {
                                            $departmentPhonesList[$departmentPhone->dpp_id] = $departmentPhone->getPhone();
                                        }
                                    }
                                    ?>
                                    <?php
                                    $optionsPhone = ['class' => 'form-control'];
                                    if (count($departmentPhonesList) > 1) {
                                        $optionsPhone['prompt'] = '---';
                                    }
                                    ?>
                                    <?php //= $form->field($comForm,'dpp_phone_id')->dropDownList(\yii\helpers\ArrayHelper::map($departmentPhones, 'dpp_id', 'dpp_phone_number'), $optionsPhone)?>
                                    <?= $form->field($comForm, 'dpp_phone_id')->dropDownList($departmentPhonesList, $optionsPhone) ?>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div id="sms-input-box" class="message-field-sms">
                            <div class="form-group" id="sms-textarea-div">
                                <?= $form->field($comForm, 'c_sms_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'sms-message']) ?>

                                <table class="table table-condensed table-responsive table-bordered" id="sms-counter">
                                    <tr>
                                        <td>Length: <span class="length"></span></td>
                                        <td>Messages: <span class="messages"></span></td>
                                        <td>Per Message: <span class="per_message"></span></td>
                                        <td>Remaining: <span class="remaining"></span></td>
                                        <td>Encoding: <span class="encoding"></span></td>
                                    </tr>
                                </table>

                            </div>
                            <div class="btn-wrapper">
                                <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send SMS', ['class' => 'btn btn-lg btn-primary']) ?>
                            </div>
                        </div>

                        <div id="email-input-box" class="message-field-email" style="display: none;">
                            <div class="form-group" id="email-textarea-div">
                                <?php //= $form->field($comForm, 'c_email_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'email-message'])?>

                                <?= $form->field($comForm, 'c_email_message')->widget(\dosamigos\ckeditor\CKEditor::class, [
                                    'options' => [
                                        'rows' => 6,
                                        'readonly' => false
                                    ],
                                    'preset' => 'custom',
                                    'clientOptions' => [
                                        'height' => 500,
                                        'fullPage' => false,

                                        'allowedContent' => true,
                                        'resize_enabled' => false,
                                        'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                                        'removePlugins' => 'elementspath',
                                    ]
                                ]) ?>

                            </div>
                            <div class="btn-wrapper">
                                <?= Html::submitButton(
                                    '<i class="fa fa-envelope-o"></i> Preview and Send Email',
                                    ['class' => 'btn btn-lg btn-primary', 'id' => 'preview_email_btn']
                                ) ?>
                            </div>
                        </div>

                        <?= $form2->field($comForm, 'c_voice_status')->hiddenInput(['id' => 'c_voice_status'])->label(false); ?>
                        <?= $form2->field($comForm, 'c_voice_sid')->hiddenInput(['id' => 'c_voice_sid'])->label(false); ?>
                        <?= $form2->field($comForm, 'c_call_id')->hiddenInput(['id' => 'c_call_id'])->label(false); ?>

                    <?php
                    if ($comForm->c_preview_email) {
                        $js = <<<JS
 
    $('#modal-email-preview').modal('show');
    
    var isProcessing = false;
    
    $(document).on('click', '#send_email_btn', function(e) {    
        if (isProcessing) {
            return;
        } 
        isProcessing = true; 
    
        e.preventDefault();
        e.stopPropagation();
        
        let btn = $(this);
        btn.prop('disabled', true);                
        let loaderInner = '<span class="spinner-border spinner-border-sm"></span> Loading';
        btn.html(loaderInner);
        
        let iframeEmail = document.getElementById('email_view');
        let contentEmail = iframeEmail.contentWindow.document.documentElement.outerHTML;
             
        $('#e_email_message').val(contentEmail);        
        $('#email-preview-form').submit(); 
        return true;       
    });
JS;
                        $this->registerJs($js);
                    }
                    ?>

                    <?php
                    if ($comForm->c_preview_sms) {
                        $this->registerJs("$('#modal-sms-preview').modal('show');");
                    }
                    ?>

                    <?php
                    $js = <<<JS
    
    function initializeMessageType(messageType) {
        if (messageType == 2) {
            $('.message-field-phone').hide();
            $('.message-field-email').hide();
            $('.message-field-sms').show();
        }
        else if (messageType == 3) {
            $('.message-field-sms').hide();
            $('.message-field-email').hide();
            $('.message-field-phone').show();
        }
        else if (messageType == 1) {
            $('.message-field-sms').hide();
            $('.message-field-phone').hide();
            $('.message-field-email').show();
        } else {
            $('.message-field-sms').hide();
            $('.message-field-phone').hide();
            $('.message-field-email').hide();          
            $("#c_email_tpl_key").val($("#c_email_tpl_key option:first").val());
        }
        
        $('#c_sms_tpl_id').trigger('change');
        $('#c_email_tpl_key').trigger('change');
        //$('#c_email_tpl_id').trigger('change');
                
        $('#sms-message').countSms('#sms-counter');
        $('#preview-sms-message').countSms('#preview-sms-counter');        
    }

    initializeMessageType($c_type_id);

var emails = '$unsubscribedEmails';
$('#email option').each(function() {             
    if (JSON.parse(emails).includes($(this).attr('value'))){ 
        $(this).html($(this).attr('value') + ' (unsubscribed)')
    }
    if ($(this).attr('value') == ""){
        $(this).html('---')       
    }
});  

JS;

                    $this->registerJs($js);
                    ?>

                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                </div>
                <?php endif; ?>

                <?php yii\widgets\Pjax::end() ?>
            </div>
        </div>

    </div>
</div>


<?php Modal::begin(['id' => 'modal-email-view',
    'title' => 'Email view',
    'size' => Modal::SIZE_LARGE
])?>
    <div class="view-mail">
        <object id="object-email-view" width="100%" height="800" data=""></object>
    </div>
<?php Modal::end()?>


<?php
    $currentUrl = \yii\helpers\Url::current();
    $jsPath = Yii::$app->request->baseUrl . '/js/sounds/';
?>

<script>
    const currentUrl = '<?=$currentUrl?>';

    function updateCommunication() {
        $.pjax.reload({url: currentUrl, container: '#<?= $pjaxContainerId ?>', push: false, replace: false, timeout: 6000});
    }

</script>

<?php
$tpl_email_blank_key = \frontend\models\CommunicationForm::TPL_TYPE_EMAIL_BLANK_KEY;
$tpl_email_support_blank_page_key = \frontend\models\CommunicationForm::TPL_TYPE_EMAIL_SUPPORT_BLANK_PAGE_KEY;
$tpl_email_exchange_blank_page_key = \frontend\models\CommunicationForm::TPL_TYPE_EMAIL_EXCHANGE_BLANK_PAGE_KEY;
$tpl_sms_blank_key = \frontend\models\CommunicationForm::TPL_TYPE_SMS_BLANK_KEY;
$emailTemplateTypes = @json_encode($emailTemplateTypes);

$js = <<<JS

    const tpl_email_support_blank_page_key = '$tpl_email_support_blank_page_key';
    const tpl_email_exchange_blank_page_key = '$tpl_email_exchange_blank_page_key';
    const tpl_email_blank_key = '$tpl_email_blank_key';
    const tpl_sms_blank_key = '$tpl_sms_blank_key';
    let projectId = '{$model->project->id}';
    let project = '{$model->project->name}';
    let emailTemplateTypes = '{$emailTemplateTypes}';

    $('body').on("change", '#c_type_id', function () {
        initializeMessageType($(this).val());        
    });
    
     $('body').on("change", '#c_sms_tpl_key', function () {
        if($(this).val() == tpl_sms_blank_key) {
            $('#sms-textarea-div').show();
        } else {
            $('#sms-textarea-div').hide();
        }
    });
    
    $('body').on("change", '#c_email_tpl_key', function () {
        let type_id = $('#c_type_id').val();

        if(parseInt(type_id) === 1) { // Email
            if($(this).val() == tpl_email_blank_key) {
                $('#email-textarea-div').show();
                $('#email-subtitle-group').show();
            } else if($(this).val() == tpl_email_support_blank_page_key) {
                $('#email-textarea-div').show();
                $('#email-subtitle-group').show();
            } else if($(this).val() == tpl_email_exchange_blank_page_key) {
                $('#email-textarea-div').show();
                $('#email-subtitle-group').show();
            } else {
                $('#email-textarea-div').hide();
                $('#email-subtitle-group').hide();
            }
        }
    });

    $('body').on("change", '#email', function () {
        let etpOptions = '<option>---</option>';      
        
        if (emails.includes(this.value)){ 
            $.each(JSON.parse(emailTemplateTypes), function(key, item) {                 
                if (item.etp_ignore_unsubscribe == 1) {                    
                   etpOptions += '<option value="'+ item.etp_key+'">' + item.etp_name + '</option>';
                }
            }); 
            document.getElementById("c_email_tpl_key").innerHTML = etpOptions;
        } else {
             $.each(JSON.parse(emailTemplateTypes), function(key, item) {
                   etpOptions += '<option value="'+ item.etp_key+'">' + item.etp_name + '</option>';              
            }); 
            document.getElementById("c_email_tpl_key").innerHTML = etpOptions;
        }
    });

    $('body').on('click', '.chat__details', function () {
        
        let id = $(this).data('id');        
        let date = $(this).data('date');
        let from = $(this).data('from');
        let to = $(this).data('to');
        let subject = $(this).data('subject');
        let files = $(this).data('files');
        
        if (files) {
            files = '<hr>' + files;
        }
        
        var obj = document.getElementById('object-email-view');
        obj.data = '/email/view?id='+id+'&preview=1';
        obj.parentNode.replaceChild(obj.cloneNode(true), obj);
        $(".view-mail").replaceWith('<div id="mail_headers"><h6><div id="email_info" class="float-left">' + subject + '<br>' + from + '<br>' + to + '<br>' +  date + files + '<br><br></div>' + '</h6><button id="print_button" title="Allow popups in your browser if this doesn`t work." data-toggle="mail_tooltip" class="btn btn-warning float-right"><i class="fa fa-print"></i> Print</button><div class="clearfix"></div><hr>' + '</div>'+ $(".view-mail").html() );
        var popup = $('#modal-email-view');
        //previewPopup.find('.modal-body').html(data);
        popup.modal('show');
        return false;
    });
    
    $('body').on('click', '#print_button', function () {
        let w = window.open();
        $(w.document.body).html($('#object-email-view').contents()[0].body.innerHTML);
        w.document.head.append('<style>@media print { body background-color:#FFFFFF; background-image:none; color:#000000 }  }</style>');
        let mail_headers = document.createElement("div");
        mail_headers.innerHTML = $('#email_info').html();
        w.document.body.prepend(mail_headers);
        let js_timer = document.createElement("script");
        js_timer.innerHTML = 'setTimeout( "window.print(); window.close();", 3000);'; 
        w.document.head.append(js_timer);
        // window.document.addEventListener('DOMContentLoaded', function () { window.print(); window.close(); }, false);
    });

    $('body').on('change', '.quotes-uid', function() {
        
        var quoteList = [];
        var jsonQuotes = '';
        
        $('input[type=checkbox].quotes-uid:checked').each(function() {
            quoteList.push($(this).data('id'));
        });
        
        if (quoteList.length === 0) {
            jsonQuotes = '';
            
        } else {
            jsonQuotes = JSON.stringify(quoteList);
        }
        $('#c_quotes').val(jsonQuotes);
    });
    
    $(document).on('beforeSubmit', '#communication-form', function(e) {
        let btn = $('#preview_email_btn'),
            loaderInner = '<span class="spinner-border spinner-border-sm"></span> Loading';
        btn.html(loaderInner).prop('disabled', true);
    });
    
JS;

$this->registerJs($js);
