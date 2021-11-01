<?php

namespace webapi\controllers;

/**
 * Class ApiDocData
 * @package webapi\modules\controllers
 */
class ApiDocData
{


    // ==================================== flight/schedule-change =================================================

    /**
     * @api {post} flight/schedule-change WebHook Hybrid OTA ( flight/schedule-change )
     * @apiVersion 0.1.0
     * @apiName Flight schedule-change
     * @apiGroup WebHooks Outgoing
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}}           type                          Type of message
     * @apiParam {array[]}              data
     * @apiParam {string{8}}               data.booking_id               Booking Id
     * @apiParam {string{32}}               data.reprotection_quote_gid   Reprotection quote GID
     * @apiParam {string{32}}               data.case_gid                 Case GID
     * @apiParam {string{32}}               data.product_quote_gid        Product quote GID
     *
     * @apiParamExample {json} Request message Example:
     *  {
     *      "type": "flight/schedule-change",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179"
     *          "case_gid": "1569a42c916c811e2033142d8ae54176"
     *          "product_quote_gid": "5569a42c916c811e2033142d8ae54170"
     *      }
     *  }
     *
     */



    // ==================================== flight/voluntary-exchange/update =========================================

    /**
     * @api {post} flight/schedule-change WebHook Hybrid OTA ( flight/voluntary-exchange/update )
     * @apiVersion 0.1.0
     * @apiName Flight voluntary-exchange update
     * @apiGroup WebHooks Outgoing
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}}           type                          Type of message
     * @apiParam {array[]}              data
     * @apiParam {string{8}}               data.booking_id              Booking Id
     * @apiParam {string{32}}              data.product_quote_gid       Product quote GID
     * @apiParam {string{32}}              data.exchange_gid            Exchange GID
     * @apiParam {string{32}=processing,canceled}              data.exchange_status         Exchange Client status
     *
     * @apiParamExample {json} Request message Example:
     *  {
     *      "type": "flight/voluntary-exchange/update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "product_quote_gid": "4569a42c916c811e2033142d8ae54179"
     *          "exchange_gid": "1569a42c916c811e2033142d8ae54176"
     *          "exchange_status": "processing"
     *      }
     *  }
     *
     */



    // ==================================== flight/voluntary-refund/update ===========================================

    /**
     * @api {post} flight/schedule-change WebHook Hybrid OTA ( flight/voluntary-refund/update )
     * @apiVersion 0.1.0
     * @apiName Flight voluntary-refund update
     * @apiGroup WebHooks Outgoing
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}}           type                          Type of message
     * @apiParam {array[]}              data
     * @apiParam {string{8}}               data.booking_id              Booking Id
     * @apiParam {string{32}}              data.product_quote_gid       Product quote GID
     * @apiParam {string{32}}              data.refund_gid            Refund GID
     * @apiParam {string{32}}              data.refund_order_id         Refund Client status
     * @apiParam {string{32}=processing,canceled}              data.refund_status         Refund Client status
     *
     * @apiParamExample {json} Request message Example:
     *  {
     *      "type": "flight/voluntary-refund/update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "product_quote_gid": "4569a42c916c811e2033142d8ae54179"
     *          "refund_gid": "1569a42c916c811e2033142d8ae54176"
     *          "refund_order_id": "1569a42c916c811e2033142d8ae54176"
     *          "refund_status": "processing"
     *      }
     *  }
     *
     */
}