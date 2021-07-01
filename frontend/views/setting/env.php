<?php

use common\models\SettingCategory;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

$this->title = 'Site Environments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-env">

    <h1><i class="fa fa-info-circle"></i> <?= Html::encode($this->title) ?></h1>

    <div class="col-md-6">
        <h4>Components:</h4>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Nr</th>
            <th>Component</th>
            <th>URL</th>
            <th>Username</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>travelServices</td>
            <td><?=Html::encode(Yii::$app->travelServices->url)?></td>
            <td><?=Html::encode(Yii::$app->travelServices->username)?></td>
        </tr>
        <tr>
            <td>2</td>
            <td>communication</td>
            <td><?=Html::encode(Yii::$app->communication->url)?></td>
            <td><?=Html::encode(Yii::$app->communication->username)?></td>
        </tr>
        <tr>
            <td>3</td>
            <td>rchat</td>
            <td><?=Html::encode(Yii::$app->rchat->url)?></td>
            <td><?=Html::encode(Yii::$app->rchat->username)?></td>
        </tr>
        <tr>
            <td>4</td>
            <td>chatBot</td>
            <td><?=Html::encode(Yii::$app->chatBot->url)?></td>
            <td><?=Html::encode(Yii::$app->chatBot->username)?></td>
        </tr>
        <tr>
            <td>5</td>
            <td>airsearch</td>
            <td><?=Html::encode(Yii::$app->airsearch->url)?></td>
            <td><?=Html::encode(Yii::$app->airsearch->username)?></td>
        </tr>
        <tr>
            <td>6</td>
            <td>telegram</td>
            <td><?=Html::encode(Yii::$app->telegram->apiUrl)?></td>
            <td><?=Html::encode(Yii::$app->telegram->botUsername)?></td>
        </tr>
        <tr>
            <td>7</td>
            <td>gaRequestService</td>
            <td><?=Html::encode(Yii::$app->gaRequestService->url)?></td>
            <td>-</td>
        </tr>
        <tr>
            <td>8</td>
            <td>centrifugo</td>
            <td><?=Html::encode(Yii::$app->centrifugo->host)?></td>
            <td>-</td>
        </tr>
        </tbody>
    </table>
    </div>
    <div class="col-md-6">
        <?php if (!empty(Yii::$app->params['release'])) : ?>
            <h4>Release:</h4>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nr</th>
                    <th>Param</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>App Version</td>
                        <td><?=Yii::$app->params['release']['version'] ?? ''?></td>
                    </tr>
                     <tr>
                         <td>2</td>
                         <td>GIT Branch</td>
                         <td><?=Yii::$app->params['release']['git_branch'] ?? ''?></td>
                     </tr>
                     <tr>
                         <td>3</td>
                         <td>GIT Hash</td>
                         <td><?=Yii::$app->params['release']['git_hash'] ?? ''?></td>
                     </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <h4>Params:</h4>
        <?php $paramsNum = 0; ?>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Nr</th>
                <th>Param</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>appHostname</td>
                <td><?=Yii::$app->params['appHostname'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>appName</td>
                <td><?=Yii::$app->params['appName'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>appEnv</td>
                <td><?=Yii::$app->params['appEnv'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>appInstance</td>
                <td><?=Yii::$app->params['appInstance'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>url_address</td>
                <td><?=Yii::$app->params['url_address'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>url_api_address</td>
                <td><?=Yii::$app->params['url_api_address'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>backOffice serverUrl</td>
                <td><?=Yii::$app->params['backOffice']['serverUrl'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>backOffice serverUrlV3</td>
                <td><?=Yii::$app->params['backOffice']['serverUrlV3'] ?? ''?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>getAirportUrl</td>
                <td><?= '/airport/search' ?? ''?></td>
            </tr>

            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>searchApiUrl</td>
                <td>Not used</td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>$_ENV</td>
                <td><?php \yii\helpers\VarDumper::dump($_ENV, 10, true) ?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>DB_NAME</td>
                <td><?php echo getenv('DB_NAME') ?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>DB_NAME2</td>
                <td><?php echo getenv('DB_NAME2') ?></td>
            </tr>
            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>DB_NAME2</td>
                <td><?php echo $_ENV['DB_NAME2'] ?? '-' ?></td>
            </tr>

            <tr>
                <td><?php echo $paramsNum++ ?></td>
                <td>centrifugo</td>
                <td>
                    <table class="table table-bordered table-hover">
                    <?php
                    foreach (Yii::$app->params['centrifugo'] ?? [] as $key => $item) {
                        echo "<tr><td>$key</td><td>$item</td></tr>";
                    }
                    ?>
                    </table>
                </td>
            </tr>

            </tbody>
        </table>
        <?php //\yii\helpers\VarDumper::dump(Yii::$app->params, 10 ,true)?>
    </div>

</div>
