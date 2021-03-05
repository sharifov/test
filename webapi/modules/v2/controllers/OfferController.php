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
     * {
    "status": 200,
    "message": "OK",
    "offer": {
        "of_gid": "efcd1c08edfe8da9e5188ca1bf2e53ca",
        "of_uid": "of604252d9cb097",
        "of_name": "Offer 1",
        "of_lead_id": 513112,
        "of_status_id": 1,
        "of_client_currency": "USD",
        "of_client_currency_rate": 1,
        "of_app_total": 4427.46,
        "of_client_total": 4427.46,
        "of_status_name": "New",
        "quotes": [
            {
                "pq_gid": "62abd30f7547b898f872db7645174ef8",
                "pq_name": "BCN | Opel Mokka or similar",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 4213.03,
                "pq_origin_price": 4070.56,
                "pq_client_price": 4213.03,
                "pq_service_fee_sum": 142.47,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "rcq_json_response": {
                        "car": {
                            "bags": "4",
                            "type": "mid-size-monospace",
                            "doors": "5",
                            "images": {
                                "SIZE67X36": "https://s1.pclncdn.com/rc-static/vehicles/partner/hz/size67x36/zeesimmr999.jpg",
                                "SIZE134X72": "https://s1.pclncdn.com/rc-static/vehicles/partner/hz/size134x72/zeesimmr999.jpg",
                                "SIZE268X144": "https://s1.pclncdn.com/rc-static/vehicles/partner/hz/size268x144/zeesimmr999.jpg"
                            },
                            "example": "Opel Mokka or similar",
                            "imageurl": "https://s1.pclncdn.com/rc-static/vehicles/partner/hz/size134x72/zeesimmr999.jpg",
                            "type_name": "Mid-Size Monospace",
                            "passengers": "5",
                            "description": "Mid-Size Monospace",
                            "vehicle_code": "IMMR",
                            "air_conditioning": "true",
                            "partner_discounts": null,
                            "automatic_transmission": "false"
                        },
                        "pickup": {
                            "latitude": "41.2996",
                            "location": "Terminal 1 And 2, Barcelona, 08820, ES",
                            "longitude": "2.0824",
                            "neighborhood": "",
                            "location_code": "BCN",
                            "city_center_distance": 1.4,
                            "location_information": "In-Terminal"
                        },
                        "dropoff": {
                            "latitude": "41.2996",
                            "location": "Terminal 1 And 2, Barcelona, 08820, ES",
                            "longitude": "2.0824",
                            "neighborhood": "",
                            "location_code": "BCN",
                            "city_center_distance": 1.4,
                            "location_information": "In-Terminal"
                        },
                        "partner": {
                            "code": "HZ",
                            "logo": "https://s1.pclncdn.com/rc-static/logos/106x27/HZ.png?v=2.0",
                            "name": "Hertz Corporation"
                        },
                        "priority": {
                            "company": null
                        },
                        "inventory": "PCLN_RC",
                        "price_details": {
                            "rate": "postpaid",
                            "mileage": "true",
                            "savings": null,
                            "currency": "USD",
                            "net_rate": "false",
                            "base_type": "week",
                            "sub_total": "580.05",
                            "base_price": "508.82",
                            "member_rate": null,
                            "total_price": "701.86",
                            "source_price": "422.57",
                            "source_total": "582.89",
                            "campaign_rate": null,
                            "display_price": "508.82",
                            "display_total": "701.86",
                            "source_symbol": "&#8364;",
                            "baseline_price": "508.82",
                            "baseline_total": "701.86",
                            "display_symbol": "$",
                            "pay_at_booking": "false",
                            "baseline_symbol": "$",
                            "num_rental_days": 8,
                            "source_currency": "EUR",
                            "display_currency": "USD",
                            "source_sub_total": "481.73",
                            "baseline_currency": "USD",
                            "display_sub_total": "580.05",
                            "free_cancellation": "true",
                            "total_price_float": 701.86,
                            "baseline_sub_total": "580.05",
                            "source_total_float": 582.89,
                            "display_total_float": 701.86,
                            "base_strikeout_price": "0.00",
                            "baseline_total_float": 701.86,
                            "total_strikeout_price": "0.00",
                            "source_price_strikeout": "0.00",
                            "source_total_strikeout": "0.00",
                            "display_price_strikeout": "0.00",
                            "display_total_strikeout": "0.00",
                            "baseline_price_strikeout": "0.00",
                            "baseline_total_strikeout": "0.00"
                        },
                        "car_reference_id": "LV2--_mykWePf-0tzOK2C4jt4gNjESmkUnYPJkll58GfHgTNNPo6QInGLVLLw2DJN4ELaXzsq8DlXsBXPckB8IxfPj2HhOhKVji2ZDm67Az77BrcuZAE1N3Vk1ly_WWiLuUbtKkEtbt5zOTq3_PZMKiv2QhLF-WhhOBGT8l3ICZapw5EPaMCmVj0Cyl-ZJhqU9pEXxA8uRQqBsCrN8g5FUJ52XhW9-zKO5vHU9DM2bqJxm1XpWriCiyAzTDywE7tQxxZUaUBI1DF5qpA7j-cTT5dzoTKttRJdMe2A3bcn6G0z6aW2mnCxfMJqhQaTgV51NesqNkht16sfJ6BPPunLilN7E5EjhIwfBJUhF7OitVm3Cz-Q9V7VlNLFAWBO_SUnmT0gFECRdezAdt28Ey0FEEMO8p4tc0TzPHgxTgnq5LLILhR2J4i_1Jzg_62m6t93k_K2WAz8j_3XEB0a7rOB_n6uA_PtlMl63Selb_SBek9VoECuYaLogZGxT-72I8olm8vgAXrO6hE4fZE3YHNXdm9VGRVFAFXpdHdTSDmPdGA4g5MYWdSEzucSwsopmhC8IvdRR3xcUG37U1RvTWyyG_wwD819yL_KU-1UpN5zIUI8hhxJ67LhV8G5ZjvkzEiVuF8e310x7x6a8xgqd5TU1t33udlnghE9TzMKFNxovt9QzhP8",
                        "contract_page_url": "https%3A%2F%2Fsecure.rezserver.com%2Fcar_rentals%2Fbook%2F%3Frefid%3D8965%26car_reference_id%3DLV2--_qJ6XceU5pl0xOjo066q7l_IWPm3BGc0VBir2HITrowrpY9SaWXiUEAhMGk_w343wNza4xNday7D67xxGf366wEn_XfMKufpE7OpMlr__6gM1RdXlewi-X3-o2iqHdTZK4mM6lC6BgaZwKv9HRJ0SlmWiDdoQwxIi9ncFLZWB-9T0U7oStZ0NrYTqYaKZ_rG7WMHYA2epY7AfwKCSREdHDCoNyduV48cf-5Vw5SVrBCbdOzu_-YyWykLNtoJWO1Ya9ybf2RP2DPwYjnQZrtqkLwiuq-rXDMYP91mvqInCVcXmH3AVATTsWcd2WKdcLuc5A3ATFiXroaH_qJC6yRas2yniU7aZKXtSX2Wywx2O58hqKVSV9u3vmfvemfgV9gyPNILF0_M77l8JSukHpDa_bkISgAuXs7PubZmmFCPEn7TAq9mSyljrg4vrB_h6jgDyVbKx3Z5DpnM3MV6vNyuZOR1iF9MspTRtgKFWnwjK5iLYuyKbVIvCuLI3Vlbx5zVQPr8gu4WXBMED7LSwxkkteVh7SWM1GUwZOfIgCNGm_u_MDvcfgpBtc8BdgwgJ1233TVHB2uquxEQZrw5t63SnmYSDO5aowqpb2NHm7woq6cY9k3g6DzeH3BTpEytpAzvL",
                        "creditCardRequired": 0,
                        "disclosure_required": false,
                        "driver_age_required": false
                    },
                    "rcq_model_name": "Opel Mokka or similar",
                    "rcq_category": "Mid-Size Monospace",
                    "rcq_image_url": "https://s1.pclncdn.com/rc-static/vehicles/partner/hz/size268x144/zeesimmr999.jpg",
                    "rcq_vendor_name": "Hertz Corporation",
                    "rcq_vendor_logo_url": "https://s1.pclncdn.com/rc-static/logos/106x27/HZ.png?v=2.0",
                    "rcq_options": {
                        "bags": "4",
                        "doors": "5",
                        "passengers": "5",
                        "automatic_transmission": "Yes"
                    },
                    "rcq_advantages": {
                        "bags": "4",
                        "doors": "5",
                        "passengers": "5",
                        "automatic_transmission": "Yes"
                    },
                    "search_request": {
                        "prc_product_id": 72,
                        "prc_pick_up_code": "BCN",
                        "prc_drop_off_code": "BCN",
                        "prc_pick_up_date": "2021-10-14",
                        "prc_drop_off_date": "2021-10-21",
                        "prc_pick_up_time": "11:00:00",
                        "prc_drop_off_time": "18:00:00"
                    },
                    "project_key": "ovago"
                },
                "product": {
                    "pr_type_id": 3,
                    "pr_name": "",
                    "pr_lead_id": 513112,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": []
            },
            {
                "pq_gid": "915c0caf84ad4fd749adf311c401bb88",
                "pq_name": "",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 104.8,
                "pq_origin_price": 104.8,
                "pq_client_price": 104.8,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 43,
                    "fq_source_id": null,
                    "fq_product_quote_id": 156,
                    "fq_gds": "T",
                    "fq_gds_pcc": "DVI",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkxMDAwL0tJVkxPTjIwMjEtMDgtMTIqTE9+I0xPNTE2I0xPMjgxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-03-08\",\"totalPrice\":104.8,\"totalTax\":61.8,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"baseFare\":43,\"pubBaseFare\":43,\"baseTax\":61.8,\"markup\":0,\"comm\":0,\"price\":104.8,\"tax\":61.8,\"oBaseFare\":{\"amount\":43,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":61.8,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-08-12 18:25\",\"arrivalTime\":\"2021-08-12 19:15\",\"stop\":0,\"stops\":[],\"flightNumber\":\"516\",\"bookingClass\":\"V\",\"duration\":110,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"WAW\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"LO\",\"airEquipType\":\"DH4\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"I\",\"mileage\":508,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-08-13 07:30\",\"arrivalTime\":\"2021-08-13 09:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"281\",\"bookingClass\":\"V\",\"duration\":175,\"departureAirportCode\":\"WAW\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"LO\",\"airEquipType\":\"738\",\"marketingAirline\":\"LO\",\"marriageGroup\":\"O\",\"mileage\":893,\"cabin\":\"Y\",\"cabinIsBasic\":true,\"brandId\":\"685421\",\"brandName\":\"ECONOMY SAVER\",\"meal\":\"\",\"fareCode\":\"V1SAV28\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":0}},\"recheckBaggage\":false}],\"duration\":1020}],\"maxSeats\":9,\"paxCnt\":1,\"validatingCarrier\":\"LO\",\"gds\":\"T\",\"pcc\":\"DVI\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"0bed8fa4-77b2-418e-a1f0-bb821efd16a7\",\"availabilitySources\":\"S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"ECONOMY SAVER\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTEwMDB8S0lWTE9OMjAyMS0wOC0xMg==\",\"lang\":\"en\",\"rank\":7,\"cheapest\":true,\"fastest\":false,\"best\":false,\"bags\":0,\"country\":\"us\"},\"price\":104.8,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-08-12 18:25\",\"arrival\":\"2021-08-13 09:25\"}],\"bagFilter\":\"\",\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1020],\"totalDuration\":1020,\"topCriteria\":\"cheapest\",\"rank\":7}",
                    "fq_last_ticket_date": "2021-03-08",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 70,
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
                            "fqt_id": 98,
                            "fqt_key": null,
                            "fqt_duration": 1020,
                            "segments": [
                                {
                                    "fqs_departure_dt": "2021-08-12 18:25:00",
                                    "fqs_arrival_dt": "2021-08-12 19:15:00",
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
                                            "qsb_flight_quote_segment_id": 251,
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
                                    "fqs_departure_dt": "2021-08-13 07:30:00",
                                    "fqs_arrival_dt": "2021-08-13 09:25:00",
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
                                            "qsb_flight_quote_segment_id": 252,
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
                    "pr_lead_id": 513112,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null
                },
                "productQuoteOptions": []
            },
            {
                "pq_gid": "b1d227705f81bb914a8809deb7f985aa",
                "pq_name": "BED.ST",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 109.63,
                "pq_origin_price": 105.92,
                "pq_client_price": 109.63,
                "pq_service_fee_sum": 3.71,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "hq_hash_key": "bee2e5c5bba6321808142d05f08f5c19",
                    "hq_destination_name": "Barcelona",
                    "hq_hotel_name": "Mellow Youth Hostel",
                    "hq_request_hash": "3d5016fd7cc683dc450d302829fbffff",
                    "hq_booking_id": null,
                    "hq_json_booking": null,
                    "hq_check_in_date": "2021-09-16",
                    "hq_check_out_date": "2021-09-23",
                    "hq_nights": 7,
                    "hotel_request": {
                        "ph_check_in_date": "2021-09-16",
                        "ph_check_out_date": "2021-09-23",
                        "ph_destination_code": "BCN",
                        "ph_destination_label": "Spain, Barcelona",
                        "destination_city": "Barcelona"
                    },
                    "hotel": {
                        "hl_name": "Mellow Youth Hostel",
                        "hl_star": null,
                        "hl_category_name": "HOSTEL",
                        "hl_destination_name": "Barcelona",
                        "hl_zone_name": "Gracia Area",
                        "hl_country_code": "ES",
                        "hl_state_code": "08",
                        "hl_description": "If the customer is looking for a neat, modern and new hostel which is safe and peaceful, this is the host in Barcelona. It has great panoramic views over the city, the sea and Tibitabo Mountain. All the city´s tourist attractions are just 15-20 min away, situated in a quiet suburb of Barcelona just 15 to 20 minutes' walk from Parc Güell. The building is new and comfortable, all the rooms are bright and exterior with large windows and most of them have balconies. The common areas are beautiful and spacious, which includes a chill out place. There is a communal area with a dining room and free Wi-Fi. The bright, spacious dormitories are heated and have tiled floors, each has a shared bathroom with a hairdryer. Solar panels are used to heat the water at Hillview.",
                        "hl_address": "AGUILAR, 54",
                        "hl_postal_code": "08032",
                        "hl_city": "BARCELONA",
                        "hl_email": "admin@mellowbarcelona.com",
                        "hl_web": null,
                        "hl_phone_list": [
                            {
                                "type": "PHONEBOOKING",
                                "number": "0034933010037"
                            },
                            {
                                "type": "PHONEHOTEL",
                                "number": "0034934294533"
                            },
                            {
                                "type": "PHONEMANAGEMENT",
                                "number": "645707626"
                            }
                        ],
                        "hl_image_list": [
                            {
                                "url": "38/388839/388839a_hb_a_001.jpg",
                                "type": "GEN"
                            }
                        ],
                        "hl_image_base_url": null,
                        "json_booking": null
                    },
                    "rooms": [
                        {
                            "hqr_room_name": "Bed In Dormitory Standard",
                            "hqr_class": "NOR",
                            "hqr_amount": 98.07,
                            "hqr_currency": "USD",
                            "hqr_cancel_amount": "98.07",
                            "hqr_cancel_from_dt": "2021-06-17 21:59:00",
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
                    "pr_lead_id": 513112,
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
        "response_id": 491,
        "request_dt": "2021-03-05 15:58:14",
        "response_dt": "2021-03-05 15:58:15",
        "execution_time": 0.12,
        "memory_usage": 2058016
    },
    "request": {
        "offerGid": "efcd1c08edfe8da9e5188ca1bf2e53ca"
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
