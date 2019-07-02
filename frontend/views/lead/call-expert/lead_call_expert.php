<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $modelLeadCallExpert LeadCallExpert
 */


/*$is_manager = false;
if(Yii::$app->user->identity->canRole('admin') || Yii::$app->user->identity->canRole('supervision')) {
    $is_manager = true;
}*/

use common\models\LeadCallExpert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

//$title = (!$leadForm->getLead()->called_expert) ? 'Call Expert' : ' Expert Called';
/*$options = (!$leadForm->getLead()->called_expert) ? [
    'class' => 'btn btn-success btn-with-icon',
    'id' => 'btn-call-expert',
    'data-url' => Url::to(['lead/call-expert', 'id' => $leadForm->getLead()->id])
] : [
    'class' => 'btn btn-default btn-with-icon',
];*/
//echo Html::a('<span class="btn-icon"><i class="fa fa-bell"></i></span> <span class="btn-text">Call</span>', null, $options);

?>
<style>
    .x_title span{color: white;}
</style>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-call-expert', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
    <div class="x_title">

        <?php
        /** @var TYPE_NAME $lastModel */
        $lastModel = null;
        $label = '';
        if($dataProvider->count > 0) {
            //$lastKey = array_key_last($dataProvider->models); php 7.3
            $lastKey = array_keys($dataProvider->models)[count($dataProvider->models)-1];
            if(isset($dataProvider->models[$lastKey])) {
                $lastModel = $dataProvider->models[$lastKey];
            }

            if($lastModel) {
                if($lastModel->lce_status_id === LeadCallExpert::STATUS_PENDING) {
                    $label = 'warning';
                } else if($lastModel->lce_status_id === LeadCallExpert::STATUS_DONE) {
                    $label = 'success';
                } else if($lastModel->lce_status_id === LeadCallExpert::STATUS_PROCESSING) {
                    $label = 'info';
                }
            }

        }
        ?>&nbsp;

        <h2><i class="fa fa-bell-o <?=$label?>"></i> Call Expert (<?=$dataProvider->count?>)

            <?php
                if($lastModel) {
                    echo ' : ' . $lastModel->getStatusLabel() . '';
                }
            ?>

        </h2>

        <ul class="nav navbar-right panel_toolbox">
            <li>

            </li>
            <li>
                <?//=Html::a('<i class="fa fa-comment"></i>', ['lead/view', 'gid' => $lead->gid, 'act' => 'call-expert-message'], ['class' => ''])?>
                <?php if(!$lastModel || $lastModel->lce_status_id === LeadCallExpert::STATUS_DONE):?>
                    <?=Html::a('<i class="fa fa-plus-circle success"></i> new Call', null, ['id' => 'btn-call-expert-form'])?>
                <?php endif; ?>
            </li>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
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
    <div class="x_content" style="display: <?=Yii::$app->request->isPjax ? 'block' : 'none';?>">

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,

                    'options' => [
                        'tag' => 'div',
                        'class' => 'list-wrapper',
                        'id' => 'list-wrapper',
                    ],
                    'emptyText' => '<div class="text-center">Not found expert messages</div><br>',
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
            'id' => 'call-expert-form',
            'method' => 'post',
            'options' => [
                'data-pjax' => 1,
            ],
        ]);

        echo $form->errorSummary($modelLeadCallExpert);

        ?>

        <div class="row" style="display: <?=$modelLeadCallExpert->hasErrors() ? 'block' : 'none'?>" id="div-call-expert-form">
            <div class="col-md-12">
                <?= $form->field($modelLeadCallExpert, 'lce_request_text')->textarea(['rows' => 8, 'id' => 'lce_request_text'])->label('Request Message') ?>
            </div>

            <div class="col-md-12">
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Create call Expert', ['class' => 'btn btn-success', 'id' => 'btn-submit-call-expert']) ?>
                    <?= Html::button('<i class="fa fa-copy"></i>', ['title' => 'Past from Lead Notes', 'class' => 'btn btn-primary', 'onclick' => '$("#lce_request_text").val($("#lead-notes_for_experts").val())']) ?>
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
        $(document).on("change",".ch_task", function() {
            $.pjax.reload({container: containerId, push: false, replace: false, timeout: 5000, data: {date: taskDate, task_id: taskId, lead_id: taskLeadId, user_id: taskUserId}});
        });


        $(document).on("click",".link2quote", function() {
            var uid = $(this).data("uid");

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#quote-" + uid).offset().top
            }, 500);

            for(i = 0; i < 4; i ++) {
                $("#quote-" + uid).fadeTo(300, 0.2).fadeTo(300, 1.0);
            }

        });


        $(document).on("click","#btn-call-expert-form", function() {
            $("#div-call-expert-form").show();
            $("#pjax-lead-call-expert .x_content").show();

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#call-expert-form").offset().top
            }, 1000);

            return false;
        });


        $("#pjax-lead-call-expert").on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
            $("#btn-submit-call-expert").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-lead-call-expert").on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");

            $("#btn-submit-call-expert").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-plus");

        });
    '
);
?>