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
        int $ofvwl_offer_id,
        ?string $ofvwl_visitor_id,
        ?string $ofvwl_ip_address,
        ?string $ofvwl_user_agent
    )
    {
        $this->ofvwl_offer_id = $ofvwl_offer_id;
        $this->ofvwl_visitor_id = $ofvwl_visitor_id;
        $this->ofvwl_ip_address = $ofvwl_ip_address;
        $this->ofvwl_user_agent = $ofvwl_user_agent;
        $this->ofvwl_created_dt = date('Y-m-d H:i:s');
    }
}
