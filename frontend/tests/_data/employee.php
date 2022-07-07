<?php

return [
    [
        'id' => 99999,
        'username' => 'abac_tester',
        'full_name' => 'Abac Tester',
        'auth_key' => 'ywS0P2b6gjg3NB-oP2qaezFCpRdrsYx3',
        'password_hash' => '$2y$13$gm46x.vg9hBTX1q/vLcQYOHLr.aNAdfvs/JAchqnz3zLSOfAZ1bYy',
        'email' => 'test-email-abac@test.test',
        'status' => \common\models\Employee::STATUS_ACTIVE,
        'created_at' => '2022-06-03 11:42:26',
        'updated_at' => '2022-06-14 08:29:06',
        'nickname' => 'test_abac_nickname',
        'roles' => ['admin'],
        'accessData' => [
            'projects' => [
                2 => 'ovago',
                6 => 'wowfare'
            ],
            'groups' => ['test_group'],
            'departments' => [],
        ]
    ],
    [
        'id' => 123456789,
        'username' => 'superadmin',
        'full_name' => 'Superadmin Abac Tester',
        'auth_key' => 'ywS0P2b6gjg3NB-oP2qaezFCpRdrsYx3',
        'password_hash' => '$2y$13$gm46x.vg9hBTX1q/vLcQYOHLr.aNAdfvs/JAchqnz3zLSOfAZ1bYy',
        'email' => 'test-email-abac-superadmin@test.test',
        'status' => \common\models\Employee::STATUS_ACTIVE,
        'created_at' => '2022-06-03 11:42:26',
        'updated_at' => '2022-06-14 08:29:06',
        'nickname' => 'test_abac_superadmin_nickname',
        'roles' => [],
        'accessData' => [
            'projects' => [],
            'groups' => [],
            'departments' => [],
        ]
    ],
];
