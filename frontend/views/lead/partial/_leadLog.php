<?php
/**
 * @var $logs \common\models\LeadLog[]
 */

?>
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
                    <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($saleLog->created), 'php: Y-m-d [H:i]') ?></td>
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
