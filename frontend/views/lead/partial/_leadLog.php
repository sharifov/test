<?php
/**
 * @var $logs \common\models\LeadLog[]
 */

use yii\helpers\Html; ?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list-alt"></i> Lead activity Logs (<?=count($logs)?>)</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                &nbsp;
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
    <div class="x_content" style="display: none;">
        <div class="panel-body">
    <div class="table-responsive mb-20">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Time</th>
                <th>Agent</th>
                <th>Note</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $saleLog) : ?>
                <tr>
                    <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($saleLog->created)) ?></td>
                    <td><i class="fa fa-user"></i> <?= $saleLog->agent ?></td>
                    <td style="max-width: 1024px;">
                        <?php
                        if (empty($saleLog->logMessage->message)) :
                            ?>
                            <p><?= sprintf('%s - %s', $saleLog->logMessage->title, $saleLog->logMessage->model) ?></p>
                            <p>Changed attributes:</p>
                            <div class="diff-itinerary">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <th style="width: 20%;">Attribute</th>
                                        <th style="width: 40%;">Old Value</th>
                                        <th style="width: 40%;">New Value</th>
                                    </tr>
                                    </tbody>
                                    <tbody>
                                    <?php foreach ($saleLog->logMessage->oldParams as $key => $attribute) : ?>
                                        <tr>
                                            <th style="width: 20%;">
                                                <?= $key ?>
                                            </th>
                                            <td style="width: 40%; word-break: break-word;">
                                                    <span class="item-new">
                                                        <?= $attribute ?>
                                                    </span>
                                            </td>
                                            <td style="width: 40%; word-break: break-word;">
                                                    <span class="item-old">
                                                        <?= $saleLog->logMessage->newParams[$key] ?>
                                                    </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p><?= $saleLog->logMessage->title ?></p>
                            <p><?= $saleLog->logMessage->message ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
    </div>
</div>
