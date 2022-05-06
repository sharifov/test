<?php

use common\models\UserProductType;
use common\models\UserProjectParams;
use frontend\models\UserFailedLogin;
use modules\user\src\update\UpdateForm;
use src\auth\Auth;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;

/**
 * @var $form UpdateForm
 * @var $userProjectParamsDataProvider \yii\data\ActiveDataProvider
 * @var $userVoiceMailDataProvider \yii\data\ActiveDataProvider
 * @var $lastFailedLoginDataProvider \yii\data\ActiveDataProvider
 * @var $userProductTypeDataProvider yii\data\ActiveDataProvider
 */

?>

    <div class="col-sm-7">

        <div class="user-project-params-index">
            <h4>Project Params</h4>

            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-upp']); ?>
            <p>
                <?= Html::a(
                    '<i class="glyphicon glyphicon-plus"></i> Create Project Params',
                    null,
                    [
                        'class' => 'btn btn-success btn-xs act-create-upp',
                        'title' => 'Create Project Params',
                        'data-user_id' => $form->targetUser->id ?? 0,
                        'data-pjax' => '0',
                    ]
                ) ?>
            </p>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $userProjectParamsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'upp_project_id',
                        'value' => function (\common\models\UserProjectParams $model) {
                            return $model->uppProject ? '' . $model->uppProject->name . '' : '-';
                        },
                        'filter' => $form->availableList->getProjects(),
                    ],
                    [
                        'attribute' => 'upp_dep_id',
                        'value' => function (\common\models\UserProjectParams $model) {
                            return $model->uppDep ? '' . $model->uppDep->dep_name . '' : '-';
                        },
                    ],
                    [
                        'class' => \common\components\grid\EmailSelect2Column::class,
                        'attribute' => 'upp_email_list_id',
                        'relation' => 'emailList',
                    ],
                    [
                        'class' => \common\components\grid\PhoneSelect2Column::class,
                        'attribute' => 'upp_phone_list_id',
                        'relation' => 'phoneList',
                    ],
                    [
                        'attribute' => 'upp_allow_general_line',
                        'format' => 'raw',
                        'value' => function (\common\models\UserProjectParams $model) {
                            if ($model->upp_allow_general_line) {
                                return '<i class="fa fa-check-square-o"></i>';
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'upp_vm_enabled',
                        'format' => 'raw',
                        'value' => function (\common\models\UserProjectParams $model) {
                            if ($model->upp_vm_enabled) {
                                return '<i class="fa fa-check-square-o"></i>';
                            }
                            return '-';
                        }
                    ],
                    [
                        'attribute' => 'upp_vm_user_status_id',
                        'value' => static function (UserProjectParams $model) {
                            return UserProjectParams::VM_USER_STATUS_LIST[$model->upp_vm_user_status_id] ?? null;
                        },
                    ],
                    [
                        'attribute' => 'upp_vm_id',
                        'value' => static function (UserProjectParams $model) {
                            return $model->upp_vm_id ? $model->voiceMail->uvm_name : null;
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{update} {delete}',
                        'controller' => 'user-project-params',
                        //'headerOptions' => ['width' => '20%', 'class' => '',],
                        'buttons' => [
                            'update' => static function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
                                    'class' => 'act-update-upp text-warning',
                                    'title' => 'Update Project params',
                                    'data-id' => $key,
                                    'data-pjax' => '0',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>

        <div class="user-voice-mail">
            <h4>User Voice Mail</h4>
            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-voice-mail']); ?>

            <p>
                <?php echo Html::a(
                    '<i class="glyphicon glyphicon-plus"></i> Add Voice Mail',
                    null,
                    [
                        'class' => 'btn btn-success btn-xs add-voice-mail',
                        'title' => 'Add Voice Mail',
                        'data-user_id' => $form->updaterUser->id,
                        'data-pjax' => '0',
                    ]
                ) ?>
            </p>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $userVoiceMailDataProvider,
                'columns' => [
                    'uvm_name',
                    'uvm_say_language',
                    'uvm_record_enable:booleanByLabel',
                    'uvm_max_recording_time',
                    'uvm_created_dt:byUserDateTime',
                    'uvm_updated_dt:byUserDateTime',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {update} {delete}',
                        'controller' => 'user-voice-mail',
                        'buttons' => [
                            'update' => static function ($key) {
                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
                                    'class' => 'update-user-voice-mail',
                                    'title' => 'Update Voice Mail',
                                    'data-id' => $key,
                                    'data-pjax' => '0',
                                ]);
                            },
                            'delete' => static function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                    'class' => 'delete-user-voice-mail',
                                    'title' => 'Delete Voice Mail',
                                    'data-pjax' => '0'
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php \yii\widgets\Pjax::end(); ?>
        </div>

        <?php if (Auth::can('user-product-type/list')) : ?>
            <div class="user-product-type">
                <h4>Product Type</h4>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-product-type']); ?>

                <?php if (Auth::can('user-product-type/create')) : ?>
                    <p>
                        <?php echo Html::a(
                            '<i class="glyphicon glyphicon-plus"></i> Add Product Type',
                            null,
                            [
                                'class' => 'btn btn-success btn-xs add-product-type',
                                'title' => 'Add Product Type',
                                'data-user_id' => $form->updaterUser->id,
                                'data-user_name' => $form->updaterUser->username,
                                'data-pjax' => '0',
                            ]
                        ) ?>
                    </p>
                <?php endif ?>

                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $userProductTypeDataProvider,
                    'columns' => [
                        [
                            'attribute' => 'upt_product_type_id',
                            'value' => static function (UserProductType $model) {
                                return $model->productType->pt_name;
                            },
                        ],
                        'upt_commission_percent',
                        'upt_product_enabled:booleanByLabel',
                        [
                            'class' => ActionColumn::class,
                            'template' => '{update} {delete}',
                            'controller' => 'user-product-type',
                            'buttons' => [
                                'update' => static function ($key) {
                                    if (Auth::can('user-product-type/update')) {
                                        $updateButton = Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
                                            'class' => 'update-product-type',
                                            'title' => 'Update Product Type',
                                            'data-id' => $key,
                                            'data-pjax' => '0',
                                        ]);
                                    } else {
                                        $updateButton = Html::tag('span', '', [
                                            'class' => 'glyphicon glyphicon-edit text-secondary',
                                            'title' => 'No access'
                                        ]);
                                    }
                                    return $updateButton;
                                },
                                'delete' => static function ($url) {
                                    if (Auth::can('user-product-type/delete')) {
                                        $deleteButton = Html::a(
                                            '<span class="glyphicon glyphicon-trash"></span>',
                                            $url,
                                            []
                                        );
                                    } else {
                                        $deleteButton = Html::tag('span', '', [
                                            'class' => 'glyphicon glyphicon-trash text-secondary',
                                            'title' => 'No access'
                                        ]);
                                    }
                                    return $deleteButton;
                                },
                            ],
                        ],
                    ],
                ]); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>

            <?php if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) : ?>
                <div class="user-failed-login">
                    <h5>User Failed Login</h5>

                    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid-user-failed']); ?>

                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $lastFailedLoginDataProvider,
                        'rowOptions' => static function (UserFailedLogin $UserFailedLogin, $index, $widget, $grid) {
                            if ($UserFailedLogin->ufl_created_dt > $UserFailedLogin->limitDateTime) {
                                return ['class' => 'danger'];
                            }
                        },
                        'columns' => [
                            'ufl_ip',
                            'ufl_ua',
                            'ufl_session_id',
                            'ufl_created_dt:byUserDateTime',
                        ],
                    ]); ?>
                    <?php \yii\widgets\Pjax::end(); ?>
                </div>

            <?php endif ?>

        <?php endif; ?>
    </div>

<?php
$js = <<<JS

    $('#modal-df').on('hidden.bs.modal', function () {
        $.pjax.reload({container:'#pjax-grid-upp', 'async': false});
        $.pjax.reload({container:'#pjax-grid-voice-mail', 'async': false});
        $.pjax.reload({container: "#pjax-grid-product-type", 'async': false});
    });

    $(document).on('click', '.act-update-upp', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        $.get('/user-project-params/update-ajax',
            {
                data: $(this).closest('tr').data('key')
            },
            function (data) {
                modal.find('.modal-title').html('Update Project params');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });

    $(document).on('click', '.act-create-upp', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        $.get('/user-project-params/create-ajax', {user_id: $(this).data('user_id')},
            function (data) {
                modal.find('.modal-title').html('Create Project params');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.add-product-type', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        let userName = $(this).data('user_name');
        $.get('/user-product-type/create-ajax', {user_id: $(this).data('user_id')},
            function (data) {
                modal.find('.modal-title').html('Add Product Type for ' + userName);
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.update-product-type', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-product-type/update-ajax', {data : $(this).closest('tr').data('key')},
            function (data) {
                modal.find('.modal-title').html('Update Product Type');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.update-user-voice-mail', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-voice-mail/ajax-update', {id: $(this).closest('tr').data('key')},
            function (data) {
                modal.find('.modal-title').html('Update Voice Mail');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });
    
    $(document).on('click', '.delete-user-voice-mail', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        
        if (confirm('Confirm deletion...'))
        $.get('/user-voice-mail/ajax-delete', {id: $(this).closest('tr').data('key')},
            function (data) {
                if (!data.error) {
                    $.pjax.reload({container:'#pjax-grid-voice-mail', 'async': false});
                } else {
                    createNotify('Error', data.message, 'error');
                }
            }
        );
    });
    
    $(document).on('click', '.add-voice-mail', function (e) { 
        e.preventDefault();
        let modal = $('#modal-df');
        
        $.get('/user-voice-mail/ajax-create', {uid: $(this).data('user_id')},
            function (data) {
                modal.find('.modal-title').html('Add Voice Mail');
                modal.find('.modal-body').html(data);
                modal.modal();
            }
        );
    });        
JS;
$this->registerJs($js);
