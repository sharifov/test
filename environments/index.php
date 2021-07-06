<?php

/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'setInitConfig' => [
 *             // list of config files that need to be replace with automatically tags
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */

return [
    'Local' => [
        'path' => 'loc',
        'setWritable' => [
            'frontend/runtime',
            'frontend/web/assets',
            'console/runtime',
            'webapi/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'frontend/config/main-local.php',
            'console/config/main-local.php',
            'webapi/config/main-local.php',
        ],
        'setInitConfig' => [
            'common/config/supervisor/queue-client-chat-job.conf',
            'common/config/supervisor/queue-email-job.conf',
            'common/config/supervisor/queue-job.conf',
            'common/config/supervisor/queue-phone-check.conf',
            'common/config/supervisor/queue-sms-job.conf',
            'common/config/supervisor/queue-system-services.conf',
            'common/config/supervisor/queue-virtual-cron.conf',
            'common/config/supervisor/socket-server.conf',
        ],
    ],
    'Development' => [
        'path' => 'dev',
        'setWritable' => [
            'frontend/runtime',
            'frontend/web/assets',
            'console/runtime',
            'webapi/runtime',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'frontend/config/main-local.php',
            'console/config/main-local.php',
            'webapi/config/main-local.php',
        ],
        'setInitConfig' => [
            'common/config/supervisor/queue-client-chat-job.conf',
            'common/config/supervisor/queue-email-job.conf',
            'common/config/supervisor/queue-job.conf',
            'common/config/supervisor/queue-phone-check.conf',
            'common/config/supervisor/queue-sms-job.conf',
            'common/config/supervisor/queue-system-services.conf',
            'common/config/supervisor/queue-virtual-cron.conf',
            'common/config/supervisor/socket-server.conf',
        ],
    ],
    'Stage' => [
        'path' => 'stage',
        'setWritable' => [
            'frontend/runtime',
            'frontend/web/assets',
            'console/runtime',
            'webapi/runtime',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'frontend/config/main-local.php',
            'console/config/main-local.php',
            'webapi/config/main-local.php',
        ],
        'setInitConfig' => [
            'common/config/supervisor/queue-client-chat-job.conf',
            'common/config/supervisor/queue-email-job.conf',
            'common/config/supervisor/queue-job.conf',
            'common/config/supervisor/queue-phone-check.conf',
            'common/config/supervisor/queue-sms-job.conf',
            'common/config/supervisor/queue-system-services.conf',
            'common/config/supervisor/queue-virtual-cron.conf',
            'common/config/supervisor/socket-server.conf',
        ],
    ],
    'Production' => [
        'path' => 'prod',
        'setWritable' => [
            'frontend/runtime',
            'frontend/web/assets',
            'console/runtime',
            'webapi/runtime',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'frontend/config/main-local.php',
            'console/config/main-local.php',
            'webapi/config/main-local.php',
        ],
        'setInitConfig' => [
            'common/config/supervisor/queue-client-chat-job.conf',
            'common/config/supervisor/queue-email-job.conf',
            'common/config/supervisor/queue-job.conf',
            'common/config/supervisor/queue-phone-check.conf',
            'common/config/supervisor/queue-sms-job.conf',
            'common/config/supervisor/queue-system-services.conf',
            'common/config/supervisor/queue-virtual-cron.conf',
            'common/config/supervisor/socket-server.conf',
        ],
    ],
];
