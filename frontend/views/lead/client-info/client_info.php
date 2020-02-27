<?php
/**
 * @var $this View
 * @var $lead Lead
 * @var $leadForm LeadForm
 */

use common\models\Employee;
use common\models\Lead;
use yii\helpers\Html;
use frontend\models\LeadForm;
use yii\web\View;
use \yii\helpers\Url;
use yii\bootstrap4\Modal;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
$manageClientInfoAccess = \sales\access\ClientInfoAccess::isUserCanManageLeadClientInfo($lead, $user);

?>

    <div class="x_panel">
        <div class="x_title" >
            <h2><i class="fa fa-user-circle-o"></i> Client Info</h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $manageClientInfoAccess): ?>
                    <li>
                        <?=Html::a('<i class="fas fa-info-circle"></i> Details', '#',  [
                            'id' => 'btn-client-details',
                            'data-client-id' => $leadForm->getClient()->id,
                            'title' => 'Client Info',
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Phone', '#', [
                            'id' => 'client-new-phone-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Add Phone',
                            'data-content-url' => Url::to(['lead-view/ajax-add-client-phone-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Email', '#',  [
                            'id' => 'client-new-email-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Add Email',
                            'data-content-url' => Url::to(['lead-view/ajax-add-client-email-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>
                    <li>
                        <?=Html::a('<i class="fas fa-edit warning"></i> Update Client', '#',  [
                            'id' => 'client-edit-user-name-button',
                            'data-modal_id' => 'client-manage-info',
                            'title' => 'Update user name',
                            'data-content-url' => Url::to(['lead-view/ajax-edit-client-name-modal-content', 'gid' => $lead->gid]),
                            'class' => 'showModalButton'
                        ])?>
                    </li>

                <?php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">

            <div class="row">

                <div class="col-md-4">
                    <?= $this->render('_client_manage_name', [
                        'client' => $lead->client
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <div id="client-manage-phone">
                        <?php if ($phones = $lead->client->clientPhones): ?>
                            <?php
                            if ($leadForm->viewPermission) {
                                echo $this->render('_client_manage_phone', [
                                    'clientPhones' => $phones,
                                    'lead' => $lead,
                                    'manageClientInfoAccess' => $manageClientInfoAccess
                                ]);
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                    <div id="client-manage-email">
                        <?php if ($emails = $lead->client->clientEmails): ?>
                            <?php
                            if ($leadForm->viewPermission) {
                                echo $this->render('_client_manage_email', [
                                    'clientEmails' => $emails,
                                    'lead' => $lead,
                                    'manageClientInfoAccess' => $manageClientInfoAccess
                                ]);
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <?= \frontend\widgets\client\ClientCounterWidget::widget([
                        'clientId' => $leadForm->getClient()->id,
                        'userId' => $user->id
                    ]) ?>

                    <?php if (!empty($leadForm->getLead()->request_ip)): ?>
                        <?= $this->render('_client_ip_info', ['lead' => $leadForm->getLead()]) ?>
                    <?php endif; ?>

                </div>
            </div>


                    <?php /*= Html::button('<i class="fa fa-user"></i> Client Info', [
                        'class' => 'btn btn-default',
                        'id' => 'btn-client-details',
                        'data-client-id' => $leadForm->getClient()->id
                    ])*/ ?>

        </div>
    </div>

    <script>
        // add email comment button
        function addEmailComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addEmailComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
        // add email comment button
        function addPhoneComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addPhoneComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
    </script>


<?= Modal::widget([
    'title' => '',
    'id' => 'modal-client-manage-info',
    'size' => Modal::SIZE_SMALL,
]) ?>

<?= Modal::widget([
    'title' => '',
    'id' => 'modal-client-large',
    'size' => Modal::SIZE_LARGE,
])
?>


<?php /*
$this->registerJs(
    '
        $(document).on("click","#btn-notes-form", function() {
            $("#div-notes-form").show();
            $("#pjax-notes .x_content").show();
            
             $([document.documentElement, document.body]).animate({
                scrollTop: $("#div-notes-form").offset().top
            }, 1000);
                        
            return false;
        });

        $("#pjax-notes").on("pjax:start", function () {            
            $("#btn-submit-note").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-note i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-notes").on("pjax:end", function () {           
            $("#btn-submit-note").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-note i").attr("class", "fa fa-plus");
            $("#pjax-notes .x_content").show();
           
        }); 
    '
);*/
?>