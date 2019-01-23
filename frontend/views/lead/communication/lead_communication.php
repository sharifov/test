<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm \frontend\models\CommunicationForm
 * @var $leadForm \frontend\models\LeadForm
 * @var $previewEmailForm \frontend\models\LeadPreviewEmailForm
 * @var $previewSmsForm \frontend\models\LeadPreviewSmsForm
 *
 */

use yii\helpers\Html;

/*if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}*/

$c_type_id = $comForm->c_type_id;
?>





<?/*php $form = Form::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]);*/ ?>

  <?php /*  <div class="row">
    <div class="col-sm-3 mail_list_column">

<?= Html::beginForm(\yii\helpers\Url::current(['email_type_id' => null, 'email_project_id' => null, 'action' => null]), 'GET', ['data-pjax' => 1]) ?>
    <div class="col-md-3">
        <?=Html::a('<i class="fa fa-envelope"></i> Create NEW', \yii\helpers\Url::current(['id' => null, 'reply_id' => null, 'edit_id' => null, 'action' => 'new']), ['class' => 'btn btn-sm btn-success'])?>
    </div>
    <div class="col-md-4">
        <?=Html::dropDownList('email_type_id', Yii::$app->request->get('email_type_id'), \common\models\Email::FILTER_TYPE_LIST, ['class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
        <?= Html::submitButton('Ok', ['id' => 'btn-submit-email', 'class' => 'btn btn-primary hidden']) ?>
    </div>
    <div class="col-md-5">
        <?=Html::dropDownList('email_project_id', Yii::$app->request->get('email_project_id'), \common\models\Project::getList(), ['prompt' => 'ALL', 'class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
    </div>
<?= Html::endForm() */ ?>


<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-communication' ,'enablePushState' => false]) ?>
    <?/*<h1><?=random_int(1, 100)?></h1>*/ ?>
    <div class="panel chat">
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
                    return $this->render('_list_item',['model' => $model, 'dataProvider' => $dataProvider]);
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



                        <?= $form2->field($previewEmailForm, 'e_lead_id')->hiddenInput()->label(false); ?>
                        <?= $form2->field($previewEmailForm, 'e_language_id')->hiddenInput()->label(false); ?>
                        <?= $form2->field($previewEmailForm, 'e_email_tpl_id')->hiddenInput()->label(false); ?>
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
                <div class="btn-wrapper">
                    <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
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
                        <div class="col-sm-6 form-group">
                            <?= $form3->field($previewSmsForm, 's_phone_from')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                            <?//= $form3->field($previewSmsForm, 's_lead_id')->hiddenInput()->label(false); ?>
                            <?= $form3->field($previewSmsForm, 's_language_id')->hiddenInput()->label(false); ?>
                            <?= $form3->field($previewSmsForm, 's_sms_tpl_id')->hiddenInput()->label(false); ?>
                        </div>
                        <div class="col-sm-6 form-group">
                            <?= $form3->field($previewSmsForm, 's_phone_to')->textInput(['class' => 'form-control', 'maxlength' => true, 'readonly' => true]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= $form3->field($previewSmsForm, 's_sms_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'email-message']) ?>
                    </div>
                    <div class="btn-wrapper">
                        <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send SMS', ['class' => 'btn btn-lg btn-primary']) ?>
                    </div>

                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                <?php \yii\bootstrap\Modal::end()?>





                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    //'action' => ['index'],
                    'id' => 'communication-form',
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                        'class' => 'panel-body',
                    ],
                ]);


                    $clientEmails = \yii\helpers\ArrayHelper::map($leadForm->getClientEmail(), 'email', 'email');
                    $clientEmails[Yii::$app->user->identity->email] = Yii::$app->user->identity->email;

                    $clientPhones = \yii\helpers\ArrayHelper::map($leadForm->getClientPhone(), 'phone', 'phone');

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
                                $agentParams = \common\models\UserProjectParams::find()->where(['upp_project_id' => $leadForm->getLead()->project_id, 'upp_user_id' => Yii::$app->user->id])->limit(1)->one();

                                //\yii\helpers\VarDumper::dump($leadForm->getLead()->id, 10, true); exit;

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

                                        if ($tk == \frontend\models\CommunicationForm::TYPE_VOICE) {

                                            if ($agentParams->upp_tw_sip_id) {
                                                $typeList[$tk] = $itemName . ' (' . $agentParams->upp_tw_sip_id . ')';
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
                            <?= $form->field($comForm, 'c_email_tpl_id')->dropDownList(\common\models\EmailTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'c_email_tpl_id']) ?>
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
                        <div class="form-group">
                            <?= $form->field($comForm, 'c_sms_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'sms-message']) ?>
                        </div>
                        <div class="btn-wrapper">
                            <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Send SMS', ['class' => 'btn btn-lg btn-primary']) ?>
                        </div>
                    </div>
                    <div id="email-input-box" class="message-field-email" style="display: none;">
                        <div class="form-group">
                            <?//= $form->field($comForm, 'c_email_message')->textarea(['rows' => 4, 'class' => 'form-control', 'id' => 'email-message']) ?>

                            <?= $form->field($comForm, 'c_email_message')->widget(\dosamigos\ckeditor\CKEditor::class, [
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
                        <div class="btn-wrapper">
                            <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Preview and Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
                        </div>
                    </div>
                    <div class="chat__call call-box message-field-phone" id="call-box" style="display: none;">

                        <div class="call-box__interlocutor">
                            <div class="call-box__interlocutor-name"><?php echo Html::encode($leadForm->getClient()->first_name.' ' . $leadForm->getClient()->last_name); ?></div>
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
                        <div class="call-box__btns">
                            <?//= Html::button('<i class="fa fa-microphone-slash"></i>', ['class' => 'btn call-box__btn call-box__btn--mute']) ?>
                            <?= Html::submitButton('<i class="fa fa-phone"></i>', ['class' => 'btn call-box__btn call-box__btn--call', 'id' => 'btn-start-call', 'disabled' => ($comForm->c_voice_status == 1 ? true : false), 'onclick' => '$("#c_voice_status").val(1)']) ?>
                            <?/*= Html::button('<i class="fa fa-pause"></i>', ['class' => 'btn call-box__btn call-box__btn--pause', 'disabled' => true, 'id' => 'btn-pause'])*/ ?>
                            <?= Html::submitButton('<i class="fa fa-stop"></i>', ['class' => 'btn call-box__btn call-box__btn--stop', 'disabled' => $comForm->c_voice_status == 1 ? false : true, 'id' => 'btn-stop-call', 'onclick' => '$("#c_voice_status").val(2)']) ?>
                        </div>
                    </div>

                    <?= $form2->field($comForm, 'c_voice_status')->hiddenInput(['id' => 'c_voice_status'])->label(false); ?>


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
    }
    
          
    

    initializeMessageType($c_type_id);
    

JS;

$this->registerJs($js);
?>






                <?php \yii\bootstrap\ActiveForm::end(); ?>

            </div>
        </div>
    </div>

<?php yii\widgets\Pjax::end() ?>


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
    var currentUrl = '<?=$currentUrl?>';

    function updateCommunication() {
        $.pjax.reload({url: currentUrl, container: '#pjax-lead-communication', push: false, replace: false, timeout: 6000});
    }
</script>


<?php



$js = <<<JS
    $('body').on("change", '#c_type_id', function () {
        initializeMessageType($(this).val());
    });

    $('body').on("change", '#c_phone_number', function () {
        $('#div-call-phone-number').text($(this).val());
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

JS;

$this->registerJs($js);

