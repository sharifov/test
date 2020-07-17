<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatNote\entity\ClientChatNote;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/***
 * @var ClientChat $clientChat
 * @var View $this
 * @var ClientChatNote $model
 */
?>

<?php Pjax::begin(['id' => 'pjax-notes', 'enablePushState' => false, 'timeout' => 10000]) ?>
    <div class="_rc-client-chat-info-wrapper mt-2" >
        <div class="_rc-block-wrapper">
            <h3 style="margin: 0;">Chat notes</h3>
            <div class="d-flex align-items-center justify-content-center">
                <?php echo Html::button('<i class="fa fa-plus"></i>', [
                    'class' => 'btn btn-success btn_toggle_form',
                    'title' => 'Show form for add note',
                ]) ?>
            </div>
        </div>

        <?php if ($clientChat->notes) :?>
            <?php foreach ($clientChat->notes as $note) :?>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>
                            <div class="float-right">
                                <?php $class = $note->ccn_deleted ? 'fa-reply' : 'fa-remove' ?>
                                <?php $textAlert = $note->ccn_deleted ? 'recover' : 'delete' ?>
                                <?= Html::a('<i class="fa ' . $class . '"></i>',
                                    ['client-chat/delete-note', 'ccn_id' => $note->ccn_id, 'cch_id' => $clientChat->cch_id],
                                    [
                                        'class' => 'text-secondary',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to ' . $textAlert . ' this note?',
                                            'method' => 'post',
                                        ],
                                        'data-pjax'=> 1,
                                    ]) ?>
                            </div>
                            <i class="fa fa-user"></i>
                                <?php echo $note->user ? Html::encode($note->user->username): '-' ?>,
                            <i class="fa fa-calendar"></i>
                                <?php echo $note->ccn_created_dt ? Yii::$app->formatter->asDatetime(strtotime($note->ccn_created_dt)) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php if($note->ccn_deleted) :?><s><?php endif?>
                                <?php echo $note->ccn_note ? nl2br(Html::encode($note->ccn_note)) : '-' ?>
                            <?php if($note->ccn_deleted) :?></s><?php endif?>
                        </td>
                    </tr>
                </table>

            <?php endforeach ?>
        <?php endif ?>

        <div class="box_note_form" style="padding: 10px; display: none;">
            <?php $form = ActiveForm::begin([
                    'id' => 'note-form',
                    'action' => ['client-chat/create-note', 'cch_id' => $clientChat->cch_id],
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                    ],
                ]);
                $model->ccn_chat_id = $clientChat->cch_id;
            ?>
                <div class="row" >
                    <?= $form->field($model, 'ccn_chat_id')->hiddenInput()->label(false) ?>

                    <div class="col-md-12">
                        <?= $form->field($model, 'ccn_note')->textarea(['rows' => 3]) ?>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <?php echo Html::submitButton('<i class="fa fa-plus"></i> Add Note', [
                                'class' => 'btn btn-success', 'id' => 'btn-submit-note'
                            ]) ?>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
<?php Pjax::end() ?>

<?php
$js = <<<JS

$(document).on('click', '.btn_toggle_form', function (e) {
    $('.box_note_form').toggle();
    $(this).toggleClass('btn-secondary').toggleClass('btn-success');
});

$("#pjax-notes").on("pjax:start", function () {            
    $("#btn-submit-note").prop("disabled", true).addClass("disabled");
    $("#btn-submit-note i").attr("class", "fa fa-cog fa-spin fa-fw");
});

$("#pjax-notes").on("pjax:end", function () {           
    $("#btn-submit-note").prop("disabled", false).removeClass("disabled");
    $("#btn-submit-note i").attr("class", "fa fa-plus");
}); 

JS;
$this->registerJs($js);


