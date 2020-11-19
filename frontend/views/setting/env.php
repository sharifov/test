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
        <?php if (!empty(Yii::$app->params['release'])): ?>
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
                <td>appName</td>
                <td><?=Yii::$app->params['appName'] ?? ''?></td>
            </tr>
            <tr>
                <td>2</td>
                <td>appEnv</td>
                <td><?=Yii::$app->params['appEnv'] ?? ''?></td>
            </tr>
            <tr>
                <td>3</td>
                <td>appInstance</td>
                <td><?=Yii::$app->params['appInstance'] ?? ''?></td>
            </tr>
            <tr>
                <td>4</td>
                <td>url_address</td>
                <td><?=Yii::$app->params['url_address'] ?? ''?></td>
            </tr>
            <tr>
                <td>5</td>
                <td>url_api_address</td>
                <td><?=Yii::$app->params['url_api_address'] ?? ''?></td>
            </tr>
            <tr>
                <td>6</td>
                <td>backOffice serverUrl</td>
                <td><?=Yii::$app->params['backOffice']['serverUrl'] ?? ''?></td>
            </tr>
            <tr>
                <td>7</td>
                <td>backOffice serverUrlV3</td>
                <td><?=Yii::$app->params['backOffice']['serverUrlV3'] ?? ''?></td>
            </tr>
            <tr>
                <td>8</td>
                <td>getAirportUrl</td>
                <td><?=Yii::$app->params['getAirportUrl'] ?? ''?></td>
            </tr>

            <tr>
                <td>9</td>
                <td>searchApiUrl</td>
                <td><?=Yii::$app->params['searchApiUrl'] ?? ''?></td>
            </tr>

            <tr>
                <td>10</td>
                <td>global_phone</td>
                <td><?=Yii::$app->params['global_phone'] ?? ''?></td>
            </tr>

            <tr>
                <td>11</td>
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
