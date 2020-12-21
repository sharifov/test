<?php

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\entities\cases\Cases;
use yii\helpers\Html;
use yii\helpers\Url;
use sales\auth\Auth;

/* @var $this yii\web\View */
/* @var $caseModel \sales\entities\cases\Cases */
/* @var $isAdmin boolean */
?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-user"></i> Client Info</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?=Html::a('<i class="fas fa-info-circle"></i> Details', '#', [
                        'id' => 'btn-client-info-details',
                        'data-client-id' => $caseModel->cs_client_id,
                        'title' => 'Client Info',
                    ])?>
                </li>
                <?php if ($caseModel->isProcessing()) :?>
                    <li>
                        <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Add Phone', '#', ['id' => 'btn-add-phone', 'title' => 'Add Phone'])?>
                    </li>
                    <li>
                        <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Add Email', '#', ['id' => 'btn-add-email', 'title' => 'Add Email'])?>
                    </li>
                    <li>
                        <?= \yii\bootstrap\Html::a('<i class="fa fa-edit warning"></i> Update', '#', ['id' => 'btn-client-update', 'title' => 'Update Client Info'])?>
                    </li>

                    <?php if ($unsubscribe) : ?>
                        <?php if (Auth::can('client-project/subscribe-client-ajax')) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell-slash info"></i> Subscribe', '#', [
                                    'id' => 'client-unsubscribe-button',
                                    'title' => 'Allow communication with client',
                                    'data-unsubscribe-url' => Url::to(['client-project/unsubscribe-client-ajax',
                                        'clientID' => $caseModel->cs_client_id,
                                        'projectID' => $caseModel->cs_project_id,
                                        'action' => false
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if (Auth::can('client-project/unsubscribe-client-ajax')) : ?>
                            <li>
                                <?=Html::a('<i class="far fa-bell-slash info"></i> Unsubscribe', '#', [
                                    'id' => 'client-unsubscribe-button',
                                    'title' => 'Restrict communication with client',
                                    'data-unsubscribe-url' => Url::to(['client-project/unsubscribe-client-ajax',
                                        'clientID' => $caseModel->cs_client_id,
                                        'projectID' => $caseModel->cs_project_id,
                                        'action' => true
                                    ]),
                                ])?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif;?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <?php /*<li class="dropdown">
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
            <?php if ($caseModel->client) :?>
                <?php if ($caseModel->client->isExcluded()) : ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger" role="alert">
                                <b><i class="fa fa-warning"></i> Warning!</b> Excluded client.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $caseModel->client,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'first_name',
                                    'value' => static function (Client $client) {
                                        return \sales\model\client\helpers\ClientFormatter::formatName($client);
                                    },
                                    'format' => 'raw',
                                ],
                                'middle_name',
                                'last_name',
                                [
                                    'attribute' => 'Client Time',
                                    'value' => static function () use ($caseModel) {
                                        return $caseModel->getClientTime();
                                    },
                                    'format' => 'html'
                                ],
                                [
                                    'attribute' => 'cl_locale',
                                    'value' => static function (Client $client) {
                                        return '<span class="js_locale_client">' . $client->cl_locale . '</span>';
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'cl_marketing_country',
                                    'value' => static function (Client $client) {
                                        return '<span class="js_marketing_country">' . $client->cl_marketing_country . '</span>';
                                    },
                                    'format' => 'raw',
                                ],
                            ],
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $caseModel->client,
                            'attributes' => [
                                'project:projectName',
                                [
                                    'label' => 'Phones',
                                    'value' => function (\common\models\Client $model) {

                                        $phones = $model->clientPhones;
                                        $data = [];
                                        if ($phones) {
                                            foreach ($phones as $k => $phone) {
                                                $data[] = '<i class="fa fa-phone"></i> 
                                                           <code class="' . $phone::getPhoneTypeTextDecoration($phone->type) . '" 
                                                                 title="' . $phone::getPhoneType($phone->type) . '">' . Html::encode($phone->phone) . '</code> ' . $phone::getPhoneTypeLabel($phone->type); //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                            }
                                        }

                                        $str = implode('<br>', $data);
                                        return '' . $str . '';
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-left'],
                                ],

                                [
                                    'label' => 'Emails',
                                    'value' => function (\common\models\Client $model) {

                                        $emails = $model->clientEmails;
                                        $data = [];
                                        if ($emails) {
                                            foreach ($emails as $k => $email) {
                                                $data[] = '<i class="fa fa-envelope"></i> 
                                                           <code class="' . $email::getEmailTypeTextDecoration($email->type) . '"
                                                                 title="' . $email::getEmailType($email->type) . '">' . Html::encode($email->email) . '</code> ' . $email::getEmailTypeLabel($email->type);
                                            }
                                        }

                                        $str = implode('<br>', $data);
                                        return '' . $str . '';
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-left'],
                                ],

                                //'created',
                                //'updated',

                                [
                                    'attribute' => 'created',
                                    'value' => function (\common\models\Client $model) {
                                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                                    },
                                    'format' => 'html',
                                ],
                                [
                                    'attribute' => 'updated',
                                    'value' => function (\common\models\Client $model) {
                                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                                    },
                                    'format' => 'html',
                                ]
                            ],
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>



<?php
$addPhoneAjaxUrl = \yii\helpers\Url::to(['cases/add-phone', 'gid' => $caseModel->cs_gid]);
$addEmailAjaxUrl = \yii\helpers\Url::to(['cases/add-email', 'gid' => $caseModel->cs_gid]);
$clientUpdateAjaxUrl = \yii\helpers\Url::to(['cases/client-update', 'gid' => $caseModel->cs_gid]);
$clientInfoUrl = \yii\helpers\Url::to(['/client/ajax-get-info']);

$js = <<<JS
     $(document).on('click', '#btn-add-phone', function(){
           var modal = $('#modalCaseSm');                
           modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
           modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
           
           $.get('$addPhoneAjaxUrl', function(data) {
                 modal.find('.modal-body').html(data);
           }); 
           
           return false;
     });
     $(document).on('click', '#btn-add-email', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$addEmailAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

    $(document).on('click', '#btn-client-update', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$clientUpdateAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });
    
    $(document).on('click', '#btn-client-info-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        $('#modalCase .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modalCase-label').html('Client Details (' + client_id + ')');
        $('#modalCase').modal();
        $.post('$clientInfoUrl', {client_id: client_id},
            function (data) {
                $('#modalCase .modal-body').html(data);
            }
        );
    });

JS;

$this->registerJs($js);
