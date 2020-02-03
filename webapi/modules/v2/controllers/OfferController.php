<?php

namespace webapi\modules\v2\controllers;

use modules\offer\src\entities\offer\OfferRepository;
use modules\offer\src\entities\offerViewLog\CreateDto;
use modules\offer\src\exceptions\OfferCodeException;
use modules\offer\src\services\OfferViewLogService;
use modules\offer\src\useCases\offer\api\view\OfferViewForm;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;

/**
 * Class OfferController
 *
 * @property OfferRepository $offerRepository
 * @property OfferViewLogService $offerViewLogService
 */
class OfferController extends BaseController
{
    private $offerRepository;
    private $offerViewLogService;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        OfferRepository $offerRepository,
        OfferViewLogService $offerViewLogService,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->offerRepository = $offerRepository;
        $this->offerViewLogService = $offerViewLogService;
    }

    /**
     * @api {post} /v2/offer/view View Offer
     * @apiVersion 0.2.0
     * @apiName ViewOffer
     * @apiGroup Offer
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}       offerGid            Offer gid
     * @apiParam {object}       visitor             Visitor
     * @apiParam {string{32}}   visitor.id          Visitor Id
     * @apiParam {string}       visitor.ipAddress   Visitor Ip Address
     * @apiParam {string{255}}  visitor.userAgent   Visitor User Agent
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *     "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
     *     "visitor": {
     *         "id": "hdsjfghsd5489tertwhf289hfgkewr",
     *         "ipAddress": "12.12.13.22",
     *         "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
     *     }
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "offer": {
     *           "of_gid": "04d3fe3fc74d0514ee93e208a52bcf90",
     *           "of_uid": "of5e2c5ea22b0f1",
     *           "of_name": "Offer 2",
     *           "of_lead_id": 371096,
     *           "of_status_id": 1,
     *           "of_client_currency": "EUR",
     *           "of_client_currency_rate": 1,
     *           "of_app_total": 0,
     *           "of_client_total": 0,
     *           "quotes": [
     *               {
     *                   "pq_gid": "6fcfc43e977dabffe6a979ebda22a281",
     *                   "pq_name": "",
     *                   "pq_order_id": null,
     *                   "pq_description": null,
     *                   "pq_status_id": 1,
     *                   "pq_price": 92.3,
     *                   "pq_origin_price": 92.3,
     *                   "pq_client_price": 92.3,
     *                   "pq_service_fee_sum": null,
     *                   "pq_origin_currency": "USD",
     *                   "pq_client_currency": "USD",
     *                   "data": {
     *                       "fq_flight_id": 21,
     *                       "fq_source_id": null,
     *                       "fq_product_quote_id": 6,
     *                       "fq_gds": "A",
     *                       "fq_gds_pcc": "DFWG32100",
     *                       "fq_gds_offer_id": null,
     *                       "fq_type_id": 0,
     *                       "fq_cabin_class": "E",
     *                       "fq_trip_type_id": 1,
     *                       "fq_main_airline": "SU",
     *                       "fq_fare_type_id": 1,
     *                       "fq_last_ticket_date": "2020-01-25",
     *                       "flight": {
     *                           "fl_product_id": 33,
     *                           "fl_trip_type_id": 1,
     *                           "fl_cabin_class": "E",
     *                           "fl_adults": 2,
     *                           "fl_children": 0,
     *                           "fl_infants": 0
     *                       },
     *                       "segments": [
     *                           {
     *                               "fqs_departure_dt": "2020-01-30 01:35:00",
     *                               "fqs_arrival_dt": "2020-01-30 05:45:00",
     *                               "fqs_stop": 0,
     *                               "fqs_flight_number": 1845,
     *                               "fqs_booking_class": "R",
     *                               "fqs_duration": 190,
     *                               "fqs_departure_airport_iata": "KIV",
     *                               "fqs_departure_airport_terminal": "",
     *                               "fqs_arrival_airport_iata": "SVO",
     *                               "fqs_arrival_airport_terminal": "D",
     *                               "fqs_operating_airline": "SU",
     *                               "fqs_marketing_airline": "SU",
     *                               "fqs_air_equip_type": "32A",
     *                               "fqs_marriage_group": "",
     *                               "fqs_cabin_class": "Y",
     *                               "fqs_meal": "",
     *                               "fqs_fare_code": "RNO",
     *                               "fqs_ticket_id": null,
     *                               "fqs_recheck_baggage": 0,
     *                               "fqs_mileage": null,
     *                               "baggages": [
     *                                   {
     *                                       "qsb_flight_pax_code_id": 1,
     *                                       "qsb_flight_quote_segment_id": 2,
     *                                       "qsb_airline_code": null,
     *                                       "qsb_allow_pieces": 0,
     *                                       "qsb_allow_weight": null,
     *                                       "qsb_allow_unit": null,
     *                                       "qsb_allow_max_weight": null,
     *                                       "qsb_allow_max_size": null
     *                                   }
     *                               ]
     *                           }
     *                       ],
     *                       "pax_prices": [
     *                          {
     *                               "qpp_fare": "43.00",
     *                               "qpp_tax": "49.30",
     *                               "qpp_system_mark_up": "0.00",
     *                               "qpp_agent_mark_up": "0.00",
     *                               "qpp_origin_fare": "43.00",
     *                               "qpp_origin_currency": "USD",
     *                               "qpp_origin_tax": "49.30",
     *                               "qpp_client_currency": "USD",
     *                               "qpp_client_fare": null,
     *                               "qpp_client_tax": null
     *                           }
     *                       ]
     *                   },
     *                   "product": {
     *                       "pr_type_id": 1,
     *                       "pr_name": "",
     *                       "pr_lead_id": 371096,
     *                       "pr_description": "",
     *                       "pr_status_id": null,
     *                       "pr_service_fee_percent": null
     *                   },
     *                   "productQuoteOptions": []
     *               },
     *               {
     *                   "pq_gid": "16fb0f9565b9cb87280a348c75c05128",
     *                   "pq_name": "DBL.ST",
     *                   "pq_order_id": null,
     *                   "pq_description": null,
     *                   "pq_status_id": 1,
     *                   "pq_price": 349.99,
     *                   "pq_origin_price": 349.99,
     *                   "pq_client_price": 349.99,
     *                   "pq_service_fee_sum": 0,
     *                   "pq_origin_currency": "USD",
     *                   "pq_client_currency": "USD",
     *                   "data": {
     *                       "hotel": {
     *                           "hl_name": "Manzil Hotel",
     *                           "hl_star": null,
     *                           "hl_category_name": "2 STARS",
     *                           "hl_destination_name": "Casablanca",
     *                           "hl_zone_name": "Casablanca",
     *                           "hl_country_code": "MA",
     *                           "hl_state_code": "07",
     *                           "hl_description": "The Hotel is ideally located in the neighborhood of Roches Noires district and close to the activity area of ​​Ain Sebaa...",
     *                           "hl_address": "RUE DES FRANCAIS, ROCHES NOIRES,38  ",
     *                           "hl_postal_code": "20000",
     *                           "hl_city": "CASABLANCA",
     *                           "hl_email": "resa@manzilhotels.com",
     *                           "hl_web": null,
     *                           "hl_phone_list": [
     *                              {
     *                                   "type": "PHONEBOOKING",
     *                                   "number": "00212522242020"
     *                               },
     *                               {
     *                                   "type": "PHONEHOTEL",
     *                                   "number": "00212522242020"
     *                               },
     *                               {
     *                                   "type": "FAXNUMBER",
     *                                   "number": "00212522242020"
     *                               }
     *                           ],
     *                           "hl_image_list": [
     *                               {
     *                                   "url": "59/590133/590133a_hb_a_001.jpg",
     *                                   "type": "GEN"
     *                               }
     *                           ],
     *                           "hl_image_base_url": null
     *                       },
     *                       "rooms": [
     *                           {
     *                               "hqr_room_name": "DOUBLE STANDARD",
     *                               "hqr_class": "NOR",
     *                               "hqr_amount": 349.99,
     *                               "hqr_currency": "USD",
     *                               "hqr_cancel_amount": null,
     *                               "hqr_cancel_from_dt": null,
     *                               "hqr_board_name": "ROOM ONLY",
     *                               "hqr_rooms": 1,
     *                               "hqr_adults": 1,
     *                               "hqr_children": null
     *                           }
     *                       ]
     *                   },
     *                   "product": {
     *                       "pr_type_id": 2,
     *                       "pr_name": "ee",
     *                       "pr_lead_id": 371096,
     *                       "pr_description": "rrr",
     *                       "pr_status_id": 1,
     *                       "pr_service_fee_percent": null
     *                   },
     *                   "productQuoteOptions": [
     *                       {
     *                           "pqo_name": "1323",
     *                           "pqo_description": "",
     *                           "pqo_status_id": 1,
     *                           "pqo_price": null,
     *                           "pqo_client_price": null,
     *                           "pqo_extra_markup": null
     *                       },
     *                       {
     *                           "pqo_name": "tests",
     *                           "pqo_description": "swe we ",
     *                           "pqo_status_id": 1,
     *                           "pqo_price": 12,
     *                           "pqo_client_price": null,
     *                           "pqo_extra_markup": 1
     *                       }
     *                   ]
     *               }
     *           ]
     *       },
     *       "technical": {
     *           "action": "v2/offer/view",
     *           "response_id": 11933859,
     *           "request_dt": "2020-02-03 12:53:50",
     *           "response_dt": "2020-02-03 12:53:50",
     *           "execution_time": 0.034,
     *           "memory_usage": 1255920
     *       },
     *       "request": {
     *           "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
     *           "visitor": {
     *               "id": "hdsjfghsd5489tertwhf289hfgkewr",
     *               "ipAddress": "12.12.12.12",
     *               "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
     *           }
     *       }
     *   }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Error",
     *     "errors": [
     *         "Not found Offer"
     *     ],
     *     "code": "18302",
     *     "technical": {
     *         "action": "v2/offer/view",
     *         "response_id": 11933860,
     *         "request_dt": "2020-02-03 13:07:10",
     *         "response_dt": "2020-02-03 13:07:10",
     *         "execution_time": 0.015,
     *         "memory_usage": 151792
     *     },
     *     "request": {
     *         "offerGid": "04d3fe3fc74d0514ee93e208a5x2bcf90",
     *         "visitor": {
     *             "id": "hdsjfghsd5489tertwhf289hfgkewr",
     *             "ipAddress": "12.12.12.12",
     *             "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
     *         }
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *        "visitor.ipAddress": [
     *             "Ip Address cant be array."
     *         ]
     *     },
     *     "code": "18301",
     *     "technical": {
     *          "action": "v2/offer/view",
     *          "response_id": 11933854,
     *          "request_dt": "2020-02-03 12:44:13",
     *          "response_dt": "2020-02-03 12:44:13",
     *          "execution_time": 0.013,
     *          "memory_usage": 127680
     *     },
     *     "request": {
     *          "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
     *          "visitor": {
     *              "id": "hdsjfghsd5489tertwhf289hfgkewr",
     *              "ipAddress": [],
     *              "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
     *          }
     *     }
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *     "status": 400,
     *     "message": "Load data error",
     *     "errors": [
     *         "Not found Offer data on POST request"
     *     ],
     *     "code": "18300",
     *     "technical": {
     *         "action": "v2/offer/view",
     *         "response_id": 11933856,
     *         "request_dt": "2020-02-03 12:49:20",
     *         "response_dt": "2020-02-03 12:49:20",
     *         "execution_time": 0.017,
     *         "memory_usage": 114232
     *     },
     *     "request": []
     * }
     */
    public function actionView()
    {
        $form = new OfferViewForm();

        if (!$form->load(\Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Offer data on POST request'),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_VALIDATE)
            );
        }

        if (!$offer = $this->offerRepository->getByGid($form->offerGid)) {
            return new ErrorResponse(
                new ErrorsMessage('Not found Offer'),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_NOT_FOUND)
            );
        }

        $this->offerViewLogService->log(
            new CreateDto(
                $offer->of_id,
                $form->visitor->id,
                $form->visitor->ipAddress,
                $form->visitor->userAgent
            )
        );

        return new SuccessResponse(
            new Message('offer', $offer->serialize())
        );
    }
}
