<?php

use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use common\models\ProjectEmployeeAccess;
use common\models\ClientPhone;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $formPreferences ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());
?>

<?php $formPreferences = ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => $formId
]); ?>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Info</h3>
    <div class="row">
        <div class="col-md-6">
            <?php if ($leadForm->getLead()->isNewRecord) : ?>
                <?= $formPreferences->field($leadForm->getLead(), 'source_id')
                    ->dropDownList(ProjectEmployeeAccess::getAllSourceByEmployee(), [
                        'prompt' => 'Select'
                    ])->label('Marketing Info:') ?>
            <?php else : ?>
                <div class="form-group field-lead-sub_source_id">
                    <label class="control-label" for="lead-sub_source_id">Marketing Info:</label><br/>
                    <?= sprintf('%s - [%s]',
                        $leadForm->getLead()->source->name,
                        $leadForm->getLead()->project->name) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if ($leadForm->getLead()->uid == null): ?>
                <?= $formPreferences->field($leadForm->getLead(), 'uid')
                    ->textInput([
                        'class' => 'form-control lead-form-input-element'
                    ]) ?>
            <?php else: ?>
                <?= $formPreferences->field($leadForm->getLead(), 'uid')
                    ->textInput(['readonly' => true]) ?>
            <?php endif; ?>
        </div>
    </div>




    <?php if (!$leadForm->getLead()->isNewRecord && !empty($leadForm->getLead()->request_ip)) : ?>
        <div class="row">
            <div class="col-md-12">
                <?php

                $ipData = @json_decode($leadForm->getLead()->request_ip_detail, true);

                $strData[] = isset($ipData['country']) ? 'Country: <b>' . $ipData['country'] . '</b>' : 'Country: -';
                $strData[] = isset($ipData['state']) ? 'State: <b>' . $ipData['state'] . '</b>' : 'State: -';
                $strData[] = isset($ipData['city']) ? 'City: <b>' . $ipData['city'] . '</b>' : 'City: -';

                $str = implode('<br> ', $strData);

                $popoverId = 'ip-popup';
                $commentTemplate = $str;

                /*$ipCount = \common\models\Lead::find()->where([
                    'request_ip' => $leadForm->getLead()->request_ip
                ])->andWhere(['NOT IN', 'id', $leadForm->getLead()->id])->count();*/

                $searchModel = new \common\models\search\LeadSearch();
                $params = Yii::$app->request->queryParams;
                $params['LeadSearch']['request_ip'] = $leadForm->getLead()->request_ip;
                //$params['ClientSearch']['not_in_client_id'] = $email->client_id;
                $dataProvider = $searchModel->search2($params);

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
                                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                                },
                                'filter' => \common\models\Lead::TRIP_TYPE_LIST
                            ],

                            [
                                'attribute' => 'cabin',
                                'value' => function(\common\models\Lead $model) {
                                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                                },
                                'filter' => \common\models\Lead::CABIN_LIST
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


                    echo Html::a('IP address: ' . $leadForm->getLead()->request_ip . ($ipCount ? ' - ' . $ipCount . ' <i class="fa fa-clone"></i>' : ''), 'javascript:void(0);', [
                        'id' => 'op-cnt-ip',
                        'data-modal_id' => 'ip-cnt-ip',
                        'title' => $leadForm->getLead()->request_ip,
                        'class' => 'btn sl-client-field-del js-cl-ip-del showModalButton',
                    ]);


                } else {


                    echo '<br>' . Html::a('IP address: ' . $leadForm->getLead()->request_ip . ($ipCount > 1 ? ' - ' . $ipCount . ' <i class="fa fa-clone"></i>' : ''), 'javascript:void(0);', [
                            'id' => $popoverId,
                            'data-toggle' => 'popover',
                            'data-placement' => 'bottom',
                            'data-content' => $commentTemplate,
                            'class' => 'btn sl-client-field-del client-comment-phone-button',
                        ]);
                }

                ?>
            </div>
        </div>
    <?php endif; ?>


</div>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Preferences</h3>
    <div class="row">
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'market_price')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'clients_budget')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
    </div>
    <?= $formPreferences->field($leadForm->getLeadPreferences(), 'number_stops')
        ->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'number',
            'min' => 0,
        ]) ?>
    <?= $formPreferences->field($leadForm->getLead(), 'notes_for_experts')
        ->textarea([
            'rows' => 7,
            'class' => 'form-control'
        ]) ?>
</div>
<?php ActiveForm::end(); ?>

<div class="sidebar__section" id="user-actions-block"
     style="display: <?= ($leadForm->getLead()->isNewRecord) ? 'none' : 'block' ?>;">
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="fa fa-list"></i></span> View client actions', [
            'id' => 'view-client-actions-btn',
            'class' => 'btn btn-primary btn-with-icon'
        ]) ?>
    </div>
</div>