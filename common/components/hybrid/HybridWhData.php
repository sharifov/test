<?php

namespace common\components\hybrid;

/**
 * Class HybridWhData
 *
 * @property array $collectedData
 */
class HybridWhData
{
    public const WH_TYPE_FLIGHT_SCHEDULE_CHANGE    = 'flight/schedule-change';
    public const WH_TYPE_VOLUNTARY_CHANGE_UPDATE   = 'flight/voluntary-change/update';
    public const WH_TYPE_VOLUNTARY_REFUND_UPDATE   = 'flight/voluntary-refund/update';

    public const WH_DATA = [
        self::WH_TYPE_FLIGHT_SCHEDULE_CHANGE => [
            'booking_id'                => '',
            'reprotection_quote_gid'    => '',
            'case_gid'                  => '',
            'product_quote_gid'         => '',
        ],
        self::WH_TYPE_VOLUNTARY_CHANGE_UPDATE => [
            'booking_id'                => '',
            'product_quote_gid'         => '',
            'change_quote_gid'          => '',
            'change_status_id'          => '',
        ],
        self::WH_TYPE_VOLUNTARY_REFUND_UPDATE => [
            'booking_id'                => '',
            'product_quote_gid'         => '',
            'refund_gid'                => '',
            'refund_status_id'          => '',
        ],
    ];

    private array $collectedData = [];

    /**
     * @param string $whType
     * @return array|string[]
     */
    public static function getData(string $whType): array
    {
        return self::WH_DATA[$whType] ?? [];
    }

    public function fillCollectedData(string $whType, array $data): HybridWhData
    {
        if (!$templateData = self::getData($whType)) {
            throw new \RuntimeException('Data not found by type(' . $whType . ')');
        }
        foreach ($templateData as $key => $value) {
            if (!array_key_exists($key, $data)) {
                throw new \RuntimeException('Data key not found (' . $key . ')');
            }
            $this->collectedData[$key] = $data[$key];
        }
        return $this;
    }

    public function getCollectedData(): array
    {
        return $this->collectedData;
    }
}
