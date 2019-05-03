<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $modelLeadCallExpert LeadCallExpert
 */


/*$is_manager = false;
if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) {
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

<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-call-expert', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-bell-o"></i> Call Expert Block </h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                &nbsp;
            </li>
            <li>
                <?//=Html::a('<i class="fa fa-comment"></i>', ['lead/view', 'gid' => $lead->gid, 'act' => 'call-expert-message'], ['class' => ''])?>
                <?=Html::a('<i class="fa fa-comment"></i>', '#', ['class' => '', 'id' => 'btn-call-expert-form'])?>
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
    <div class="x_content" style="display: block;">


        <?/*<h1><?=random_int(1, 100)?></h1>*/ ?>

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
            //'id' => 'email-preview-form',
            'method' => 'post',
            'options' => [
                'data-pjax' => 1,
            ],
        ]);

        echo $form->errorSummary($modelLeadCallExpert);

        ?>

        <div class="row" style="display: none" id="div-call-expert-form">
            <div class="col-md-12">
                <?= $form->field($modelLeadCallExpert, 'lce_request_text')->textarea(['rows' => 8, 'id' => 'lce_request_text'])->label('Request Message') ?>
            </div>

            <div class="col-md-12">
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Create call Expert', ['class' => 'btn btn-success']) ?>
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
        
        $(document).on("click","#btn-call-expert-form", function() {
            $("#div-call-expert-form").toggle();
            return false;
        });
                

        $(document).on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
        });

        $(document).on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");
        });
    '
);
?>