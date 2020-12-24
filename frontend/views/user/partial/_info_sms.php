<?php

use common\models\Employee;
use yii\widgets\Pjax;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;

?>

<?php Pjax::begin(['timeout' => 10000]); ?>

<?php /*echo $this->render('_info_sms_search', ['model' => $smsSearchModel]); */ ?>
<h5>Sms Stats</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $smsDataProvider,
        'filterModel' => $smsSearchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Sms $model, $index, $widget, $grid) {
            if ($model->s_status_id == \common\models\Sms::STATUS_ERROR) {
                return ['class' => 'danger'];
            } elseif ($model->s_status_id == \common\models\Sms::STATUS_PROCESS) {
                return ['class' => 'warning'];
            } elseif ($model->s_status_id == \common\models\Sms::STATUS_DONE) {
                return ['class' => 'success'];
            }
        },
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'s_is_deleted',

            [
                'attribute' => 's_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            's_is_new:boolean',

            [
                'attribute' => 's_type_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Sms::TYPE_LIST
            ],

            [
                'attribute' => 's_lead_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_lead_id ? Html::a($model->s_lead_id, ['lead/view', 'gid' => $model->sLead->gid], ['target' => '_blank']) : '';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],


            //'s_reply_id',
            //'s_lead_id',
            //'s_project_id',
            [
                'attribute' => 's_project_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->sProject ? $model->sProject->name : '-';
                },
                'filter' => \common\models\Project::getListByUser(Yii::$app->user->id)
            ],

            [
                'attribute' => 's_phone_from',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_phone_from;
                },
                'filter' => Employee::getPhoneList(Yii::$app->user->id)
            ],
            [
                'attribute' => 's_phone_to',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_phone_to;
                },
                'filter' => Employee::getPhoneList(Yii::$app->request->get('id'))
            ],

            's_sms_text:ntext',
            //'s_sms_data:ntext',
            //'s_type_id',

            //'s_template_type_id',
            //'s_language_id',
            /*[
                'attribute' => 's_language_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_language_id;
                },
                'filter' => \common\models\Language::getLanguages(true)
            ],*/
            //'s_communication_id',
            //'s_is_deleted',
            //'s_is_new',
            //'s_delay',
            //'s_priority',
            [
                'attribute' => 's_status_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Sms::STATUS_LIST
            ],

            /*[
                'attribute' => 's_created_user_id',
                'value' => static function (\common\models\Sms $model) {
                    return  ($model->sCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sCreatedUser->username) : $model->s_created_user_id);
                },
                'format' => 'raw'
            ],*/
            [
                'attribute' => 's_created_dt',
                'value' => static function (\common\models\Sms $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $smsSearchModel,
                    'attribute' => 's_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],

            /*[
                'class' => 'yii\grid\ActionColumn',
                //'controller' => 'order-shipping',
                'template' => '{view2} {soft-delete}',

                'buttons' => [
                    'view2' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                            'title' => 'View',
                        ]);
                    },
                    'soft-delete' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                            'title' => 'Delete',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this SMS?',
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],*/
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

