<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProviderNotes \yii\data\ActiveDataProvider
 * @var $modelLeadChecklist \common\models\LeadChecklist
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>
    <style>
        .x_title span{color: white;}
    </style>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-notes', 'enablePushState' => false, 'timeout' => 10000]) ?>
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-th-list"></i> Agent Notes</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?php if($lead->status === \common\models\Lead::STATUS_PROCESSING && ($lead->employee_id === Yii::$app->user->id || Yii::$app->user->identity->canRoles(['admin']))): ?>
                        <?php if(Yii::$app->request->get('act') === 'add-note-form'): ?>
                            <?/*=Html::a('<i class="fa fa-minus-circle success"></i> Refresh', ['lead/view', 'gid' => $lead->gid])*/?>
                        <?php else: ?>
                            <?=Html::a('<i class="fa fa-plus-circle success"></i> Add', ['lead/view', 'gid' => $lead->gid, 'act' => 'add-note-form'], ['id' => 'btn-notes-form2'])?>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProviderNotes,

                'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],
                'emptyText' => '<div class="text-center">Not found checklist tasks</div><br>',
                'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_list_notes', ['model' => $model, 'index' => $index]);
                },
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>


           <?php
/*            $checkListTypes = \common\models\LeadChecklistType::getList(true);

            $currentCheckList = $dataProviderNotes->getModels();

            foreach ($currentCheckList as $currentCheck) {
                if($currentCheck->lc_user_id === Yii::$app->user->id && isset($checkListTypes[$currentCheck->lc_type_id])) {
                    unset($checkListTypes[$currentCheck->lc_type_id]);
                }
            }
            */?>

            <?php /*if($checkListTypes):*/?><!--
                <table class="table table-bordered">
                    <?php /*foreach ($checkListTypes as $n => $checkListType):*/?>
                        <tr>
                            <td style="width: 40px">

                            </td>
                            <td>
                                <span class="fa fa-square-o warning"></span>
                                <?/*=Html::encode($checkListType)*/?>
                            </td>
                        </tr>
                    <?php /*endforeach; */?>
                </table>
            --><?php /*endif; */?>


            <?php /*if(Yii::$app->request->get('act') === 'add-note-form'): */?><!--

                <?php /*$form = ActiveForm::begin([
                    //'action' => ['index'],
                    'id' => 'notes-form',
                    'method' => 'post',
                    'options' => [
                        'data-pjax' => 1,
                    ],
                ]);

                echo $form->errorSummary($modelLeadChecklist);

                */?>

                <div class="row" id="div-notes-form">
                    <div class="col-md-7">
                        <?/*= $form->field($modelLeadChecklist, 'lc_notes')->textInput(['maxlength' => true]) */?>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <?/*= Html::submitButton('<i class="fa fa-plus"></i> Add option', ['class' => 'btn btn-success', 'id' => 'btn-submit-checklist']) */?>
                            <?/*=Html::a('<i class="fa fa-close"></i> Close', ['lead/view', 'gid' => $lead->gid], ['class' => 'btn btn-danger'])*/?>
                        </div>
                    </div>
                </div>

                <?php /*ActiveForm::end(); */?>
            --><?php /*endif; */?>

        </div>
    </div>
<?php yii\widgets\Pjax::end() ?>

<?php
$this->registerJs(
    '

        $(document).on("click","#btn-checklist-form", function() {
            $("#div-notes-form").show();
            $("#pjax-notes .x_content").show();
            return false;
        });


        $("#pjax-notes").on("pjax:start", function () {            
            $("#btn-submit-checklist").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-notes").on("pjax:end", function () {           
            $("#btn-submit-checklist").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-plus");

        });
    '
);
?>