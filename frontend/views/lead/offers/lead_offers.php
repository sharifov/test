<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $modelOffer \common\models\Offer
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

//$offer =

?>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-offers', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
    <div class="x_title">

        <h2><i class="fa fa-check-circle-o"></i> Offers</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

<!--                --><?//= \yii\widgets\ListView::widget([
//                    'dataProvider' => $dataProvider,
//
//                    'options' => [
//                        'tag' => 'table',
//                        'class' => 'table table-bordered',
//                    ],
//                    'emptyText' => '<div class="text-center">Not found checklist tasks</div><br>',
//                    'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
//                    'itemView' => function ($model, $key, $index, $widget) {
//                        return $this->render('_list_item', ['model' => $model, 'index' => $index]);
//                    },
//
//                    'itemOptions' => [
//                        //'class' => 'item',
//                        'tag' => false,
//                    ],
//
//                    /*'pager' => [
//                        'firstPageLabel' => 'first',
//                        'lastPageLabel' => 'last',
//                        'nextPageLabel' => 'next',
//                        'prevPageLabel' => 'previous',
//                        'maxButtonCount' => 3,
//                    ],*/
//
//                ]) ?>

    </div>
</div>
<?php yii\widgets\Pjax::end() ?>

<?php
//$this->registerJs(
//    '
//
//        $(document).on("click","#btn-checklist-form", function() {
//            $("#div-checklist-form").show();
//            $("#pjax-lead-checklist .x_content").show();
//            return false;
//        });
//
//
//        $("#pjax-lead-checklist").on("pjax:start", function () {
//            //$("#pjax-container").fadeOut("fast");
//            $("#btn-submit-checklist").attr("disabled", true).prop("disabled", true).addClass("disabled");
//            $("#btn-submit-checklist i").attr("class", "fa fa-spinner fa-pulse fa-fw")
//
//        });
//
//        $("#pjax-lead-checklist").on("pjax:end", function () {
//            //$("#pjax-container").fadeIn("fast");
//            $("#btn-submit-checklist").attr("disabled", false).prop("disabled", false).removeClass("disabled");
//            $("#btn-submit-checklist i").attr("class", "fa fa-plus");
//
//        });
//    '
//);
