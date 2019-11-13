<?php

/** @var int $allLeads */
/** @var int $activeLeads */
/** @var int $allCases */
/** @var int $activeCases */

?>
<table class="table table-bordered table-condensed">
    <tr>
        <td title="Leads active: <?= $activeLeads ?> / Leads all: <?= $allLeads ?>">Leads: <?= $activeLeads ?> / <?= $allLeads ?></td>
        <td title="Cases active: <?= $activeCases ?> / Cases all: <?= $allCases ?>">Cases: <?= $activeCases ?> / <?= $allCases ?></td>
    </tr>
</table>