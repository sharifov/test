<?php
/**
 * @var $form ActiveForm
 * @var $phone ClientPhone
 * @var $key string|integer
 * @var $nr integer
 * @var $leadForm LeadForm
 */

use yii\widgets\ActiveForm;
use common\models\ClientPhone;
use yii\helpers\Html;
use frontend\models\LeadForm;
use borales\extensions\phoneInput\PhoneInput;

?>

<div class="form-group sl-client-field">
    <?php
    if ($key == '__id__') {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => '',
            ],
            'template' => '{input}{error}'
        ])->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'tel'
        ])->label(false);
    } else {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => '',
            ],
            'template' => '{input}{error}'
        ])->widget(PhoneInput::class, [
            'options' => [
                'class' => 'form-control lead-form-input-element'
            ],
            'jsOptions' => [
                'nationalMode' => false,
                'preferredCountries' => ['us'],
            ]
        ])->label(false);
    }

    if (($key == '__id__' || strpos($key, 'new') !== false) && $nr != 0) {
        echo Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0);', [
            'class' => 'btn sl-client-field-del js-cl-email-del client-remove-phone-button',
        ]);
    } else if (!$phone->isNewRecord) {
        $popoverId = 'addPhoneComment-' . $key;
        $commentTemplate = '
<div>
    <form action="/lead/add-comment?type=phone&amp;id=' . $key . '" method="post">
        <textarea id="email-comment-' . $key . '" style="background-color: #fafafc; border: 1px solid #e4e8ef;" class="form-control mb-20" name="comment" rows="3">' . $phone->comments . '</textarea>
        <button type="button" class="btn btn-success popover-close-btn" onclick="addPhoneComment($(this), \'' . $key . '\');">Add</button>    
    </form>
</div>
';

        //$phoneCount = rand(0, 1);

        $searchModel = new \common\models\search\ClientSearch();
        $params = Yii::$app->request->queryParams;
        $params['ClientSearch']['client_phone'] = $phone->phone;
        $params['ClientSearch']['not_in_client_id'] = $phone->client_id;
        $dataProvider = $searchModel->search($params);

        $phoneCount = $dataProvider->count;

        if ($phoneCount > 0) {

            $phoneContent = \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    'id',
                    'first_name',
                    [
                        'header' => 'Phones',
                        'attribute' => 'client_phone',
                        'value' => function (\common\models\Client $model) {

                            $phones = $model->clientPhones;
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
                        'value' => function (\common\models\Client $model) {

                            $emails = $model->clientEmails;
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
                        'header' => 'Leads',
                        'value' => function (\common\models\Client $model) {

                            $leads = $model->leads;
                            $data = [];
                            if ($leads) {
                                foreach ($leads as $lead) {
                                    $data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead->id, ['/admin/leads/view', 'id' => $lead->id], ['target' => '_blank', 'data-pjax' => 0]) . ' (IP: ' . $lead->request_ip . ')';
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
                    ],

                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime($model->created, 'php:Y-m-d [H:i]');
                        },
                        'format' => 'html',
                    ],

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'admin/client'],
                ],
            ]);


            yii\bootstrap\Modal::begin([
                'headerOptions' => ['id' => 'modal-header-' . $key],
                'id' => 'modal-phone-cnt-' . $key,
                'size' => 'modal-lg',
                'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
            ]);
            echo $phoneContent; //"<div id='modalContent'></div>";
            yii\bootstrap\Modal::end();


            echo Html::a(($phoneCount) . ' <i class="fa fa-user"></i>', 'javascript:void(0);', [
                'id' => 'phone-cnt-' . $key,
                'title' => $phone->phone,
                'data-modal_id' => 'phone-cnt-' . $key,
                'class' => 'btn sl-client-field-del js-cl-email-del showModalButton',
            ]);


        }

        echo Html::a('<i class="fa fa-comment"></i>', 'javascript:void(0);', [
            'id' => $popoverId,
            'data-toggle' => 'popover',
            'data-placement' => 'right',
            'data-content' => $commentTemplate,
            'class' => 'btn sl-client-field-del js-cl-email-del client-comment-phone-button',
        ]);


    }
    ?>
</div>