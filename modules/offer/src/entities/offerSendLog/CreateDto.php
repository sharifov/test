<?php

namespace modules\offer\src\entities\offerSendLog;

/**
 * Class CreateDto
 *
 * @property int $ofsndl_offer_id
 * @property int $ofsndl_type_id
 * @property string $ofsndl_send_to
 * @property int|null $ofsndl_created_user_id
 * @property string $ofsndl_created_dt
 */
class CreateDto
{
    public $ofsndl_offer_id;
    public $ofsndl_type_id;
    public $ofsndl_send_to;
    public $ofsndl_created_user_id;
    public $ofsndl_created_dt;

    public function __construct(
        int $ofsndl_offer_id,
        int $ofsndl_type_id,
        ?int $ofsndl_created_user_id,
        ?string $ofsndl_send_to
    )
    {
        $this->ofsndl_offer_id = $ofsndl_offer_id;
        $this->ofsndl_type_id = $ofsndl_type_id;
        $this->ofsndl_created_user_id = $ofsndl_created_user_id;
        $this->ofsndl_send_to = $ofsndl_send_to;
        $this->ofsndl_created_dt = date('Y-m-d H:i:s');
    }
}
