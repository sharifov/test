<?php
use yii\bootstrap\Modal;

/**
 * @var $result []
 * @var $airlines []
 * @var $locations []
 */
$this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css');
$js = <<<JS
    $('.search_details__btn').click(function (e) {
        e.preventDefault();
        var modal = $('#flight-details');
        modal.find('.modal-title').html($(this).data('title'));
        var target = $($(this).data('target')).html();
        modal.find('.modal-body').html(target);
        modal.modal('show');
    });
JS;
$this->registerJs($js);
?>
<?php if($result || (isset($result['count']) && $result['count'] > 0)):?>
<div class="filters-panel">
    <div class="filters-aux">
        <div class="filters-total"><strong><?= $result['count']?> res</strong></div>
        <div class="filters-sort">
            <label for="sort" class="control-label">
                <i class="fa fa-sort"></i>
                Sort by</label>
            <select name="sort" id="sort" class="form-control">
                <option value="price">Price</option>
                <option value="time">Time</option>
            </select>
        </div>
    </div>

    <div class="search-filters">
        <!--Price-->
        <div class="filter filter--price dropdown js-filter">
            <a data-toggle="dropdown" href="#">Price
                <i class="icn-clear-filter js-clear-filter"></i></a>

            <!--Dropdown-->
            <div class="dropdown-menu dropdown-menu-right">
                <div class="search-filters__stops">
                    <h3 class="search-filters__header">
                        Price
                        <i class="search-filters__close-btn js-dropdown-close"></i>
                    </h3>
                    <div class="search-filters__body">
                        <span class="search-filters__slider-label" id="price-slider-label">1000.00</span>
                        <a href="#" class="search-filters__clear-link js-filter-reset">Clear</a>
                        <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="price-slider"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(1, 1);"></div></div><div class="noUi-origin" style="transform: translate(0%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="100.0" aria-valuetext="1000.00"><div class="noUi-tooltip">1000.00</div></div></div></div></div>
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
                            <input type="radio" id="any" name="stops" checked="">
                            <label for="any"></label>
                            <label for="any">Any</label>
                        </div>
                        <div class="form-group custom-radio">
                            <input type="radio" id="nonstop" name="stops">
                            <label for="nonstop"></label>
                            <label for="nonstop">Direct only</label>
                        </div>
                        <div class="form-group custom-radio">
                            <input type="radio" id="one-stop" name="stops">
                            <label for="one-stop"></label>
                            <label for="one-stop">Up to 1 stop</label>
                        </div>
                        <div class="form-group custom-radio">
                            <input type="radio" id="two-plus-stop" name="stops">
                            <label for="two-plus-stop"></label>
                            <label for="two-plus-stop">Up to 2 stops</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Travel Time-->
        <div class="filter filter--travel-time selected dropdown js-filter">
            <!--a href="#" data-toggle="dropdown">Время <i class="icn-clear-filter js-clear-filter"></i> </a-->
            <a href="#" data-toggle="dropdown">
                <span class="filter__summary js-filter-summary">
                    <span class="filter__summary-time">
                        <i class="icn-take-off-white"></i>
                        00:00 - 12:45
                    </span>
                     <span class="filter__summary-time">
                        <i class="icn-take-on-white"></i>
                        00:00 - 15:00
                    </span>
                </span>
                <i class="icn-clear-filter js-clear-filter"></i> </a>

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

                            <ul class="nav nav-tabs search-filters__tabs">
                                <li class="active"><a href="#filter-time-depart" data-toggle="tab">Depart</a></li>
                                <li><a href="#filter-time-return" data-toggle="tab">Return</a></li>
                            </ul>

                            <div class="tab-content search-filters__tab-content">
                                <div class="tab-pane active" id="filter-time-depart">
                                    <div class="search-filters__time-section">
                                        <h4 class="search-filters__flight-title">London → Paris</h4>
                                        <div class="search-filters__time-item takeoff" id="takeoff-dep">
                                            <h4 class="search-filters__section-subtitle">
                                                <i class="icn-take-off"></i>
                                                Takeoff:
                                                <span class="search-filters__time-value search-filters__default" id="takeoff-value-dep">100.00</span>
                                            </h4>
                                            <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="takeoff-slider-dep"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(0.909091, 1);"></div></div><div class="noUi-origin" style="transform: translate(-100%, 0px); z-index: 5;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="90.9" aria-valuenow="0.0" aria-valuetext="0.00"></div></div><div class="noUi-origin" style="transform: translate(-9.09091%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-upper" data-handle="1" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="90.9" aria-valuetext="100.00"><div class="noUi-tooltip">100.00</div></div></div></div></div>
                                        </div>
                                        <div id="search-time-departure">
                                            <div class="search-filters__time-item landing" id="landing-dep">
                                                <h4 class="search-filters__section-subtitle">
                                                    <i class="icn-take-on"></i>
                                                    Landing: <span class="search-filters__time-value" id="landing-value-dep">100.00</span>
                                                </h4>
                                                <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="landing-slider-dep"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(0.909091, 1);"></div></div><div class="noUi-origin" style="transform: translate(-100%, 0px); z-index: 5;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="90.9" aria-valuenow="0.0" aria-valuetext="0.00"></div></div><div class="noUi-origin" style="transform: translate(-9.09091%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-upper" data-handle="1" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="90.9" aria-valuetext="100.00"><div class="noUi-tooltip">100.00</div></div></div></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="filter-time-return">
                                    <div class="search-filters__time-section">
                                        <h4 class="search-filters__flight-title">Paris → London </h4>
                                        <div class="search-filters__time-item takeoff" id="takeoff-ret">
                                            <h4 class="search-filters__section-subtitle">
                                                <i class="icn-take-off"></i>
                                                Takeoff:
                                                <span class="search-filters__time-value search-filters__default" id="takeoff-value-ret">100.00</span>
                                            </h4>
                                            <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="takeoff-slider-ret"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(0.909091, 1);"></div></div><div class="noUi-origin" style="transform: translate(-100%, 0px); z-index: 5;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="90.9" aria-valuenow="0.0" aria-valuetext="0.00"></div></div><div class="noUi-origin" style="transform: translate(-9.09091%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-upper" data-handle="1" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="90.9" aria-valuetext="100.00"><div class="noUi-tooltip">100.00</div></div></div></div></div>
                                        </div>
                                        <div id="search-time-return">
                                            <div class="search-filters__time-item landing" id="landing-ret">
                                                <h4 class="search-filters__section-subtitle">
                                                    <i class="icn-take-on"></i>
                                                    Landing:
                                                    <span class="search-filters__time-value search-filters__default" id="landing-value-ret">100.00</span>
                                                </h4>
                                                <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="landing-slider-ret"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(0.909091, 1);"></div></div><div class="noUi-origin" style="transform: translate(-100%, 0px); z-index: 5;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="90.9" aria-valuenow="0.0" aria-valuetext="0.00"></div></div><div class="noUi-origin" style="transform: translate(-9.09091%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-upper" data-handle="1" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="90.9" aria-valuetext="100.00"><div class="noUi-tooltip">100.00</div></div></div></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <li class="search-filters__airlines-item custom-checkbox form-group js-filter-airl-item">
                                <input type="checkbox" id="a-su">
                                <label for="a-su"></label>
                                <label for="a-su">Aeroflot Russian Airlines</label>
                                <span class="search-filters__filter-only js-filter-only">Only</span>
                            </li>
                            <li class="search-filters__airlines-item custom-checkbox form-group js-filter-airl-item">
                                <input type="checkbox" id="a-af">
                                <label for="a-af"></label>
                                <label for="a-af">Air France</label>
                                <span class="search-filters__filter-only js-filter-only">Only</span>
                            </li>
                            <li class="search-filters__airlines-item custom-checkbox form-group js-filter-airl-item">
                                <input type="checkbox" id="a-9u">
                                <label for="a-9u"></label>
                                <label for="a-9u">Air Moldova</label>
                                <span class="search-filters__filter-only js-filter-only">Only</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!--Airports-->
        <div class="filter filter--airports dropdown js-filter">
            <a href="#" data-toggle="dropdown">Airports</a>

            <!--Dropdown-->
            <div class="dropdown-menu dropdown-menu-right">
                <div class="search-filters__airports">
                    <h3 class="search-filters__header">
                        Аэропорты
                        <i class="search-filters__close-btn js-dropdown-close"></i>
                    </h3>
                    <div class="search-filters__body">
                        <div class="search-filters__airports-departure">
                            <h4 class="search-filters__section-subtitle">Вылет из</h4>
                            <ul class="search-filters__airlines-list">
                                <li class="search-filters__airlines-item disabled custom-checkbox form-group">
                                    <input type="checkbox" id="kiv" disabled="">
                                    <label for="kiv"></label>
                                    <label for="kiv">KIV Chisinau Airport</label>
                                </li>
                            </ul>
                        </div>
                        <div class="search-filters__airports-travelling">
                            <h4 class="search-filters__section-subtitle">Прилет в</h4>
                            <ul class="search-filters__airlines-list">
                                <li class="search-filters__airlines-item disabled custom-checkbox form-group">
                                    <input type="checkbox" id="vie" disabled="">
                                    <label for="vie"></label>
                                    <label for="vie">VIE Vienna Schwechat Airport</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Duration-->
        <div class="filter filter--duration dropdown js-filter">
            <a href="#" data-toggle="dropdown">Duration
                <i class="icn-clear-filter js-clear-filter"></i></a>

            <!--Dropdown-->
            <div class="dropdown-menu dropdown-menu-right">
                <div class="search-filters__stops">
                    <h3 class="search-filters__header">
                        Duration
                        <i class="search-filters__close-btn js-dropdown-close"></i>
                    </h3>
                    <div class="search-filters__body">
                        <span class="search-filters__slider-label search-filters__default" id="duration-slider-label">1000.00</span>
                        <a href="#" class="search-filters__clear-link js-filter-reset">Clear</a>
                        <div class="search-filters__slider noUi-target noUi-ltr noUi-horizontal" id="duration-slider"><div class="noUi-base"><div class="noUi-connects"><div class="noUi-connect" style="transform: translate(0%, 0px) scale(1, 1);"></div></div><div class="noUi-origin" style="transform: translate(0%, 0px); z-index: 4;"><div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0" role="slider" aria-orientation="horizontal" aria-valuemin="0.0" aria-valuemax="100.0" aria-valuenow="100.0" aria-valuetext="1000.00"><div class="noUi-tooltip">1000.00</div></div></div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-quote fade in" id="flight-details" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"></div>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<?php foreach ($result['results'] as $key => $resultItem):?>
	<?= $this->render('_search_result_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines]);?>
<?php endforeach;?>
<?php else:?>
	<p>No search results</p>
<?php endif;?>
