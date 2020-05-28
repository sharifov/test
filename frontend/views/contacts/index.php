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
        <?= Html::a('<i class="fa fa-plus"></i> Add Contact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (Client $model, $index, $widget, $grid) {
            if ($model->disabled) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{view} &nbsp;&nbsp; {update} &nbsp;&nbsp;&nbsp; {delete}',
                'buttons' => [
                    'delete' => static function($url, Client $model){
                        return Html::a('<span class="glyphicon glyphicon-trash text-danger"></span>', ['delete', 'id' => $model->id], [
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
                    'style' => 'width:100px'
                ],
            ],
            [
                'attribute' => 'is_company',
                'label' => 'Type',
                'value' => static function(Client $model) {
                    return $model->is_company ? '<i class="fa fa-building-o" title="Company"></i>' : '<i class="fa fa-user" title="Personal"></i>';
                },
                'format' => 'raw',
                'filter' => [1 => 'Company', 0 => 'Personal'],
                'options' => [
                    'style' => 'width:80px',
                ],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'ucl_favorite',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if ($model->contact) {
                        $class = $model->contact->ucl_favorite ? 'fa fa-star text-warning' : 'fa fa-star-o';
                        $out = Html::a('<i class="' . $class . '"></i>', null, [
                            'class' => 'btn-favorite',
                            'data' => [
                                'client-id' => $model->id,
                                'is-favorite' => $model->contact->ucl_favorite,
                            ],
                        ]);
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'options' => [
                    'style' => 'width:80px',
                ],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Name',
                'attribute' => 'by_name',
                'value' => static function(Client $model) {
//                    $out = '';
//                    $out .= $model->first_name ? '<em>First name:</em> ' . Html::encode($model->first_name) . '<br />' : '';
//                    $out .= $model->middle_name ? '<em>Middle name:</em> ' . Html::encode($model->middle_name) . '<br />' : '';
//                    $out .= $model->last_name ? '<em>Last name:</em> ' . Html::encode($model->last_name) . '<br />' : '';

                    return  $model->is_company ? '-' : '<i class="fa fa-user"></i> ' . '<b>' . Html::encode($model->full_name) .'</b>';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'company_name',
                'value' => static function(Client $model) {

                    return $model->is_company ? '<i class="fa fa-building-o" title="Company"></i> ' . '<b>' . Html::encode($model->company_name) .'</b>' : '-';
                },
                'format' => 'raw',
            ],

            [
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => static function(Client $model) {
                    $phones = $model->clientPhones;
                    $data = [];
                    if($phones) {
                        foreach ($phones as $k => $phone) {

                            $access = CallAccess::isUserCanDial(Auth::id(),UserProfile::CALL_TYPE_WEB);

                            $out = '<span data-toggle="tooltip" 
                                            title="'. Html::encode($phone->cp_title) . '"
                                            data-original-title="' . Html::encode($phone->cp_title) . '">';

                            $out .= CallHelper::callNumber($phone->phone, $access, '', ['data-title' => $model->full_name, 'disable-icon' => $access ? false : true], 'span');
                            $out .= '</span>';

                            $data[] = $out;
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
                            $data[] = ' <code data-toggle="tooltip" 
                                            title="'. Html::encode($email->ce_title) . '"
                                            data-original-title="'. Html::encode($email->ce_title) . '">' .
                                Html::encode($email->email) . '</code>';
                        }
                    }
                    return implode('<br>', $data);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'label' => 'Public',
                'attribute' => 'is_public',
                'value' => static function(Client $model) {
                    return $model->is_public ? '<i class="fa fa-globe" title="public"></i>' : '<i class="fa fa-book" title="private"></i>';
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'options' => [
                    'style' => 'width:80px',
                ],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'disabled',
                'value' => static function(Client $model) {


//                    $innerBtn = $model->disabled ? '<span class="label label-success">Disabled</span>' : '-';
//                    $out = Html::a($innerBtn, null, [
//                        'class' => 'btn-disabled',
//                        'data' => [
//                            'client-id' => $model->id,
//                            'is-disabled' => $model->disabled,
//                        ],
//                    ]);

                    return $model->disabled ? '<span class="label label-danger">Disabled</span>' : '-';
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No']
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

$js = <<<JS
    $(document).ready( function () {
        $(document).on('click', '.btn-disabled', function(e) {
            e.preventDefault();
            
            let yesHtml = '<span class="label label-success">Yes</span>';
            let noHtml = '<span class="label label-danger">No</span>';
            
            let objBtn = $(this),
                clientId = objBtn.data('client-id'),
                isDisabled = objBtn.data('is-disabled');
            
            $.ajax({
                type: 'post',
                url: '/contacts/set-disabled-ajax',
                dataType: 'json',
                data: {client_id:clientId, is_disabled:isDisabled},                
                beforeSend: function () {                    
                    objBtn.html('<span class="spinner-border spinner-border-sm"></span>');
                    objBtn.prop('disabled', true);    
                },
                success: function (dataResponse) {
                
                    objBtn.prop('disabled', false);    
                    if (dataResponse.status === 1) {                        
                        if (dataResponse.disabled === 1) {                            
                            objBtn.html(yesHtml);                            
                        } else {                            
                            objBtn.html(noHtml);
                        }     
                        objBtn.data('is-disabled', dataResponse.disabled);                     
                    } else {                        
                        new PNotify({
                            title: "Error:",
                            type: "error",
                            text: dataResponse.message,
                            hide: true
                        });
                        if (isDisabled) {                            
                            objBtn.html(yesHtml);
                        } else {                            
                            objBtn.html(noHtml);
                        }
                    }                          
                },
                error: function () {
                    objBtn.prop('disabled', false); 
                    if (isDisabled) {                        
                        objBtn.html(yesHtml); 
                    } else {                       
                        objBtn.html(noHtml); 
                    } 
                }
            });                         
        });
    
        $(document).on('click', '.btn-favorite', function(e) {
            e.preventDefault();
            
            let enableHtml = '<i class="fa fa-star text-warning"></i>';
            let disableHtml = '<i class="fa fa-star-o"></i>';
            
            let objBtn = $(this),
                clientId = objBtn.data('client-id'),
                isFavorite = objBtn.data('is-favorite');
            
            $.ajax({
                type: 'post',
                url: '/contacts/set-favorite-ajax',
                dataType: 'json',
                data: {client_id:clientId, is_favorite:isFavorite},                
                beforeSend: function () {                    
                    objBtn.html('<span class="spinner-border spinner-border-sm"></span>');
                    objBtn.prop('disabled', true);    
                },
                success: function (dataResponse) {
                
                    objBtn.prop('disabled', false);    
                    if (dataResponse.status === 1) {                        
                        if (dataResponse.favorite === 1) {                            
                            objBtn.html(enableHtml);                            
                        } else {                            
                            objBtn.html(disableHtml);
                        }     
                        objBtn.data('is-favorite', dataResponse.favorite);                     
                    } else {                        
                        new PNotify({
                            title: "Error:",
                            type: "error",
                            text: dataResponse.message,
                            hide: true
                        });
                        if (isFavorite) {                            
                            objBtn.html(enableHtml);
                        } else {                            
                            objBtn.html(disableHtml);
                        }
                    }                          
                },
                error: function () {
                    objBtn.prop('disabled', false); 
                    if (isFavorite) {                        
                        objBtn.html(enableHtml); 
                    } else {                       
                        objBtn.html(disableHtml); 
                    } 
                }
            });                         
        });
    });
JS;
$this->registerJs($js);

$jsCode = <<<JS

    $(document).on('click', '.show-modal', function(){
        
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $('#modalClient-label').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
          $('#modalClient').find('#modalClientContent').html(data);
        });
       return false;
    });
    
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);