<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $modelLeadChecklist \common\models\LeadChecklist
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<style>
    .x_title span{color: white;}
</style>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-checklist', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
    <div class="x_title">

        <h2><i class="fa fa-check-circle-o"></i> Check List block</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>

            </li>
            <li>
                <?//=Html::a('<i class="fa fa-comment"></i>', ['lead/view', 'gid' => $lead->gid, 'act' => 'call-expert-message'], ['class' => ''])?>
                <?//php if(!$lastModel || $lastModel->lce_status_id === LeadCallExpert::STATUS_DONE):?>

                <?php if($lead->status === \common\models\Lead::STATUS_PROCESSING && ($lead->employee_id === Yii::$app->user->id || Yii::$app->user->identity->canRoles(['admin']))): ?>
                    <?php if(Yii::$app->request->get('act') === 'add-checklist-form'): ?>
                        <?/*=Html::a('<i class="fa fa-minus-circle success"></i> Refresh', ['lead/view', 'gid' => $lead->gid])*/?>
                    <?php else: ?>
                        <?=Html::a('<i class="fa fa-plus-circle success"></i> Add', ['lead/view', 'gid' => $lead->gid, 'act' => 'add-checklist-form'], ['id' => 'btn-checklist-form2'])?>
                    <?php endif; ?>
                <?php endif; ?>

                <?//php endif; ?>
            </li>
            <?php if(Yii::$app->user->identity->canRoles(['admin'])):?>
                <li>
                    <?=Html::a('<i class="fa fa-search warning"></i> Details', ['lead-checklist/index', 'LeadChecklistSearch[lc_lead_id]' => $lead->id], ['data-pjax' => 0, 'target' => '_blank'])?>
                </li>
            <?php endif;?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>

            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,

                    'options' => [
                        'tag' => 'table',
                        'class' => 'table table-bordered',
                    ],
                    'emptyText' => '<div class="text-center">Not found checklist tasks</div><br>',
                    'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_list_item', ['model' => $model, 'index' => $index]);
                    },

                    'itemOptions' => [
                        //'class' => 'item',
                        'tag' => false,
                    ],

                    /*'pager' => [
                        'firstPageLabel' => 'first',
                        'lastPageLabel' => 'last',
                        'nextPageLabel' => 'next',
                        'prevPageLabel' => 'previous',
                        'maxButtonCount' => 3,
                    ],*/

                ]) ?>


        <?php if(Yii::$app->request->get('act') === 'add-checklist-form'): ?>

        <?php $form = ActiveForm::begin([
            //'action' => ['index'],
            'id' => 'checklist-form',
            'method' => 'post',
            'options' => [
                'data-pjax' => 1,
            ],
        ]);

        echo $form->errorSummary($modelLeadChecklist);

        $checkListTypes = \common\models\LeadChecklistType::getList(true);

        $currentCheckList = $dataProvider->getModels();

        foreach ($currentCheckList as $currentCheck) {
            if(isset($checkListTypes[$currentCheck->lc_type_id])) {
                unset($checkListTypes[$currentCheck->lc_type_id]);
            }
        }

        ?>

        <div class="row" id="div-checklist-form">
            <div class="col-md-5">
                <?= $form->field($modelLeadChecklist, 'lc_type_id')->dropDownList($checkListTypes, ['prompt' => '--- select task ---']) ?>
            </div>
            <div class="col-md-7">
                <?= $form->field($modelLeadChecklist, 'lc_notes')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-12">
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Add option', ['class' => 'btn btn-success', 'id' => 'btn-submit-checklist']) ?>
                    <?=Html::a('<i class="fa fa-close"></i> Close', ['lead/view', 'gid' => $lead->gid], ['class' => 'btn btn-danger'])?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php endif; ?>

    </div>
</div>
<?php yii\widgets\Pjax::end() ?>

<?php
$this->registerJs(
    '

        $(document).on("click","#btn-checklist-form", function() {
            $("#div-checklist-form").show();
            $("#pjax-lead-checklist .x_content").show();
            return false;
        });


        $("#pjax-lead-checklist").on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
            $("#btn-submit-checklist").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-lead-checklist").on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            $("#btn-submit-checklist").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-plus");

        });
    '
);
?>