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
        "of_gid": "da722b84598b5559c5485617e67f3dd9",
        "of_uid": "of60409f7d83ef7",
        "of_name": "Offer 1",
        "of_lead_id": 513107,
        "of_status_id": 1,
        "of_client_currency": "EUR",
        "of_client_currency_rate": 0.92008,
        "of_app_total": 599.72,
        "of_client_total": 551.79,
        "of_status_name": "New",
        "quotes": [
            {
                "pq_gid": "260175221b9ef393f9462e20ff341699",
                "pq_name": "",
                "pq_order_id": 94,
                "pq_description": null,
                "pq_status_id": 3,
                "pq_price": 104.8,
                "pq_origin_price": 104.8,
                "pq_client_price": 104.8,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Applied",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 38,
                    "fq_source_id": null,
                    "fq_product_quote_id": 147,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkxMDAwL0tJVkxPTjIwMjEtMDctMTUqTE9+I0xPNTE2I0xPMjgxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-03-06\",\"totalPrice\":104.8,\"totalTax\":61.8,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"baseFare\":43,\"pubBaseFare\":43,\"baseTax\":61.8,\"markup\":0,\"comm\":0,\"price\":104.8,\"tax\":61.8,\"oBaseFare\":{\"amount\":43,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.8,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-07-15 18:25\",\"arrivalTime\":\"2021-07-15 19:15\",\"stop\":0,\"stops\":[],\"flightNumber\":\"516\",\"bookingClass\":\"V\",\"duration\":110,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"WAW\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"LO\",\"airEquipType\":\"DH4\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"I\",\"mileage\":508,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-07-16 07:30\",\"arrivalTime\":\"2021-07-16 09:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"281\",\"bookingClass\":\"V\",\"duration\":175,\"departureAirportCode\":\"WAW\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"LO\",\"airEquipType\":\"738\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"O\",\"mileage\":893,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":1020}],\"maxSeats\":9,\"paxCnt\":1,\"validatingCarrier\":\"LO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"28633fd6-f575-407f-97f1-050835c4bfb5\",\"availabilitySources\":\"P,P\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"ECONOMY SAVER\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTEwMDB8S0lWTE9OMjAyMS0wNy0xNQ==\",\"lang\":\"en\",\"rank\":7,\"cheapest\":true,\"fastest\":false,\"best\":false,\"bags\":0,\"country\":\"us\"},\"price\":104.8,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-07-15 18:25\",\"arrival\":\"2021-07-16 09:25\"}],\"bagFilter\":\"\",\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1020],\"totalDuration\":1020,\"topCriteria\":\"cheapest\",\"rank\":7}",
                    "fq_last_ticket_date": "2021-03-06",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 62,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 1,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "fqt_id": 93,
                            "fqt_key": null,
                            "fqt_duration": 1020,
                            "segments": [
                                {
                                    "fqs_departure_dt": "2021-07-15 18:25:00",
                                    "fqs_arrival_dt": "2021-07-15 19:15:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 516,
                                    "fqs_booking_class": "V",
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
                                    "fqs_fare_code": "V1SAV28",
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
                                            "qsb_flight_quote_segment_id": 241,
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
                                    "fqs_departure_dt": "2021-07-16 07:30:00",
                                    "fqs_arrival_dt": "2021-07-16 09:25:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 281,
                                    "fqs_booking_class": "V",
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
                                    "fqs_fare_code": "V1SAV28",
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
                                            "qsb_flight_quote_segment_id": 242,
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
                            "qpp_fare": "43.00",
                            "qpp_tax": "61.80",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.80",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.80",
                            "paxType": "ADT"
                        }
                    ]
                },
                "product": {
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513107,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": [
                    {
                        "pqo_name": "",
                        "pqo_description": "",
                        "pqo_status_id": 1,
                        "pqo_price": 100,
                        "pqo_client_price": 120,
                        "pqo_extra_markup": 20,
                        "productOption": {
                            "po_key": "travelGuard",
                            "po_name": "Travel Guard",
                            "po_description": ""
                        }
                    }
                ]
            },
            {
                "pq_gid": "4d140ea75cb0561ea8629ab3989c0fc5",
                "pq_name": "SGL.ST",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 374.92,
                "pq_origin_price": 362.24,
                "pq_client_price": 374.92,
                "pq_service_fee_sum": 12.68,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "hq_hash_key": "60316ad5c24b4feaee8f9e2264cbe699",
                    "hq_destination_name": "Rome",
                    "hq_hotel_name": "Patria",
                    "hq_request_hash": "163f5375651606c67cca80552e642914",
                    "hq_booking_id": null,
                    "hq_json_booking": null,
                    "hq_check_in_date": "2021-06-17",
                    "hq_check_out_date": "2021-06-24",
                    "hq_nights": 7,
                    "hotel_request": {
                        "ph_check_in_date": "2021-06-17",
                        "ph_check_out_date": "2021-06-24",
                        "ph_destination_code": "ROE",
                        "ph_destination_label": "Italy, Rome",
                        "destination_city": null
                    },
                    "hotel": {
                        "hl_name": "Patria",
                        "hl_star": "",
                        "hl_category_name": "3 STARS",
                        "hl_destination_name": "Rome",
                        "hl_zone_name": "Via Nazionale",
                        "hl_country_code": "IT",
                        "hl_state_code": "07",
                        "hl_description": "This hotel, of unparalleled beauty, offers comfortable and elegant rooms, fully equipped for a pleasant stay. Travelers looking to have a cultural adventure, learn about art, architecture, ruins and Roman history, are the favorites. With a privileged location just 14 kilometers from the Rome - Ciampino Airport, a few minutes from the Repubblica Metro Station and the Teatro dell'Opera, this hotel is surrounded by restaurants, markets, squares, museums and popular theaters, as well as It provides easy access to other important sites in the city, so it invites visitors to take out their spirit of adventure and curiosity in the Roman streets. The service staff is exceptional, distinguished by its excellent service and warm friendliness. Nobody misses the gastronomic delights of Rome in this hotel, from the cafeteria and the hotel menus, to the variety of restaurants that are just around the corner. The ideal place to vacation in the heart of Rome.",
                        "hl_address": "Via Torino, 36-37",
                        "hl_postal_code": "00184",
                        "hl_city": "ROME",
                        "hl_email": "INFO@HOTELPATRIA.IT",
                        "hl_web": null,
                        "hl_phone_list": [
                            {
                                "type": "PHONEBOOKING",
                                "number": "0039064880756"
                            },
                            {
                                "type": "PHONEHOTEL",
                                "number": "0039064880756"
                            },
                            {
                                "type": "PHONEMANAGEMENT",
                                "number": "0039064818254"
                            },
                            {
                                "type": "FAXNUMBER",
                                "number": "0039 064814872"
                            }
                        ],
                        "hl_image_list": [
                            {
                                "url": "01/017317/017317a_hb_a_007.jpg",
                                "type": "GEN"
                            }
                        ],
                        "hl_image_base_url": null,
                        "json_booking": null
                    },
                    "rooms": [
                        {
                            "hqr_room_name": "Single Standard",
                            "hqr_class": "NRF",
                            "hqr_amount": 335.41,
                            "hqr_currency": "USD",
                            "hqr_cancel_amount": "362.24",
                            "hqr_cancel_from_dt": "2021-03-03 22:59:00",
                            "hqr_board_name": "ROOM ONLY",
                            "hqr_rooms": 1,
                            "hqr_adults": 1,
                            "hqr_children": null
                        }
                    ]
                },
                "product": {
                    "pr_type_id": 2,
                    "pr_name": "",
                    "pr_lead_id": 513107,
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
        "response_id": 476,
        "request_dt": "2021-03-04 13:02:14",
        "response_dt": "2021-03-04 13:02:14",
        "execution_time": 0.121,
        "memory_usage": 1761344
    },
    "request": {
        "offerGid": "da722b84598b5559c5485617e67f3dd9"
    }
}
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
