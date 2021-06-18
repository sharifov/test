<?php

return [
    'adminEmail' => 'admin@example.com',
    'serviceName' => 'sales-frontend',
    'limitUserConnections' => 0,   // WebSocket Limit user Connections
    'bsVersion' => '4.x',
    'minifiedAssetsEnabled' => false,
    'dateRangePicker' => [
        'configs' => [
            'default' => [
                    "Today" => ["moment().startOf('day')", "moment().endOf('day')"],
                    "Yesterday" => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                    "Last 7 Days" => ["moment().startOf('day').subtract(6,'days')", "moment().endOf('day')"],
                    "Last 30 Days" => ["moment().startOf('day').subtract(29,'days')", "moment().endOf('day')"],
                    "This Month" => ["moment().startOf('month')", "moment().endOf('month')"],
                    "Past Month" => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
            ],
            'added3monthAllTime' => [
                    "Today" => ["moment().startOf('day')", "moment().endOf('day')"],
                    "Yesterday" => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                    "Last 7 Days" => ["moment().startOf('day').subtract(6,'days')", "moment().endOf('day')"],
                    "Last 30 Days" => ["moment().startOf('day').subtract(29,'days')", "moment().endOf('day')"],
                    "This Month" => ["moment().startOf('month')", "moment().endOf('month')"],
                    "Past Month" => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                    "Last 3 Months" => ["moment().subtract(3, 'month').startOf('day')", "moment().endOf('day')"],
                    // "Past 3 Months (or Previous/Past 3 Completed Month )" => ["moment().subtract(3, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                    "All time" => ["moment('2000-01-01')", "moment().endOf('day')"],
            ]
        ]
    ]
];
