<?php

use common\models\Employee;
use yii\grid\SerialColumn;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Project;
use common\models\search\ContactsSearch;
use common\models\UserProfile;
use src\access\CallAccess;
use common\models\UserContactList;
use src\access\ContactUpdateAccess;
use src\access\EmployeeProjectAccess;
use src\auth\Auth;
use src\helpers\call\CallHelper;
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

        <p>
            <?= Html::a('<i class="fa fa-plus"></i> Add Contact', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php Pjax::begin(); ?>

        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-search"></i> Search</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?= $this->render('_search_union', ['model' => $searchModel]); ?>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'rowOptions' => static function ($model, $index, $widget, $grid) {
                if ($model['disabled']) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                ['class' => SerialColumn::class, 'options' => ['style' => 'width:50px']],
                [
                    //'label' => 'Type/...',
                    'value' => static function ($model) {
                        $onlineClass = '';
                        if ($model['type'] === Client::TYPE_INTERNAL) {
                            /** @var Employee $user */
                            $user = $model['model'];
                            if ($user && $user->isOnline()) {
                                $onlineClass = 'text-success';
                            }
                        }

                        $labels = [];
                        $labels[] = $model['is_company'] ? '<i class="fa fa-building-o" title="Company"></i>' : '<i class="fa fa-user ' . $onlineClass . '" title="Personal"></i>';
                        $labels[] = $model['is_public'] ? '<i class="fa fa-globe" title="public"></i>' : '<i class="fa fa-book" title="private"></i>';
                        $labels[] = $model['disabled'] ? '<i class="fa fa-ban text-danger" title="Disabled"></i>' : '';
                        return implode('&nbsp;&nbsp;&nbsp;', $labels);
                    },
                    'label' => '',
                    'format' => 'raw',
                    'options' => [
                        'style' => 'width:100px'
                    ],
                ],
//                [
//                    'attribute' => 'favorite',
//                    'value' => static function ($model) {
//                        if ($model['type'] === Client::TYPE_INTERNAL) {
//                            return '';
//                        }
//                        /** @var Client $model */
//                        $model = $model['model'];
//                        $out = '<span class="not-set">(not set)</span>';
//                        if ($model->contact) {
//                            $class = $model->contact->ucl_favorite ? 'fa fa-star text-warning' : 'fa fa-star-o';
//                            $out = Html::a('<i class="' . $class . '"></i>', null, [
//                                'class' => 'btn-favorite',
//                                'data' => [
//                                    'client-id' => $model->id,
//                                    'is-favorite' => $model->contact->ucl_favorite,
//                                ],
//                            ]);
//                        }
//                        return $out;
//                    },
//                    'format' => 'raw',
//                    'filter' => [1 => 'Yes', 0 => 'No'],
//                    'contentOptions' => ['class' => 'text-center'],
//                ],
                [
                    'label' => 'Name',
                    'attribute' => 'full_name',
                    'value' => static function ($model) {
                        $out = '';
                        if ($model['type'] === Client::TYPE_CONTACT) {
                            /** @var Client $client */
                            $client = $model['model'];
                            $out = '<span class="not-set">(not set)</span>';
                            if ($client->contact) {
                                $class = $client->contact->ucl_favorite ? 'fa fa-star text-warning' : 'fa fa-star-o';
                                $out = Html::a('<i class="' . $class . '"></i>', null, [
                                    'class' => 'btn-favorite',
                                    'data' => [
                                        'client-id' => $client->id,
                                        'is-favorite' => $client->contact->ucl_favorite,
                                    ],
                                ]) . '&nbsp;&nbsp;&nbsp;';
                            }
                        }
                        $out .= ($model['type'] === Client::TYPE_INTERNAL ? '<i class="fa fa-user"></i>&nbsp;&nbsp; ' : '') . '<b>' . Html::encode($model['full_name']) . '</b>';
                        return $out;
                    },
                    'format' => 'raw',
                    'filter' => false,
                ],
                [
                    'header' => 'Phones',
                    'attribute' => 'client_phone',
                    'value' => static function ($model) {
                        if ($model['type'] === Client::TYPE_INTERNAL) {
                            /** @var Employee $model */
                            $model = $model['model'];
                            $phones = [];
                            foreach ($model->userProjectParams as $params) {
                                if ($phone = $params->getPhone()) {
                                    $phones[] = $phone;
                                }
                            }
                            $data = [];
                            foreach ($phones as $phone) {
                                $access = CallAccess::isUserCanDial(Auth::id(), UserProfile::CALL_TYPE_WEB);

                                $out = '<span data-toggle="tooltip" title="" data-original-title="">';

                                $out .= CallHelper::callNumber($phone, $access, '', ['data-title' => $model->full_name, 'disable-icon' => $access ? false : true, 'data-user-id' => $model->id], 'span');
                                $out .= '</span>';

                                $data[] = $out;
                            }

                            return implode('<br>', $data);
                        }

                        /** @var Client $model */
                        $model = $model['model'];
                        $phones = $model->clientPhones;
                        $data = [];
                        if ($phones) {
                            foreach ($phones as $k => $phone) {
                                $access = CallAccess::isUserCanDial(Auth::id(), UserProfile::CALL_TYPE_WEB);

                                $out = '<span data-toggle="tooltip" 
                                            title="' . Html::encode($phone->cp_title) . '"
                                            data-original-title="' . Html::encode($phone->cp_title) . '">';

                                $out .= CallHelper::callNumber($phone->phone, $access, '', ['data-contact-id' => $model->id, 'data-title' => $model->getNameByType(), 'disable-icon' => $access ? false : true], 'span');
                                $out .= '</span>';

                                $data[] = $out;
                            }
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                    'filter' => false,
                ],
                [
                    'header' => 'Emails',
                    'attribute' => 'client_email',
                    'value' => static function ($model) {
                        if ($model['type'] === Client::TYPE_INTERNAL) {

                            /** @var Employee $model */
                            $model = $model['model'];
                            $emails = [];
                            foreach ($model->userProjectParams as $params) {
                                if ($email = $params->getEmail()) {
                                    $emails[] = $email;
                                }
                            }
                            $data = [];
                            foreach ($emails as $email) {
                                $data[] = ' <code>' . Html::encode($email) . '</code>';
                            }

                            return implode('<br>', $data);
                        }

                        /** @var Client $phones */
                        $model = $model['model'];
                        $emails = $model->clientEmails;
                        $data = [];
                        if ($emails) {
                            foreach ($emails as $k => $email) {
                                $data[] = ' <code data-toggle="tooltip" 
                                            title="' . Html::encode($email->ce_title) . '"
                                            data-original-title="' . Html::encode($email->ce_title) . '">' .
                                    Html::encode($email->email) . '</code>';
                            }
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Actions',
                    'template' => '{view} &nbsp;&nbsp; {update} &nbsp;&nbsp;&nbsp; {delete}',
                    'buttons' => [
                        'delete' => static function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash text-danger"></span>', ['delete', 'id' => $model['id']], [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                        'update' => static function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model['id']], [
                            ]);
                        },
                        'view' => static function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model['id']], [
                            ]);
                        }

                    ],
                    'visibleButtons' =>
                        [
                            'update' => static function ($model) {
                                if ($model['type'] === Client::TYPE_CONTACT) {
                                    return (new ContactUpdateAccess())->isUserCanUpdateContact($model['model'], Auth::user());
                                }
                                return false;
                            },
                            'delete' => static function ($model) {
                                if ($model['type'] === Client::TYPE_CONTACT) {
                                    return (new ContactUpdateAccess())->isUserCanUpdateContact($model['model'], Auth::user());
                                }
                                return false;
                            },
                            'view' => static function ($model) {
                                return $model['type'] === Client::TYPE_CONTACT;
                            },
                        ],
                    'options' => [
                        'style' => 'width:100px'
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
                        createNotifyByObject({
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
                        createNotifyByObject({
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