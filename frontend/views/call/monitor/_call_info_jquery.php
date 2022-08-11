<?php

/**
 * @var Call $call
 * @var \src\guards\call\CallDisplayGuard $callGuard
 * @var \yii\web\View $this
 */

use common\models\Call;
use src\auth\Auth;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>

<div id="modal-call-info">
    <div class="row">
        <div class="col-md-12">
            <?= DetailView::widget([
                'model' => $call,
                'attributes' => [
                    'c_id',
                    'c_call_sid',
                    'c_parent_call_sid',
                    'c_conference_sid',

                    [
                        'attribute' => 'c_call_type_id',
                        'value' => static function (\common\models\Call $model) {
                            return $model->getCallTypeName();
                        },
                    ],
                    'c_from',
                    'c_to',
                    'c_call_status',
                    [
                        'attribute' => 'c_status_id',
                        'value' => static function (\common\models\Call $model) {
                            return $model->getStatusName();
                        },
                    ],
                    'c_forwarded_from',
                    'c_caller_name',
                    'c_call_duration',
                    [
                        'attribute' => 'c_client_id',
                        'value' => static function (Call $model) {
                            return  $model->c_client_id ?: '-';
                        },
                    ],
                    [
                        'label' => 'Department',
                        'attribute' => 'c_dep_id',
                        'value' => static function (Call $model) {
                            return $model->cDep ? $model->cDep->dep_name : '-';
                        },
                    ],
                    [
                        'label' => 'UserGroups',
                        //'attribute' => 'c_dep_id',
                        'value' => static function (Call $model) {
                            $userGroupList = [];
                            if ($model->cugUgs) {
                                foreach ($model->cugUgs as $userGroup) {
                                    $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                                }
                            }
                            return $userGroupList ? implode(' ', $userGroupList) : '-';
                        },
                        'format' => 'raw'
                    ],
                    'c_language_id',
                    'c_recording_disabled:booleanByLabel',
                ],
            ]) ?>
        </div>
        <div class="col-md-12">
            <div class="d-flex">
                <?php if (Auth::can('call/assignUsers', ['call' => $call])) : ?>
                    <button class="btn btn-success btn-sm add-user-btn"
                            data-add-user="true"
                            data-call-id="<?= $call->c_id; ?>" style="margin-right: 10px;">
                        <span><i class="fa fa-plus"> </i> add users</span>
                        <span><i class="fa fa-spin fa-spinner"></i></span>
                    </button>
                <?php endif; ?>
                <?php if ($callGuard->canDisplayJoinUserBtn($call, Auth::user())) : ?>
                    <div class="dropdown">
                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-phone"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" data-call-sid="<?= $call->c_call_sid; ?>">
                            <a class="dropdown-item conference-coach" href="#"
                               data-join-source="<?= Call::SOURCE_LISTEN ?>"
                               data-join-type="joinListen">Listen</a>
                            <a class="dropdown-item conference-coach" href="#"
                               data-join-source="<?= Call::SOURCE_COACH ?>"
                               data-join-type="joinCoach">Coach</a>
                            <a class="dropdown-item conference-coach" href="#"
                               data-join-source="<?= Call::SOURCE_BARGE ?>"
                               data-join-type="joinBarge">Barge</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
    .add-user-btn span:nth-child(2) {
        display: none;
    }
    .add-user-btn.loading span:nth-child(1) {
        display: none;
    }
    .add-user-btn.loading span:nth-child(2) {
        display: inline;
    }
CSS;
$this->registerCss($css);

// @TODO: next iteration move to vue-components
$js = <<<JS
    var selector = 'a[data-join-id]';
    $(selector).off();
    $(selector).on('click', function (e) {
        e.preventDefault();
        var dataType = $(this).attr('data-join-type');
        if (typeof PhoneWidget !== 'undefined' && typeof PhoneWidget[dataType] === 'function') {
            PhoneWidget[dataType]($(this).parent().attr('data-call-sid'));
        }
    });
    
    $('[data-add-user="true"]').one('click', function () {
        var self = $(this);
        if (self.hasClass('loading')) {
            return false;
        }
        self.addClass('loading');
        $.get('/call/get-users-for-call?id=' + self.attr('data-call-id'))
            .done(function( data ) {
                $('#modal-md').modal('hide');

                let modal = $('#modal-df');
                modal.find('.modal-title').html('Add users');
                modal.find('.modal-body').html(data);
                modal.modal('show');
          });
    });
JS;

$this->registerJs($js);
