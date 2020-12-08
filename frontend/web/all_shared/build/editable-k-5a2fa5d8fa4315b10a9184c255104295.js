!function(e){"use strict";var t,i;t={NAMESPACE:".editable",isEmpty:function(t,i){return null==t||0===t.length||i&&""===e.trim(t)},addCss:function(e,t){e.removeClass(t).addClass(t)},handler:function(e,i,a){var o=i+t.NAMESPACE;e.off(o).on(o,a)},raise:function(t,i,a){var o=e.Event(i);return void 0!==a?t.trigger(o,a):t.trigger(o),!o.isDefaultPrevented()}},(i=function(t,i){this.$container=e(t),this.init(i),this.destroy(),this.create()}).prototype={constructor:i,init:function(t){var i=this,a=i.$container;i.$input=a.find(".kv-editable-input"),i.$form=a.find(".kv-editable-form"),i.$value=a.find(".kv-editable-value"),i.$close=a.find(".kv-editable-close"),i.$popover=a.find(".kv-editable-popover"),i.$inline=a.find(".kv-editable-inline"),i.$btnSubmit=a.find("button.kv-editable-submit"),i.$btnReset=a.find("button.kv-editable-reset"),i.$loading=a.find(".kv-editable-loading"),i.$target=a.find(t.target),e.each(t,function(e,t){i[e]=t}),i.$targetEl=".kv-editable-button"===i.target?i.$target:i.$value,i.initActions()},initActions:function(){var i,a=this,o=a.$form,n=o.parent(),r=a.$container,s=a.$inline,l=a.$loading,c=a.$input,u="",d="",f=o.data("yiiActiveForm"),h=c.closest(".field-"+c.attr("id")),p=h.find(".help-block"),v=c.closest(".kv-editable-parent"),m=a.displayValueConfig,b=o.find('input[name="hasEditable"]'),g=t.isEmpty(h.attr("class"))||t.isEmpty(p.attr("class")),y=v.find(".kv-help-block");i=function(i){g?(y.length||(y=e(document.createElement("div")).attr({class:"help-block kv-help-block"}).appendTo(v)),y.html(i).show()):(t.addCss(p,"kv-help-block"),p.html(i).show()),t.addCss(v,"has-error"),l.hide(),n.removeClass("kv-editable-processing")},a.actions={formReset:function(){setTimeout(function(){o.data("kvEditableSubmit",!1),g?(v.find(".help-block").remove(),v.removeClass("has-error")):(h.removeClass("has-error"),p.html(" ")),a.refreshPopover()},a.resetDelay)},formSubmit:function(e){e.preventDefault()},formChange:function(){t.raise(r,"editableChange",[c.val()])&&a.refreshPopover()},formKeyup:function(e){13===e.which&&a.submitOnEnter&&(a.submitFlag=!0,a.actions.submitClick())},formBlur:function(e){var t=e.delegateTarget;setTimeout(function(){t.contains(document.activeElement)||a.submitFlag||!a.closeOnBlur||a.toggle(!1)},0)},inlineKeyup:function(e){27===e.which&&a.actions.closeClick()},closeClick:function(){a.toggle(!1)},targetClick:function(){var e;a.submitFlag=!1,a.asPopover?a.toggle(!0):(e=!s.is(":visible"),a.toggle(e))},resetClick:function(){t.raise(r,"editableReset")&&(b.val(0),setTimeout(function(){o[0].reset()},a.resetDelay))},submitClick:function(){var E,k=a.asPopover?n:s,$=!1;t.addCss(k,"kv-editable-processing"),l.show(),b.val(1),o.find(".help-block").each(function(){e(this).html("")}),o.find(".has-error").removeClass("has-error"),o.find("input, select").each(function(){var i=e(this),a=i.val(),o=a,n="file"===i.attr("type");$||($=n),i.attr("disabled")||(n?t.raise(i,"blur"):(e.isArray(a)?o.push("-"):o+="-",i.val(o),t.raise(i,"blur"),i.val(a),t.raise(i,"blur")))}),E={type:o.attr("method"),url:o.attr("action"),dataType:"json",beforeSend:function(e){t.raise(r,"editableBeforeSubmit",[e])||e.abort()},error:function(e,o,n){t.raise(r,"editableAjaxError",[e,o,n])&&a.showAjaxErrors&&i(n)},success:function(n,s,b){u="",d=t.isEmpty(n.output)?a.htmlEncode(c.val()):n.output,a.refreshPopover(),t.isEmpty(n.message)?(t.isEmpty(y.attr("class"))||(h.removeClass("has-error"),y.html("").hide(),p.html("")),o.find(".help-block").each(function(){var i=e(this).text();u+=i?i.trim():"",t.isEmpty(u)||l.hide()}),t.isEmpty(u)?t.raise(r,"editableSuccess",[c.val(),o,n,s,b])&&(l.hide(),t.isEmpty(d)?d=a.valueIfNull:void 0!==m[d]&&(d=m[d]),g?(v.find(".help-block").remove(),v.removeClass("has-error"),p.html(""),a.toggle(!1),a.$value.html(d)):(h.removeClass("has-error"),p.html(""),a.toggle(!1),a.$value.html(d),f&&(o.yiiActiveForm("destroy"),o.yiiActiveForm(f.attributes,f.settings)))):t.raise(r,"editableError",[c.val(),o,n]),k.removeClass("kv-editable-processing")):t.raise(r,"editableError",[c.val(),o,n])&&i(n.message)}},$&&window.FormData?(o.attr("enctype","multipart/form-data"),E.data=new FormData(o[0]),E.contentType=!1,E.processData=!1,E.cache=!1):E.data=o.serialize(),setTimeout(function(){t.raise(r,"editableSubmit",[c.val(),o])&&e.ajax(e.extend(!0,E,a.ajaxSettings))},a.validationDelay)}}},htmlEncode:function(t){var i=this;return i.encodeOutput?"object"==typeof t?(e.each(t,function(e,a){t[e]=i.htmlEncode(a)}),t):t.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&apos;"):t},toggle:function(e){var i=this,a=i.$value,o=i.$inline,n=i.animationDelay,r=function(){i.$btnSubmit.focus(),i.selectAllOnEdit&&i.$input.select()};if(e)return i.asPopover?void r():void a.fadeOut(n,function(){o.fadeIn(n,function(){r()}),".kv-editable-button"===i.target&&t.addCss(i.$target,"kv-inline-open")});i.asPopover?i.$popover.popoverX("hide"):o.fadeOut(n,function(){a.fadeIn(n),i.$target.removeClass("kv-inline-open")})},refreshPopover:function(){this.asPopover&&this.$popover.popoverX("refreshPosition")},destroy:function(){this.$form.off(t.NAMESPACE),this.$form.find("input, select").off(t.NAMESPACE),this.$close.off(t.NAMESPACE),this.$inline.off(t.NAMESPACE),this.$popover.off(t.NAMESPACE),this.$btnSubmit.off(t.NAMESPACE),this.$btnReset.off(t.NAMESPACE),this.$targetEl.off(t.NAMESPACE)},create:function(){var i=this.actions,a=this.$form,o=this.$inline;t.handler(a,"reset",e.proxy(i.formReset,this)),t.handler(a,"submit",e.proxy(i.formSubmit,this)),t.handler(a.find("input, select"),"change",e.proxy(i.formChange,this)),t.handler(a,"keyup",e.proxy(i.formKeyup,this)),this.asPopover?t.handler(this.$popover,"focusout",e.proxy(i.formBlur,this)):(t.handler(o,"keyup",e.proxy(i.inlineKeyup,this)),t.handler(o,"focusout",e.proxy(i.formBlur,this))),t.handler(this.$btnReset,"click",e.proxy(i.resetClick,this)),t.handler(this.$btnSubmit,"click",e.proxy(i.submitClick,this)),t.handler(this.$close,"click",e.proxy(i.closeClick,this)),t.handler(this.$targetEl,"click",e.proxy(i.targetClick,this))}},e.fn.editable=function(t){var a=Array.apply(null,arguments);return a.shift(),this.each(function(){var o=e(this),n=o.data("editable"),r="object"==typeof t&&t;n||(n=new i(this,e.extend({},e.fn.editable.defaults,r,e(this).data())),o.data("editable",n)),"string"==typeof t&&n[t].apply(n,a)})},e.fn.editable.defaults={valueIfNull:"<em>(not set)</em>",placement:"right",displayValueConfig:{},ajaxSettings:{},showAjaxErrors:!0,submitOnEnter:!0,selectAllOnEdit:!0,asPopover:!0,encodeOutput:!0,closeOnBlur:!0,validationDelay:500,resetDelay:200,animationDelay:300},e.fn.editable.Constructor=i}(window.jQuery);