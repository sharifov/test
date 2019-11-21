<?php
/**
 * @var $form ActiveForm
 * @var $email ClientEmail
 * @var $key string|integer
 * @var $nr integer
 * @var $leadForm LeadForm
 */

use yii\widgets\ActiveForm;
use common\models\ClientEmail;
use yii\helpers\Html;
use frontend\models\LeadForm;
use yii\bootstrap4\Modal;

?>

<div class="sl-client-field">
    <?php

    $template = '<div class="input-group"><span class="input-group-addon">@</span>{input}';
    if (($key == '__id__' || strpos($key, 'new') !== false) && $nr != 0) {
        $template .= '<span class="input-group-btn">'.
            Html::button('<i class="fa fa-trash"></i>',[
                'class' => 'btn btn-danger client-remove-email-button'
            ]).
            '</span>';
    }
    $template .= '</div>{error}';
    echo $form->field($email, '[' . $key . ']email', [
        'template' => $template,
        'options' => [
            'class' => 'form-group'
        ]
    ])->textInput([
        'class' => 'form-control email lead-form-input-element',
        'type' => 'email'
    ])->label(false);

    if (!$email->isNewRecord) {
        $popoverId = 'addEmailComment-' . $key;
        $commentTemplate = '
<div>
    <form action="/lead/add-comment?type=email&amp;id=' . $key . '" method="post">
        <textarea id="email-comment-' . $key . '" style="background-color: #fafafc; border: 1px solid #e4e8ef;" class="form-control mb-20" name="comment" rows="3">' . $email->comments . '</textarea>
        <button type="button" class="btn btn-success popover-close-btn" onclick="addEmailComment($(this), \'' . $key . '\');">Add</button>
    </form>
</div>
';


        $searchModel = new \common\models\search\ClientSearch();
        $params = Yii::$app->request->queryParams;
        $params['ClientSearch']['client_email'] = $email->email;
        $params['ClientSearch']['not_in_client_id'] = $email->client_id;
        $dataProvider = $searchModel->searchFromLead($params);

        $emailCount = $dataProvider->count;

        if ($emailCount > 0) {


            $emailContent = \yii\grid\GridView::widget([
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
                        'value' => function (\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'client'],
                ],
            ]);


            Modal::begin([
                'title' => '',
                'id' => 'modal-email-cnt-' . $key,
                'size' => Modal::SIZE_LARGE,
                'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
            ]);
            echo $emailContent; //"<div id='modalContent'></div>";
            Modal::end();

            echo Html::button('<i class="fa fa-user"></i> '.$emailCount, [
                'id' => 'email-cnt-' . $key,
                'data-modal_id' => 'email-cnt-' . $key,
                'title' => $email->email,
                'class' => 'btn btn-primary showModalButton',
            ]);


        }


       /*  echo Html::a('<i class="fa fa-comment"></i>', 'javascript:void(0);', [
            'id' => $popoverId,
            'data-toggle' => 'popover',
            'data-placement' => 'right',
            'data-content' => $commentTemplate,
            'class' => 'btn btn-primary client-comment-email-button',
        ]); */
    }
    ?>
</div>
