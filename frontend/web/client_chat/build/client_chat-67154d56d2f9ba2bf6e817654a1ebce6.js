!function(e){"use strict";function t(e){return $("#"+e).serialize()}e._cc_apply_filter=function(t,n,o,i,s,a,r,l,u){let c=$("#load-channels-txt"),d=new URLSearchParams(e.location.search),g=n+"?status="+o+"&group="+a+"&read="+r+"&agentId="+l+"&createdDate="+u;i>0&&(g=g+"&dep="+i),s>0&&(g=g+"&project="+s),t>0&&(g=g+"&channelId="+t),$.ajax({type:"post",url:g,dataType:"json",cache:!1,data:{page:1,channelId:d.get("channelId")|t},beforeSend:function(){$("#_channel_list_wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"> </i></div></div>')},success:function(g){$("._cc-list-wrapper").html(g.html),g.html?(c.html("Load more"),$("#cc-dialogs-wrapper").attr("data-page",g.page),e.allDialogsLoaded=!1):(c.html("All conversations are loaded").addClass("disabled"),e.allDialogsLoaded=!0),d.set("page",1),d.set("channelId",t),d.set("status",o),d.set("dep",i),d.set("project",s),d.set("group",a),d.set("read",r),d.set("agentId",l),d.set("createdDate",u),e.history.replaceState({},"",n+"?"+d.toString())},complete:function(){$("#_channel_list_wrapper").find("#_cc-load").remove()}})},e.updateClientChatFilter=function(n,o,i){let s=t(n),a=new URLSearchParams(e.location.search);a.delete("chid"),a.delete("page");let r=new URLSearchParams;a.forEach(function(e,t){0!==t.indexOf(o)&&r.set(t,e)}),e.history.replaceState({},"",i+"?"+s+"&"+r.toString()),$(".cc_btn_read_filter").removeClass("active"),$("._rc-iframe").hide(),$("#_client-chat-info").html(""),$("#_client-chat-note").html(""),$("#canned-response-wrap").addClass("disabled"),$("#couch_note_box").html(""),pjaxReload({container:"#pjax-client-chat-channel-list"}),e.allDialogsLoaded=!1,e.refreshChannelList(),e.removeChatFromActiveConnection()},e.getClientChatLoadMoreUrl=function(n,o){let i=t(n),s=new URLSearchParams(e.location.search);s.delete("page");let a=new URLSearchParams;return s.forEach(function(e,t){0!==t.indexOf(o)&&a.set(t,e)}),i+(a.toString()?"&"+a.toString():"")}}(window),function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports&&"function"==typeof require?require("jquery"):jQuery)}(function(e){"use strict";function t(n,o){var i=this;i.element=n,i.el=e(n),i.suggestions=[],i.badQueries=[],i.selectedIndex=-1,i.currentValue=i.element.value,i.timeoutId=null,i.cachedResponse={},i.onChangeTimeout=null,i.onChange=null,i.isLocal=!1,i.suggestionsContainer=null,i.noSuggestionsContainer=null,i.options=e.extend(!0,{},t.defaults,o),i.classes={selected:"autocomplete-selected",suggestion:"autocomplete-suggestion"},i.hint=null,i.hintValue="",i.selection=null,i.initialize(),i.setOptions(o)}var n={escapeRegExChars:function(e){return e.replace(/[|\\{}()[\]^$+*?.]/g,"\\$&")},createNode:function(e){var t=document.createElement("div");return t.className=e,t.style.position="absolute",t.style.display="none",t}},o=27,i=9,s=13,a=38,r=39,l=40,u=e.noop;t.utils=n,e.Autocomplete=t,t.defaults={ajaxSettings:{},autoSelectFirst:!1,appendTo:"body",serviceUrl:null,lookup:null,onSelect:null,width:"auto",minChars:1,maxHeight:300,deferRequestBy:0,params:{},formatResult:function(e,t){if(!t)return e.value;var o="("+n.escapeRegExChars(t)+")";return e.value.replace(new RegExp(o,"gi"),"<strong>$1</strong>").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/&lt;(\/?strong)&gt;/g,"<$1>")},formatGroup:function(e,t){return'<div class="autocomplete-group">'+t+"</div>"},delimiter:null,zIndex:9999,type:"GET",noCache:!1,onSearchStart:u,onSearchComplete:u,onSearchError:u,preserveInput:!1,containerClass:"autocomplete-suggestions",tabDisabled:!1,dataType:"text",currentRequest:null,triggerSelectOnValidInput:!0,preventBadQueries:!0,lookupFilter:function(e,t,n){return-1!==e.value.toLowerCase().indexOf(n)},paramName:"query",transformResult:function(t){return"string"==typeof t?e.parseJSON(t):t},showNoSuggestionNotice:!1,noSuggestionNotice:"No results",orientation:"bottom",forceFixPosition:!1},t.prototype={initialize:function(){var n,o=this,i="."+o.classes.suggestion,s=o.classes.selected,a=o.options;o.element.setAttribute("autocomplete","off"),o.noSuggestionsContainer=e('<div class="autocomplete-no-suggestion"></div>').html(this.options.noSuggestionNotice).get(0),o.suggestionsContainer=t.utils.createNode(a.containerClass),(n=e(o.suggestionsContainer)).appendTo(a.appendTo||"body"),"auto"!==a.width&&n.css("width",a.width),n.on("mouseover.autocomplete",i,function(){o.activate(e(this).data("index"))}),n.on("mouseout.autocomplete",function(){o.selectedIndex=-1,n.children("."+s).removeClass(s)}),n.on("click.autocomplete",i,function(){o.select(e(this).data("index"))}),n.on("click.autocomplete",function(){clearTimeout(o.blurTimeoutId)}),o.fixPositionCapture=function(){o.visible&&o.fixPosition()},e(window).on("resize.autocomplete",o.fixPositionCapture),o.el.on("keydown.autocomplete",function(e){o.onKeyPress(e)}),o.el.on("keyup.autocomplete",function(e){o.onKeyUp(e)}),o.el.on("blur.autocomplete",function(){o.onBlur()}),o.el.on("focus.autocomplete",function(){o.onFocus()}),o.el.on("change.autocomplete",function(e){o.onKeyUp(e)}),o.el.on("input.autocomplete",function(e){o.onKeyUp(e)})},onFocus:function(){var e=this;e.disabled||(e.fixPosition(),e.el.val().length>=e.options.minChars&&e.onValueChange())},onBlur:function(){var t=this,n=t.options,o=t.el.val(),i=t.getQuery(o);t.blurTimeoutId=setTimeout(function(){t.hide(),t.selection&&t.currentValue!==i&&(n.onInvalidateSelection||e.noop).call(t.element)},200)},abortAjax:function(){var e=this;e.currentRequest&&(e.currentRequest.abort(),e.currentRequest=null)},setOptions:function(t){var n=this,o=e.extend({},n.options,t);n.isLocal=Array.isArray(o.lookup),n.isLocal&&(o.lookup=n.verifySuggestionsFormat(o.lookup)),o.orientation=n.validateOrientation(o.orientation,"bottom"),e(n.suggestionsContainer).css({"max-height":o.maxHeight+"px",width:o.width+"px","z-index":o.zIndex}),this.options=o},clearCache:function(){this.cachedResponse={},this.badQueries=[]},clear:function(){this.clearCache(),this.currentValue="",this.suggestions=[]},disable:function(){var e=this;e.disabled=!0,clearTimeout(e.onChangeTimeout),e.abortAjax()},enable:function(){this.disabled=!1},fixPosition:function(){var t=this,n=e(t.suggestionsContainer),o=n.parent().get(0);if(o===document.body||t.options.forceFixPosition){var i=t.options.orientation,s=n.outerHeight(),a=t.el.outerHeight(),r=t.el.offset(),l={top:r.top,left:r.left};if("auto"===i){var u=e(window).height(),c=e(window).scrollTop(),d=-c+r.top-s,g=c+u-(r.top+a+s);i=Math.max(d,g)===d?"top":"bottom"}if(l.top+="top"===i?-s:a,o!==document.body){var p,h=n.css("opacity");t.visible||n.css("opacity",0).show(),p=n.offsetParent().offset(),l.top-=p.top,l.top+=o.scrollTop,l.left-=p.left,t.visible||n.css("opacity",h).hide()}"auto"===t.options.width&&(l.width=t.el.outerWidth()+"px"),n.css(l)}},isCursorAtEnd:function(){var e,t=this.el.val().length,n=this.element.selectionStart;return"number"==typeof n?n===t:!document.selection||((e=document.selection.createRange()).moveStart("character",-t),t===e.text.length)},onKeyPress:function(e){var t=this;if(t.disabled||t.visible||e.which!==l||!t.currentValue){if(!t.disabled&&t.visible){switch(e.which){case o:t.el.val(t.currentValue),t.hide();break;case r:if(t.hint&&t.options.onHint&&t.isCursorAtEnd()){t.selectHint();break}return;case i:if(t.hint&&t.options.onHint)return void t.selectHint();if(-1===t.selectedIndex)return void t.hide();if(t.select(t.selectedIndex),!1===t.options.tabDisabled)return;break;case s:if(-1===t.selectedIndex)return void t.hide();t.select(t.selectedIndex);break;case a:t.moveUp();break;case l:t.moveDown();break;default:return}e.stopImmediatePropagation(),e.preventDefault()}}else t.suggest()},onKeyUp:function(e){var t=this;if(!t.disabled){switch(e.which){case a:case l:return}clearTimeout(t.onChangeTimeout),t.currentValue!==t.el.val()&&(t.findBestHint(),t.options.deferRequestBy>0?t.onChangeTimeout=setTimeout(function(){t.onValueChange()},t.options.deferRequestBy):t.onValueChange())}},onValueChange:function(){if(!this.ignoreValueChange){var t=this,n=t.options,o=t.el.val(),i=t.getQuery(o);return t.selection&&t.currentValue!==i&&(t.selection=null,(n.onInvalidateSelection||e.noop).call(t.element)),clearTimeout(t.onChangeTimeout),t.currentValue=o,t.selectedIndex=-1,n.triggerSelectOnValidInput&&t.isExactMatch(i)?void t.select(0):void(i.length<n.minChars?t.hide():t.getSuggestions(i))}this.ignoreValueChange=!1},isExactMatch:function(e){var t=this.suggestions;return 1===t.length&&t[0].value.toLowerCase()===e.toLowerCase()},getQuery:function(t){var n,o=this.options.delimiter;return o?(n=t.split(o),e.trim(n[n.length-1])):t},getSuggestionsLocal:function(t){var n,o=this.options,i=t.toLowerCase(),s=o.lookupFilter,a=parseInt(o.lookupLimit,10);return n={suggestions:e.grep(o.lookup,function(e){return s(e,t,i)})},a&&n.suggestions.length>a&&(n.suggestions=n.suggestions.slice(0,a)),n},getSuggestions:function(t){var n,o,i,s,a=this,r=a.options,l=r.serviceUrl;if(r.params[r.paramName]=t,!1!==r.onSearchStart.call(a.element,r.params)){if(o=r.ignoreParams?null:r.params,e.isFunction(r.lookup))return void r.lookup(t,function(e){a.suggestions=e.suggestions,a.suggest(),r.onSearchComplete.call(a.element,t,e.suggestions)});a.isLocal?n=a.getSuggestionsLocal(t):(e.isFunction(l)&&(l=l.call(a.element,t)),i=l+"?"+e.param(o||{}),n=a.cachedResponse[i]),n&&Array.isArray(n.suggestions)?(a.suggestions=n.suggestions,a.suggest(),r.onSearchComplete.call(a.element,t,n.suggestions)):a.isBadQuery(t)?r.onSearchComplete.call(a.element,t,[]):(a.abortAjax(),s={url:l,data:o,type:r.type,dataType:r.dataType},e.extend(s,r.ajaxSettings),a.currentRequest=e.ajax(s).done(function(e){var n;a.currentRequest=null,n=r.transformResult(e,t),a.processResponse(n,t,i),r.onSearchComplete.call(a.element,t,n.suggestions)}).fail(function(e,n,o){r.onSearchError.call(a.element,t,e,n,o)}))}},isBadQuery:function(e){if(!this.options.preventBadQueries)return!1;for(var t=this.badQueries,n=t.length;n--;)if(0===e.indexOf(t[n]))return!0;return!1},hide:function(){var t=this,n=e(t.suggestionsContainer);e.isFunction(t.options.onHide)&&t.visible&&t.options.onHide.call(t.element,n),t.visible=!1,t.selectedIndex=-1,clearTimeout(t.onChangeTimeout),e(t.suggestionsContainer).hide(),t.signalHint(null)},suggest:function(){if(this.suggestions.length){var t,n=this,o=n.options,i=o.groupBy,s=o.formatResult,a=n.getQuery(n.currentValue),r=n.classes.suggestion,l=n.classes.selected,u=e(n.suggestionsContainer),c=e(n.noSuggestionsContainer),d=o.beforeRender,g="",p=function(e,n){var s=e.data[i];return t===s?"":(t=s,o.formatGroup(e,t))};return o.triggerSelectOnValidInput&&n.isExactMatch(a)?void n.select(0):(e.each(n.suggestions,function(e,t){i&&(g+=p(t,0)),g+='<div class="'+r+'" data-index="'+e+'">'+s(t,a,e)+"</div>"}),this.adjustContainerWidth(),c.detach(),u.html(g),e.isFunction(d)&&d.call(n.element,u,n.suggestions),n.fixPosition(),u.show(),o.autoSelectFirst&&(n.selectedIndex=0,u.scrollTop(0),u.children("."+r).first().addClass(l)),n.visible=!0,void n.findBestHint())}this.options.showNoSuggestionNotice?this.noSuggestions():this.hide()},noSuggestions:function(){var t=this,n=t.options.beforeRender,o=e(t.suggestionsContainer),i=e(t.noSuggestionsContainer);this.adjustContainerWidth(),i.detach(),o.empty(),o.append(i),e.isFunction(n)&&n.call(t.element,o,t.suggestions),t.fixPosition(),o.show(),t.visible=!0},adjustContainerWidth:function(){var t,n=this,o=n.options,i=e(n.suggestionsContainer);"auto"===o.width?(t=n.el.outerWidth(),i.css("width",t>0?t:300)):"flex"===o.width&&i.css("width","")},findBestHint:function(){var t=this,n=t.el.val().toLowerCase(),o=null;n&&(e.each(t.suggestions,function(e,t){var i=0===t.value.toLowerCase().indexOf(n);return i&&(o=t),!i}),t.signalHint(o))},signalHint:function(t){var n="",o=this;t&&(n=o.currentValue+t.value.substr(o.currentValue.length)),o.hintValue!==n&&(o.hintValue=n,o.hint=t,(this.options.onHint||e.noop)(n))},verifySuggestionsFormat:function(t){return t.length&&"string"==typeof t[0]?e.map(t,function(e){return{value:e,data:null}}):t},validateOrientation:function(t,n){return t=e.trim(t||"").toLowerCase(),-1===e.inArray(t,["auto","bottom","top"])&&(t=n),t},processResponse:function(e,t,n){var o=this,i=o.options;e.suggestions=o.verifySuggestionsFormat(e.suggestions),i.noCache||(o.cachedResponse[n]=e,i.preventBadQueries&&!e.suggestions.length&&o.badQueries.push(t)),t===o.getQuery(o.currentValue)&&(o.suggestions=e.suggestions,o.suggest())},activate:function(t){var n,o=this,i=o.classes.selected,s=e(o.suggestionsContainer),a=s.find("."+o.classes.suggestion);return s.find("."+i).removeClass(i),o.selectedIndex=t,-1!==o.selectedIndex&&a.length>o.selectedIndex?(n=a.get(o.selectedIndex),e(n).addClass(i),n):null},selectHint:function(){var t=this,n=e.inArray(t.hint,t.suggestions);t.select(n)},select:function(e){this.hide(),this.onSelect(e)},moveUp:function(){var t=this;if(-1!==t.selectedIndex)return 0===t.selectedIndex?(e(t.suggestionsContainer).children("."+t.classes.suggestion).first().removeClass(t.classes.selected),t.selectedIndex=-1,t.ignoreValueChange=!1,t.el.val(t.currentValue),void t.findBestHint()):void t.adjustScroll(t.selectedIndex-1)},moveDown:function(){var e=this;e.selectedIndex!==e.suggestions.length-1&&e.adjustScroll(e.selectedIndex+1)},adjustScroll:function(t){var n=this,o=n.activate(t);if(o){var i,s,a,r=e(o).outerHeight();i=o.offsetTop,a=(s=e(n.suggestionsContainer).scrollTop())+n.options.maxHeight-r,i<s?e(n.suggestionsContainer).scrollTop(i):i>a&&e(n.suggestionsContainer).scrollTop(i-n.options.maxHeight+r),n.options.preserveInput||(n.ignoreValueChange=!0,n.el.val(n.getValue(n.suggestions[t].value))),n.signalHint(null)}},onSelect:function(t){var n=this,o=n.options.onSelect,i=n.suggestions[t];n.currentValue=n.getValue(i.value),n.currentValue===n.el.val()||n.options.preserveInput||n.el.val(n.currentValue),n.signalHint(null),n.suggestions=[],n.selection=i,e.isFunction(o)&&o.call(n.element,i)},getValue:function(e){var t,n,o=this.options.delimiter;return o?1===(n=(t=this.currentValue).split(o)).length?e:t.substr(0,t.length-n[n.length-1].length)+e:e},dispose:function(){var t=this;t.el.off(".autocomplete").removeData("autocomplete"),e(window).off("resize.autocomplete",t.fixPositionCapture),e(t.suggestionsContainer).remove()}},e.fn.devbridgeAutocomplete=function(n,o){var i="autocomplete";return arguments.length?this.each(function(){var s=e(this),a=s.data(i);"string"==typeof n?a&&"function"==typeof a[n]&&a[n](o):(a&&a.dispose&&a.dispose(),a=new t(this,n),s.data(i,a))}):this.first().data(i)},e.fn.autocomplete||(e.fn.autocomplete=e.fn.devbridgeAutocomplete)});