<?php
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\models\Lead;

/**
 * @var $result []
 * @var $airlines []
 * @var $locations []
 * @var $leadId int
 * @var $gds string
 * @var $lead Lead
 */
if($result && (isset($result['count']) && $result['count'] > 0)):
    $js = <<<JS
    $(document).on('click','.search_details__btn', function (e) {
        e.preventDefault();
        var modal = $('#flight-details__modal');
        modal.find('.modal-header h2').html($(this).data('title'));
        var target = $($(this).data('target')).html();
        modal.find('.modal-body').html(target);
        modal.modal('show');
    });

    $(document).on('change', '#sort_search', function(e) {
        var self = $(this).find('option:selected')[0];
        $('.search-results__wrapper').html(
            $('.search-result__quote').toArray().sort(function(a,b){
                var a = +a.getAttribute('data-'+self.getAttribute('data-field'));
                var b = +b.getAttribute('data-'+self.getAttribute('data-field'));
                if(self.getAttribute('data-sort') === 'asc'){
                    return a - b;
                }
                return b - a;
            })
        )
    });

    var searchResult = new SearchResult();    
    searchResult.init();

JS;
    $this->registerJs($js);
    ?>
    <?php

    $minPrice = $result['results'][0]['prices']['totalPrice'];
    if(isset($result['results'][0]['passengers']['ADT'])){
        $minPrice = $result['results'][0]['passengers']['ADT']['price'];
    }elseif (isset($result['results'][0]['passengers']['CHD'])){
        $minPrice = $result['results'][0]['passengers']['CHD']['price'];
    }elseif (isset($result['results'][0]['passengers']['INF'])){
        $minPrice = $result['results'][0]['passengers']['INF']['price'];
    }
    $lastResult = end($result['results']);
    $maxPrice = $lastResult['prices']['totalPrice'];
    if(isset($lastResult['passengers']['ADT'])){
        $maxPrice = $lastResult['passengers']['ADT']['price'];
    }elseif (isset($lastResult['passengers']['CHD'])){
        $maxPrice = $lastResult['passengers']['CHD']['price'];
    }elseif (isset($lastResult['passengers']['INF'])){
        $maxPrice = $lastResult['passengers']['INF']['price'];
    }
    ?>
    <div class="filters-panel">
        <div class="filters-aux">
            <div class="filters-total"><strong id="search-results__cnt"><?= $result['count']?></strong> res</div>
            <div class="filters-sort">
                <label for="sort" class="control-label"><i class="fa fa-sort"></i> Sort by</label>
                <select name="sort" id="sort_search" class="form-control">
                    <option value="price_asc" data-field="price" data-sort="asc">Price (ASC)</option>
                    <option value="price_desc" data-field="price" data-sort="desc">Price (DESC)</option>
                    <option value="duration_asc" data-field="durationmax" data-sort="asc">Duration (ASC)</option>
                    <option value="duration_desc" data-field="durationmax" data-sort="desc">Duration (DESC)</option>
                </select>
            </div>
        </div>
        <div class="search-filters">
            <!--Fare types-->
            <div class="filter filter--fareType dropdown js-filter" id="filter-fareType">
                <a data-toggle="dropdown" href="#">Fare type
                    <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__fareType">
                        <h3 class="search-filters__header">
                            Fare type
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <div class="form-group custom-checkbox js-filter-fareType-item">
                                <input type="checkbox" id="PUB" name="fareType" checked>
                                <label for="PUB"></label>
                                <label for="PUB">Public</label>
                            </div>
                            <div class="form-group custom-checkbox js-filter-fareType-item">
                                <input type="checkbox" id="SR" name="fareType" checked>
                                <label for="SR"></label>
                                <label for="SR">Private</label>
                            </div>
                            <div class="form-group custom-checkbox js-filter-fareType-item">
                                <input type="checkbox" id="COMM" name="fareType" checked>
                                <label for="COMM"></label>
                                <label for="COMM">Commission</label>
                            </div>
                            <div class="form-group custom-checkbox js-filter-fareType-item">
                                <input type="checkbox" id="TOUR" name="fareType" checked>
                                <label for="TOUR"></label>
                                <label for="TOUR">Tour</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Price-->
            <div class="filter filter--price dropdown js-filter">
                <a data-toggle="dropdown" href="#">Price <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__stops">
                        <h3 class="search-filters__header">
                            Price
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <span class="search-filters__slider-label" id="price-slider-label">To $500</span>
                            <a href="#" class="search-filters__clear-link js-filter-reset">Clear</a>
                            <div class="search-filters__slider" id="price-slider" data-min="<?= $minPrice?>" data-max="<?= $maxPrice?>"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!--Connections-->
            <div class="filter filter--connections dropdown js-filter">
                <a data-toggle="dropdown" href="#">Stops
                    <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__stops">
                        <h3 class="search-filters__header">
                            Stops
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <div class="form-group custom-radio">
                                <input type="radio" id="any" name="stops" checked>
                                <label for="any"></label>
                                <label for="any">Any</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="nonstop" name="stops">
                                <label for="nonstop"></label>
                                <label for="nonstop">Direct only</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="stop-1" name="stops">
                                <label for="one-stop"></label>
                                <label for="one-stop">Up to 1 stop</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="stop-2" name="stops">
                                <label for="two-plus-stop"></label>
                                <label for="two-plus-stop">Up to 2 stops</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Airlines-->
            <div class="filter filter--airlines dropdown js-filter" id="filter-airlines">

                <a data-toggle="dropdown" data-placement="auto" href="#">Airlines</a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__airlines">
                        <h3 class="search-filters__header">
                            Airlines
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <div class="search-filters__airlines-all">
                                All Airlines
                                <label class="switch search-filters__switch">
                                    <input type="checkbox" class="switch__input js-all-airlines">
                                    <span class="switch__slider"></span>
                                </label>
                            </div>
                            <ul class="search-filters__airlines-list">
                                <?php foreach ($airlines as $iata => $airline):?>
                                    <li class="search-filters__airlines-item custom-checkbox form-group js-filter-airl-item">
                                        <input type="checkbox" id="<?= $iata?>" checked>
                                        <label for="<?= $iata?>"></label>
                                        <label for="<?= $iata?>"><?= $airline?></label>
                                        <span class="search-filters__filter-only js-filter-only">Only</span>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!--Duration-->
            <div class="filter filter--duration dropdown js-filter">
                <a href="#" data-toggle="dropdown">Trip Duration <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__stops">
                        <h3 class="search-filters__header">
                            Trip Duration
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <span class="search-filters__slider-label search-filters__default" id="duration-slider-label">Any</span>
                            <a href="#" class="search-filters__clear-link js-filter-reset">Clear</a>
                            <div class="search-filters__slider" id="duration-slider"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter filter--travel-time dropdown js-filter">
                <a href="#" data-toggle="dropdown">
                    <span class="filter__summary js-filter-summary">Time</span>
                    <i class="icn-clear-filter js-clear-filter"></i>
                </a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__time">
                        <h3 class="search-filters__header">
                            Time
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <!--Flight Depart-->
                            <div class="search-filters__flight">
                                <?php $tabTtl = ['Depart','Return'];?>
                                <?php $cntTrips = count($lead->leadFlightSegments);?>
                                <?php if($cntTrips > 1):?>
                                    <ul class="nav nav-tabs search-filters__tabs">
                                        <?php foreach ($lead->leadFlightSegments as $idx => $flSegment):?>
                                            <li<?php if($idx == 0):?> class="active"<?php endif;?>>
                                                <a href="#filter-time-<?= $idx?>" data-toggle="tab">
                                                    <?php if($cntTrips > 2):?>Trip <?= $idx+1?><?php else:?><?= $tabTtl[$idx]?><?php endif;?>
                                                </a>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                <?php endif;?>

                                <div class="tab-content search-filters__tab-content">
                                    <?php foreach ($lead->leadFlightSegments as $idx => $flSegment):?>
                                        <div class="tab-pane<?php if($idx == 0):?> active<?php endif;?>" id="filter-time-<?= $idx?>" data-index="<?= $idx?>">
                                            <div class="search-filters__time-section">
                                                <h4 class="search-filters__flight-title"><?= $flSegment->origin?> &#8594; <?= $flSegment->destination?></h4>
                                                <div class="search-filters__time-item takeoff" data-id="landing-time">
                                                    <h4 class="search-filters__section-subtitle">
                                                        <i class="icn-take-off"></i>
                                                        Takeoff:
                                                        <span class="search-filters__time-value search-filters__default" data-id="landing-value-time"><span> Any Time</span></span>
                                                    </h4>
                                                    <div class="search-filters__slider" data-id="landing-slider-time" data-direction="depart"></div>
                                                </div>
                                                <div id="search-time-departure">
                                                    <div class="search-filters__time-item landing" data-id="landing-time">
                                                        <h4 class="search-filters__section-subtitle">
                                                            <i class="icn-take-on"></i>
                                                            Landing:
                                                            <span class="search-filters__time-value" data-id="landing-value-time"><span> Any Time</span></span>
                                                        </h4>
                                                        <div class="search-filters__slider" data-id="landing-slider-time" data-direction="arrival"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--Airport Change-->
            <div class="filter filter--airportChange dropdown js-filter">
                <a data-toggle="dropdown" href="#">Airport Change
                    <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__changeAirport">
                        <h3 class="search-filters__header">
                            Airport Change
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <div class="form-group custom-radio">
                                <input type="radio" id="anyAirport" name="changeAirport" checked>
                                <label for="anyAirport"></label>
                                <label for="anyAirport">Any</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="nochange" name="changeAirport">
                                <label for="nochange"></label>
                                <label for="nochange">No Airport Change</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Baggage-->
            <div class="filter filter--baggage dropdown js-filter">
                <a data-toggle="dropdown" href="#">Baggage
                    <i class="icn-clear-filter js-clear-filter"></i></a>

                <!--Dropdown-->
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="search-filters__baggae">
                        <h3 class="search-filters__header">
                            Baggage
                            <i class="search-filters__close-btn js-dropdown-close"></i>
                        </h3>
                        <div class="search-filters__body">
                            <div class="form-group custom-radio">
                                <input type="radio" id="anyBaggage" name="baggage" checked>
                                <label for="anyBaggage"></label>
                                <label for="anyBaggage">Any</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="pcs1" name="baggage">
                                <label for="pcs1"></label>
                                <label for="pcs1">1+</label>
                            </div>
                            <div class="form-group custom-radio">
                                <input type="radio" id="pcs2" name="baggage">
                                <label for="pcs2"></label>
                                <label for="pcs2">2+</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="search-results__wrapper">
        <?php $n = 0; ?>
        <?php foreach ($result['results'] as $key => $resultItem):?>
            <?= $this->render('_search_result_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines]);?>
            <?php
            $n++;
            if($n > 50) {
                //break;
            }
            ?>
        <?php endforeach;?>
    </div>
<?php else:?>
    <div class="search-results__wrapper">
        <p>No search results</p>
    </div>
<?php endif;?>