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
 * @var bool $showContent
 */

$showContent = $showContent ?? false;
?>

<?php Pjax::begin(['id' => 'pjax-notes', 'enablePushState' => false, 'timeout' => 10000]) ?>
    <div class="x_panel" style="margin-top: 10px;">
        <div class="x_title">
            <h2><i class="fa fa-sticky-note-o"></i>  Chat notes (<?php echo count($clientChat->notes) ?>) </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?php echo Html::button('<i class="fa fa-plus"></i>', [
                        'class' => 'btn btn-success btn_toggle_form',
                        'title' => 'Show form for add note',
                        'style' => 'margin-right: 5px;'
                    ]) ?>
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
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>
                                <div class="float-right" >
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
        </div>

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


