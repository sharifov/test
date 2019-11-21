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
use yii\bootstrap4\Modal;
//use frontend\extensions\PhoneInput;

?>
<div class="sl-client-field">
    <?php
    $template = '<div class="input-group">{input}';
    if (($key == '__id__' || strpos($key, 'new') !== false) && $nr != 0) {
        $template .= '<span class="input-group-btn">'.
            Html::button('<i class="fa fa-trash"></i>',[
                'class' => 'btn btn-danger client-remove-phone-button'
            ]).
            '</span>';
    }
    if (!$phone->isNewRecord) {
        //$phoneCount = rand(0, 1);

        $searchModel = new \common\models\search\ClientSearch();
        $params = Yii::$app->request->queryParams;
        $params['ClientSearch']['client_phone'] = $phone->phone;
        $params['ClientSearch']['not_in_client_id'] = $phone->client_id;
        $dataProvider = $searchModel->searchFromLead($params);

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
                        'value' => static function (\common\models\Client $model) {

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
                        'value' => static function (\common\models\Client $model) {

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
                        'value' => static function (\common\models\Client $model) {

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
                    ],

                    [
                        'attribute' => 'created',
                        'value' => static function (\common\models\Client $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'html',
                    ],

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'client'],
                    ],
                    ]);


            Modal::begin([
                'title' => '',
                'id' => 'modal-phone-cnt-' . $key,
                'size' => Modal::SIZE_LARGE,
                'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
            ]);
            echo $phoneContent; //"<div id='modalContent'></div>";
            Modal::end();


            $template .= '<span class="input-group-btn">'.Html::button('<i class="fa fa-user"></i> '.$phoneCount, [
                'id' => 'phone-cnt-' . $key,
                'title' => $phone->phone,
                'data-modal_id' => 'phone-cnt-' . $key,
                'class' => 'btn btn-primary showModalButton',
            ]).'</span>';
        }

    }
    $template .= '</div>{error}';

    if ($key == '__id__') {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => 'form-group',
            ],
            'template' => $template
        ])->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'tel'
        ])->label(false);
    } else {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => 'form-group',
            ],
            'template' => $template
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
    ?>
</div>