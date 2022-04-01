<?php
/**
 * User: shakarim
 * Date: 4/1/22
 * Time: 5:54 PM
 */

namespace modules\requestControl\interfaces;


use modules\requestControl\accessCheck\RequestCountLedger;

interface AllowanceInterface {
    public function isAllow(RequestCountLedger $registry): bool;
}