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
    ) {
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
     *{
    "status": 200,
    "message": "OK",
    "offer": {
        "of_gid": "ea6dc06421db46b5a77e8505d0934f38",
        "of_uid": "of604642e300c54",
        "of_name": "Offer 2",
        "of_lead_id": 513111,
        "of_status_id": 1,
        "of_client_currency": "USD",
        "of_client_currency_rate": 1,
        "of_app_total": 343.5,
        "of_client_total": 343.5,
        "of_status_name": "New",
        "quotes": [
            {
                "pq_gid": "f81636da78e007fcc6653d26a3650285",
                "pq_name": "",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 343.5,
                "pq_origin_price": 343.5,
                "pq_client_price": 343.5,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 47,
                    "fq_source_id": null,
                    "fq_product_quote_id": 159,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMDktMTYqTE9+I0xPNTE2I0xPMjgxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-03-11\",\"totalPrice\":343.5,\"totalTax\":184.5,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":58,\"pubBaseFare\":58,\"baseTax\":61.5,\"markup\":0,\"comm\":0,\"price\":119.5,\"tax\":61.5,\"oBaseFare\":{\"amount\":58,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.5,\"currency\":\"USD\"}},\"CHD\":{\"codeAs\":\"CHD\",\"cnt\":1,\"baseFare\":43,\"pubBaseFare\":43,\"baseTax\":61.5,\"markup\":0,\"comm\":0,\"price\":104.5,\"tax\":61.5,\"oBaseFare\":{\"amount\":43,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.5,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-09-16 18:25\",\"arrivalTime\":\"2021-09-16 19:15\",\"stop\":0,\"stops\":[],\"flightNumber\":\"516\",\"bookingClass\":\"S\",\"duration\":110,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"WAW\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"LO\",\"airEquipType\":\"DH4\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"I\",\"mileage\":508,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"S1SAV14\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0},\"CHD\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-09-17 07:30\",\"arrivalTime\":\"2021-09-17 09:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"281\",\"bookingClass\":\"S\",\"duration\":175,\"departureAirportCode\":\"WAW\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"LO\",\"airEquipType\":\"738\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"O\",\"mileage\":893,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"S1SAV14\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0},\"CHD\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":1020}],\"maxSeats\":7,\"paxCnt\":3,\"validatingCarrier\":\"LO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"b58ab976-7391-40b0-a1d2-44a2821d44cf\",\"availabilitySources\":\"S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":7}},\"ngsFeatures\":{\"stars\":1,\"name\":\"ECONOMY SAVER\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0wOS0xNg==\",\"lang\":\"en\",\"rank\":6,\"cheapest\":true,\"fastest\":false,\"best\":false,\"bags\":0,\"country\":\"us\"},\"price\":119.5,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-09-16 18:25\",\"arrival\":\"2021-09-17 09:25\"}],\"bagFilter\":\"\",\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1020],\"totalDuration\":1020,\"topCriteria\":\"cheapest\",\"rank\":6}",
                    "fq_last_ticket_date": "2021-03-11",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 76,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 1,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "fqt_id": 100,
                            "fqt_uid": "fqt6046483f5c6cf",
                            "fqt_key": null,
                            "fqt_duration": 1020,
                            "segments": [
                                {
                                    "fqs_uid": "fqs6046483e349c6",
                                    "fqs_departure_dt": "2021-09-16 18:25:00",
                                    "fqs_arrival_dt": "2021-09-16 19:15:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 516,
                                    "fqs_booking_class": "S",
                                    "fqs_duration": 110,
                                    "fqs_departure_airport_iata": "KIV",
                                    "fqs_departure_airport_terminal": "",
                                    "fqs_arrival_airport_iata": "WAW",
                                    "fqs_arrival_airport_terminal": "",
                                    "fqs_operating_airline": "LO",
                                    "fqs_marketing_airline": "LO",
                                    "fqs_air_equip_type": "DH4",
                                    "fqs_marriage_group": "I",
                                    "fqs_cabin_class": "Y",
                                    "fqs_meal": "",
                                    "fqs_fare_code": "S1SAV14",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "fqs_mileage": 508,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Warsaw",
                                    "operating_airline": "LOT Polish Airlines",
                                    "marketing_airline": "LOT Polish Airlines",
                                    "baggages": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 255,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 255,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "fqs_uid": "fqs6046483e37fc7",
                                    "fqs_departure_dt": "2021-09-17 07:30:00",
                                    "fqs_arrival_dt": "2021-09-17 09:25:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 281,
                                    "fqs_booking_class": "S",
                                    "fqs_duration": 175,
                                    "fqs_departure_airport_iata": "WAW",
                                    "fqs_departure_airport_terminal": "",
                                    "fqs_arrival_airport_iata": "LHR",
                                    "fqs_arrival_airport_terminal": "2",
                                    "fqs_operating_airline": "LO",
                                    "fqs_marketing_airline": "LO",
                                    "fqs_air_equip_type": "738",
                                    "fqs_marriage_group": "O",
                                    "fqs_cabin_class": "Y",
                                    "fqs_meal": "",
                                    "fqs_fare_code": "S1SAV14",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "fqs_mileage": 893,
                                    "departureLocation": "Warsaw",
                                    "arrivalLocation": "London",
                                    "operating_airline": "LOT Polish Airlines",
                                    "marketing_airline": "LOT Polish Airlines",
                                    "baggages": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 256,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 256,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "58.00",
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "58.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "58.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "ADT"
                        },
                        {
                            "qpp_fare": "43.00",
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "CHD"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6046483b5f034",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6046483b61c29",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6046483b64835",
                            "fp_pax_id": null,
                            "fp_pax_type": "CHD",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                },
                "product": {
                    "pr_gid": "",
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513111,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": []
            }
        ]
    },
    "technical": {
        "action": "v2/offer/view",
        "response_id": 496,
        "request_dt": "2021-03-08 15:57:33",
        "response_dt": "2021-03-08 15:57:33",
        "execution_time": 0.104,
        "memory_usage": 1290648
    },
    "request": {
        "offerGid": "ea6dc06421db46b5a77e8505d0934f38"
    }
}
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
