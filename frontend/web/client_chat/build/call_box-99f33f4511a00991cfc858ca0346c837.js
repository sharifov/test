function toggleFab(){$(".prime").toggleClass("fa-close"),$(".prime").toggleClass("is-active"),$(".prime").toggleClass("is-visible"),$("#prime").toggleClass("is-float"),$(".call_box").toggleClass("is-visible"),$(".fab").toggleClass("is-visible")}function hideCallBox(e){switch(e){case 0:$("#call_box_converse").css("display","none"),$("#call_box_form").css("display","none"),$(".call_box_login").css("display","block"),$(".call_box_fullscreen_loader").css("display","none"),$("#call_box_fullscreen").css("display","none");break;case 1:$("#call_box_converse").css("display","block"),$("#call_box_form").css("display","none"),$(".call_box_login").css("display","none"),$(".call_box_fullscreen_loader").css("display","block");break;case 2:$("#call_box_converse").css("display","none"),$("#call_box_form").css("display","none"),$(".call_box_login").css("display","none"),$(".call_box_fullscreen_loader").css("display","block")}}function getCookie(e){var l=document.cookie.match(new RegExp("(?:^|; )"+e.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")+"=([^;]*)"));return l?decodeURIComponent(l[1]):void 0}function setCookie(e,l,o){var s=(o=o||{}).expires;if("number"==typeof s&&s){var c=new Date;c.setTime(c.getTime()+1e3*s),s=o.expires=c}s&&s.toUTCString&&(o.expires=s.toUTCString());var a=e+"="+(l=encodeURIComponent(l));for(var i in o){a+="; "+i;var n=o[i];!0!==n&&(a+="="+n)}document.cookie=a}function deleteCookie(e){setCookie(e,"",{expires:-1})}$(document).on("click","#prime",function(){toggleFab()}),$(document).on("click","#call_box_first_screen",function(e){hideCallBox(2)}),$(document).on("click","#call_box_third_screen",function(e){hideCallBox(0)}),$(document).on("click","#call_box_fullscreen_loader",function(e){$(".fullscreen").toggleClass("fa-window-maximize"),$(".fullscreen").toggleClass("fa-window-restore"),$(".call_box").toggleClass("call_box_fullscreen"),$(".fab").toggleClass("is-hide"),$(".header_img").toggleClass("change_img"),$(".img_container").toggleClass("change_img"),$(".call_box_header").toggleClass("call_box_header2"),$(".fab_field").toggleClass("fab_field2"),$(".call_box_converse").toggleClass("call_box_converse2")});