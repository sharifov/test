<?php

use common\models\Lead;
use sales\access\LeadPreferencesAccess;
use yii\helpers\Url;
use yii\web\View;
use \yii\helpers\Html;

/**
 * @var $this View
 * @var $lead Lead
 */

$leadPreferences = $lead->leadPreferences;

$manageLeadPreferencesAccess = LeadPreferencesAccess::isUserCanManageLeadPreference($lead, Yii::$app->user->id);
?>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-cog"></i> Lead Preferences</h2>
		<ul class="nav navbar-right panel_toolbox" style="min-width: initial;">
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
                        <th class="bg-info" style="width: 25%">Clients Budget</th>
                        <th class="bg-info" style="width: 25%">Stops</th>
                        <th class="bg-info">Delayed Charge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= !empty($leadPreferences->market_price) ? Html::encode($leadPreferences->market_price) : '-' ?></td>
                        <td><?= is_numeric($leadPreferences->clients_budget) ? Html::encode($leadPreferences->clients_budget) : '-' ?></td>
                        <td><?= is_numeric($leadPreferences->number_stops) ? Html::encode($leadPreferences->number_stops) : '-' ?></td>
                        <td>
                            <?php if($lead->l_delayed_charge): ?>
                                <i class="fa fa-check-square-o green"></i> yes
                            <?php else: ?>
                                -
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
                        <td><?= $lead->notes_for_experts ? nl2br(Html::encode($lead->notes_for_experts)) : '-' ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>