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
                    <?=Html::a('<i class="fa fa-plus-circle success"></i> new Option', null, ['id' => 'btn-checklist-form'])?>
                <?//php endif; ?>
            </li>
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
                        'tag' => 'div',
                        'class' => 'list-wrapper',
                        'id' => 'list-wrapper',
                    ],
                    'emptyText' => '<div class="text-center">Not found checklist tasks</div><br>',
                    'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_list_item', ['model' => $model]);
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


        <?php $form = ActiveForm::begin([
            //'action' => ['index'],
            'id' => 'checklist-form',
            'method' => 'post',
            'options' => [
                'data-pjax' => 1,
            ],
        ]);

        echo $form->errorSummary($modelLeadChecklist);

        ?>

        <div class="row" style="display: <?=$modelLeadChecklist->hasErrors() ? 'block' : 'none'?>" id="div-checklist-form">
            <div class="col-md-12">
                <?//= $form->field($modelLeadChecklist, 'lce_request_text')->textarea(['rows' => 8, 'id' => 'lce_request_text'])->label('Request Message') ?>
            </div>

            <div class="col-md-12">
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Add option', ['class' => 'btn btn-success', 'id' => 'btn-submit-checklist']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<?php yii\widgets\Pjax::end() ?>

<?php
$this->registerJs(
    '

        $(document).on("click","#btn-checklist-form", function() {
            $("#div-checklist-form").show();
            $("#pjax-lead-checklist .x_content").show();

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#checklist-form").offset().top
            }, 1000);

            return false;
        });


        $("#pjax-lead-checklist").on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
            $("#btn-submit-checklist").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-lead-checklist").on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");

            $("#btn-submit-checklist").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-checklist i").attr("class", "fa fa-plus");

        });
    '
);
?>