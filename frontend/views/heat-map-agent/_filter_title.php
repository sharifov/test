<h5>
    Report Interval: <?= $searchModel->dateRange ?><br>
    TimeZone: <?= !empty($searchModel->timeZone) ? $searchModel->timeZone : 'UTC' ?><br>
    <?php if (is_array($searchModel->shifts)) : ?>
        <?php $shiftModels = array_map(function ($key) use ($shifts) {
            return $shifts[$key] ?? null;
        }, $searchModel->shifts); ?>
        Shifts: <?= implode(', ', $shiftModels) ?><br>
    <?php endif; ?>
    <?php if (is_array($searchModel->roles)) : ?>
        <?php $roleModels = array_map(function ($key) use ($roles) {
            return $roles[$key] ?? null;
        }, $searchModel->roles); ?>
        Roles: <?= implode(', ', $roleModels) ?><br>
    <?php endif; ?>

    <?php if (is_array($searchModel->userGroup)) : ?>
        <?php $userGroupModels = array_map(function ($key) use ($userGroups) {
            return $userGroups[$key] ?? null;
        }, $searchModel->userGroup); ?>
        User Group: <?= implode(', ', $userGroupModels) ?>
    <?php endif; ?>
</h5>
