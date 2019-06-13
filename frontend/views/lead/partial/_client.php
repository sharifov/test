<?php
use yii\bootstrap\ActiveForm;
use frontend\models\LeadForm;
use common\models\ClientEmail;
use common\models\ClientPhone;
use yii\helpers\Html;
use yii\helpers\VarDumper;

/**
 * @var $this \yii\web\View
 * @var $formClient ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
?>

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

<?php $formClient = ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => $formId
]); ?>
    <div class="sidebar__section">
        <h3 class="sidebar__subtitle">
            <i class="fa fa-user"></i>
        </h3>
        <div class="sidebar__subsection">
            <?= $formClient->field($leadForm->getClient(), 'first_name')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
            <?= $formClient->field($leadForm->getClient(), 'middle_name')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
            <?= $formClient->field($leadForm->getClient(), 'last_name')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
        <div class="sidebar__subsection">
            <div id="client-emails">
                <?php
                if ($leadForm->viewPermission) :
                    $nr = 0;
                    foreach ($leadForm->getClientEmail() as $key => $_email) {
                        /**
                         * @var $_email ClientEmail
                         */
                        echo $this->render('_formClientEmail', [
                            'key' => $_email->isNewRecord
                                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                                : $_email->id,
                            'form' => $formClient,
                            'email' => $_email,
                            'leadForm' => $leadForm,
                            'nr' => $nr
                        ]);
                        $nr++;
                    }
                    ?>
                    <!-- new email fields -->
                    <div id="client-new-email-block" style="display: none;">
                        <?php $newEmail = new ClientEmail(); ?>
                        <?= $this->render('_formClientEmail', [
                            'key' => '__id__',
                            'form' => $formClient,
                            'email' => $newEmail,
                            'leadForm' => $leadForm,
                            'nr' => $nr
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            if ($leadForm->mode != $leadForm::VIEW_MODE) :
                echo '<div class="btn-wrapper">'.
                    Html::button('<i class="fa fa-plus"></i> <i class="fa fa-envelope"></i>', [
                        'id' => 'client-new-email-button',
                        'class' => 'btn btn-primary'
                    ]).
                    '</div>';
                ob_start(); // output buffer the javascript to register later
                ?>
                <script>
                    // add email button
                    var email_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
                    $('#client-new-email-button').on('click', function () {
                        email_k += 1;
                        $('#client-emails').append($('#client-new-email-block').html().replace(/__id__/g, 'new' + email_k));
                    });

                    // remove email button
                    $(document).on('click', '.client-remove-email-button', function () {
                        $(this).closest('div').remove();
                    });
                </script>
                <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()));
            endif; ?>
        </div>
        <div class="sidebar__subsection">
            <div id="client-phones">
                <?php
                if ($leadForm->viewPermission) :
                    // existing emails fields
                    $nr = 0;
                    foreach ($leadForm->getClientPhone() as $key => $_phone) {
                        /**
                         * @var $_phone ClientPhone
                         */
                        echo $this->render('_formClientPhone', [
                            'key' => $_phone->isNewRecord
                                ? (strpos($key, 'new') !== false ? $key : 'new' . $key)
                                : $_phone->id,
                            'form' => $formClient,
                            'phone' => $_phone,
                            'leadForm' => $leadForm,
                            'nr' => $nr
                        ]);
                        $nr++;
                    }
                    ?>
                    <!-- new phone fields -->
                    <div id="client-new-phone-block" style="display: none;">
                        <?php $newPhone = new ClientPhone(); ?>
                        <?= $this->render('_formClientPhone', [
                            'key' => '__id__',
                            'form' => $formClient,
                            'phone' => $newPhone,
                            'leadForm' => $leadForm,
                            'nr' => $nr
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            if ($leadForm->mode != $leadForm::VIEW_MODE) :
                echo '<div class="btn-wrapper">'.Html::button('<i class="fa fa-plus"></i> <i class="fa fa-phone"></i>', [
                        'id' => 'client-new-phone-button',
                        'class' => 'btn btn-primary'
                    ]).
                    '</div>';
                ob_start(); // output buffer the javascript to register later
                ?>
                <script>
                    // add phone button
                    var phone_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
                    $('#client-new-phone-button').on('click', function () {
                        phone_k += 1;
                        $('#client-phones').append($('#client-new-phone-block').html().replace(/__id__/g, 'new' + phone_k));
                        var phoneId = '<?= strtolower($newPhone->formName()) ?>-new' + phone_k + '-phone';
                        $('#' + phoneId).intlTelInput({"nationalMode": false, "preferredCountries": ["us"]});
                    });

                    // remove phone button
                    $(document).on('click', '.client-remove-phone-button', function () {
                        $(this).closest('div').remove();
                    });
                </script>
                <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()));
            endif; ?>
        </div>
        <?php if(!$leadForm->getLead()->isNewRecord) :?>
            <div class="btn-group" id="user-actions-block">
                <?= Html::button('<i class="fa fa-history"></i> Actions', [
                    'id' => 'view-client-actions-btn',
                    'class' => 'btn btn-default'
                ]) ?>
                <?php if (!empty($leadForm->getLead()->request_ip)) : ?>
                    <?php
                    $ipData = @json_decode($leadForm->getLead()->request_ip_detail, true);
                    $strData = [];

                    $str = '';

                    if($ipData) {
                        $str .= '<table class="table table-bordered">';
                        foreach ($ipData as $key => $val) {
                            if(is_array($val)){
                                continue;
                            }
                            $strData[] = $key.': '.$val;
                            $str .= '<tr><th>'.$key.'</th><td>'.$val.'</td></tr>';
                        }

                        $str .= '</table>';

                        /*$strData[] = isset($ipData['country']) ? 'Country: <b>' . $ipData['country'] . '</b>' : 'Country: -';
                        $strData[] = isset($ipData['state']) ? 'State: <b>' . $ipData['state'] . '</b>' : 'State: -';
                        $strData[] = isset($ipData['city']) ? 'City: <b>' . $ipData['city'] . '</b>' : 'City: -';*/
                    }

                    //$str = implode('<br> ', $strData);

                    $popoverId = 'ip-popup';
                    $commentTemplate = $str;

                    /*$ipCount = \common\models\Lead::find()->where([
                        'request_ip' => $leadForm->getLead()->request_ip
                    ])->andWhere(['NOT IN', 'id', $leadForm->getLead()->id])->count();*/

                    $searchModel = new \common\models\search\LeadSearch();
                    $params = Yii::$app->request->queryParams;
                    $params['LeadSearch']['request_ip'] = $leadForm->getLead()->request_ip;
                    //$params['ClientSearch']['not_in_client_id'] = $email->client_id;
                    $dataProvider = $searchModel->search($params);

                    $ipCount = $dataProvider->count;

                    if ($ipCount > 1) {
                        $ipContent = \yii\grid\GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => false,
                            'columns' => [
                                //['class' => 'yii\grid\SerialColumn'],
                                'id',
                                [
                                    'header' => 'IP',
                                    'attribute' => 'request_ip',
                                    'value' => function (\common\models\Lead $model) {
                                        return '' . Html::a($model->request_ip, ['leads/index', 'LeadSearch[request_ip]' => $model->request_ip], ['data-pjax' => 0, 'target' => '_blank']) . '';
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-left'],
                                ],
                                [
                                    'attribute' => 'uid',
                                    'options' => ['style' => 'width:100px'],
                                    'contentOptions' => ['class' => 'text-center'],
                                ],

                                [   'attribute' => 'client_id',
                                    'options' => ['style' => 'width:80px'],
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                [
                                    //'attribute' => 'client_id',
                                    'header' => 'Client name',
                                    'format' => 'raw',
                                    'value' => function(\common\models\Lead $model) {

                                        if($model->client) {
                                            $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                            if ($clientName === 'Client Name') {
                                                $clientName = '- - - ';
                                            } else {
                                                $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                                            }
                                        } else {
                                            $clientName = '-';
                                        }

                                        return $clientName;
                                    },
                                    'options' => ['style' => 'width:160px'],
                                    //'filter' => \common\models\Employee::getList()
                                ],
                                [
                                    'header' => 'Phones',
                                    'attribute' => 'client_phone',
                                    'value' => function (\common\models\Lead $model) {

                                        $phones = $model->client->clientPhones;
                                        $data = [];
                                        if ($phones) {
                                            foreach ($phones as $k => $phone) {
                                                $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>';
                                            }
                                        }

                                        $str = implode('<br>', $data);
                                        return '' . $str . '';
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-left'],
                                ],

                                [
                                    'header' => 'Emails',
                                    'attribute' => 'client_email',
                                    'value' => function (\common\models\Lead $model) {

                                        $emails = $model->client->clientEmails;
                                        $data = [];
                                        if ($emails) {
                                            foreach ($emails as $k => $email) {
                                                $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code>';
                                            }
                                        }

                                        $str = implode('<br>', $data);
                                        return '' . $str . '';
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-left'],
                                ],
                                [
                                    'attribute' => 'trip_type',
                                    'value' => function(\common\models\Lead $model) {
                                        return $model->getFlightTypeName();
                                    },
                                    'filter' => \common\models\Lead::getFlightTypeList()
                                ],

                                [
                                    'attribute' => 'cabin',
                                    'value' => function(\common\models\Lead $model) {
                                        return $model->getCabinClassName();
                                    },
                                    'filter' => \common\models\Lead::getCabinList()
                                ],

                                [
                                    'header' => 'Segments',
                                    'value' => function(\common\models\Lead $model) {

                                        $segments = $model->leadFlightSegments;
                                        $segmentData = [];
                                        if($segments) {
                                            foreach ($segments as $sk => $segment) {
                                                $segmentData[] = ($sk + 1).'. <code>'.Html::a($segment->origin.'->'.$segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                            }
                                        }

                                        $segmentStr = implode('<br>', $segmentData);
                                        return ''.$segmentStr.'';
                                        //return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                                    },
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'text-center'],
                                    'options' => ['style' => 'width:140px'],
                                ],

                                /*[
                                    'header' => 'Leads',
                                    'value' => function (\common\models\Client $model) {

                                        $leads = $model->leads;
                                        $data = [];
                                        if ($leads) {
                                            foreach ($leads as $lead) {
                                                $data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead->id, ['leads/view', 'id' => $lead->id], ['target' => '_blank', 'data-pjax' => 0]) . ' (IP: ' . $lead->request_ip . ')';
                                            }
                                        }

                                        $str = '';
                                        if ($data) {
                                            $str = '' . implode('<br>', $data) . '';
                                        }

                                        return $str;
                                    },
                                    'format' => 'raw',
                                    //'options' => ['style' => 'width:100px']
                                ],*/

                                [
                                    'attribute' => 'created',
                                    'value' => function (\common\models\Lead $model) {
                                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                                    },
                                    'format' => 'html',
                                ],

                                //['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'leads'],
                            ],
                        ]);


                        yii\bootstrap\Modal::begin([
                            'headerOptions' => ['id' => 'modal-header-ip'],
                            'id' => 'modal-ip-cnt-ip',
                            'size' => 'modal-lg',
                            'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
                        ]);
                        echo '<pre>';
                        echo $commentTemplate;
                        echo '</pre>';
                        echo $ipContent; //"<div id='modalContent'></div>";

                        yii\bootstrap\Modal::end();


                        echo Html::button('<i class="fa fa-globe"></i> IP: ' . $leadForm->getLead()->request_ip . ($ipCount ? ' - ' . $ipCount . ' <i class="fa fa-clone"></i>' : ''), [
                            'id' => 'op-cnt-ip',
                            'data-modal_id' => 'ip-cnt-ip',
                            'title' => $leadForm->getLead()->request_ip,
                            'class' => 'btn btn-default showModalButton',
                        ]);

                    } else {
                        echo Html::button('<i class="fa fa-globe"></i> IP: ' . $leadForm->getLead()->request_ip . ($ipCount > 1 ? ' - ' . $ipCount . ' <i class="fa fa-clone"></i>' : ''), [
                            'id' => $popoverId,
                            'data-toggle' => 'popover',
                            'data-placement' => 'bottom',
                            'data-content' => $commentTemplate,
                            'class' => 'btn btn-default client-comment-phone-button',
                        ]);
                    }
                    ?>
                <?php endif;?>
            </div>
        <?php endif;?>

        <?php if (empty($leadForm->getLead()->request_ip) && $leadForm->getLead()->isNewRecord) : ?>
            <div class="sidebar__subsection">
                <?= $formClient->field($leadForm->getLead(), 'request_ip')
                    ->textInput([
                        'class' => 'form-control lead-form-input-element'
                    ])->label('Client IP') ?>
            </div>
        <?php endif; ?>

    </div>

<?php ActiveForm::end(); ?>

    <style type="text/css">
        @media screen and (min-width: 768px) {
            .modal-dialog {
                width: 700px; /* New width for default modal */
            }
            .modal-sm {
                width: 350px; /* New width for small modal */
            }
        }
        @media screen and (min-width: 992px) {
            .modal-lg {
                width: 70%; /* New width for large modal */
            }
        }
    </style>


<?php
$jsCode = <<<JS

    $(document).on('click', '.showModalButton', function(){
        var id = $(this).data('modal_id');

        //$('#' + id).modal('show').find('#modalContent').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#modal-header-' + id).html('<h4>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');

        //$('#modal').modal('show');

        //alert($(this).attr('title'));
        //$('#modalHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        /*$.get($(this).attr('href'), function(data) {
          $('#modal').find('#modalContent').html(data);
        });*/

        $('#modal-' + id).modal('show');
        //$('#modal').find('#modalContent').html(data);
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
