<?php

namespace sales\model\clientChatRequest;

use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\helpers\VarDumper;

/**
 * Class ClientShortInfo
 *
 * @property string $geo
 * @property string $ip
 * @property string $userAgent
 * @property string $utc_offset
 */
class ClientShortInfo
{
    public string $geo;
    public string $ip;
    public string $userAgent;
    public string $utc_offset;

    public function __construct(?ClientChatRequest $request)
    {
        if (!$request) {
            return;
        }

        $data = $request->getDecodedData();

        $this->geo = $data['geo'] ?? [];
        $this->ip = $data['geo']['ip'] ?? null;
        $this->userAgent = $data['system']['user_agent'] ?? null;
        if (isset($data['geo']['utc_offset'])) {
            $utc_offset = (string)$data['geo']['utc_offset'];
            $strlen = strlen($utc_offset);
            if ($strlen === 4) {
                $this->utc_offset = substr($utc_offset, 0,2) . ':' . substr($utc_offset, 2,2);
            } elseif ($strlen === 5) {
                $this->utc_offset = substr($utc_offset, 0,3) . ':' . substr($utc_offset, 3,2);
            } else {
                \Yii::error(VarDumper::dumpAsString(['error' => 'Undefined utc_offset', 'data' => $data, 'ClientChatRequestId' => $request->ccr_id]), 'ClientShortInfo');
            }
        }
    }
}
