<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm \frontend\models\CommunicationForm
 * @var $leadForm \frontend\models\LeadForm
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

    <div class="panel chat">
        <div class="chat__list">

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,

                'options' => [
                    'tag' => 'div',
                    'class' => 'list-wrapper',
                    'id' => 'list-wrapper',
                ],
                'layout' => "{summary}\n{pager}\n{items}\n",
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

                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    //'action' => ['index'],
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                        'class' => 'panel-body',
                    ],
                ]);


                    $clientEmails = \yii\helpers\ArrayHelper::map($leadForm->getClientEmail(), 'email', 'email');
                    $clientEmails[Yii::$app->user->identity->email] = Yii::$app->user->identity->email;

                    $clientPhones = \yii\helpers\ArrayHelper::map($leadForm->getClientPhone(), 'phone', 'phone');

                    echo $form->errorSummary($comForm);

                ?>


                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <?= $form->field($comForm, 'c_type_id')->dropDownList(\frontend\models\CommunicationForm::TYPE_LIST, ['class' => 'form-control', 'id' => 'message-type']) ?>
                            <?//=$form->field($comForm, 'c_lead_id')->hiddenInput()->label(false); ?>
                        </div>

                        <div class="col-sm-3 form-group message-field-sms" id="sms-template-group">
                            <?= $form->field($comForm, 'c_sms_tpl_id')->dropDownList(\common\models\SmsTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'sms-template']) ?>
                        </div>

                        <div class="col-sm-3 form-group message-field-email" id="email-template-group" style="display: none;">
                            <?= $form->field($comForm, 'c_email_tpl_id')->dropDownList(\common\models\EmailTemplateType::getList(false), ['prompt' => '---', 'class' => 'form-control', 'id' => 'email-template']) ?>
                        </div>

                        <div class="col-sm-3 form-group message-field-sms message-field-email" id="language-group" style="display: block;">
                            <?= $form->field($comForm, 'c_language_id')->dropDownList(\lajax\translatemanager\models\Language::getLanguageNames(true), ['class' => 'form-control', 'id' => 'language']) ?>
                        </div>

                        <div class="col-sm-3 form-group message-field-email" id="email-address" style="display: none;">
                            <?= $form->field($comForm, 'c_email_to')->dropDownList($clientEmails, ['prompt' => '---', 'class' => 'form-control', 'id' => 'email']) ?>
                        </div>


                        <div class="col-sm-12 form-group message-field-email" id="email-subtitle-group" style="display: none;">
                            <?= $form->field($comForm, 'c_email_subject')->textInput(['class' => 'form-control', 'id' => 'email-subtitle', 'maxlength' => true]) ?>
                        </div>

                        <div class="col-sm-3 form-group message-field-phone message-field-sms" id="phone-numbers-group" style="display: block;">
                            <?= $form->field($comForm, 'c_phone_number')->dropDownList($clientPhones, ['prompt' => '---', 'class' => 'form-control', 'id' => 'phone']) ?>
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
                                    //'removePlugins' => 'elementspath',
                                ]
                            ]) ?>

                        </div>
                        <div class="btn-wrapper">
                            <?= Html::submitButton('<i class="fa fa-envelope-o"></i> Preview and Send Email', ['class' => 'btn btn-lg btn-primary']) ?>
                        </div>
                    </div>
                    <div class="chat__call call-box message-field-phone" id="call-box" style="display: none;">
                        <div class="call-box__interlocutor">
                            <div class="call-box__interlocutor-name">Mr. Chandi Fradita</div>
                            <div class="call-box__interlocutor-number">+1 880 770 88</div>
                        </div>
                        <div class="call-box__img call-box__img--waiting">
                            <?=Html::img('/img/user.png', ['class' => 'img-circle img-responsive', 'alt' => 'client'])?>
                        </div>
                        <div class="call-box__status call-box__status--waiting hidden">Connection ...</div>
                        <div class="call-box__status call-box__status--call"><i class="fa fa-clock"></i>&nbsp;<strong>2:05</strong></div>
                        <div class="call-box__btns">
                            <!--<button class="btn call-box__btn call-box__btn&#45;&#45;mute">-->
                            <!--<i class="fas fa-microphone-slash"></i>-->
                            <!--&lt;!&ndash;<i class="fas fa-microphone"></i>&ndash;&gt;-->
                            <!--</button>-->
                            <?= Html::button('<i class="fa fa-phone"></i>', ['class' => 'btn call-box__btn call-box__btn--call']) ?>
                            <?= Html::button('<i class="fa fa-pause"></i>', ['class' => 'btn call-box__btn call-box__btn--pause']) ?>
                        </div>
                    </div>

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

<?php
$js = <<<JS
   

    //$('input[type="tel"]').intlTelInput();

    //    Chat fields

    /*function initializeMessageType(messageType) {
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
        }
    }

    initializeMessageType($c_type_id);*/

    $('body').on("change", '#message-type', function () {
        initializeMessageType($(this).val());
    });
    
    /*$('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').on('click', function (e) {
        $('[data-toggle="popover"]').not(this).popover('hide');
    });*/

JS;

$this->registerJs($js);

