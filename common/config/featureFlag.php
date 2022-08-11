<?php

use kivork\FeatureFlag\Components\FeatureFlagComponent;

return [
    'class' => FeatureFlagComponent::class,
    'cache' => 'cache',
    'scanDirs' => [
        '@root/modules/',
        '@frontend/',
        '@console/',
        '@webapi/',
        '@common/',
        '@root/src/',
    ],
    'objectListModel' => \modules\featureFlag\FFlag::class,
    'scanExtMask' => ['*.php'],
    'experimentLogCategory' => 'info\FeatureFlagExperimentLog'
];
