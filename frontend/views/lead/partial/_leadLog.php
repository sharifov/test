<?php
/**
 * @var $logs \common\models\LeadLog[]
 */

?>

<div class="panel panel-neutral panel-wrapper history-block">
    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#agents-activity-logs" aria-expanded="false"
           class="collapsing-heading__collapse-link collapsed">
            Activity Logs
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>

    <div class="collapse" id="agents-activity-logs" aria-expanded="false" style="">
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
                            <td><?= date('d/m/Y H:i', strtotime($saleLog->created)) ?></td>
                            <td><?= $saleLog->agent ?></td>
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