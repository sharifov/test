<?php
use yii\bootstrap\Modal;
use yii\helpers\Url;

/**
 * @var $result []
 * @var $airlines []
 * @var $locations []
 * @var $leadId int
 * @var $gds string
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
$maxPrice = end($result['results'])['prices']['totalPrice'];
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
                        <span class="search-filters__slider-label search-filters__default" id="duration-slider-label">Any</span>
                        <a href="#" class="search-filters__clear-link js-filter-reset">Clear</a>
                        <div class="search-filters__slider" id="duration-slider"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="search-results__wrapper">
<?php foreach ($result['results'] as $key => $resultItem):?>
	<?= $this->render('_search_result_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines]);?>
<?php endforeach;?>
</div>
<?php else:?>
<div class="search-results__wrapper">
	<p>No search results</p>
</div>
<?php endif;?>