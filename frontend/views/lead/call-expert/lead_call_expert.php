<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 */


/*$is_manager = false;
if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) {
    $is_manager = true;
}*/

use yii\helpers\Html;
use yii\widgets\Pjax; ?>


<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-bell-o"></i> Call Expert Block</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">

        <?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-call-expert' ,'enablePushState' => false]) ?>
        <?/*<h1><?=random_int(1, 100)?></h1>*/ ?>

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,

                    'options' => [
                        'tag' => 'div',
                        'class' => 'list-wrapper',
                        'id' => 'list-wrapper',
                    ],
                    'emptyText' => '<div class="text-center">Not found expert messages</div><br>',
                    'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                    'itemView' => function ($model, $key, $index, $widget) use ($dataProvider) {
                        return $this->render('_list_item',['model' => $model, 'dataProvider' => $dataProvider]);
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

        <?php yii\widgets\Pjax::end() ?>
    </div>
</div>

<?php
$this->registerJs(
    '
        $(document).on("change",".ch_task", function() {
            
            $.pjax.reload({container: containerId, push: false, replace: false, timeout: 5000, data: {date: taskDate, task_id: taskId, lead_id: taskLeadId, user_id: taskUserId}});
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