<?php

use common\models\Client;
use common\models\Project;
use sales\access\ContactUpdateAccess;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var common\models\search\ClientSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Contacts';
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
            'id',
            'first_name',
            'last_name',
            'company_name',
            [
                'attribute' => 'is_company',
                'value' => function(Client $model) {
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
                'value' => function(Client $model) {
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
                'value' => function(Client $model) {
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
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => function(Client $model) {
                    $phones = $model->clientPhones;
                    $data = [];
                    if($phones) {
                        foreach ($phones as $k => $phone) {
                            $sms = $phone->is_sms ? '<i class="fa fa-comments-o"></i>  ' : '';
                            $data[] = $sms . '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>';
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
                            $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code>';
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
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{view}',
                'visibleButtons'=>
                [
                     'update' => static function (Client $model) {
                        return (new ContactUpdateAccess())->isUserCanUpdateContact($model, Auth::user());
                     },
                    'view' => true,
                ],
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