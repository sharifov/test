<?php

use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatData $model */
/* @var string $error */

$this->title = 'Manage User Client Chat Data: ' . $model->uccd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uccd_id, 'url' => ['view', 'id' => $model->uccd_id]];
$this->params['breadcrumbs'][] = 'Manage';
?>
<div class="user-client-chat-data-update">
    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <h5>User: <?php echo $model->uccdEmployee->username ?></h5>

            <div class="row">
                <div class="col-md-8">
                    <?php if (Auth::can('/user-client-chat-data/activate')) : ?>
                        <?php
                        $optionsActivate = [
                            'id' => 'activate_to_rc',
                            'class' => 'btn btn-success rc_btns',
                            'data-inner' => '',
                            'data-class' => 'btn btn-success rc_btns',
                            'data-pjax' => '0',
                            'data-user_id' => $model->getEmployeeId(),
                            'title' => 'Activate user',
                        ];
                        if ($model->isActive() && $model->isRegisteredInRc()) {
                            $optionsActivate['disabled'] = 'disabled';
                            $optionsActivate['style'] = 'opacity: 0.3';
                        }
                        echo Html::button('<i class="fa fa-check"></i>  Activate user', $optionsActivate);
                        ?>
                    <?php endif ?>

                    <?php if (Auth::can('/user-client-chat-data/deactivate')) : ?>
                        <?php
                        $optionsDeactivate = [
                            'id' => 'deactivate_from_rc',
                            'class' => 'btn btn-warning rc_btns',
                            'data-inner' => '',
                            'data-class' => 'btn btn-warning rc_btns',
                            'data-pjax' => '0',
                            'data-user_id' => $model->getEmployeeId(),
                            'title' => 'Deactivate user',
                        ];
                        if (!$model->isActive() && $model->isRegisteredInRc()) {
                            $optionsDeactivate['disabled'] = 'disabled';
                            $optionsDeactivate['style'] = 'opacity: 0.3';
                        }
                        echo Html::button('<i class="fa fa-lock"></i>  Deactivate user', $optionsDeactivate);
                        ?>
                    <?php endif ?>

                    <?php if (Auth::can('/user-client-chat-data/delete')) : ?>
                        <?php
                        $optionsDelete = [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete the user and all his data (rooms, messages etc.)?',
                                'method' => 'post',
                            ],
                            'id' => 'un_register_from_rc',
                            'data-inner' => '',
                            'data-class' => 'btn btn-danger rc_btns',
                            'data-pjax' => '0',
                            'title' => 'Remove all data',
                        ];
                        if (!$model->isRegisteredInRc()) {
                            $optionsDelete['disabled'] = 'disabled';
                            $optionsDelete['style'] = 'opacity: 0.3';
                        }
                        echo Html::a('<i class="fa fa-times"></i>  Delete from Rocket Chat', ['delete', 'id' => $model->uccd_id], $optionsDelete);
                        ?>
                    <?php endif ?>

                    <?php if ($model->isRegisteredInRc()) : ?>
                        <?php if (Auth::can('/user-client-chat-data/refresh-rocket-chat-user-token')) : ?>
                            <button class="btn btn-success refresh_token" data-user-id="<?= $model->getEmployeeId() ?>">Refresh Token</button>
                        <?php endif ?>
                        <?php if (Auth::can('/user-client-chat-data/validate-rocket-chat-credential')) : ?>
                            <button class="btn btn-success validate_credential" data-user-id="<?= $model->getEmployeeId() ?>">Validate credential</button>
                        <?php endif ?>
                    <?php endif; ?>
                </div>
            </div>
            <br />

            <?php
            if ($model->isActive()) {
                $activeStatusIco = '<i class="fa fa-check" title="User is active"></i>';
            } else {
                $activeStatusIco = '<i class="fa fa-lock" title="User not active"></i>';
            }
            ?>

            <h5>
                Rocket Chat Credentials &nbsp;
                <span id="rc_active_status"><?php echo $activeStatusIco ?></span>
            </h5>

            <div class="row ">
                <div class="col-md-6">

                    <?php if ($error) : ?>
                        <div class="alert alert-error" role="alert">
                            <?php echo $error ?>
                        </div>
                    <?php endif ?>

                    <?php $form = ActiveForm::begin([
                        'id' => sprintf('%s-ID', $model->formName()),
                    ]) ?>

                        <?php $form->errorSummary($model) ?>

                        <div class="form-group well">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'uccd_name')
                                        ->textInput(['maxlength' => true, 'title' => 'Display name of the user', 'id' => 'rcName']) ?>

                                    <?php echo $form->field($model, 'uccd_username')
                                        ->textInput(['maxlength' => true, 'title' => 'Username for the user', 'id' => 'rcUserName']) ?>

                                    <?php echo $form->field($model, 'uccd_password')->passwordInput(['autocomplete' => 'off']) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'uccd_rc_user_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                                    <?= $form->field($model, 'uccd_auth_token')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                                    <?= $form->field($model, 'uccd_token_expired')->textInput(['disabled' => true]) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo
                                    Html::submitButton(
                                        '<i class="fa fa-rocket"></i> Update and sync to Rocket Chat',
                                        ['class' => 'btn btn-success']
                                    )
                                ?>
                            </div>
                        </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$validateCredentialRcUrl = Url::to(['/user-client-chat-data/validate-rocket-chat-credential']);
$refreshTokenRcUrl = Url::to(['/user-client-chat-data/refresh-rocket-chat-user-token']);
$activateToRcUrl = Url::to(['/user-client-chat-data/activate']);
$deactivateFromRcUrl = Url::to(['/user-client-chat-data/deactivate']);

$js = <<<JS
    
    $(document).on('click', '#activate_to_rc', function (e) { 
        e.preventDefault();
        
        let btn = $(this);
        loadingBtn(btn, true);    
        
        $.ajax({
            url: '{$activateToRcUrl}',
            type: 'POST',
            data: {id: btn.data('user_id')},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {                
                if (dataResponse.message.length) {
                    createNotify('Success', dataResponse.message, 'success');
                } else {
                    createNotify('Success', 'The request was successful', 'success');
                }
                $(btn).prop('disabled', true).css('opacity', '0.3'); 
                $('#deactivate_from_rc').prop('disabled', false).css('opacity', '1.0');   
                $('#rc_active_status').html('<i class="fa fa-check" title="User is active"></i>');             
            } else {
                createNotify('Error', dataResponse.message, 'error');
                $(btn).prop('disabled', false).css('opacity', '1.0'); 
            } 
            loadingBtn(btn, false);      
        })
        .fail(function(error) {
            createNotify('Error', 'Server error. Please try again later', 'error');
            loadingBtn(btn, false);
            $(btn).prop('disabled', false).css('opacity', '1.0');            
        })
        .always(function() {                
        });        
    });
    
    $(document).on('click', '#deactivate_from_rc', function (e) { 
        e.preventDefault();
        
        if(!confirm('Are you sure you want to deactivate the user from chats?')) {
            return false;
        }
        
        let btn = $(this);
        loadingBtn(btn, true);    
        
        $.ajax({
            url: '{$deactivateFromRcUrl}',
            type: 'POST',
            data: {id: btn.data('user_id')},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {                
                if (dataResponse.message.length) {
                    createNotify('Success', dataResponse.message, 'success');
                } else {
                    createNotify('Success', 'The request was successful', 'success');
                }
                $('#activate_to_rc').prop('disabled', false).css('opacity', '1.0');
                btn.prop('disabled', true).css('opacity', '0.3');
                $('#rc_active_status').html('<i class="fa fa-lock" title="User not active"></i>');       
            } else {
                createNotify('Error', dataResponse.message, 'error');
                btn.prop('disabled', false).css('opacity', '1.0');
            } 
            loadingBtn(btn, false);              
        })
        .fail(function(error) {
            loadingBtn($('#deactivate_from_rc'), false);
            btn.prop('disabled', false).css('opacity', '1.0');
            createNotify('Error', 'Server error. Please try again later', 'error');
        })
        .always(function() {                
        });        
    });
    
    function loadingBtn(btnObj, loading) {
        if (loading === true) {
            btnObj.removeClass()
                .addClass('btn btn-default')
                .html('<i class="fa fa-cog fa-spin"></i> Loading...')
                .prop('disabled', true);
        } else {            
            btnObj.removeClass()
                .addClass(btnObj.data('class'))
                .html(btnObj.data('inner'));
        }  
    }
    
    $(document).ready( function () {
        $(".rc_btns").each(function( index ) {
            $(this).data('inner', $(this).html());            
        });
    });    
    
    $(document).on('click', '.validate_credential', function(e) {
        e.preventDefault();
        let btn = $(this); 
        let userId = btn.attr('data-user-id');
        btn.html('<i class="fa fa-spinner fa-spin"></i> Validate credential');
        btn.attr('disabled', true);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '{$validateCredentialRcUrl}',
            data: {
                id: userId
            }
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Validate Rocket Chat credential', data.message, 'error');
                return;
            }
            createNotify('Validate Rocket Chat credential', 'Success', 'success');
        })  
         .fail(function (xhr, textStatus, errorThrown) {
             createNotify('Validate Rocket Chat credential', xhr.responseText, 'error');
         })
        .always(function () {
            btn.html('Validate credential');
            btn.attr('disabled', false);
        });
    });
    
    $(document).on('click', '.refresh_token', function(e) {
        e.preventDefault();
        let btn = $(this); 
        let userId = btn.attr('data-user-id');
        btn.html('<i class="fa fa-spinner fa-spin"></i> Refresh Token');
        btn.attr('disabled', true);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '{$refreshTokenRcUrl}',
            data: {
                id: userId
            }
        })
        .done(function(data) {
            if (data.error) {
                createNotify('Refresh Rocket Chat User Token', data.message, 'error');
                return;
            }
            createNotify('Refresh Rocket Chat User Token', 'Success', 'success');
            setTimeout(() => {window.location.reload();}, 500);
        })  
         .fail(function (xhr, textStatus, errorThrown) {
             createNotify('Refresh Rocket Chat User Token', xhr.responseText, 'error');
         })
        .always(function () {
            btn.html('Refresh Token');
            btn.attr('disabled', false);
        });
    });
    
JS;
$this->registerJs($js);
