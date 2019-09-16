<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm \frontend\models\CaseCommunicationForm
 * @var $model \sales\entities\cases\Cases
 * @var $previewEmailForm \frontend\models\CasePreviewEmailForm
 * @var $previewSmsForm \frontend\models\CasePreviewSmsForm
 * @var $isAdmin bool
 *
 */

use yii\helpers\Html;
$c_type_id = $comForm->c_type_id;

// <script src="https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.9.0/timer.jquery.min.js"></script>

?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-comments"></i> Communication</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
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
    <?php yii\widgets\Pjax::begin(['id' => 'pjax-case-communication' ,'enablePushState' => false]) ?>
        <?/*<h1><?=random_int(1, 100)?></h1>*/ ?>
        <div class="panel">
            <div class="chat__list">

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,

                    'options' => [
                        'tag' => 'div',
                        'class' => 'list-wrapper',
                        'id' => 'list-wrapper',
                    ],
                    'emptyText' => '<div class="text-center">Not found communication messages</div><br>',
                    'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                    'itemView' => function ($model, $key, $index, $widget) use ($dataProvider) {
                        return $this->render('/lead/communication/_list_item',['model' => $model, 'dataProvider' => $dataProvider]);
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

                <?php if($model->isProcessing()):?>
                     <div class="chat__form panel">



                    <?php \yii\bootstrap\Modal::begin(['id' => 'modal-email-preview',
                        'header' => '<h2>Email preview</h2>',
                        'size' => \yii\bootstrap\Modal::SIZE_LARGE
                    ])?>

                    <?php $form2 = \yii\bootstrap\ActiveForm::begin([
                        //'action' => ['index'],
                        //'id' => 'email-preview-form',
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
                    <div class="form-group">
                        <?= $form2->field($previewEmailForm, 'e_email_message')->widget(\dosamigos\ckeditor\CKEditor::class, [
                            'options' => [
                                'rows' => 6,
                                'readonly' => false
                            ],
                            'preset' => 'custom',
                            'clientOptions' => [
                                'height' => 500,
                                'fullPage' => true,

                                'allowedContent' => true,
                                'resize_enabled' => false,
                                'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
                                'removePlugins' => 'elementspath',
                            ]
                        ]) ?>
                    </div>
                    <?php if($isAdmin):?>
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
                            <?php if($messageSize > 102 * 1024): ?>
                                    &nbsp;&nbsp;&nbsp;<span class="danger">Warning: recommended MAX content size: <b><?=Yii::$app->formatter->asShortSize(102 * 1024, 1) ?></b>.</span>
                            <?php endif; ?>

                            <hr>
                        </div>
                    </div>

                    <div class="btn-wrapper text-right">
                        <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
                        <?php if($isAdmin):?>
                            <?= Html::button('<i class="fa fa-list"></i> Show Email data (for Admins)', ['class' => 'btn btn-lg btn-warning', 'onclick' => '$("#email-data-content-div").toggle()']) ?>
                        <?php endif; ?>
                    </div>
                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                    <?php \yii\bootstrap\Modal::end()?>






                    <?php \yii\bootstrap\Modal::begin(['id' => 'modal-sms-preview',
                        'header' => '<h2>SMS preview</h2>',
                        'size' => \yii\bootstrap\Modal::SIZE_DEFAULT
                    ])?>

                        <?php $form3 = \yii\bootstrap\ActiveForm::begin([
                                //'action' => ['index'],
                                //'id' => 'email-preview-form',
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
                                <?//= $form3->field($previewSmsForm, 's_case_id')->hiddenInput()->label(false); ?>
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

                    <?php \yii\bootstrap\Modal::end()?>





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
                        $clientPhones = $model->client ? $model->client->getPhoneNumbersSms() : [];

                        if(Yii::$app->session->hasFlash('send-success')) {
                            echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            echo Yii::$app->session->getFlash('send-success');
                            echo '</div>';
                        }

                        if(Yii::$app->session->hasFlash('sms-send-success')) {
                            echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            echo Yii::$app->session->getFlash('sms-send-success');
                            echo '</div>';
                        }

                        if(Yii::$app->session->hasFlash('send-error')) {
                            echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            echo Yii::$app->session->getFlash('send-error');
                            echo '</div>';
                        }

                        echo $form->errorSummary($comForm);

                    ?>


                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <?php
                                    $typeList = [];
                                    $agentParams = \common\models\UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->limit(1)->one();

                                    /** @var \common\models\Employee $userModel */
                                    $userModel = Yii::$app->user->identity;


                                    $call_type = \common\models\UserProfile::find()->select('up_call_type_id')->where(['up_user_id' => Yii::$app->user->id])->one();


                                    if($call_type && $call_type->up_call_type_id) {
                                        $call_type_id = $call_type->up_call_type_id;

                                    } else {
                                        $call_type_id = \common\models\UserProfile::CALL_TYPE_OFF;
                                    }


                                    if($agentParams) {
                                        foreach (\frontend\models\CommunicationForm::TYPE_LIST as $tk => $itemName) {

                                            if ($tk == \frontend\models\CommunicationForm::TYPE_EMAIL) {

                                                if ($agentParams->upp_email) {
                                                    $typeList[$tk] = $itemName . ' (' . $agentParams->upp_email . ')';
                                                }
                                            }

                                            if ($tk == \frontend\models\CommunicationForm::TYPE_SMS) {

                                                if ($agentParams->upp_tw_phone_number) {
                                                    $typeList[$tk] = $itemName . ' (' . $agentParams->upp_tw_phone_number . ')';
                                                }
                                            }


                                            if($call_type_id) {

                                                $callTypeName = \common\models\UserProfile::CALL_TYPE_LIST[$call_type_id] ?? '-';

                                                if($call_type_id == \common\models\UserProfile::CALL_TYPE_SIP && $userModel->userProfile && !$userModel->userProfile->up_sip) {
                                                    $callTypeName .= ' [empty account]';
                                                }

                                                if ($tk == \frontend\models\CommunicationForm::TYPE_VOICE) {
                                                    //if ($userModel->userProfile->up_sip) {
                                                        $typeList[$tk] = $itemName . ' ('.$callTypeName.')';
                                                    //}
                                                }
                                            }
                                        }
                                    }

                                ?>


                                <?= $form->field($comForm, 'c_type_id')->dropDownList($typeList, ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_type_id']) ?>
                                <?= $form->field($comForm, 'c_quotes')->hiddenInput(['id' => 'c_quotes'])->label(false); ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-sms" id="sms-template-group">
                                <?= $form->field($comForm, 'c_sms_tpl_id')->dropDownList(\common\models\SmsTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_sms_tpl_id']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-email" id="email-template-group" style="display: none;">
                                <?= $form->field($comForm, 'c_email_tpl_id')->dropDownList(\common\models\EmailTemplateType::getList(false, $model->cs_dep_id), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_tpl_id']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-sms message-field-email" id="language-group" style="display: block;">
                                <?= $form->field($comForm, 'c_language_id')->dropDownList(\lajax\translatemanager\models\Language::getLanguageNames(true), ['prompt' => '---', 'class' => 'form-control', 'id' => 'language']) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-email" id="email-address" style="display: none;">
                                <?= $form->field($comForm, 'c_email_to')->dropDownList($clientEmails, ['prompt' => '---', 'class' => 'form-control', 'id' => 'email']) ?>
                            </div>


                            <div class="col-sm-12 form-group message-field-email" id="email-subtitle-group" style="display: none;">
                                <?= $form->field($comForm, 'c_email_subject')->textInput(['class' => 'form-control', 'id' => 'email-subtitle', 'maxlength' => true]) ?>
                            </div>

                            <div class="col-sm-3 form-group message-field-phone message-field-sms" id="phone-numbers-group" style="display: block;">
                                <?= $form->field($comForm, 'c_phone_number')->dropDownList($clientPhones, ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_phone_number']) ?>
                            </div>
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
                                <?//= $form->field($comForm, 'c_email_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'email-message']) ?>

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
                                <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Preview and Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
                            </div>
                        </div>
                        <div class="chat__call call-box message-field-phone" id="call-box" style="display: none;">

                            <div class="call-box__interlocutor">
                                <div class="call-box__interlocutor-name"><?= $model->client ? Html::encode($model->client->full_name) : '' ?></div>
                                <div class="call-box__interlocutor-number" id="div-call-phone-number"><?=$comForm->c_phone_number?></div>
                            </div>
                            <div class="call-box__img <?=$comForm->c_voice_status == 1 ? 'call-box__img--waiting':''?>" id="div-call-img">
                                <?=Html::img('/img/user.png', ['class' => 'img-circle img-responsive', 'alt' => 'client'])?>
                            </div>

                                <div class="call-box__status call-box__status--waiting" style="display: block" id="div-call-message">
                                    <?php if($comForm->c_voice_status == 0):?>
                                        Waiting
                                    <?php endif;?>
                                    <?php if($comForm->c_voice_status == 1):?>
                                        Connection ... <?=$comForm->c_voice_sid?>
                                    <?php endif;?>
                                    <?php if($comForm->c_voice_status == 2):?>
                                        Canceled Call
                                    <?php endif;?>
                                    <?php if($comForm->c_voice_status == 5):?>
                                        Error Call
                                    <?php endif;?>
                                </div>
                            <?php if($comForm->c_voice_status == 1):?>
                                <div class="call-box__status call-box__status--call" style="display: block" id="div-call-time"><i class="fa fa-clock-o"></i>&nbsp;<strong id="div-call-timer">00:00</strong></div>
                            <?php endif;?>



                            <?php if($call_type_id == \common\models\UserProfile::CALL_TYPE_WEB): ?>
                                <div class="call-box__btns">

                                    <?= Html::a('<i class="fa fa-phone"></i>', '#', ['class' => 'btn call-box__btn call-box__btn--call', 'id' => 'btn-start-web-call',
                                        'data-project-id' => $model->cs_project_id,
                                        'data-case-id' => $model->cs_id,
                                        'disabled' => $comForm->c_voice_status == 1 ? true : false
                                    ]) ?>

                                    <?/*<a href="#" class="call-phone" data-project-id="6" data-case-id="92138" data-phone="+37369594567">+37369594567</a> - Alex <br/>*/?>

                                    <?//= Html::button('<i class="fa fa-microphone-slash"></i>', ['class' => 'btn call-box__btn call-box__btn--mute']) ?>
                                    <?/*= Html::button('<i class="fa fa-pause"></i>', ['class' => 'btn call-box__btn call-box__btn--pause', 'disabled' => true, 'id' => 'btn-pause'])*/ ?>
                                </div>
                            <? else: ?>
                                <div class="call-box__btns">
                                    <?= Html::submitButton('<i class="fa fa-phone"></i>', ['class' => 'btn call-box__btn call-box__btn--call', 'id' => 'btn-start-call', 'disabled' => ($comForm->c_voice_status == 1 ? true : false), 'onclick' => '$("#c_voice_status").val(1)']) ?>
                                    <?= Html::submitButton('<i class="fa fa-stop"></i>', ['class' => 'btn call-box__btn call-box__btn--stop', 'disabled' => $comForm->c_voice_status == 1 ? false : true, 'id' => 'btn-stop-call', 'onclick' => '$("#c_voice_status").val(2)']) ?>
                                </div>
                            <? endif; ?>
                        </div>

                        <?= $form2->field($comForm, 'c_voice_status')->hiddenInput(['id' => 'c_voice_status'])->label(false); ?>
                        <?= $form2->field($comForm, 'c_voice_sid')->hiddenInput(['id' => 'c_voice_sid'])->label(false); ?>
                        <?= $form2->field($comForm, 'c_call_id')->hiddenInput(['id' => 'c_call_id'])->label(false); ?>



                    <?/*php if($comForm->c_voice_status === 1):?>
                        <?php
                        $js = "
                            var previewPopup = $('#modal-email-preview');
                            //previewPopup.find('.modal-body').html(data);
                            previewPopup.modal('show');";

                        $this->registerJs($js);

                        ?>
                    <?php endif; */?>


                    <?php if($comForm->c_preview_email):?>
                        <?php
                            $js = "
                            var previewPopup = $('#modal-email-preview');
                            //previewPopup.find('.modal-body').html(data);
                            previewPopup.modal('show');";

                            $this->registerJs($js);

                        ?>
                    <?php endif; ?>



                    <?php if($comForm->c_preview_sms):?>
                        <?php
                        $js = "
                            var previewPopup = $('#modal-sms-preview');
                            //previewPopup.find('.modal-body').html(data);
                            previewPopup.modal('show');";

                        $this->registerJs($js);
                        ?>
                    <?php endif; ?>




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
            }
            
            $('#c_sms_tpl_id').trigger('change');
            $('#c_email_tpl_id').trigger('change');
                    
            $('#sms-message').countSms('#sms-counter');
            $('#preview-sms-message').countSms('#preview-sms-counter');
            
        }
            
    
        initializeMessageType($c_type_id);
        

JS;

    $this->registerJs($js);
    ?>






                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                </div>
                <?php endif; ?>

            </div>
        </div>

    <?php yii\widgets\Pjax::end() ?>
    </div>
</div>


<?php \yii\bootstrap\Modal::begin(['id' => 'modal-email-view',
    'header' => '<h2>Email view</h2>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE
])?>
    <div class="view-mail">
        <object id="object-email-view" width="100%" height="800" data=""></object>
    </div>
<?php \yii\bootstrap\Modal::end()?>


<?php
    $currentUrl = \yii\helpers\Url::current();
    $jsPath = Yii::$app->request->baseUrl.'/js/sounds/';
?>

<script>
    const currentUrl = '<?=$currentUrl?>';

    function updateCommunication() {
        $.pjax.reload({url: currentUrl, container: '#pjax-case-communication', push: false, replace: false, timeout: 6000});
    }

    function stopCall(duration) {
        $('#div-call-img').removeClass('call-box__img--waiting');
        //$('#div-call-message').hide();
        //$('#div-call-time').hide();
        $('#btn-start-call').attr('disabled', false);
        $('#btn-stop-call').attr('disabled', true);
        stopCallTimer(duration);
    }


    function startCall() {

        $('#div-call-img').addClass('call-box__img--waiting');
        $('#div-call-message').show();
        $('#div-call-time').show();
        $('#btn-start-call').attr('disabled', true);
        $('#btn-stop-call').attr('disabled', false);

        //startCallTimer();
    }


    function callUpdate(obj) {
        console.log(obj);
        //status: "completed", duration: "1", snr: "3"
        $('#div-call-message').html(obj.snr + ' - ' + obj.status);

        if(obj.status == 'completed') {
            stopCall(obj.duration); //updateCommunication();
            updateCommunication();
        } else if(obj.status == 'in-progress') {
            startCallTimer();
            //$('#div-call-timer').timer('resume');
        } else if(obj.status == 'initiated') {
            startCall();
        } else if(obj.status == 'busy') {
            stopCall(0);
            updateCommunication();
        } else if(obj.status == 'no-answer') {
            stopCall(0);
            updateCommunication();
        }
    }

    function startCallTimer() {
        $('#div-call-timer').timer('remove');
        $('#div-call-timer').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    function stopCallTimer(sec) {
        $('#div-call-timer').timer('remove');
        $('#div-call-timer').timer({format: '%M:%S', seconds: sec}).timer('pause');
    }


</script>

<?php
\yii\bootstrap\Modal::begin([
    'header' => '<b>Call Recording</b>',
    // 'toggleButton' => ['label' => 'click me'],
    'id' => 'modalCallRecording',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
]);
?>
    <div class="row">
        <div class="col-md-12" id="audio_recording">

        </div>
    </div>
<?php \yii\bootstrap\Modal::end(); ?>

<?php


$tpl_email_blank_id = \frontend\models\CommunicationForm::TPL_TYPE_EMAIL_BLANK;


$js = <<<JS

    const tpl_email_blank_id = '$tpl_email_blank_id';

    $('body').on("change", '#c_type_id', function () {
        initializeMessageType($(this).val());
    });

    $('body').on("change", '#c_phone_number', function () {
        $('#div-call-phone-number').text($(this).val());
    });
    
    $('body').on("change", '#c_sms_tpl_id', function () {
        if($(this).val() == 2) {
            $('#sms-textarea-div').hide();
        } else {
            $('#sms-textarea-div').show();
        }
    });
    
    $('body').on('click', '#btn-start-web-call', function(e) {
        
        var phone_number = $('#c_phone_number').val();
        var project_id = $(this).data('project-id');
        var case_id = $(this).data('case-id');
        
        //alert(phoneNumber);
        
        e.preventDefault();
        
        if(phone_number) {
        
            $('#web-phone-dial-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
            $('#web-phone-dial-modal').modal();
            
            $.post(ajaxPhoneDialUrl, {'phone_number': phone_number, 'project_id': project_id, 'case_id': case_id},
                function (data) {
                    $('#web-phone-dial-modal .modal-body').html(data);
                }
            );
        } else {
            alert('Warning: Select client phone number');
        }
        
    });
    
    $('body').on("change", '#c_email_tpl_id', function () {
                
        var type_id = $('#c_type_id').val();
        
        if(type_id != tpl_email_blank_id) {
            if($(this).val() != tpl_email_blank_id) {
                $('#email-textarea-div').hide();
                $('#email-subtitle-group').hide();
            } else {
                $('#email-textarea-div').show();
                $('#email-subtitle-group').show();
            }
        }
    });


    $('body').on('click', '.chat__details', function () {
        var id = $(this).data('id');
        $('#object-email-view').attr('data', '/email/view?id='+id+'&preview=1');
        var popup = $('#modal-email-view');
        //previewPopup.find('.modal-body').html(data);
        popup.modal('show');
        return false;
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
    
       
    //startCallTimer();
    
    /*$('body').on('click', '#btn-start-call', function() {
        $('#div-call-img').addClass('call-box__img--waiting');
        $('#div-call-message').show();
        $('#div-call-time').show();
        $(this).attr('disabled', true);
        $('#btn-stop-call').attr('disabled', false);
        
    });
    
    $('body').on('click', '#btn-stop-call', function() {
        $('#div-call-img').removeClass('call-box__img--waiting');
        
        $('#div-call-message').hide();
        $('#div-call-time').hide();
        $(this).attr('disabled', true);
        $('#btn-start-call').attr('disabled', false);
    });*/
    
    
    
    
    /*$('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').on('click', function (e) {
        $('[data-toggle="popover"]').not(this).popover('hide');
    });*/
    
    $(document).on('click', '.btn-recording_url', function() {
         var source_src = $(this).data('source_src');
         $('#audio_recording').html('<audio controls="controls" controlsList="nodownload" autoplay="true" id="audio_controls" style="width: 100%;"><source src="'+ source_src +'" type="audio/mpeg"></audio>');
         $('#modalCallRecording').modal('show');
    });
    
    $('#modalCallRecording').on('hidden.bs.modal', function () {
        $('#audio_recording').html('');
    });

JS;

$this->registerJs($js);

