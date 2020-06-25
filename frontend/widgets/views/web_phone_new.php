<?php

use sales\model\call\services\currentQueueCalls\QueueCalls;
use frontend\widgets\newWebPhone\NewWebPhoneAsset;
use yii\helpers\Url;
use yii\web\View;

/* @var $userPhoneProject string */
/* @var $formattedPhoneProject string */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $this View */
/** @var array $userPhones */
/** @var array $userEmails */
/** @var int $countMissedCalls */

NewWebPhoneAsset::register($this);
?>

<?= $this->render('partial/_phone_widget', [
	'showWidgetContent' => !empty($userPhoneProject),
	'userPhones' => $userPhones,
	'userEmails' => $userEmails,
	'userCallStatus' => $userCallStatus,
	'countMissedCalls' => $countMissedCalls
]) ?>
<?= $this->render('partial/_phone_widget_icon') ?>

<?php
$ajaxCheckUserForCallUrl = Url::to(['/phone/ajax-check-user-for-call']);
$ajaxBlackList = Url::to(['/phone/check-black-phone']);
$ajaxCreateCallUrl = Url::to(['/phone/ajax-create-call']);

$conferenceBase = 0;
if (isset(Yii::$app->params['settings']['voip_conference_base'])) {
	$conferenceBase = Yii::$app->params['settings']['voip_conference_base'] ? 1 : 0;
}

$csrf_param = Yii::$app->request->csrfParam;
$csrf_token = Yii::$app->request->csrfToken;

?>

<?php

$js = <<<JS
	var data = JSON.parse('{$formattedPhoneProject}');
	window.phoneNumbers = toSelect($('.custom-phone-select'), data);


    $(document).on('click', '#btn-new-make-call', function(e) {
        e.preventDefault();
        
		let phone_to = $('#call-pane__dial-number').val();
		let case_id = $(this).attr('data-case-id') || null;
		let lead_id = $(this).attr('data-lead-id') || null;
		
		if (!phone_to) {
			new PNotify({title: "Phone Widget", type: "error", text: 'Phone number not entered', hide: true});
			return false;
		}
		
		var reg = new RegExp('^[+]?[0-9]{9,15}$');
		if (!reg.test(phone_to)) {
		    new PNotify({title: "Phone Widget", type: "error", text: 'Entered phone number is not correct. Phone number should contain only numbers and +', hide: true});
			return false;	
		}
		
        $.post('{$ajaxCheckUserForCallUrl}', {user_id: userId}, function(data) {
            
            if(data && data.is_ready) {
               let phone_from = phoneNumbers.getPrimaryData.value || phoneNumbers.getData.value;
               let project_id = phoneNumbers.getPrimaryData.projectId || phoneNumbers.getData.projectId;

                $.post('{$ajaxBlackList}', {phone: phone_to}, function(data) {
                    if (data.success) {
						if (device) {

							if (conferenceBase && callOutBackendSide) {
								let createCallParams = {
									'<?= $csrf_param ?>' : '<?= $csrf_token ?>',
									'called': phone_to, 
							        'from': phone_from, 
							        'project_id': project_id,
								};
								$.post('{$ajaxCreateCallUrl}', createCallParams, function(data) {
									if (data.error) {
										var text = 'Error. Try again later';
										if (data.message) {
											text = data.message;
										}
										new PNotify({title: "Make call", type: "error", text: text, hide: true});
									} else {
										console.log('webCall success');
									}
								}, 'json');								
							} else { 
								let params = {'To': phone_to, 'FromAgentPhone': phone_from, 'project_id': project_id, 'lead_id': lead_id, 'case_id': case_id, 'c_type': 'call-web', 'c_user_id': userId, 'is_conference_call': {$conferenceBase}};						
								webPhoneParams = params;
								let PhoneNumbersData = phoneNumbers.getPrimaryData.value ? phoneNumbers.getPrimaryData : phoneNumbers.getData;
								PhoneWidgetCall.requestOutgoingCall({  
									'callId': '',
									'callSid': '',
									'type': 'Outgoing',
									'status': 'Dialing',  
									'duration': 0,
									'projectName': PhoneNumbersData.project,
									'sourceName': '',
									'phone': data.phone,
									'name': data.callToName,
									'contact': {
									    'name': data.callToName
									}
								});
								createNotify('Calling', 'Calling ' + params.To + '...', 'success');
								updateAgentStatus(connection, false, 0);
								connection = device.connect(params);
							}
						}      
                    } else {
                        var text = 'Error. Try again later';
                        if (data.message) {
                            text = data.message;
                        }
                        new PNotify({title: "Make call", type: "error", text: text, hide: true});
                    }
                    
                    phoneNumbers.clearPrimaryData();
                }, 'json');
					
            } else {
								widgetIcon.update({
									type: 'incoming',
									timer: true,
									text: null,
									currentCalls: null,
									status: 'online',
									timerStamp: 0
								});
								alert('You have active call');
								$('.call-pane').removeClass('is_active');
								$('.call-pane-calling').addClass('is_active');
								$(".call-pane__call-btns").addClass("is-on-call");

                return false;
            }
        }, 'json');
        
    });
JS;
$this->registerJs($js);



