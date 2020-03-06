<?php

use common\models\Employee;
use common\models\Lead;
use sales\access\LeadPreferencesAccess;
use yii\helpers\Url;
use yii\web\View;
use \yii\helpers\Html;

/**
 * @var $this View
 * @var $lead Lead
 */

/** @var Employee $user */
$user = Yii::$app->user->identity;

$leadPreferences = $lead->leadPreferences;
$manageLeadPreferencesAccess = LeadPreferencesAccess::isUserCanManageLeadPreference($lead, $user);
?>



<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-cog"></i> Lead Preferences</h2>
        <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">
            <li>
                <?php if(!$lead->l_answered): ?>

                    <?php if($lead->isProcessing()):?>
                        <?= Html::a(($lead->l_answered ? '<i class="fa fa-commenting-o"></i>Make UnAnswered' : '<i class="fa fa-commenting"></i> Make Answered'), ['lead/update2', 'id' => $lead->id, 'act' => 'answer'], [
                            'class' => ''.($lead->l_answered ? 'text-success' : 'text-info'),
                            'data-pjax' => false,
                            'data' => [
                                'confirm' => 'Are you sure?',
                                'method' => 'post',
                                'pjax' => 0
                            ],
                        ]) ?>
                    <?php else: ?>
                        <a href="#" class="text-warning disabled"><i class="fa fa-commenting-o"></i> ANSWERED: false</a>
                    <?php endif;?>

                <?php else: ?>
                    <a href="#" class="text-success disabled"><i class="fa fa-commenting-o"></i> ANSWERED: true</a>
                <?php endif; ?>
            </li>

            <?php if($manageLeadPreferencesAccess): ?>
                <li>
                    <?=
                    Html::a('<i class="fa fa-edit yellow"></i> Edit Preferences', '#', [
                        'class' => 'showModalButton',
                        'title' => 'Edit Lead Preferences',
                        'data-modal_id' => 'client-manage-info',
                        'data-content-url' => Url::to([
                            'lead-view/ajax-edit-lead-preferences-modal-content',
                            'gid' => $lead->gid
                        ])
                    ])
                    ?>
                </li>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th class="bg-info" style="width: 25%">Market Price</th>
                        <th class="bg-info" style="width: 80px" title="Client Budget">Budget</th>
                        <th class="bg-info" style="width: 80px">Stops</th>
                        <th class="bg-info" style="width: 80px">Currency</th>
                        <th class="bg-info">Delayed Charge</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-right"><?= !empty($leadPreferences->market_price) ? Html::encode($leadPreferences->market_price) : '-' ?></td>
                        <td class="text-right"><?= $leadPreferences && is_numeric($leadPreferences->clients_budget) ? Html::encode($leadPreferences->clients_budget) : '-' ?></td>
                        <td class="text-center"><?= $leadPreferences && is_numeric($leadPreferences->number_stops) ? Html::encode($leadPreferences->number_stops) : '-' ?></td>
                        <td><?= $leadPreferences && $leadPreferences->pref_currency ? Html::encode($leadPreferences->pref_currency) : '-' ?></td>
                        <td>
                            <?php if($lead->l_delayed_charge === null): ?>
                                -
                            <?php else: ?>
                                <?php if($lead->l_delayed_charge): ?>
                                    <i class="fa fa-check-square-o green"></i> yes
                                <?php else: ?>
                                    <i class="fa fa-square-o warning"></i> no
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                    <thead>
                    <tr>
                        <th class="bg-info">Notes from client</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td id="lead-notes_for_experts"><?= $lead->notes_for_experts ? nl2br(Html::encode($lead->notes_for_experts)) : '-' ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>