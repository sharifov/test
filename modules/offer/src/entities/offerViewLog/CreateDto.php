<?php

namespace modules\offer\src\entities\offerViewLog;

/**
 * Class CreateDto
 *
 * @property int $ofvwl_offer_id
 * @property string $ofvwl_visitor_id
 * @property string $ofvwl_ip_address
 * @property string $ofvwl_user_agent
 * @property string $ofvwl_created_dt
 */
class CreateDto
{
    public $ofvwl_offer_id;
    public $ofvwl_visitor_id;
    public $ofvwl_ip_address;
    public $ofvwl_user_agent;
    public $ofvwl_created_dt;

    public function __construct(
        int $offerId,
        ?string $visitorId,
        ?string $ipAddress,
        ?string $userAgent
    )
    {
        $this->ofvwl_offer_id = $offerId;
        $this->ofvwl_visitor_id = $visitorId;
        $this->ofvwl_ip_address = $ipAddress;
        $this->ofvwl_user_agent = $userAgent;
        $this->ofvwl_created_dt = date('Y-m-d H:i:s');
    }
}
