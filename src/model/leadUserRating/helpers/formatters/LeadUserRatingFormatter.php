<?php

namespace src\model\leadUserRating\helpers\formatters;

class LeadUserRatingFormatter
{
    public static function asStarRating(int $rating, int $leadId, bool $canUpdateRating): string
    {
        $disabled = $canUpdateRating ? '' : 'disabled';
        $str = '<fieldset class="rate-input-group" ">
            <input type="radio"
                name="lead-rating-' . $leadId . '" 
                class="lead-rating-star"
                data-default-value="' . $rating . '"
                data-lead-id="' . $leadId . '"  
                id="rate-5-' . $leadId . '"  
                value="5" ' . ($rating === 5 ? 'checked' : '') . ' ' . $disabled . '>
            
            <label for="rate-5-' . $leadId . '"></label>
            
            <input type="radio"
                name="lead-rating-' . $leadId . '"
                class="lead-rating-star"
                data-default-value="' . $rating . '"  
                data-lead-id="' . $leadId . '"
                id="rate-4-' . $leadId . '"
                value="4" ' . ($rating === 4 ? 'checked' : '') . ' ' . $disabled . '>
              
            <label for="rate-4-' . $leadId . '"></label>
            
            <input type="radio"
                name="lead-rating-' . $leadId . '"
                class="lead-rating-star"
                data-default-value="' . $rating . '"
                data-lead-id="' . $leadId . '" 
                id="rate-3-' . $leadId . '"
                value="3" ' . ($rating === 3 ? 'checked' : '') . ' ' . $disabled . '>
             
            <label for="rate-3-' . $leadId . '"></label>
            
            <input type="radio"
                name="lead-rating-' . $leadId . '"
                class="lead-rating-star"
                data-default-value="' . $rating . '"
                data-lead-id="' . $leadId . '"
                id="rate-2-' . $leadId . '" value="2" ' . ($rating === 2 ? 'checked' : '') . ' ' . $disabled . '>
             
            <label for="rate-2-' . $leadId . '"></label>
            
            <input type="radio"
                name="lead-rating-' . $leadId . '"
                class="lead-rating-star"
                data-default-value="' . $rating . '"
                data-lead-id="' . $leadId . '"
                id="rate-1-' . $leadId . '"
                value="1" ' . ($rating === 1 ? 'checked' : '') . ' ' . $disabled . '>
            <label for="rate-1-' . $leadId . '"></label>
        </fieldset>';
        return $str;
    }
}
