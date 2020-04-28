<?php

use frontend\assets\NewWebPhoneAsset;
use yii\helpers\Url;
use yii\web\View;

/* @var $phoneFrom string */
/* @var $projectId int */
/* @var $this View */

NewWebPhoneAsset::register($this);
?>

<?= $this->render('partial/_phone_widget', [
	'phoneFrom' => $phoneFrom
]) ?>
<?= $this->render('partial/_phone_widget_icon') ?>

<?php
$ajaxCheckUserForCallUrl = Url::to(['phone/ajax-check-user-for-call']);
$ajaxBlackList = Url::to(['phone/check-black-phone']);
?>

<?php

$js = <<<JS
    $(document).on('click', '#btn-new-make-call', function(e) {
        e.preventDefault();
        
        $.post('{$ajaxCheckUserForCallUrl}', {user_id: userId}, function(data) {
            
            if(data && data.is_ready) {
                let phone_to = '+'+$('#call-pane__dial-number').val();
                let phone_from = '{$phoneFrom}';
                
                let project_id = '{$projectId}';
                
                $.post('{$ajaxBlackList}', {phone: phone_to}, function(data) {
                    if (data.success) {
                        webCall(phone_from, phone_to, project_id, null, null, 'web-call');        
                    } else {
                        var text = 'Error. Try again later';
                        if (data.message) {
                            text = data.message;
                        }
                        new PNotify({title: "Make call", type: "error", text: text, hide: true});
                    }
                }, 'json');
                
            } else {
                alert('You have active call');
                return false;
            }
        }, 'json');
        
    });
JS;
$this->registerJs($js);



