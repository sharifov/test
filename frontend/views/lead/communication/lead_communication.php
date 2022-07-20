<?php

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm CommunicationForm
 * @var $leadForm LeadForm
 * @var $previewEmailForm LeadPreviewEmailForm
 * @var $previewSmsForm LeadPreviewSmsForm
 * @var $isAdmin bool
 * @var $lead Lead
 * @var $unsubscribedEmails array
 * @var $disableMasking bool
 * @var $unsubscribe
 * @var AbacCallFromNumberList $callFromNumberList
 * @var AbacSmsFromNumberList $smsFromNumberList
 * @var AbacEmailList $emailFromList
 */

use common\models\Call;
use common\models\Lead;
use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use modules\email\src\abac\dto\EmailPreviewDto;
use modules\email\src\abac\EmailAbacObject;
use modules\featureFlag\FFlag;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageEmailSendListWidget;
use src\auth\Auth;
use src\helpers\communication\StatisticsHelper;
use src\helpers\projectLocale\ProjectLocaleHelper;
use src\helpers\setting\SettingHelper;
use src\model\call\useCase\createCall\fromLead\AbacCallFromNumberList;
use src\model\email\useCase\send\fromLead\AbacEmailList;
use src\model\project\entity\projectLocale\ProjectLocale;
use src\model\sms\useCase\send\fromLead\AbacSmsFromNumberList;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use common\models\EmailTemplateType;
use common\models\QuoteCommunication;

$c_type_id = $comForm->c_type_id;

$pjaxContainerId = 'pjax-lead-communication-log';
$unsubscribedEmails = @json_encode($unsubscribedEmails);
$emailTemplateTypes = EmailTemplateType::getEmailTemplateTypesList(false, $lead->getDepartmentId(), $lead->project_id, $lead);
$emailTemplateTypes = @json_encode($emailTemplateTypes);

$abacDto = new EmailPreviewDto($previewEmailForm->e_email_tpl_id, null, null, null, $lead, null);

/** @abac $abacDto, EmailAbacObject::ACT_VIEOBJ_PREVIEW_EMAILmailAbacObject::ACTION_EDIT_FROM, Restrict access to edit input email_from in lead communication block */
$emailFromReadonly = !Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_FROM);
/** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_TO, Restrict access to edit input email_to in lead communication block*/
$emailToReadonly = !Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_TO);
/** @abac $abacDto, EmailAbacObject::ACT_VIOBJ_PREVIEW_EMAILEmailAbacObject::ACTION_EDIT_EMAIL_FROM_NAME, Restrict access to edit input email_from_name in lead communication block*/
$emailFromNameReadonly = !Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_EMAIL_FROM_NAME);
/** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_EMAIL_TO_NAME, Restrict access to edit input email_to_name in lead communication block*/
$emailToNameReadonly = !Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_EMAIL_TO_NAME);
/** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_SUBJECT, Restrict access to edit input email_subject in lead communication block*/
$emailSubjectReadonly = !Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_SUBJECT);
/** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_MESSAGE, Restrict access to edit input email_message in lead communication block*/
$emailMessageReadonly = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_EDIT_MESSAGE);
/** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_ATTACH_FILES, Restrict access to attach files in lead communication block*/
$canAttachFiles = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_ATTACH_FILES);
/** @abac $abacDto, EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_SHOW_EMAIL_DATA, Restrict access to view emails on case or lead*/
$canShowEmailData = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SHOW_EMAIL_DATA);

?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-comments"></i> Communication</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">

            <div class="panel">
                <div class="chat__list">

                    <div class="communication-block-scroll">

                    <?php yii\widgets\Pjax::begin(['id' => $pjaxContainerId , 'timeout' => 5000]) ?>

                    <?php $statistics = new StatisticsHelper($lead->id, StatisticsHelper::TYPE_LEAD) ?>
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
                        'itemView' => function ($model, $key, $index, $widget) use ($dataProvider, $disableMasking) {
                            return $this->render('_list_item_log', [
                                    'model' => $model,
                                    'dataProvider' => $dataProvider,
                                    'disableMasking' => $disableMasking
                                ]);
                        },

                        'itemOptions' => [
                            //'class' => 'item',
                            'tag' => false,
                        ],

                    ]) ?>

                    <?php yii\widgets\Pjax::end() ?>

                    </div>

                    <?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-communication-log-form' , 'timeout' => 5000]) ?>

                        <?php if ($unsubscribe) : ?>
                            <div class="chat__form panel">
                                <div class="alert alert-warning" role="alert">
                                    <h4 class="alert-heading">Warning!</h4>
                                    <p>Client communication restricted. By client request...</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        $typeList = [];
                        $call_type = \common\models\UserProfile::find()->select('up_call_type_id')->where(['up_user_id' => Yii::$app->user->id])->one();

                        if ($call_type && $call_type->up_call_type_id && $callFromNumberList->canMakeCall()) {
                            $typeList[\frontend\models\CommunicationForm::TYPE_VOICE] = \frontend\models\CommunicationForm::TYPE_LIST[\frontend\models\CommunicationForm::TYPE_VOICE];
                        }

                        if ($smsFromNumberList->canSendSms()) {
                            $typeList[\frontend\models\CommunicationForm::TYPE_SMS] = \frontend\models\CommunicationForm::TYPE_LIST[\frontend\models\CommunicationForm::TYPE_SMS];
                        }

                        if ($emailFromList->canSendEmail()) {
                            $typeList[\frontend\models\CommunicationForm::TYPE_EMAIL] = \frontend\models\CommunicationForm::TYPE_LIST[\frontend\models\CommunicationForm::TYPE_EMAIL];
                        }
                        ?>

                    <?php if ($typeList) : ?>
                        <div class="chat__form panel">

                            <?php // Modal Preview Email Start ?>
                            <?php Modal::begin(['id' => 'modal-email-preview',
                                'title' => 'Email preview',
                                'size' => Modal::SIZE_LARGE
                            ])?>

                                <?php $previewEmailActiveForm = \yii\bootstrap\ActiveForm::begin([
                                    'id' => 'email-preview-form',
                                    'method' => 'post',
                                    'options' => [
                                        'data-pjax' => 1,
                                        'class' => 'panel-body',
                                    ],
                                ]); ?>

                                    <?= $previewEmailActiveForm->errorSummary($previewEmailForm); ?>

                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => $emailFromReadonly]) ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_from_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => $emailFromNameReadonly]) ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_lead_id')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_language_id')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_tpl_id')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_quote_list')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_offer_list')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_message_edited')->hiddenInput(['id' => 'e_email_message_edited'])->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_subject_origin')->hiddenInput()->label(false); ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_qc_uid')->hiddenInput(['value' => $comForm->c_qc_uid])->label(false); ?>
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => $emailToReadonly]) ?>
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_to_name')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => $emailToNameReadonly]) ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <?= $previewEmailActiveForm->field($previewEmailForm, 'e_email_subject')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => $emailSubjectReadonly]) ?>
                                        </div>
                                    </div>

                                    <?php if ($canAttachFiles && FileStorageSettings::canEmailAttach()) : ?>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <?= FileStorageEmailSendListWidget::byLead($previewEmailForm->getFileList()) ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="form-group">

                                        <?php echo $previewEmailActiveForm->field($previewEmailForm, 'e_email_message')->textarea(['style' => 'display:none', 'id' => 'e_email_message']) ?>

                                        <?php if ($previewEmailForm->keyCache) : ?>
                                            <div style="max-height: 800px;">
                                                <iframe
                                                        id="email_view"
                                                        src="<?php echo Url::to(['/lead/get-template', 'key_cache' => $previewEmailForm->keyCache]) ?>"
                                                        style="width: 100%; height: 800px; border: 0;<?= !$emailMessageReadonly ? 'pointer-events: none;' : '' ?>"></iframe>
                                            </div>
                                        <?php endif ?>

                                    </div>

                                    <?php if ($canShowEmailData) : ?>
                                    <div class="row" style="display: none" id="email-data-content-div">
                                        <pre><?php
                                            VarDumper::dump($previewEmailForm->e_content_data, 15, true);
                                        ?>
                                        </pre>
                                        JSON:
                                        <pre><?php echo Html::encode(json_encode($previewEmailForm->e_content_data)) ?></pre>
                                    </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php $messageSize = $previewEmailForm->countLettersInEmailMessage() ?>
                                            <b>Content size: <?=Yii::$app->formatter->asShortSize($messageSize, 1) ?></b>
                                            <?php if ($messageSize > 102 * 1024) : ?>
                                                &nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
                                            <?php endif; ?>

                                            <hr>
                                        </div>
                                    </div>

                                    <div class="btn-wrapper text-right">
                                        <?php /** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND, Restrict access to send email in preview email */ ?>
                                        <?php /** if (Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND)) : */ ?>
                                            <?= Html::submitButton(
                                                '<i class="fa fa-envelope-o"></i> Send Email',
                                                ['class' => 'btn btn-lg btn-primary', 'id' => 'send_email_btn']
                                            ) ?>
                                        <?php  /** endif; */ ?>
                                        <?php if ($canShowEmailData) :?>
                                            <?= Html::button('<i class="fa fa-list"></i> Show Email data (for Admins)', ['class' => 'btn btn-lg btn-warning', 'onclick' => '$("#email-data-content-div").toggle()']) ?>
                                        <?php endif; ?>
                                    </div>

                                <?php \yii\bootstrap\ActiveForm::end(); ?>

                            <?php Modal::end(); ?>
                            <?php // Modal Preview Email End ?>


                            <?php // Modal Preview SMS Start ?>
                            <?php Modal::begin(['id' => 'modal-sms-preview',
                                'title' => 'SMS preview',
                                'size' => Modal::SIZE_DEFAULT
                            ])?>

                                <?php $previewSmsActiveForm = \yii\bootstrap\ActiveForm::begin([
                                    //'action' => ['index'],
                                    //'id' => 'sms-preview-form',
                                    'method' => 'post',
                                    'options' => [
                                        'data-pjax' => 1,
                                        'class' => 'panel-body',
                                    ],
                                ]); ?>

                                    <?= $previewSmsActiveForm->errorSummary($previewSmsForm); ?>

                                    <div class="row">
                                        <div class="alert alert-info alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Attention!</strong> Check the length of messages and try to use the minimum number of characters.
                                        </div>

                                        <div class="col-sm-6 form-group">
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_phone_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                                            <?php //= $form3->field($previewSmsForm, 's_lead_id')->hiddenInput()->label(false);?>
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_language_id')->hiddenInput()->label(false); ?>
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_sms_tpl_id')->hiddenInput()->label(false); ?>
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_quote_list')->hiddenInput()->label(false) ?>
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_qc_uid')->hiddenInput(['value' => $comForm->c_qc_uid])->label(false) ?>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <?= $previewSmsActiveForm->field($previewSmsForm, 's_phone_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <?= $previewSmsActiveForm->field($previewSmsForm, 's_sms_message')->textarea(['rows' => 6, 'class' => 'form-control', 'id' => 'preview-sms-message']) ?>
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
                            <?php // Modal Preview SMS End ?>

                            <?php $communicationActiveForm = \yii\bootstrap\ActiveForm::begin([
                                //'action' => ['index'],
                                'id' => 'communication-form',
                                'method' => 'post',
                                'options' => [
                                    'data-pjax' => 1,
                                ],
                            ]);

                                $clientEmails = \yii\helpers\ArrayHelper::map($leadForm->getClientEmail(), 'email', 'email');
                                $clientEmails[Yii::$app->user->identity->email] = Yii::$app->user->identity->email;

                            foreach ($clientEmails as $key => $element) {
                                $clientEmails[$key] = \src\helpers\email\MaskEmailHelper::masking($element, $disableMasking);
                            }

                                $clientPhones = $leadForm->getClient()->getPhoneNumbersSms($disableMasking);

                            if (Yii::$app->session->hasFlash('send-success')) {
                                echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                echo Yii::$app->session->getFlash('send-success');
                                echo '</div>';
                                $this->registerJs('$("body").removeClass("modal-open"); $(".modal-backdrop").remove();updateCommunication();');
                            }
                            if (Yii::$app->session->hasFlash('send-warning')) {
                                echo '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                echo Yii::$app->session->getFlash('send-warning');
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

                            echo $communicationActiveForm->errorSummary($comForm);
                            echo $communicationActiveForm->field($comForm, 'c_qc_uid')->label(false)->hiddenInput(['value' => is_null($comForm->c_qc_uid) ? QuoteCommunication::generateUid() : $comForm->c_qc_uid]);
                            ?>


                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <?= $communicationActiveForm->field($comForm, 'c_type_id')->dropDownList($typeList, ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_type_id']) ?>
                                        <?= $communicationActiveForm->field($comForm, 'c_quotes')->hiddenInput(['id' => 'c_quotes'])->label(false) ?>
                                        <?= $communicationActiveForm->field($comForm, 'c_offers')->hiddenInput(['id' => 'c_offers'])->label(false) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-sms" id="sms-phone-from-group">
                                        <?php
                                        if (!$comForm->c_sms_from) {
                                            $comForm->c_sms_from = $smsFromNumberList->first();
                                        }
                                        ?>
                                        <?= $communicationActiveForm->field($comForm, 'c_sms_from')->dropDownList($smsFromNumberList->format(), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_from']) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-sms" id="sms-template-group">
                                        <?php //= $form->field($comForm, 'c_sms_tpl_id')->dropDownList(\common\models\SmsTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_tpl_id'])?>
                                        <?= $communicationActiveForm->field($comForm, 'c_sms_tpl_key')->dropDownList(\common\models\SmsTemplateType::getKeyList(false, \common\models\Department::DEPARTMENT_SALES, $lead->project_id), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_tpl_key']) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-email" id="email-from-address" style="display: none;">
                                        <?php
                                        if (!$comForm->c_email_from) {
                                            $comForm->c_email_from = $emailFromList->first();
                                        }
                                        ?>
                                        <?= $communicationActiveForm->field($comForm, 'c_email_from')->dropDownList($emailFromList->format(), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_from']) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-email" id="email-address" style="display: none;">
                                        <?= $communicationActiveForm->field($comForm, 'c_email_to')->dropDownList($clientEmails, ['prompt' => '---', 'class' => 'form-control', 'id' => 'email']) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-email" id="email-template-group" style="display: none;">
                                        <?= $communicationActiveForm->field($comForm, 'c_email_tpl_key')->dropDownList([], ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_tpl_key']) ?>
                                    </div>

                                    <?php
                                    $localeList = ProjectLocale::getLocaleListByProject((int)$lead->project_id);
                                    $comForm->c_language_id = null;
                                    ?>
                                    <?php if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_HIDE_LANGUAGE_FIELD_COMMUNICATION_BLOCK)) : ?>
                                        <div class="col-sm-3 form-group" id="language-group" style="display: none;">
                                            <?php echo $communicationActiveForm->field($comForm, 'c_language_id')->hiddenInput(['value' => 'en-US'])->label(false); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="col-sm-3 form-group message-field-sms message-field-email"
                                             id="language-group" style="display: block;">
                                            <?php echo $communicationActiveForm->field($comForm, 'c_language_id')
                                                ->dropDownList(
                                                    $localeList,
                                                    ['prompt' => '---', 'class' => 'form-control', 'id' => 'language']
                                                ) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-sm-12 form-group message-field-email" id="email-subtitle-group" style="display: none;">
                                        <?= $communicationActiveForm->field($comForm, 'c_email_subject')->textInput(['class' => 'form-control', 'id' => 'email-subtitle', 'maxlength' => true]) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-phone message-field-sms" id="phone-numbers-group" style="display: block;">
                                        <?= $communicationActiveForm->field($comForm, 'c_phone_number')->dropDownList($clientPhones, ['prompt' => '---', 'class' => 'form-control', 'id' => 'call-to-number']) ?>
                                    </div>

                                    <div class="col-sm-3 form-group message-field-phone" style="display: block;">
                                        <?= Html::label('Phone from', null, ['class' => 'control-label']) ?>
                                        <?= Html::dropDownList('call-from-number', $callFromNumberList->first(), $callFromNumberList->format(), ['prompt' => '---', 'id' => 'call-from-number', 'class' => 'form-control', 'label'])?>
                                    </div>
                                    <div class="col-sm-3 form-group message-field-phone" style="display: block;">
                                        <?= Html::button('<i class="fa fa-phone-square"></i> Make Call', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-make-call-lead-communication-block', 'style' => 'margin-top: 28px'])?>
                                    </div>
                                    <?=Html::hiddenInput('call-lead-id', $lead->id, ['id' => 'call-lead-id'])?>
                                    <?=Html::hiddenInput('call-client-name', ($lead->client_id ? $lead->client->getShortName() : ''), ['id' => 'call-client-name'])?>
                                </div>
                                <div id="sms-input-box" class="message-field-sms" >
                                    <div class="form-group" id="sms-textarea-div">
                                        <?= $communicationActiveForm->field($comForm, 'c_sms_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'sms-message']) ?>

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


                                        <?php
                                        echo $communicationActiveForm->field($comForm, 'c_email_message')->widget(Widget::class, [
                                            'settings' => [
                                                'minHeight' => 300,
                                                'plugins' => [
                                                    'clips',
                                                    'fullscreen',
                                                ],
                                                'pasteCallback' => new \yii\web\JsExpression('function(html) { 
                                                    let dataImageSubstring = `<img src="data:image/png;base64,`;
                                                    if(html.includes(dataImageSubstring)) {
                                                        html = "";
                                                    }
                                                  return html;}')
                                            ],
                                        ]);
                                        ?>

                                    </div>
                                    <div class="btn-wrapper">
                                        <?= Html::submitButton(
                                            '<i class="fa fa-envelope-o"></i> Preview and Send Email',
                                            ['class' => 'btn btn-lg btn-primary', 'id' => 'preview_email_btn']
                                        ) ?>
                                    </div>
                                </div>

                                <?= $previewEmailActiveForm->field($comForm, 'c_voice_status')->hiddenInput(['id' => 'c_voice_status'])->label(false); ?>
                                <?= $previewEmailActiveForm->field($comForm, 'c_voice_sid')->hiddenInput(['id' => 'c_voice_sid'])->label(false); ?>
                                <?= $previewEmailActiveForm->field($comForm, 'c_call_id')->hiddenInput(['id' => 'c_call_id'])->label(false); ?>

                                <?php
                                if ($comForm->c_preview_email) {
                                    $js = <<<JS

    $('#modal-email-preview').modal('show');
    
    var isProcessing = false;
    var originContentSize = 0;
    
    if ('$emailMessageReadonly' == true) {
        function updateMessageInputVal() {
            let iframeEmail = document.getElementById('email_view');
            let contentEmail = iframeEmail.contentWindow.document.documentElement.outerHTML;
            if (originContentSize !== new Blob([iframeEmail.contentWindow.document.documentElement.outerHTML]).size) {
                $('#e_email_message_edited').attr('value', 1);
            } else {
                $('#e_email_message_edited').attr('value', 0);
            }
            $('#e_email_message').val(contentEmail);
        }
    }
    
    $('#email_view').on('load', function (e) {
        let iframeEmail = document.getElementById('email_view');
        originContentSize = new Blob([iframeEmail.contentWindow.document.documentElement.outerHTML]).size;
    });
    
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
        
        if (typeof updateMessageInputVal === "function") {
            updateMessageInputVal();
        }
        
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
            
            $('#c_sms_tpl_key').trigger('change');
            $('#c_email_tpl_key').trigger('change');
                    
            $('#sms-message').countSms('#sms-counter');
            $('#preview-sms-message').countSms('#preview-sms-counter');
            
        }            
    
        initializeMessageType($c_type_id);

var emails = '$unsubscribedEmails';
var emailTemplateTypes = '{$emailTemplateTypes}';
$('#email option').each(function() {             
    if (JSON.parse(emails).includes($(this).attr('value'))){                
        //$(this).attr('disabled', 'disabled');
        $(this).html($(this).html() + ' (unsubscribed)')
    }
    if ($(this).attr('value') == ""){
        $(this).html('---')
        //$(this).removeAttr('disabled')
    }
}); 
function initializeTemplateType(email, types) {
    let etpOptions = '<option>---</option>';      
        
        if (JSON.parse(emails).includes(email)){ 
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
}

initializeTemplateType($('#email').val(), emailTemplateTypes)           

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
            $.pjax.reload({url: currentUrl, container: '#<?= $pjaxContainerId ?>', push: false, replace: false, timeout: 6000, async: false});
            $.pjax.reload({url: currentUrl, container: '#quotes_list', push: false, replace: false, timeout: 6000, async: false});
        }

    </script>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.9.0/timer.jquery.min.js"></script>-->
<?php

$tpl_email_blank_key = CommunicationForm::TPL_TYPE_EMAIL_BLANK_KEY;
$tpl_sms_blank_key = CommunicationForm::TPL_TYPE_SMS_BLANK_KEY;

$projectId = $lead->project_id;
$project = $lead->project->name ?? '';


$js = <<<JS

    const tpl_email_blank_key = '$tpl_email_blank_key';
    const tpl_sms_blank_key = '$tpl_sms_blank_key';
    let projectId = '{$projectId}';
    let project = '{$project}';    

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
        
        let popup = $('#modal-email-view');
        
        $(".view-mail").replaceWith('<div id="mail_headers"><h6><div id="email_info" class="float-left" style="word-break: break-all; max-width: 100%;">' + subject + '<br>' + from + '<br>' + to + '<br>' +  date + files + '<br><br></div>' + '</h6><button id="print_button" title="Allow popups in your browser if this doesn`t work." data-toggle="mail_tooltip" class="btn btn-warning float-right"><i class="fa fa-print"></i> Print</button><div class="clearfix"></div><hr>' + '</div>'+ $(".view-mail").html() );
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
        w.document.body.style.maxWidth = "100%";
        w.document.body.style.wordBreak = "break-all";
        let js_timer = document.createElement("script");
        js_timer.innerHTML = 'setTimeout( "window.print(); window.close();", 3000);'; 
        w.document.head.append(js_timer);
        // window.document.addEventListener('DOMContentLoaded', function () { window.print(); window.close(); }, false);
    });
    
    $('body').on('change', '.quotes-uid', function() {
        
        let quoteList = [];
        let jsonQuotes = '';
        
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
    
     $('body').on('change', '.offer-checkbox', function() {
        
        let offerList = [];
        let jsonOffers = '';
        
        $('input[type=checkbox].offer-checkbox:checked').each(function() {
            offerList.push($(this).data('id'));
        });
        
        if (offerList.length === 0) {
            jsonOffers = '';
            
        } else {
            jsonOffers = JSON.stringify(offerList);
        }
        $('#c_offers').val(jsonOffers);
    });
        
    $(document).on('beforeSubmit', '#communication-form', function(e) {
        let btn = $('#preview_email_btn'),
            loaderInner = '<span class="spinner-border spinner-border-sm"></span> Loading';
        btn.html(loaderInner).prop('disabled', true);
    });
    
JS;

$this->registerJs($js);
