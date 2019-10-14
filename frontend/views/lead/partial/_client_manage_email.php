<?php

use Codeception\Module\Cli;
use common\models\ClientEmail;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientEmails ClientEmail[]
 */
?>

<?php
//    echo $this->render('_client_manage_phone', [
//        'phone' => $_phone,
//        'fieldName' => 'phone['.$key.']',
//        'href' => Url::to('/lead-view/ajax-get-users-same-phone-info'),
//        'gid' => $lead->gid,
//        'lead' => $lead,
//        'countUsersSamePhone' => $_phone->countUsersSamePhone()
//    ]);
?>

<? Pjax::begin(['id' => 'pjax-client-manage-email', 'enablePushState' => false, 'enableReplaceState' => false]) ?>

<? foreach ($clientEmails as $key => $email): ?>
	<table class="table table-condensed">
		<tr>
            <td title="<?= ClientEmail::EMAIL_TYPE[$email->type] ?? '' ?>">
				<?= ClientEmail::EMAIL_TYPE_ICONS[$email->type] ?? '' ?>
            </td>
			<td><i class="fa fa-envelope"></i> <?= $email->email ?? 'email is not set'?></td>
			<td class="text-right showModalButton" title="Edit Email" data-content-url="<?= Url::to(['lead-view/ajax-edit-client-email-modal-content', 'gid' => $lead->gid, 'pid' => $email->id]) ?>"
				data-modal_id="client-manage-info"><i class="fa fa-edit" style="cursor:pointer;"></i></td>
		</tr>
	</table>
<? endforeach; ?>

<? Pjax::end() ?>


<!--<div class="form-group phone_number_readonly">-->
<!--    <div class="input-group">-->
<!--        --><?//= PhoneInput::widget([
//            'name' => 'phone',
//            'value' => Html::encode($phone->phone),
//            'jsOptions' => [
//                'nationalMode' => false,
//                'preferredCountries' => ['us'],
//            ],
//            'options' => [
//                'class' => 'form-control lead-form-input-element',
//                'readonly' => true,
//            ]
//        ]) ?>
<!--        <span class="input-group-btn">-->
<!--            --><?// if ($countUsersSamePhone): ?>
<!--                --><?//=
//                    \yii\helpers\Html::button('<i class="fa fa-user"></i> '.$countUsersSamePhone, [
//                        'id' => 'phone-cnt-' . $phone->id,
//                        'title' => $phone->phone,
//                        'data-modal_id' => 'phone-cnt-' . $phone->id,
//                        'data-phone-number' => $phone->phone,
//                        'data-client-id' => $phone->client_id,
//                        'class' => 'btn btn-primary getSameUsersByPhone showModalButton',
//                    ]);
//                ?>
<!--            --><?// endif; ?>
<!---->
<!--            --><?// if (!is_null($phone->type)): ?>
<!--                --><?//=
//                \yii\helpers\Html::button(ClientPhone::PHONE_TYPE_ICONS[$phone->type], [
//                    'title' => ClientPhone::PHONE_TYPE[$phone->type],
//                    'class' => 'btn btn-primary ',
//                ]);
//                ?>
<!--            --><?// endif; ?>
<!---->
<!--            --><?//=
//                \yii\helpers\Html::button('<i class="fa fa-pencil"></i> ', [
//                    'title' => 'Edit',
//                    'data-modal_id' => 'edit-phone-' . $phone->id,
//                    'class' => 'btn btn-warning showModalButton',
//                ]);
//			?>
<!--        </span>-->
<!--    </div>-->
<!--</div>-->

<?//=
//Modal::widget([
//	'headerOptions' => ['id' => 'modal-header-' . $phone->id],
//	'id' => 'modal-phone-cnt-' . $phone->id,
//	'size' => 'modal-lg',
//	'clientOptions' => ['backdrop' => 'static'],//, 'keyboard' => FALSE]
//]);
//?>
<!---->
<?//
//Modal::begin([
//	'headerOptions' => ['id' => 'modal-edit-header-' . $phone->id],
//	'header' => '<h3>Edit Phone</h3>',
//	'id' => 'modal-edit-phone-' . $phone->id,
//	'size' => 'modal-sm',
//	'clientOptions' => ['backdrop' => 'static'],//, 'keyboard' => FALSE]
//	'clientEvents' => [
//		'hide' => 'function () {
//		    $(this).find("form").trigger("reset");
//        }'
//	]
//]);
//?>
<?//= $this->render('_client_phone_modal_content', [
//    'phone' => $phone,
//    'form' => $form,
//    'gid' => $lead->gid,
//    'lead' => $lead
//]) ?>
<?//
//Modal::end();
//?>

<?php
$href = Url::to('lead-view/ajax-get-users-same-phone-info');
$js = <<<JS
        // $('.showModalButton').off().on('click', function(){
        //     var id = $(this).data('modal_id');
        //
        //     $('#modal-' + id).modal('show');
        //    return false;
        // });
        
//        $(document).on('click', '.getSameUsersByPhone', function (e) {
//            var modalId = $(this).data('modal_id');
//            var href = "$href";
//            var phone = $(this).data('phone-number');
//            var clientId = $(this).data('client-id');
//            
//            $.ajax({
//               type: 'post',
//               url: href,
//               dataType: 'html',
//               data: {phone: phone, clientId: clientId},
//               beforeSend: function () {
//                    $('#modal-' + modalId + ' .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
//               },
//               success: function (data) {
//                    $('#modal-' + modalId + ' .modal-body').html(data);
//               },
//               error: function (text) {
//                   new PNotify({
//                        title: "Error",
//                        type: "error",
//                        text: "Internal Server Error. Try again letter.",
//                        hide: true,
//                        delay: 3000
//                    });
//                    
//                   $('#modal-' + modalId).modal('hide');
//               }
//            })
//        });
JS;
$this->registerJs($js);
?>
