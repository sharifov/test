<?php

use frontend\widgets\newWebPhone\NewWebPhoneAsset;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\web\View;

/* @var $formattedPhoneProject string */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $this View */
/** @var array $userPhones */
/** @var array $userEmails */
/** @var int $countMissedCalls */

NewWebPhoneAsset::register($this);

Modal::begin([
    'id' => 'call-box-modal',
    'title' => '',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_LARGE
]);

Modal::end();

?>

<?= $this->render('partial/_phone_widget', [
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
$createInternalCallUrl = Url::to(['/phone/create-internal-call']);
$getUserByPhoneUrl = Url::to(['/phone/get-user-by-phone']);
$getCallHistoryFromNumberUrl = Url::to(['/phone/get-call-history-from-number']);
$ajaxCheckRecording = Url::to(['/phone/ajax-check-recording']);

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

/*
	let dialData = {
	    'user_id': '',
	    'to': '',
	    'from': '',
	    'project_id': '',
	    'department_id': '',
	    'client_id': '',
	    'source_type_id': '',
	    'lead_id': '',
	    'case_id': ''	    ,
	    'is_conference_call': conferenceBase,
	    'nickname': '',
	};
*/

	 $(document).on('click', '#btn-new-make-call', function(e) {
        e.preventDefault();
        makeCallFromPhoneWidget(); 
	 });

	 $(document).on('click', '.phone-dial-history', function(e) {
        e.preventDefault();
       
        let data = $(this);
		let isInternal = !!data.data('user-id');
		$(".widget-phone__contact-info-modal").hide();
		$('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
		$('.phone-widget__tab').removeClass('is_active');
		$('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
		$('#tab-phone').addClass('is_active');
				
		insertPhoneNumber({
			'formatted': data.data('phone'),
			'title': isInternal ? '' : data.data('title'),
			'user_id': data.data('user-id'),
			'phone_to': data.data('phone'),
			'is_request_from': !isInternal ? true : '',
			'request_call_sid': !isInternal ? data.data('call-sid') : '',
			'project_id': data.data('project-id'),
			'department_id': data.data('department-id'),
			'client_id': data.data('client-id'),
			'source_type_id': data.data('source-type-id'),
			'lead_id': data.data('lead-id'),
			'case_id': data.data('case-id'),
		});
	 });

	 $(document).on('click', '#btn-make-call-communication-block', function(e) {
        e.preventDefault();
        
        let to = $('#call-to-number').val();
        let from = $('#call-from-number').val();        		
        
        if (!to) {
            createNotify('Make call', 'Please select Phone number', 'error');
            return false;
        }
        
        if (!from) {
            createNotify('Make call', 'Please select Phone from', 'error');
            return false;
        }
        
        insertPhoneNumber({
                'formatted': to,
                'title': $('#call-client-name').val(),
                'user_id': '',
                'phone_to': to,
                'phone_from': from,
                'project_id': $('#call-project-id').val(),
                'department_id': $('#call-department-id').val(),
                'client_id': $('#call-client-id').val(),
                'source_type_id': $('#call-source-type-id').val(),
                'lead_id': $('#call-lead-id').val(),
                'case_id': $('#call-case-id').val(),
        });
        
        $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
        $('.phone-widget__tab').removeClass('is_active');
        $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
        $('#tab-phone').addClass('is_active');
        $('.phone-widget').addClass('is_active');
	 	
        reserveDialButton();
	 	makeCallFromPhoneWidget();
	 });
	 
	 function makeCallFromPhoneWidget() {
	    let value = $('#call-pane__dial-number-value');
	    let to = $('#call-pane__dial-number').val();
        let data = {
            'user_id': value.attr('data-user-id'),
            'from': value.attr('data-phone-from') || (phoneNumbers.getPrimaryData.value || phoneNumbers.getData.value),
            'is_request_from': value.attr('data-is-request-from'),
            'request_call_sid': value.attr('data-request-call-sid'),
			'to': to,
			'project_id': value.attr('data-project-id') || (phoneNumbers.getPrimaryData.projectId || phoneNumbers.getData.projectId),
			'department_id': value.attr('data-department-id'),
			'client_id': value.attr('data-client-id'),
			'source_type_id': value.attr('data-source-type-id'),
			'lead_id': value.attr('data-lead-id'),
			'case_id': value.attr('data-case-id')
        };
                
        if (data.user_id) {
            let nickname = $('#call-to-label').html();
            if (!nickname) {
             	nickname = to;
            }
            data.nickname = nickname;
        }
        
		createCall(data);
	 }

	function createCall(data) {
	     if (data.user_id) {
            reserveDialButton();
            createInternalCall(data.user_id, data.nickname);
            return false;
        }

        if (!data.to) {
            freeDialButton();
			new PNotify({title: "Create call", type: "error", text: 'Phone number not entered', hide: true});
			return false;
		}

		if (!(new RegExp('^[+]{1}[0-9]{9,15}$')).test(data.to)) {
		    freeDialButton();
		    new PNotify({title: "Create call", type: "error", text: 'Entered phone number is not correct. Phone number should contain only numbers and +', hide: true});
			return false;	
		}	

	    reserveDialButton();

	    $.ajax({
			type: 'post',
			data: {
				'phone': data.to
			},
			url: '{$getUserByPhoneUrl}'
		 })
			.done(function (result) {
				if (result.error) {
					createNotify('Create Call', result.message, 'error');
					freeDialButton();
					return false;
				}
				if (result.userId) {
					 createInternalCall(result.userId, result.nickname);
					 return false;
				}
				prepareExternalCall(data);
			})
			.fail(function () {
				createNotify('Create Call', 'Server error', 'error');
				freeDialButton();
			});
	}
	
	function prepareExternalCall(data) {
	     if (data.is_request_from && data.is_request_from === 'true') {
			$.ajax({
				type: 'post',
				data: {
					'sid': data.request_call_sid
				},
				url: '{$getCallHistoryFromNumberUrl}'
			})
			.done(function (result) {
				if (result.error) {
					createNotify('Create Call', result.message, 'error');
					freeDialButton();
					return false;
				}
				data.from = result.phone;
				createExternalCall(data);			
			})
			.fail(function () {
				createNotify('Create Call', 'Server error', 'error');
				freeDialButton();
			});
			return false;
	     }
	   
	  	createExternalCall(data);
	}

    function createExternalCall(dialData) {
	    		
	    if (!dialData.from) {
	        createNotify('Make call', 'Not found From phone', 'error');
	        freeDialButton();
	        return false;
        }
	     
        $.post('{$ajaxCheckUserForCallUrl}', {user_id: userId}, function(data) {
            
            if(data && data.is_ready) {

                $.post('{$ajaxBlackList}', {phone: dialData.to}, function(data) {
                    if (data.success) {
                        
                        $.post('{$ajaxCheckRecording}', 
                            {
                                'toPhone': filterCallParams(dialData.to),
                                'fromPhone': dialData.from,
                                'projectId': dialData.project_id,
                                'departmentId': dialData.department_id,
                                'contactId': dialData.client_id
                            }, function(dataRecording) {
                        
                            if (dataRecording.success) {
                                
                                if (device) {
        
                                    if (conferenceBase && callOutBackendSide) {
                                        let createCallParams = {
                                            '{$csrf_param}' : '{$csrf_token}',
                                            'called': dialData.to, 
                                            'from': dialData.from, 
                                            'project_id': dialData.project_id,
                                        };
                                        $.post('{$ajaxCreateCallUrl}', createCallParams, function(data) {
                                            if (data.error) {
                                                freeDialButton();
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
                                        $.post(ajaxGetPhoneListIdUrl, {'phone': filterCallParams(dialData.from)}, function(data) {
                                            if (data.error) {
                                                var text = 'Error. Try again later';
                                                if (data.message) {
                                                    text = data.message;
                                                }
                                                new PNotify({title: "Make call", type: "error", text: text, hide: true});
                                                freeDialButton();
                                            } else {
                                                let params = {
                                                    'To': filterCallParams(dialData.to), 
                                                    'FromAgentPhone': filterCallParams(dialData.from), 
                                                    'c_project_id': filterCallParams(dialData.project_id),
                                                    'c_dep_id': filterCallParams(dialData.department_id),
                                                    'lead_id': filterCallParams(dialData.lead_id), 
                                                    'case_id': filterCallParams(dialData.case_id), 
                                                    'c_type': 'call-web', 
                                                    'c_user_id': userId, 
                                                    'user_identity': window.userIdentity, 
                                                    'is_conference_call': conferenceBase,
                                                    'c_client_id': filterCallParams(dialData.client_id),
                                                    'c_source_type_id': filterCallParams(dialData.source_type_id),
                                                    'call_recording_disabled': dataRecording.value,
                                                    'phone_list_id': data.phone_list_id
                                                };
                                                console.log('create call with params:');
                                                console.log(params);
                                                // createNotify('Calling', 'Calling ' + params.To + '...', 'success');
                                                connection = device.connect(params);
                                            }
                                        }, 'json');
                                    }
                                } else {
                                    freeDialButton();
                                }
                            } else {
                                freeDialButton();
                                var text = 'Error. Try again later';
                                if (dataRecording.message) {
                                    text = dataRecording.message;
                                }
                                new PNotify({title: "Make call", type: "error", text: text, hide: true});
                            } 
						}, 'json');
                        
                    } else {
                        freeDialButton();
                        var text = 'Error. Try again later';
                        if (data.message) {
                            text = data.message;
                        }
                        new PNotify({title: "Make call", type: "error", text: text, hide: true});
                    }
                    
                    phoneNumbers.clearPrimaryData();
                }, 'json');
					
            } else {
                if (data && data.is_on_call === true) {
                    freeDialButton();
				    window.sendCommandUpdatePhoneWidgetCurrentCalls(null, userId, window.generalLinePriorityIsEnabled);
				    alert('New Call Error: You have an active call. If the message is shown by mistake please contact Administrator.');
                }
                if (data && data.is_offline === true) {
                    alert('You status is offline.');
                }
                return false;
            }
        }, 'json');
    }
    
    function filterCallParams(param) {
        if (typeof param === 'undefined') {
            return '';
        }
        return param;
    }
    
    function createInternalCall(toUserId, nickname) {
        // createNotify('Calling', 'Calling ' + nickname + ' ...', 'success');
        $.ajax({
                type: 'post',
                data: {
                    'user_id': toUserId
                },
                url: '{$createInternalCallUrl}'
            })
			.done(function (data) {
				if (data.error) {
					createNotify('Create Internal Call', data.message, 'error');
					freeDialButton();
				}
			})
			.fail(function () {
				createNotify('Create Internal Call', 'Server error', 'error');
				freeDialButton();
			})
    }
JS;
$this->registerJs($js);
