<?php

use frontend\widgets\newWebPhone\NewWebPhoneAsset;
use yii\helpers\Url;
use yii\web\View;

/* @var $userPhoneProject string */
/* @var $formattedPhoneProject string */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $this View */
/** @var array $userPhones */
/** @var array $userEmails */
/** @var bool $isCallRinging */
/** @var bool $isCallInProgress */
/** @var \common\models\Call|null $call */

NewWebPhoneAsset::register($this);
?>

<?= $this->render('partial/_phone_widget', [
	'showWidgetContent' => !empty($userPhoneProject),
	'userPhones' => $userPhones,
	'userEmails' => $userEmails,
	'userCallStatus' => $userCallStatus,
	'isCallRinging' => $isCallRinging,
	'isCallInProgress' => $isCallInProgress,
	'call' => $call
]) ?>
<?= $this->render('partial/_phone_widget_icon') ?>

<?php
$ajaxCheckUserForCallUrl = Url::to(['/phone/ajax-check-user-for-call']);
$ajaxBlackList = Url::to(['/phone/check-black-phone']);
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
							let params = {'To': phone_to, 'FromAgentPhone': phone_from, 'project_id': project_id, 'lead_id': lead_id, 'case_id': case_id, 'c_type': 'call-web', 'c_user_id': userId};
							webPhoneParams = params;
							let PhoneNumbersData = phoneNumbers.getPrimaryData.value ? phoneNumbers.getPrimaryData : phoneNumbers.getData;
							PhoneWidgetCall.initCall({from: PhoneNumbersData, to: data});
							createNotify('Calling', 'Calling ' + params.To + '...', 'success');
							connection = device.connect(params);
							updateAgentStatus(connection, false, 0);
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



