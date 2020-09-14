<?php

use frontend\helpers\OutHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatNote\entity\ClientChatNote;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Modal;

/***
 * @var ClientChat $clientChat
 * @var View $this
 * @var ClientChatNote $model
 * @var bool $showContent
 */

$showContent = $showContent ?? false;
?>

<?php Pjax::begin(['id' => 'pjax-notes', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="_rc-block-wrapper">
    <div class="x_panel">
        <div class="x_title">
            <h2>Chat notes (<?php echo count($clientChat->notes) ?>) </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="btn_toggle_form"><i class="fa fa-plus"></i> New Note</a>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>

        <div class="x_content" style="<?php echo $showContent ? '' : 'display: none;' ?>">
            <?php if ($clientChat->notes) :?>
                <?php foreach ($clientChat->notes as $note) :?>
                <div class="_cc-chat-notes-item">
                    <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                        <span class="_cc_agent_name"><?php echo $note->user ? Html::encode($note->user->username): '-' ?></span>
                        <span>
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
                        </span>
                    </div>
                    <div class="_cc_chat_note_date_item">
						<?= $note->ccn_created_dt ? Yii::$app->formatter->asDatetime(strtotime($note->ccn_created_dt)) : '' ?>
                    </div>
                    <div class="_cc_chat_note_item_content">
						<?= OutHelper::formattedChatNote($note) ?>
                    </div>
                </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>

        <?php Modal::begin(['id' => 'add-note-model',
            'title' => 'Add note',
            'size' => Modal::SIZE_DEFAULT
        ])?>

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

        <?php Modal::end()?>

    </div>
</div>
<?php Pjax::end() ?>






