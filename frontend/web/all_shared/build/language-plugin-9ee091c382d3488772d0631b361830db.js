var helpers=function(){var e=!1;function t(){setTimeout(function(){$(".alert-box").remove()},5e3)}function n(e,t){return $("<div>").attr({class:"alert-box",role:"alert"}).addClass("alert").addClass(void 0===t?"alert-info":t).text(e)}return{post:function(t,n){!1===e&&(e=!0,$.post(t,n,$.proxy(function(t){e=!1,this.showTooltip(t)},this),"json"))},showTooltip:function(e){if(0===$("#alert-tooltip").length){var t=$("<div>").attr({id:"alert-tooltip"}).addClass(0===e.length?"green":"red").append($("<span>").addClass("glyphicon").addClass(0===e.length?" glyphicon-ok":"glyphicon-remove"));$("body").append(t),setTimeout(function(){$("#alert-tooltip").remove()},500)}},showMessages:function(e,o){$(".alert-box").length?$(".alert-box").append(e):($(void 0===o?$("body").find(".container").eq(1):o).prepend(n(e)),t())},showErrorMessages:function(e,n){for(i in e){var o=0;if($messages=new Array,"object"==typeof e[i])for(j in e[i])$messages[o++]=e[i][j];else $messages[o++]=e[i];this.showErrorMessage($messages.join(" "),n+i)}t()},showErrorMessage:function(e,t){$(t).next().html(n(e,"alert-danger"))}}}();$(document).ready(function(){Language.init()});var Language={init:function(){$("#languages").on("change","select.status",$.proxy(function(e){this.changeStatus($(e.currentTarget))},this))},changeStatus:function(e){var t={language_id:e.attr("id"),status:e.val()};helpers.post(e.data("url"),t)}};