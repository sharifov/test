<?php

/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProviderNotes \yii\data\ActiveDataProvider
 * @var $modelNote \common\models\Note
 */


use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>
<?php Pjax::begin(['id' => 'pjax-notes', 'enablePushState' => false, 'timeout' => 10000]) ?>
    <div class="x_panel">
        <div class="x_title" >
            <h2><i class="fa fa-sticky-note-o"></i> Notes (<?=$dataProviderNotes->query->count('message')?>)</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?php if ($lead->isProcessing() && ($lead->isOwner($user->id) || $user->isAdmin())) : ?>
                        <?php if (Yii::$app->request->get('act') === 'add-note-form') : ?>
                            <?php /*=Html::a('<i class="fa fa-minus-circle success"></i> Refresh', ['lead/view', 'gid' => $lead->gid])*/?>
                        <?php else : ?>
                            <?=Html::a('<i class="fa fa-plus-circle success"></i> Add', ['lead/view', 'gid' => $lead->gid, 'act' => 'add-note-form'], ['id' => 'btn-notes-form'])?>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none; margin-top: -10px;">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProviderNotes,
                'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],
                'emptyText' => '<div class="text-center">Not found any notes</div><br>',
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_list_notes', ['model' => $model, 'index' => $index, 'key' => $key]);
                },
                'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>

            <?php if (Yii::$app->request->get('act') === 'add-note-form') : ?>
                <?php $form = ActiveForm::begin([
                    'id' => 'notes-form',
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                    ],
                ]);
                echo $form->errorSummary($modelNote);
                ?>

                <div class="row" id="div-notes-form">
                    <div class="col-md-12"  style="padding-top:10px">
                        <?= $form->field($modelNote, 'message')->textarea(['rows' => 3]) ?>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <?= Html::submitButton('<i class="fa fa-plus"></i> Add Note', ['class' => 'btn btn-success', 'id' => 'btn-submit-note']) ?>
                            <?= Html::a('<i class="fa fa-close"></i> Close', ['lead/view', 'gid' => $lead->gid], ['class' => 'btn btn-danger'])?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
<?php Pjax::end() ?>

<?php
$this->registerJs(
    '
//        $(document).on("click","#btn-notes-form", function() {
//            $("#div-notes-form").show();
//            $("#pjax-notes .x_content").show();
//            
//             $([document.documentElement, document.body]).animate({
//                scrollTop: $("#div-notes-form").offset().top
//            }, 1000);
//                        
//            return false;
//        });

        $(document).on("beforeSubmit", "#notes-form", function() {
            $("#btn-submit-note").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-note i").attr("class", "fa fa-spinner fa-pulse fa-fw")
        });

        $("#pjax-notes").on("pjax:end", function () {           
            $("#btn-submit-note").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-note i").attr("class", "fa fa-plus");
            $("#pjax-notes .x_content").show();
        }); 
    '
);
?>