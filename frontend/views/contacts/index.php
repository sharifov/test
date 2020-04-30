<?php

use yii\grid\SerialColumn;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Project;
use common\models\search\ContactsSearch;
use common\models\UserProfile;
use sales\access\CallAccess;
use common\models\UserContactList;
use sales\access\ContactUpdateAccess;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use sales\helpers\call\CallHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var common\models\search\ContactsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Contacts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'delete' => static function($url, Client $model){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                            'class' => '',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ],
                'visibleButtons'=>
                [
                     'update' => static function (Client $model) {
                        return (new ContactUpdateAccess())->isUserCanUpdateContact($model, Auth::user());
                     },
                     'delete' => static function (Client $model) {
                        return (new ContactUpdateAccess())->isUserCanUpdateContact($model, Auth::user());
                     },
                    'view' => true,
                ],
                'options' => [
                    'style' => 'width:70px'
                ],
            ],
            'first_name',
            'last_name',
            'company_name',
            [
                'attribute' => 'is_company',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if (isset($model->is_company)) {
                        $out = $model->is_company ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'options' => [
                    'style' => 'width:100px'
                ],
            ],
            [
                'attribute' => 'is_public',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if (isset($model->is_public)) {
                        $out = $model->is_public ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'options' => [
                    'style' => 'width:100px'
                ],
            ],
            [
                'attribute' => 'disabled',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if (isset($model->disabled)) {
                        $out = $model->disabled ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No']
            ],
            [
                'attribute' => 'ucl_favorite',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if ($model->contact) {
                        $out = $model->contact->ucl_favorite ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No']
            ],
            [
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => static function(Client $model) {
                    $phones = $model->clientPhones;
                    $data = [];
                    if($phones) {
                        foreach ($phones as $k => $phone) {
                            $sms = $phone->is_sms ? '<i class="fa fa-comments-o"></i>  ' : '';
                            $iconClass = ClientPhone::PHONE_TYPE_ICO_CLASS[$phone->type] ?? 'fa fa-phone';
                            $data[] = $sms . CallHelper::callNumber($phone->phone, CallAccess::isUserCanDial(Auth::id(),
                                UserProfile::CALL_TYPE_WEB), '', ['icon-class' => $iconClass], 'code');
                        }
                    }
                    return implode('<br>', $data);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'header' => 'Emails',
                'attribute' => 'client_email',
                'value' => static function(Client $model) {
                    $emails = $model->clientEmails;
                    $data = [];
                    if($emails) {
                        foreach ($emails as $k => $email) {
                            $ico = ClientEmail::EMAIL_TYPE_ICONS[$email->type] ?? '<i class="fa fa-envelope"></i> ';
                            $data[] = $ico . ' <code>' . Html::encode($email->email) . '</code>';
                        }
                    }
                    return implode('<br>', $data);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],
            /*[
                'label' => 'Projects',
                'attribute' => 'contact_project_id',
                'value' => static function (Client $model) {
                    $str = '';
                    foreach ($model->projects as $project) {
                        $str .= '<div style="margin: 1px;">' . Yii::$app->formatter->asProjectName($project->name) . '</div>';
                    }
                    return $str;
                },
                'format' => 'raw',
                'filter' => EmployeeProjectAccess::getProjects(Auth::id())
            ],*/
            [
                'attribute' => 'created',
                'value' => function(Client $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


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
                width: 80%; /* New width for large modal */
            }
        }
    </style>

<?php
yii\bootstrap4\Modal::begin([
    'id' => 'modalClient',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalClientContent'></div>";
yii\bootstrap4\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '.show-modal', function(){
        //e.preventDefault();
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $('#modalClient-label').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
          $('#modalClient').find('#modalClientContent').html(data);
        });
       return false;
    });
    
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);