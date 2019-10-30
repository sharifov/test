<?php
use yii\widgets\Pjax;
/**
 *  @var $profitDataProvider yii\data\SqlDataProvider
 *  @var $soldDataProvider yii\data\SqlDataProvider
 *  @var $profitPerPaxDataProvider yii\data\SqlDataProvider
 *  @var $tipsDataProvider yii\data\SqlDataProvider
 *  @var $boardsSettings array
 *  @var $showRatingSettings boolean
 */
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/favicon.ico']);

?>

<?= $this->render('ratings/_agent_ratings_settings')?>

<div id="agent-leader-board" class="col-md-12">
    <div class="row">
        <?php if ($boardsSettings['finalProfit']) : ?>
        <div id="finalProfit" class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-users"></i> Top - AGENT By FINAL PROFIT</div>
                <div class="panel-body">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $profitDataProvider,
                        'emptyText' => '<div class="text-center">Not found online users</div><br>',
                        'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                        'itemView' => function ($model, $key, $index, $widget) {
                            if ($index <= 29) {
                                return $this->render('ratings/_list_item_ratings', ['model' => $model, 'index' => $index]);
                            }
                        },
                        'itemOptions' => [
                            //'class' => 'item',
                            //'tag' => false,
                        ],
                    ])?>
                </div>
            </div>
        </div>

        <?php endif;?>

        <?php if ($boardsSettings['soldLeads']) : ?>
        <div id="soldLeads" class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-users"></i> Top - AGENT by SOLD LEADS </div>
                <div class="panel-body">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $soldDataProvider,
                        'emptyText' => '<div class="text-center">Not found online users</div><br>',
                        'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                        'itemView' => function ($model, $key, $index, $widget) {
                            if ($index <= 29) {
                                return $this->render('ratings/_list_item_ratings', ['model' => $model, 'index' => $index]);
                            }
                        },
                        'itemOptions' => [
                            //'class' => 'item',
                            //'tag' => false,
                        ],
                    ])?>
                </div>
            </div>
        </div>
        <?php endif;?>

        <?php if ($boardsSettings['profitPerPax']) : ?>
        <div id="profitPerPax" class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-users"></i> Top - AGENT by PROFIT PER PAX </div>
                <div class="panel-body">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $profitPerPaxDataProvider,
                        'emptyText' => '<div class="text-center">Not found online users</div><br>',
                        'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                        'itemView' => function ($model, $key, $index, $widget) {
                            if ($index <= 29) {
                                return $this->render('ratings/_list_item_ratings', ['model' => $model, 'index' => $index]);
                            }
                        },
                        'itemOptions' => [
                            //'class' => 'item',
                            //'tag' => false,
                        ],
                    ])?>
                </div>
            </div>
        </div>
        <?php endif;?>

        <?php if ($boardsSettings['tips']) : ?>
        <div id="tips" class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-users"></i> Top - AGENT By TIPS</div>
                <div class="panel-body">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $tipsDataProvider,
                        'emptyText' => '<div class="text-center">Not found online users</div><br>',
                        'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                        'itemView' => function ($model, $key, $index, $widget) {
                            if ($index <= 29) {
                                return $this->render('ratings/_list_item_ratings', ['model' => $model, 'index' => $index]);
                            }
                        },
                        'itemOptions' => [
                            //'class' => 'item',
                            //'tag' => false,
                        ],
                    ])?>
                </div>
            </div>
        </div>
        <?php endif;?>

    </div>
</div>
