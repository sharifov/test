<?php
use yii\widgets\Pjax;
/**
 *  @var $profitDataProvider yii\data\SqlDataProvider
 *  @var $soldDataProvider yii\data\SqlDataProvider
 *  @var $profitPerPaxDataProvider yii\data\SqlDataProvider
 *  @var $tipsDataProvider yii\data\SqlDataProvider
 *  @var $conversionDataProvider yii\data\SqlDataProvider
 *  @var $teamsProfitDataProvider yii\data\SqlDataProvider
 *  @var $avgSoldLeadsDataProvider yii\data\SqlDataProvider
 *  @var $avgProfitPerPax yii\data\SqlDataProvider
 *  @var $avgProfitPerAgent yii\data\SqlDataProvider
 *  @var $teamConversion yii\data\SqlDataProvider
 *  @var $agentsBoardsSettings array
 *  @var $teamsBoardsSettings array
 *  @var $showRatingSettings boolean
 */
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/favicon.ico']);

?>

<?= $this->render('ratings/_agent_ratings_settings')?>

<div id="agent-leader-board" class="col-md-12">
    <div class="row">
        <?php if ($agentsBoardsSettings['finalProfit']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - AGENT By FINAL PROFIT</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $profitDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
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

        <?php if ($agentsBoardsSettings['soldLeads']) : ?>
            <div id="soldLeads" class="col" style="width: 20%;">
                <div class="card card-default" >
                    <div class="card-header"><i class="fa fa-users"></i> Top - AGENT by SOLD LEADS </div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $soldDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
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

        <?php if ($agentsBoardsSettings['profitPerPax']) : ?>
            <div id="profitPerPax" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - AGENT by AVERAGE PROFIT PER PAX </div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $profitPerPaxDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
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

        <?php if ($agentsBoardsSettings['tips']) : ?>
            <div id="tips" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - AGENT By TIPS</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $tipsDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
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

        <?php if ($agentsBoardsSettings['leadConversion']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - AGENT By NEW LEAD CONVERSION</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $conversionDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
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

    <div class="row mt-3">
        <?php if ($teamsBoardsSettings['teamsProfit']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - Team By PROFIT</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $teamsProfitDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
                                    return $this->render('ratings/_team_list_item_ratings', ['model' => $model, 'index' => $index]);
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

        <?php if ($teamsBoardsSettings['teamsSoldLeads']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - Average Sold Leads per Agent</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $avgSoldLeadsDataProvider,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
                                    return $this->render('ratings/_team_list_item_ratings', ['model' => $model, 'index' => $index]);
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

        <?php if ($teamsBoardsSettings['teamsProfitPerPax']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - Average Profit Per Pax</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $avgProfitPerPax,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
                                    return $this->render('ratings/_team_list_item_ratings', ['model' => $model, 'index' => $index]);
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

        <?php if ($teamsBoardsSettings['teamsProfitPerAgent']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - Average Profit Per Agent</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $avgProfitPerAgent,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
                                    return $this->render('ratings/_team_list_item_ratings', ['model' => $model, 'index' => $index]);
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

        <?php if ($teamsBoardsSettings['teamsConversion']) : ?>
            <div id="finalProfit" class="col" style="width: 20%;">
                <div class="card card-default">
                    <div class="card-header"><i class="fa fa-users"></i> Top - Team by Conversion</div>
                    <div class="card-body">
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $teamConversion,
                            'emptyText' => '<div class="text-center">No data</div><br>',
                            'layout' => "{items}<div class=\"text-center\">{pager}</div>\n", //{summary}\n
                            'itemView' => function ($model, $key, $index, $widget) {
                                if ($index <= 14) {
                                    return $this->render('ratings/_team_list_item_ratings', ['model' => $model, 'index' => $index]);
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
