(()=>{var tl={6997:()=>{+function(P){"use strict";var y=".dropdown-backdrop",o='[data-toggle="dropdown"]',g=function(p){P(p).on("click.bs.dropdown",this.toggle)};g.VERSION="3.4.1";function r(p){var a=p.attr("data-target");a||(a=p.attr("href"),a=a&&/#[A-Za-z]/.test(a)&&a.replace(/.*(?=#[^\s]*$)/,""));var u=a!=="#"?P(document).find(a):null;return u&&u.length?u:p.parent()}function t(p){p&&p.which===3||(P(y).remove(),P(o).each(function(){var a=P(this),u=r(a),h={relatedTarget:this};!u.hasClass("open")||p&&p.type=="click"&&/input|textarea/i.test(p.target.tagName)&&P.contains(u[0],p.target)||(u.trigger(p=P.Event("hide.bs.dropdown",h)),!p.isDefaultPrevented()&&(a.attr("aria-expanded","false"),u.removeClass("open").trigger(P.Event("hidden.bs.dropdown",h))))}))}g.prototype.toggle=function(p){var a=P(this);if(!a.is(".disabled, :disabled")){var u=r(a),h=u.hasClass("open");if(t(),!h){"ontouchstart"in document.documentElement&&!u.closest(".navbar-nav").length&&P(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(P(this)).on("click",t);var i={relatedTarget:this};if(u.trigger(p=P.Event("show.bs.dropdown",i)),p.isDefaultPrevented())return;a.trigger("focus").attr("aria-expanded","true"),u.toggleClass("open").trigger(P.Event("shown.bs.dropdown",i))}return!1}},g.prototype.keydown=function(p){if(!(!/(38|40|27|32)/.test(p.which)||/input|textarea/i.test(p.target.tagName))){var a=P(this);if(p.preventDefault(),p.stopPropagation(),!a.is(".disabled, :disabled")){var u=r(a),h=u.hasClass("open");if(!h&&p.which!=27||h&&p.which==27)return p.which==27&&u.find(o).trigger("focus"),a.trigger("click");var i=" li:not(.disabled):visible a",m=u.find(".dropdown-menu"+i);if(!!m.length){var d=m.index(p.target);p.which==38&&d>0&&d--,p.which==40&&d<m.length-1&&d++,~d||(d=0),m.eq(d).trigger("focus")}}}};function l(p){return this.each(function(){var a=P(this),u=a.data("bs.dropdown");u||a.data("bs.dropdown",u=new g(this)),typeof p=="string"&&u[p].call(a)})}var c=P.fn.dropdown;P.fn.dropdown=l,P.fn.dropdown.Constructor=g,P.fn.dropdown.noConflict=function(){return P.fn.dropdown=c,this},P(document).on("click.bs.dropdown.data-api",t).on("click.bs.dropdown.data-api",".dropdown form",function(p){p.stopPropagation()}).on("click.bs.dropdown.data-api",o,g.prototype.toggle).on("keydown.bs.dropdown.data-api",o,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery)},4582:()=>{+function(P){"use strict";var y=function(r,t){this.init("popover",r,t)};if(!P.fn.tooltip)throw new Error("Popover requires tooltip.js");y.VERSION="3.4.1",y.DEFAULTS=P.extend({},P.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),y.prototype=P.extend({},P.fn.tooltip.Constructor.prototype),y.prototype.constructor=y,y.prototype.getDefaults=function(){return y.DEFAULTS},y.prototype.setContent=function(){var r=this.tip(),t=this.getTitle(),l=this.getContent();if(this.options.html){var c=typeof l;this.options.sanitize&&(t=this.sanitizeHtml(t),c==="string"&&(l=this.sanitizeHtml(l))),r.find(".popover-title").html(t),r.find(".popover-content").children().detach().end()[c==="string"?"html":"append"](l)}else r.find(".popover-title").text(t),r.find(".popover-content").children().detach().end().text(l);r.removeClass("fade top bottom left right in"),r.find(".popover-title").html()||r.find(".popover-title").hide()},y.prototype.hasContent=function(){return this.getTitle()||this.getContent()},y.prototype.getContent=function(){var r=this.$element,t=this.options;return r.attr("data-content")||(typeof t.content=="function"?t.content.call(r[0]):t.content)},y.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};function o(r){return this.each(function(){var t=P(this),l=t.data("bs.popover"),c=typeof r=="object"&&r;!l&&/destroy|hide/.test(r)||(l||t.data("bs.popover",l=new y(this,c)),typeof r=="string"&&l[r]())})}var g=P.fn.popover;P.fn.popover=o,P.fn.popover.Constructor=y,P.fn.popover.noConflict=function(){return P.fn.popover=g,this}}(jQuery)},9121:()=>{+function(P){"use strict";function y(r,t){this.$body=P(document.body),this.$scrollElement=P(r).is(document.body)?P(window):P(r),this.options=P.extend({},y.DEFAULTS,t),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",P.proxy(this.process,this)),this.refresh(),this.process()}y.VERSION="3.4.1",y.DEFAULTS={offset:10},y.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},y.prototype.refresh=function(){var r=this,t="offset",l=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),P.isWindow(this.$scrollElement[0])||(t="position",l=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var c=P(this),p=c.data("target")||c.attr("href"),a=/^#./.test(p)&&P(p);return a&&a.length&&a.is(":visible")&&[[a[t]().top+l,p]]||null}).sort(function(c,p){return c[0]-p[0]}).each(function(){r.offsets.push(this[0]),r.targets.push(this[1])})},y.prototype.process=function(){var r=this.$scrollElement.scrollTop()+this.options.offset,t=this.getScrollHeight(),l=this.options.offset+t-this.$scrollElement.height(),c=this.offsets,p=this.targets,a=this.activeTarget,u;if(this.scrollHeight!=t&&this.refresh(),r>=l)return a!=(u=p[p.length-1])&&this.activate(u);if(a&&r<c[0])return this.activeTarget=null,this.clear();for(u=c.length;u--;)a!=p[u]&&r>=c[u]&&(c[u+1]===void 0||r<c[u+1])&&this.activate(p[u])},y.prototype.activate=function(r){this.activeTarget=r,this.clear();var t=this.selector+'[data-target="'+r+'"],'+this.selector+'[href="'+r+'"]',l=P(t).parents("li").addClass("active");l.parent(".dropdown-menu").length&&(l=l.closest("li.dropdown").addClass("active")),l.trigger("activate.bs.scrollspy")},y.prototype.clear=function(){P(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};function o(r){return this.each(function(){var t=P(this),l=t.data("bs.scrollspy"),c=typeof r=="object"&&r;l||t.data("bs.scrollspy",l=new y(this,c)),typeof r=="string"&&l[r]()})}var g=P.fn.scrollspy;P.fn.scrollspy=o,P.fn.scrollspy.Constructor=y,P.fn.scrollspy.noConflict=function(){return P.fn.scrollspy=g,this},P(window).on("load.bs.scrollspy.data-api",function(){P('[data-spy="scroll"]').each(function(){var r=P(this);o.call(r,r.data())})})}(jQuery)},6690:()=>{+function(P){"use strict";var y=function(t){this.element=P(t)};y.VERSION="3.4.1",y.TRANSITION_DURATION=150,y.prototype.show=function(){var t=this.element,l=t.closest("ul:not(.dropdown-menu)"),c=t.data("target");if(c||(c=t.attr("href"),c=c&&c.replace(/.*(?=#[^\s]*$)/,"")),!t.parent("li").hasClass("active")){var p=l.find(".active:last a"),a=P.Event("hide.bs.tab",{relatedTarget:t[0]}),u=P.Event("show.bs.tab",{relatedTarget:p[0]});if(p.trigger(a),t.trigger(u),!(u.isDefaultPrevented()||a.isDefaultPrevented())){var h=P(document).find(c);this.activate(t.closest("li"),l),this.activate(h,h.parent(),function(){p.trigger({type:"hidden.bs.tab",relatedTarget:t[0]}),t.trigger({type:"shown.bs.tab",relatedTarget:p[0]})})}}},y.prototype.activate=function(t,l,c){var p=l.find("> .active"),a=c&&P.support.transition&&(p.length&&p.hasClass("fade")||!!l.find("> .fade").length);function u(){p.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),t.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),a?(t[0].offsetWidth,t.addClass("in")):t.removeClass("fade"),t.parent(".dropdown-menu").length&&t.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),c&&c()}p.length&&a?p.one("bsTransitionEnd",u).emulateTransitionEnd(y.TRANSITION_DURATION):u(),p.removeClass("in")};function o(t){return this.each(function(){var l=P(this),c=l.data("bs.tab");c||l.data("bs.tab",c=new y(this)),typeof t=="string"&&c[t]()})}var g=P.fn.tab;P.fn.tab=o,P.fn.tab.Constructor=y,P.fn.tab.noConflict=function(){return P.fn.tab=g,this};var r=function(t){t.preventDefault(),o.call(P(this),"show")};P(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',r).on("click.bs.tab.data-api",'[data-toggle="pill"]',r)}(jQuery)},9984:()=>{+function(P){"use strict";var y=["sanitize","whiteList","sanitizeFn"],o=["background","cite","href","itemtype","longdesc","poster","src","xlink:href"],g=/^aria-[\w-]*$/i,r={"*":["class","dir","id","lang","role",g],a:["target","href","title","rel"],area:[],b:[],br:[],col:[],code:[],div:[],em:[],hr:[],h1:[],h2:[],h3:[],h4:[],h5:[],h6:[],i:[],img:["src","alt","title","width","height"],li:[],ol:[],p:[],pre:[],s:[],small:[],span:[],sub:[],sup:[],strong:[],u:[],ul:[]},t=/^(?:(?:https?|mailto|ftp|tel|file):|[^&:/?#]*(?:[/?#]|$))/gi,l=/^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[a-z0-9+/]+=*$/i;function c(i,m){var d=i.nodeName.toLowerCase();if(P.inArray(d,m)!==-1)return P.inArray(d,o)!==-1?Boolean(i.nodeValue.match(t)||i.nodeValue.match(l)):!0;for(var f=P(m).filter(function(A,C){return C instanceof RegExp}),v=0,_=f.length;v<_;v++)if(d.match(f[v]))return!0;return!1}function p(i,m,d){if(i.length===0)return i;if(d&&typeof d=="function")return d(i);if(!document.implementation||!document.implementation.createHTMLDocument)return i;var f=document.implementation.createHTMLDocument("sanitization");f.body.innerHTML=i;for(var v=P.map(m,function(I,w){return w}),_=P(f.body).find("*"),A=0,C=_.length;A<C;A++){var E=_[A],S=E.nodeName.toLowerCase();if(P.inArray(S,v)===-1){E.parentNode.removeChild(E);continue}for(var b=P.map(E.attributes,function(I){return I}),x=[].concat(m["*"]||[],m[S]||[]),D=0,N=b.length;D<N;D++)c(b[D],x)||E.removeAttribute(b[D].nodeName)}return f.body.innerHTML}var a=function(i,m){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",i,m)};a.VERSION="3.4.1",a.TRANSITION_DURATION=150,a.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0},sanitize:!0,sanitizeFn:null,whiteList:r},a.prototype.init=function(i,m,d){if(this.enabled=!0,this.type=i,this.$element=P(m),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&P(document).find(P.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var f=this.options.trigger.split(" "),v=f.length;v--;){var _=f[v];if(_=="click")this.$element.on("click."+this.type,this.options.selector,P.proxy(this.toggle,this));else if(_!="manual"){var A=_=="hover"?"mouseenter":"focusin",C=_=="hover"?"mouseleave":"focusout";this.$element.on(A+"."+this.type,this.options.selector,P.proxy(this.enter,this)),this.$element.on(C+"."+this.type,this.options.selector,P.proxy(this.leave,this))}}this.options.selector?this._options=P.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},a.prototype.getDefaults=function(){return a.DEFAULTS},a.prototype.getOptions=function(i){var m=this.$element.data();for(var d in m)m.hasOwnProperty(d)&&P.inArray(d,y)!==-1&&delete m[d];return i=P.extend({},this.getDefaults(),m,i),i.delay&&typeof i.delay=="number"&&(i.delay={show:i.delay,hide:i.delay}),i.sanitize&&(i.template=p(i.template,i.whiteList,i.sanitizeFn)),i},a.prototype.getDelegateOptions=function(){var i={},m=this.getDefaults();return this._options&&P.each(this._options,function(d,f){m[d]!=f&&(i[d]=f)}),i},a.prototype.enter=function(i){var m=i instanceof this.constructor?i:P(i.currentTarget).data("bs."+this.type);if(m||(m=new this.constructor(i.currentTarget,this.getDelegateOptions()),P(i.currentTarget).data("bs."+this.type,m)),i instanceof P.Event&&(m.inState[i.type=="focusin"?"focus":"hover"]=!0),m.tip().hasClass("in")||m.hoverState=="in"){m.hoverState="in";return}if(clearTimeout(m.timeout),m.hoverState="in",!m.options.delay||!m.options.delay.show)return m.show();m.timeout=setTimeout(function(){m.hoverState=="in"&&m.show()},m.options.delay.show)},a.prototype.isInStateTrue=function(){for(var i in this.inState)if(this.inState[i])return!0;return!1},a.prototype.leave=function(i){var m=i instanceof this.constructor?i:P(i.currentTarget).data("bs."+this.type);if(m||(m=new this.constructor(i.currentTarget,this.getDelegateOptions()),P(i.currentTarget).data("bs."+this.type,m)),i instanceof P.Event&&(m.inState[i.type=="focusout"?"focus":"hover"]=!1),!m.isInStateTrue()){if(clearTimeout(m.timeout),m.hoverState="out",!m.options.delay||!m.options.delay.hide)return m.hide();m.timeout=setTimeout(function(){m.hoverState=="out"&&m.hide()},m.options.delay.hide)}},a.prototype.show=function(){var i=P.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(i);var m=P.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(i.isDefaultPrevented()||!m)return;var d=this,f=this.tip(),v=this.getUID(this.type);this.setContent(),f.attr("id",v),this.$element.attr("aria-describedby",v),this.options.animation&&f.addClass("fade");var _=typeof this.options.placement=="function"?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,A=/\s?auto?\s?/i,C=A.test(_);C&&(_=_.replace(A,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(_).data("bs."+this.type,this),this.options.container?f.appendTo(P(document).find(this.options.container)):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var E=this.getPosition(),S=f[0].offsetWidth,b=f[0].offsetHeight;if(C){var x=_,D=this.getPosition(this.$viewport);_=_=="bottom"&&E.bottom+b>D.bottom?"top":_=="top"&&E.top-b<D.top?"bottom":_=="right"&&E.right+S>D.width?"left":_=="left"&&E.left-S<D.left?"right":_,f.removeClass(x).addClass(_)}var N=this.getCalculatedOffset(_,E,S,b);this.applyPlacement(N,_);var I=function(){var w=d.hoverState;d.$element.trigger("shown.bs."+d.type),d.hoverState=null,w=="out"&&d.leave(d)};P.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",I).emulateTransitionEnd(a.TRANSITION_DURATION):I()}},a.prototype.applyPlacement=function(i,m){var d=this.tip(),f=d[0].offsetWidth,v=d[0].offsetHeight,_=parseInt(d.css("margin-top"),10),A=parseInt(d.css("margin-left"),10);isNaN(_)&&(_=0),isNaN(A)&&(A=0),i.top+=_,i.left+=A,P.offset.setOffset(d[0],P.extend({using:function(N){d.css({top:Math.round(N.top),left:Math.round(N.left)})}},i),0),d.addClass("in");var C=d[0].offsetWidth,E=d[0].offsetHeight;m=="top"&&E!=v&&(i.top=i.top+v-E);var S=this.getViewportAdjustedDelta(m,i,C,E);S.left?i.left+=S.left:i.top+=S.top;var b=/top|bottom/.test(m),x=b?S.left*2-f+C:S.top*2-v+E,D=b?"offsetWidth":"offsetHeight";d.offset(i),this.replaceArrow(x,d[0][D],b)},a.prototype.replaceArrow=function(i,m,d){this.arrow().css(d?"left":"top",50*(1-i/m)+"%").css(d?"top":"left","")},a.prototype.setContent=function(){var i=this.tip(),m=this.getTitle();this.options.html?(this.options.sanitize&&(m=p(m,this.options.whiteList,this.options.sanitizeFn)),i.find(".tooltip-inner").html(m)):i.find(".tooltip-inner").text(m),i.removeClass("fade in top bottom left right")},a.prototype.hide=function(i){var m=this,d=P(this.$tip),f=P.Event("hide.bs."+this.type);function v(){m.hoverState!="in"&&d.detach(),m.$element&&m.$element.removeAttr("aria-describedby").trigger("hidden.bs."+m.type),i&&i()}if(this.$element.trigger(f),!f.isDefaultPrevented())return d.removeClass("in"),P.support.transition&&d.hasClass("fade")?d.one("bsTransitionEnd",v).emulateTransitionEnd(a.TRANSITION_DURATION):v(),this.hoverState=null,this},a.prototype.fixTitle=function(){var i=this.$element;(i.attr("title")||typeof i.attr("data-original-title")!="string")&&i.attr("data-original-title",i.attr("title")||"").attr("title","")},a.prototype.hasContent=function(){return this.getTitle()},a.prototype.getPosition=function(i){i=i||this.$element;var m=i[0],d=m.tagName=="BODY",f=m.getBoundingClientRect();f.width==null&&(f=P.extend({},f,{width:f.right-f.left,height:f.bottom-f.top}));var v=window.SVGElement&&m instanceof window.SVGElement,_=d?{top:0,left:0}:v?null:i.offset(),A={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:i.scrollTop()},C=d?{width:P(window).width(),height:P(window).height()}:null;return P.extend({},f,A,C,_)},a.prototype.getCalculatedOffset=function(i,m,d,f){return i=="bottom"?{top:m.top+m.height,left:m.left+m.width/2-d/2}:i=="top"?{top:m.top-f,left:m.left+m.width/2-d/2}:i=="left"?{top:m.top+m.height/2-f/2,left:m.left-d}:{top:m.top+m.height/2-f/2,left:m.left+m.width}},a.prototype.getViewportAdjustedDelta=function(i,m,d,f){var v={top:0,left:0};if(!this.$viewport)return v;var _=this.options.viewport&&this.options.viewport.padding||0,A=this.getPosition(this.$viewport);if(/right|left/.test(i)){var C=m.top-_-A.scroll,E=m.top+_-A.scroll+f;C<A.top?v.top=A.top-C:E>A.top+A.height&&(v.top=A.top+A.height-E)}else{var S=m.left-_,b=m.left+_+d;S<A.left?v.left=A.left-S:b>A.right&&(v.left=A.left+A.width-b)}return v},a.prototype.getTitle=function(){var i,m=this.$element,d=this.options;return i=m.attr("data-original-title")||(typeof d.title=="function"?d.title.call(m[0]):d.title),i},a.prototype.getUID=function(i){do i+=~~(Math.random()*1e6);while(document.getElementById(i));return i},a.prototype.tip=function(){if(!this.$tip&&(this.$tip=P(this.options.template),this.$tip.length!=1))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},a.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},a.prototype.enable=function(){this.enabled=!0},a.prototype.disable=function(){this.enabled=!1},a.prototype.toggleEnabled=function(){this.enabled=!this.enabled},a.prototype.toggle=function(i){var m=this;i&&(m=P(i.currentTarget).data("bs."+this.type),m||(m=new this.constructor(i.currentTarget,this.getDelegateOptions()),P(i.currentTarget).data("bs."+this.type,m))),i?(m.inState.click=!m.inState.click,m.isInStateTrue()?m.enter(m):m.leave(m)):m.tip().hasClass("in")?m.leave(m):m.enter(m)},a.prototype.destroy=function(){var i=this;clearTimeout(this.timeout),this.hide(function(){i.$element.off("."+i.type).removeData("bs."+i.type),i.$tip&&i.$tip.detach(),i.$tip=null,i.$arrow=null,i.$viewport=null,i.$element=null})},a.prototype.sanitizeHtml=function(i){return p(i,this.options.whiteList,this.options.sanitizeFn)};function u(i){return this.each(function(){var m=P(this),d=m.data("bs.tooltip"),f=typeof i=="object"&&i;!d&&/destroy|hide/.test(i)||(d||m.data("bs.tooltip",d=new a(this,f)),typeof i=="string"&&d[i]())})}var h=P.fn.tooltip;P.fn.tooltip=u,P.fn.tooltip.Constructor=a,P.fn.tooltip.noConflict=function(){return P.fn.tooltip=h,this}}(jQuery)},1155:P=>{var y=function(){this.Diff_Timeout=1,this.Diff_EditCost=4,this.Match_Threshold=.5,this.Match_Distance=1e3,this.Patch_DeleteThreshold=.5,this.Patch_Margin=4,this.Match_MaxBits=32},o=-1,g=1,r=0;y.Diff=function(t,l){return[t,l]},y.prototype.diff_main=function(t,l,c,p){typeof p=="undefined"&&(this.Diff_Timeout<=0?p=Number.MAX_VALUE:p=new Date().getTime()+this.Diff_Timeout*1e3);var a=p;if(t==null||l==null)throw new Error("Null input. (diff_main)");if(t==l)return t?[new y.Diff(r,t)]:[];typeof c=="undefined"&&(c=!0);var u=c,h=this.diff_commonPrefix(t,l),i=t.substring(0,h);t=t.substring(h),l=l.substring(h),h=this.diff_commonSuffix(t,l);var m=t.substring(t.length-h);t=t.substring(0,t.length-h),l=l.substring(0,l.length-h);var d=this.diff_compute_(t,l,u,a);return i&&d.unshift(new y.Diff(r,i)),m&&d.push(new y.Diff(r,m)),this.diff_cleanupMerge(d),d},y.prototype.diff_compute_=function(t,l,c,p){var a;if(!t)return[new y.Diff(g,l)];if(!l)return[new y.Diff(o,t)];var u=t.length>l.length?t:l,h=t.length>l.length?l:t,i=u.indexOf(h);if(i!=-1)return a=[new y.Diff(g,u.substring(0,i)),new y.Diff(r,h),new y.Diff(g,u.substring(i+h.length))],t.length>l.length&&(a[0][0]=a[2][0]=o),a;if(h.length==1)return[new y.Diff(o,t),new y.Diff(g,l)];var m=this.diff_halfMatch_(t,l);if(m){var d=m[0],f=m[1],v=m[2],_=m[3],A=m[4],C=this.diff_main(d,v,c,p),E=this.diff_main(f,_,c,p);return C.concat([new y.Diff(r,A)],E)}return c&&t.length>100&&l.length>100?this.diff_lineMode_(t,l,p):this.diff_bisect_(t,l,p)},y.prototype.diff_lineMode_=function(t,l,c){var p=this.diff_linesToChars_(t,l);t=p.chars1,l=p.chars2;var a=p.lineArray,u=this.diff_main(t,l,!1,c);this.diff_charsToLines_(u,a),this.diff_cleanupSemantic(u),u.push(new y.Diff(r,""));for(var h=0,i=0,m=0,d="",f="";h<u.length;){switch(u[h][0]){case g:m++,f+=u[h][1];break;case o:i++,d+=u[h][1];break;case r:if(i>=1&&m>=1){u.splice(h-i-m,i+m),h=h-i-m;for(var v=this.diff_main(d,f,!1,c),_=v.length-1;_>=0;_--)u.splice(h,0,v[_]);h=h+v.length}m=0,i=0,d="",f="";break}h++}return u.pop(),u},y.prototype.diff_bisect_=function(t,l,c){for(var p=t.length,a=l.length,u=Math.ceil((p+a)/2),h=u,i=2*u,m=new Array(i),d=new Array(i),f=0;f<i;f++)m[f]=-1,d[f]=-1;m[h+1]=0,d[h+1]=0;for(var v=p-a,_=v%2!=0,A=0,C=0,E=0,S=0,b=0;b<u&&!(new Date().getTime()>c);b++){for(var x=-b+A;x<=b-C;x+=2){var D=h+x,N;x==-b||x!=b&&m[D-1]<m[D+1]?N=m[D+1]:N=m[D-1]+1;for(var I=N-x;N<p&&I<a&&t.charAt(N)==l.charAt(I);)N++,I++;if(m[D]=N,N>p)C+=2;else if(I>a)A+=2;else if(_){var w=h+v-x;if(w>=0&&w<i&&d[w]!=-1){var O=p-d[w];if(N>=O)return this.diff_bisectSplit_(t,l,N,I,c)}}}for(var B=-b+E;B<=b-S;B+=2){var w=h+B,O;B==-b||B!=b&&d[w-1]<d[w+1]?O=d[w+1]:O=d[w-1]+1;for(var U=O-B;O<p&&U<a&&t.charAt(p-O-1)==l.charAt(a-U-1);)O++,U++;if(d[w]=O,O>p)S+=2;else if(U>a)E+=2;else if(!_){var D=h+v-B;if(D>=0&&D<i&&m[D]!=-1){var N=m[D],I=h+N-D;if(O=p-O,N>=O)return this.diff_bisectSplit_(t,l,N,I,c)}}}}return[new y.Diff(o,t),new y.Diff(g,l)]},y.prototype.diff_bisectSplit_=function(t,l,c,p,a){var u=t.substring(0,c),h=l.substring(0,p),i=t.substring(c),m=l.substring(p),d=this.diff_main(u,h,!1,a),f=this.diff_main(i,m,!1,a);return d.concat(f)},y.prototype.diff_linesToChars_=function(t,l){var c=[],p={};c[0]="";function a(m){for(var d="",f=0,v=-1,_=c.length;v<m.length-1;){v=m.indexOf(`
`,f),v==-1&&(v=m.length-1);var A=m.substring(f,v+1);(p.hasOwnProperty?p.hasOwnProperty(A):p[A]!==void 0)?d+=String.fromCharCode(p[A]):(_==u&&(A=m.substring(f),v=m.length),d+=String.fromCharCode(_),p[A]=_,c[_++]=A),f=v+1}return d}var u=4e4,h=a(t);u=65535;var i=a(l);return{chars1:h,chars2:i,lineArray:c}},y.prototype.diff_charsToLines_=function(t,l){for(var c=0;c<t.length;c++){for(var p=t[c][1],a=[],u=0;u<p.length;u++)a[u]=l[p.charCodeAt(u)];t[c][1]=a.join("")}},y.prototype.diff_commonPrefix=function(t,l){if(!t||!l||t.charAt(0)!=l.charAt(0))return 0;for(var c=0,p=Math.min(t.length,l.length),a=p,u=0;c<a;)t.substring(u,a)==l.substring(u,a)?(c=a,u=c):p=a,a=Math.floor((p-c)/2+c);return a},y.prototype.diff_commonSuffix=function(t,l){if(!t||!l||t.charAt(t.length-1)!=l.charAt(l.length-1))return 0;for(var c=0,p=Math.min(t.length,l.length),a=p,u=0;c<a;)t.substring(t.length-a,t.length-u)==l.substring(l.length-a,l.length-u)?(c=a,u=c):p=a,a=Math.floor((p-c)/2+c);return a},y.prototype.diff_commonOverlap_=function(t,l){var c=t.length,p=l.length;if(c==0||p==0)return 0;c>p?t=t.substring(c-p):c<p&&(l=l.substring(0,c));var a=Math.min(c,p);if(t==l)return a;for(var u=0,h=1;;){var i=t.substring(a-h),m=l.indexOf(i);if(m==-1)return u;h+=m,(m==0||t.substring(a-h)==l.substring(0,h))&&(u=h,h++)}},y.prototype.diff_halfMatch_=function(t,l){if(this.Diff_Timeout<=0)return null;var c=t.length>l.length?t:l,p=t.length>l.length?l:t;if(c.length<4||p.length*2<c.length)return null;var a=this;function u(C,E,S){for(var b=C.substring(S,S+Math.floor(C.length/4)),x=-1,D="",N,I,w,O;(x=E.indexOf(b,x+1))!=-1;){var B=a.diff_commonPrefix(C.substring(S),E.substring(x)),U=a.diff_commonSuffix(C.substring(0,S),E.substring(0,x));D.length<U+B&&(D=E.substring(x-U,x)+E.substring(x,x+B),N=C.substring(0,S-U),I=C.substring(S+B),w=E.substring(0,x-U),O=E.substring(x+B))}return D.length*2>=C.length?[N,I,w,O,D]:null}var h=u(c,p,Math.ceil(c.length/4)),i=u(c,p,Math.ceil(c.length/2)),m;if(!h&&!i)return null;i?h?m=h[4].length>i[4].length?h:i:m=i:m=h;var d,f,v,_;t.length>l.length?(d=m[0],f=m[1],v=m[2],_=m[3]):(v=m[0],_=m[1],d=m[2],f=m[3]);var A=m[4];return[d,f,v,_,A]},y.prototype.diff_cleanupSemantic=function(t){for(var l=!1,c=[],p=0,a=null,u=0,h=0,i=0,m=0,d=0;u<t.length;)t[u][0]==r?(c[p++]=u,h=m,i=d,m=0,d=0,a=t[u][1]):(t[u][0]==g?m+=t[u][1].length:d+=t[u][1].length,a&&a.length<=Math.max(h,i)&&a.length<=Math.max(m,d)&&(t.splice(c[p-1],0,new y.Diff(o,a)),t[c[p-1]+1][0]=g,p--,p--,u=p>0?c[p-1]:-1,h=0,i=0,m=0,d=0,a=null,l=!0)),u++;for(l&&this.diff_cleanupMerge(t),this.diff_cleanupSemanticLossless(t),u=1;u<t.length;){if(t[u-1][0]==o&&t[u][0]==g){var f=t[u-1][1],v=t[u][1],_=this.diff_commonOverlap_(f,v),A=this.diff_commonOverlap_(v,f);_>=A?(_>=f.length/2||_>=v.length/2)&&(t.splice(u,0,new y.Diff(r,v.substring(0,_))),t[u-1][1]=f.substring(0,f.length-_),t[u+1][1]=v.substring(_),u++):(A>=f.length/2||A>=v.length/2)&&(t.splice(u,0,new y.Diff(r,f.substring(0,A))),t[u-1][0]=g,t[u-1][1]=v.substring(0,v.length-A),t[u+1][0]=o,t[u+1][1]=f.substring(A),u++),u++}u++}},y.prototype.diff_cleanupSemanticLossless=function(t){function l(A,C){if(!A||!C)return 6;var E=A.charAt(A.length-1),S=C.charAt(0),b=E.match(y.nonAlphaNumericRegex_),x=S.match(y.nonAlphaNumericRegex_),D=b&&E.match(y.whitespaceRegex_),N=x&&S.match(y.whitespaceRegex_),I=D&&E.match(y.linebreakRegex_),w=N&&S.match(y.linebreakRegex_),O=I&&A.match(y.blanklineEndRegex_),B=w&&C.match(y.blanklineStartRegex_);return O||B?5:I||w?4:b&&!D&&N?3:D||N?2:b||x?1:0}for(var c=1;c<t.length-1;){if(t[c-1][0]==r&&t[c+1][0]==r){var p=t[c-1][1],a=t[c][1],u=t[c+1][1],h=this.diff_commonSuffix(p,a);if(h){var i=a.substring(a.length-h);p=p.substring(0,p.length-h),a=i+a.substring(0,a.length-h),u=i+u}for(var m=p,d=a,f=u,v=l(p,a)+l(a,u);a.charAt(0)===u.charAt(0);){p+=a.charAt(0),a=a.substring(1)+u.charAt(0),u=u.substring(1);var _=l(p,a)+l(a,u);_>=v&&(v=_,m=p,d=a,f=u)}t[c-1][1]!=m&&(m?t[c-1][1]=m:(t.splice(c-1,1),c--),t[c][1]=d,f?t[c+1][1]=f:(t.splice(c+1,1),c--))}c++}},y.nonAlphaNumericRegex_=/[^a-zA-Z0-9]/,y.whitespaceRegex_=/\s/,y.linebreakRegex_=/[\r\n]/,y.blanklineEndRegex_=/\n\r?\n$/,y.blanklineStartRegex_=/^\r?\n\r?\n/,y.prototype.diff_cleanupEfficiency=function(t){for(var l=!1,c=[],p=0,a=null,u=0,h=!1,i=!1,m=!1,d=!1;u<t.length;)t[u][0]==r?(t[u][1].length<this.Diff_EditCost&&(m||d)?(c[p++]=u,h=m,i=d,a=t[u][1]):(p=0,a=null),m=d=!1):(t[u][0]==o?d=!0:m=!0,a&&(h&&i&&m&&d||a.length<this.Diff_EditCost/2&&h+i+m+d==3)&&(t.splice(c[p-1],0,new y.Diff(o,a)),t[c[p-1]+1][0]=g,p--,a=null,h&&i?(m=d=!0,p=0):(p--,u=p>0?c[p-1]:-1,m=d=!1),l=!0)),u++;l&&this.diff_cleanupMerge(t)},y.prototype.diff_cleanupMerge=function(t){t.push(new y.Diff(r,""));for(var l=0,c=0,p=0,a="",u="",h;l<t.length;)switch(t[l][0]){case g:p++,u+=t[l][1],l++;break;case o:c++,a+=t[l][1],l++;break;case r:c+p>1?(c!==0&&p!==0&&(h=this.diff_commonPrefix(u,a),h!==0&&(l-c-p>0&&t[l-c-p-1][0]==r?t[l-c-p-1][1]+=u.substring(0,h):(t.splice(0,0,new y.Diff(r,u.substring(0,h))),l++),u=u.substring(h),a=a.substring(h)),h=this.diff_commonSuffix(u,a),h!==0&&(t[l][1]=u.substring(u.length-h)+t[l][1],u=u.substring(0,u.length-h),a=a.substring(0,a.length-h))),l-=c+p,t.splice(l,c+p),a.length&&(t.splice(l,0,new y.Diff(o,a)),l++),u.length&&(t.splice(l,0,new y.Diff(g,u)),l++),l++):l!==0&&t[l-1][0]==r?(t[l-1][1]+=t[l][1],t.splice(l,1)):l++,p=0,c=0,a="",u="";break}t[t.length-1][1]===""&&t.pop();var i=!1;for(l=1;l<t.length-1;)t[l-1][0]==r&&t[l+1][0]==r&&(t[l][1].substring(t[l][1].length-t[l-1][1].length)==t[l-1][1]?(t[l][1]=t[l-1][1]+t[l][1].substring(0,t[l][1].length-t[l-1][1].length),t[l+1][1]=t[l-1][1]+t[l+1][1],t.splice(l-1,1),i=!0):t[l][1].substring(0,t[l+1][1].length)==t[l+1][1]&&(t[l-1][1]+=t[l+1][1],t[l][1]=t[l][1].substring(t[l+1][1].length)+t[l+1][1],t.splice(l+1,1),i=!0)),l++;i&&this.diff_cleanupMerge(t)},y.prototype.diff_xIndex=function(t,l){var c=0,p=0,a=0,u=0,h;for(h=0;h<t.length&&(t[h][0]!==g&&(c+=t[h][1].length),t[h][0]!==o&&(p+=t[h][1].length),!(c>l));h++)a=c,u=p;return t.length!=h&&t[h][0]===o?u:u+(l-a)},y.prototype.diff_prettyHtml=function(t){for(var l=[],c=/&/g,p=/</g,a=/>/g,u=/\n/g,h=0;h<t.length;h++){var i=t[h][0],m=t[h][1],d=m.replace(c,"&amp;").replace(p,"&lt;").replace(a,"&gt;").replace(u,"&para;<br>");switch(i){case g:l[h]='<ins style="background:#e6ffe6;">'+d+"</ins>";break;case o:l[h]='<del style="background:#ffe6e6;">'+d+"</del>";break;case r:l[h]="<span>"+d+"</span>";break}}return l.join("")},y.prototype.diff_text1=function(t){for(var l=[],c=0;c<t.length;c++)t[c][0]!==g&&(l[c]=t[c][1]);return l.join("")},y.prototype.diff_text2=function(t){for(var l=[],c=0;c<t.length;c++)t[c][0]!==o&&(l[c]=t[c][1]);return l.join("")},y.prototype.diff_levenshtein=function(t){for(var l=0,c=0,p=0,a=0;a<t.length;a++){var u=t[a][0],h=t[a][1];switch(u){case g:c+=h.length;break;case o:p+=h.length;break;case r:l+=Math.max(c,p),c=0,p=0;break}}return l+=Math.max(c,p),l},y.prototype.diff_toDelta=function(t){for(var l=[],c=0;c<t.length;c++)switch(t[c][0]){case g:l[c]="+"+encodeURI(t[c][1]);break;case o:l[c]="-"+t[c][1].length;break;case r:l[c]="="+t[c][1].length;break}return l.join("	").replace(/%20/g," ")},y.prototype.diff_fromDelta=function(t,l){for(var c=[],p=0,a=0,u=l.split(/\t/g),h=0;h<u.length;h++){var i=u[h].substring(1);switch(u[h].charAt(0)){case"+":try{c[p++]=new y.Diff(g,decodeURI(i))}catch(f){throw new Error("Illegal escape in diff_fromDelta: "+i)}break;case"-":case"=":var m=parseInt(i,10);if(isNaN(m)||m<0)throw new Error("Invalid number in diff_fromDelta: "+i);var d=t.substring(a,a+=m);u[h].charAt(0)=="="?c[p++]=new y.Diff(r,d):c[p++]=new y.Diff(o,d);break;default:if(u[h])throw new Error("Invalid diff operation in diff_fromDelta: "+u[h])}}if(a!=t.length)throw new Error("Delta length ("+a+") does not equal source text length ("+t.length+").");return c},y.prototype.match_main=function(t,l,c){if(t==null||l==null||c==null)throw new Error("Null input. (match_main)");return c=Math.max(0,Math.min(c,t.length)),t==l?0:t.length?t.substring(c,c+l.length)==l?c:this.match_bitap_(t,l,c):-1},y.prototype.match_bitap_=function(t,l,c){if(l.length>this.Match_MaxBits)throw new Error("Pattern too long for this browser.");var p=this.match_alphabet_(l),a=this;function u(N,I){var w=N/l.length,O=Math.abs(c-I);return a.Match_Distance?w+O/a.Match_Distance:O?1:w}var h=this.Match_Threshold,i=t.indexOf(l,c);i!=-1&&(h=Math.min(u(0,i),h),i=t.lastIndexOf(l,c+l.length),i!=-1&&(h=Math.min(u(0,i),h)));var m=1<<l.length-1;i=-1;for(var d,f,v=l.length+t.length,_,A=0;A<l.length;A++){for(d=0,f=v;d<f;)u(A,c+f)<=h?d=f:v=f,f=Math.floor((v-d)/2+d);v=f;var C=Math.max(1,c-f+1),E=Math.min(c+f,t.length)+l.length,S=Array(E+2);S[E+1]=(1<<A)-1;for(var b=E;b>=C;b--){var x=p[t.charAt(b-1)];if(A===0?S[b]=(S[b+1]<<1|1)&x:S[b]=(S[b+1]<<1|1)&x|((_[b+1]|_[b])<<1|1)|_[b+1],S[b]&m){var D=u(A,b-1);if(D<=h)if(h=D,i=b-1,i>c)C=Math.max(1,2*c-i);else break}}if(u(A+1,c)>h)break;_=S}return i},y.prototype.match_alphabet_=function(t){for(var l={},c=0;c<t.length;c++)l[t.charAt(c)]=0;for(var c=0;c<t.length;c++)l[t.charAt(c)]|=1<<t.length-c-1;return l},y.prototype.patch_addContext_=function(t,l){if(l.length!=0){if(t.start2===null)throw Error("patch not initialized");for(var c=l.substring(t.start2,t.start2+t.length1),p=0;l.indexOf(c)!=l.lastIndexOf(c)&&c.length<this.Match_MaxBits-this.Patch_Margin-this.Patch_Margin;)p+=this.Patch_Margin,c=l.substring(t.start2-p,t.start2+t.length1+p);p+=this.Patch_Margin;var a=l.substring(t.start2-p,t.start2);a&&t.diffs.unshift(new y.Diff(r,a));var u=l.substring(t.start2+t.length1,t.start2+t.length1+p);u&&t.diffs.push(new y.Diff(r,u)),t.start1-=a.length,t.start2-=a.length,t.length1+=a.length+u.length,t.length2+=a.length+u.length}},y.prototype.patch_make=function(t,l,c){var p,a;if(typeof t=="string"&&typeof l=="string"&&typeof c=="undefined")p=t,a=this.diff_main(p,l,!0),a.length>2&&(this.diff_cleanupSemantic(a),this.diff_cleanupEfficiency(a));else if(t&&typeof t=="object"&&typeof l=="undefined"&&typeof c=="undefined")a=t,p=this.diff_text1(a);else if(typeof t=="string"&&l&&typeof l=="object"&&typeof c=="undefined")p=t,a=l;else if(typeof t=="string"&&typeof l=="string"&&c&&typeof c=="object")p=t,a=c;else throw new Error("Unknown call format to patch_make.");if(a.length===0)return[];for(var u=[],h=new y.patch_obj,i=0,m=0,d=0,f=p,v=p,_=0;_<a.length;_++){var A=a[_][0],C=a[_][1];switch(!i&&A!==r&&(h.start1=m,h.start2=d),A){case g:h.diffs[i++]=a[_],h.length2+=C.length,v=v.substring(0,d)+C+v.substring(d);break;case o:h.length1+=C.length,h.diffs[i++]=a[_],v=v.substring(0,d)+v.substring(d+C.length);break;case r:C.length<=2*this.Patch_Margin&&i&&a.length!=_+1?(h.diffs[i++]=a[_],h.length1+=C.length,h.length2+=C.length):C.length>=2*this.Patch_Margin&&i&&(this.patch_addContext_(h,f),u.push(h),h=new y.patch_obj,i=0,f=v,m=d);break}A!==g&&(m+=C.length),A!==o&&(d+=C.length)}return i&&(this.patch_addContext_(h,f),u.push(h)),u},y.prototype.patch_deepCopy=function(t){for(var l=[],c=0;c<t.length;c++){var p=t[c],a=new y.patch_obj;a.diffs=[];for(var u=0;u<p.diffs.length;u++)a.diffs[u]=new y.Diff(p.diffs[u][0],p.diffs[u][1]);a.start1=p.start1,a.start2=p.start2,a.length1=p.length1,a.length2=p.length2,l[c]=a}return l},y.prototype.patch_apply=function(t,l){if(t.length==0)return[l,[]];t=this.patch_deepCopy(t);var c=this.patch_addPadding(t);l=c+l+c,this.patch_splitMax(t);for(var p=0,a=[],u=0;u<t.length;u++){var h=t[u].start2+p,i=this.diff_text1(t[u].diffs),m,d=-1;if(i.length>this.Match_MaxBits?(m=this.match_main(l,i.substring(0,this.Match_MaxBits),h),m!=-1&&(d=this.match_main(l,i.substring(i.length-this.Match_MaxBits),h+i.length-this.Match_MaxBits),(d==-1||m>=d)&&(m=-1))):m=this.match_main(l,i,h),m==-1)a[u]=!1,p-=t[u].length2-t[u].length1;else{a[u]=!0,p=m-h;var f;if(d==-1?f=l.substring(m,m+i.length):f=l.substring(m,d+this.Match_MaxBits),i==f)l=l.substring(0,m)+this.diff_text2(t[u].diffs)+l.substring(m+i.length);else{var v=this.diff_main(i,f,!1);if(i.length>this.Match_MaxBits&&this.diff_levenshtein(v)/i.length>this.Patch_DeleteThreshold)a[u]=!1;else{this.diff_cleanupSemanticLossless(v);for(var _=0,A,C=0;C<t[u].diffs.length;C++){var E=t[u].diffs[C];E[0]!==r&&(A=this.diff_xIndex(v,_)),E[0]===g?l=l.substring(0,m+A)+E[1]+l.substring(m+A):E[0]===o&&(l=l.substring(0,m+A)+l.substring(m+this.diff_xIndex(v,_+E[1].length))),E[0]!==o&&(_+=E[1].length)}}}}}return l=l.substring(c.length,l.length-c.length),[l,a]},y.prototype.patch_addPadding=function(t){for(var l=this.Patch_Margin,c="",p=1;p<=l;p++)c+=String.fromCharCode(p);for(var p=0;p<t.length;p++)t[p].start1+=l,t[p].start2+=l;var a=t[0],u=a.diffs;if(u.length==0||u[0][0]!=r)u.unshift(new y.Diff(r,c)),a.start1-=l,a.start2-=l,a.length1+=l,a.length2+=l;else if(l>u[0][1].length){var h=l-u[0][1].length;u[0][1]=c.substring(u[0][1].length)+u[0][1],a.start1-=h,a.start2-=h,a.length1+=h,a.length2+=h}if(a=t[t.length-1],u=a.diffs,u.length==0||u[u.length-1][0]!=r)u.push(new y.Diff(r,c)),a.length1+=l,a.length2+=l;else if(l>u[u.length-1][1].length){var h=l-u[u.length-1][1].length;u[u.length-1][1]+=c.substring(0,h),a.length1+=h,a.length2+=h}return c},y.prototype.patch_splitMax=function(t){for(var l=this.Match_MaxBits,c=0;c<t.length;c++)if(!(t[c].length1<=l)){var p=t[c];t.splice(c--,1);for(var a=p.start1,u=p.start2,h="";p.diffs.length!==0;){var i=new y.patch_obj,m=!0;for(i.start1=a-h.length,i.start2=u-h.length,h!==""&&(i.length1=i.length2=h.length,i.diffs.push(new y.Diff(r,h)));p.diffs.length!==0&&i.length1<l-this.Patch_Margin;){var d=p.diffs[0][0],f=p.diffs[0][1];d===g?(i.length2+=f.length,u+=f.length,i.diffs.push(p.diffs.shift()),m=!1):d===o&&i.diffs.length==1&&i.diffs[0][0]==r&&f.length>2*l?(i.length1+=f.length,a+=f.length,m=!1,i.diffs.push(new y.Diff(d,f)),p.diffs.shift()):(f=f.substring(0,l-i.length1-this.Patch_Margin),i.length1+=f.length,a+=f.length,d===r?(i.length2+=f.length,u+=f.length):m=!1,i.diffs.push(new y.Diff(d,f)),f==p.diffs[0][1]?p.diffs.shift():p.diffs[0][1]=p.diffs[0][1].substring(f.length))}h=this.diff_text2(i.diffs),h=h.substring(h.length-this.Patch_Margin);var v=this.diff_text1(p.diffs).substring(0,this.Patch_Margin);v!==""&&(i.length1+=v.length,i.length2+=v.length,i.diffs.length!==0&&i.diffs[i.diffs.length-1][0]===r?i.diffs[i.diffs.length-1][1]+=v:i.diffs.push(new y.Diff(r,v))),m||t.splice(++c,0,i)}}},y.prototype.patch_toText=function(t){for(var l=[],c=0;c<t.length;c++)l[c]=t[c];return l.join("")},y.prototype.patch_fromText=function(t){var l=[];if(!t)return l;for(var c=t.split(`
`),p=0,a=/^@@ -(\d+),?(\d*) \+(\d+),?(\d*) @@$/;p<c.length;){var u=c[p].match(a);if(!u)throw new Error("Invalid patch string: "+c[p]);var h=new y.patch_obj;for(l.push(h),h.start1=parseInt(u[1],10),u[2]===""?(h.start1--,h.length1=1):u[2]=="0"?h.length1=0:(h.start1--,h.length1=parseInt(u[2],10)),h.start2=parseInt(u[3],10),u[4]===""?(h.start2--,h.length2=1):u[4]=="0"?h.length2=0:(h.start2--,h.length2=parseInt(u[4],10)),p++;p<c.length;){var i=c[p].charAt(0);try{var m=decodeURI(c[p].substring(1))}catch(d){throw new Error("Illegal escape in patch_fromText: "+m)}if(i=="-")h.diffs.push(new y.Diff(o,m));else if(i=="+")h.diffs.push(new y.Diff(g,m));else if(i==" ")h.diffs.push(new y.Diff(r,m));else{if(i=="@")break;if(i!=="")throw new Error('Invalid patch mode "'+i+'" in: '+m)}p++}}return l},y.patch_obj=function(){this.diffs=[],this.start1=null,this.start2=null,this.length1=0,this.length2=0},y.patch_obj.prototype.toString=function(){var t,l;this.length1===0?t=this.start1+",0":this.length1==1?t=this.start1+1:t=this.start1+1+","+this.length1,this.length2===0?l=this.start2+",0":this.length2==1?l=this.start2+1:l=this.start2+1+","+this.length2;for(var c=["@@ -"+t+" +"+l+` @@
`],p,a=0;a<this.diffs.length;a++){switch(this.diffs[a][0]){case g:p="+";break;case o:p="-";break;case r:p=" ";break}c[a+1]=p+encodeURI(this.diffs[a][1])+`
`}return c.join("").replace(/%20/g," ")},P.exports=y,P.exports.diff_match_patch=y,P.exports.DIFF_DELETE=o,P.exports.DIFF_INSERT=g,P.exports.DIFF_EQUAL=r},6566:function(P){/**!

 @license
 handlebars v4.7.7

Copyright (C) 2011-2019 by Yehuda Katz

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/(function(y,o){P.exports=o()})(this,function(){return function(y){function o(r){if(g[r])return g[r].exports;var t=g[r]={exports:{},id:r,loaded:!1};return y[r].call(t.exports,t,t.exports,o),t.loaded=!0,t.exports}var g={};return o.m=y,o.c=g,o.p="",o(0)}([function(y,o,g){"use strict";function r(){var E=A();return E.compile=function(S,b){return h.compile(S,b,E)},E.precompile=function(S,b){return h.precompile(S,b,E)},E.AST=a.default,E.Compiler=h.Compiler,E.JavaScriptCompiler=m.default,E.Parser=u.parser,E.parse=u.parse,E.parseWithoutProcessing=u.parseWithoutProcessing,E}var t=g(1).default;o.__esModule=!0;var l=g(2),c=t(l),p=g(45),a=t(p),u=g(46),h=g(51),i=g(52),m=t(i),d=g(49),f=t(d),v=g(44),_=t(v),A=c.default.create,C=r();C.create=r,_.default(C),C.Visitor=f.default,C.default=C,o.default=C,y.exports=o.default},function(y,o){"use strict";o.default=function(g){return g&&g.__esModule?g:{default:g}},o.__esModule=!0},function(y,o,g){"use strict";function r(){var E=new p.HandlebarsEnvironment;return d.extend(E,p),E.SafeString=u.default,E.Exception=i.default,E.Utils=d,E.escapeExpression=d.escapeExpression,E.VM=v,E.template=function(S){return v.template(S,E)},E}var t=g(3).default,l=g(1).default;o.__esModule=!0;var c=g(4),p=t(c),a=g(37),u=l(a),h=g(6),i=l(h),m=g(5),d=t(m),f=g(38),v=t(f),_=g(44),A=l(_),C=r();C.create=r,A.default(C),C.default=C,o.default=C,y.exports=o.default},function(y,o){"use strict";o.default=function(g){if(g&&g.__esModule)return g;var r={};if(g!=null)for(var t in g)Object.prototype.hasOwnProperty.call(g,t)&&(r[t]=g[t]);return r.default=g,r},o.__esModule=!0},function(y,o,g){"use strict";function r(E,S,b){this.helpers=E||{},this.partials=S||{},this.decorators=b||{},a.registerDefaultHelpers(this),u.registerDefaultDecorators(this)}var t=g(1).default;o.__esModule=!0,o.HandlebarsEnvironment=r;var l=g(5),c=g(6),p=t(c),a=g(10),u=g(30),h=g(32),i=t(h),m=g(33),d="4.7.7";o.VERSION=d;var f=8;o.COMPILER_REVISION=f;var v=7;o.LAST_COMPATIBLE_COMPILER_REVISION=v;var _={1:"<= 1.0.rc.2",2:"== 1.0.0-rc.3",3:"== 1.0.0-rc.4",4:"== 1.x.x",5:"== 2.0.0-alpha.x",6:">= 2.0.0-beta.1",7:">= 4.0.0 <4.3.0",8:">= 4.3.0"};o.REVISION_CHANGES=_;var A="[object Object]";r.prototype={constructor:r,logger:i.default,log:i.default.log,registerHelper:function(E,S){if(l.toString.call(E)===A){if(S)throw new p.default("Arg not supported with multiple helpers");l.extend(this.helpers,E)}else this.helpers[E]=S},unregisterHelper:function(E){delete this.helpers[E]},registerPartial:function(E,S){if(l.toString.call(E)===A)l.extend(this.partials,E);else{if(typeof S=="undefined")throw new p.default('Attempting to register a partial called "'+E+'" as undefined');this.partials[E]=S}},unregisterPartial:function(E){delete this.partials[E]},registerDecorator:function(E,S){if(l.toString.call(E)===A){if(S)throw new p.default("Arg not supported with multiple decorators");l.extend(this.decorators,E)}else this.decorators[E]=S},unregisterDecorator:function(E){delete this.decorators[E]},resetLoggedPropertyAccesses:function(){m.resetLoggedProperties()}};var C=i.default.log;o.log=C,o.createFrame=l.createFrame,o.logger=i.default},function(y,o){"use strict";function g(_){return h[_]}function r(_){for(var A=1;A<arguments.length;A++)for(var C in arguments[A])Object.prototype.hasOwnProperty.call(arguments[A],C)&&(_[C]=arguments[A][C]);return _}function t(_,A){for(var C=0,E=_.length;C<E;C++)if(_[C]===A)return C;return-1}function l(_){if(typeof _!="string"){if(_&&_.toHTML)return _.toHTML();if(_==null)return"";if(!_)return _+"";_=""+_}return m.test(_)?_.replace(i,g):_}function c(_){return!_&&_!==0||!(!v(_)||_.length!==0)}function p(_){var A=r({},_);return A._parent=_,A}function a(_,A){return _.path=A,_}function u(_,A){return(_?_+".":"")+A}o.__esModule=!0,o.extend=r,o.indexOf=t,o.escapeExpression=l,o.isEmpty=c,o.createFrame=p,o.blockParams=a,o.appendContextPath=u;var h={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;","=":"&#x3D;"},i=/[&<>"'`=]/g,m=/[&<>"'`=]/,d=Object.prototype.toString;o.toString=d;var f=function(_){return typeof _=="function"};f(/x/)&&(o.isFunction=f=function(_){return typeof _=="function"&&d.call(_)==="[object Function]"}),o.isFunction=f;var v=Array.isArray||function(_){return!(!_||typeof _!="object")&&d.call(_)==="[object Array]"};o.isArray=v},function(y,o,g){"use strict";function r(c,p){var a=p&&p.loc,u=void 0,h=void 0,i=void 0,m=void 0;a&&(u=a.start.line,h=a.end.line,i=a.start.column,m=a.end.column,c+=" - "+u+":"+i);for(var d=Error.prototype.constructor.call(this,c),f=0;f<l.length;f++)this[l[f]]=d[l[f]];Error.captureStackTrace&&Error.captureStackTrace(this,r);try{a&&(this.lineNumber=u,this.endLineNumber=h,t?(Object.defineProperty(this,"column",{value:i,enumerable:!0}),Object.defineProperty(this,"endColumn",{value:m,enumerable:!0})):(this.column=i,this.endColumn=m))}catch(v){}}var t=g(7).default;o.__esModule=!0;var l=["description","fileName","lineNumber","endLineNumber","message","name","number","stack"];r.prototype=new Error,o.default=r,y.exports=o.default},function(y,o,g){y.exports={default:g(8),__esModule:!0}},function(y,o,g){var r=g(9);y.exports=function(t,l,c){return r.setDesc(t,l,c)}},function(y,o){var g=Object;y.exports={create:g.create,getProto:g.getPrototypeOf,isEnum:{}.propertyIsEnumerable,getDesc:g.getOwnPropertyDescriptor,setDesc:g.defineProperty,setDescs:g.defineProperties,getKeys:g.keys,getNames:g.getOwnPropertyNames,getSymbols:g.getOwnPropertySymbols,each:[].forEach}},function(y,o,g){"use strict";function r(S){p.default(S),u.default(S),i.default(S),d.default(S),v.default(S),A.default(S),E.default(S)}function t(S,b,x){S.helpers[b]&&(S.hooks[b]=S.helpers[b],x||delete S.helpers[b])}var l=g(1).default;o.__esModule=!0,o.registerDefaultHelpers=r,o.moveHelperToHooks=t;var c=g(11),p=l(c),a=g(12),u=l(a),h=g(25),i=l(h),m=g(26),d=l(m),f=g(27),v=l(f),_=g(28),A=l(_),C=g(29),E=l(C)},function(y,o,g){"use strict";o.__esModule=!0;var r=g(5);o.default=function(t){t.registerHelper("blockHelperMissing",function(l,c){var p=c.inverse,a=c.fn;if(l===!0)return a(this);if(l===!1||l==null)return p(this);if(r.isArray(l))return l.length>0?(c.ids&&(c.ids=[c.name]),t.helpers.each(l,c)):p(this);if(c.data&&c.ids){var u=r.createFrame(c.data);u.contextPath=r.appendContextPath(c.data.contextPath,c.name),c={data:u}}return a(l,c)})},y.exports=o.default},function(y,o,g){(function(r){"use strict";var t=g(13).default,l=g(1).default;o.__esModule=!0;var c=g(5),p=g(6),a=l(p);o.default=function(u){u.registerHelper("each",function(h,i){function m(D,N,I){A&&(A.key=D,A.index=N,A.first=N===0,A.last=!!I,C&&(A.contextPath=C+D)),_+=d(h[D],{data:A,blockParams:c.blockParams([h[D],D],[C+D,null])})}if(!i)throw new a.default("Must pass iterator to #each");var d=i.fn,f=i.inverse,v=0,_="",A=void 0,C=void 0;if(i.data&&i.ids&&(C=c.appendContextPath(i.data.contextPath,i.ids[0])+"."),c.isFunction(h)&&(h=h.call(this)),i.data&&(A=c.createFrame(i.data)),h&&typeof h=="object")if(c.isArray(h))for(var E=h.length;v<E;v++)v in h&&m(v,v,v===h.length-1);else if(r.Symbol&&h[r.Symbol.iterator]){for(var S=[],b=h[r.Symbol.iterator](),x=b.next();!x.done;x=b.next())S.push(x.value);h=S;for(var E=h.length;v<E;v++)m(v,v,v===h.length-1)}else(function(){var D=void 0;t(h).forEach(function(N){D!==void 0&&m(D,v-1),D=N,v++}),D!==void 0&&m(D,v-1,!0)})();return v===0&&(_=f(this)),_})},y.exports=o.default}).call(o,function(){return this}())},function(y,o,g){y.exports={default:g(14),__esModule:!0}},function(y,o,g){g(15),y.exports=g(21).Object.keys},function(y,o,g){var r=g(16);g(18)("keys",function(t){return function(l){return t(r(l))}})},function(y,o,g){var r=g(17);y.exports=function(t){return Object(r(t))}},function(y,o){y.exports=function(g){if(g==null)throw TypeError("Can't call method on  "+g);return g}},function(y,o,g){var r=g(19),t=g(21),l=g(24);y.exports=function(c,p){var a=(t.Object||{})[c]||Object[c],u={};u[c]=p(a),r(r.S+r.F*l(function(){a(1)}),"Object",u)}},function(y,o,g){var r=g(20),t=g(21),l=g(22),c="prototype",p=function(a,u,h){var i,m,d,f=a&p.F,v=a&p.G,_=a&p.S,A=a&p.P,C=a&p.B,E=a&p.W,S=v?t:t[u]||(t[u]={}),b=v?r:_?r[u]:(r[u]||{})[c];v&&(h=u);for(i in h)m=!f&&b&&i in b,m&&i in S||(d=m?b[i]:h[i],S[i]=v&&typeof b[i]!="function"?h[i]:C&&m?l(d,r):E&&b[i]==d?function(x){var D=function(N){return this instanceof x?new x(N):x(N)};return D[c]=x[c],D}(d):A&&typeof d=="function"?l(Function.call,d):d,A&&((S[c]||(S[c]={}))[i]=d))};p.F=1,p.G=2,p.S=4,p.P=8,p.B=16,p.W=32,y.exports=p},function(y,o){var g=y.exports=typeof window!="undefined"&&window.Math==Math?window:typeof self!="undefined"&&self.Math==Math?self:Function("return this")();typeof __g=="number"&&(__g=g)},function(y,o){var g=y.exports={version:"1.2.6"};typeof __e=="number"&&(__e=g)},function(y,o,g){var r=g(23);y.exports=function(t,l,c){if(r(t),l===void 0)return t;switch(c){case 1:return function(p){return t.call(l,p)};case 2:return function(p,a){return t.call(l,p,a)};case 3:return function(p,a,u){return t.call(l,p,a,u)}}return function(){return t.apply(l,arguments)}}},function(y,o){y.exports=function(g){if(typeof g!="function")throw TypeError(g+" is not a function!");return g}},function(y,o){y.exports=function(g){try{return!!g()}catch(r){return!0}}},function(y,o,g){"use strict";var r=g(1).default;o.__esModule=!0;var t=g(6),l=r(t);o.default=function(c){c.registerHelper("helperMissing",function(){if(arguments.length!==1)throw new l.default('Missing helper: "'+arguments[arguments.length-1].name+'"')})},y.exports=o.default},function(y,o,g){"use strict";var r=g(1).default;o.__esModule=!0;var t=g(5),l=g(6),c=r(l);o.default=function(p){p.registerHelper("if",function(a,u){if(arguments.length!=2)throw new c.default("#if requires exactly one argument");return t.isFunction(a)&&(a=a.call(this)),!u.hash.includeZero&&!a||t.isEmpty(a)?u.inverse(this):u.fn(this)}),p.registerHelper("unless",function(a,u){if(arguments.length!=2)throw new c.default("#unless requires exactly one argument");return p.helpers.if.call(this,a,{fn:u.inverse,inverse:u.fn,hash:u.hash})})},y.exports=o.default},function(y,o){"use strict";o.__esModule=!0,o.default=function(g){g.registerHelper("log",function(){for(var r=[void 0],t=arguments[arguments.length-1],l=0;l<arguments.length-1;l++)r.push(arguments[l]);var c=1;t.hash.level!=null?c=t.hash.level:t.data&&t.data.level!=null&&(c=t.data.level),r[0]=c,g.log.apply(g,r)})},y.exports=o.default},function(y,o){"use strict";o.__esModule=!0,o.default=function(g){g.registerHelper("lookup",function(r,t,l){return r&&l.lookupProperty(r,t)})},y.exports=o.default},function(y,o,g){"use strict";var r=g(1).default;o.__esModule=!0;var t=g(5),l=g(6),c=r(l);o.default=function(p){p.registerHelper("with",function(a,u){if(arguments.length!=2)throw new c.default("#with requires exactly one argument");t.isFunction(a)&&(a=a.call(this));var h=u.fn;if(t.isEmpty(a))return u.inverse(this);var i=u.data;return u.data&&u.ids&&(i=t.createFrame(u.data),i.contextPath=t.appendContextPath(u.data.contextPath,u.ids[0])),h(a,{data:i,blockParams:t.blockParams([a],[i&&i.contextPath])})})},y.exports=o.default},function(y,o,g){"use strict";function r(p){c.default(p)}var t=g(1).default;o.__esModule=!0,o.registerDefaultDecorators=r;var l=g(31),c=t(l)},function(y,o,g){"use strict";o.__esModule=!0;var r=g(5);o.default=function(t){t.registerDecorator("inline",function(l,c,p,a){var u=l;return c.partials||(c.partials={},u=function(h,i){var m=p.partials;p.partials=r.extend({},m,c.partials);var d=l(h,i);return p.partials=m,d}),c.partials[a.args[0]]=a.fn,u})},y.exports=o.default},function(y,o,g){"use strict";o.__esModule=!0;var r=g(5),t={methodMap:["debug","info","warn","error"],level:"info",lookupLevel:function(l){if(typeof l=="string"){var c=r.indexOf(t.methodMap,l.toLowerCase());l=c>=0?c:parseInt(l,10)}return l},log:function(l){if(l=t.lookupLevel(l),typeof console!="undefined"&&t.lookupLevel(t.level)<=l){var c=t.methodMap[l];console[c]||(c="log");for(var p=arguments.length,a=Array(p>1?p-1:0),u=1;u<p;u++)a[u-1]=arguments[u];console[c].apply(console,a)}}};o.default=t,y.exports=o.default},function(y,o,g){"use strict";function r(v){var _=a(null);_.constructor=!1,_.__defineGetter__=!1,_.__defineSetter__=!1,_.__lookupGetter__=!1;var A=a(null);return A.__proto__=!1,{properties:{whitelist:i.createNewLookupObject(A,v.allowedProtoProperties),defaultValue:v.allowProtoPropertiesByDefault},methods:{whitelist:i.createNewLookupObject(_,v.allowedProtoMethods),defaultValue:v.allowProtoMethodsByDefault}}}function t(v,_,A){return l(typeof v=="function"?_.methods:_.properties,A)}function l(v,_){return v.whitelist[_]!==void 0?v.whitelist[_]===!0:v.defaultValue!==void 0?v.defaultValue:(c(_),!1)}function c(v){f[v]!==!0&&(f[v]=!0,d.log("error",'Handlebars: Access has been denied to resolve the property "'+v+`" because it is not an "own property" of its parent.
You can add a runtime option to disable the check or this warning:
See https://handlebarsjs.com/api-reference/runtime-options.html#options-to-control-prototype-access for details`))}function p(){u(f).forEach(function(v){delete f[v]})}var a=g(34).default,u=g(13).default,h=g(3).default;o.__esModule=!0,o.createProtoAccessControl=r,o.resultIsAllowed=t,o.resetLoggedProperties=p;var i=g(36),m=g(32),d=h(m),f=a(null)},function(y,o,g){y.exports={default:g(35),__esModule:!0}},function(y,o,g){var r=g(9);y.exports=function(t,l){return r.create(t,l)}},function(y,o,g){"use strict";function r(){for(var c=arguments.length,p=Array(c),a=0;a<c;a++)p[a]=arguments[a];return l.extend.apply(void 0,[t(null)].concat(p))}var t=g(34).default;o.__esModule=!0,o.createNewLookupObject=r;var l=g(5)},function(y,o){"use strict";function g(r){this.string=r}o.__esModule=!0,g.prototype.toString=g.prototype.toHTML=function(){return""+this.string},o.default=g,y.exports=o.default},function(y,o,g){"use strict";function r(I){var w=I&&I[0]||1,O=b.COMPILER_REVISION;if(!(w>=b.LAST_COMPATIBLE_COMPILER_REVISION&&w<=b.COMPILER_REVISION)){if(w<b.LAST_COMPATIBLE_COMPILER_REVISION){var B=b.REVISION_CHANGES[O],U=b.REVISION_CHANGES[w];throw new S.default("Template was precompiled with an older version of Handlebars than the current runtime. Please update your precompiler to a newer version ("+B+") or downgrade your runtime to an older version ("+U+").")}throw new S.default("Template was precompiled with a newer version of Handlebars than the current runtime. Please update your runtime to a newer version ("+I[1]+").")}}function t(I,w){function O(F,Y,z){z.hash&&(Y=C.extend({},Y,z.hash),z.ids&&(z.ids[0]=!0)),F=w.VM.resolvePartial.call(this,F,Y,z);var G=C.extend({},z,{hooks:this.hooks,protoAccessControl:this.protoAccessControl}),W=w.VM.invokePartial.call(this,F,Y,G);if(W==null&&w.compile&&(z.partials[z.name]=w.compile(F,I.compilerOptions,w),W=z.partials[z.name](Y,G)),W!=null){if(z.indent){for(var te=W.split(`
`),oe=0,ge=te.length;oe<ge&&(te[oe]||oe+1!==ge);oe++)te[oe]=z.indent+te[oe];W=te.join(`
`)}return W}throw new S.default("The partial "+z.name+" could not be compiled when running in runtime-only mode")}function B(F){function Y(oe){return""+I.main(L,oe,L.helpers,L.partials,G,te,W)}var z=arguments.length<=1||arguments[1]===void 0?{}:arguments[1],G=z.data;B._setup(z),!z.partial&&I.useData&&(G=u(F,G));var W=void 0,te=I.useBlockParams?[]:void 0;return I.useDepths&&(W=z.depths?F!=z.depths[0]?[F].concat(z.depths):z.depths:[F]),(Y=h(I.main,Y,L,z.depths||[],G,te))(F,z)}if(!w)throw new S.default("No environment passed to template");if(!I||!I.main)throw new S.default("Unknown template object: "+typeof I);I.main.decorator=I.main_d,w.VM.checkRevision(I.compiler);var U=I.compiler&&I.compiler[0]===7,L={strict:function(F,Y,z){if(!(F&&Y in F))throw new S.default('"'+Y+'" not defined in '+F,{loc:z});return L.lookupProperty(F,Y)},lookupProperty:function(F,Y){var z=F[Y];return z==null||Object.prototype.hasOwnProperty.call(F,Y)||N.resultIsAllowed(z,L.protoAccessControl,Y)?z:void 0},lookup:function(F,Y){for(var z=F.length,G=0;G<z;G++){var W=F[G]&&L.lookupProperty(F[G],Y);if(W!=null)return F[G][Y]}},lambda:function(F,Y){return typeof F=="function"?F.call(Y):F},escapeExpression:C.escapeExpression,invokePartial:O,fn:function(F){var Y=I[F];return Y.decorator=I[F+"_d"],Y},programs:[],program:function(F,Y,z,G,W){var te=this.programs[F],oe=this.fn(F);return Y||W||G||z?te=l(this,F,oe,Y,z,G,W):te||(te=this.programs[F]=l(this,F,oe)),te},data:function(F,Y){for(;F&&Y--;)F=F._parent;return F},mergeIfNeeded:function(F,Y){var z=F||Y;return F&&Y&&F!==Y&&(z=C.extend({},Y,F)),z},nullContext:d({}),noop:w.VM.noop,compilerInfo:I.compiler};return B.isTop=!0,B._setup=function(F){if(F.partial)L.protoAccessControl=F.protoAccessControl,L.helpers=F.helpers,L.partials=F.partials,L.decorators=F.decorators,L.hooks=F.hooks;else{var Y=C.extend({},w.helpers,F.helpers);i(Y,L),L.helpers=Y,I.usePartial&&(L.partials=L.mergeIfNeeded(F.partials,w.partials)),(I.usePartial||I.useDecorators)&&(L.decorators=C.extend({},w.decorators,F.decorators)),L.hooks={},L.protoAccessControl=N.createProtoAccessControl(F);var z=F.allowCallsToHelperMissing||U;x.moveHelperToHooks(L,"helperMissing",z),x.moveHelperToHooks(L,"blockHelperMissing",z)}},B._child=function(F,Y,z,G){if(I.useBlockParams&&!z)throw new S.default("must pass block params");if(I.useDepths&&!G)throw new S.default("must pass parent depths");return l(L,F,I[F],Y,0,z,G)},B}function l(I,w,O,B,U,L,F){function Y(z){var G=arguments.length<=1||arguments[1]===void 0?{}:arguments[1],W=F;return!F||z==F[0]||z===I.nullContext&&F[0]===null||(W=[z].concat(F)),O(I,z,I.helpers,I.partials,G.data||B,L&&[G.blockParams].concat(L),W)}return Y=h(O,Y,I,F,B,L),Y.program=w,Y.depth=F?F.length:0,Y.blockParams=U||0,Y}function c(I,w,O){return I?I.call||O.name||(O.name=I,I=O.partials[I]):I=O.name==="@partial-block"?O.data["partial-block"]:O.partials[O.name],I}function p(I,w,O){var B=O.data&&O.data["partial-block"];O.partial=!0,O.ids&&(O.data.contextPath=O.ids[0]||O.data.contextPath);var U=void 0;if(O.fn&&O.fn!==a&&function(){O.data=b.createFrame(O.data);var L=O.fn;U=O.data["partial-block"]=function(F){var Y=arguments.length<=1||arguments[1]===void 0?{}:arguments[1];return Y.data=b.createFrame(Y.data),Y.data["partial-block"]=B,L(F,Y)},L.partials&&(O.partials=C.extend({},O.partials,L.partials))}(),I===void 0&&U&&(I=U),I===void 0)throw new S.default("The partial "+O.name+" could not be found");if(I instanceof Function)return I(w,O)}function a(){return""}function u(I,w){return w&&"root"in w||(w=w?b.createFrame(w):{},w.root=I),w}function h(I,w,O,B,U,L){if(I.decorator){var F={};w=I.decorator(w,F,O,B&&B[0],U,L,B),C.extend(w,F)}return w}function i(I,w){f(I).forEach(function(O){var B=I[O];I[O]=m(B,w)})}function m(I,w){var O=w.lookupProperty;return D.wrapHelper(I,function(B){return C.extend({lookupProperty:O},B)})}var d=g(39).default,f=g(13).default,v=g(3).default,_=g(1).default;o.__esModule=!0,o.checkRevision=r,o.template=t,o.wrapProgram=l,o.resolvePartial=c,o.invokePartial=p,o.noop=a;var A=g(5),C=v(A),E=g(6),S=_(E),b=g(4),x=g(10),D=g(43),N=g(33)},function(y,o,g){y.exports={default:g(40),__esModule:!0}},function(y,o,g){g(41),y.exports=g(21).Object.seal},function(y,o,g){var r=g(42);g(18)("seal",function(t){return function(l){return t&&r(l)?t(l):l}})},function(y,o){y.exports=function(g){return typeof g=="object"?g!==null:typeof g=="function"}},function(y,o){"use strict";function g(r,t){if(typeof r!="function")return r;var l=function(){var c=arguments[arguments.length-1];return arguments[arguments.length-1]=t(c),r.apply(this,arguments)};return l}o.__esModule=!0,o.wrapHelper=g},function(y,o){(function(g){"use strict";o.__esModule=!0,o.default=function(r){var t=typeof g!="undefined"?g:window,l=t.Handlebars;r.noConflict=function(){return t.Handlebars===r&&(t.Handlebars=l),r}},y.exports=o.default}).call(o,function(){return this}())},function(y,o){"use strict";o.__esModule=!0;var g={helpers:{helperExpression:function(r){return r.type==="SubExpression"||(r.type==="MustacheStatement"||r.type==="BlockStatement")&&!!(r.params&&r.params.length||r.hash)},scopedId:function(r){return/^\.|this\b/.test(r.original)},simpleId:function(r){return r.parts.length===1&&!g.helpers.scopedId(r)&&!r.depth}}};o.default=g,y.exports=o.default},function(y,o,g){"use strict";function r(v,_){if(v.type==="Program")return v;a.default.yy=f,f.locInfo=function(C){return new f.SourceLocation(_&&_.srcName,C)};var A=a.default.parse(v);return A}function t(v,_){var A=r(v,_),C=new h.default(_);return C.accept(A)}var l=g(1).default,c=g(3).default;o.__esModule=!0,o.parseWithoutProcessing=r,o.parse=t;var p=g(47),a=l(p),u=g(48),h=l(u),i=g(50),m=c(i),d=g(5);o.parser=a.default;var f={};d.extend(f,m)},function(y,o){"use strict";o.__esModule=!0;var g=function(){function r(){this.yy={}}var t={trace:function(){},yy:{},symbols_:{error:2,root:3,program:4,EOF:5,program_repetition0:6,statement:7,mustache:8,block:9,rawBlock:10,partial:11,partialBlock:12,content:13,COMMENT:14,CONTENT:15,openRawBlock:16,rawBlock_repetition0:17,END_RAW_BLOCK:18,OPEN_RAW_BLOCK:19,helperName:20,openRawBlock_repetition0:21,openRawBlock_option0:22,CLOSE_RAW_BLOCK:23,openBlock:24,block_option0:25,closeBlock:26,openInverse:27,block_option1:28,OPEN_BLOCK:29,openBlock_repetition0:30,openBlock_option0:31,openBlock_option1:32,CLOSE:33,OPEN_INVERSE:34,openInverse_repetition0:35,openInverse_option0:36,openInverse_option1:37,openInverseChain:38,OPEN_INVERSE_CHAIN:39,openInverseChain_repetition0:40,openInverseChain_option0:41,openInverseChain_option1:42,inverseAndProgram:43,INVERSE:44,inverseChain:45,inverseChain_option0:46,OPEN_ENDBLOCK:47,OPEN:48,mustache_repetition0:49,mustache_option0:50,OPEN_UNESCAPED:51,mustache_repetition1:52,mustache_option1:53,CLOSE_UNESCAPED:54,OPEN_PARTIAL:55,partialName:56,partial_repetition0:57,partial_option0:58,openPartialBlock:59,OPEN_PARTIAL_BLOCK:60,openPartialBlock_repetition0:61,openPartialBlock_option0:62,param:63,sexpr:64,OPEN_SEXPR:65,sexpr_repetition0:66,sexpr_option0:67,CLOSE_SEXPR:68,hash:69,hash_repetition_plus0:70,hashSegment:71,ID:72,EQUALS:73,blockParams:74,OPEN_BLOCK_PARAMS:75,blockParams_repetition_plus0:76,CLOSE_BLOCK_PARAMS:77,path:78,dataName:79,STRING:80,NUMBER:81,BOOLEAN:82,UNDEFINED:83,NULL:84,DATA:85,pathSegments:86,SEP:87,$accept:0,$end:1},terminals_:{2:"error",5:"EOF",14:"COMMENT",15:"CONTENT",18:"END_RAW_BLOCK",19:"OPEN_RAW_BLOCK",23:"CLOSE_RAW_BLOCK",29:"OPEN_BLOCK",33:"CLOSE",34:"OPEN_INVERSE",39:"OPEN_INVERSE_CHAIN",44:"INVERSE",47:"OPEN_ENDBLOCK",48:"OPEN",51:"OPEN_UNESCAPED",54:"CLOSE_UNESCAPED",55:"OPEN_PARTIAL",60:"OPEN_PARTIAL_BLOCK",65:"OPEN_SEXPR",68:"CLOSE_SEXPR",72:"ID",73:"EQUALS",75:"OPEN_BLOCK_PARAMS",77:"CLOSE_BLOCK_PARAMS",80:"STRING",81:"NUMBER",82:"BOOLEAN",83:"UNDEFINED",84:"NULL",85:"DATA",87:"SEP"},productions_:[0,[3,2],[4,1],[7,1],[7,1],[7,1],[7,1],[7,1],[7,1],[7,1],[13,1],[10,3],[16,5],[9,4],[9,4],[24,6],[27,6],[38,6],[43,2],[45,3],[45,1],[26,3],[8,5],[8,5],[11,5],[12,3],[59,5],[63,1],[63,1],[64,5],[69,1],[71,3],[74,3],[20,1],[20,1],[20,1],[20,1],[20,1],[20,1],[20,1],[56,1],[56,1],[79,2],[78,1],[86,3],[86,1],[6,0],[6,2],[17,0],[17,2],[21,0],[21,2],[22,0],[22,1],[25,0],[25,1],[28,0],[28,1],[30,0],[30,2],[31,0],[31,1],[32,0],[32,1],[35,0],[35,2],[36,0],[36,1],[37,0],[37,1],[40,0],[40,2],[41,0],[41,1],[42,0],[42,1],[46,0],[46,1],[49,0],[49,2],[50,0],[50,1],[52,0],[52,2],[53,0],[53,1],[57,0],[57,2],[58,0],[58,1],[61,0],[61,2],[62,0],[62,1],[66,0],[66,2],[67,0],[67,1],[70,1],[70,2],[76,1],[76,2]],performAction:function(c,p,a,u,h,i,m){var d=i.length-1;switch(h){case 1:return i[d-1];case 2:this.$=u.prepareProgram(i[d]);break;case 3:this.$=i[d];break;case 4:this.$=i[d];break;case 5:this.$=i[d];break;case 6:this.$=i[d];break;case 7:this.$=i[d];break;case 8:this.$=i[d];break;case 9:this.$={type:"CommentStatement",value:u.stripComment(i[d]),strip:u.stripFlags(i[d],i[d]),loc:u.locInfo(this._$)};break;case 10:this.$={type:"ContentStatement",original:i[d],value:i[d],loc:u.locInfo(this._$)};break;case 11:this.$=u.prepareRawBlock(i[d-2],i[d-1],i[d],this._$);break;case 12:this.$={path:i[d-3],params:i[d-2],hash:i[d-1]};break;case 13:this.$=u.prepareBlock(i[d-3],i[d-2],i[d-1],i[d],!1,this._$);break;case 14:this.$=u.prepareBlock(i[d-3],i[d-2],i[d-1],i[d],!0,this._$);break;case 15:this.$={open:i[d-5],path:i[d-4],params:i[d-3],hash:i[d-2],blockParams:i[d-1],strip:u.stripFlags(i[d-5],i[d])};break;case 16:this.$={path:i[d-4],params:i[d-3],hash:i[d-2],blockParams:i[d-1],strip:u.stripFlags(i[d-5],i[d])};break;case 17:this.$={path:i[d-4],params:i[d-3],hash:i[d-2],blockParams:i[d-1],strip:u.stripFlags(i[d-5],i[d])};break;case 18:this.$={strip:u.stripFlags(i[d-1],i[d-1]),program:i[d]};break;case 19:var f=u.prepareBlock(i[d-2],i[d-1],i[d],i[d],!1,this._$),v=u.prepareProgram([f],i[d-1].loc);v.chained=!0,this.$={strip:i[d-2].strip,program:v,chain:!0};break;case 20:this.$=i[d];break;case 21:this.$={path:i[d-1],strip:u.stripFlags(i[d-2],i[d])};break;case 22:this.$=u.prepareMustache(i[d-3],i[d-2],i[d-1],i[d-4],u.stripFlags(i[d-4],i[d]),this._$);break;case 23:this.$=u.prepareMustache(i[d-3],i[d-2],i[d-1],i[d-4],u.stripFlags(i[d-4],i[d]),this._$);break;case 24:this.$={type:"PartialStatement",name:i[d-3],params:i[d-2],hash:i[d-1],indent:"",strip:u.stripFlags(i[d-4],i[d]),loc:u.locInfo(this._$)};break;case 25:this.$=u.preparePartialBlock(i[d-2],i[d-1],i[d],this._$);break;case 26:this.$={path:i[d-3],params:i[d-2],hash:i[d-1],strip:u.stripFlags(i[d-4],i[d])};break;case 27:this.$=i[d];break;case 28:this.$=i[d];break;case 29:this.$={type:"SubExpression",path:i[d-3],params:i[d-2],hash:i[d-1],loc:u.locInfo(this._$)};break;case 30:this.$={type:"Hash",pairs:i[d],loc:u.locInfo(this._$)};break;case 31:this.$={type:"HashPair",key:u.id(i[d-2]),value:i[d],loc:u.locInfo(this._$)};break;case 32:this.$=u.id(i[d-1]);break;case 33:this.$=i[d];break;case 34:this.$=i[d];break;case 35:this.$={type:"StringLiteral",value:i[d],original:i[d],loc:u.locInfo(this._$)};break;case 36:this.$={type:"NumberLiteral",value:Number(i[d]),original:Number(i[d]),loc:u.locInfo(this._$)};break;case 37:this.$={type:"BooleanLiteral",value:i[d]==="true",original:i[d]==="true",loc:u.locInfo(this._$)};break;case 38:this.$={type:"UndefinedLiteral",original:void 0,value:void 0,loc:u.locInfo(this._$)};break;case 39:this.$={type:"NullLiteral",original:null,value:null,loc:u.locInfo(this._$)};break;case 40:this.$=i[d];break;case 41:this.$=i[d];break;case 42:this.$=u.preparePath(!0,i[d],this._$);break;case 43:this.$=u.preparePath(!1,i[d],this._$);break;case 44:i[d-2].push({part:u.id(i[d]),original:i[d],separator:i[d-1]}),this.$=i[d-2];break;case 45:this.$=[{part:u.id(i[d]),original:i[d]}];break;case 46:this.$=[];break;case 47:i[d-1].push(i[d]);break;case 48:this.$=[];break;case 49:i[d-1].push(i[d]);break;case 50:this.$=[];break;case 51:i[d-1].push(i[d]);break;case 58:this.$=[];break;case 59:i[d-1].push(i[d]);break;case 64:this.$=[];break;case 65:i[d-1].push(i[d]);break;case 70:this.$=[];break;case 71:i[d-1].push(i[d]);break;case 78:this.$=[];break;case 79:i[d-1].push(i[d]);break;case 82:this.$=[];break;case 83:i[d-1].push(i[d]);break;case 86:this.$=[];break;case 87:i[d-1].push(i[d]);break;case 90:this.$=[];break;case 91:i[d-1].push(i[d]);break;case 94:this.$=[];break;case 95:i[d-1].push(i[d]);break;case 98:this.$=[i[d]];break;case 99:i[d-1].push(i[d]);break;case 100:this.$=[i[d]];break;case 101:i[d-1].push(i[d])}},table:[{3:1,4:2,5:[2,46],6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{1:[3]},{5:[1,4]},{5:[2,2],7:5,8:6,9:7,10:8,11:9,12:10,13:11,14:[1,12],15:[1,20],16:17,19:[1,23],24:15,27:16,29:[1,21],34:[1,22],39:[2,2],44:[2,2],47:[2,2],48:[1,13],51:[1,14],55:[1,18],59:19,60:[1,24]},{1:[2,1]},{5:[2,47],14:[2,47],15:[2,47],19:[2,47],29:[2,47],34:[2,47],39:[2,47],44:[2,47],47:[2,47],48:[2,47],51:[2,47],55:[2,47],60:[2,47]},{5:[2,3],14:[2,3],15:[2,3],19:[2,3],29:[2,3],34:[2,3],39:[2,3],44:[2,3],47:[2,3],48:[2,3],51:[2,3],55:[2,3],60:[2,3]},{5:[2,4],14:[2,4],15:[2,4],19:[2,4],29:[2,4],34:[2,4],39:[2,4],44:[2,4],47:[2,4],48:[2,4],51:[2,4],55:[2,4],60:[2,4]},{5:[2,5],14:[2,5],15:[2,5],19:[2,5],29:[2,5],34:[2,5],39:[2,5],44:[2,5],47:[2,5],48:[2,5],51:[2,5],55:[2,5],60:[2,5]},{5:[2,6],14:[2,6],15:[2,6],19:[2,6],29:[2,6],34:[2,6],39:[2,6],44:[2,6],47:[2,6],48:[2,6],51:[2,6],55:[2,6],60:[2,6]},{5:[2,7],14:[2,7],15:[2,7],19:[2,7],29:[2,7],34:[2,7],39:[2,7],44:[2,7],47:[2,7],48:[2,7],51:[2,7],55:[2,7],60:[2,7]},{5:[2,8],14:[2,8],15:[2,8],19:[2,8],29:[2,8],34:[2,8],39:[2,8],44:[2,8],47:[2,8],48:[2,8],51:[2,8],55:[2,8],60:[2,8]},{5:[2,9],14:[2,9],15:[2,9],19:[2,9],29:[2,9],34:[2,9],39:[2,9],44:[2,9],47:[2,9],48:[2,9],51:[2,9],55:[2,9],60:[2,9]},{20:25,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:36,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{4:37,6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],39:[2,46],44:[2,46],47:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{4:38,6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],44:[2,46],47:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{15:[2,48],17:39,18:[2,48]},{20:41,56:40,64:42,65:[1,43],72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{4:44,6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],47:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{5:[2,10],14:[2,10],15:[2,10],18:[2,10],19:[2,10],29:[2,10],34:[2,10],39:[2,10],44:[2,10],47:[2,10],48:[2,10],51:[2,10],55:[2,10],60:[2,10]},{20:45,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:46,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:47,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:41,56:48,64:42,65:[1,43],72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{33:[2,78],49:49,65:[2,78],72:[2,78],80:[2,78],81:[2,78],82:[2,78],83:[2,78],84:[2,78],85:[2,78]},{23:[2,33],33:[2,33],54:[2,33],65:[2,33],68:[2,33],72:[2,33],75:[2,33],80:[2,33],81:[2,33],82:[2,33],83:[2,33],84:[2,33],85:[2,33]},{23:[2,34],33:[2,34],54:[2,34],65:[2,34],68:[2,34],72:[2,34],75:[2,34],80:[2,34],81:[2,34],82:[2,34],83:[2,34],84:[2,34],85:[2,34]},{23:[2,35],33:[2,35],54:[2,35],65:[2,35],68:[2,35],72:[2,35],75:[2,35],80:[2,35],81:[2,35],82:[2,35],83:[2,35],84:[2,35],85:[2,35]},{23:[2,36],33:[2,36],54:[2,36],65:[2,36],68:[2,36],72:[2,36],75:[2,36],80:[2,36],81:[2,36],82:[2,36],83:[2,36],84:[2,36],85:[2,36]},{23:[2,37],33:[2,37],54:[2,37],65:[2,37],68:[2,37],72:[2,37],75:[2,37],80:[2,37],81:[2,37],82:[2,37],83:[2,37],84:[2,37],85:[2,37]},{23:[2,38],33:[2,38],54:[2,38],65:[2,38],68:[2,38],72:[2,38],75:[2,38],80:[2,38],81:[2,38],82:[2,38],83:[2,38],84:[2,38],85:[2,38]},{23:[2,39],33:[2,39],54:[2,39],65:[2,39],68:[2,39],72:[2,39],75:[2,39],80:[2,39],81:[2,39],82:[2,39],83:[2,39],84:[2,39],85:[2,39]},{23:[2,43],33:[2,43],54:[2,43],65:[2,43],68:[2,43],72:[2,43],75:[2,43],80:[2,43],81:[2,43],82:[2,43],83:[2,43],84:[2,43],85:[2,43],87:[1,50]},{72:[1,35],86:51},{23:[2,45],33:[2,45],54:[2,45],65:[2,45],68:[2,45],72:[2,45],75:[2,45],80:[2,45],81:[2,45],82:[2,45],83:[2,45],84:[2,45],85:[2,45],87:[2,45]},{52:52,54:[2,82],65:[2,82],72:[2,82],80:[2,82],81:[2,82],82:[2,82],83:[2,82],84:[2,82],85:[2,82]},{25:53,38:55,39:[1,57],43:56,44:[1,58],45:54,47:[2,54]},{28:59,43:60,44:[1,58],47:[2,56]},{13:62,15:[1,20],18:[1,61]},{33:[2,86],57:63,65:[2,86],72:[2,86],80:[2,86],81:[2,86],82:[2,86],83:[2,86],84:[2,86],85:[2,86]},{33:[2,40],65:[2,40],72:[2,40],80:[2,40],81:[2,40],82:[2,40],83:[2,40],84:[2,40],85:[2,40]},{33:[2,41],65:[2,41],72:[2,41],80:[2,41],81:[2,41],82:[2,41],83:[2,41],84:[2,41],85:[2,41]},{20:64,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{26:65,47:[1,66]},{30:67,33:[2,58],65:[2,58],72:[2,58],75:[2,58],80:[2,58],81:[2,58],82:[2,58],83:[2,58],84:[2,58],85:[2,58]},{33:[2,64],35:68,65:[2,64],72:[2,64],75:[2,64],80:[2,64],81:[2,64],82:[2,64],83:[2,64],84:[2,64],85:[2,64]},{21:69,23:[2,50],65:[2,50],72:[2,50],80:[2,50],81:[2,50],82:[2,50],83:[2,50],84:[2,50],85:[2,50]},{33:[2,90],61:70,65:[2,90],72:[2,90],80:[2,90],81:[2,90],82:[2,90],83:[2,90],84:[2,90],85:[2,90]},{20:74,33:[2,80],50:71,63:72,64:75,65:[1,43],69:73,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{72:[1,79]},{23:[2,42],33:[2,42],54:[2,42],65:[2,42],68:[2,42],72:[2,42],75:[2,42],80:[2,42],81:[2,42],82:[2,42],83:[2,42],84:[2,42],85:[2,42],87:[1,50]},{20:74,53:80,54:[2,84],63:81,64:75,65:[1,43],69:82,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{26:83,47:[1,66]},{47:[2,55]},{4:84,6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],39:[2,46],44:[2,46],47:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{47:[2,20]},{20:85,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{4:86,6:3,14:[2,46],15:[2,46],19:[2,46],29:[2,46],34:[2,46],47:[2,46],48:[2,46],51:[2,46],55:[2,46],60:[2,46]},{26:87,47:[1,66]},{47:[2,57]},{5:[2,11],14:[2,11],15:[2,11],19:[2,11],29:[2,11],34:[2,11],39:[2,11],44:[2,11],47:[2,11],48:[2,11],51:[2,11],55:[2,11],60:[2,11]},{15:[2,49],18:[2,49]},{20:74,33:[2,88],58:88,63:89,64:75,65:[1,43],69:90,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{65:[2,94],66:91,68:[2,94],72:[2,94],80:[2,94],81:[2,94],82:[2,94],83:[2,94],84:[2,94],85:[2,94]},{5:[2,25],14:[2,25],15:[2,25],19:[2,25],29:[2,25],34:[2,25],39:[2,25],44:[2,25],47:[2,25],48:[2,25],51:[2,25],55:[2,25],60:[2,25]},{20:92,72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:74,31:93,33:[2,60],63:94,64:75,65:[1,43],69:95,70:76,71:77,72:[1,78],75:[2,60],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:74,33:[2,66],36:96,63:97,64:75,65:[1,43],69:98,70:76,71:77,72:[1,78],75:[2,66],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:74,22:99,23:[2,52],63:100,64:75,65:[1,43],69:101,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{20:74,33:[2,92],62:102,63:103,64:75,65:[1,43],69:104,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{33:[1,105]},{33:[2,79],65:[2,79],72:[2,79],80:[2,79],81:[2,79],82:[2,79],83:[2,79],84:[2,79],85:[2,79]},{33:[2,81]},{23:[2,27],33:[2,27],54:[2,27],65:[2,27],68:[2,27],72:[2,27],75:[2,27],80:[2,27],81:[2,27],82:[2,27],83:[2,27],84:[2,27],85:[2,27]},{23:[2,28],33:[2,28],54:[2,28],65:[2,28],68:[2,28],72:[2,28],75:[2,28],80:[2,28],81:[2,28],82:[2,28],83:[2,28],84:[2,28],85:[2,28]},{23:[2,30],33:[2,30],54:[2,30],68:[2,30],71:106,72:[1,107],75:[2,30]},{23:[2,98],33:[2,98],54:[2,98],68:[2,98],72:[2,98],75:[2,98]},{23:[2,45],33:[2,45],54:[2,45],65:[2,45],68:[2,45],72:[2,45],73:[1,108],75:[2,45],80:[2,45],81:[2,45],82:[2,45],83:[2,45],84:[2,45],85:[2,45],87:[2,45]},{23:[2,44],33:[2,44],54:[2,44],65:[2,44],68:[2,44],72:[2,44],75:[2,44],80:[2,44],81:[2,44],82:[2,44],83:[2,44],84:[2,44],85:[2,44],87:[2,44]},{54:[1,109]},{54:[2,83],65:[2,83],72:[2,83],80:[2,83],81:[2,83],82:[2,83],83:[2,83],84:[2,83],85:[2,83]},{54:[2,85]},{5:[2,13],14:[2,13],15:[2,13],19:[2,13],29:[2,13],34:[2,13],39:[2,13],44:[2,13],47:[2,13],48:[2,13],51:[2,13],55:[2,13],60:[2,13]},{38:55,39:[1,57],43:56,44:[1,58],45:111,46:110,47:[2,76]},{33:[2,70],40:112,65:[2,70],72:[2,70],75:[2,70],80:[2,70],81:[2,70],82:[2,70],83:[2,70],84:[2,70],85:[2,70]},{47:[2,18]},{5:[2,14],14:[2,14],15:[2,14],19:[2,14],29:[2,14],34:[2,14],39:[2,14],44:[2,14],47:[2,14],48:[2,14],51:[2,14],55:[2,14],60:[2,14]},{33:[1,113]},{33:[2,87],65:[2,87],72:[2,87],80:[2,87],81:[2,87],82:[2,87],83:[2,87],84:[2,87],85:[2,87]},{33:[2,89]},{20:74,63:115,64:75,65:[1,43],67:114,68:[2,96],69:116,70:76,71:77,72:[1,78],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{33:[1,117]},{32:118,33:[2,62],74:119,75:[1,120]},{33:[2,59],65:[2,59],72:[2,59],75:[2,59],80:[2,59],81:[2,59],82:[2,59],83:[2,59],84:[2,59],85:[2,59]},{33:[2,61],75:[2,61]},{33:[2,68],37:121,74:122,75:[1,120]},{33:[2,65],65:[2,65],72:[2,65],75:[2,65],80:[2,65],81:[2,65],82:[2,65],83:[2,65],84:[2,65],85:[2,65]},{33:[2,67],75:[2,67]},{23:[1,123]},{23:[2,51],65:[2,51],72:[2,51],80:[2,51],81:[2,51],82:[2,51],83:[2,51],84:[2,51],85:[2,51]},{23:[2,53]},{33:[1,124]},{33:[2,91],65:[2,91],72:[2,91],80:[2,91],81:[2,91],82:[2,91],83:[2,91],84:[2,91],85:[2,91]},{33:[2,93]},{5:[2,22],14:[2,22],15:[2,22],19:[2,22],29:[2,22],34:[2,22],39:[2,22],44:[2,22],47:[2,22],48:[2,22],51:[2,22],55:[2,22],60:[2,22]},{23:[2,99],33:[2,99],54:[2,99],68:[2,99],72:[2,99],75:[2,99]},{73:[1,108]},{20:74,63:125,64:75,65:[1,43],72:[1,35],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{5:[2,23],14:[2,23],15:[2,23],19:[2,23],29:[2,23],34:[2,23],39:[2,23],44:[2,23],47:[2,23],48:[2,23],51:[2,23],55:[2,23],60:[2,23]},{47:[2,19]},{47:[2,77]},{20:74,33:[2,72],41:126,63:127,64:75,65:[1,43],69:128,70:76,71:77,72:[1,78],75:[2,72],78:26,79:27,80:[1,28],81:[1,29],82:[1,30],83:[1,31],84:[1,32],85:[1,34],86:33},{5:[2,24],14:[2,24],15:[2,24],19:[2,24],29:[2,24],34:[2,24],39:[2,24],44:[2,24],47:[2,24],48:[2,24],51:[2,24],55:[2,24],60:[2,24]},{68:[1,129]},{65:[2,95],68:[2,95],72:[2,95],80:[2,95],81:[2,95],82:[2,95],83:[2,95],84:[2,95],85:[2,95]},{68:[2,97]},{5:[2,21],14:[2,21],15:[2,21],19:[2,21],29:[2,21],34:[2,21],39:[2,21],44:[2,21],47:[2,21],48:[2,21],51:[2,21],55:[2,21],60:[2,21]},{33:[1,130]},{33:[2,63]},{72:[1,132],76:131},{33:[1,133]},{33:[2,69]},{15:[2,12],18:[2,12]},{14:[2,26],15:[2,26],19:[2,26],29:[2,26],34:[2,26],47:[2,26],48:[2,26],51:[2,26],55:[2,26],60:[2,26]},{23:[2,31],33:[2,31],54:[2,31],68:[2,31],72:[2,31],75:[2,31]},{33:[2,74],42:134,74:135,75:[1,120]},{33:[2,71],65:[2,71],72:[2,71],75:[2,71],80:[2,71],81:[2,71],82:[2,71],83:[2,71],84:[2,71],85:[2,71]},{33:[2,73],75:[2,73]},{23:[2,29],33:[2,29],54:[2,29],65:[2,29],68:[2,29],72:[2,29],75:[2,29],80:[2,29],81:[2,29],82:[2,29],83:[2,29],84:[2,29],85:[2,29]},{14:[2,15],15:[2,15],19:[2,15],29:[2,15],34:[2,15],39:[2,15],44:[2,15],47:[2,15],48:[2,15],51:[2,15],55:[2,15],60:[2,15]},{72:[1,137],77:[1,136]},{72:[2,100],77:[2,100]},{14:[2,16],15:[2,16],19:[2,16],29:[2,16],34:[2,16],44:[2,16],47:[2,16],48:[2,16],51:[2,16],55:[2,16],60:[2,16]},{33:[1,138]},{33:[2,75]},{33:[2,32]},{72:[2,101],77:[2,101]},{14:[2,17],15:[2,17],19:[2,17],29:[2,17],34:[2,17],39:[2,17],44:[2,17],47:[2,17],48:[2,17],51:[2,17],55:[2,17],60:[2,17]}],defaultActions:{4:[2,1],54:[2,55],56:[2,20],60:[2,57],73:[2,81],82:[2,85],86:[2,18],90:[2,89],101:[2,53],104:[2,93],110:[2,19],111:[2,77],116:[2,97],119:[2,63],122:[2,69],135:[2,75],136:[2,32]},parseError:function(c,p){throw new Error(c)},parse:function(c){function p(){var L;return L=a.lexer.lex()||1,typeof L!="number"&&(L=a.symbols_[L]||L),L}var a=this,u=[0],h=[null],i=[],m=this.table,d="",f=0,v=0,_=0;this.lexer.setInput(c),this.lexer.yy=this.yy,this.yy.lexer=this.lexer,this.yy.parser=this,typeof this.lexer.yylloc=="undefined"&&(this.lexer.yylloc={});var A=this.lexer.yylloc;i.push(A);var C=this.lexer.options&&this.lexer.options.ranges;typeof this.yy.parseError=="function"&&(this.parseError=this.yy.parseError);for(var E,S,b,x,D,N,I,w,O,B={};;){if(b=u[u.length-1],this.defaultActions[b]?x=this.defaultActions[b]:(E!==null&&typeof E!="undefined"||(E=p()),x=m[b]&&m[b][E]),typeof x=="undefined"||!x.length||!x[0]){var U="";if(!_){O=[];for(N in m[b])this.terminals_[N]&&N>2&&O.push("'"+this.terminals_[N]+"'");U=this.lexer.showPosition?"Parse error on line "+(f+1)+`:
`+this.lexer.showPosition()+`
Expecting `+O.join(", ")+", got '"+(this.terminals_[E]||E)+"'":"Parse error on line "+(f+1)+": Unexpected "+(E==1?"end of input":"'"+(this.terminals_[E]||E)+"'"),this.parseError(U,{text:this.lexer.match,token:this.terminals_[E]||E,line:this.lexer.yylineno,loc:A,expected:O})}}if(x[0]instanceof Array&&x.length>1)throw new Error("Parse Error: multiple actions possible at state: "+b+", token: "+E);switch(x[0]){case 1:u.push(E),h.push(this.lexer.yytext),i.push(this.lexer.yylloc),u.push(x[1]),E=null,S?(E=S,S=null):(v=this.lexer.yyleng,d=this.lexer.yytext,f=this.lexer.yylineno,A=this.lexer.yylloc,_>0&&_--);break;case 2:if(I=this.productions_[x[1]][1],B.$=h[h.length-I],B._$={first_line:i[i.length-(I||1)].first_line,last_line:i[i.length-1].last_line,first_column:i[i.length-(I||1)].first_column,last_column:i[i.length-1].last_column},C&&(B._$.range=[i[i.length-(I||1)].range[0],i[i.length-1].range[1]]),D=this.performAction.call(B,d,v,f,this.yy,x[1],h,i),typeof D!="undefined")return D;I&&(u=u.slice(0,-1*I*2),h=h.slice(0,-1*I),i=i.slice(0,-1*I)),u.push(this.productions_[x[1]][0]),h.push(B.$),i.push(B._$),w=m[u[u.length-2]][u[u.length-1]],u.push(w);break;case 3:return!0}}return!0}},l=function(){var c={EOF:1,parseError:function(p,a){if(!this.yy.parser)throw new Error(p);this.yy.parser.parseError(p,a)},setInput:function(p){return this._input=p,this._more=this._less=this.done=!1,this.yylineno=this.yyleng=0,this.yytext=this.matched=this.match="",this.conditionStack=["INITIAL"],this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0},this.options.ranges&&(this.yylloc.range=[0,0]),this.offset=0,this},input:function(){var p=this._input[0];this.yytext+=p,this.yyleng++,this.offset++,this.match+=p,this.matched+=p;var a=p.match(/(?:\r\n?|\n).*/g);return a?(this.yylineno++,this.yylloc.last_line++):this.yylloc.last_column++,this.options.ranges&&this.yylloc.range[1]++,this._input=this._input.slice(1),p},unput:function(p){var a=p.length,u=p.split(/(?:\r\n?|\n)/g);this._input=p+this._input,this.yytext=this.yytext.substr(0,this.yytext.length-a-1),this.offset-=a;var h=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1),this.matched=this.matched.substr(0,this.matched.length-1),u.length-1&&(this.yylineno-=u.length-1);var i=this.yylloc.range;return this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:u?(u.length===h.length?this.yylloc.first_column:0)+h[h.length-u.length].length-u[0].length:this.yylloc.first_column-a},this.options.ranges&&(this.yylloc.range=[i[0],i[0]+this.yyleng-a]),this},more:function(){return this._more=!0,this},less:function(p){this.unput(this.match.slice(p))},pastInput:function(){var p=this.matched.substr(0,this.matched.length-this.match.length);return(p.length>20?"...":"")+p.substr(-20).replace(/\n/g,"")},upcomingInput:function(){var p=this.match;return p.length<20&&(p+=this._input.substr(0,20-p.length)),(p.substr(0,20)+(p.length>20?"...":"")).replace(/\n/g,"")},showPosition:function(){var p=this.pastInput(),a=new Array(p.length+1).join("-");return p+this.upcomingInput()+`
`+a+"^"},next:function(){if(this.done)return this.EOF;this._input||(this.done=!0);var p,a,u,h,i;this._more||(this.yytext="",this.match="");for(var m=this._currentRules(),d=0;d<m.length&&(u=this._input.match(this.rules[m[d]]),!u||a&&!(u[0].length>a[0].length)||(a=u,h=d,this.options.flex));d++);return a?(i=a[0].match(/(?:\r\n?|\n).*/g),i&&(this.yylineno+=i.length),this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:i?i[i.length-1].length-i[i.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+a[0].length},this.yytext+=a[0],this.match+=a[0],this.matches=a,this.yyleng=this.yytext.length,this.options.ranges&&(this.yylloc.range=[this.offset,this.offset+=this.yyleng]),this._more=!1,this._input=this._input.slice(a[0].length),this.matched+=a[0],p=this.performAction.call(this,this.yy,this,m[h],this.conditionStack[this.conditionStack.length-1]),this.done&&this._input&&(this.done=!1),p||void 0):this._input===""?this.EOF:this.parseError("Lexical error on line "+(this.yylineno+1)+`. Unrecognized text.
`+this.showPosition(),{text:"",token:null,line:this.yylineno})},lex:function(){var p=this.next();return typeof p!="undefined"?p:this.lex()},begin:function(p){this.conditionStack.push(p)},popState:function(){return this.conditionStack.pop()},_currentRules:function(){return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules},topState:function(){return this.conditionStack[this.conditionStack.length-2]},pushState:function(p){this.begin(p)}};return c.options={},c.performAction=function(p,a,u,h){function i(m,d){return a.yytext=a.yytext.substring(m,a.yyleng-d+m)}switch(u){case 0:if(a.yytext.slice(-2)==="\\\\"?(i(0,1),this.begin("mu")):a.yytext.slice(-1)==="\\"?(i(0,1),this.begin("emu")):this.begin("mu"),a.yytext)return 15;break;case 1:return 15;case 2:return this.popState(),15;case 3:return this.begin("raw"),15;case 4:return this.popState(),this.conditionStack[this.conditionStack.length-1]==="raw"?15:(i(5,9),"END_RAW_BLOCK");case 5:return 15;case 6:return this.popState(),14;case 7:return 65;case 8:return 68;case 9:return 19;case 10:return this.popState(),this.begin("raw"),23;case 11:return 55;case 12:return 60;case 13:return 29;case 14:return 47;case 15:return this.popState(),44;case 16:return this.popState(),44;case 17:return 34;case 18:return 39;case 19:return 51;case 20:return 48;case 21:this.unput(a.yytext),this.popState(),this.begin("com");break;case 22:return this.popState(),14;case 23:return 48;case 24:return 73;case 25:return 72;case 26:return 72;case 27:return 87;case 28:break;case 29:return this.popState(),54;case 30:return this.popState(),33;case 31:return a.yytext=i(1,2).replace(/\\"/g,'"'),80;case 32:return a.yytext=i(1,2).replace(/\\'/g,"'"),80;case 33:return 85;case 34:return 82;case 35:return 82;case 36:return 83;case 37:return 84;case 38:return 81;case 39:return 75;case 40:return 77;case 41:return 72;case 42:return a.yytext=a.yytext.replace(/\\([\\\]])/g,"$1"),72;case 43:return"INVALID";case 44:return 5}},c.rules=[/^(?:[^\x00]*?(?=(\{\{)))/,/^(?:[^\x00]+)/,/^(?:[^\x00]{2,}?(?=(\{\{|\\\{\{|\\\\\{\{|$)))/,/^(?:\{\{\{\{(?=[^\/]))/,/^(?:\{\{\{\{\/[^\s!"#%-,\.\/;->@\[-\^`\{-~]+(?=[=}\s\/.])\}\}\}\})/,/^(?:[^\x00]+?(?=(\{\{\{\{)))/,/^(?:[\s\S]*?--(~)?\}\})/,/^(?:\()/,/^(?:\))/,/^(?:\{\{\{\{)/,/^(?:\}\}\}\})/,/^(?:\{\{(~)?>)/,/^(?:\{\{(~)?#>)/,/^(?:\{\{(~)?#\*?)/,/^(?:\{\{(~)?\/)/,/^(?:\{\{(~)?\^\s*(~)?\}\})/,/^(?:\{\{(~)?\s*else\s*(~)?\}\})/,/^(?:\{\{(~)?\^)/,/^(?:\{\{(~)?\s*else\b)/,/^(?:\{\{(~)?\{)/,/^(?:\{\{(~)?&)/,/^(?:\{\{(~)?!--)/,/^(?:\{\{(~)?![\s\S]*?\}\})/,/^(?:\{\{(~)?\*?)/,/^(?:=)/,/^(?:\.\.)/,/^(?:\.(?=([=~}\s\/.)|])))/,/^(?:[\/.])/,/^(?:\s+)/,/^(?:\}(~)?\}\})/,/^(?:(~)?\}\})/,/^(?:"(\\["]|[^"])*")/,/^(?:'(\\[']|[^'])*')/,/^(?:@)/,/^(?:true(?=([~}\s)])))/,/^(?:false(?=([~}\s)])))/,/^(?:undefined(?=([~}\s)])))/,/^(?:null(?=([~}\s)])))/,/^(?:-?[0-9]+(?:\.[0-9]+)?(?=([~}\s)])))/,/^(?:as\s+\|)/,/^(?:\|)/,/^(?:([^\s!"#%-,\.\/;->@\[-\^`\{-~]+(?=([=~}\s\/.)|]))))/,/^(?:\[(\\\]|[^\]])*\])/,/^(?:.)/,/^(?:$)/],c.conditions={mu:{rules:[7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44],inclusive:!1},emu:{rules:[2],inclusive:!1},com:{rules:[6],inclusive:!1},raw:{rules:[3,4,5],inclusive:!1},INITIAL:{rules:[0,1,44],inclusive:!0}},c}();return t.lexer=l,r.prototype=t,t.Parser=r,new r}();o.default=g,y.exports=o.default},function(y,o,g){"use strict";function r(){var i=arguments.length<=0||arguments[0]===void 0?{}:arguments[0];this.options=i}function t(i,m,d){m===void 0&&(m=i.length);var f=i[m-1],v=i[m-2];return f?f.type==="ContentStatement"?(v||!d?/\r?\n\s*?$/:/(^|\r?\n)\s*?$/).test(f.original):void 0:d}function l(i,m,d){m===void 0&&(m=-1);var f=i[m+1],v=i[m+2];return f?f.type==="ContentStatement"?(v||!d?/^\s*?\r?\n/:/^\s*?(\r?\n|$)/).test(f.original):void 0:d}function c(i,m,d){var f=i[m==null?0:m+1];if(f&&f.type==="ContentStatement"&&(d||!f.rightStripped)){var v=f.value;f.value=f.value.replace(d?/^\s+/:/^[ \t]*\r?\n?/,""),f.rightStripped=f.value!==v}}function p(i,m,d){var f=i[m==null?i.length-1:m-1];if(f&&f.type==="ContentStatement"&&(d||!f.leftStripped)){var v=f.value;return f.value=f.value.replace(d?/\s+$/:/[ \t]+$/,""),f.leftStripped=f.value!==v,f.leftStripped}}var a=g(1).default;o.__esModule=!0;var u=g(49),h=a(u);r.prototype=new h.default,r.prototype.Program=function(i){var m=!this.options.ignoreStandalone,d=!this.isRootSeen;this.isRootSeen=!0;for(var f=i.body,v=0,_=f.length;v<_;v++){var A=f[v],C=this.accept(A);if(C){var E=t(f,v,d),S=l(f,v,d),b=C.openStandalone&&E,x=C.closeStandalone&&S,D=C.inlineStandalone&&E&&S;C.close&&c(f,v,!0),C.open&&p(f,v,!0),m&&D&&(c(f,v),p(f,v)&&A.type==="PartialStatement"&&(A.indent=/([ \t]+$)/.exec(f[v-1].original)[1])),m&&b&&(c((A.program||A.inverse).body),p(f,v)),m&&x&&(c(f,v),p((A.inverse||A.program).body))}}return i},r.prototype.BlockStatement=r.prototype.DecoratorBlock=r.prototype.PartialBlockStatement=function(i){this.accept(i.program),this.accept(i.inverse);var m=i.program||i.inverse,d=i.program&&i.inverse,f=d,v=d;if(d&&d.chained)for(f=d.body[0].program;v.chained;)v=v.body[v.body.length-1].program;var _={open:i.openStrip.open,close:i.closeStrip.close,openStandalone:l(m.body),closeStandalone:t((f||m).body)};if(i.openStrip.close&&c(m.body,null,!0),d){var A=i.inverseStrip;A.open&&p(m.body,null,!0),A.close&&c(f.body,null,!0),i.closeStrip.open&&p(v.body,null,!0),!this.options.ignoreStandalone&&t(m.body)&&l(f.body)&&(p(m.body),c(f.body))}else i.closeStrip.open&&p(m.body,null,!0);return _},r.prototype.Decorator=r.prototype.MustacheStatement=function(i){return i.strip},r.prototype.PartialStatement=r.prototype.CommentStatement=function(i){var m=i.strip||{};return{inlineStandalone:!0,open:m.open,close:m.close}},o.default=r,y.exports=o.default},function(y,o,g){"use strict";function r(){this.parents=[]}function t(h){this.acceptRequired(h,"path"),this.acceptArray(h.params),this.acceptKey(h,"hash")}function l(h){t.call(this,h),this.acceptKey(h,"program"),this.acceptKey(h,"inverse")}function c(h){this.acceptRequired(h,"name"),this.acceptArray(h.params),this.acceptKey(h,"hash")}var p=g(1).default;o.__esModule=!0;var a=g(6),u=p(a);r.prototype={constructor:r,mutating:!1,acceptKey:function(h,i){var m=this.accept(h[i]);if(this.mutating){if(m&&!r.prototype[m.type])throw new u.default('Unexpected node type "'+m.type+'" found when accepting '+i+" on "+h.type);h[i]=m}},acceptRequired:function(h,i){if(this.acceptKey(h,i),!h[i])throw new u.default(h.type+" requires "+i)},acceptArray:function(h){for(var i=0,m=h.length;i<m;i++)this.acceptKey(h,i),h[i]||(h.splice(i,1),i--,m--)},accept:function(h){if(h){if(!this[h.type])throw new u.default("Unknown type: "+h.type,h);this.current&&this.parents.unshift(this.current),this.current=h;var i=this[h.type](h);return this.current=this.parents.shift(),!this.mutating||i?i:i!==!1?h:void 0}},Program:function(h){this.acceptArray(h.body)},MustacheStatement:t,Decorator:t,BlockStatement:l,DecoratorBlock:l,PartialStatement:c,PartialBlockStatement:function(h){c.call(this,h),this.acceptKey(h,"program")},ContentStatement:function(){},CommentStatement:function(){},SubExpression:t,PathExpression:function(){},StringLiteral:function(){},NumberLiteral:function(){},BooleanLiteral:function(){},UndefinedLiteral:function(){},NullLiteral:function(){},Hash:function(h){this.acceptArray(h.pairs)},HashPair:function(h){this.acceptRequired(h,"value")}},o.default=r,y.exports=o.default},function(y,o,g){"use strict";function r(A,C){if(C=C.path?C.path.original:C,A.path.original!==C){var E={loc:A.path.loc};throw new _.default(A.path.original+" doesn't match "+C,E)}}function t(A,C){this.source=A,this.start={line:C.first_line,column:C.first_column},this.end={line:C.last_line,column:C.last_column}}function l(A){return/^\[.*\]$/.test(A)?A.substring(1,A.length-1):A}function c(A,C){return{open:A.charAt(2)==="~",close:C.charAt(C.length-3)==="~"}}function p(A){return A.replace(/^\{\{~?!-?-?/,"").replace(/-?-?~?\}\}$/,"")}function a(A,C,E){E=this.locInfo(E);for(var S=A?"@":"",b=[],x=0,D=0,N=C.length;D<N;D++){var I=C[D].part,w=C[D].original!==I;if(S+=(C[D].separator||"")+I,w||I!==".."&&I!=="."&&I!=="this")b.push(I);else{if(b.length>0)throw new _.default("Invalid path: "+S,{loc:E});I===".."&&x++}}return{type:"PathExpression",data:A,depth:x,parts:b,original:S,loc:E}}function u(A,C,E,S,b,x){var D=S.charAt(3)||S.charAt(2),N=D!=="{"&&D!=="&",I=/\*/.test(S);return{type:I?"Decorator":"MustacheStatement",path:A,params:C,hash:E,escaped:N,strip:b,loc:this.locInfo(x)}}function h(A,C,E,S){r(A,E),S=this.locInfo(S);var b={type:"Program",body:C,strip:{},loc:S};return{type:"BlockStatement",path:A.path,params:A.params,hash:A.hash,program:b,openStrip:{},inverseStrip:{},closeStrip:{},loc:S}}function i(A,C,E,S,b,x){S&&S.path&&r(A,S);var D=/\*/.test(A.open);C.blockParams=A.blockParams;var N=void 0,I=void 0;if(E){if(D)throw new _.default("Unexpected inverse block on decorator",E);E.chain&&(E.program.body[0].closeStrip=S.strip),I=E.strip,N=E.program}return b&&(b=N,N=C,C=b),{type:D?"DecoratorBlock":"BlockStatement",path:A.path,params:A.params,hash:A.hash,program:C,inverse:N,openStrip:A.strip,inverseStrip:I,closeStrip:S&&S.strip,loc:this.locInfo(x)}}function m(A,C){if(!C&&A.length){var E=A[0].loc,S=A[A.length-1].loc;E&&S&&(C={source:E.source,start:{line:E.start.line,column:E.start.column},end:{line:S.end.line,column:S.end.column}})}return{type:"Program",body:A,strip:{},loc:C}}function d(A,C,E,S){return r(A,E),{type:"PartialBlockStatement",name:A.path,params:A.params,hash:A.hash,program:C,openStrip:A.strip,closeStrip:E&&E.strip,loc:this.locInfo(S)}}var f=g(1).default;o.__esModule=!0,o.SourceLocation=t,o.id=l,o.stripFlags=c,o.stripComment=p,o.preparePath=a,o.prepareMustache=u,o.prepareRawBlock=h,o.prepareBlock=i,o.prepareProgram=m,o.preparePartialBlock=d;var v=g(6),_=f(v)},function(y,o,g){"use strict";function r(){}function t(_,A,C){if(_==null||typeof _!="string"&&_.type!=="Program")throw new i.default("You must pass a string or Handlebars AST to Handlebars.precompile. You passed "+_);A=A||{},"data"in A||(A.data=!0),A.compat&&(A.useDepths=!0);var E=C.parse(_,A),S=new C.Compiler().compile(E,A);return new C.JavaScriptCompiler().compile(S,A)}function l(_,A,C){function E(){var x=C.parse(_,A),D=new C.Compiler().compile(x,A),N=new C.JavaScriptCompiler().compile(D,A,void 0,!0);return C.template(N)}function S(x,D){return b||(b=E()),b.call(this,x,D)}if(A===void 0&&(A={}),_==null||typeof _!="string"&&_.type!=="Program")throw new i.default("You must pass a string or Handlebars AST to Handlebars.compile. You passed "+_);A=m.extend({},A),"data"in A||(A.data=!0),A.compat&&(A.useDepths=!0);var b=void 0;return S._setup=function(x){return b||(b=E()),b._setup(x)},S._child=function(x,D,N,I){return b||(b=E()),b._child(x,D,N,I)},S}function c(_,A){if(_===A)return!0;if(m.isArray(_)&&m.isArray(A)&&_.length===A.length){for(var C=0;C<_.length;C++)if(!c(_[C],A[C]))return!1;return!0}}function p(_){if(!_.path.parts){var A=_.path;_.path={type:"PathExpression",data:!1,depth:0,parts:[A.original+""],original:A.original+"",loc:A.loc}}}var a=g(34).default,u=g(1).default;o.__esModule=!0,o.Compiler=r,o.precompile=t,o.compile=l;var h=g(6),i=u(h),m=g(5),d=g(45),f=u(d),v=[].slice;r.prototype={compiler:r,equals:function(_){var A=this.opcodes.length;if(_.opcodes.length!==A)return!1;for(var C=0;C<A;C++){var E=this.opcodes[C],S=_.opcodes[C];if(E.opcode!==S.opcode||!c(E.args,S.args))return!1}A=this.children.length;for(var C=0;C<A;C++)if(!this.children[C].equals(_.children[C]))return!1;return!0},guid:0,compile:function(_,A){return this.sourceNode=[],this.opcodes=[],this.children=[],this.options=A,this.stringParams=A.stringParams,this.trackIds=A.trackIds,A.blockParams=A.blockParams||[],A.knownHelpers=m.extend(a(null),{helperMissing:!0,blockHelperMissing:!0,each:!0,if:!0,unless:!0,with:!0,log:!0,lookup:!0},A.knownHelpers),this.accept(_)},compileProgram:function(_){var A=new this.compiler,C=A.compile(_,this.options),E=this.guid++;return this.usePartial=this.usePartial||C.usePartial,this.children[E]=C,this.useDepths=this.useDepths||C.useDepths,E},accept:function(_){if(!this[_.type])throw new i.default("Unknown type: "+_.type,_);this.sourceNode.unshift(_);var A=this[_.type](_);return this.sourceNode.shift(),A},Program:function(_){this.options.blockParams.unshift(_.blockParams);for(var A=_.body,C=A.length,E=0;E<C;E++)this.accept(A[E]);return this.options.blockParams.shift(),this.isSimple=C===1,this.blockParams=_.blockParams?_.blockParams.length:0,this},BlockStatement:function(_){p(_);var A=_.program,C=_.inverse;A=A&&this.compileProgram(A),C=C&&this.compileProgram(C);var E=this.classifySexpr(_);E==="helper"?this.helperSexpr(_,A,C):E==="simple"?(this.simpleSexpr(_),this.opcode("pushProgram",A),this.opcode("pushProgram",C),this.opcode("emptyHash"),this.opcode("blockValue",_.path.original)):(this.ambiguousSexpr(_,A,C),this.opcode("pushProgram",A),this.opcode("pushProgram",C),this.opcode("emptyHash"),this.opcode("ambiguousBlockValue")),this.opcode("append")},DecoratorBlock:function(_){var A=_.program&&this.compileProgram(_.program),C=this.setupFullMustacheParams(_,A,void 0),E=_.path;this.useDecorators=!0,this.opcode("registerDecorator",C.length,E.original)},PartialStatement:function(_){this.usePartial=!0;var A=_.program;A&&(A=this.compileProgram(_.program));var C=_.params;if(C.length>1)throw new i.default("Unsupported number of partial arguments: "+C.length,_);C.length||(this.options.explicitPartialContext?this.opcode("pushLiteral","undefined"):C.push({type:"PathExpression",parts:[],depth:0}));var E=_.name.original,S=_.name.type==="SubExpression";S&&this.accept(_.name),this.setupFullMustacheParams(_,A,void 0,!0);var b=_.indent||"";this.options.preventIndent&&b&&(this.opcode("appendContent",b),b=""),this.opcode("invokePartial",S,E,b),this.opcode("append")},PartialBlockStatement:function(_){this.PartialStatement(_)},MustacheStatement:function(_){this.SubExpression(_),_.escaped&&!this.options.noEscape?this.opcode("appendEscaped"):this.opcode("append")},Decorator:function(_){this.DecoratorBlock(_)},ContentStatement:function(_){_.value&&this.opcode("appendContent",_.value)},CommentStatement:function(){},SubExpression:function(_){p(_);var A=this.classifySexpr(_);A==="simple"?this.simpleSexpr(_):A==="helper"?this.helperSexpr(_):this.ambiguousSexpr(_)},ambiguousSexpr:function(_,A,C){var E=_.path,S=E.parts[0],b=A!=null||C!=null;this.opcode("getContext",E.depth),this.opcode("pushProgram",A),this.opcode("pushProgram",C),E.strict=!0,this.accept(E),this.opcode("invokeAmbiguous",S,b)},simpleSexpr:function(_){var A=_.path;A.strict=!0,this.accept(A),this.opcode("resolvePossibleLambda")},helperSexpr:function(_,A,C){var E=this.setupFullMustacheParams(_,A,C),S=_.path,b=S.parts[0];if(this.options.knownHelpers[b])this.opcode("invokeKnownHelper",E.length,b);else{if(this.options.knownHelpersOnly)throw new i.default("You specified knownHelpersOnly, but used the unknown helper "+b,_);S.strict=!0,S.falsy=!0,this.accept(S),this.opcode("invokeHelper",E.length,S.original,f.default.helpers.simpleId(S))}},PathExpression:function(_){this.addDepth(_.depth),this.opcode("getContext",_.depth);var A=_.parts[0],C=f.default.helpers.scopedId(_),E=!_.depth&&!C&&this.blockParamIndex(A);E?this.opcode("lookupBlockParam",E,_.parts):A?_.data?(this.options.data=!0,this.opcode("lookupData",_.depth,_.parts,_.strict)):this.opcode("lookupOnContext",_.parts,_.falsy,_.strict,C):this.opcode("pushContext")},StringLiteral:function(_){this.opcode("pushString",_.value)},NumberLiteral:function(_){this.opcode("pushLiteral",_.value)},BooleanLiteral:function(_){this.opcode("pushLiteral",_.value)},UndefinedLiteral:function(){this.opcode("pushLiteral","undefined")},NullLiteral:function(){this.opcode("pushLiteral","null")},Hash:function(_){var A=_.pairs,C=0,E=A.length;for(this.opcode("pushHash");C<E;C++)this.pushParam(A[C].value);for(;C--;)this.opcode("assignToHash",A[C].key);this.opcode("popHash")},opcode:function(_){this.opcodes.push({opcode:_,args:v.call(arguments,1),loc:this.sourceNode[0].loc})},addDepth:function(_){_&&(this.useDepths=!0)},classifySexpr:function(_){var A=f.default.helpers.simpleId(_.path),C=A&&!!this.blockParamIndex(_.path.parts[0]),E=!C&&f.default.helpers.helperExpression(_),S=!C&&(E||A);if(S&&!E){var b=_.path.parts[0],x=this.options;x.knownHelpers[b]?E=!0:x.knownHelpersOnly&&(S=!1)}return E?"helper":S?"ambiguous":"simple"},pushParams:function(_){for(var A=0,C=_.length;A<C;A++)this.pushParam(_[A])},pushParam:function(_){var A=_.value!=null?_.value:_.original||"";if(this.stringParams)A.replace&&(A=A.replace(/^(\.?\.\/)*/g,"").replace(/\//g,".")),_.depth&&this.addDepth(_.depth),this.opcode("getContext",_.depth||0),this.opcode("pushStringParam",A,_.type),_.type==="SubExpression"&&this.accept(_);else{if(this.trackIds){var C=void 0;if(!_.parts||f.default.helpers.scopedId(_)||_.depth||(C=this.blockParamIndex(_.parts[0])),C){var E=_.parts.slice(1).join(".");this.opcode("pushId","BlockParam",C,E)}else A=_.original||A,A.replace&&(A=A.replace(/^this(?:\.|$)/,"").replace(/^\.\//,"").replace(/^\.$/,"")),this.opcode("pushId",_.type,A)}this.accept(_)}},setupFullMustacheParams:function(_,A,C,E){var S=_.params;return this.pushParams(S),this.opcode("pushProgram",A),this.opcode("pushProgram",C),_.hash?this.accept(_.hash):this.opcode("emptyHash",E),S},blockParamIndex:function(_){for(var A=0,C=this.options.blockParams.length;A<C;A++){var E=this.options.blockParams[A],S=E&&m.indexOf(E,_);if(E&&S>=0)return[A,S]}}}},function(y,o,g){"use strict";function r(f){this.value=f}function t(){}function l(f,v,_,A){var C=v.popStack(),E=0,S=_.length;for(f&&S--;E<S;E++)C=v.nameLookup(C,_[E],A);return f?[v.aliasable("container.strict"),"(",C,", ",v.quotedString(_[E]),", ",JSON.stringify(v.source.currentLocation)," )"]:C}var c=g(13).default,p=g(1).default;o.__esModule=!0;var a=g(4),u=g(6),h=p(u),i=g(5),m=g(53),d=p(m);t.prototype={nameLookup:function(f,v){return this.internalNameLookup(f,v)},depthedLookup:function(f){return[this.aliasable("container.lookup"),"(depths, ",JSON.stringify(f),")"]},compilerInfo:function(){var f=a.COMPILER_REVISION,v=a.REVISION_CHANGES[f];return[f,v]},appendToBuffer:function(f,v,_){return i.isArray(f)||(f=[f]),f=this.source.wrap(f,v),this.environment.isSimple?["return ",f,";"]:_?["buffer += ",f,";"]:(f.appendToBuffer=!0,f)},initializeBuffer:function(){return this.quotedString("")},internalNameLookup:function(f,v){return this.lookupPropertyFunctionIsUsed=!0,["lookupProperty(",f,",",JSON.stringify(v),")"]},lookupPropertyFunctionIsUsed:!1,compile:function(f,v,_,A){this.environment=f,this.options=v,this.stringParams=this.options.stringParams,this.trackIds=this.options.trackIds,this.precompile=!A,this.name=this.environment.name,this.isChild=!!_,this.context=_||{decorators:[],programs:[],environments:[]},this.preamble(),this.stackSlot=0,this.stackVars=[],this.aliases={},this.registers={list:[]},this.hashes=[],this.compileStack=[],this.inlineStack=[],this.blockParams=[],this.compileChildren(f,v),this.useDepths=this.useDepths||f.useDepths||f.useDecorators||this.options.compat,this.useBlockParams=this.useBlockParams||f.useBlockParams;var C=f.opcodes,E=void 0,S=void 0,b=void 0,x=void 0;for(b=0,x=C.length;b<x;b++)E=C[b],this.source.currentLocation=E.loc,S=S||E.loc,this[E.opcode].apply(this,E.args);if(this.source.currentLocation=S,this.pushSource(""),this.stackSlot||this.inlineStack.length||this.compileStack.length)throw new h.default("Compile completed with content left on stack");this.decorators.isEmpty()?this.decorators=void 0:(this.useDecorators=!0,this.decorators.prepend(["var decorators = container.decorators, ",this.lookupPropertyFunctionVarDeclaration(),`;
`]),this.decorators.push("return fn;"),A?this.decorators=Function.apply(this,["fn","props","container","depth0","data","blockParams","depths",this.decorators.merge()]):(this.decorators.prepend(`function(fn, props, container, depth0, data, blockParams, depths) {
`),this.decorators.push(`}
`),this.decorators=this.decorators.merge()));var D=this.createFunctionContext(A);if(this.isChild)return D;var N={compiler:this.compilerInfo(),main:D};this.decorators&&(N.main_d=this.decorators,N.useDecorators=!0);var I=this.context,w=I.programs,O=I.decorators;for(b=0,x=w.length;b<x;b++)w[b]&&(N[b]=w[b],O[b]&&(N[b+"_d"]=O[b],N.useDecorators=!0));return this.environment.usePartial&&(N.usePartial=!0),this.options.data&&(N.useData=!0),this.useDepths&&(N.useDepths=!0),this.useBlockParams&&(N.useBlockParams=!0),this.options.compat&&(N.compat=!0),A?N.compilerOptions=this.options:(N.compiler=JSON.stringify(N.compiler),this.source.currentLocation={start:{line:1,column:0}},N=this.objectLiteral(N),v.srcName?(N=N.toStringWithSourceMap({file:v.destName}),N.map=N.map&&N.map.toString()):N=N.toString()),N},preamble:function(){this.lastContext=0,this.source=new d.default(this.options.srcName),this.decorators=new d.default(this.options.srcName)},createFunctionContext:function(f){var v=this,_="",A=this.stackVars.concat(this.registers.list);A.length>0&&(_+=", "+A.join(", "));var C=0;c(this.aliases).forEach(function(b){var x=v.aliases[b];x.children&&x.referenceCount>1&&(_+=", alias"+ ++C+"="+b,x.children[0]="alias"+C)}),this.lookupPropertyFunctionIsUsed&&(_+=", "+this.lookupPropertyFunctionVarDeclaration());var E=["container","depth0","helpers","partials","data"];(this.useBlockParams||this.useDepths)&&E.push("blockParams"),this.useDepths&&E.push("depths");var S=this.mergeSource(_);return f?(E.push(S),Function.apply(this,E)):this.source.wrap(["function(",E.join(","),`) {
  `,S,"}"])},mergeSource:function(f){var v=this.environment.isSimple,_=!this.forceBuffer,A=void 0,C=void 0,E=void 0,S=void 0;return this.source.each(function(b){b.appendToBuffer?(E?b.prepend("  + "):E=b,S=b):(E&&(C?E.prepend("buffer += "):A=!0,S.add(";"),E=S=void 0),C=!0,v||(_=!1))}),_?E?(E.prepend("return "),S.add(";")):C||this.source.push('return "";'):(f+=", buffer = "+(A?"":this.initializeBuffer()),E?(E.prepend("return buffer + "),S.add(";")):this.source.push("return buffer;")),f&&this.source.prepend("var "+f.substring(2)+(A?"":`;
`)),this.source.merge()},lookupPropertyFunctionVarDeclaration:function(){return`
      lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    }
    `.trim()},blockValue:function(f){var v=this.aliasable("container.hooks.blockHelperMissing"),_=[this.contextName(0)];this.setupHelperArgs(f,0,_);var A=this.popStack();_.splice(1,0,A),this.push(this.source.functionCall(v,"call",_))},ambiguousBlockValue:function(){var f=this.aliasable("container.hooks.blockHelperMissing"),v=[this.contextName(0)];this.setupHelperArgs("",0,v,!0),this.flushInline();var _=this.topStack();v.splice(1,0,_),this.pushSource(["if (!",this.lastHelper,") { ",_," = ",this.source.functionCall(f,"call",v),"}"])},appendContent:function(f){this.pendingContent?f=this.pendingContent+f:this.pendingLocation=this.source.currentLocation,this.pendingContent=f},append:function(){if(this.isInline())this.replaceStack(function(v){return[" != null ? ",v,' : ""']}),this.pushSource(this.appendToBuffer(this.popStack()));else{var f=this.popStack();this.pushSource(["if (",f," != null) { ",this.appendToBuffer(f,void 0,!0)," }"]),this.environment.isSimple&&this.pushSource(["else { ",this.appendToBuffer("''",void 0,!0)," }"])}},appendEscaped:function(){this.pushSource(this.appendToBuffer([this.aliasable("container.escapeExpression"),"(",this.popStack(),")"]))},getContext:function(f){this.lastContext=f},pushContext:function(){this.pushStackLiteral(this.contextName(this.lastContext))},lookupOnContext:function(f,v,_,A){var C=0;A||!this.options.compat||this.lastContext?this.pushContext():this.push(this.depthedLookup(f[C++])),this.resolvePath("context",f,C,v,_)},lookupBlockParam:function(f,v){this.useBlockParams=!0,this.push(["blockParams[",f[0],"][",f[1],"]"]),this.resolvePath("context",v,1)},lookupData:function(f,v,_){f?this.pushStackLiteral("container.data(data, "+f+")"):this.pushStackLiteral("data"),this.resolvePath("data",v,0,!0,_)},resolvePath:function(f,v,_,A,C){var E=this;if(this.options.strict||this.options.assumeObjects)return void this.push(l(this.options.strict&&C,this,v,f));for(var S=v.length;_<S;_++)this.replaceStack(function(b){var x=E.nameLookup(b,v[_],f);return A?[" && ",x]:[" != null ? ",x," : ",b]})},resolvePossibleLambda:function(){this.push([this.aliasable("container.lambda"),"(",this.popStack(),", ",this.contextName(0),")"])},pushStringParam:function(f,v){this.pushContext(),this.pushString(v),v!=="SubExpression"&&(typeof f=="string"?this.pushString(f):this.pushStackLiteral(f))},emptyHash:function(f){this.trackIds&&this.push("{}"),this.stringParams&&(this.push("{}"),this.push("{}")),this.pushStackLiteral(f?"undefined":"{}")},pushHash:function(){this.hash&&this.hashes.push(this.hash),this.hash={values:{},types:[],contexts:[],ids:[]}},popHash:function(){var f=this.hash;this.hash=this.hashes.pop(),this.trackIds&&this.push(this.objectLiteral(f.ids)),this.stringParams&&(this.push(this.objectLiteral(f.contexts)),this.push(this.objectLiteral(f.types))),this.push(this.objectLiteral(f.values))},pushString:function(f){this.pushStackLiteral(this.quotedString(f))},pushLiteral:function(f){this.pushStackLiteral(f)},pushProgram:function(f){f!=null?this.pushStackLiteral(this.programExpression(f)):this.pushStackLiteral(null)},registerDecorator:function(f,v){var _=this.nameLookup("decorators",v,"decorator"),A=this.setupHelperArgs(v,f);this.decorators.push(["fn = ",this.decorators.functionCall(_,"",["fn","props","container",A])," || fn;"])},invokeHelper:function(f,v,_){var A=this.popStack(),C=this.setupHelper(f,v),E=[];_&&E.push(C.name),E.push(A),this.options.strict||E.push(this.aliasable("container.hooks.helperMissing"));var S=["(",this.itemsSeparatedBy(E,"||"),")"],b=this.source.functionCall(S,"call",C.callParams);this.push(b)},itemsSeparatedBy:function(f,v){var _=[];_.push(f[0]);for(var A=1;A<f.length;A++)_.push(v,f[A]);return _},invokeKnownHelper:function(f,v){var _=this.setupHelper(f,v);this.push(this.source.functionCall(_.name,"call",_.callParams))},invokeAmbiguous:function(f,v){this.useRegister("helper");var _=this.popStack();this.emptyHash();var A=this.setupHelper(0,f,v),C=this.lastHelper=this.nameLookup("helpers",f,"helper"),E=["(","(helper = ",C," || ",_,")"];this.options.strict||(E[0]="(helper = ",E.push(" != null ? helper : ",this.aliasable("container.hooks.helperMissing"))),this.push(["(",E,A.paramsInit?["),(",A.paramsInit]:[],"),","(typeof helper === ",this.aliasable('"function"')," ? ",this.source.functionCall("helper","call",A.callParams)," : helper))"])},invokePartial:function(f,v,_){var A=[],C=this.setupParams(v,1,A);f&&(v=this.popStack(),delete C.name),_&&(C.indent=JSON.stringify(_)),C.helpers="helpers",C.partials="partials",C.decorators="container.decorators",f?A.unshift(v):A.unshift(this.nameLookup("partials",v,"partial")),this.options.compat&&(C.depths="depths"),C=this.objectLiteral(C),A.push(C),this.push(this.source.functionCall("container.invokePartial","",A))},assignToHash:function(f){var v=this.popStack(),_=void 0,A=void 0,C=void 0;this.trackIds&&(C=this.popStack()),this.stringParams&&(A=this.popStack(),_=this.popStack());var E=this.hash;_&&(E.contexts[f]=_),A&&(E.types[f]=A),C&&(E.ids[f]=C),E.values[f]=v},pushId:function(f,v,_){f==="BlockParam"?this.pushStackLiteral("blockParams["+v[0]+"].path["+v[1]+"]"+(_?" + "+JSON.stringify("."+_):"")):f==="PathExpression"?this.pushString(v):f==="SubExpression"?this.pushStackLiteral("true"):this.pushStackLiteral("null")},compiler:t,compileChildren:function(f,v){for(var _=f.children,A=void 0,C=void 0,E=0,S=_.length;E<S;E++){A=_[E],C=new this.compiler;var b=this.matchExistingProgram(A);if(b==null){this.context.programs.push("");var x=this.context.programs.length;A.index=x,A.name="program"+x,this.context.programs[x]=C.compile(A,v,this.context,!this.precompile),this.context.decorators[x]=C.decorators,this.context.environments[x]=A,this.useDepths=this.useDepths||C.useDepths,this.useBlockParams=this.useBlockParams||C.useBlockParams,A.useDepths=this.useDepths,A.useBlockParams=this.useBlockParams}else A.index=b.index,A.name="program"+b.index,this.useDepths=this.useDepths||b.useDepths,this.useBlockParams=this.useBlockParams||b.useBlockParams}},matchExistingProgram:function(f){for(var v=0,_=this.context.environments.length;v<_;v++){var A=this.context.environments[v];if(A&&A.equals(f))return A}},programExpression:function(f){var v=this.environment.children[f],_=[v.index,"data",v.blockParams];return(this.useBlockParams||this.useDepths)&&_.push("blockParams"),this.useDepths&&_.push("depths"),"container.program("+_.join(", ")+")"},useRegister:function(f){this.registers[f]||(this.registers[f]=!0,this.registers.list.push(f))},push:function(f){return f instanceof r||(f=this.source.wrap(f)),this.inlineStack.push(f),f},pushStackLiteral:function(f){this.push(new r(f))},pushSource:function(f){this.pendingContent&&(this.source.push(this.appendToBuffer(this.source.quotedString(this.pendingContent),this.pendingLocation)),this.pendingContent=void 0),f&&this.source.push(f)},replaceStack:function(f){var v=["("],_=void 0,A=void 0,C=void 0;if(!this.isInline())throw new h.default("replaceStack on non-inline");var E=this.popStack(!0);if(E instanceof r)_=[E.value],v=["(",_],C=!0;else{A=!0;var S=this.incrStack();v=["((",this.push(S)," = ",E,")"],_=this.topStack()}var b=f.call(this,_);C||this.popStack(),A&&this.stackSlot--,this.push(v.concat(b,")"))},incrStack:function(){return this.stackSlot++,this.stackSlot>this.stackVars.length&&this.stackVars.push("stack"+this.stackSlot),this.topStackName()},topStackName:function(){return"stack"+this.stackSlot},flushInline:function(){var f=this.inlineStack;this.inlineStack=[];for(var v=0,_=f.length;v<_;v++){var A=f[v];if(A instanceof r)this.compileStack.push(A);else{var C=this.incrStack();this.pushSource([C," = ",A,";"]),this.compileStack.push(C)}}},isInline:function(){return this.inlineStack.length},popStack:function(f){var v=this.isInline(),_=(v?this.inlineStack:this.compileStack).pop();if(!f&&_ instanceof r)return _.value;if(!v){if(!this.stackSlot)throw new h.default("Invalid stack pop");this.stackSlot--}return _},topStack:function(){var f=this.isInline()?this.inlineStack:this.compileStack,v=f[f.length-1];return v instanceof r?v.value:v},contextName:function(f){return this.useDepths&&f?"depths["+f+"]":"depth"+f},quotedString:function(f){return this.source.quotedString(f)},objectLiteral:function(f){return this.source.objectLiteral(f)},aliasable:function(f){var v=this.aliases[f];return v?(v.referenceCount++,v):(v=this.aliases[f]=this.source.wrap(f),v.aliasable=!0,v.referenceCount=1,v)},setupHelper:function(f,v,_){var A=[],C=this.setupHelperArgs(v,f,A,_),E=this.nameLookup("helpers",v,"helper"),S=this.aliasable(this.contextName(0)+" != null ? "+this.contextName(0)+" : (container.nullContext || {})");return{params:A,paramsInit:C,name:E,callParams:[S].concat(A)}},setupParams:function(f,v,_){var A={},C=[],E=[],S=[],b=!_,x=void 0;b&&(_=[]),A.name=this.quotedString(f),A.hash=this.popStack(),this.trackIds&&(A.hashIds=this.popStack()),this.stringParams&&(A.hashTypes=this.popStack(),A.hashContexts=this.popStack());var D=this.popStack(),N=this.popStack();(N||D)&&(A.fn=N||"container.noop",A.inverse=D||"container.noop");for(var I=v;I--;)x=this.popStack(),_[I]=x,this.trackIds&&(S[I]=this.popStack()),this.stringParams&&(E[I]=this.popStack(),C[I]=this.popStack());return b&&(A.args=this.source.generateArray(_)),this.trackIds&&(A.ids=this.source.generateArray(S)),this.stringParams&&(A.types=this.source.generateArray(E),A.contexts=this.source.generateArray(C)),this.options.data&&(A.data="data"),this.useBlockParams&&(A.blockParams="blockParams"),A},setupHelperArgs:function(f,v,_,A){var C=this.setupParams(f,v,_);return C.loc=JSON.stringify(this.source.currentLocation),C=this.objectLiteral(C),A?(this.useRegister("options"),_.push("options"),["options=",C]):_?(_.push(C),""):C}},function(){for(var f="break else new var case finally return void catch for switch while continue function this with default if throw delete in try do instanceof typeof abstract enum int short boolean export interface static byte extends long super char final native synchronized class float package throws const goto private transient debugger implements protected volatile double import public let yield await null true false".split(" "),v=t.RESERVED_WORDS={},_=0,A=f.length;_<A;_++)v[f[_]]=!0}(),t.isValidJavaScriptVariableName=function(f){return!t.RESERVED_WORDS[f]&&/^[a-zA-Z_$][0-9a-zA-Z_$]*$/.test(f)},o.default=t,y.exports=o.default},function(y,o,g){"use strict";function r(a,u,h){if(c.isArray(a)){for(var i=[],m=0,d=a.length;m<d;m++)i.push(u.wrap(a[m],h));return i}return typeof a=="boolean"||typeof a=="number"?a+"":a}function t(a){this.srcFile=a,this.source=[]}var l=g(13).default;o.__esModule=!0;var c=g(5),p=void 0;try{}catch(a){}p||(p=function(a,u,h,i){this.src="",i&&this.add(i)},p.prototype={add:function(a){c.isArray(a)&&(a=a.join("")),this.src+=a},prepend:function(a){c.isArray(a)&&(a=a.join("")),this.src=a+this.src},toStringWithSourceMap:function(){return{code:this.toString()}},toString:function(){return this.src}}),t.prototype={isEmpty:function(){return!this.source.length},prepend:function(a,u){this.source.unshift(this.wrap(a,u))},push:function(a,u){this.source.push(this.wrap(a,u))},merge:function(){var a=this.empty();return this.each(function(u){a.add(["  ",u,`
`])}),a},each:function(a){for(var u=0,h=this.source.length;u<h;u++)a(this.source[u])},empty:function(){var a=this.currentLocation||{start:{}};return new p(a.start.line,a.start.column,this.srcFile)},wrap:function(a){var u=arguments.length<=1||arguments[1]===void 0?this.currentLocation||{start:{}}:arguments[1];return a instanceof p?a:(a=r(a,this,u),new p(u.start.line,u.start.column,this.srcFile,a))},functionCall:function(a,u,h){return h=this.generateList(h),this.wrap([a,u?"."+u+"(":"(",h,")"])},quotedString:function(a){return'"'+(a+"").replace(/\\/g,"\\\\").replace(/"/g,'\\"').replace(/\n/g,"\\n").replace(/\r/g,"\\r").replace(/\u2028/g,"\\u2028").replace(/\u2029/g,"\\u2029")+'"'},objectLiteral:function(a){var u=this,h=[];l(a).forEach(function(m){var d=r(a[m],u);d!=="undefined"&&h.push([u.quotedString(m),":",d])});var i=this.generateList(h);return i.prepend("{"),i.add("}"),i},generateList:function(a){for(var u=this.empty(),h=0,i=a.length;h<i;h++)h&&u.add(","),u.add(r(a[h],this));return u},generateArray:function(a){var u=this.generateList(a);return u.prepend("["),u.add("]"),u}},o.default=t,y.exports=o.default}])})},6601:(P,y,o)=>{var g;/*!
* Sizzle CSS Selector Engine v2.3.6
* https://sizzlejs.com/
*
* Copyright JS Foundation and other contributors
* Released under the MIT license
* https://js.foundation/
*
* Date: 2021-02-16
*/(function(r){var t,l,c,p,a,u,h,i,m,d,f,v,_,A,C,E,S,b,x,D="sizzle"+1*new Date,N=r.document,I=0,w=0,O=et(),B=et(),U=et(),L=et(),F=function(q,H){return q===H&&(f=!0),0},Y={}.hasOwnProperty,z=[],G=z.pop,W=z.push,te=z.push,oe=z.slice,ge=function(q,H){for(var K=0,ne=q.length;K<ne;K++)if(q[K]===H)return K;return-1},Q="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",Te="[\\x20\\t\\r\\n\\f]",Pe="(?:\\\\[\\da-fA-F]{1,6}"+Te+"?|\\\\[^\\r\\n\\f]|[\\w-]|[^\0-\\x7f])+",Ge="\\["+Te+"*("+Pe+")(?:"+Te+"*([*^$|!~]?=)"+Te+`*(?:'((?:\\\\.|[^\\\\'])*)'|"((?:\\\\.|[^\\\\"])*)"|(`+Pe+"))|)"+Te+"*\\]",mn=":("+Pe+`)(?:\\((('((?:\\\\.|[^\\\\'])*)'|"((?:\\\\.|[^\\\\"])*)")|((?:\\\\.|[^\\\\()[\\]]|`+Ge+")*)|.*)\\)|)",Rn=new RegExp(Te+"+","g"),Dn=new RegExp("^"+Te+"+|((?:^|[^\\\\])(?:\\\\.)*)"+Te+"+$","g"),Nn=new RegExp("^"+Te+"*,"+Te+"*"),jn=new RegExp("^"+Te+"*([>+~]|"+Te+")"+Te+"*"),je=new RegExp(Te+"|>"),Ln=new RegExp(mn),Ze=new RegExp("^"+Pe+"$"),nn={ID:new RegExp("^#("+Pe+")"),CLASS:new RegExp("^\\.("+Pe+")"),TAG:new RegExp("^("+Pe+"|[*])"),ATTR:new RegExp("^"+Ge),PSEUDO:new RegExp("^"+mn),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+Te+"*(even|odd|(([+-]|)(\\d*)n|)"+Te+"*(?:([+-]|)"+Te+"*(\\d+)|))"+Te+"*\\)|)","i"),bool:new RegExp("^(?:"+Q+")$","i"),needsContext:new RegExp("^"+Te+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+Te+"*((?:-\\d)?\\d*)"+Te+"*\\)|)(?=[^-]|$)","i")},Yn=/HTML$/i,kt=/^(?:input|select|textarea|button)$/i,bn=/^h\d$/i,Wn=/^[^{]+\{\s*\[native \w/,mt=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,On=/[+~]/,un=new RegExp("\\\\[\\da-fA-F]{1,6}"+Te+"?|\\\\([^\\r\\n\\f])","g"),gn=function(q,H){var K="0x"+q.slice(1)-65536;return H||(K<0?String.fromCharCode(K+65536):String.fromCharCode(K>>10|55296,K&1023|56320))},xt=/([\0-\x1f\x7f]|^-?\d)|^-$|[^\0-\x1f\x7f-\uFFFF\w-]/g,er=function(q,H){return H?q==="\0"?"\uFFFD":q.slice(0,-1)+"\\"+q.charCodeAt(q.length-1).toString(16)+" ":"\\"+q},Qn=function(){v()},hr=Ne(function(q){return q.disabled===!0&&q.nodeName.toLowerCase()==="fieldset"},{dir:"parentNode",next:"legend"});try{te.apply(z=oe.call(N.childNodes),N.childNodes),z[N.childNodes.length].nodeType}catch(q){te={apply:z.length?function(H,K){W.apply(H,oe.call(K))}:function(H,K){for(var ne=H.length,$=0;H[ne++]=K[$++];);H.length=ne-1}}}function tn(q,H,K,ne){var $,re,de,Ae,be,ke,Ie,Me=H&&H.ownerDocument,$e=H?H.nodeType:9;if(K=K||[],typeof q!="string"||!q||$e!==1&&$e!==9&&$e!==11)return K;if(!ne&&(v(H),H=H||_,C)){if($e!==11&&(be=mt.exec(q)))if($=be[1]){if($e===9)if(de=H.getElementById($)){if(de.id===$)return K.push(de),K}else return K;else if(Me&&(de=Me.getElementById($))&&x(H,de)&&de.id===$)return K.push(de),K}else{if(be[2])return te.apply(K,H.getElementsByTagName(q)),K;if(($=be[3])&&l.getElementsByClassName&&H.getElementsByClassName)return te.apply(K,H.getElementsByClassName($)),K}if(l.qsa&&!L[q+" "]&&(!E||!E.test(q))&&($e!==1||H.nodeName.toLowerCase()!=="object")){if(Ie=q,Me=H,$e===1&&(je.test(q)||jn.test(q))){for(Me=On.test(q)&&Ee(H.parentNode)||H,(Me!==H||!l.scope)&&((Ae=H.getAttribute("id"))?Ae=Ae.replace(xt,er):H.setAttribute("id",Ae=D)),ke=u(q),re=ke.length;re--;)ke[re]=(Ae?"#"+Ae:":scope")+" "+Ue(ke[re]);Ie=ke.join(",")}try{return te.apply(K,Me.querySelectorAll(Ie)),K}catch(ln){L(q,!0)}finally{Ae===D&&H.removeAttribute("id")}}}return i(q.replace(Dn,"$1"),H,K,ne)}function et(){var q=[];function H(K,ne){return q.push(K+" ")>c.cacheLength&&delete H[q.shift()],H[K+" "]=ne}return H}function qn(q){return q[D]=!0,q}function ce(q){var H=_.createElement("fieldset");try{return!!q(H)}catch(K){return!1}finally{H.parentNode&&H.parentNode.removeChild(H),H=null}}function J(q,H){for(var K=q.split("|"),ne=K.length;ne--;)c.attrHandle[K[ne]]=H}function ue(q,H){var K=H&&q,ne=K&&q.nodeType===1&&H.nodeType===1&&q.sourceIndex-H.sourceIndex;if(ne)return ne;if(K){for(;K=K.nextSibling;)if(K===H)return-1}return q?1:-1}function Ce(q){return function(H){var K=H.nodeName.toLowerCase();return K==="input"&&H.type===q}}function ie(q){return function(H){var K=H.nodeName.toLowerCase();return(K==="input"||K==="button")&&H.type===q}}function ye(q){return function(H){return"form"in H?H.parentNode&&H.disabled===!1?"label"in H?"label"in H.parentNode?H.parentNode.disabled===q:H.disabled===q:H.isDisabled===q||H.isDisabled!==!q&&hr(H)===q:H.disabled===q:"label"in H?H.disabled===q:!1}}function pe(q){return qn(function(H){return H=+H,qn(function(K,ne){for(var $,re=q([],K.length,H),de=re.length;de--;)K[$=re[de]]&&(K[$]=!(ne[$]=K[$]))})})}function Ee(q){return q&&typeof q.getElementsByTagName!="undefined"&&q}l=tn.support={},a=tn.isXML=function(q){var H=q&&q.namespaceURI,K=q&&(q.ownerDocument||q).documentElement;return!Yn.test(H||K&&K.nodeName||"HTML")},v=tn.setDocument=function(q){var H,K,ne=q?q.ownerDocument||q:N;return ne==_||ne.nodeType!==9||!ne.documentElement||(_=ne,A=_.documentElement,C=!a(_),N!=_&&(K=_.defaultView)&&K.top!==K&&(K.addEventListener?K.addEventListener("unload",Qn,!1):K.attachEvent&&K.attachEvent("onunload",Qn)),l.scope=ce(function($){return A.appendChild($).appendChild(_.createElement("div")),typeof $.querySelectorAll!="undefined"&&!$.querySelectorAll(":scope fieldset div").length}),l.attributes=ce(function($){return $.className="i",!$.getAttribute("className")}),l.getElementsByTagName=ce(function($){return $.appendChild(_.createComment("")),!$.getElementsByTagName("*").length}),l.getElementsByClassName=Wn.test(_.getElementsByClassName),l.getById=ce(function($){return A.appendChild($).id=D,!_.getElementsByName||!_.getElementsByName(D).length}),l.getById?(c.filter.ID=function($){var re=$.replace(un,gn);return function(de){return de.getAttribute("id")===re}},c.find.ID=function($,re){if(typeof re.getElementById!="undefined"&&C){var de=re.getElementById($);return de?[de]:[]}}):(c.filter.ID=function($){var re=$.replace(un,gn);return function(de){var Ae=typeof de.getAttributeNode!="undefined"&&de.getAttributeNode("id");return Ae&&Ae.value===re}},c.find.ID=function($,re){if(typeof re.getElementById!="undefined"&&C){var de,Ae,be,ke=re.getElementById($);if(ke){if(de=ke.getAttributeNode("id"),de&&de.value===$)return[ke];for(be=re.getElementsByName($),Ae=0;ke=be[Ae++];)if(de=ke.getAttributeNode("id"),de&&de.value===$)return[ke]}return[]}}),c.find.TAG=l.getElementsByTagName?function($,re){if(typeof re.getElementsByTagName!="undefined")return re.getElementsByTagName($);if(l.qsa)return re.querySelectorAll($)}:function($,re){var de,Ae=[],be=0,ke=re.getElementsByTagName($);if($==="*"){for(;de=ke[be++];)de.nodeType===1&&Ae.push(de);return Ae}return ke},c.find.CLASS=l.getElementsByClassName&&function($,re){if(typeof re.getElementsByClassName!="undefined"&&C)return re.getElementsByClassName($)},S=[],E=[],(l.qsa=Wn.test(_.querySelectorAll))&&(ce(function($){var re;A.appendChild($).innerHTML="<a id='"+D+"'></a><select id='"+D+"-\r\\' msallowcapture=''><option selected=''></option></select>",$.querySelectorAll("[msallowcapture^='']").length&&E.push("[*^$]="+Te+`*(?:''|"")`),$.querySelectorAll("[selected]").length||E.push("\\["+Te+"*(?:value|"+Q+")"),$.querySelectorAll("[id~="+D+"-]").length||E.push("~="),re=_.createElement("input"),re.setAttribute("name",""),$.appendChild(re),$.querySelectorAll("[name='']").length||E.push("\\["+Te+"*name"+Te+"*="+Te+`*(?:''|"")`),$.querySelectorAll(":checked").length||E.push(":checked"),$.querySelectorAll("a#"+D+"+*").length||E.push(".#.+[+~]"),$.querySelectorAll("\\\f"),E.push("[\\r\\n\\f]")}),ce(function($){$.innerHTML="<a href='' disabled='disabled'></a><select disabled='disabled'><option/></select>";var re=_.createElement("input");re.setAttribute("type","hidden"),$.appendChild(re).setAttribute("name","D"),$.querySelectorAll("[name=d]").length&&E.push("name"+Te+"*[*^$|!~]?="),$.querySelectorAll(":enabled").length!==2&&E.push(":enabled",":disabled"),A.appendChild($).disabled=!0,$.querySelectorAll(":disabled").length!==2&&E.push(":enabled",":disabled"),$.querySelectorAll("*,:x"),E.push(",.*:")})),(l.matchesSelector=Wn.test(b=A.matches||A.webkitMatchesSelector||A.mozMatchesSelector||A.oMatchesSelector||A.msMatchesSelector))&&ce(function($){l.disconnectedMatch=b.call($,"*"),b.call($,"[s!='']:x"),S.push("!=",mn)}),E=E.length&&new RegExp(E.join("|")),S=S.length&&new RegExp(S.join("|")),H=Wn.test(A.compareDocumentPosition),x=H||Wn.test(A.contains)?function($,re){var de=$.nodeType===9?$.documentElement:$,Ae=re&&re.parentNode;return $===Ae||!!(Ae&&Ae.nodeType===1&&(de.contains?de.contains(Ae):$.compareDocumentPosition&&$.compareDocumentPosition(Ae)&16))}:function($,re){if(re){for(;re=re.parentNode;)if(re===$)return!0}return!1},F=H?function($,re){if($===re)return f=!0,0;var de=!$.compareDocumentPosition-!re.compareDocumentPosition;return de||(de=($.ownerDocument||$)==(re.ownerDocument||re)?$.compareDocumentPosition(re):1,de&1||!l.sortDetached&&re.compareDocumentPosition($)===de?$==_||$.ownerDocument==N&&x(N,$)?-1:re==_||re.ownerDocument==N&&x(N,re)?1:d?ge(d,$)-ge(d,re):0:de&4?-1:1)}:function($,re){if($===re)return f=!0,0;var de,Ae=0,be=$.parentNode,ke=re.parentNode,Ie=[$],Me=[re];if(!be||!ke)return $==_?-1:re==_?1:be?-1:ke?1:d?ge(d,$)-ge(d,re):0;if(be===ke)return ue($,re);for(de=$;de=de.parentNode;)Ie.unshift(de);for(de=re;de=de.parentNode;)Me.unshift(de);for(;Ie[Ae]===Me[Ae];)Ae++;return Ae?ue(Ie[Ae],Me[Ae]):Ie[Ae]==N?-1:Me[Ae]==N?1:0}),_},tn.matches=function(q,H){return tn(q,null,null,H)},tn.matchesSelector=function(q,H){if(v(q),l.matchesSelector&&C&&!L[H+" "]&&(!S||!S.test(H))&&(!E||!E.test(H)))try{var K=b.call(q,H);if(K||l.disconnectedMatch||q.document&&q.document.nodeType!==11)return K}catch(ne){L(H,!0)}return tn(H,_,null,[q]).length>0},tn.contains=function(q,H){return(q.ownerDocument||q)!=_&&v(q),x(q,H)},tn.attr=function(q,H){(q.ownerDocument||q)!=_&&v(q);var K=c.attrHandle[H.toLowerCase()],ne=K&&Y.call(c.attrHandle,H.toLowerCase())?K(q,H,!C):void 0;return ne!==void 0?ne:l.attributes||!C?q.getAttribute(H):(ne=q.getAttributeNode(H))&&ne.specified?ne.value:null},tn.escape=function(q){return(q+"").replace(xt,er)},tn.error=function(q){throw new Error("Syntax error, unrecognized expression: "+q)},tn.uniqueSort=function(q){var H,K=[],ne=0,$=0;if(f=!l.detectDuplicates,d=!l.sortStable&&q.slice(0),q.sort(F),f){for(;H=q[$++];)H===q[$]&&(ne=K.push($));for(;ne--;)q.splice(K[ne],1)}return d=null,q},p=tn.getText=function(q){var H,K="",ne=0,$=q.nodeType;if($){if($===1||$===9||$===11){if(typeof q.textContent=="string")return q.textContent;for(q=q.firstChild;q;q=q.nextSibling)K+=p(q)}else if($===3||$===4)return q.nodeValue}else for(;H=q[ne++];)K+=p(H);return K},c=tn.selectors={cacheLength:50,createPseudo:qn,match:nn,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(q){return q[1]=q[1].replace(un,gn),q[3]=(q[3]||q[4]||q[5]||"").replace(un,gn),q[2]==="~="&&(q[3]=" "+q[3]+" "),q.slice(0,4)},CHILD:function(q){return q[1]=q[1].toLowerCase(),q[1].slice(0,3)==="nth"?(q[3]||tn.error(q[0]),q[4]=+(q[4]?q[5]+(q[6]||1):2*(q[3]==="even"||q[3]==="odd")),q[5]=+(q[7]+q[8]||q[3]==="odd")):q[3]&&tn.error(q[0]),q},PSEUDO:function(q){var H,K=!q[6]&&q[2];return nn.CHILD.test(q[0])?null:(q[3]?q[2]=q[4]||q[5]||"":K&&Ln.test(K)&&(H=u(K,!0))&&(H=K.indexOf(")",K.length-H)-K.length)&&(q[0]=q[0].slice(0,H),q[2]=K.slice(0,H)),q.slice(0,3))}},filter:{TAG:function(q){var H=q.replace(un,gn).toLowerCase();return q==="*"?function(){return!0}:function(K){return K.nodeName&&K.nodeName.toLowerCase()===H}},CLASS:function(q){var H=O[q+" "];return H||(H=new RegExp("(^|"+Te+")"+q+"("+Te+"|$)"))&&O(q,function(K){return H.test(typeof K.className=="string"&&K.className||typeof K.getAttribute!="undefined"&&K.getAttribute("class")||"")})},ATTR:function(q,H,K){return function(ne){var $=tn.attr(ne,q);return $==null?H==="!=":H?($+="",H==="="?$===K:H==="!="?$!==K:H==="^="?K&&$.indexOf(K)===0:H==="*="?K&&$.indexOf(K)>-1:H==="$="?K&&$.slice(-K.length)===K:H==="~="?(" "+$.replace(Rn," ")+" ").indexOf(K)>-1:H==="|="?$===K||$.slice(0,K.length+1)===K+"-":!1):!0}},CHILD:function(q,H,K,ne,$){var re=q.slice(0,3)!=="nth",de=q.slice(-4)!=="last",Ae=H==="of-type";return ne===1&&$===0?function(be){return!!be.parentNode}:function(be,ke,Ie){var Me,$e,ln,qe,me,fe,ve=re!==de?"nextSibling":"previousSibling",_e=be.parentNode,Ye=Ae&&be.nodeName.toLowerCase(),Xe=!Ie&&!Ae,Re=!1;if(_e){if(re){for(;ve;){for(qe=be;qe=qe[ve];)if(Ae?qe.nodeName.toLowerCase()===Ye:qe.nodeType===1)return!1;fe=ve=q==="only"&&!fe&&"nextSibling"}return!0}if(fe=[de?_e.firstChild:_e.lastChild],de&&Xe){for(qe=_e,ln=qe[D]||(qe[D]={}),$e=ln[qe.uniqueID]||(ln[qe.uniqueID]={}),Me=$e[q]||[],me=Me[0]===I&&Me[1],Re=me&&Me[2],qe=me&&_e.childNodes[me];qe=++me&&qe&&qe[ve]||(Re=me=0)||fe.pop();)if(qe.nodeType===1&&++Re&&qe===be){$e[q]=[I,me,Re];break}}else if(Xe&&(qe=be,ln=qe[D]||(qe[D]={}),$e=ln[qe.uniqueID]||(ln[qe.uniqueID]={}),Me=$e[q]||[],me=Me[0]===I&&Me[1],Re=me),Re===!1)for(;(qe=++me&&qe&&qe[ve]||(Re=me=0)||fe.pop())&&!((Ae?qe.nodeName.toLowerCase()===Ye:qe.nodeType===1)&&++Re&&(Xe&&(ln=qe[D]||(qe[D]={}),$e=ln[qe.uniqueID]||(ln[qe.uniqueID]={}),$e[q]=[I,Re]),qe===be)););return Re-=$,Re===ne||Re%ne===0&&Re/ne>=0}}},PSEUDO:function(q,H){var K,ne=c.pseudos[q]||c.setFilters[q.toLowerCase()]||tn.error("unsupported pseudo: "+q);return ne[D]?ne(H):ne.length>1?(K=[q,q,"",H],c.setFilters.hasOwnProperty(q.toLowerCase())?qn(function($,re){for(var de,Ae=ne($,H),be=Ae.length;be--;)de=ge($,Ae[be]),$[de]=!(re[de]=Ae[be])}):function($){return ne($,0,K)}):ne}},pseudos:{not:qn(function(q){var H=[],K=[],ne=h(q.replace(Dn,"$1"));return ne[D]?qn(function($,re,de,Ae){for(var be,ke=ne($,null,Ae,[]),Ie=$.length;Ie--;)(be=ke[Ie])&&($[Ie]=!(re[Ie]=be))}):function($,re,de){return H[0]=$,ne(H,null,de,K),H[0]=null,!K.pop()}}),has:qn(function(q){return function(H){return tn(q,H).length>0}}),contains:qn(function(q){return q=q.replace(un,gn),function(H){return(H.textContent||p(H)).indexOf(q)>-1}}),lang:qn(function(q){return Ze.test(q||"")||tn.error("unsupported lang: "+q),q=q.replace(un,gn).toLowerCase(),function(H){var K;do if(K=C?H.lang:H.getAttribute("xml:lang")||H.getAttribute("lang"))return K=K.toLowerCase(),K===q||K.indexOf(q+"-")===0;while((H=H.parentNode)&&H.nodeType===1);return!1}}),target:function(q){var H=r.location&&r.location.hash;return H&&H.slice(1)===q.id},root:function(q){return q===A},focus:function(q){return q===_.activeElement&&(!_.hasFocus||_.hasFocus())&&!!(q.type||q.href||~q.tabIndex)},enabled:ye(!1),disabled:ye(!0),checked:function(q){var H=q.nodeName.toLowerCase();return H==="input"&&!!q.checked||H==="option"&&!!q.selected},selected:function(q){return q.parentNode&&q.parentNode.selectedIndex,q.selected===!0},empty:function(q){for(q=q.firstChild;q;q=q.nextSibling)if(q.nodeType<6)return!1;return!0},parent:function(q){return!c.pseudos.empty(q)},header:function(q){return bn.test(q.nodeName)},input:function(q){return kt.test(q.nodeName)},button:function(q){var H=q.nodeName.toLowerCase();return H==="input"&&q.type==="button"||H==="button"},text:function(q){var H;return q.nodeName.toLowerCase()==="input"&&q.type==="text"&&((H=q.getAttribute("type"))==null||H.toLowerCase()==="text")},first:pe(function(){return[0]}),last:pe(function(q,H){return[H-1]}),eq:pe(function(q,H,K){return[K<0?K+H:K]}),even:pe(function(q,H){for(var K=0;K<H;K+=2)q.push(K);return q}),odd:pe(function(q,H){for(var K=1;K<H;K+=2)q.push(K);return q}),lt:pe(function(q,H,K){for(var ne=K<0?K+H:K>H?H:K;--ne>=0;)q.push(ne);return q}),gt:pe(function(q,H,K){for(var ne=K<0?K+H:K;++ne<H;)q.push(ne);return q})}},c.pseudos.nth=c.pseudos.eq;for(t in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})c.pseudos[t]=Ce(t);for(t in{submit:!0,reset:!0})c.pseudos[t]=ie(t);function Oe(){}Oe.prototype=c.filters=c.pseudos,c.setFilters=new Oe,u=tn.tokenize=function(q,H){var K,ne,$,re,de,Ae,be,ke=B[q+" "];if(ke)return H?0:ke.slice(0);for(de=q,Ae=[],be=c.preFilter;de;){(!K||(ne=Nn.exec(de)))&&(ne&&(de=de.slice(ne[0].length)||de),Ae.push($=[])),K=!1,(ne=jn.exec(de))&&(K=ne.shift(),$.push({value:K,type:ne[0].replace(Dn," ")}),de=de.slice(K.length));for(re in c.filter)(ne=nn[re].exec(de))&&(!be[re]||(ne=be[re](ne)))&&(K=ne.shift(),$.push({value:K,type:re,matches:ne}),de=de.slice(K.length));if(!K)break}return H?de.length:de?tn.error(q):B(q,Ae).slice(0)};function Ue(q){for(var H=0,K=q.length,ne="";H<K;H++)ne+=q[H].value;return ne}function Ne(q,H,K){var ne=H.dir,$=H.next,re=$||ne,de=K&&re==="parentNode",Ae=w++;return H.first?function(be,ke,Ie){for(;be=be[ne];)if(be.nodeType===1||de)return q(be,ke,Ie);return!1}:function(be,ke,Ie){var Me,$e,ln,qe=[I,Ae];if(Ie){for(;be=be[ne];)if((be.nodeType===1||de)&&q(be,ke,Ie))return!0}else for(;be=be[ne];)if(be.nodeType===1||de)if(ln=be[D]||(be[D]={}),$e=ln[be.uniqueID]||(ln[be.uniqueID]={}),$&&$===be.nodeName.toLowerCase())be=be[ne]||be;else{if((Me=$e[re])&&Me[0]===I&&Me[1]===Ae)return qe[2]=Me[2];if($e[re]=qe,qe[2]=q(be,ke,Ie))return!0}return!1}}function Se(q){return q.length>1?function(H,K,ne){for(var $=q.length;$--;)if(!q[$](H,K,ne))return!1;return!0}:q[0]}function Fe(q,H,K){for(var ne=0,$=H.length;ne<$;ne++)tn(q,H[ne],K);return K}function Le(q,H,K,ne,$){for(var re,de=[],Ae=0,be=q.length,ke=H!=null;Ae<be;Ae++)(re=q[Ae])&&(!K||K(re,ne,$))&&(de.push(re),ke&&H.push(Ae));return de}function sn(q,H,K,ne,$,re){return ne&&!ne[D]&&(ne=sn(ne)),$&&!$[D]&&($=sn($,re)),qn(function(de,Ae,be,ke){var Ie,Me,$e,ln=[],qe=[],me=Ae.length,fe=de||Fe(H||"*",be.nodeType?[be]:be,[]),ve=q&&(de||!H)?Le(fe,ln,q,be,ke):fe,_e=K?$||(de?q:me||ne)?[]:Ae:ve;if(K&&K(ve,_e,be,ke),ne)for(Ie=Le(_e,qe),ne(Ie,[],be,ke),Me=Ie.length;Me--;)($e=Ie[Me])&&(_e[qe[Me]]=!(ve[qe[Me]]=$e));if(de){if($||q){if($){for(Ie=[],Me=_e.length;Me--;)($e=_e[Me])&&Ie.push(ve[Me]=$e);$(null,_e=[],Ie,ke)}for(Me=_e.length;Me--;)($e=_e[Me])&&(Ie=$?ge(de,$e):ln[Me])>-1&&(de[Ie]=!(Ae[Ie]=$e))}}else _e=Le(_e===Ae?_e.splice(me,_e.length):_e),$?$(null,Ae,_e,ke):te.apply(Ae,_e)})}function An(q){for(var H,K,ne,$=q.length,re=c.relative[q[0].type],de=re||c.relative[" "],Ae=re?1:0,be=Ne(function(Me){return Me===H},de,!0),ke=Ne(function(Me){return ge(H,Me)>-1},de,!0),Ie=[function(Me,$e,ln){var qe=!re&&(ln||$e!==m)||((H=$e).nodeType?be(Me,$e,ln):ke(Me,$e,ln));return H=null,qe}];Ae<$;Ae++)if(K=c.relative[q[Ae].type])Ie=[Ne(Se(Ie),K)];else{if(K=c.filter[q[Ae].type].apply(null,q[Ae].matches),K[D]){for(ne=++Ae;ne<$&&!c.relative[q[ne].type];ne++);return sn(Ae>1&&Se(Ie),Ae>1&&Ue(q.slice(0,Ae-1).concat({value:q[Ae-2].type===" "?"*":""})).replace(Dn,"$1"),K,Ae<ne&&An(q.slice(Ae,ne)),ne<$&&An(q=q.slice(ne)),ne<$&&Ue(q))}Ie.push(K)}return Se(Ie)}function We(q,H){var K=H.length>0,ne=q.length>0,$=function(re,de,Ae,be,ke){var Ie,Me,$e,ln=0,qe="0",me=re&&[],fe=[],ve=m,_e=re||ne&&c.find.TAG("*",ke),Ye=I+=ve==null?1:Math.random()||.1,Xe=_e.length;for(ke&&(m=de==_||de||ke);qe!==Xe&&(Ie=_e[qe])!=null;qe++){if(ne&&Ie){for(Me=0,!de&&Ie.ownerDocument!=_&&(v(Ie),Ae=!C);$e=q[Me++];)if($e(Ie,de||_,Ae)){be.push(Ie);break}ke&&(I=Ye)}K&&((Ie=!$e&&Ie)&&ln--,re&&me.push(Ie))}if(ln+=qe,K&&qe!==ln){for(Me=0;$e=H[Me++];)$e(me,fe,de,Ae);if(re){if(ln>0)for(;qe--;)me[qe]||fe[qe]||(fe[qe]=G.call(be));fe=Le(fe)}te.apply(be,fe),ke&&!re&&fe.length>0&&ln+H.length>1&&tn.uniqueSort(be)}return ke&&(I=Ye,m=ve),me};return K?qn($):$}h=tn.compile=function(q,H){var K,ne=[],$=[],re=U[q+" "];if(!re){for(H||(H=u(q)),K=H.length;K--;)re=An(H[K]),re[D]?ne.push(re):$.push(re);re=U(q,We($,ne)),re.selector=q}return re},i=tn.select=function(q,H,K,ne){var $,re,de,Ae,be,ke=typeof q=="function"&&q,Ie=!ne&&u(q=ke.selector||q);if(K=K||[],Ie.length===1){if(re=Ie[0]=Ie[0].slice(0),re.length>2&&(de=re[0]).type==="ID"&&H.nodeType===9&&C&&c.relative[re[1].type]){if(H=(c.find.ID(de.matches[0].replace(un,gn),H)||[])[0],H)ke&&(H=H.parentNode);else return K;q=q.slice(re.shift().value.length)}for($=nn.needsContext.test(q)?0:re.length;$--&&(de=re[$],!c.relative[Ae=de.type]);)if((be=c.find[Ae])&&(ne=be(de.matches[0].replace(un,gn),On.test(re[0].type)&&Ee(H.parentNode)||H))){if(re.splice($,1),q=ne.length&&Ue(re),!q)return te.apply(K,ne),K;break}}return(ke||h(q,Ie))(ne,H,!C,K,!H||On.test(q)&&Ee(H.parentNode)||H),K},l.sortStable=D.split("").sort(F).join("")===D,l.detectDuplicates=!!f,v(),l.sortDetached=ce(function(q){return q.compareDocumentPosition(_.createElement("fieldset"))&1}),ce(function(q){return q.innerHTML="<a href='#'></a>",q.firstChild.getAttribute("href")==="#"})||J("type|href|height|width",function(q,H,K){if(!K)return q.getAttribute(H,H.toLowerCase()==="type"?1:2)}),(!l.attributes||!ce(function(q){return q.innerHTML="<input/>",q.firstChild.setAttribute("value",""),q.firstChild.getAttribute("value")===""}))&&J("value",function(q,H,K){if(!K&&q.nodeName.toLowerCase()==="input")return q.defaultValue}),ce(function(q){return q.getAttribute("disabled")==null})||J(Q,function(q,H,K){var ne;if(!K)return q[H]===!0?H.toLowerCase():(ne=q.getAttributeNode(H))&&ne.specified?ne.value:null});var kn=r.Sizzle;tn.noConflict=function(){return r.Sizzle===tn&&(r.Sizzle=kn),tn},g=function(){return tn}.call(y,o,y,P),g!==void 0&&(P.exports=g)})(window)},8857:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(8954),o(6258),o(8074),o(7830),o(5749),o(852),o(5214),o(4505),o(2599),o(5210)],r=function(t,l,c,p,a,u,h){"use strict";var i=/%20/g,m=/#.*$/,d=/([?&])_=[^&]*/,f=/^(.*?):[ \t]*([^\r\n]*)$/mg,v=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,_=/^(?:GET|HEAD)$/,A=/^\/\//,C={},E={},S="*/".concat("*"),b=l.createElement("a");b.href=a.href;function x(O){return function(B,U){typeof B!="string"&&(U=B,B="*");var L,F=0,Y=B.toLowerCase().match(p)||[];if(c(U))for(;L=Y[F++];)L[0]==="+"?(L=L.slice(1)||"*",(O[L]=O[L]||[]).unshift(U)):(O[L]=O[L]||[]).push(U)}}function D(O,B,U,L){var F={},Y=O===E;function z(G){var W;return F[G]=!0,t.each(O[G]||[],function(te,oe){var ge=oe(B,U,L);if(typeof ge=="string"&&!Y&&!F[ge])return B.dataTypes.unshift(ge),z(ge),!1;if(Y)return!(W=ge)}),W}return z(B.dataTypes[0])||!F["*"]&&z("*")}function N(O,B){var U,L,F=t.ajaxSettings.flatOptions||{};for(U in B)B[U]!==void 0&&((F[U]?O:L||(L={}))[U]=B[U]);return L&&t.extend(!0,O,L),O}function I(O,B,U){for(var L,F,Y,z,G=O.contents,W=O.dataTypes;W[0]==="*";)W.shift(),L===void 0&&(L=O.mimeType||B.getResponseHeader("Content-Type"));if(L){for(F in G)if(G[F]&&G[F].test(L)){W.unshift(F);break}}if(W[0]in U)Y=W[0];else{for(F in U){if(!W[0]||O.converters[F+" "+W[0]]){Y=F;break}z||(z=F)}Y=Y||z}if(Y)return Y!==W[0]&&W.unshift(Y),U[Y]}function w(O,B,U,L){var F,Y,z,G,W,te={},oe=O.dataTypes.slice();if(oe[1])for(z in O.converters)te[z.toLowerCase()]=O.converters[z];for(Y=oe.shift();Y;)if(O.responseFields[Y]&&(U[O.responseFields[Y]]=B),!W&&L&&O.dataFilter&&(B=O.dataFilter(B,O.dataType)),W=Y,Y=oe.shift(),Y){if(Y==="*")Y=W;else if(W!=="*"&&W!==Y){if(z=te[W+" "+Y]||te["* "+Y],!z){for(F in te)if(G=F.split(" "),G[1]===Y&&(z=te[W+" "+G[0]]||te["* "+G[0]],z)){z===!0?z=te[F]:te[F]!==!0&&(Y=G[0],oe.unshift(G[1]));break}}if(z!==!0)if(z&&O.throws)B=z(B);else try{B=z(B)}catch(ge){return{state:"parsererror",error:z?ge:"No conversion from "+W+" to "+Y}}}}return{state:"success",data:B}}return t.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:a.href,type:"GET",isLocal:v.test(a.protocol),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":S,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":JSON.parse,"text xml":t.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(O,B){return B?N(N(O,t.ajaxSettings),B):N(t.ajaxSettings,O)},ajaxPrefilter:x(C),ajaxTransport:x(E),ajax:function(O,B){typeof O=="object"&&(B=O,O=void 0),B=B||{};var U,L,F,Y,z,G,W,te,oe,ge,Q=t.ajaxSetup({},B),Te=Q.context||Q,Pe=Q.context&&(Te.nodeType||Te.jquery)?t(Te):t.event,Ge=t.Deferred(),mn=t.Callbacks("once memory"),Rn=Q.statusCode||{},Dn={},Nn={},jn="canceled",je={readyState:0,getResponseHeader:function(Ze){var nn;if(W){if(!Y)for(Y={};nn=f.exec(F);)Y[nn[1].toLowerCase()+" "]=(Y[nn[1].toLowerCase()+" "]||[]).concat(nn[2]);nn=Y[Ze.toLowerCase()+" "]}return nn==null?null:nn.join(", ")},getAllResponseHeaders:function(){return W?F:null},setRequestHeader:function(Ze,nn){return W==null&&(Ze=Nn[Ze.toLowerCase()]=Nn[Ze.toLowerCase()]||Ze,Dn[Ze]=nn),this},overrideMimeType:function(Ze){return W==null&&(Q.mimeType=Ze),this},statusCode:function(Ze){var nn;if(Ze)if(W)je.always(Ze[je.status]);else for(nn in Ze)Rn[nn]=[Rn[nn],Ze[nn]];return this},abort:function(Ze){var nn=Ze||jn;return U&&U.abort(nn),Ln(0,nn),this}};if(Ge.promise(je),Q.url=((O||Q.url||a.href)+"").replace(A,a.protocol+"//"),Q.type=B.method||B.type||Q.method||Q.type,Q.dataTypes=(Q.dataType||"*").toLowerCase().match(p)||[""],Q.crossDomain==null){G=l.createElement("a");try{G.href=Q.url,G.href=G.href,Q.crossDomain=b.protocol+"//"+b.host!=G.protocol+"//"+G.host}catch(Ze){Q.crossDomain=!0}}if(Q.data&&Q.processData&&typeof Q.data!="string"&&(Q.data=t.param(Q.data,Q.traditional)),D(C,Q,B,je),W)return je;te=t.event&&Q.global,te&&t.active++===0&&t.event.trigger("ajaxStart"),Q.type=Q.type.toUpperCase(),Q.hasContent=!_.test(Q.type),L=Q.url.replace(m,""),Q.hasContent?Q.data&&Q.processData&&(Q.contentType||"").indexOf("application/x-www-form-urlencoded")===0&&(Q.data=Q.data.replace(i,"+")):(ge=Q.url.slice(L.length),Q.data&&(Q.processData||typeof Q.data=="string")&&(L+=(h.test(L)?"&":"?")+Q.data,delete Q.data),Q.cache===!1&&(L=L.replace(d,"$1"),ge=(h.test(L)?"&":"?")+"_="+u.guid+++ge),Q.url=L+ge),Q.ifModified&&(t.lastModified[L]&&je.setRequestHeader("If-Modified-Since",t.lastModified[L]),t.etag[L]&&je.setRequestHeader("If-None-Match",t.etag[L])),(Q.data&&Q.hasContent&&Q.contentType!==!1||B.contentType)&&je.setRequestHeader("Content-Type",Q.contentType),je.setRequestHeader("Accept",Q.dataTypes[0]&&Q.accepts[Q.dataTypes[0]]?Q.accepts[Q.dataTypes[0]]+(Q.dataTypes[0]!=="*"?", "+S+"; q=0.01":""):Q.accepts["*"]);for(oe in Q.headers)je.setRequestHeader(oe,Q.headers[oe]);if(Q.beforeSend&&(Q.beforeSend.call(Te,je,Q)===!1||W))return je.abort();if(jn="abort",mn.add(Q.complete),je.done(Q.success),je.fail(Q.error),U=D(E,Q,B,je),!U)Ln(-1,"No Transport");else{if(je.readyState=1,te&&Pe.trigger("ajaxSend",[je,Q]),W)return je;Q.async&&Q.timeout>0&&(z=window.setTimeout(function(){je.abort("timeout")},Q.timeout));try{W=!1,U.send(Dn,Ln)}catch(Ze){if(W)throw Ze;Ln(-1,Ze)}}function Ln(Ze,nn,Yn,kt){var bn,Wn,mt,On,un,gn=nn;W||(W=!0,z&&window.clearTimeout(z),U=void 0,F=kt||"",je.readyState=Ze>0?4:0,bn=Ze>=200&&Ze<300||Ze===304,Yn&&(On=I(Q,je,Yn)),!bn&&t.inArray("script",Q.dataTypes)>-1&&t.inArray("json",Q.dataTypes)<0&&(Q.converters["text script"]=function(){}),On=w(Q,On,je,bn),bn?(Q.ifModified&&(un=je.getResponseHeader("Last-Modified"),un&&(t.lastModified[L]=un),un=je.getResponseHeader("etag"),un&&(t.etag[L]=un)),Ze===204||Q.type==="HEAD"?gn="nocontent":Ze===304?gn="notmodified":(gn=On.state,Wn=On.data,mt=On.error,bn=!mt)):(mt=gn,(Ze||!gn)&&(gn="error",Ze<0&&(Ze=0))),je.status=Ze,je.statusText=(nn||gn)+"",bn?Ge.resolveWith(Te,[Wn,gn,je]):Ge.rejectWith(Te,[je,gn,mt]),je.statusCode(Rn),Rn=void 0,te&&Pe.trigger(bn?"ajaxSuccess":"ajaxError",[je,Q,bn?Wn:mt]),mn.fireWith(Te,[je,gn]),te&&(Pe.trigger("ajaxComplete",[je,Q]),--t.active||t.event.trigger("ajaxStop")))}return je},getJSON:function(O,B,U){return t.get(O,B,U,"json")},getScript:function(O,B){return t.get(O,void 0,B,"script")}}),t.each(["get","post"],function(O,B){t[B]=function(U,L,F,Y){return c(L)&&(Y=Y||F,F=L,L=void 0),t.ajax(t.extend({url:U,type:B,dataType:Y,data:L,success:F},t.isPlainObject(U)&&U))}}),t.ajaxPrefilter(function(O){var B;for(B in O.headers)B.toLowerCase()==="content-type"&&(O.contentType=O.headers[B]||"")}),t}.apply(y,g),r!==void 0&&(P.exports=r)},3150:(P,y,o)=>{var g,r;g=[o(6934),o(8954),o(7830),o(5749),o(8857)],r=function(t,l,c,p){"use strict";var a=[],u=/(=)\?(?=&|$)|\?\?/;t.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var h=a.pop()||t.expando+"_"+c.guid++;return this[h]=!0,h}}),t.ajaxPrefilter("json jsonp",function(h,i,m){var d,f,v,_=h.jsonp!==!1&&(u.test(h.url)?"url":typeof h.data=="string"&&(h.contentType||"").indexOf("application/x-www-form-urlencoded")===0&&u.test(h.data)&&"data");if(_||h.dataTypes[0]==="jsonp")return d=h.jsonpCallback=l(h.jsonpCallback)?h.jsonpCallback():h.jsonpCallback,_?h[_]=h[_].replace(u,"$1"+d):h.jsonp!==!1&&(h.url+=(p.test(h.url)?"&":"?")+h.jsonp+"="+d),h.converters["script json"]=function(){return v||t.error(d+" was not called"),v[0]},h.dataTypes[0]="json",f=window[d],window[d]=function(){v=arguments},m.always(function(){f===void 0?t(window).removeProp(d):window[d]=f,h[d]&&(h.jsonpCallback=i.jsonpCallback,a.push(d)),v&&l(f)&&f(v[0]),v=f=void 0}),"script"})}.apply(y,g),r!==void 0&&(P.exports=r)},5774:(P,y,o)=>{var g,r;g=[o(6934),o(230),o(8954),o(5109),o(8857),o(4048),o(4819),o(3670)],r=function(t,l,c){"use strict";t.fn.load=function(p,a,u){var h,i,m,d=this,f=p.indexOf(" ");return f>-1&&(h=l(p.slice(f)),p=p.slice(0,f)),c(a)?(u=a,a=void 0):a&&typeof a=="object"&&(i="POST"),d.length>0&&t.ajax({url:p,type:i||"GET",dataType:"html",data:a}).done(function(v){m=arguments,d.html(h?t("<div>").append(t.parseHTML(v)).find(h):v)}).always(u&&function(v,_){d.each(function(){u.apply(this,m||[v.responseText,_,v])})}),this}}.apply(y,g),r!==void 0&&(P.exports=r)},9155:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(8857)],r=function(t,l){"use strict";t.ajaxPrefilter(function(c){c.crossDomain&&(c.contents.script=!1)}),t.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function(c){return t.globalEval(c),c}}}),t.ajaxPrefilter("script",function(c){c.cache===void 0&&(c.cache=!1),c.crossDomain&&(c.type="GET")}),t.ajaxTransport("script",function(c){if(c.crossDomain||c.scriptAttrs){var p,a;return{send:function(u,h){p=t("<script>").attr(c.scriptAttrs||{}).prop({charset:c.scriptCharset,src:c.url}).on("load error",a=function(i){p.remove(),a=null,i&&h(i.type==="error"?404:200,i.type)}),l.head.appendChild(p[0])},abort:function(){a&&a()}}}})}.apply(y,g),r!==void 0&&(P.exports=r)},8074:(P,y,o)=>{var g;g=function(){"use strict";return window.location}.call(y,o,y,P),g!==void 0&&(P.exports=g)},7830:(P,y,o)=>{var g;g=function(){"use strict";return{guid:Date.now()}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},5749:(P,y,o)=>{var g;g=function(){"use strict";return/\?/}.call(y,o,y,P),g!==void 0&&(P.exports=g)},8838:(P,y,o)=>{var g,r;g=[o(6934),o(7511),o(8857)],r=function(t,l){"use strict";t.ajaxSettings.xhr=function(){try{return new window.XMLHttpRequest}catch(a){}};var c={0:200,1223:204},p=t.ajaxSettings.xhr();l.cors=!!p&&"withCredentials"in p,l.ajax=p=!!p,t.ajaxTransport(function(a){var u,h;if(l.cors||p&&!a.crossDomain)return{send:function(i,m){var d,f=a.xhr();if(f.open(a.type,a.url,a.async,a.username,a.password),a.xhrFields)for(d in a.xhrFields)f[d]=a.xhrFields[d];a.mimeType&&f.overrideMimeType&&f.overrideMimeType(a.mimeType),!a.crossDomain&&!i["X-Requested-With"]&&(i["X-Requested-With"]="XMLHttpRequest");for(d in i)f.setRequestHeader(d,i[d]);u=function(v){return function(){u&&(u=h=f.onload=f.onerror=f.onabort=f.ontimeout=f.onreadystatechange=null,v==="abort"?f.abort():v==="error"?typeof f.status!="number"?m(0,"error"):m(f.status,f.statusText):m(c[f.status]||f.status,f.statusText,(f.responseType||"text")!=="text"||typeof f.responseText!="string"?{binary:f.response}:{text:f.responseText},f.getAllResponseHeaders()))}},f.onload=u(),h=f.onerror=f.ontimeout=u("error"),f.onabort!==void 0?f.onabort=h:f.onreadystatechange=function(){f.readyState===4&&window.setTimeout(function(){u&&h()})},u=u("abort");try{f.send(a.hasContent&&a.data||null)}catch(v){if(u)throw v}},abort:function(){u&&u()}}})}.apply(y,g),r!==void 0&&(P.exports=r)},1159:(P,y,o)=>{var g,r;g=[o(6934),o(8238),o(6799),o(3254),o(3393)],r=function(t){"use strict";return t}.apply(y,g),r!==void 0&&(P.exports=r)},8238:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(8251),o(4877),o(6258),o(3670)],r=function(t,l,c,p,a){"use strict";var u,h=t.expr.attrHandle;t.fn.extend({attr:function(i,m){return l(this,t.attr,i,m,arguments.length>1)},removeAttr:function(i){return this.each(function(){t.removeAttr(this,i)})}}),t.extend({attr:function(i,m,d){var f,v,_=i.nodeType;if(!(_===3||_===8||_===2)){if(typeof i.getAttribute=="undefined")return t.prop(i,m,d);if((_!==1||!t.isXMLDoc(i))&&(v=t.attrHooks[m.toLowerCase()]||(t.expr.match.bool.test(m)?u:void 0)),d!==void 0){if(d===null){t.removeAttr(i,m);return}return v&&"set"in v&&(f=v.set(i,d,m))!==void 0?f:(i.setAttribute(m,d+""),d)}return v&&"get"in v&&(f=v.get(i,m))!==null?f:(f=t.find.attr(i,m),f==null?void 0:f)}},attrHooks:{type:{set:function(i,m){if(!p.radioValue&&m==="radio"&&c(i,"input")){var d=i.value;return i.setAttribute("type",m),d&&(i.value=d),m}}}},removeAttr:function(i,m){var d,f=0,v=m&&m.match(a);if(v&&i.nodeType===1)for(;d=v[f++];)i.removeAttribute(d)}}),u={set:function(i,m,d){return m===!1?t.removeAttr(i,d):i.setAttribute(d,d),d}},t.each(t.expr.match.bool.source.match(/\w+/g),function(i,m){var d=h[m]||t.find.attr;h[m]=function(f,v,_){var A,C,E=v.toLowerCase();return _||(C=h[E],h[E]=A,A=d(f,v,_)!=null?E:null,h[E]=C),A}})}.apply(y,g),r!==void 0&&(P.exports=r)},3254:(P,y,o)=>{var g,r;g=[o(6934),o(230),o(8954),o(6258),o(1535),o(852)],r=function(t,l,c,p,a){"use strict";function u(i){return i.getAttribute&&i.getAttribute("class")||""}function h(i){return Array.isArray(i)?i:typeof i=="string"?i.match(p)||[]:[]}t.fn.extend({addClass:function(i){var m,d,f,v,_,A,C,E=0;if(c(i))return this.each(function(S){t(this).addClass(i.call(this,S,u(this)))});if(m=h(i),m.length){for(;d=this[E++];)if(v=u(d),f=d.nodeType===1&&" "+l(v)+" ",f){for(A=0;_=m[A++];)f.indexOf(" "+_+" ")<0&&(f+=_+" ");C=l(f),v!==C&&d.setAttribute("class",C)}}return this},removeClass:function(i){var m,d,f,v,_,A,C,E=0;if(c(i))return this.each(function(S){t(this).removeClass(i.call(this,S,u(this)))});if(!arguments.length)return this.attr("class","");if(m=h(i),m.length){for(;d=this[E++];)if(v=u(d),f=d.nodeType===1&&" "+l(v)+" ",f){for(A=0;_=m[A++];)for(;f.indexOf(" "+_+" ")>-1;)f=f.replace(" "+_+" "," ");C=l(f),v!==C&&d.setAttribute("class",C)}}return this},toggleClass:function(i,m){var d=typeof i,f=d==="string"||Array.isArray(i);return typeof m=="boolean"&&f?m?this.addClass(i):this.removeClass(i):c(i)?this.each(function(v){t(this).toggleClass(i.call(this,v,u(this),m),m)}):this.each(function(){var v,_,A,C;if(f)for(_=0,A=t(this),C=h(i);v=C[_++];)A.hasClass(v)?A.removeClass(v):A.addClass(v);else(i===void 0||d==="boolean")&&(v=u(this),v&&a.set(this,"__className__",v),this.setAttribute&&this.setAttribute("class",v||i===!1?"":a.get(this,"__className__")||""))})},hasClass:function(i){var m,d,f=0;for(m=" "+i+" ";d=this[f++];)if(d.nodeType===1&&(" "+l(u(d))+" ").indexOf(m)>-1)return!0;return!1}})}.apply(y,g),r!==void 0&&(P.exports=r)},6799:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(4877),o(3670)],r=function(t,l,c){"use strict";var p=/^(?:input|select|textarea|button)$/i,a=/^(?:a|area)$/i;t.fn.extend({prop:function(u,h){return l(this,t.prop,u,h,arguments.length>1)},removeProp:function(u){return this.each(function(){delete this[t.propFix[u]||u]})}}),t.extend({prop:function(u,h,i){var m,d,f=u.nodeType;if(!(f===3||f===8||f===2))return(f!==1||!t.isXMLDoc(u))&&(h=t.propFix[h]||h,d=t.propHooks[h]),i!==void 0?d&&"set"in d&&(m=d.set(u,i,h))!==void 0?m:u[h]=i:d&&"get"in d&&(m=d.get(u,h))!==null?m:u[h]},propHooks:{tabIndex:{get:function(u){var h=t.find.attr(u,"tabindex");return h?parseInt(h,10):p.test(u.nodeName)||a.test(u.nodeName)&&u.href?0:-1}}},propFix:{for:"htmlFor",class:"className"}}),c.optSelected||(t.propHooks.selected={get:function(u){var h=u.parentNode;return h&&h.parentNode&&h.parentNode.selectedIndex,null},set:function(u){var h=u.parentNode;h&&(h.selectedIndex,h.parentNode&&h.parentNode.selectedIndex)}}),t.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){t.propFix[this.toLowerCase()]=this})}.apply(y,g),r!==void 0&&(P.exports=r)},4877:(P,y,o)=>{var g,r;g=[o(3540),o(7511)],r=function(t,l){"use strict";return function(){var c=t.createElement("input"),p=t.createElement("select"),a=p.appendChild(t.createElement("option"));c.type="checkbox",l.checkOn=c.value!=="",l.optSelected=a.selected,c=t.createElement("input"),c.value="t",c.type="radio",l.radioValue=c.value==="t"}(),l}.apply(y,g),r!==void 0&&(P.exports=r)},3393:(P,y,o)=>{var g,r;g=[o(6934),o(230),o(4877),o(8251),o(8954),o(852)],r=function(t,l,c,p,a){"use strict";var u=/\r/g;t.fn.extend({val:function(h){var i,m,d,f=this[0];return arguments.length?(d=a(h),this.each(function(v){var _;this.nodeType===1&&(d?_=h.call(this,v,t(this).val()):_=h,_==null?_="":typeof _=="number"?_+="":Array.isArray(_)&&(_=t.map(_,function(A){return A==null?"":A+""})),i=t.valHooks[this.type]||t.valHooks[this.nodeName.toLowerCase()],(!i||!("set"in i)||i.set(this,_,"value")===void 0)&&(this.value=_))})):f?(i=t.valHooks[f.type]||t.valHooks[f.nodeName.toLowerCase()],i&&"get"in i&&(m=i.get(f,"value"))!==void 0?m:(m=f.value,typeof m=="string"?m.replace(u,""):m==null?"":m)):void 0}}),t.extend({valHooks:{option:{get:function(h){var i=t.find.attr(h,"value");return i!=null?i:l(t.text(h))}},select:{get:function(h){var i,m,d,f=h.options,v=h.selectedIndex,_=h.type==="select-one",A=_?null:[],C=_?v+1:f.length;for(v<0?d=C:d=_?v:0;d<C;d++)if(m=f[d],(m.selected||d===v)&&!m.disabled&&(!m.parentNode.disabled||!p(m.parentNode,"optgroup"))){if(i=t(m).val(),_)return i;A.push(i)}return A},set:function(h,i){for(var m,d,f=h.options,v=t.makeArray(i),_=f.length;_--;)d=f[_],(d.selected=t.inArray(t.valHooks.option.get(d),v)>-1)&&(m=!0);return m||(h.selectedIndex=-1),v}}}}),t.each(["radio","checkbox"],function(){t.valHooks[this]={set:function(h,i){if(Array.isArray(i))return h.checked=t.inArray(t(h).val(),i)>-1}},c.checkOn||(t.valHooks[this].get=function(h){return h.getAttribute("value")===null?"on":h.value})})}.apply(y,g),r!==void 0&&(P.exports=r)},5367:(P,y,o)=>{var g,r;g=[o(6934),o(6627),o(8954),o(6258)],r=function(t,l,c,p){"use strict";function a(u){var h={};return t.each(u.match(p)||[],function(i,m){h[m]=!0}),h}return t.Callbacks=function(u){u=typeof u=="string"?a(u):t.extend({},u);var h,i,m,d,f=[],v=[],_=-1,A=function(){for(d=d||u.once,m=h=!0;v.length;_=-1)for(i=v.shift();++_<f.length;)f[_].apply(i[0],i[1])===!1&&u.stopOnFalse&&(_=f.length,i=!1);u.memory||(i=!1),h=!1,d&&(i?f=[]:f="")},C={add:function(){return f&&(i&&!h&&(_=f.length-1,v.push(i)),function E(S){t.each(S,function(b,x){c(x)?(!u.unique||!C.has(x))&&f.push(x):x&&x.length&&l(x)!=="string"&&E(x)})}(arguments),i&&!h&&A()),this},remove:function(){return t.each(arguments,function(E,S){for(var b;(b=t.inArray(S,f,b))>-1;)f.splice(b,1),b<=_&&_--}),this},has:function(E){return E?t.inArray(E,f)>-1:f.length>0},empty:function(){return f&&(f=[]),this},disable:function(){return d=v=[],f=i="",this},disabled:function(){return!f},lock:function(){return d=v=[],!i&&!h&&(f=i=""),this},locked:function(){return!!d},fireWith:function(E,S){return d||(S=S||[],S=[E,S.slice?S.slice():S],v.push(S),h||A()),this},fire:function(){return C.fireWith(this,arguments),this},fired:function(){return!!m}};return C},t}.apply(y,g),r!==void 0&&(P.exports=r)},6934:(P,y,o)=>{var g,r;g=[o(9929),o(1410),o(7451),o(5115),o(8076),o(7337),o(8002),o(3947),o(5862),o(6704),o(21),o(7511),o(8954),o(8194),o(294),o(6627)],r=function(t,l,c,p,a,u,h,i,m,d,f,v,_,A,C,E){"use strict";var S="3.6.0",b=function(D,N){return new b.fn.init(D,N)};b.fn=b.prototype={jquery:S,constructor:b,length:0,toArray:function(){return c.call(this)},get:function(D){return D==null?c.call(this):D<0?this[D+this.length]:this[D]},pushStack:function(D){var N=b.merge(this.constructor(),D);return N.prevObject=this,N},each:function(D){return b.each(this,D)},map:function(D){return this.pushStack(b.map(this,function(N,I){return D.call(N,I,N)}))},slice:function(){return this.pushStack(c.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},even:function(){return this.pushStack(b.grep(this,function(D,N){return(N+1)%2}))},odd:function(){return this.pushStack(b.grep(this,function(D,N){return N%2}))},eq:function(D){var N=this.length,I=+D+(D<0?N:0);return this.pushStack(I>=0&&I<N?[this[I]]:[])},end:function(){return this.prevObject||this.constructor()},push:a,sort:t.sort,splice:t.splice},b.extend=b.fn.extend=function(){var D,N,I,w,O,B,U=arguments[0]||{},L=1,F=arguments.length,Y=!1;for(typeof U=="boolean"&&(Y=U,U=arguments[L]||{},L++),typeof U!="object"&&!_(U)&&(U={}),L===F&&(U=this,L--);L<F;L++)if((D=arguments[L])!=null)for(N in D)w=D[N],!(N==="__proto__"||U===w)&&(Y&&w&&(b.isPlainObject(w)||(O=Array.isArray(w)))?(I=U[N],O&&!Array.isArray(I)?B=[]:!O&&!b.isPlainObject(I)?B={}:B=I,O=!1,U[N]=b.extend(Y,B,w)):w!==void 0&&(U[N]=w));return U},b.extend({expando:"jQuery"+(S+Math.random()).replace(/\D/g,""),isReady:!0,error:function(D){throw new Error(D)},noop:function(){},isPlainObject:function(D){var N,I;return!D||i.call(D)!=="[object Object]"?!1:(N=l(D),N?(I=m.call(N,"constructor")&&N.constructor,typeof I=="function"&&d.call(I)===f):!0)},isEmptyObject:function(D){var N;for(N in D)return!1;return!0},globalEval:function(D,N,I){C(D,{nonce:N&&N.nonce},I)},each:function(D,N){var I,w=0;if(x(D))for(I=D.length;w<I&&N.call(D[w],w,D[w])!==!1;w++);else for(w in D)if(N.call(D[w],w,D[w])===!1)break;return D},makeArray:function(D,N){var I=N||[];return D!=null&&(x(Object(D))?b.merge(I,typeof D=="string"?[D]:D):a.call(I,D)),I},inArray:function(D,N,I){return N==null?-1:u.call(N,D,I)},merge:function(D,N){for(var I=+N.length,w=0,O=D.length;w<I;w++)D[O++]=N[w];return D.length=O,D},grep:function(D,N,I){for(var w,O=[],B=0,U=D.length,L=!I;B<U;B++)w=!N(D[B],B),w!==L&&O.push(D[B]);return O},map:function(D,N,I){var w,O,B=0,U=[];if(x(D))for(w=D.length;B<w;B++)O=N(D[B],B,I),O!=null&&U.push(O);else for(B in D)O=N(D[B],B,I),O!=null&&U.push(O);return p(U)},guid:1,support:v}),typeof Symbol=="function"&&(b.fn[Symbol.iterator]=t[Symbol.iterator]),b.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(D,N){h["[object "+N+"]"]=N.toLowerCase()});function x(D){var N=!!D&&"length"in D&&D.length,I=E(D);return _(D)||A(D)?!1:I==="array"||N===0||typeof N=="number"&&N>0&&N-1 in D}return b}.apply(y,g),r!==void 0&&(P.exports=r)},294:(P,y,o)=>{var g,r;g=[o(3540)],r=function(t){"use strict";var l={type:!0,src:!0,nonce:!0,noModule:!0};function c(p,a,u){u=u||t;var h,i,m=u.createElement("script");if(m.text=p,a)for(h in l)i=a[h]||a.getAttribute&&a.getAttribute(h),i&&m.setAttribute(h,i);u.head.appendChild(m).parentNode.removeChild(m)}return c}.apply(y,g),r!==void 0&&(P.exports=r)},1619:(P,y,o)=>{var g,r;g=[o(6934),o(6627),o(8954)],r=function(t,l,c){"use strict";var p=function(a,u,h,i,m,d,f){var v=0,_=a.length,A=h==null;if(l(h)==="object"){m=!0;for(v in h)p(a,u,v,h[v],!0,d,f)}else if(i!==void 0&&(m=!0,c(i)||(f=!0),A&&(f?(u.call(a,i),u=null):(A=u,u=function(C,E,S){return A.call(t(C),S)})),u))for(;v<_;v++)u(a[v],h,f?i:i.call(a[v],v,u(a[v],h)));return m?a:A?u.call(a):_?u(a[0],h):d};return p}.apply(y,g),r!==void 0&&(P.exports=r)},2504:(P,y)=>{var o,g;o=[],g=function(){"use strict";var r=/^-ms-/,t=/-([a-z])/g;function l(p,a){return a.toUpperCase()}function c(p){return p.replace(r,"ms-").replace(t,l)}return c}.apply(y,o),g!==void 0&&(P.exports=g)},852:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(8954),o(4933),o(6441)],r=function(t,l,c,p){"use strict";var a,u=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/,h=t.fn.init=function(i,m,d){var f,v;if(!i)return this;if(d=d||a,typeof i=="string")if(i[0]==="<"&&i[i.length-1]===">"&&i.length>=3?f=[null,i,null]:f=u.exec(i),f&&(f[1]||!m))if(f[1]){if(m=m instanceof t?m[0]:m,t.merge(this,t.parseHTML(f[1],m&&m.nodeType?m.ownerDocument||m:l,!0)),p.test(f[1])&&t.isPlainObject(m))for(f in m)c(this[f])?this[f](m[f]):this.attr(f,m[f]);return this}else return v=l.getElementById(f[2]),v&&(this[0]=v,this.length=1),this;else return!m||m.jquery?(m||d).find(i):this.constructor(m).find(i);else{if(i.nodeType)return this[0]=i,this.length=1,this;if(c(i))return d.ready!==void 0?d.ready(i):i(t)}return t.makeArray(i,this)};return h.prototype=t.fn,a=t(l),h}.apply(y,g),r!==void 0&&(P.exports=r)},9203:(P,y,o)=>{var g,r;g=[o(6934),o(4042),o(3670)],r=function(t,l){"use strict";var c=function(a){return t.contains(a.ownerDocument,a)},p={composed:!0};return l.getRootNode&&(c=function(a){return t.contains(a.ownerDocument,a)||a.getRootNode(p)===a.ownerDocument}),c}.apply(y,g),r!==void 0&&(P.exports=r)},8251:(P,y,o)=>{var g;g=function(){"use strict";function r(t,l){return t.nodeName&&t.nodeName.toLowerCase()===l.toLowerCase()}return r}.call(y,o,y,P),g!==void 0&&(P.exports=g)},5109:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(4933),o(6993),o(8233)],r=function(t,l,c,p,a){"use strict";return t.parseHTML=function(u,h,i){if(typeof u!="string")return[];typeof h=="boolean"&&(i=h,h=!1);var m,d,f;return h||(a.createHTMLDocument?(h=l.implementation.createHTMLDocument(""),m=h.createElement("base"),m.href=l.location.href,h.head.appendChild(m)):h=l),d=c.exec(u),f=!i&&[],d?[h.createElement(d[1])]:(d=p([u],h,f),f&&f.length&&t(f).remove(),t.merge([],d.childNodes))},t.parseHTML}.apply(y,g),r!==void 0&&(P.exports=r)},5214:(P,y,o)=>{var g,r;g=[o(6934)],r=function(t){"use strict";return t.parseXML=function(l){var c,p;if(!l||typeof l!="string")return null;try{c=new window.DOMParser().parseFromString(l,"text/xml")}catch(a){}return p=c&&c.getElementsByTagName("parsererror")[0],(!c||p)&&t.error("Invalid XML: "+(p?t.map(p.childNodes,function(a){return a.textContent}).join(`
`):l)),c},t.parseXML}.apply(y,g),r!==void 0&&(P.exports=r)},5832:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(820),o(2599)],r=function(t,l){"use strict";var c=t.Deferred();t.fn.ready=function(a){return c.then(a).catch(function(u){t.readyException(u)}),this},t.extend({isReady:!1,readyWait:1,ready:function(a){(a===!0?--t.readyWait:t.isReady)||(t.isReady=!0,!(a!==!0&&--t.readyWait>0)&&c.resolveWith(l,[t]))}}),t.ready.then=c.then;function p(){l.removeEventListener("DOMContentLoaded",p),window.removeEventListener("load",p),t.ready()}l.readyState==="complete"||l.readyState!=="loading"&&!l.documentElement.doScroll?window.setTimeout(t.ready):(l.addEventListener("DOMContentLoaded",p),window.addEventListener("load",p))}.apply(y,g),r!==void 0&&(P.exports=r)},820:(P,y,o)=>{var g,r;g=[o(6934)],r=function(t){"use strict";t.readyException=function(l){window.setTimeout(function(){throw l})}}.apply(y,g),r!==void 0&&(P.exports=r)},230:(P,y,o)=>{var g,r;g=[o(6258)],r=function(t){"use strict";function l(c){var p=c.match(t)||[];return p.join(" ")}return l}.apply(y,g),r!==void 0&&(P.exports=r)},8233:(P,y,o)=>{var g,r;g=[o(3540),o(7511)],r=function(t,l){"use strict";return l.createHTMLDocument=function(){var c=t.implementation.createHTMLDocument("").body;return c.innerHTML="<form></form><form></form>",c.childNodes.length===2}(),l}.apply(y,g),r!==void 0&&(P.exports=r)},6627:(P,y,o)=>{var g,r;g=[o(8002),o(3947)],r=function(t,l){"use strict";function c(p){return p==null?p+"":typeof p=="object"||typeof p=="function"?t[l.call(p)]||"object":typeof p}return c}.apply(y,g),r!==void 0&&(P.exports=r)},4933:(P,y,o)=>{var g;g=function(){"use strict";return/^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i}.call(y,o,y,P),g!==void 0&&(P.exports=g)},3035:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(2504),o(8251),o(7729),o(4830),o(3395),o(5053),o(1333),o(4454),o(3415),o(4326),o(3087),o(463),o(852),o(5832),o(3670)],r=function(t,l,c,p,a,u,h,i,m,d,f,v,_,A){"use strict";var C=/^(none|table(?!-c[ea]).+)/,E=/^--/,S={position:"absolute",visibility:"hidden",display:"block"},b={letterSpacing:"0",fontWeight:"400"};function x(I,w,O){var B=a.exec(w);return B?Math.max(0,B[2]-(O||0))+(B[3]||"px"):w}function D(I,w,O,B,U,L){var F=w==="width"?1:0,Y=0,z=0;if(O===(B?"border":"content"))return 0;for(;F<4;F+=2)O==="margin"&&(z+=t.css(I,O+h[F],!0,U)),B?(O==="content"&&(z-=t.css(I,"padding"+h[F],!0,U)),O!=="margin"&&(z-=t.css(I,"border"+h[F]+"Width",!0,U))):(z+=t.css(I,"padding"+h[F],!0,U),O!=="padding"?z+=t.css(I,"border"+h[F]+"Width",!0,U):Y+=t.css(I,"border"+h[F]+"Width",!0,U));return!B&&L>=0&&(z+=Math.max(0,Math.ceil(I["offset"+w[0].toUpperCase()+w.slice(1)]-L-z-Y-.5))||0),z}function N(I,w,O){var B=i(I),U=!_.boxSizingReliable()||O,L=U&&t.css(I,"boxSizing",!1,B)==="border-box",F=L,Y=d(I,w,B),z="offset"+w[0].toUpperCase()+w.slice(1);if(u.test(Y)){if(!O)return Y;Y="auto"}return(!_.boxSizingReliable()&&L||!_.reliableTrDimensions()&&p(I,"tr")||Y==="auto"||!parseFloat(Y)&&t.css(I,"display",!1,B)==="inline")&&I.getClientRects().length&&(L=t.css(I,"boxSizing",!1,B)==="border-box",F=z in I,F&&(Y=I[z])),Y=parseFloat(Y)||0,Y+D(I,w,O||(L?"border":"content"),F,B,Y)+"px"}return t.extend({cssHooks:{opacity:{get:function(I,w){if(w){var O=d(I,"opacity");return O===""?"1":O}}}},cssNumber:{animationIterationCount:!0,columnCount:!0,fillOpacity:!0,flexGrow:!0,flexShrink:!0,fontWeight:!0,gridArea:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnStart:!0,gridRow:!0,gridRowEnd:!0,gridRowStart:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{},style:function(I,w,O,B){if(!(!I||I.nodeType===3||I.nodeType===8||!I.style)){var U,L,F,Y=c(w),z=E.test(w),G=I.style;if(z||(w=A(Y)),F=t.cssHooks[w]||t.cssHooks[Y],O!==void 0){if(L=typeof O,L==="string"&&(U=a.exec(O))&&U[1]&&(O=f(I,w,U),L="number"),O==null||O!==O)return;L==="number"&&!z&&(O+=U&&U[3]||(t.cssNumber[Y]?"":"px")),!_.clearCloneStyle&&O===""&&w.indexOf("background")===0&&(G[w]="inherit"),(!F||!("set"in F)||(O=F.set(I,O,B))!==void 0)&&(z?G.setProperty(w,O):G[w]=O)}else return F&&"get"in F&&(U=F.get(I,!1,B))!==void 0?U:G[w]}},css:function(I,w,O,B){var U,L,F,Y=c(w),z=E.test(w);return z||(w=A(Y)),F=t.cssHooks[w]||t.cssHooks[Y],F&&"get"in F&&(U=F.get(I,!0,O)),U===void 0&&(U=d(I,w,B)),U==="normal"&&w in b&&(U=b[w]),O===""||O?(L=parseFloat(U),O===!0||isFinite(L)?L||0:U):U}}),t.each(["height","width"],function(I,w){t.cssHooks[w]={get:function(O,B,U){if(B)return C.test(t.css(O,"display"))&&(!O.getClientRects().length||!O.getBoundingClientRect().width)?m(O,S,function(){return N(O,w,U)}):N(O,w,U)},set:function(O,B,U){var L,F=i(O),Y=!_.scrollboxSize()&&F.position==="absolute",z=Y||U,G=z&&t.css(O,"boxSizing",!1,F)==="border-box",W=U?D(O,w,U,G,F):0;return G&&Y&&(W-=Math.ceil(O["offset"+w[0].toUpperCase()+w.slice(1)]-parseFloat(F[w])-D(O,w,"border",!1,F)-.5)),W&&(L=a.exec(B))&&(L[3]||"px")!=="px"&&(O.style[w]=B,B=t.css(O,w)),x(O,B,W)}}}),t.cssHooks.marginLeft=v(_.reliableMarginLeft,function(I,w){if(w)return(parseFloat(d(I,"marginLeft"))||I.getBoundingClientRect().left-m(I,{marginLeft:0},function(){return I.getBoundingClientRect().left}))+"px"}),t.each({margin:"",padding:"",border:"Width"},function(I,w){t.cssHooks[I+w]={expand:function(O){for(var B=0,U={},L=typeof O=="string"?O.split(" "):[O];B<4;B++)U[I+h[B]+w]=L[B]||L[B-2]||L[0];return U}},I!=="margin"&&(t.cssHooks[I+w].set=x)}),t.fn.extend({css:function(I,w){return l(this,function(O,B,U){var L,F,Y={},z=0;if(Array.isArray(B)){for(L=i(O),F=B.length;z<F;z++)Y[B[z]]=t.css(O,B[z],!1,L);return Y}return U!==void 0?t.style(O,B,U):t.css(O,B)},I,w,arguments.length>1)}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},4326:(P,y,o)=>{var g;g=function(){"use strict";function r(t,l){return{get:function(){if(t()){delete this.get;return}return(this.get=l).apply(this,arguments)}}}return r}.call(y,o,y,P),g!==void 0&&(P.exports=g)},3415:(P,y,o)=>{var g,r;g=[o(6934),o(7729)],r=function(t,l){"use strict";function c(p,a,u,h){var i,m,d=20,f=h?function(){return h.cur()}:function(){return t.css(p,a,"")},v=f(),_=u&&u[3]||(t.cssNumber[a]?"":"px"),A=p.nodeType&&(t.cssNumber[a]||_!=="px"&&+v)&&l.exec(t.css(p,a));if(A&&A[3]!==_){for(v=v/2,_=_||A[3],A=+v||1;d--;)t.style(p,a,A+_),(1-m)*(1-(m=f()/v||.5))<=0&&(d=0),A=A/m;A=A*2,t.style(p,a,A+_),u=u||[]}return u&&(A=+A||+v||0,i=u[1]?A+(u[1]+1)*u[2]:+u[2],h&&(h.unit=_,h.start=A,h.end=i)),i}return c}.apply(y,g),r!==void 0&&(P.exports=r)},4454:(P,y,o)=>{var g,r;g=[o(6934),o(9203),o(84),o(4830),o(5053),o(3087)],r=function(t,l,c,p,a,u){"use strict";function h(i,m,d){var f,v,_,A,C=i.style;return d=d||a(i),d&&(A=d.getPropertyValue(m)||d[m],A===""&&!l(i)&&(A=t.style(i,m)),!u.pixelBoxStyles()&&p.test(A)&&c.test(m)&&(f=C.width,v=C.minWidth,_=C.maxWidth,C.minWidth=C.maxWidth=C.width=A,A=d.width,C.width=f,C.minWidth=v,C.maxWidth=_)),A!==void 0?A+"":A}return h}.apply(y,g),r!==void 0&&(P.exports=r)},463:(P,y,o)=>{var g,r;g=[o(3540),o(6934)],r=function(t,l){"use strict";var c=["Webkit","Moz","ms"],p=t.createElement("div").style,a={};function u(i){for(var m=i[0].toUpperCase()+i.slice(1),d=c.length;d--;)if(i=c[d]+m,i in p)return i}function h(i){var m=l.cssProps[i]||a[i];return m||(i in p?i:a[i]=u(i)||i)}return h}.apply(y,g),r!==void 0&&(P.exports=r)},3241:(P,y,o)=>{var g,r;g=[o(6934),o(3670)],r=function(t){"use strict";t.expr.pseudos.hidden=function(l){return!t.expr.pseudos.visible(l)},t.expr.pseudos.visible=function(l){return!!(l.offsetWidth||l.offsetHeight||l.getClientRects().length)}}.apply(y,g),r!==void 0&&(P.exports=r)},7267:(P,y,o)=>{var g,r;g=[o(6934),o(1535),o(186)],r=function(t,l,c){"use strict";var p={};function a(h){var i,m=h.ownerDocument,d=h.nodeName,f=p[d];return f||(i=m.body.appendChild(m.createElement(d)),f=t.css(i,"display"),i.parentNode.removeChild(i),f==="none"&&(f="block"),p[d]=f,f)}function u(h,i){for(var m,d,f=[],v=0,_=h.length;v<_;v++)d=h[v],!!d.style&&(m=d.style.display,i?(m==="none"&&(f[v]=l.get(d,"display")||null,f[v]||(d.style.display="")),d.style.display===""&&c(d)&&(f[v]=a(d))):m!=="none"&&(f[v]="none",l.set(d,"display",m)));for(v=0;v<_;v++)f[v]!=null&&(h[v].style.display=f[v]);return h}return t.fn.extend({show:function(){return u(this,!0)},hide:function(){return u(this)},toggle:function(h){return typeof h=="boolean"?h?this.show():this.hide():this.each(function(){c(this)?t(this).show():t(this).hide()})}}),u}.apply(y,g),r!==void 0&&(P.exports=r)},3087:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(4042),o(7511)],r=function(t,l,c,p){"use strict";return function(){function a(){if(!!A){_.style.cssText="position:absolute;left:-11111px;width:60px;margin-top:1px;padding:0;border:0",A.style.cssText="position:relative;display:block;box-sizing:border-box;overflow:scroll;margin:auto;border:1px;padding:1px;width:60%;top:1%",c.appendChild(_).appendChild(A);var C=window.getComputedStyle(A);h=C.top!=="1%",v=u(C.marginLeft)===12,A.style.right="60%",d=u(C.right)===36,i=u(C.width)===36,A.style.position="absolute",m=u(A.offsetWidth/3)===12,c.removeChild(_),A=null}}function u(C){return Math.round(parseFloat(C))}var h,i,m,d,f,v,_=l.createElement("div"),A=l.createElement("div");!A.style||(A.style.backgroundClip="content-box",A.cloneNode(!0).style.backgroundClip="",p.clearCloneStyle=A.style.backgroundClip==="content-box",t.extend(p,{boxSizingReliable:function(){return a(),i},pixelBoxStyles:function(){return a(),d},pixelPosition:function(){return a(),h},reliableMarginLeft:function(){return a(),v},scrollboxSize:function(){return a(),m},reliableTrDimensions:function(){var C,E,S,b;return f==null&&(C=l.createElement("table"),E=l.createElement("tr"),S=l.createElement("div"),C.style.cssText="position:absolute;left:-11111px;border-collapse:separate",E.style.cssText="border:1px solid",E.style.height="1px",S.style.height="9px",S.style.display="block",c.appendChild(C).appendChild(E).appendChild(S),b=window.getComputedStyle(E),f=parseInt(b.height,10)+parseInt(b.borderTopWidth,10)+parseInt(b.borderBottomWidth,10)===E.offsetHeight,c.removeChild(C)),f}}))}(),p}.apply(y,g),r!==void 0&&(P.exports=r)},3395:(P,y,o)=>{var g;g=function(){"use strict";return["Top","Right","Bottom","Left"]}.call(y,o,y,P),g!==void 0&&(P.exports=g)},5053:(P,y,o)=>{var g;g=function(){"use strict";return function(r){var t=r.ownerDocument.defaultView;return(!t||!t.opener)&&(t=window),t.getComputedStyle(r)}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},186:(P,y,o)=>{var g,r;g=[o(6934),o(9203)],r=function(t,l){"use strict";return function(c,p){return c=p||c,c.style.display==="none"||c.style.display===""&&l(c)&&t.css(c,"display")==="none"}}.apply(y,g),r!==void 0&&(P.exports=r)},84:(P,y,o)=>{var g,r;g=[o(3395)],r=function(t){"use strict";return new RegExp(t.join("|"),"i")}.apply(y,g),r!==void 0&&(P.exports=r)},4830:(P,y,o)=>{var g,r;g=[o(6668)],r=function(t){"use strict";return new RegExp("^("+t+")(?!px)[a-z%]+$","i")}.apply(y,g),r!==void 0&&(P.exports=r)},1333:(P,y,o)=>{var g;g=function(){"use strict";return function(r,t,l){var c,p,a={};for(p in t)a[p]=r.style[p],r.style[p]=t[p];c=l.call(r);for(p in t)r.style[p]=a[p];return c}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},4569:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(2504),o(1535),o(6141)],r=function(t,l,c,p,a){"use strict";var u=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,h=/[A-Z]/g;function i(d){return d==="true"?!0:d==="false"?!1:d==="null"?null:d===+d+""?+d:u.test(d)?JSON.parse(d):d}function m(d,f,v){var _;if(v===void 0&&d.nodeType===1)if(_="data-"+f.replace(h,"-$&").toLowerCase(),v=d.getAttribute(_),typeof v=="string"){try{v=i(v)}catch(A){}a.set(d,f,v)}else v=void 0;return v}return t.extend({hasData:function(d){return a.hasData(d)||p.hasData(d)},data:function(d,f,v){return a.access(d,f,v)},removeData:function(d,f){a.remove(d,f)},_data:function(d,f,v){return p.access(d,f,v)},_removeData:function(d,f){p.remove(d,f)}}),t.fn.extend({data:function(d,f){var v,_,A,C=this[0],E=C&&C.attributes;if(d===void 0){if(this.length&&(A=a.get(C),C.nodeType===1&&!p.get(C,"hasDataAttrs"))){for(v=E.length;v--;)E[v]&&(_=E[v].name,_.indexOf("data-")===0&&(_=c(_.slice(5)),m(C,_,A[_])));p.set(C,"hasDataAttrs",!0)}return A}return typeof d=="object"?this.each(function(){a.set(this,d)}):l(this,function(S){var b;if(C&&S===void 0)return b=a.get(C,d),b!==void 0||(b=m(C,d),b!==void 0)?b:void 0;this.each(function(){a.set(this,d,S)})},null,f,arguments.length>1,null,!0)},removeData:function(d){return this.each(function(){a.remove(this,d)})}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},157:(P,y,o)=>{var g,r;g=[o(6934),o(2504),o(6258),o(1289)],r=function(t,l,c,p){"use strict";function a(){this.expando=t.expando+a.uid++}return a.uid=1,a.prototype={cache:function(u){var h=u[this.expando];return h||(h={},p(u)&&(u.nodeType?u[this.expando]=h:Object.defineProperty(u,this.expando,{value:h,configurable:!0}))),h},set:function(u,h,i){var m,d=this.cache(u);if(typeof h=="string")d[l(h)]=i;else for(m in h)d[l(m)]=h[m];return d},get:function(u,h){return h===void 0?this.cache(u):u[this.expando]&&u[this.expando][l(h)]},access:function(u,h,i){return h===void 0||h&&typeof h=="string"&&i===void 0?this.get(u,h):(this.set(u,h,i),i!==void 0?i:h)},remove:function(u,h){var i,m=u[this.expando];if(m!==void 0){if(h!==void 0)for(Array.isArray(h)?h=h.map(l):(h=l(h),h=h in m?[h]:h.match(c)||[]),i=h.length;i--;)delete m[h[i]];(h===void 0||t.isEmptyObject(m))&&(u.nodeType?u[this.expando]=void 0:delete u[this.expando])}},hasData:function(u){var h=u[this.expando];return h!==void 0&&!t.isEmptyObject(h)}},a}.apply(y,g),r!==void 0&&(P.exports=r)},1289:(P,y,o)=>{var g;g=function(){"use strict";return function(r){return r.nodeType===1||r.nodeType===9||!+r.nodeType}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},1535:(P,y,o)=>{var g,r;g=[o(157)],r=function(t){"use strict";return new t}.apply(y,g),r!==void 0&&(P.exports=r)},6141:(P,y,o)=>{var g,r;g=[o(157)],r=function(t){"use strict";return new t}.apply(y,g),r!==void 0&&(P.exports=r)},2599:(P,y,o)=>{var g,r;g=[o(6934),o(8954),o(7451),o(5367)],r=function(t,l,c){"use strict";function p(h){return h}function a(h){throw h}function u(h,i,m,d){var f;try{h&&l(f=h.promise)?f.call(h).done(i).fail(m):h&&l(f=h.then)?f.call(h,i,m):i.apply(void 0,[h].slice(d))}catch(v){m.apply(void 0,[v])}}return t.extend({Deferred:function(h){var i=[["notify","progress",t.Callbacks("memory"),t.Callbacks("memory"),2],["resolve","done",t.Callbacks("once memory"),t.Callbacks("once memory"),0,"resolved"],["reject","fail",t.Callbacks("once memory"),t.Callbacks("once memory"),1,"rejected"]],m="pending",d={state:function(){return m},always:function(){return f.done(arguments).fail(arguments),this},catch:function(v){return d.then(null,v)},pipe:function(){var v=arguments;return t.Deferred(function(_){t.each(i,function(A,C){var E=l(v[C[4]])&&v[C[4]];f[C[1]](function(){var S=E&&E.apply(this,arguments);S&&l(S.promise)?S.promise().progress(_.notify).done(_.resolve).fail(_.reject):_[C[0]+"With"](this,E?[S]:arguments)})}),v=null}).promise()},then:function(v,_,A){var C=0;function E(S,b,x,D){return function(){var N=this,I=arguments,w=function(){var B,U;if(!(S<C)){if(B=x.apply(N,I),B===b.promise())throw new TypeError("Thenable self-resolution");U=B&&(typeof B=="object"||typeof B=="function")&&B.then,l(U)?D?U.call(B,E(C,b,p,D),E(C,b,a,D)):(C++,U.call(B,E(C,b,p,D),E(C,b,a,D),E(C,b,p,b.notifyWith))):(x!==p&&(N=void 0,I=[B]),(D||b.resolveWith)(N,I))}},O=D?w:function(){try{w()}catch(B){t.Deferred.exceptionHook&&t.Deferred.exceptionHook(B,O.stackTrace),S+1>=C&&(x!==a&&(N=void 0,I=[B]),b.rejectWith(N,I))}};S?O():(t.Deferred.getStackHook&&(O.stackTrace=t.Deferred.getStackHook()),window.setTimeout(O))}}return t.Deferred(function(S){i[0][3].add(E(0,S,l(A)?A:p,S.notifyWith)),i[1][3].add(E(0,S,l(v)?v:p)),i[2][3].add(E(0,S,l(_)?_:a))}).promise()},promise:function(v){return v!=null?t.extend(v,d):d}},f={};return t.each(i,function(v,_){var A=_[2],C=_[5];d[_[1]]=A.add,C&&A.add(function(){m=C},i[3-v][2].disable,i[3-v][3].disable,i[0][2].lock,i[0][3].lock),A.add(_[3].fire),f[_[0]]=function(){return f[_[0]+"With"](this===f?void 0:this,arguments),this},f[_[0]+"With"]=A.fireWith}),d.promise(f),h&&h.call(f,f),f},when:function(h){var i=arguments.length,m=i,d=Array(m),f=c.call(arguments),v=t.Deferred(),_=function(A){return function(C){d[A]=this,f[A]=arguments.length>1?c.call(arguments):C,--i||v.resolveWith(d,f)}};if(i<=1&&(u(h,v.done(_(m)).resolve,v.reject,!i),v.state()==="pending"||l(f[m]&&f[m].then)))return v.then();for(;m--;)u(f[m],_(m),v.reject);return v.promise()}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},2335:(P,y,o)=>{var g,r;g=[o(6934),o(2599)],r=function(t){"use strict";var l=/^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;t.Deferred.exceptionHook=function(c,p){window.console&&window.console.warn&&c&&l.test(c.name)&&window.console.warn("jQuery.Deferred exception: "+c.message,c.stack,p)}}.apply(y,g),r!==void 0&&(P.exports=r)},7454:(P,y,o)=>{var g,r;g=[o(6934),o(8251),o(2504),o(6627),o(8954),o(8194),o(7451),o(7334),o(9163)],r=function(t,l,c,p,a,u,h){"use strict";var i=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;t.proxy=function(m,d){var f,v,_;if(typeof d=="string"&&(f=m[d],d=m,m=f),!!a(m))return v=h.call(arguments,2),_=function(){return m.apply(d||this,v.concat(h.call(arguments)))},_.guid=m.guid=m.guid||t.guid++,_},t.holdReady=function(m){m?t.readyWait++:t.ready(!0)},t.isArray=Array.isArray,t.parseJSON=JSON.parse,t.nodeName=l,t.isFunction=a,t.isWindow=u,t.camelCase=c,t.type=p,t.now=Date.now,t.isNumeric=function(m){var d=t.type(m);return(d==="number"||d==="string")&&!isNaN(m-parseFloat(m))},t.trim=function(m){return m==null?"":(m+"").replace(i,"")}}.apply(y,g),r!==void 0&&(P.exports=r)},7334:(P,y,o)=>{var g,r;g=[o(6934),o(8857),o(4833)],r=function(t){"use strict";t.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(l,c){t.fn[c]=function(p){return this.on(c,p)}})}.apply(y,g),r!==void 0&&(P.exports=r)},9163:(P,y,o)=>{var g,r;g=[o(6934),o(4833),o(4505)],r=function(t){"use strict";t.fn.extend({bind:function(l,c,p){return this.on(l,null,c,p)},unbind:function(l,c){return this.off(l,null,c)},delegate:function(l,c,p,a){return this.on(c,l,p,a)},undelegate:function(l,c,p){return arguments.length===1?this.off(l,"**"):this.off(c,l||"**",p)},hover:function(l,c){return this.mouseenter(l).mouseleave(c||l)}}),t.each("blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu".split(" "),function(l,c){t.fn[c]=function(p,a){return arguments.length>0?this.on(c,null,p,a):this.trigger(c)}})}.apply(y,g),r!==void 0&&(P.exports=r)},1327:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(8194),o(3035)],r=function(t,l,c){"use strict";return t.each({Height:"height",Width:"width"},function(p,a){t.each({padding:"inner"+p,content:a,"":"outer"+p},function(u,h){t.fn[h]=function(i,m){var d=arguments.length&&(u||typeof i!="boolean"),f=u||(i===!0||m===!0?"margin":"border");return l(this,function(v,_,A){var C;return c(v)?h.indexOf("outer")===0?v["inner"+p]:v.document.documentElement["client"+p]:v.nodeType===9?(C=v.documentElement,Math.max(v.body["scroll"+p],C["scroll"+p],v.body["offset"+p],C["offset"+p],C["client"+p])):A===void 0?t.css(v,_,f):t.style(v,_,A,f)},a,d?i:void 0,d)}})}),t}.apply(y,g),r!==void 0&&(P.exports=r)},4519:(P,y,o)=>{var g,r;g=[o(6934),o(2504),o(3540),o(8954),o(7729),o(6258),o(3395),o(186),o(3415),o(1535),o(7267),o(852),o(1261),o(2599),o(4048),o(4819),o(3035),o(5164)],r=function(t,l,c,p,a,u,h,i,m,d,f){"use strict";var v,_,A=/^(?:toggle|show|hide)$/,C=/queueHooks$/;function E(){_&&(c.hidden===!1&&window.requestAnimationFrame?window.requestAnimationFrame(E):window.setTimeout(E,t.fx.interval),t.fx.tick())}function S(){return window.setTimeout(function(){v=void 0}),v=Date.now()}function b(w,O){var B,U=0,L={height:w};for(O=O?1:0;U<4;U+=2-O)B=h[U],L["margin"+B]=L["padding"+B]=w;return O&&(L.opacity=L.width=w),L}function x(w,O,B){for(var U,L=(I.tweeners[O]||[]).concat(I.tweeners["*"]),F=0,Y=L.length;F<Y;F++)if(U=L[F].call(B,O,w))return U}function D(w,O,B){var U,L,F,Y,z,G,W,te,oe="width"in O||"height"in O,ge=this,Q={},Te=w.style,Pe=w.nodeType&&i(w),Ge=d.get(w,"fxshow");B.queue||(Y=t._queueHooks(w,"fx"),Y.unqueued==null&&(Y.unqueued=0,z=Y.empty.fire,Y.empty.fire=function(){Y.unqueued||z()}),Y.unqueued++,ge.always(function(){ge.always(function(){Y.unqueued--,t.queue(w,"fx").length||Y.empty.fire()})}));for(U in O)if(L=O[U],A.test(L)){if(delete O[U],F=F||L==="toggle",L===(Pe?"hide":"show"))if(L==="show"&&Ge&&Ge[U]!==void 0)Pe=!0;else continue;Q[U]=Ge&&Ge[U]||t.style(w,U)}if(G=!t.isEmptyObject(O),!(!G&&t.isEmptyObject(Q))){oe&&w.nodeType===1&&(B.overflow=[Te.overflow,Te.overflowX,Te.overflowY],W=Ge&&Ge.display,W==null&&(W=d.get(w,"display")),te=t.css(w,"display"),te==="none"&&(W?te=W:(f([w],!0),W=w.style.display||W,te=t.css(w,"display"),f([w]))),(te==="inline"||te==="inline-block"&&W!=null)&&t.css(w,"float")==="none"&&(G||(ge.done(function(){Te.display=W}),W==null&&(te=Te.display,W=te==="none"?"":te)),Te.display="inline-block")),B.overflow&&(Te.overflow="hidden",ge.always(function(){Te.overflow=B.overflow[0],Te.overflowX=B.overflow[1],Te.overflowY=B.overflow[2]})),G=!1;for(U in Q)G||(Ge?"hidden"in Ge&&(Pe=Ge.hidden):Ge=d.access(w,"fxshow",{display:W}),F&&(Ge.hidden=!Pe),Pe&&f([w],!0),ge.done(function(){Pe||f([w]),d.remove(w,"fxshow");for(U in Q)t.style(w,U,Q[U])})),G=x(Pe?Ge[U]:0,U,ge),U in Ge||(Ge[U]=G.start,Pe&&(G.end=G.start,G.start=0))}}function N(w,O){var B,U,L,F,Y;for(B in w)if(U=l(B),L=O[U],F=w[B],Array.isArray(F)&&(L=F[1],F=w[B]=F[0]),B!==U&&(w[U]=F,delete w[B]),Y=t.cssHooks[U],Y&&"expand"in Y){F=Y.expand(F),delete w[U];for(B in F)B in w||(w[B]=F[B],O[B]=L)}else O[U]=L}function I(w,O,B){var U,L,F=0,Y=I.prefilters.length,z=t.Deferred().always(function(){delete G.elem}),G=function(){if(L)return!1;for(var oe=v||S(),ge=Math.max(0,W.startTime+W.duration-oe),Q=ge/W.duration||0,Te=1-Q,Pe=0,Ge=W.tweens.length;Pe<Ge;Pe++)W.tweens[Pe].run(Te);return z.notifyWith(w,[W,Te,ge]),Te<1&&Ge?ge:(Ge||z.notifyWith(w,[W,1,0]),z.resolveWith(w,[W]),!1)},W=z.promise({elem:w,props:t.extend({},O),opts:t.extend(!0,{specialEasing:{},easing:t.easing._default},B),originalProperties:O,originalOptions:B,startTime:v||S(),duration:B.duration,tweens:[],createTween:function(oe,ge){var Q=t.Tween(w,W.opts,oe,ge,W.opts.specialEasing[oe]||W.opts.easing);return W.tweens.push(Q),Q},stop:function(oe){var ge=0,Q=oe?W.tweens.length:0;if(L)return this;for(L=!0;ge<Q;ge++)W.tweens[ge].run(1);return oe?(z.notifyWith(w,[W,1,0]),z.resolveWith(w,[W,oe])):z.rejectWith(w,[W,oe]),this}}),te=W.props;for(N(te,W.opts.specialEasing);F<Y;F++)if(U=I.prefilters[F].call(W,w,te,W.opts),U)return p(U.stop)&&(t._queueHooks(W.elem,W.opts.queue).stop=U.stop.bind(U)),U;return t.map(te,x,W),p(W.opts.start)&&W.opts.start.call(w,W),W.progress(W.opts.progress).done(W.opts.done,W.opts.complete).fail(W.opts.fail).always(W.opts.always),t.fx.timer(t.extend(G,{elem:w,anim:W,queue:W.opts.queue})),W}return t.Animation=t.extend(I,{tweeners:{"*":[function(w,O){var B=this.createTween(w,O);return m(B.elem,w,a.exec(O),B),B}]},tweener:function(w,O){p(w)?(O=w,w=["*"]):w=w.match(u);for(var B,U=0,L=w.length;U<L;U++)B=w[U],I.tweeners[B]=I.tweeners[B]||[],I.tweeners[B].unshift(O)},prefilters:[D],prefilter:function(w,O){O?I.prefilters.unshift(w):I.prefilters.push(w)}}),t.speed=function(w,O,B){var U=w&&typeof w=="object"?t.extend({},w):{complete:B||!B&&O||p(w)&&w,duration:w,easing:B&&O||O&&!p(O)&&O};return t.fx.off?U.duration=0:typeof U.duration!="number"&&(U.duration in t.fx.speeds?U.duration=t.fx.speeds[U.duration]:U.duration=t.fx.speeds._default),(U.queue==null||U.queue===!0)&&(U.queue="fx"),U.old=U.complete,U.complete=function(){p(U.old)&&U.old.call(this),U.queue&&t.dequeue(this,U.queue)},U},t.fn.extend({fadeTo:function(w,O,B,U){return this.filter(i).css("opacity",0).show().end().animate({opacity:O},w,B,U)},animate:function(w,O,B,U){var L=t.isEmptyObject(w),F=t.speed(O,B,U),Y=function(){var z=I(this,t.extend({},w),F);(L||d.get(this,"finish"))&&z.stop(!0)};return Y.finish=Y,L||F.queue===!1?this.each(Y):this.queue(F.queue,Y)},stop:function(w,O,B){var U=function(L){var F=L.stop;delete L.stop,F(B)};return typeof w!="string"&&(B=O,O=w,w=void 0),O&&this.queue(w||"fx",[]),this.each(function(){var L=!0,F=w!=null&&w+"queueHooks",Y=t.timers,z=d.get(this);if(F)z[F]&&z[F].stop&&U(z[F]);else for(F in z)z[F]&&z[F].stop&&C.test(F)&&U(z[F]);for(F=Y.length;F--;)Y[F].elem===this&&(w==null||Y[F].queue===w)&&(Y[F].anim.stop(B),L=!1,Y.splice(F,1));(L||!B)&&t.dequeue(this,w)})},finish:function(w){return w!==!1&&(w=w||"fx"),this.each(function(){var O,B=d.get(this),U=B[w+"queue"],L=B[w+"queueHooks"],F=t.timers,Y=U?U.length:0;for(B.finish=!0,t.queue(this,w,[]),L&&L.stop&&L.stop.call(this,!0),O=F.length;O--;)F[O].elem===this&&F[O].queue===w&&(F[O].anim.stop(!0),F.splice(O,1));for(O=0;O<Y;O++)U[O]&&U[O].finish&&U[O].finish.call(this);delete B.finish})}}),t.each(["toggle","show","hide"],function(w,O){var B=t.fn[O];t.fn[O]=function(U,L,F){return U==null||typeof U=="boolean"?B.apply(this,arguments):this.animate(b(O,!0),U,L,F)}}),t.each({slideDown:b("show"),slideUp:b("hide"),slideToggle:b("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(w,O){t.fn[w]=function(B,U,L){return this.animate(O,B,U,L)}}),t.timers=[],t.fx.tick=function(){var w,O=0,B=t.timers;for(v=Date.now();O<B.length;O++)w=B[O],!w()&&B[O]===w&&B.splice(O--,1);B.length||t.fx.stop(),v=void 0},t.fx.timer=function(w){t.timers.push(w),t.fx.start()},t.fx.interval=13,t.fx.start=function(){_||(_=!0,E())},t.fx.stop=function(){_=null},t.fx.speeds={slow:600,fast:200,_default:400},t}.apply(y,g),r!==void 0&&(P.exports=r)},5164:(P,y,o)=>{var g,r;g=[o(6934),o(463),o(3035)],r=function(t,l){"use strict";function c(p,a,u,h,i){return new c.prototype.init(p,a,u,h,i)}t.Tween=c,c.prototype={constructor:c,init:function(p,a,u,h,i,m){this.elem=p,this.prop=u,this.easing=i||t.easing._default,this.options=a,this.start=this.now=this.cur(),this.end=h,this.unit=m||(t.cssNumber[u]?"":"px")},cur:function(){var p=c.propHooks[this.prop];return p&&p.get?p.get(this):c.propHooks._default.get(this)},run:function(p){var a,u=c.propHooks[this.prop];return this.options.duration?this.pos=a=t.easing[this.easing](p,this.options.duration*p,0,1,this.options.duration):this.pos=a=p,this.now=(this.end-this.start)*a+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),u&&u.set?u.set(this):c.propHooks._default.set(this),this}},c.prototype.init.prototype=c.prototype,c.propHooks={_default:{get:function(p){var a;return p.elem.nodeType!==1||p.elem[p.prop]!=null&&p.elem.style[p.prop]==null?p.elem[p.prop]:(a=t.css(p.elem,p.prop,""),!a||a==="auto"?0:a)},set:function(p){t.fx.step[p.prop]?t.fx.step[p.prop](p):p.elem.nodeType===1&&(t.cssHooks[p.prop]||p.elem.style[l(p.prop)]!=null)?t.style(p.elem,p.prop,p.now+p.unit):p.elem[p.prop]=p.now}}},c.propHooks.scrollTop=c.propHooks.scrollLeft={set:function(p){p.elem.nodeType&&p.elem.parentNode&&(p.elem[p.prop]=p.now)}},t.easing={linear:function(p){return p},swing:function(p){return .5-Math.cos(p*Math.PI)/2},_default:"swing"},t.fx=c.prototype.init,t.fx.step={}}.apply(y,g),r!==void 0&&(P.exports=r)},9748:(P,y,o)=>{var g,r;g=[o(6934),o(3670),o(4519)],r=function(t){"use strict";t.expr.pseudos.animated=function(l){return t.grep(t.timers,function(c){return l===c.elem}).length}}.apply(y,g),r!==void 0&&(P.exports=r)},4833:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(4042),o(8954),o(6258),o(4556),o(7451),o(1289),o(1535),o(8251),o(852),o(3670)],r=function(t,l,c,p,a,u,h,i,m,d){"use strict";var f=/^([^.]*)(?:\.(.+)|)/;function v(){return!0}function _(){return!1}function A(b,x){return b===C()==(x==="focus")}function C(){try{return l.activeElement}catch(b){}}function E(b,x,D,N,I,w){var O,B;if(typeof x=="object"){typeof D!="string"&&(N=N||D,D=void 0);for(B in x)E(b,B,D,N,x[B],w);return b}if(N==null&&I==null?(I=D,N=D=void 0):I==null&&(typeof D=="string"?(I=N,N=void 0):(I=N,N=D,D=void 0)),I===!1)I=_;else if(!I)return b;return w===1&&(O=I,I=function(U){return t().off(U),O.apply(this,arguments)},I.guid=O.guid||(O.guid=t.guid++)),b.each(function(){t.event.add(this,x,I,N,D)})}t.event={global:{},add:function(b,x,D,N,I){var w,O,B,U,L,F,Y,z,G,W,te,oe=m.get(b);if(!!i(b))for(D.handler&&(w=D,D=w.handler,I=w.selector),I&&t.find.matchesSelector(c,I),D.guid||(D.guid=t.guid++),(U=oe.events)||(U=oe.events=Object.create(null)),(O=oe.handle)||(O=oe.handle=function(ge){return typeof t!="undefined"&&t.event.triggered!==ge.type?t.event.dispatch.apply(b,arguments):void 0}),x=(x||"").match(a)||[""],L=x.length;L--;)B=f.exec(x[L])||[],G=te=B[1],W=(B[2]||"").split(".").sort(),!!G&&(Y=t.event.special[G]||{},G=(I?Y.delegateType:Y.bindType)||G,Y=t.event.special[G]||{},F=t.extend({type:G,origType:te,data:N,handler:D,guid:D.guid,selector:I,needsContext:I&&t.expr.match.needsContext.test(I),namespace:W.join(".")},w),(z=U[G])||(z=U[G]=[],z.delegateCount=0,(!Y.setup||Y.setup.call(b,N,W,O)===!1)&&b.addEventListener&&b.addEventListener(G,O)),Y.add&&(Y.add.call(b,F),F.handler.guid||(F.handler.guid=D.guid)),I?z.splice(z.delegateCount++,0,F):z.push(F),t.event.global[G]=!0)},remove:function(b,x,D,N,I){var w,O,B,U,L,F,Y,z,G,W,te,oe=m.hasData(b)&&m.get(b);if(!(!oe||!(U=oe.events))){for(x=(x||"").match(a)||[""],L=x.length;L--;){if(B=f.exec(x[L])||[],G=te=B[1],W=(B[2]||"").split(".").sort(),!G){for(G in U)t.event.remove(b,G+x[L],D,N,!0);continue}for(Y=t.event.special[G]||{},G=(N?Y.delegateType:Y.bindType)||G,z=U[G]||[],B=B[2]&&new RegExp("(^|\\.)"+W.join("\\.(?:.*\\.|)")+"(\\.|$)"),O=w=z.length;w--;)F=z[w],(I||te===F.origType)&&(!D||D.guid===F.guid)&&(!B||B.test(F.namespace))&&(!N||N===F.selector||N==="**"&&F.selector)&&(z.splice(w,1),F.selector&&z.delegateCount--,Y.remove&&Y.remove.call(b,F));O&&!z.length&&((!Y.teardown||Y.teardown.call(b,W,oe.handle)===!1)&&t.removeEvent(b,G,oe.handle),delete U[G])}t.isEmptyObject(U)&&m.remove(b,"handle events")}},dispatch:function(b){var x,D,N,I,w,O,B=new Array(arguments.length),U=t.event.fix(b),L=(m.get(this,"events")||Object.create(null))[U.type]||[],F=t.event.special[U.type]||{};for(B[0]=U,x=1;x<arguments.length;x++)B[x]=arguments[x];if(U.delegateTarget=this,!(F.preDispatch&&F.preDispatch.call(this,U)===!1)){for(O=t.event.handlers.call(this,U,L),x=0;(I=O[x++])&&!U.isPropagationStopped();)for(U.currentTarget=I.elem,D=0;(w=I.handlers[D++])&&!U.isImmediatePropagationStopped();)(!U.rnamespace||w.namespace===!1||U.rnamespace.test(w.namespace))&&(U.handleObj=w,U.data=w.data,N=((t.event.special[w.origType]||{}).handle||w.handler).apply(I.elem,B),N!==void 0&&(U.result=N)===!1&&(U.preventDefault(),U.stopPropagation()));return F.postDispatch&&F.postDispatch.call(this,U),U.result}},handlers:function(b,x){var D,N,I,w,O,B=[],U=x.delegateCount,L=b.target;if(U&&L.nodeType&&!(b.type==="click"&&b.button>=1)){for(;L!==this;L=L.parentNode||this)if(L.nodeType===1&&!(b.type==="click"&&L.disabled===!0)){for(w=[],O={},D=0;D<U;D++)N=x[D],I=N.selector+" ",O[I]===void 0&&(O[I]=N.needsContext?t(I,this).index(L)>-1:t.find(I,this,null,[L]).length),O[I]&&w.push(N);w.length&&B.push({elem:L,handlers:w})}}return L=this,U<x.length&&B.push({elem:L,handlers:x.slice(U)}),B},addProp:function(b,x){Object.defineProperty(t.Event.prototype,b,{enumerable:!0,configurable:!0,get:p(x)?function(){if(this.originalEvent)return x(this.originalEvent)}:function(){if(this.originalEvent)return this.originalEvent[b]},set:function(D){Object.defineProperty(this,b,{enumerable:!0,configurable:!0,writable:!0,value:D})}})},fix:function(b){return b[t.expando]?b:new t.Event(b)},special:{load:{noBubble:!0},click:{setup:function(b){var x=this||b;return u.test(x.type)&&x.click&&d(x,"input")&&S(x,"click",v),!1},trigger:function(b){var x=this||b;return u.test(x.type)&&x.click&&d(x,"input")&&S(x,"click"),!0},_default:function(b){var x=b.target;return u.test(x.type)&&x.click&&d(x,"input")&&m.get(x,"click")||d(x,"a")}},beforeunload:{postDispatch:function(b){b.result!==void 0&&b.originalEvent&&(b.originalEvent.returnValue=b.result)}}}};function S(b,x,D){if(!D){m.get(b,x)===void 0&&t.event.add(b,x,v);return}m.set(b,x,!1),t.event.add(b,x,{namespace:!1,handler:function(N){var I,w,O=m.get(this,x);if(N.isTrigger&1&&this[x]){if(O.length)(t.event.special[x]||{}).delegateType&&N.stopPropagation();else if(O=h.call(arguments),m.set(this,x,O),I=D(this,x),this[x](),w=m.get(this,x),O!==w||I?m.set(this,x,!1):w={},O!==w)return N.stopImmediatePropagation(),N.preventDefault(),w&&w.value}else O.length&&(m.set(this,x,{value:t.event.trigger(t.extend(O[0],t.Event.prototype),O.slice(1),this)}),N.stopImmediatePropagation())}})}return t.removeEvent=function(b,x,D){b.removeEventListener&&b.removeEventListener(x,D)},t.Event=function(b,x){if(!(this instanceof t.Event))return new t.Event(b,x);b&&b.type?(this.originalEvent=b,this.type=b.type,this.isDefaultPrevented=b.defaultPrevented||b.defaultPrevented===void 0&&b.returnValue===!1?v:_,this.target=b.target&&b.target.nodeType===3?b.target.parentNode:b.target,this.currentTarget=b.currentTarget,this.relatedTarget=b.relatedTarget):this.type=b,x&&t.extend(this,x),this.timeStamp=b&&b.timeStamp||Date.now(),this[t.expando]=!0},t.Event.prototype={constructor:t.Event,isDefaultPrevented:_,isPropagationStopped:_,isImmediatePropagationStopped:_,isSimulated:!1,preventDefault:function(){var b=this.originalEvent;this.isDefaultPrevented=v,b&&!this.isSimulated&&b.preventDefault()},stopPropagation:function(){var b=this.originalEvent;this.isPropagationStopped=v,b&&!this.isSimulated&&b.stopPropagation()},stopImmediatePropagation:function(){var b=this.originalEvent;this.isImmediatePropagationStopped=v,b&&!this.isSimulated&&b.stopImmediatePropagation(),this.stopPropagation()}},t.each({altKey:!0,bubbles:!0,cancelable:!0,changedTouches:!0,ctrlKey:!0,detail:!0,eventPhase:!0,metaKey:!0,pageX:!0,pageY:!0,shiftKey:!0,view:!0,char:!0,code:!0,charCode:!0,key:!0,keyCode:!0,button:!0,buttons:!0,clientX:!0,clientY:!0,offsetX:!0,offsetY:!0,pointerId:!0,pointerType:!0,screenX:!0,screenY:!0,targetTouches:!0,toElement:!0,touches:!0,which:!0},t.event.addProp),t.each({focus:"focusin",blur:"focusout"},function(b,x){t.event.special[b]={setup:function(){return S(this,b,A),!1},trigger:function(){return S(this,b),!0},_default:function(){return!0},delegateType:x}}),t.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(b,x){t.event.special[b]={delegateType:x,bindType:x,handle:function(D){var N,I=this,w=D.relatedTarget,O=D.handleObj;return(!w||w!==I&&!t.contains(I,w))&&(D.type=O.origType,N=O.handler.apply(this,arguments),D.type=x),N}}}),t.fn.extend({on:function(b,x,D,N){return E(this,b,x,D,N)},one:function(b,x,D,N){return E(this,b,x,D,N,1)},off:function(b,x,D){var N,I;if(b&&b.preventDefault&&b.handleObj)return N=b.handleObj,t(b.delegateTarget).off(N.namespace?N.origType+"."+N.namespace:N.origType,N.selector,N.handler),this;if(typeof b=="object"){for(I in b)this.off(I,x,b[I]);return this}return(x===!1||typeof x=="function")&&(D=x,x=void 0),D===!1&&(D=_),this.each(function(){t.event.remove(this,b,D,x)})}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},1244:(P,y,o)=>{var g,r;g=[o(6934),o(1535),o(7429),o(4833),o(4505)],r=function(t,l,c){"use strict";return c.focusin||t.each({focus:"focusin",blur:"focusout"},function(p,a){var u=function(h){t.event.simulate(a,h.target,t.event.fix(h))};t.event.special[a]={setup:function(){var h=this.ownerDocument||this.document||this,i=l.access(h,a);i||h.addEventListener(p,u,!0),l.access(h,a,(i||0)+1)},teardown:function(){var h=this.ownerDocument||this.document||this,i=l.access(h,a)-1;i?l.access(h,a,i):(h.removeEventListener(p,u,!0),l.remove(h,a))}}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},7429:(P,y,o)=>{var g,r;g=[o(7511)],r=function(t){"use strict";return t.focusin="onfocusin"in window,t}.apply(y,g),r!==void 0&&(P.exports=r)},4505:(P,y,o)=>{var g,r;g=[o(6934),o(3540),o(1535),o(1289),o(5862),o(8954),o(8194),o(4833)],r=function(t,l,c,p,a,u,h){"use strict";var i=/^(?:focusinfocus|focusoutblur)$/,m=function(d){d.stopPropagation()};return t.extend(t.event,{trigger:function(d,f,v,_){var A,C,E,S,b,x,D,N,I=[v||l],w=a.call(d,"type")?d.type:d,O=a.call(d,"namespace")?d.namespace.split("."):[];if(C=N=E=v=v||l,!(v.nodeType===3||v.nodeType===8)&&!i.test(w+t.event.triggered)&&(w.indexOf(".")>-1&&(O=w.split("."),w=O.shift(),O.sort()),b=w.indexOf(":")<0&&"on"+w,d=d[t.expando]?d:new t.Event(w,typeof d=="object"&&d),d.isTrigger=_?2:3,d.namespace=O.join("."),d.rnamespace=d.namespace?new RegExp("(^|\\.)"+O.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,d.result=void 0,d.target||(d.target=v),f=f==null?[d]:t.makeArray(f,[d]),D=t.event.special[w]||{},!(!_&&D.trigger&&D.trigger.apply(v,f)===!1))){if(!_&&!D.noBubble&&!h(v)){for(S=D.delegateType||w,i.test(S+w)||(C=C.parentNode);C;C=C.parentNode)I.push(C),E=C;E===(v.ownerDocument||l)&&I.push(E.defaultView||E.parentWindow||window)}for(A=0;(C=I[A++])&&!d.isPropagationStopped();)N=C,d.type=A>1?S:D.bindType||w,x=(c.get(C,"events")||Object.create(null))[d.type]&&c.get(C,"handle"),x&&x.apply(C,f),x=b&&C[b],x&&x.apply&&p(C)&&(d.result=x.apply(C,f),d.result===!1&&d.preventDefault());return d.type=w,!_&&!d.isDefaultPrevented()&&(!D._default||D._default.apply(I.pop(),f)===!1)&&p(v)&&b&&u(v[w])&&!h(v)&&(E=v[b],E&&(v[b]=null),t.event.triggered=w,d.isPropagationStopped()&&N.addEventListener(w,m),v[w](),d.isPropagationStopped()&&N.removeEventListener(w,m),t.event.triggered=void 0,E&&(v[b]=E)),d.result}},simulate:function(d,f,v){var _=t.extend(new t.Event,v,{type:d,isSimulated:!0});t.event.trigger(_,null,f)}}),t.fn.extend({trigger:function(d,f){return this.each(function(){t.event.trigger(d,f,this)})},triggerHandler:function(d,f){var v=this[0];if(v)return t.event.trigger(d,f,v,!0)}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},6056:(P,y,o)=>{var g,r,g,r;g=[o(6934)],r=function(t){"use strict";g=[],r=function(){return t}.apply(y,g),r!==void 0&&(P.exports=r)}.apply(y,g),r!==void 0&&(P.exports=r)},1392:(P,y,o)=>{var g,r;g=[o(6934)],r=function(t){"use strict";var l=window.jQuery,c=window.$;t.noConflict=function(p){return window.$===t&&(window.$=c),p&&window.jQuery===t&&(window.jQuery=l),t},typeof noGlobal=="undefined"&&(window.jQuery=window.$=t)}.apply(y,g),r!==void 0&&(P.exports=r)},3766:(P,y,o)=>{var g,r;g=[o(6934),o(3670),o(4048),o(5367),o(2599),o(2335),o(5832),o(4569),o(1261),o(5094),o(1159),o(4833),o(1244),o(4819),o(2772),o(8495),o(3035),o(3241),o(5210),o(8857),o(8838),o(9155),o(3150),o(5774),o(5214),o(5109),o(4519),o(9748),o(7743),o(1327),o(7454),o(6056),o(1392)],r=function(t){"use strict";return t}.apply(y,g),r!==void 0&&(P.exports=r)},4819:(P,y,o)=>{var g,r;g=[o(6934),o(9203),o(5115),o(8954),o(8076),o(4556),o(1619),o(2195),o(9440),o(9019),o(2188),o(4279),o(6993),o(9707),o(1535),o(6141),o(1289),o(294),o(8251),o(852),o(4048),o(3670),o(4833)],r=function(t,l,c,p,a,u,h,i,m,d,f,v,_,A,C,E,S,b,x){"use strict";var D=/<script|<style|<link/i,N=/checked\s*(?:[^=]|=\s*.checked.)/i,I=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;function w(z,G){return x(z,"table")&&x(G.nodeType!==11?G:G.firstChild,"tr")&&t(z).children("tbody")[0]||z}function O(z){return z.type=(z.getAttribute("type")!==null)+"/"+z.type,z}function B(z){return(z.type||"").slice(0,5)==="true/"?z.type=z.type.slice(5):z.removeAttribute("type"),z}function U(z,G){var W,te,oe,ge,Q,Te,Pe;if(G.nodeType===1){if(C.hasData(z)&&(ge=C.get(z),Pe=ge.events,Pe)){C.remove(G,"handle events");for(oe in Pe)for(W=0,te=Pe[oe].length;W<te;W++)t.event.add(G,oe,Pe[oe][W])}E.hasData(z)&&(Q=E.access(z),Te=t.extend({},Q),E.set(G,Te))}}function L(z,G){var W=G.nodeName.toLowerCase();W==="input"&&u.test(z.type)?G.checked=z.checked:(W==="input"||W==="textarea")&&(G.defaultValue=z.defaultValue)}function F(z,G,W,te){G=c(G);var oe,ge,Q,Te,Pe,Ge,mn=0,Rn=z.length,Dn=Rn-1,Nn=G[0],jn=p(Nn);if(jn||Rn>1&&typeof Nn=="string"&&!A.checkClone&&N.test(Nn))return z.each(function(je){var Ln=z.eq(je);jn&&(G[0]=Nn.call(this,je,Ln.html())),F(Ln,G,W,te)});if(Rn&&(oe=_(G,z[0].ownerDocument,!1,z,te),ge=oe.firstChild,oe.childNodes.length===1&&(oe=ge),ge||te)){for(Q=t.map(f(oe,"script"),O),Te=Q.length;mn<Rn;mn++)Pe=oe,mn!==Dn&&(Pe=t.clone(Pe,!0,!0),Te&&t.merge(Q,f(Pe,"script"))),W.call(z[mn],Pe,mn);if(Te)for(Ge=Q[Q.length-1].ownerDocument,t.map(Q,B),mn=0;mn<Te;mn++)Pe=Q[mn],m.test(Pe.type||"")&&!C.access(Pe,"globalEval")&&t.contains(Ge,Pe)&&(Pe.src&&(Pe.type||"").toLowerCase()!=="module"?t._evalUrl&&!Pe.noModule&&t._evalUrl(Pe.src,{nonce:Pe.nonce||Pe.getAttribute("nonce")},Ge):b(Pe.textContent.replace(I,""),Pe,Ge))}return z}function Y(z,G,W){for(var te,oe=G?t.filter(G,z):z,ge=0;(te=oe[ge])!=null;ge++)!W&&te.nodeType===1&&t.cleanData(f(te)),te.parentNode&&(W&&l(te)&&v(f(te,"script")),te.parentNode.removeChild(te));return z}return t.extend({htmlPrefilter:function(z){return z},clone:function(z,G,W){var te,oe,ge,Q,Te=z.cloneNode(!0),Pe=l(z);if(!A.noCloneChecked&&(z.nodeType===1||z.nodeType===11)&&!t.isXMLDoc(z))for(Q=f(Te),ge=f(z),te=0,oe=ge.length;te<oe;te++)L(ge[te],Q[te]);if(G)if(W)for(ge=ge||f(z),Q=Q||f(Te),te=0,oe=ge.length;te<oe;te++)U(ge[te],Q[te]);else U(z,Te);return Q=f(Te,"script"),Q.length>0&&v(Q,!Pe&&f(z,"script")),Te},cleanData:function(z){for(var G,W,te,oe=t.event.special,ge=0;(W=z[ge])!==void 0;ge++)if(S(W)){if(G=W[C.expando]){if(G.events)for(te in G.events)oe[te]?t.event.remove(W,te):t.removeEvent(W,te,G.handle);W[C.expando]=void 0}W[E.expando]&&(W[E.expando]=void 0)}}}),t.fn.extend({detach:function(z){return Y(this,z,!0)},remove:function(z){return Y(this,z)},text:function(z){return h(this,function(G){return G===void 0?t.text(this):this.empty().each(function(){(this.nodeType===1||this.nodeType===11||this.nodeType===9)&&(this.textContent=G)})},null,z,arguments.length)},append:function(){return F(this,arguments,function(z){if(this.nodeType===1||this.nodeType===11||this.nodeType===9){var G=w(this,z);G.appendChild(z)}})},prepend:function(){return F(this,arguments,function(z){if(this.nodeType===1||this.nodeType===11||this.nodeType===9){var G=w(this,z);G.insertBefore(z,G.firstChild)}})},before:function(){return F(this,arguments,function(z){this.parentNode&&this.parentNode.insertBefore(z,this)})},after:function(){return F(this,arguments,function(z){this.parentNode&&this.parentNode.insertBefore(z,this.nextSibling)})},empty:function(){for(var z,G=0;(z=this[G])!=null;G++)z.nodeType===1&&(t.cleanData(f(z,!1)),z.textContent="");return this},clone:function(z,G){return z=z==null?!1:z,G=G==null?z:G,this.map(function(){return t.clone(this,z,G)})},html:function(z){return h(this,function(G){var W=this[0]||{},te=0,oe=this.length;if(G===void 0&&W.nodeType===1)return W.innerHTML;if(typeof G=="string"&&!D.test(G)&&!d[(i.exec(G)||["",""])[1].toLowerCase()]){G=t.htmlPrefilter(G);try{for(;te<oe;te++)W=this[te]||{},W.nodeType===1&&(t.cleanData(f(W,!1)),W.innerHTML=G);W=0}catch(ge){}}W&&this.empty().append(G)},null,z,arguments.length)},replaceWith:function(){var z=[];return F(this,arguments,function(G){var W=this.parentNode;t.inArray(this,z)<0&&(t.cleanData(f(this)),W&&W.replaceChild(G,this))},z)}}),t.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(z,G){t.fn[z]=function(W){for(var te,oe=[],ge=t(W),Q=ge.length-1,Te=0;Te<=Q;Te++)te=Te===Q?this:this.clone(!0),t(ge[Te])[G](te),a.apply(oe,te.get());return this.pushStack(oe)}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},2772:(P,y,o)=>{var g,r;g=[o(8857)],r=function(t){"use strict";return t._evalUrl=function(l,c,p){return t.ajax({url:l,type:"GET",dataType:"script",cache:!0,async:!1,global:!1,converters:{"text script":function(){}},dataFilter:function(a){t.globalEval(a,c,p)}})},t._evalUrl}.apply(y,g),r!==void 0&&(P.exports=r)},6993:(P,y,o)=>{var g,r;g=[o(6934),o(6627),o(9203),o(2195),o(9440),o(9019),o(2188),o(4279)],r=function(t,l,c,p,a,u,h,i){"use strict";var m=/<|&#?\w+;/;function d(f,v,_,A,C){for(var E,S,b,x,D,N,I=v.createDocumentFragment(),w=[],O=0,B=f.length;O<B;O++)if(E=f[O],E||E===0)if(l(E)==="object")t.merge(w,E.nodeType?[E]:E);else if(!m.test(E))w.push(v.createTextNode(E));else{for(S=S||I.appendChild(v.createElement("div")),b=(p.exec(E)||["",""])[1].toLowerCase(),x=u[b]||u._default,S.innerHTML=x[1]+t.htmlPrefilter(E)+x[2],N=x[0];N--;)S=S.lastChild;t.merge(w,S.childNodes),S=I.firstChild,S.textContent=""}for(I.textContent="",O=0;E=w[O++];){if(A&&t.inArray(E,A)>-1){C&&C.push(E);continue}if(D=c(E),S=h(I.appendChild(E),"script"),D&&i(S),_)for(N=0;E=S[N++];)a.test(E.type||"")&&_.push(E)}return I}return d}.apply(y,g),r!==void 0&&(P.exports=r)},2188:(P,y,o)=>{var g,r;g=[o(6934),o(8251)],r=function(t,l){"use strict";function c(p,a){var u;return typeof p.getElementsByTagName!="undefined"?u=p.getElementsByTagName(a||"*"):typeof p.querySelectorAll!="undefined"?u=p.querySelectorAll(a||"*"):u=[],a===void 0||a&&l(p,a)?t.merge([p],u):u}return c}.apply(y,g),r!==void 0&&(P.exports=r)},4279:(P,y,o)=>{var g,r;g=[o(1535)],r=function(t){"use strict";function l(c,p){for(var a=0,u=c.length;a<u;a++)t.set(c[a],"globalEval",!p||t.get(p[a],"globalEval"))}return l}.apply(y,g),r!==void 0&&(P.exports=r)},9707:(P,y,o)=>{var g,r;g=[o(3540),o(7511)],r=function(t,l){"use strict";return function(){var c=t.createDocumentFragment(),p=c.appendChild(t.createElement("div")),a=t.createElement("input");a.setAttribute("type","radio"),a.setAttribute("checked","checked"),a.setAttribute("name","t"),p.appendChild(a),l.checkClone=p.cloneNode(!0).cloneNode(!0).lastChild.checked,p.innerHTML="<textarea>x</textarea>",l.noCloneChecked=!!p.cloneNode(!0).lastChild.defaultValue,p.innerHTML="<option></option>",l.option=!!p.lastChild}(),l}.apply(y,g),r!==void 0&&(P.exports=r)},9440:(P,y,o)=>{var g;g=function(){"use strict";return/^$|^module$|\/(?:java|ecma)script/i}.call(y,o,y,P),g!==void 0&&(P.exports=g)},2195:(P,y,o)=>{var g;g=function(){"use strict";return/<([a-z][^\/\0>\x20\t\r\n\f]*)/i}.call(y,o,y,P),g!==void 0&&(P.exports=g)},9019:(P,y,o)=>{var g,r;g=[o(9707)],r=function(t){"use strict";var l={thead:[1,"<table>","</table>"],col:[2,"<table><colgroup>","</colgroup></table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]};return l.tbody=l.tfoot=l.colgroup=l.caption=l.thead,l.th=l.td,t.option||(l.optgroup=l.option=[1,"<select multiple='multiple'>","</select>"]),l}.apply(y,g),r!==void 0&&(P.exports=r)},7743:(P,y,o)=>{var g,r;g=[o(6934),o(1619),o(4042),o(8954),o(4830),o(4454),o(4326),o(3087),o(8194),o(852),o(3035),o(3670)],r=function(t,l,c,p,a,u,h,i,m){"use strict";return t.offset={setOffset:function(d,f,v){var _,A,C,E,S,b,x,D=t.css(d,"position"),N=t(d),I={};D==="static"&&(d.style.position="relative"),S=N.offset(),C=t.css(d,"top"),b=t.css(d,"left"),x=(D==="absolute"||D==="fixed")&&(C+b).indexOf("auto")>-1,x?(_=N.position(),E=_.top,A=_.left):(E=parseFloat(C)||0,A=parseFloat(b)||0),p(f)&&(f=f.call(d,v,t.extend({},S))),f.top!=null&&(I.top=f.top-S.top+E),f.left!=null&&(I.left=f.left-S.left+A),"using"in f?f.using.call(d,I):N.css(I)}},t.fn.extend({offset:function(d){if(arguments.length)return d===void 0?this:this.each(function(A){t.offset.setOffset(this,d,A)});var f,v,_=this[0];if(!!_)return _.getClientRects().length?(f=_.getBoundingClientRect(),v=_.ownerDocument.defaultView,{top:f.top+v.pageYOffset,left:f.left+v.pageXOffset}):{top:0,left:0}},position:function(){if(!!this[0]){var d,f,v,_=this[0],A={top:0,left:0};if(t.css(_,"position")==="fixed")f=_.getBoundingClientRect();else{for(f=this.offset(),v=_.ownerDocument,d=_.offsetParent||v.documentElement;d&&(d===v.body||d===v.documentElement)&&t.css(d,"position")==="static";)d=d.parentNode;d&&d!==_&&d.nodeType===1&&(A=t(d).offset(),A.top+=t.css(d,"borderTopWidth",!0),A.left+=t.css(d,"borderLeftWidth",!0))}return{top:f.top-A.top-t.css(_,"marginTop",!0),left:f.left-A.left-t.css(_,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){for(var d=this.offsetParent;d&&t.css(d,"position")==="static";)d=d.offsetParent;return d||c})}}),t.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(d,f){var v=f==="pageYOffset";t.fn[d]=function(_){return l(this,function(A,C,E){var S;if(m(A)?S=A:A.nodeType===9&&(S=A.defaultView),E===void 0)return S?S[f]:A[C];S?S.scrollTo(v?S.pageXOffset:E,v?E:S.pageYOffset):A[C]=E},d,_,arguments.length)}}),t.each(["top","left"],function(d,f){t.cssHooks[f]=h(i.pixelPosition,function(v,_){if(_)return _=u(v,f),a.test(_)?t(v).position()[f]+"px":_})}),t}.apply(y,g),r!==void 0&&(P.exports=r)},1261:(P,y,o)=>{var g,r;g=[o(6934),o(1535),o(2599),o(5367)],r=function(t,l){"use strict";return t.extend({queue:function(c,p,a){var u;if(c)return p=(p||"fx")+"queue",u=l.get(c,p),a&&(!u||Array.isArray(a)?u=l.access(c,p,t.makeArray(a)):u.push(a)),u||[]},dequeue:function(c,p){p=p||"fx";var a=t.queue(c,p),u=a.length,h=a.shift(),i=t._queueHooks(c,p),m=function(){t.dequeue(c,p)};h==="inprogress"&&(h=a.shift(),u--),h&&(p==="fx"&&a.unshift("inprogress"),delete i.stop,h.call(c,m,i)),!u&&i&&i.empty.fire()},_queueHooks:function(c,p){var a=p+"queueHooks";return l.get(c,a)||l.access(c,a,{empty:t.Callbacks("once memory").add(function(){l.remove(c,[p+"queue",a])})})}}),t.fn.extend({queue:function(c,p){var a=2;return typeof c!="string"&&(p=c,c="fx",a--),arguments.length<a?t.queue(this[0],c):p===void 0?this:this.each(function(){var u=t.queue(this,c,p);t._queueHooks(this,c),c==="fx"&&u[0]!=="inprogress"&&t.dequeue(this,c)})},dequeue:function(c){return this.each(function(){t.dequeue(this,c)})},clearQueue:function(c){return this.queue(c||"fx",[])},promise:function(c,p){var a,u=1,h=t.Deferred(),i=this,m=this.length,d=function(){--u||h.resolveWith(i,[i])};for(typeof c!="string"&&(p=c,c=void 0),c=c||"fx";m--;)a=l.get(i[m],c+"queueHooks"),a&&a.empty&&(u++,a.empty.add(d));return d(),h.promise(p)}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},5094:(P,y,o)=>{var g,r;g=[o(6934),o(1261),o(4519)],r=function(t){"use strict";return t.fn.delay=function(l,c){return l=t.fx&&t.fx.speeds[l]||l,c=c||"fx",this.queue(c,function(p,a){var u=window.setTimeout(p,l);a.stop=function(){window.clearTimeout(u)}})},t.fn.delay}.apply(y,g),r!==void 0&&(P.exports=r)},8195:(P,y,o)=>{var g,r;g=[o(6934),o(6601)],r=function(t,l){"use strict";t.find=l,t.expr=l.selectors,t.expr[":"]=t.expr.pseudos,t.uniqueSort=t.unique=l.uniqueSort,t.text=l.getText,t.isXMLDoc=l.isXML,t.contains=l.contains,t.escapeSelector=l.escape}.apply(y,g),r!==void 0&&(P.exports=r)},3670:(P,y,o)=>{var g,r;g=[o(8195)],r=function(){"use strict"}.apply(y,g),r!==void 0&&(P.exports=r)},5210:(P,y,o)=>{var g,r;g=[o(6934),o(6627),o(4556),o(8954),o(852),o(4048),o(6799)],r=function(t,l,c,p){"use strict";var a=/\[\]$/,u=/\r?\n/g,h=/^(?:submit|button|image|reset|file)$/i,i=/^(?:input|select|textarea|keygen)/i;function m(d,f,v,_){var A;if(Array.isArray(f))t.each(f,function(C,E){v||a.test(d)?_(d,E):m(d+"["+(typeof E=="object"&&E!=null?C:"")+"]",E,v,_)});else if(!v&&l(f)==="object")for(A in f)m(d+"["+A+"]",f[A],v,_);else _(d,f)}return t.param=function(d,f){var v,_=[],A=function(C,E){var S=p(E)?E():E;_[_.length]=encodeURIComponent(C)+"="+encodeURIComponent(S==null?"":S)};if(d==null)return"";if(Array.isArray(d)||d.jquery&&!t.isPlainObject(d))t.each(d,function(){A(this.name,this.value)});else for(v in d)m(v,d[v],f,A);return _.join("&")},t.fn.extend({serialize:function(){return t.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var d=t.prop(this,"elements");return d?t.makeArray(d):this}).filter(function(){var d=this.type;return this.name&&!t(this).is(":disabled")&&i.test(this.nodeName)&&!h.test(d)&&(this.checked||!c.test(d))}).map(function(d,f){var v=t(this).val();return v==null?null:Array.isArray(v)?t.map(v,function(_){return{name:f.name,value:_.replace(u,`\r
`)}}):{name:f.name,value:v.replace(u,`\r
`)}}).get()}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},4048:(P,y,o)=>{var g,r;g=[o(6934),o(1410),o(7337),o(6237),o(1),o(7347),o(8251),o(852),o(6441),o(3670)],r=function(t,l,c,p,a,u,h){"use strict";var i=/^(?:parents|prev(?:Until|All))/,m={children:!0,contents:!0,next:!0,prev:!0};t.fn.extend({has:function(f){var v=t(f,this),_=v.length;return this.filter(function(){for(var A=0;A<_;A++)if(t.contains(this,v[A]))return!0})},closest:function(f,v){var _,A=0,C=this.length,E=[],S=typeof f!="string"&&t(f);if(!u.test(f)){for(;A<C;A++)for(_=this[A];_&&_!==v;_=_.parentNode)if(_.nodeType<11&&(S?S.index(_)>-1:_.nodeType===1&&t.find.matchesSelector(_,f))){E.push(_);break}}return this.pushStack(E.length>1?t.uniqueSort(E):E)},index:function(f){return f?typeof f=="string"?c.call(t(f),this[0]):c.call(this,f.jquery?f[0]:f):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(f,v){return this.pushStack(t.uniqueSort(t.merge(this.get(),t(f,v))))},addBack:function(f){return this.add(f==null?this.prevObject:this.prevObject.filter(f))}});function d(f,v){for(;(f=f[v])&&f.nodeType!==1;);return f}return t.each({parent:function(f){var v=f.parentNode;return v&&v.nodeType!==11?v:null},parents:function(f){return p(f,"parentNode")},parentsUntil:function(f,v,_){return p(f,"parentNode",_)},next:function(f){return d(f,"nextSibling")},prev:function(f){return d(f,"previousSibling")},nextAll:function(f){return p(f,"nextSibling")},prevAll:function(f){return p(f,"previousSibling")},nextUntil:function(f,v,_){return p(f,"nextSibling",_)},prevUntil:function(f,v,_){return p(f,"previousSibling",_)},siblings:function(f){return a((f.parentNode||{}).firstChild,f)},children:function(f){return a(f.firstChild)},contents:function(f){return f.contentDocument!=null&&l(f.contentDocument)?f.contentDocument:(h(f,"template")&&(f=f.content||f),t.merge([],f.childNodes))}},function(f,v){t.fn[f]=function(_,A){var C=t.map(this,v,_);return f.slice(-5)!=="Until"&&(A=_),A&&typeof A=="string"&&(C=t.filter(A,C)),this.length>1&&(m[f]||t.uniqueSort(C),i.test(f)&&C.reverse()),this.pushStack(C)}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},6441:(P,y,o)=>{var g,r;g=[o(6934),o(7337),o(8954),o(7347),o(3670)],r=function(t,l,c,p){"use strict";function a(u,h,i){return c(h)?t.grep(u,function(m,d){return!!h.call(m,d,m)!==i}):h.nodeType?t.grep(u,function(m){return m===h!==i}):typeof h!="string"?t.grep(u,function(m){return l.call(h,m)>-1!==i}):t.filter(h,u,i)}t.filter=function(u,h,i){var m=h[0];return i&&(u=":not("+u+")"),h.length===1&&m.nodeType===1?t.find.matchesSelector(m,u)?[m]:[]:t.find.matches(u,t.grep(h,function(d){return d.nodeType===1}))},t.fn.extend({find:function(u){var h,i,m=this.length,d=this;if(typeof u!="string")return this.pushStack(t(u).filter(function(){for(h=0;h<m;h++)if(t.contains(d[h],this))return!0}));for(i=this.pushStack([]),h=0;h<m;h++)t.find(u,d[h],i);return m>1?t.uniqueSort(i):i},filter:function(u){return this.pushStack(a(this,u||[],!1))},not:function(u){return this.pushStack(a(this,u||[],!0))},is:function(u){return!!a(this,typeof u=="string"&&p.test(u)?t(u):u||[],!1).length}})}.apply(y,g),r!==void 0&&(P.exports=r)},6237:(P,y,o)=>{var g,r;g=[o(6934)],r=function(t){"use strict";return function(l,c,p){for(var a=[],u=p!==void 0;(l=l[c])&&l.nodeType!==9;)if(l.nodeType===1){if(u&&t(l).is(p))break;a.push(l)}return a}}.apply(y,g),r!==void 0&&(P.exports=r)},7347:(P,y,o)=>{var g,r;g=[o(6934),o(3670)],r=function(t){"use strict";return t.expr.match.needsContext}.apply(y,g),r!==void 0&&(P.exports=r)},1:(P,y,o)=>{var g;g=function(){"use strict";return function(r,t){for(var l=[];r;r=r.nextSibling)r.nodeType===1&&r!==t&&l.push(r);return l}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},21:(P,y,o)=>{var g,r;g=[o(6704)],r=function(t){"use strict";return t.call(Object)}.apply(y,g),r!==void 0&&(P.exports=r)},9929:(P,y,o)=>{var g;g=function(){"use strict";return[]}.call(y,o,y,P),g!==void 0&&(P.exports=g)},8002:(P,y,o)=>{var g;g=function(){"use strict";return{}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},3540:(P,y,o)=>{var g;g=function(){"use strict";return window.document}.call(y,o,y,P),g!==void 0&&(P.exports=g)},4042:(P,y,o)=>{var g,r;g=[o(3540)],r=function(t){"use strict";return t.documentElement}.apply(y,g),r!==void 0&&(P.exports=r)},5115:(P,y,o)=>{var g,r;g=[o(9929)],r=function(t){"use strict";return t.flat?function(l){return t.flat.call(l)}:function(l){return t.concat.apply([],l)}}.apply(y,g),r!==void 0&&(P.exports=r)},6704:(P,y,o)=>{var g,r;g=[o(5862)],r=function(t){"use strict";return t.toString}.apply(y,g),r!==void 0&&(P.exports=r)},1410:(P,y,o)=>{var g;g=function(){"use strict";return Object.getPrototypeOf}.call(y,o,y,P),g!==void 0&&(P.exports=g)},5862:(P,y,o)=>{var g,r;g=[o(8002)],r=function(t){"use strict";return t.hasOwnProperty}.apply(y,g),r!==void 0&&(P.exports=r)},7337:(P,y,o)=>{var g,r;g=[o(9929)],r=function(t){"use strict";return t.indexOf}.apply(y,g),r!==void 0&&(P.exports=r)},8954:(P,y,o)=>{var g;g=function(){"use strict";return function(t){return typeof t=="function"&&typeof t.nodeType!="number"&&typeof t.item!="function"}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},8194:(P,y,o)=>{var g;g=function(){"use strict";return function(t){return t!=null&&t===t.window}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},6668:(P,y,o)=>{var g;g=function(){"use strict";return/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source}.call(y,o,y,P),g!==void 0&&(P.exports=g)},8076:(P,y,o)=>{var g,r;g=[o(9929)],r=function(t){"use strict";return t.push}.apply(y,g),r!==void 0&&(P.exports=r)},4556:(P,y,o)=>{var g;g=function(){"use strict";return/^(?:checkbox|radio)$/i}.call(y,o,y,P),g!==void 0&&(P.exports=g)},7729:(P,y,o)=>{var g,r;g=[o(6668)],r=function(t){"use strict";return new RegExp("^(?:([+-])=|)("+t+")([a-z%]*)$","i")}.apply(y,g),r!==void 0&&(P.exports=r)},6258:(P,y,o)=>{var g;g=function(){"use strict";return/[^\x20\t\r\n\f]+/g}.call(y,o,y,P),g!==void 0&&(P.exports=g)},7451:(P,y,o)=>{var g,r;g=[o(9929)],r=function(t){"use strict";return t.slice}.apply(y,g),r!==void 0&&(P.exports=r)},7511:(P,y,o)=>{var g;g=function(){"use strict";return{}}.call(y,o,y,P),g!==void 0&&(P.exports=g)},3947:(P,y,o)=>{var g,r;g=[o(8002)],r=function(t){"use strict";return t.toString}.apply(y,g),r!==void 0&&(P.exports=r)},8495:(P,y,o)=>{var g,r;g=[o(6934),o(8954),o(852),o(4819),o(4048)],r=function(t,l){"use strict";return t.fn.extend({wrapAll:function(c){var p;return this[0]&&(l(c)&&(c=c.call(this[0])),p=t(c,this[0].ownerDocument).eq(0).clone(!0),this[0].parentNode&&p.insertBefore(this[0]),p.map(function(){for(var a=this;a.firstElementChild;)a=a.firstElementChild;return a}).append(this)),this},wrapInner:function(c){return l(c)?this.each(function(p){t(this).wrapInner(c.call(this,p))}):this.each(function(){var p=t(this),a=p.contents();a.length?a.wrapAll(c):p.append(c)})},wrap:function(c){var p=l(c);return this.each(function(a){t(this).wrapAll(p?c.call(this,a):c)})},unwrap:function(c){return this.parent(c).not("body").each(function(){t(this).replaceWith(this.childNodes)}),this}}),t}.apply(y,g),r!==void 0&&(P.exports=r)},8242:function(P,y,o){P=o.nmd(P);var g;/**
* @license
* Lodash <https://lodash.com/>
* Copyright OpenJS Foundation and other contributors <https://openjsf.org/>
* Released under MIT license <https://lodash.com/license>
* Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
* Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
*/(function(){var r,t="4.17.21",l=200,c="Unsupported core-js use. Try https://npms.io/search?q=ponyfill.",p="Expected a function",a="Invalid `variable` option passed into `_.template`",u="__lodash_hash_undefined__",h=500,i="__lodash_placeholder__",m=1,d=2,f=4,v=1,_=2,A=1,C=2,E=4,S=8,b=16,x=32,D=64,N=128,I=256,w=512,O=30,B="...",U=800,L=16,F=1,Y=2,z=3,G=1/0,W=9007199254740991,te=17976931348623157e292,oe=0/0,ge=4294967295,Q=ge-1,Te=ge>>>1,Pe=[["ary",N],["bind",A],["bindKey",C],["curry",S],["curryRight",b],["flip",w],["partial",x],["partialRight",D],["rearg",I]],Ge="[object Arguments]",mn="[object Array]",Rn="[object AsyncFunction]",Dn="[object Boolean]",Nn="[object Date]",jn="[object DOMException]",je="[object Error]",Ln="[object Function]",Ze="[object GeneratorFunction]",nn="[object Map]",Yn="[object Number]",kt="[object Null]",bn="[object Object]",Wn="[object Promise]",mt="[object Proxy]",On="[object RegExp]",un="[object Set]",gn="[object String]",xt="[object Symbol]",er="[object Undefined]",Qn="[object WeakMap]",hr="[object WeakSet]",tn="[object ArrayBuffer]",et="[object DataView]",qn="[object Float32Array]",ce="[object Float64Array]",J="[object Int8Array]",ue="[object Int16Array]",Ce="[object Int32Array]",ie="[object Uint8Array]",ye="[object Uint8ClampedArray]",pe="[object Uint16Array]",Ee="[object Uint32Array]",Oe=/\b__p \+= '';/g,Ue=/\b(__p \+=) '' \+/g,Ne=/(__e\(.*?\)|\b__t\)) \+\n'';/g,Se=/&(?:amp|lt|gt|quot|#39);/g,Fe=/[&<>"']/g,Le=RegExp(Se.source),sn=RegExp(Fe.source),An=/<%-([\s\S]+?)%>/g,We=/<%([\s\S]+?)%>/g,kn=/<%=([\s\S]+?)%>/g,q=/\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/,H=/^\w*$/,K=/[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g,ne=/[\\^$.*+?()[\]{}|]/g,$=RegExp(ne.source),re=/^\s+/,de=/\s/,Ae=/\{(?:\n\/\* \[wrapped with .+\] \*\/)?\n?/,be=/\{\n\/\* \[wrapped with (.+)\] \*/,ke=/,? & /,Ie=/[^\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]+/g,Me=/[()=,{}\[\]\/\s]/,$e=/\\(\\)?/g,ln=/\$\{([^\\}]*(?:\\.[^\\}]*)*)\}/g,qe=/\w*$/,me=/^[-+]0x[0-9a-f]+$/i,fe=/^0b[01]+$/i,ve=/^\[object .+?Constructor\]$/,_e=/^0o[0-7]+$/i,Ye=/^(?:0|[1-9]\d*)$/,Xe=/[\xc0-\xd6\xd8-\xf6\xf8-\xff\u0100-\u017f]/g,Re=/($^)/,Fn=/['\n\r\u2028\u2029\\]/g,nt="\\ud800-\\udfff",Tt="\\u0300-\\u036f",Ut="\\ufe20-\\ufe2f",_n="\\u20d0-\\u20ff",hn=Tt+Ut+_n,Ir="\\u2700-\\u27bf",Ai="a-z\\xdf-\\xf6\\xf8-\\xff",wa="\\xac\\xb1\\xd7\\xf7",nr="\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf",rl="\\u2000-\\u206f",il=" \\t\\x0b\\f\\xa0\\ufeff\\n\\r\\u2028\\u2029\\u1680\\u180e\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200a\\u202f\\u205f\\u3000",Ia="A-Z\\xc0-\\xd6\\xd8-\\xde",Oa="\\ufe0e\\ufe0f",qa=wa+nr+rl+il,Ti="['\u2019]",al="["+nt+"]",ka="["+qa+"]",Or="["+hn+"]",Ua="\\d+",ol="["+Ir+"]",La="["+Ai+"]",Ma="[^"+nt+qa+Ua+Ir+Ai+Ia+"]",Ei="\\ud83c[\\udffb-\\udfff]",sl="(?:"+Or+"|"+Ei+")",Ba="[^"+nt+"]",bi="(?:\\ud83c[\\udde6-\\uddff]){2}",Pi="[\\ud800-\\udbff][\\udc00-\\udfff]",tr="["+Ia+"]",Fa="\\u200d",za="(?:"+La+"|"+Ma+")",ll="(?:"+tr+"|"+Ma+")",Ha="(?:"+Ti+"(?:d|ll|m|re|s|t|ve))?",ja="(?:"+Ti+"(?:D|LL|M|RE|S|T|VE))?",Ya=sl+"?",Wa="["+Oa+"]?",pl="(?:"+Fa+"(?:"+[Ba,bi,Pi].join("|")+")"+Wa+Ya+")*",ul="\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])",cl="\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])",Ga=Wa+Ya+pl,dl="(?:"+[ol,bi,Pi].join("|")+")"+Ga,fl="(?:"+[Ba+Or+"?",Or,bi,Pi,al].join("|")+")",gl=RegExp(Ti,"g"),hl=RegExp(Or,"g"),xi=RegExp(Ei+"(?="+Ei+")|"+fl+Ga,"g"),ml=RegExp([tr+"?"+La+"+"+Ha+"(?="+[ka,tr,"$"].join("|")+")",ll+"+"+ja+"(?="+[ka,tr+za,"$"].join("|")+")",tr+"?"+za+"+"+Ha,tr+"+"+ja,cl,ul,Ua,dl].join("|"),"g"),_l=RegExp("["+Fa+nt+hn+Oa+"]"),yl=/[a-z][A-Z]|[A-Z]{2}[a-z]|[0-9][a-zA-Z]|[a-zA-Z][0-9]|[^a-zA-Z0-9 ]/,vl=["Array","Buffer","DataView","Date","Error","Float32Array","Float64Array","Function","Int8Array","Int16Array","Int32Array","Map","Math","Object","Promise","RegExp","Set","String","Symbol","TypeError","Uint8Array","Uint8ClampedArray","Uint16Array","Uint32Array","WeakMap","_","clearTimeout","isFinite","parseInt","setTimeout"],Al=-1,Tn={};Tn[qn]=Tn[ce]=Tn[J]=Tn[ue]=Tn[Ce]=Tn[ie]=Tn[ye]=Tn[pe]=Tn[Ee]=!0,Tn[Ge]=Tn[mn]=Tn[tn]=Tn[Dn]=Tn[et]=Tn[Nn]=Tn[je]=Tn[Ln]=Tn[nn]=Tn[Yn]=Tn[bn]=Tn[On]=Tn[un]=Tn[gn]=Tn[Qn]=!1;var vn={};vn[Ge]=vn[mn]=vn[tn]=vn[et]=vn[Dn]=vn[Nn]=vn[qn]=vn[ce]=vn[J]=vn[ue]=vn[Ce]=vn[nn]=vn[Yn]=vn[bn]=vn[On]=vn[un]=vn[gn]=vn[xt]=vn[ie]=vn[ye]=vn[pe]=vn[Ee]=!0,vn[je]=vn[Ln]=vn[Qn]=!1;var Tl={\u00C0:"A",\u00C1:"A",\u00C2:"A",\u00C3:"A",\u00C4:"A",\u00C5:"A",\u00E0:"a",\u00E1:"a",\u00E2:"a",\u00E3:"a",\u00E4:"a",\u00E5:"a",\u00C7:"C",\u00E7:"c",\u00D0:"D",\u00F0:"d",\u00C8:"E",\u00C9:"E",\u00CA:"E",\u00CB:"E",\u00E8:"e",\u00E9:"e",\u00EA:"e",\u00EB:"e",\u00CC:"I",\u00CD:"I",\u00CE:"I",\u00CF:"I",\u00EC:"i",\u00ED:"i",\u00EE:"i",\u00EF:"i",\u00D1:"N",\u00F1:"n",\u00D2:"O",\u00D3:"O",\u00D4:"O",\u00D5:"O",\u00D6:"O",\u00D8:"O",\u00F2:"o",\u00F3:"o",\u00F4:"o",\u00F5:"o",\u00F6:"o",\u00F8:"o",\u00D9:"U",\u00DA:"U",\u00DB:"U",\u00DC:"U",\u00F9:"u",\u00FA:"u",\u00FB:"u",\u00FC:"u",\u00DD:"Y",\u00FD:"y",\u00FF:"y",\u00C6:"Ae",\u00E6:"ae",\u00DE:"Th",\u00FE:"th",\u00DF:"ss",\u0100:"A",\u0102:"A",\u0104:"A",\u0101:"a",\u0103:"a",\u0105:"a",\u0106:"C",\u0108:"C",\u010A:"C",\u010C:"C",\u0107:"c",\u0109:"c",\u010B:"c",\u010D:"c",\u010E:"D",\u0110:"D",\u010F:"d",\u0111:"d",\u0112:"E",\u0114:"E",\u0116:"E",\u0118:"E",\u011A:"E",\u0113:"e",\u0115:"e",\u0117:"e",\u0119:"e",\u011B:"e",\u011C:"G",\u011E:"G",\u0120:"G",\u0122:"G",\u011D:"g",\u011F:"g",\u0121:"g",\u0123:"g",\u0124:"H",\u0126:"H",\u0125:"h",\u0127:"h",\u0128:"I",\u012A:"I",\u012C:"I",\u012E:"I",\u0130:"I",\u0129:"i",\u012B:"i",\u012D:"i",\u012F:"i",\u0131:"i",\u0134:"J",\u0135:"j",\u0136:"K",\u0137:"k",\u0138:"k",\u0139:"L",\u013B:"L",\u013D:"L",\u013F:"L",\u0141:"L",\u013A:"l",\u013C:"l",\u013E:"l",\u0140:"l",\u0142:"l",\u0143:"N",\u0145:"N",\u0147:"N",\u014A:"N",\u0144:"n",\u0146:"n",\u0148:"n",\u014B:"n",\u014C:"O",\u014E:"O",\u0150:"O",\u014D:"o",\u014F:"o",\u0151:"o",\u0154:"R",\u0156:"R",\u0158:"R",\u0155:"r",\u0157:"r",\u0159:"r",\u015A:"S",\u015C:"S",\u015E:"S",\u0160:"S",\u015B:"s",\u015D:"s",\u015F:"s",\u0161:"s",\u0162:"T",\u0164:"T",\u0166:"T",\u0163:"t",\u0165:"t",\u0167:"t",\u0168:"U",\u016A:"U",\u016C:"U",\u016E:"U",\u0170:"U",\u0172:"U",\u0169:"u",\u016B:"u",\u016D:"u",\u016F:"u",\u0171:"u",\u0173:"u",\u0174:"W",\u0175:"w",\u0176:"Y",\u0177:"y",\u0178:"Y",\u0179:"Z",\u017B:"Z",\u017D:"Z",\u017A:"z",\u017C:"z",\u017E:"z",\u0132:"IJ",\u0133:"ij",\u0152:"Oe",\u0153:"oe",\u0149:"'n",\u017F:"s"},El={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"},bl={"&amp;":"&","&lt;":"<","&gt;":">","&quot;":'"',"&#39;":"'"},Pl={"\\":"\\","'":"'","\n":"n","\r":"r","\u2028":"u2028","\u2029":"u2029"},xl=parseFloat,Cl=parseInt,Ka=typeof o.g=="object"&&o.g&&o.g.Object===Object&&o.g,Sl=typeof self=="object"&&self&&self.Object===Object&&self,Mn=Ka||Sl||Function("return this")(),Va=y&&!y.nodeType&&y,mr=Va&&!0&&P&&!P.nodeType&&P,$a=mr&&mr.exports===Va,Ci=$a&&Ka.process,lt=function(){try{var Z=mr&&mr.require&&mr.require("util").types;return Z||Ci&&Ci.binding&&Ci.binding("util")}catch(ae){}}(),Za=lt&&lt.isArrayBuffer,Xa=lt&&lt.isDate,Ja=lt&&lt.isMap,Qa=lt&&lt.isRegExp,eo=lt&&lt.isSet,no=lt&&lt.isTypedArray;function tt(Z,ae,ee){switch(ee.length){case 0:return Z.call(ae);case 1:return Z.call(ae,ee[0]);case 2:return Z.call(ae,ee[0],ee[1]);case 3:return Z.call(ae,ee[0],ee[1],ee[2])}return Z.apply(ae,ee)}function Dl(Z,ae,ee,De){for(var Ke=-1,pn=Z==null?0:Z.length;++Ke<pn;){var wn=Z[Ke];ae(De,wn,ee(wn),Z)}return De}function pt(Z,ae){for(var ee=-1,De=Z==null?0:Z.length;++ee<De&&ae(Z[ee],ee,Z)!==!1;);return Z}function Rl(Z,ae){for(var ee=Z==null?0:Z.length;ee--&&ae(Z[ee],ee,Z)!==!1;);return Z}function to(Z,ae){for(var ee=-1,De=Z==null?0:Z.length;++ee<De;)if(!ae(Z[ee],ee,Z))return!1;return!0}function Lt(Z,ae){for(var ee=-1,De=Z==null?0:Z.length,Ke=0,pn=[];++ee<De;){var wn=Z[ee];ae(wn,ee,Z)&&(pn[Ke++]=wn)}return pn}function qr(Z,ae){var ee=Z==null?0:Z.length;return!!ee&&rr(Z,ae,0)>-1}function Si(Z,ae,ee){for(var De=-1,Ke=Z==null?0:Z.length;++De<Ke;)if(ee(ae,Z[De]))return!0;return!1}function En(Z,ae){for(var ee=-1,De=Z==null?0:Z.length,Ke=Array(De);++ee<De;)Ke[ee]=ae(Z[ee],ee,Z);return Ke}function Mt(Z,ae){for(var ee=-1,De=ae.length,Ke=Z.length;++ee<De;)Z[Ke+ee]=ae[ee];return Z}function Di(Z,ae,ee,De){var Ke=-1,pn=Z==null?0:Z.length;for(De&&pn&&(ee=Z[++Ke]);++Ke<pn;)ee=ae(ee,Z[Ke],Ke,Z);return ee}function Nl(Z,ae,ee,De){var Ke=Z==null?0:Z.length;for(De&&Ke&&(ee=Z[--Ke]);Ke--;)ee=ae(ee,Z[Ke],Ke,Z);return ee}function Ri(Z,ae){for(var ee=-1,De=Z==null?0:Z.length;++ee<De;)if(ae(Z[ee],ee,Z))return!0;return!1}var wl=Ni("length");function Il(Z){return Z.split("")}function Ol(Z){return Z.match(Ie)||[]}function ro(Z,ae,ee){var De;return ee(Z,function(Ke,pn,wn){if(ae(Ke,pn,wn))return De=pn,!1}),De}function kr(Z,ae,ee,De){for(var Ke=Z.length,pn=ee+(De?1:-1);De?pn--:++pn<Ke;)if(ae(Z[pn],pn,Z))return pn;return-1}function rr(Z,ae,ee){return ae===ae?Wl(Z,ae,ee):kr(Z,io,ee)}function ql(Z,ae,ee,De){for(var Ke=ee-1,pn=Z.length;++Ke<pn;)if(De(Z[Ke],ae))return Ke;return-1}function io(Z){return Z!==Z}function ao(Z,ae){var ee=Z==null?0:Z.length;return ee?Ii(Z,ae)/ee:oe}function Ni(Z){return function(ae){return ae==null?r:ae[Z]}}function wi(Z){return function(ae){return Z==null?r:Z[ae]}}function oo(Z,ae,ee,De,Ke){return Ke(Z,function(pn,wn,yn){ee=De?(De=!1,pn):ae(ee,pn,wn,yn)}),ee}function kl(Z,ae){var ee=Z.length;for(Z.sort(ae);ee--;)Z[ee]=Z[ee].value;return Z}function Ii(Z,ae){for(var ee,De=-1,Ke=Z.length;++De<Ke;){var pn=ae(Z[De]);pn!==r&&(ee=ee===r?pn:ee+pn)}return ee}function Oi(Z,ae){for(var ee=-1,De=Array(Z);++ee<Z;)De[ee]=ae(ee);return De}function Ul(Z,ae){return En(ae,function(ee){return[ee,Z[ee]]})}function so(Z){return Z&&Z.slice(0,co(Z)+1).replace(re,"")}function rt(Z){return function(ae){return Z(ae)}}function qi(Z,ae){return En(ae,function(ee){return Z[ee]})}function _r(Z,ae){return Z.has(ae)}function lo(Z,ae){for(var ee=-1,De=Z.length;++ee<De&&rr(ae,Z[ee],0)>-1;);return ee}function po(Z,ae){for(var ee=Z.length;ee--&&rr(ae,Z[ee],0)>-1;);return ee}function Ll(Z,ae){for(var ee=Z.length,De=0;ee--;)Z[ee]===ae&&++De;return De}var Ml=wi(Tl),Bl=wi(El);function Fl(Z){return"\\"+Pl[Z]}function zl(Z,ae){return Z==null?r:Z[ae]}function ir(Z){return _l.test(Z)}function Hl(Z){return yl.test(Z)}function jl(Z){for(var ae,ee=[];!(ae=Z.next()).done;)ee.push(ae.value);return ee}function ki(Z){var ae=-1,ee=Array(Z.size);return Z.forEach(function(De,Ke){ee[++ae]=[Ke,De]}),ee}function uo(Z,ae){return function(ee){return Z(ae(ee))}}function Bt(Z,ae){for(var ee=-1,De=Z.length,Ke=0,pn=[];++ee<De;){var wn=Z[ee];(wn===ae||wn===i)&&(Z[ee]=i,pn[Ke++]=ee)}return pn}function Ur(Z){var ae=-1,ee=Array(Z.size);return Z.forEach(function(De){ee[++ae]=De}),ee}function Yl(Z){var ae=-1,ee=Array(Z.size);return Z.forEach(function(De){ee[++ae]=[De,De]}),ee}function Wl(Z,ae,ee){for(var De=ee-1,Ke=Z.length;++De<Ke;)if(Z[De]===ae)return De;return-1}function Gl(Z,ae,ee){for(var De=ee+1;De--;)if(Z[De]===ae)return De;return De}function ar(Z){return ir(Z)?Vl(Z):wl(Z)}function _t(Z){return ir(Z)?$l(Z):Il(Z)}function co(Z){for(var ae=Z.length;ae--&&de.test(Z.charAt(ae)););return ae}var Kl=wi(bl);function Vl(Z){for(var ae=xi.lastIndex=0;xi.test(Z);)++ae;return ae}function $l(Z){return Z.match(xi)||[]}function Zl(Z){return Z.match(ml)||[]}var Xl=function Z(ae){ae=ae==null?Mn:Lr.defaults(Mn.Object(),ae,Lr.pick(Mn,vl));var ee=ae.Array,De=ae.Date,Ke=ae.Error,pn=ae.Function,wn=ae.Math,yn=ae.Object,Ui=ae.RegExp,Jl=ae.String,ut=ae.TypeError,Mr=ee.prototype,Ql=pn.prototype,or=yn.prototype,Br=ae["__core-js_shared__"],Fr=Ql.toString,fn=or.hasOwnProperty,ep=0,fo=function(){var e=/[^.]+$/.exec(Br&&Br.keys&&Br.keys.IE_PROTO||"");return e?"Symbol(src)_1."+e:""}(),zr=or.toString,np=Fr.call(yn),tp=Mn._,rp=Ui("^"+Fr.call(fn).replace(ne,"\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g,"$1.*?")+"$"),Hr=$a?ae.Buffer:r,Ft=ae.Symbol,jr=ae.Uint8Array,go=Hr?Hr.allocUnsafe:r,Yr=uo(yn.getPrototypeOf,yn),ho=yn.create,mo=or.propertyIsEnumerable,Wr=Mr.splice,_o=Ft?Ft.isConcatSpreadable:r,yr=Ft?Ft.iterator:r,Gt=Ft?Ft.toStringTag:r,Gr=function(){try{var e=Xt(yn,"defineProperty");return e({},"",{}),e}catch(n){}}(),ip=ae.clearTimeout!==Mn.clearTimeout&&ae.clearTimeout,ap=De&&De.now!==Mn.Date.now&&De.now,op=ae.setTimeout!==Mn.setTimeout&&ae.setTimeout,Kr=wn.ceil,Vr=wn.floor,Li=yn.getOwnPropertySymbols,sp=Hr?Hr.isBuffer:r,yo=ae.isFinite,lp=Mr.join,pp=uo(yn.keys,yn),In=wn.max,zn=wn.min,up=De.now,cp=ae.parseInt,vo=wn.random,dp=Mr.reverse,Mi=Xt(ae,"DataView"),vr=Xt(ae,"Map"),Bi=Xt(ae,"Promise"),sr=Xt(ae,"Set"),Ar=Xt(ae,"WeakMap"),Tr=Xt(yn,"create"),$r=Ar&&new Ar,lr={},fp=Jt(Mi),gp=Jt(vr),hp=Jt(Bi),mp=Jt(sr),_p=Jt(Ar),Zr=Ft?Ft.prototype:r,Er=Zr?Zr.valueOf:r,Ao=Zr?Zr.toString:r;function k(e){if(xn(e)&&!Ve(e)&&!(e instanceof an)){if(e instanceof ct)return e;if(fn.call(e,"__wrapped__"))return Ts(e)}return new ct(e)}var pr=function(){function e(){}return function(n){if(!Pn(n))return{};if(ho)return ho(n);e.prototype=n;var s=new e;return e.prototype=r,s}}();function Xr(){}function ct(e,n){this.__wrapped__=e,this.__actions__=[],this.__chain__=!!n,this.__index__=0,this.__values__=r}k.templateSettings={escape:An,evaluate:We,interpolate:kn,variable:"",imports:{_:k}},k.prototype=Xr.prototype,k.prototype.constructor=k,ct.prototype=pr(Xr.prototype),ct.prototype.constructor=ct;function an(e){this.__wrapped__=e,this.__actions__=[],this.__dir__=1,this.__filtered__=!1,this.__iteratees__=[],this.__takeCount__=ge,this.__views__=[]}function yp(){var e=new an(this.__wrapped__);return e.__actions__=$n(this.__actions__),e.__dir__=this.__dir__,e.__filtered__=this.__filtered__,e.__iteratees__=$n(this.__iteratees__),e.__takeCount__=this.__takeCount__,e.__views__=$n(this.__views__),e}function vp(){if(this.__filtered__){var e=new an(this);e.__dir__=-1,e.__filtered__=!0}else e=this.clone(),e.__dir__*=-1;return e}function Ap(){var e=this.__wrapped__.value(),n=this.__dir__,s=Ve(e),T=n<0,R=s?e.length:0,M=Iu(0,R,this.__views__),j=M.start,V=M.end,X=V-j,se=T?V:j-1,le=this.__iteratees__,he=le.length,xe=0,we=zn(X,this.__takeCount__);if(!s||!T&&R==X&&we==X)return Yo(e,this.__actions__);var ze=[];e:for(;X--&&xe<we;){se+=n;for(var Qe=-1,He=e[se];++Qe<he;){var rn=le[Qe],on=rn.iteratee,ot=rn.type,Vn=on(He);if(ot==Y)He=Vn;else if(!Vn){if(ot==F)continue e;break e}}ze[xe++]=He}return ze}an.prototype=pr(Xr.prototype),an.prototype.constructor=an;function Kt(e){var n=-1,s=e==null?0:e.length;for(this.clear();++n<s;){var T=e[n];this.set(T[0],T[1])}}function Tp(){this.__data__=Tr?Tr(null):{},this.size=0}function Ep(e){var n=this.has(e)&&delete this.__data__[e];return this.size-=n?1:0,n}function bp(e){var n=this.__data__;if(Tr){var s=n[e];return s===u?r:s}return fn.call(n,e)?n[e]:r}function Pp(e){var n=this.__data__;return Tr?n[e]!==r:fn.call(n,e)}function xp(e,n){var s=this.__data__;return this.size+=this.has(e)?0:1,s[e]=Tr&&n===r?u:n,this}Kt.prototype.clear=Tp,Kt.prototype.delete=Ep,Kt.prototype.get=bp,Kt.prototype.has=Pp,Kt.prototype.set=xp;function Ct(e){var n=-1,s=e==null?0:e.length;for(this.clear();++n<s;){var T=e[n];this.set(T[0],T[1])}}function Cp(){this.__data__=[],this.size=0}function Sp(e){var n=this.__data__,s=Jr(n,e);if(s<0)return!1;var T=n.length-1;return s==T?n.pop():Wr.call(n,s,1),--this.size,!0}function Dp(e){var n=this.__data__,s=Jr(n,e);return s<0?r:n[s][1]}function Rp(e){return Jr(this.__data__,e)>-1}function Np(e,n){var s=this.__data__,T=Jr(s,e);return T<0?(++this.size,s.push([e,n])):s[T][1]=n,this}Ct.prototype.clear=Cp,Ct.prototype.delete=Sp,Ct.prototype.get=Dp,Ct.prototype.has=Rp,Ct.prototype.set=Np;function St(e){var n=-1,s=e==null?0:e.length;for(this.clear();++n<s;){var T=e[n];this.set(T[0],T[1])}}function wp(){this.size=0,this.__data__={hash:new Kt,map:new(vr||Ct),string:new Kt}}function Ip(e){var n=ui(this,e).delete(e);return this.size-=n?1:0,n}function Op(e){return ui(this,e).get(e)}function qp(e){return ui(this,e).has(e)}function kp(e,n){var s=ui(this,e),T=s.size;return s.set(e,n),this.size+=s.size==T?0:1,this}St.prototype.clear=wp,St.prototype.delete=Ip,St.prototype.get=Op,St.prototype.has=qp,St.prototype.set=kp;function Vt(e){var n=-1,s=e==null?0:e.length;for(this.__data__=new St;++n<s;)this.add(e[n])}function Up(e){return this.__data__.set(e,u),this}function Lp(e){return this.__data__.has(e)}Vt.prototype.add=Vt.prototype.push=Up,Vt.prototype.has=Lp;function yt(e){var n=this.__data__=new Ct(e);this.size=n.size}function Mp(){this.__data__=new Ct,this.size=0}function Bp(e){var n=this.__data__,s=n.delete(e);return this.size=n.size,s}function Fp(e){return this.__data__.get(e)}function zp(e){return this.__data__.has(e)}function Hp(e,n){var s=this.__data__;if(s instanceof Ct){var T=s.__data__;if(!vr||T.length<l-1)return T.push([e,n]),this.size=++s.size,this;s=this.__data__=new St(T)}return s.set(e,n),this.size=s.size,this}yt.prototype.clear=Mp,yt.prototype.delete=Bp,yt.prototype.get=Fp,yt.prototype.has=zp,yt.prototype.set=Hp;function To(e,n){var s=Ve(e),T=!s&&Qt(e),R=!s&&!T&&Wt(e),M=!s&&!T&&!R&&fr(e),j=s||T||R||M,V=j?Oi(e.length,Jl):[],X=V.length;for(var se in e)(n||fn.call(e,se))&&!(j&&(se=="length"||R&&(se=="offset"||se=="parent")||M&&(se=="buffer"||se=="byteLength"||se=="byteOffset")||wt(se,X)))&&V.push(se);return V}function Eo(e){var n=e.length;return n?e[Zi(0,n-1)]:r}function jp(e,n){return ci($n(e),$t(n,0,e.length))}function Yp(e){return ci($n(e))}function Fi(e,n,s){(s!==r&&!vt(e[n],s)||s===r&&!(n in e))&&Dt(e,n,s)}function br(e,n,s){var T=e[n];(!(fn.call(e,n)&&vt(T,s))||s===r&&!(n in e))&&Dt(e,n,s)}function Jr(e,n){for(var s=e.length;s--;)if(vt(e[s][0],n))return s;return-1}function Wp(e,n,s,T){return zt(e,function(R,M,j){n(T,R,s(R),j)}),T}function bo(e,n){return e&&bt(n,Un(n),e)}function Gp(e,n){return e&&bt(n,Xn(n),e)}function Dt(e,n,s){n=="__proto__"&&Gr?Gr(e,n,{configurable:!0,enumerable:!0,value:s,writable:!0}):e[n]=s}function zi(e,n){for(var s=-1,T=n.length,R=ee(T),M=e==null;++s<T;)R[s]=M?r:Ta(e,n[s]);return R}function $t(e,n,s){return e===e&&(s!==r&&(e=e<=s?e:s),n!==r&&(e=e>=n?e:n)),e}function dt(e,n,s,T,R,M){var j,V=n&m,X=n&d,se=n&f;if(s&&(j=R?s(e,T,R,M):s(e)),j!==r)return j;if(!Pn(e))return e;var le=Ve(e);if(le){if(j=qu(e),!V)return $n(e,j)}else{var he=Hn(e),xe=he==Ln||he==Ze;if(Wt(e))return Ko(e,V);if(he==bn||he==Ge||xe&&!R){if(j=X||xe?{}:ds(e),!V)return X?bu(e,Gp(j,e)):Eu(e,bo(j,e))}else{if(!vn[he])return R?e:{};j=ku(e,he,V)}}M||(M=new yt);var we=M.get(e);if(we)return we;M.set(e,j),zs(e)?e.forEach(function(He){j.add(dt(He,n,s,He,e,M))}):Bs(e)&&e.forEach(function(He,rn){j.set(rn,dt(He,n,s,rn,e,M))});var ze=se?X?sa:oa:X?Xn:Un,Qe=le?r:ze(e);return pt(Qe||e,function(He,rn){Qe&&(rn=He,He=e[rn]),br(j,rn,dt(He,n,s,rn,e,M))}),j}function Kp(e){var n=Un(e);return function(s){return Po(s,e,n)}}function Po(e,n,s){var T=s.length;if(e==null)return!T;for(e=yn(e);T--;){var R=s[T],M=n[R],j=e[R];if(j===r&&!(R in e)||!M(j))return!1}return!0}function xo(e,n,s){if(typeof e!="function")throw new ut(p);return Nr(function(){e.apply(r,s)},n)}function Pr(e,n,s,T){var R=-1,M=qr,j=!0,V=e.length,X=[],se=n.length;if(!V)return X;s&&(n=En(n,rt(s))),T?(M=Si,j=!1):n.length>=l&&(M=_r,j=!1,n=new Vt(n));e:for(;++R<V;){var le=e[R],he=s==null?le:s(le);if(le=T||le!==0?le:0,j&&he===he){for(var xe=se;xe--;)if(n[xe]===he)continue e;X.push(le)}else M(n,he,T)||X.push(le)}return X}var zt=Jo(Et),Co=Jo(ji,!0);function Vp(e,n){var s=!0;return zt(e,function(T,R,M){return s=!!n(T,R,M),s}),s}function Qr(e,n,s){for(var T=-1,R=e.length;++T<R;){var M=e[T],j=n(M);if(j!=null&&(V===r?j===j&&!at(j):s(j,V)))var V=j,X=M}return X}function $p(e,n,s,T){var R=e.length;for(s=Je(s),s<0&&(s=-s>R?0:R+s),T=T===r||T>R?R:Je(T),T<0&&(T+=R),T=s>T?0:js(T);s<T;)e[s++]=n;return e}function So(e,n){var s=[];return zt(e,function(T,R,M){n(T,R,M)&&s.push(T)}),s}function Bn(e,n,s,T,R){var M=-1,j=e.length;for(s||(s=Lu),R||(R=[]);++M<j;){var V=e[M];n>0&&s(V)?n>1?Bn(V,n-1,s,T,R):Mt(R,V):T||(R[R.length]=V)}return R}var Hi=Qo(),Do=Qo(!0);function Et(e,n){return e&&Hi(e,n,Un)}function ji(e,n){return e&&Do(e,n,Un)}function ei(e,n){return Lt(n,function(s){return It(e[s])})}function Zt(e,n){n=jt(n,e);for(var s=0,T=n.length;e!=null&&s<T;)e=e[Pt(n[s++])];return s&&s==T?e:r}function Ro(e,n,s){var T=n(e);return Ve(e)?T:Mt(T,s(e))}function Gn(e){return e==null?e===r?er:kt:Gt&&Gt in yn(e)?wu(e):Yu(e)}function Yi(e,n){return e>n}function Zp(e,n){return e!=null&&fn.call(e,n)}function Xp(e,n){return e!=null&&n in yn(e)}function Jp(e,n,s){return e>=zn(n,s)&&e<In(n,s)}function Wi(e,n,s){for(var T=s?Si:qr,R=e[0].length,M=e.length,j=M,V=ee(M),X=1/0,se=[];j--;){var le=e[j];j&&n&&(le=En(le,rt(n))),X=zn(le.length,X),V[j]=!s&&(n||R>=120&&le.length>=120)?new Vt(j&&le):r}le=e[0];var he=-1,xe=V[0];e:for(;++he<R&&se.length<X;){var we=le[he],ze=n?n(we):we;if(we=s||we!==0?we:0,!(xe?_r(xe,ze):T(se,ze,s))){for(j=M;--j;){var Qe=V[j];if(!(Qe?_r(Qe,ze):T(e[j],ze,s)))continue e}xe&&xe.push(ze),se.push(we)}}return se}function Qp(e,n,s,T){return Et(e,function(R,M,j){n(T,s(R),M,j)}),T}function xr(e,n,s){n=jt(n,e),e=ms(e,n);var T=e==null?e:e[Pt(gt(n))];return T==null?r:tt(T,e,s)}function No(e){return xn(e)&&Gn(e)==Ge}function eu(e){return xn(e)&&Gn(e)==tn}function nu(e){return xn(e)&&Gn(e)==Nn}function Cr(e,n,s,T,R){return e===n?!0:e==null||n==null||!xn(e)&&!xn(n)?e!==e&&n!==n:tu(e,n,s,T,Cr,R)}function tu(e,n,s,T,R,M){var j=Ve(e),V=Ve(n),X=j?mn:Hn(e),se=V?mn:Hn(n);X=X==Ge?bn:X,se=se==Ge?bn:se;var le=X==bn,he=se==bn,xe=X==se;if(xe&&Wt(e)){if(!Wt(n))return!1;j=!0,le=!1}if(xe&&!le)return M||(M=new yt),j||fr(e)?ps(e,n,s,T,R,M):Ru(e,n,X,s,T,R,M);if(!(s&v)){var we=le&&fn.call(e,"__wrapped__"),ze=he&&fn.call(n,"__wrapped__");if(we||ze){var Qe=we?e.value():e,He=ze?n.value():n;return M||(M=new yt),R(Qe,He,s,T,M)}}return xe?(M||(M=new yt),Nu(e,n,s,T,R,M)):!1}function ru(e){return xn(e)&&Hn(e)==nn}function Gi(e,n,s,T){var R=s.length,M=R,j=!T;if(e==null)return!M;for(e=yn(e);R--;){var V=s[R];if(j&&V[2]?V[1]!==e[V[0]]:!(V[0]in e))return!1}for(;++R<M;){V=s[R];var X=V[0],se=e[X],le=V[1];if(j&&V[2]){if(se===r&&!(X in e))return!1}else{var he=new yt;if(T)var xe=T(se,le,X,e,n,he);if(!(xe===r?Cr(le,se,v|_,T,he):xe))return!1}}return!0}function wo(e){if(!Pn(e)||Bu(e))return!1;var n=It(e)?rp:ve;return n.test(Jt(e))}function iu(e){return xn(e)&&Gn(e)==On}function au(e){return xn(e)&&Hn(e)==un}function ou(e){return xn(e)&&_i(e.length)&&!!Tn[Gn(e)]}function Io(e){return typeof e=="function"?e:e==null?Jn:typeof e=="object"?Ve(e)?ko(e[0],e[1]):qo(e):el(e)}function Ki(e){if(!Rr(e))return pp(e);var n=[];for(var s in yn(e))fn.call(e,s)&&s!="constructor"&&n.push(s);return n}function su(e){if(!Pn(e))return ju(e);var n=Rr(e),s=[];for(var T in e)T=="constructor"&&(n||!fn.call(e,T))||s.push(T);return s}function Vi(e,n){return e<n}function Oo(e,n){var s=-1,T=Zn(e)?ee(e.length):[];return zt(e,function(R,M,j){T[++s]=n(R,M,j)}),T}function qo(e){var n=pa(e);return n.length==1&&n[0][2]?gs(n[0][0],n[0][1]):function(s){return s===e||Gi(s,e,n)}}function ko(e,n){return ca(e)&&fs(n)?gs(Pt(e),n):function(s){var T=Ta(s,e);return T===r&&T===n?Ea(s,e):Cr(n,T,v|_)}}function ni(e,n,s,T,R){e!==n&&Hi(n,function(M,j){if(R||(R=new yt),Pn(M))lu(e,n,j,s,ni,T,R);else{var V=T?T(fa(e,j),M,j+"",e,n,R):r;V===r&&(V=M),Fi(e,j,V)}},Xn)}function lu(e,n,s,T,R,M,j){var V=fa(e,s),X=fa(n,s),se=j.get(X);if(se){Fi(e,s,se);return}var le=M?M(V,X,s+"",e,n,j):r,he=le===r;if(he){var xe=Ve(X),we=!xe&&Wt(X),ze=!xe&&!we&&fr(X);le=X,xe||we||ze?Ve(V)?le=V:Cn(V)?le=$n(V):we?(he=!1,le=Ko(X,!0)):ze?(he=!1,le=Vo(X,!0)):le=[]:wr(X)||Qt(X)?(le=V,Qt(V)?le=Ys(V):(!Pn(V)||It(V))&&(le=ds(X))):he=!1}he&&(j.set(X,le),R(le,X,T,M,j),j.delete(X)),Fi(e,s,le)}function Uo(e,n){var s=e.length;if(!!s)return n+=n<0?s:0,wt(n,s)?e[n]:r}function Lo(e,n,s){n.length?n=En(n,function(M){return Ve(M)?function(j){return Zt(j,M.length===1?M[0]:M)}:M}):n=[Jn];var T=-1;n=En(n,rt(Be()));var R=Oo(e,function(M,j,V){var X=En(n,function(se){return se(M)});return{criteria:X,index:++T,value:M}});return kl(R,function(M,j){return Tu(M,j,s)})}function pu(e,n){return Mo(e,n,function(s,T){return Ea(e,T)})}function Mo(e,n,s){for(var T=-1,R=n.length,M={};++T<R;){var j=n[T],V=Zt(e,j);s(V,j)&&Sr(M,jt(j,e),V)}return M}function uu(e){return function(n){return Zt(n,e)}}function $i(e,n,s,T){var R=T?ql:rr,M=-1,j=n.length,V=e;for(e===n&&(n=$n(n)),s&&(V=En(e,rt(s)));++M<j;)for(var X=0,se=n[M],le=s?s(se):se;(X=R(V,le,X,T))>-1;)V!==e&&Wr.call(V,X,1),Wr.call(e,X,1);return e}function Bo(e,n){for(var s=e?n.length:0,T=s-1;s--;){var R=n[s];if(s==T||R!==M){var M=R;wt(R)?Wr.call(e,R,1):Qi(e,R)}}return e}function Zi(e,n){return e+Vr(vo()*(n-e+1))}function cu(e,n,s,T){for(var R=-1,M=In(Kr((n-e)/(s||1)),0),j=ee(M);M--;)j[T?M:++R]=e,e+=s;return j}function Xi(e,n){var s="";if(!e||n<1||n>W)return s;do n%2&&(s+=e),n=Vr(n/2),n&&(e+=e);while(n);return s}function en(e,n){return ga(hs(e,n,Jn),e+"")}function du(e){return Eo(gr(e))}function fu(e,n){var s=gr(e);return ci(s,$t(n,0,s.length))}function Sr(e,n,s,T){if(!Pn(e))return e;n=jt(n,e);for(var R=-1,M=n.length,j=M-1,V=e;V!=null&&++R<M;){var X=Pt(n[R]),se=s;if(X==="__proto__"||X==="constructor"||X==="prototype")return e;if(R!=j){var le=V[X];se=T?T(le,X,V):r,se===r&&(se=Pn(le)?le:wt(n[R+1])?[]:{})}br(V,X,se),V=V[X]}return e}var Fo=$r?function(e,n){return $r.set(e,n),e}:Jn,gu=Gr?function(e,n){return Gr(e,"toString",{configurable:!0,enumerable:!1,value:Pa(n),writable:!0})}:Jn;function hu(e){return ci(gr(e))}function ft(e,n,s){var T=-1,R=e.length;n<0&&(n=-n>R?0:R+n),s=s>R?R:s,s<0&&(s+=R),R=n>s?0:s-n>>>0,n>>>=0;for(var M=ee(R);++T<R;)M[T]=e[T+n];return M}function mu(e,n){var s;return zt(e,function(T,R,M){return s=n(T,R,M),!s}),!!s}function ti(e,n,s){var T=0,R=e==null?T:e.length;if(typeof n=="number"&&n===n&&R<=Te){for(;T<R;){var M=T+R>>>1,j=e[M];j!==null&&!at(j)&&(s?j<=n:j<n)?T=M+1:R=M}return R}return Ji(e,n,Jn,s)}function Ji(e,n,s,T){var R=0,M=e==null?0:e.length;if(M===0)return 0;n=s(n);for(var j=n!==n,V=n===null,X=at(n),se=n===r;R<M;){var le=Vr((R+M)/2),he=s(e[le]),xe=he!==r,we=he===null,ze=he===he,Qe=at(he);if(j)var He=T||ze;else se?He=ze&&(T||xe):V?He=ze&&xe&&(T||!we):X?He=ze&&xe&&!we&&(T||!Qe):we||Qe?He=!1:He=T?he<=n:he<n;He?R=le+1:M=le}return zn(M,Q)}function zo(e,n){for(var s=-1,T=e.length,R=0,M=[];++s<T;){var j=e[s],V=n?n(j):j;if(!s||!vt(V,X)){var X=V;M[R++]=j===0?0:j}}return M}function Ho(e){return typeof e=="number"?e:at(e)?oe:+e}function it(e){if(typeof e=="string")return e;if(Ve(e))return En(e,it)+"";if(at(e))return Ao?Ao.call(e):"";var n=e+"";return n=="0"&&1/e==-G?"-0":n}function Ht(e,n,s){var T=-1,R=qr,M=e.length,j=!0,V=[],X=V;if(s)j=!1,R=Si;else if(M>=l){var se=n?null:Su(e);if(se)return Ur(se);j=!1,R=_r,X=new Vt}else X=n?[]:V;e:for(;++T<M;){var le=e[T],he=n?n(le):le;if(le=s||le!==0?le:0,j&&he===he){for(var xe=X.length;xe--;)if(X[xe]===he)continue e;n&&X.push(he),V.push(le)}else R(X,he,s)||(X!==V&&X.push(he),V.push(le))}return V}function Qi(e,n){return n=jt(n,e),e=ms(e,n),e==null||delete e[Pt(gt(n))]}function jo(e,n,s,T){return Sr(e,n,s(Zt(e,n)),T)}function ri(e,n,s,T){for(var R=e.length,M=T?R:-1;(T?M--:++M<R)&&n(e[M],M,e););return s?ft(e,T?0:M,T?M+1:R):ft(e,T?M+1:0,T?R:M)}function Yo(e,n){var s=e;return s instanceof an&&(s=s.value()),Di(n,function(T,R){return R.func.apply(R.thisArg,Mt([T],R.args))},s)}function ea(e,n,s){var T=e.length;if(T<2)return T?Ht(e[0]):[];for(var R=-1,M=ee(T);++R<T;)for(var j=e[R],V=-1;++V<T;)V!=R&&(M[R]=Pr(M[R]||j,e[V],n,s));return Ht(Bn(M,1),n,s)}function Wo(e,n,s){for(var T=-1,R=e.length,M=n.length,j={};++T<R;){var V=T<M?n[T]:r;s(j,e[T],V)}return j}function na(e){return Cn(e)?e:[]}function ta(e){return typeof e=="function"?e:Jn}function jt(e,n){return Ve(e)?e:ca(e,n)?[e]:As(cn(e))}var _u=en;function Yt(e,n,s){var T=e.length;return s=s===r?T:s,!n&&s>=T?e:ft(e,n,s)}var Go=ip||function(e){return Mn.clearTimeout(e)};function Ko(e,n){if(n)return e.slice();var s=e.length,T=go?go(s):new e.constructor(s);return e.copy(T),T}function ra(e){var n=new e.constructor(e.byteLength);return new jr(n).set(new jr(e)),n}function yu(e,n){var s=n?ra(e.buffer):e.buffer;return new e.constructor(s,e.byteOffset,e.byteLength)}function vu(e){var n=new e.constructor(e.source,qe.exec(e));return n.lastIndex=e.lastIndex,n}function Au(e){return Er?yn(Er.call(e)):{}}function Vo(e,n){var s=n?ra(e.buffer):e.buffer;return new e.constructor(s,e.byteOffset,e.length)}function $o(e,n){if(e!==n){var s=e!==r,T=e===null,R=e===e,M=at(e),j=n!==r,V=n===null,X=n===n,se=at(n);if(!V&&!se&&!M&&e>n||M&&j&&X&&!V&&!se||T&&j&&X||!s&&X||!R)return 1;if(!T&&!M&&!se&&e<n||se&&s&&R&&!T&&!M||V&&s&&R||!j&&R||!X)return-1}return 0}function Tu(e,n,s){for(var T=-1,R=e.criteria,M=n.criteria,j=R.length,V=s.length;++T<j;){var X=$o(R[T],M[T]);if(X){if(T>=V)return X;var se=s[T];return X*(se=="desc"?-1:1)}}return e.index-n.index}function Zo(e,n,s,T){for(var R=-1,M=e.length,j=s.length,V=-1,X=n.length,se=In(M-j,0),le=ee(X+se),he=!T;++V<X;)le[V]=n[V];for(;++R<j;)(he||R<M)&&(le[s[R]]=e[R]);for(;se--;)le[V++]=e[R++];return le}function Xo(e,n,s,T){for(var R=-1,M=e.length,j=-1,V=s.length,X=-1,se=n.length,le=In(M-V,0),he=ee(le+se),xe=!T;++R<le;)he[R]=e[R];for(var we=R;++X<se;)he[we+X]=n[X];for(;++j<V;)(xe||R<M)&&(he[we+s[j]]=e[R++]);return he}function $n(e,n){var s=-1,T=e.length;for(n||(n=ee(T));++s<T;)n[s]=e[s];return n}function bt(e,n,s,T){var R=!s;s||(s={});for(var M=-1,j=n.length;++M<j;){var V=n[M],X=T?T(s[V],e[V],V,s,e):r;X===r&&(X=e[V]),R?Dt(s,V,X):br(s,V,X)}return s}function Eu(e,n){return bt(e,ua(e),n)}function bu(e,n){return bt(e,us(e),n)}function ii(e,n){return function(s,T){var R=Ve(s)?Dl:Wp,M=n?n():{};return R(s,e,Be(T,2),M)}}function ur(e){return en(function(n,s){var T=-1,R=s.length,M=R>1?s[R-1]:r,j=R>2?s[2]:r;for(M=e.length>3&&typeof M=="function"?(R--,M):r,j&&Kn(s[0],s[1],j)&&(M=R<3?r:M,R=1),n=yn(n);++T<R;){var V=s[T];V&&e(n,V,T,M)}return n})}function Jo(e,n){return function(s,T){if(s==null)return s;if(!Zn(s))return e(s,T);for(var R=s.length,M=n?R:-1,j=yn(s);(n?M--:++M<R)&&T(j[M],M,j)!==!1;);return s}}function Qo(e){return function(n,s,T){for(var R=-1,M=yn(n),j=T(n),V=j.length;V--;){var X=j[e?V:++R];if(s(M[X],X,M)===!1)break}return n}}function Pu(e,n,s){var T=n&A,R=Dr(e);function M(){var j=this&&this!==Mn&&this instanceof M?R:e;return j.apply(T?s:this,arguments)}return M}function es(e){return function(n){n=cn(n);var s=ir(n)?_t(n):r,T=s?s[0]:n.charAt(0),R=s?Yt(s,1).join(""):n.slice(1);return T[e]()+R}}function cr(e){return function(n){return Di(Js(Xs(n).replace(gl,"")),e,"")}}function Dr(e){return function(){var n=arguments;switch(n.length){case 0:return new e;case 1:return new e(n[0]);case 2:return new e(n[0],n[1]);case 3:return new e(n[0],n[1],n[2]);case 4:return new e(n[0],n[1],n[2],n[3]);case 5:return new e(n[0],n[1],n[2],n[3],n[4]);case 6:return new e(n[0],n[1],n[2],n[3],n[4],n[5]);case 7:return new e(n[0],n[1],n[2],n[3],n[4],n[5],n[6])}var s=pr(e.prototype),T=e.apply(s,n);return Pn(T)?T:s}}function xu(e,n,s){var T=Dr(e);function R(){for(var M=arguments.length,j=ee(M),V=M,X=dr(R);V--;)j[V]=arguments[V];var se=M<3&&j[0]!==X&&j[M-1]!==X?[]:Bt(j,X);if(M-=se.length,M<s)return as(e,n,ai,R.placeholder,r,j,se,r,r,s-M);var le=this&&this!==Mn&&this instanceof R?T:e;return tt(le,this,j)}return R}function ns(e){return function(n,s,T){var R=yn(n);if(!Zn(n)){var M=Be(s,3);n=Un(n),s=function(V){return M(R[V],V,R)}}var j=e(n,s,T);return j>-1?R[M?n[j]:j]:r}}function ts(e){return Nt(function(n){var s=n.length,T=s,R=ct.prototype.thru;for(e&&n.reverse();T--;){var M=n[T];if(typeof M!="function")throw new ut(p);if(R&&!j&&pi(M)=="wrapper")var j=new ct([],!0)}for(T=j?T:s;++T<s;){M=n[T];var V=pi(M),X=V=="wrapper"?la(M):r;X&&da(X[0])&&X[1]==(N|S|x|I)&&!X[4].length&&X[9]==1?j=j[pi(X[0])].apply(j,X[3]):j=M.length==1&&da(M)?j[V]():j.thru(M)}return function(){var se=arguments,le=se[0];if(j&&se.length==1&&Ve(le))return j.plant(le).value();for(var he=0,xe=s?n[he].apply(this,se):le;++he<s;)xe=n[he].call(this,xe);return xe}})}function ai(e,n,s,T,R,M,j,V,X,se){var le=n&N,he=n&A,xe=n&C,we=n&(S|b),ze=n&w,Qe=xe?r:Dr(e);function He(){for(var rn=arguments.length,on=ee(rn),ot=rn;ot--;)on[ot]=arguments[ot];if(we)var Vn=dr(He),st=Ll(on,Vn);if(T&&(on=Zo(on,T,R,we)),M&&(on=Xo(on,M,j,we)),rn-=st,we&&rn<se){var Sn=Bt(on,Vn);return as(e,n,ai,He.placeholder,s,on,Sn,V,X,se-rn)}var At=he?s:this,qt=xe?At[e]:e;return rn=on.length,V?on=Wu(on,V):ze&&rn>1&&on.reverse(),le&&X<rn&&(on.length=X),this&&this!==Mn&&this instanceof He&&(qt=Qe||Dr(qt)),qt.apply(At,on)}return He}function rs(e,n){return function(s,T){return Qp(s,e,n(T),{})}}function oi(e,n){return function(s,T){var R;if(s===r&&T===r)return n;if(s!==r&&(R=s),T!==r){if(R===r)return T;typeof s=="string"||typeof T=="string"?(s=it(s),T=it(T)):(s=Ho(s),T=Ho(T)),R=e(s,T)}return R}}function ia(e){return Nt(function(n){return n=En(n,rt(Be())),en(function(s){var T=this;return e(n,function(R){return tt(R,T,s)})})})}function si(e,n){n=n===r?" ":it(n);var s=n.length;if(s<2)return s?Xi(n,e):n;var T=Xi(n,Kr(e/ar(n)));return ir(n)?Yt(_t(T),0,e).join(""):T.slice(0,e)}function Cu(e,n,s,T){var R=n&A,M=Dr(e);function j(){for(var V=-1,X=arguments.length,se=-1,le=T.length,he=ee(le+X),xe=this&&this!==Mn&&this instanceof j?M:e;++se<le;)he[se]=T[se];for(;X--;)he[se++]=arguments[++V];return tt(xe,R?s:this,he)}return j}function is(e){return function(n,s,T){return T&&typeof T!="number"&&Kn(n,s,T)&&(s=T=r),n=Ot(n),s===r?(s=n,n=0):s=Ot(s),T=T===r?n<s?1:-1:Ot(T),cu(n,s,T,e)}}function li(e){return function(n,s){return typeof n=="string"&&typeof s=="string"||(n=ht(n),s=ht(s)),e(n,s)}}function as(e,n,s,T,R,M,j,V,X,se){var le=n&S,he=le?j:r,xe=le?r:j,we=le?M:r,ze=le?r:M;n|=le?x:D,n&=~(le?D:x),n&E||(n&=~(A|C));var Qe=[e,n,R,we,he,ze,xe,V,X,se],He=s.apply(r,Qe);return da(e)&&_s(He,Qe),He.placeholder=T,ys(He,e,n)}function aa(e){var n=wn[e];return function(s,T){if(s=ht(s),T=T==null?0:zn(Je(T),292),T&&yo(s)){var R=(cn(s)+"e").split("e"),M=n(R[0]+"e"+(+R[1]+T));return R=(cn(M)+"e").split("e"),+(R[0]+"e"+(+R[1]-T))}return n(s)}}var Su=sr&&1/Ur(new sr([,-0]))[1]==G?function(e){return new sr(e)}:Sa;function os(e){return function(n){var s=Hn(n);return s==nn?ki(n):s==un?Yl(n):Ul(n,e(n))}}function Rt(e,n,s,T,R,M,j,V){var X=n&C;if(!X&&typeof e!="function")throw new ut(p);var se=T?T.length:0;if(se||(n&=~(x|D),T=R=r),j=j===r?j:In(Je(j),0),V=V===r?V:Je(V),se-=R?R.length:0,n&D){var le=T,he=R;T=R=r}var xe=X?r:la(e),we=[e,n,s,T,R,le,he,M,j,V];if(xe&&Hu(we,xe),e=we[0],n=we[1],s=we[2],T=we[3],R=we[4],V=we[9]=we[9]===r?X?0:e.length:In(we[9]-se,0),!V&&n&(S|b)&&(n&=~(S|b)),!n||n==A)var ze=Pu(e,n,s);else n==S||n==b?ze=xu(e,n,V):(n==x||n==(A|x))&&!R.length?ze=Cu(e,n,s,T):ze=ai.apply(r,we);var Qe=xe?Fo:_s;return ys(Qe(ze,we),e,n)}function ss(e,n,s,T){return e===r||vt(e,or[s])&&!fn.call(T,s)?n:e}function ls(e,n,s,T,R,M){return Pn(e)&&Pn(n)&&(M.set(n,e),ni(e,n,r,ls,M),M.delete(n)),e}function Du(e){return wr(e)?r:e}function ps(e,n,s,T,R,M){var j=s&v,V=e.length,X=n.length;if(V!=X&&!(j&&X>V))return!1;var se=M.get(e),le=M.get(n);if(se&&le)return se==n&&le==e;var he=-1,xe=!0,we=s&_?new Vt:r;for(M.set(e,n),M.set(n,e);++he<V;){var ze=e[he],Qe=n[he];if(T)var He=j?T(Qe,ze,he,n,e,M):T(ze,Qe,he,e,n,M);if(He!==r){if(He)continue;xe=!1;break}if(we){if(!Ri(n,function(rn,on){if(!_r(we,on)&&(ze===rn||R(ze,rn,s,T,M)))return we.push(on)})){xe=!1;break}}else if(!(ze===Qe||R(ze,Qe,s,T,M))){xe=!1;break}}return M.delete(e),M.delete(n),xe}function Ru(e,n,s,T,R,M,j){switch(s){case et:if(e.byteLength!=n.byteLength||e.byteOffset!=n.byteOffset)return!1;e=e.buffer,n=n.buffer;case tn:return!(e.byteLength!=n.byteLength||!M(new jr(e),new jr(n)));case Dn:case Nn:case Yn:return vt(+e,+n);case je:return e.name==n.name&&e.message==n.message;case On:case gn:return e==n+"";case nn:var V=ki;case un:var X=T&v;if(V||(V=Ur),e.size!=n.size&&!X)return!1;var se=j.get(e);if(se)return se==n;T|=_,j.set(e,n);var le=ps(V(e),V(n),T,R,M,j);return j.delete(e),le;case xt:if(Er)return Er.call(e)==Er.call(n)}return!1}function Nu(e,n,s,T,R,M){var j=s&v,V=oa(e),X=V.length,se=oa(n),le=se.length;if(X!=le&&!j)return!1;for(var he=X;he--;){var xe=V[he];if(!(j?xe in n:fn.call(n,xe)))return!1}var we=M.get(e),ze=M.get(n);if(we&&ze)return we==n&&ze==e;var Qe=!0;M.set(e,n),M.set(n,e);for(var He=j;++he<X;){xe=V[he];var rn=e[xe],on=n[xe];if(T)var ot=j?T(on,rn,xe,n,e,M):T(rn,on,xe,e,n,M);if(!(ot===r?rn===on||R(rn,on,s,T,M):ot)){Qe=!1;break}He||(He=xe=="constructor")}if(Qe&&!He){var Vn=e.constructor,st=n.constructor;Vn!=st&&"constructor"in e&&"constructor"in n&&!(typeof Vn=="function"&&Vn instanceof Vn&&typeof st=="function"&&st instanceof st)&&(Qe=!1)}return M.delete(e),M.delete(n),Qe}function Nt(e){return ga(hs(e,r,Ps),e+"")}function oa(e){return Ro(e,Un,ua)}function sa(e){return Ro(e,Xn,us)}var la=$r?function(e){return $r.get(e)}:Sa;function pi(e){for(var n=e.name+"",s=lr[n],T=fn.call(lr,n)?s.length:0;T--;){var R=s[T],M=R.func;if(M==null||M==e)return R.name}return n}function dr(e){var n=fn.call(k,"placeholder")?k:e;return n.placeholder}function Be(){var e=k.iteratee||xa;return e=e===xa?Io:e,arguments.length?e(arguments[0],arguments[1]):e}function ui(e,n){var s=e.__data__;return Mu(n)?s[typeof n=="string"?"string":"hash"]:s.map}function pa(e){for(var n=Un(e),s=n.length;s--;){var T=n[s],R=e[T];n[s]=[T,R,fs(R)]}return n}function Xt(e,n){var s=zl(e,n);return wo(s)?s:r}function wu(e){var n=fn.call(e,Gt),s=e[Gt];try{e[Gt]=r;var T=!0}catch(M){}var R=zr.call(e);return T&&(n?e[Gt]=s:delete e[Gt]),R}var ua=Li?function(e){return e==null?[]:(e=yn(e),Lt(Li(e),function(n){return mo.call(e,n)}))}:Da,us=Li?function(e){for(var n=[];e;)Mt(n,ua(e)),e=Yr(e);return n}:Da,Hn=Gn;(Mi&&Hn(new Mi(new ArrayBuffer(1)))!=et||vr&&Hn(new vr)!=nn||Bi&&Hn(Bi.resolve())!=Wn||sr&&Hn(new sr)!=un||Ar&&Hn(new Ar)!=Qn)&&(Hn=function(e){var n=Gn(e),s=n==bn?e.constructor:r,T=s?Jt(s):"";if(T)switch(T){case fp:return et;case gp:return nn;case hp:return Wn;case mp:return un;case _p:return Qn}return n});function Iu(e,n,s){for(var T=-1,R=s.length;++T<R;){var M=s[T],j=M.size;switch(M.type){case"drop":e+=j;break;case"dropRight":n-=j;break;case"take":n=zn(n,e+j);break;case"takeRight":e=In(e,n-j);break}}return{start:e,end:n}}function Ou(e){var n=e.match(be);return n?n[1].split(ke):[]}function cs(e,n,s){n=jt(n,e);for(var T=-1,R=n.length,M=!1;++T<R;){var j=Pt(n[T]);if(!(M=e!=null&&s(e,j)))break;e=e[j]}return M||++T!=R?M:(R=e==null?0:e.length,!!R&&_i(R)&&wt(j,R)&&(Ve(e)||Qt(e)))}function qu(e){var n=e.length,s=new e.constructor(n);return n&&typeof e[0]=="string"&&fn.call(e,"index")&&(s.index=e.index,s.input=e.input),s}function ds(e){return typeof e.constructor=="function"&&!Rr(e)?pr(Yr(e)):{}}function ku(e,n,s){var T=e.constructor;switch(n){case tn:return ra(e);case Dn:case Nn:return new T(+e);case et:return yu(e,s);case qn:case ce:case J:case ue:case Ce:case ie:case ye:case pe:case Ee:return Vo(e,s);case nn:return new T;case Yn:case gn:return new T(e);case On:return vu(e);case un:return new T;case xt:return Au(e)}}function Uu(e,n){var s=n.length;if(!s)return e;var T=s-1;return n[T]=(s>1?"& ":"")+n[T],n=n.join(s>2?", ":" "),e.replace(Ae,`{
/* [wrapped with `+n+`] */
`)}function Lu(e){return Ve(e)||Qt(e)||!!(_o&&e&&e[_o])}function wt(e,n){var s=typeof e;return n=n==null?W:n,!!n&&(s=="number"||s!="symbol"&&Ye.test(e))&&e>-1&&e%1==0&&e<n}function Kn(e,n,s){if(!Pn(s))return!1;var T=typeof n;return(T=="number"?Zn(s)&&wt(n,s.length):T=="string"&&n in s)?vt(s[n],e):!1}function ca(e,n){if(Ve(e))return!1;var s=typeof e;return s=="number"||s=="symbol"||s=="boolean"||e==null||at(e)?!0:H.test(e)||!q.test(e)||n!=null&&e in yn(n)}function Mu(e){var n=typeof e;return n=="string"||n=="number"||n=="symbol"||n=="boolean"?e!=="__proto__":e===null}function da(e){var n=pi(e),s=k[n];if(typeof s!="function"||!(n in an.prototype))return!1;if(e===s)return!0;var T=la(s);return!!T&&e===T[0]}function Bu(e){return!!fo&&fo in e}var Fu=Br?It:Ra;function Rr(e){var n=e&&e.constructor,s=typeof n=="function"&&n.prototype||or;return e===s}function fs(e){return e===e&&!Pn(e)}function gs(e,n){return function(s){return s==null?!1:s[e]===n&&(n!==r||e in yn(s))}}function zu(e){var n=hi(e,function(T){return s.size===h&&s.clear(),T}),s=n.cache;return n}function Hu(e,n){var s=e[1],T=n[1],R=s|T,M=R<(A|C|N),j=T==N&&s==S||T==N&&s==I&&e[7].length<=n[8]||T==(N|I)&&n[7].length<=n[8]&&s==S;if(!(M||j))return e;T&A&&(e[2]=n[2],R|=s&A?0:E);var V=n[3];if(V){var X=e[3];e[3]=X?Zo(X,V,n[4]):V,e[4]=X?Bt(e[3],i):n[4]}return V=n[5],V&&(X=e[5],e[5]=X?Xo(X,V,n[6]):V,e[6]=X?Bt(e[5],i):n[6]),V=n[7],V&&(e[7]=V),T&N&&(e[8]=e[8]==null?n[8]:zn(e[8],n[8])),e[9]==null&&(e[9]=n[9]),e[0]=n[0],e[1]=R,e}function ju(e){var n=[];if(e!=null)for(var s in yn(e))n.push(s);return n}function Yu(e){return zr.call(e)}function hs(e,n,s){return n=In(n===r?e.length-1:n,0),function(){for(var T=arguments,R=-1,M=In(T.length-n,0),j=ee(M);++R<M;)j[R]=T[n+R];R=-1;for(var V=ee(n+1);++R<n;)V[R]=T[R];return V[n]=s(j),tt(e,this,V)}}function ms(e,n){return n.length<2?e:Zt(e,ft(n,0,-1))}function Wu(e,n){for(var s=e.length,T=zn(n.length,s),R=$n(e);T--;){var M=n[T];e[T]=wt(M,s)?R[M]:r}return e}function fa(e,n){if(!(n==="constructor"&&typeof e[n]=="function")&&n!="__proto__")return e[n]}var _s=vs(Fo),Nr=op||function(e,n){return Mn.setTimeout(e,n)},ga=vs(gu);function ys(e,n,s){var T=n+"";return ga(e,Uu(T,Gu(Ou(T),s)))}function vs(e){var n=0,s=0;return function(){var T=up(),R=L-(T-s);if(s=T,R>0){if(++n>=U)return arguments[0]}else n=0;return e.apply(r,arguments)}}function ci(e,n){var s=-1,T=e.length,R=T-1;for(n=n===r?T:n;++s<n;){var M=Zi(s,R),j=e[M];e[M]=e[s],e[s]=j}return e.length=n,e}var As=zu(function(e){var n=[];return e.charCodeAt(0)===46&&n.push(""),e.replace(K,function(s,T,R,M){n.push(R?M.replace($e,"$1"):T||s)}),n});function Pt(e){if(typeof e=="string"||at(e))return e;var n=e+"";return n=="0"&&1/e==-G?"-0":n}function Jt(e){if(e!=null){try{return Fr.call(e)}catch(n){}try{return e+""}catch(n){}}return""}function Gu(e,n){return pt(Pe,function(s){var T="_."+s[0];n&s[1]&&!qr(e,T)&&e.push(T)}),e.sort()}function Ts(e){if(e instanceof an)return e.clone();var n=new ct(e.__wrapped__,e.__chain__);return n.__actions__=$n(e.__actions__),n.__index__=e.__index__,n.__values__=e.__values__,n}function Ku(e,n,s){(s?Kn(e,n,s):n===r)?n=1:n=In(Je(n),0);var T=e==null?0:e.length;if(!T||n<1)return[];for(var R=0,M=0,j=ee(Kr(T/n));R<T;)j[M++]=ft(e,R,R+=n);return j}function Vu(e){for(var n=-1,s=e==null?0:e.length,T=0,R=[];++n<s;){var M=e[n];M&&(R[T++]=M)}return R}function $u(){var e=arguments.length;if(!e)return[];for(var n=ee(e-1),s=arguments[0],T=e;T--;)n[T-1]=arguments[T];return Mt(Ve(s)?$n(s):[s],Bn(n,1))}var Zu=en(function(e,n){return Cn(e)?Pr(e,Bn(n,1,Cn,!0)):[]}),Xu=en(function(e,n){var s=gt(n);return Cn(s)&&(s=r),Cn(e)?Pr(e,Bn(n,1,Cn,!0),Be(s,2)):[]}),Ju=en(function(e,n){var s=gt(n);return Cn(s)&&(s=r),Cn(e)?Pr(e,Bn(n,1,Cn,!0),r,s):[]});function Qu(e,n,s){var T=e==null?0:e.length;return T?(n=s||n===r?1:Je(n),ft(e,n<0?0:n,T)):[]}function ec(e,n,s){var T=e==null?0:e.length;return T?(n=s||n===r?1:Je(n),n=T-n,ft(e,0,n<0?0:n)):[]}function nc(e,n){return e&&e.length?ri(e,Be(n,3),!0,!0):[]}function tc(e,n){return e&&e.length?ri(e,Be(n,3),!0):[]}function rc(e,n,s,T){var R=e==null?0:e.length;return R?(s&&typeof s!="number"&&Kn(e,n,s)&&(s=0,T=R),$p(e,n,s,T)):[]}function Es(e,n,s){var T=e==null?0:e.length;if(!T)return-1;var R=s==null?0:Je(s);return R<0&&(R=In(T+R,0)),kr(e,Be(n,3),R)}function bs(e,n,s){var T=e==null?0:e.length;if(!T)return-1;var R=T-1;return s!==r&&(R=Je(s),R=s<0?In(T+R,0):zn(R,T-1)),kr(e,Be(n,3),R,!0)}function Ps(e){var n=e==null?0:e.length;return n?Bn(e,1):[]}function ic(e){var n=e==null?0:e.length;return n?Bn(e,G):[]}function ac(e,n){var s=e==null?0:e.length;return s?(n=n===r?1:Je(n),Bn(e,n)):[]}function oc(e){for(var n=-1,s=e==null?0:e.length,T={};++n<s;){var R=e[n];T[R[0]]=R[1]}return T}function xs(e){return e&&e.length?e[0]:r}function sc(e,n,s){var T=e==null?0:e.length;if(!T)return-1;var R=s==null?0:Je(s);return R<0&&(R=In(T+R,0)),rr(e,n,R)}function lc(e){var n=e==null?0:e.length;return n?ft(e,0,-1):[]}var pc=en(function(e){var n=En(e,na);return n.length&&n[0]===e[0]?Wi(n):[]}),uc=en(function(e){var n=gt(e),s=En(e,na);return n===gt(s)?n=r:s.pop(),s.length&&s[0]===e[0]?Wi(s,Be(n,2)):[]}),cc=en(function(e){var n=gt(e),s=En(e,na);return n=typeof n=="function"?n:r,n&&s.pop(),s.length&&s[0]===e[0]?Wi(s,r,n):[]});function dc(e,n){return e==null?"":lp.call(e,n)}function gt(e){var n=e==null?0:e.length;return n?e[n-1]:r}function fc(e,n,s){var T=e==null?0:e.length;if(!T)return-1;var R=T;return s!==r&&(R=Je(s),R=R<0?In(T+R,0):zn(R,T-1)),n===n?Gl(e,n,R):kr(e,io,R,!0)}function gc(e,n){return e&&e.length?Uo(e,Je(n)):r}var hc=en(Cs);function Cs(e,n){return e&&e.length&&n&&n.length?$i(e,n):e}function mc(e,n,s){return e&&e.length&&n&&n.length?$i(e,n,Be(s,2)):e}function _c(e,n,s){return e&&e.length&&n&&n.length?$i(e,n,r,s):e}var yc=Nt(function(e,n){var s=e==null?0:e.length,T=zi(e,n);return Bo(e,En(n,function(R){return wt(R,s)?+R:R}).sort($o)),T});function vc(e,n){var s=[];if(!(e&&e.length))return s;var T=-1,R=[],M=e.length;for(n=Be(n,3);++T<M;){var j=e[T];n(j,T,e)&&(s.push(j),R.push(T))}return Bo(e,R),s}function ha(e){return e==null?e:dp.call(e)}function Ac(e,n,s){var T=e==null?0:e.length;return T?(s&&typeof s!="number"&&Kn(e,n,s)?(n=0,s=T):(n=n==null?0:Je(n),s=s===r?T:Je(s)),ft(e,n,s)):[]}function Tc(e,n){return ti(e,n)}function Ec(e,n,s){return Ji(e,n,Be(s,2))}function bc(e,n){var s=e==null?0:e.length;if(s){var T=ti(e,n);if(T<s&&vt(e[T],n))return T}return-1}function Pc(e,n){return ti(e,n,!0)}function xc(e,n,s){return Ji(e,n,Be(s,2),!0)}function Cc(e,n){var s=e==null?0:e.length;if(s){var T=ti(e,n,!0)-1;if(vt(e[T],n))return T}return-1}function Sc(e){return e&&e.length?zo(e):[]}function Dc(e,n){return e&&e.length?zo(e,Be(n,2)):[]}function Rc(e){var n=e==null?0:e.length;return n?ft(e,1,n):[]}function Nc(e,n,s){return e&&e.length?(n=s||n===r?1:Je(n),ft(e,0,n<0?0:n)):[]}function wc(e,n,s){var T=e==null?0:e.length;return T?(n=s||n===r?1:Je(n),n=T-n,ft(e,n<0?0:n,T)):[]}function Ic(e,n){return e&&e.length?ri(e,Be(n,3),!1,!0):[]}function Oc(e,n){return e&&e.length?ri(e,Be(n,3)):[]}var qc=en(function(e){return Ht(Bn(e,1,Cn,!0))}),kc=en(function(e){var n=gt(e);return Cn(n)&&(n=r),Ht(Bn(e,1,Cn,!0),Be(n,2))}),Uc=en(function(e){var n=gt(e);return n=typeof n=="function"?n:r,Ht(Bn(e,1,Cn,!0),r,n)});function Lc(e){return e&&e.length?Ht(e):[]}function Mc(e,n){return e&&e.length?Ht(e,Be(n,2)):[]}function Bc(e,n){return n=typeof n=="function"?n:r,e&&e.length?Ht(e,r,n):[]}function ma(e){if(!(e&&e.length))return[];var n=0;return e=Lt(e,function(s){if(Cn(s))return n=In(s.length,n),!0}),Oi(n,function(s){return En(e,Ni(s))})}function Ss(e,n){if(!(e&&e.length))return[];var s=ma(e);return n==null?s:En(s,function(T){return tt(n,r,T)})}var Fc=en(function(e,n){return Cn(e)?Pr(e,n):[]}),zc=en(function(e){return ea(Lt(e,Cn))}),Hc=en(function(e){var n=gt(e);return Cn(n)&&(n=r),ea(Lt(e,Cn),Be(n,2))}),jc=en(function(e){var n=gt(e);return n=typeof n=="function"?n:r,ea(Lt(e,Cn),r,n)}),Yc=en(ma);function Wc(e,n){return Wo(e||[],n||[],br)}function Gc(e,n){return Wo(e||[],n||[],Sr)}var Kc=en(function(e){var n=e.length,s=n>1?e[n-1]:r;return s=typeof s=="function"?(e.pop(),s):r,Ss(e,s)});function Ds(e){var n=k(e);return n.__chain__=!0,n}function Vc(e,n){return n(e),e}function di(e,n){return n(e)}var $c=Nt(function(e){var n=e.length,s=n?e[0]:0,T=this.__wrapped__,R=function(M){return zi(M,e)};return n>1||this.__actions__.length||!(T instanceof an)||!wt(s)?this.thru(R):(T=T.slice(s,+s+(n?1:0)),T.__actions__.push({func:di,args:[R],thisArg:r}),new ct(T,this.__chain__).thru(function(M){return n&&!M.length&&M.push(r),M}))});function Zc(){return Ds(this)}function Xc(){return new ct(this.value(),this.__chain__)}function Jc(){this.__values__===r&&(this.__values__=Hs(this.value()));var e=this.__index__>=this.__values__.length,n=e?r:this.__values__[this.__index__++];return{done:e,value:n}}function Qc(){return this}function ed(e){for(var n,s=this;s instanceof Xr;){var T=Ts(s);T.__index__=0,T.__values__=r,n?R.__wrapped__=T:n=T;var R=T;s=s.__wrapped__}return R.__wrapped__=e,n}function nd(){var e=this.__wrapped__;if(e instanceof an){var n=e;return this.__actions__.length&&(n=new an(this)),n=n.reverse(),n.__actions__.push({func:di,args:[ha],thisArg:r}),new ct(n,this.__chain__)}return this.thru(ha)}function td(){return Yo(this.__wrapped__,this.__actions__)}var rd=ii(function(e,n,s){fn.call(e,s)?++e[s]:Dt(e,s,1)});function id(e,n,s){var T=Ve(e)?to:Vp;return s&&Kn(e,n,s)&&(n=r),T(e,Be(n,3))}function ad(e,n){var s=Ve(e)?Lt:So;return s(e,Be(n,3))}var od=ns(Es),sd=ns(bs);function ld(e,n){return Bn(fi(e,n),1)}function pd(e,n){return Bn(fi(e,n),G)}function ud(e,n,s){return s=s===r?1:Je(s),Bn(fi(e,n),s)}function Rs(e,n){var s=Ve(e)?pt:zt;return s(e,Be(n,3))}function Ns(e,n){var s=Ve(e)?Rl:Co;return s(e,Be(n,3))}var cd=ii(function(e,n,s){fn.call(e,s)?e[s].push(n):Dt(e,s,[n])});function dd(e,n,s,T){e=Zn(e)?e:gr(e),s=s&&!T?Je(s):0;var R=e.length;return s<0&&(s=In(R+s,0)),yi(e)?s<=R&&e.indexOf(n,s)>-1:!!R&&rr(e,n,s)>-1}var fd=en(function(e,n,s){var T=-1,R=typeof n=="function",M=Zn(e)?ee(e.length):[];return zt(e,function(j){M[++T]=R?tt(n,j,s):xr(j,n,s)}),M}),gd=ii(function(e,n,s){Dt(e,s,n)});function fi(e,n){var s=Ve(e)?En:Oo;return s(e,Be(n,3))}function hd(e,n,s,T){return e==null?[]:(Ve(n)||(n=n==null?[]:[n]),s=T?r:s,Ve(s)||(s=s==null?[]:[s]),Lo(e,n,s))}var md=ii(function(e,n,s){e[s?0:1].push(n)},function(){return[[],[]]});function _d(e,n,s){var T=Ve(e)?Di:oo,R=arguments.length<3;return T(e,Be(n,4),s,R,zt)}function yd(e,n,s){var T=Ve(e)?Nl:oo,R=arguments.length<3;return T(e,Be(n,4),s,R,Co)}function vd(e,n){var s=Ve(e)?Lt:So;return s(e,mi(Be(n,3)))}function Ad(e){var n=Ve(e)?Eo:du;return n(e)}function Td(e,n,s){(s?Kn(e,n,s):n===r)?n=1:n=Je(n);var T=Ve(e)?jp:fu;return T(e,n)}function Ed(e){var n=Ve(e)?Yp:hu;return n(e)}function bd(e){if(e==null)return 0;if(Zn(e))return yi(e)?ar(e):e.length;var n=Hn(e);return n==nn||n==un?e.size:Ki(e).length}function Pd(e,n,s){var T=Ve(e)?Ri:mu;return s&&Kn(e,n,s)&&(n=r),T(e,Be(n,3))}var xd=en(function(e,n){if(e==null)return[];var s=n.length;return s>1&&Kn(e,n[0],n[1])?n=[]:s>2&&Kn(n[0],n[1],n[2])&&(n=[n[0]]),Lo(e,Bn(n,1),[])}),gi=ap||function(){return Mn.Date.now()};function Cd(e,n){if(typeof n!="function")throw new ut(p);return e=Je(e),function(){if(--e<1)return n.apply(this,arguments)}}function ws(e,n,s){return n=s?r:n,n=e&&n==null?e.length:n,Rt(e,N,r,r,r,r,n)}function Is(e,n){var s;if(typeof n!="function")throw new ut(p);return e=Je(e),function(){return--e>0&&(s=n.apply(this,arguments)),e<=1&&(n=r),s}}var _a=en(function(e,n,s){var T=A;if(s.length){var R=Bt(s,dr(_a));T|=x}return Rt(e,T,n,s,R)}),Os=en(function(e,n,s){var T=A|C;if(s.length){var R=Bt(s,dr(Os));T|=x}return Rt(n,T,e,s,R)});function qs(e,n,s){n=s?r:n;var T=Rt(e,S,r,r,r,r,r,n);return T.placeholder=qs.placeholder,T}function ks(e,n,s){n=s?r:n;var T=Rt(e,b,r,r,r,r,r,n);return T.placeholder=ks.placeholder,T}function Us(e,n,s){var T,R,M,j,V,X,se=0,le=!1,he=!1,xe=!0;if(typeof e!="function")throw new ut(p);n=ht(n)||0,Pn(s)&&(le=!!s.leading,he="maxWait"in s,M=he?In(ht(s.maxWait)||0,n):M,xe="trailing"in s?!!s.trailing:xe);function we(Sn){var At=T,qt=R;return T=R=r,se=Sn,j=e.apply(qt,At),j}function ze(Sn){return se=Sn,V=Nr(rn,n),le?we(Sn):j}function Qe(Sn){var At=Sn-X,qt=Sn-se,nl=n-At;return he?zn(nl,M-qt):nl}function He(Sn){var At=Sn-X,qt=Sn-se;return X===r||At>=n||At<0||he&&qt>=M}function rn(){var Sn=gi();if(He(Sn))return on(Sn);V=Nr(rn,Qe(Sn))}function on(Sn){return V=r,xe&&T?we(Sn):(T=R=r,j)}function ot(){V!==r&&Go(V),se=0,T=X=R=V=r}function Vn(){return V===r?j:on(gi())}function st(){var Sn=gi(),At=He(Sn);if(T=arguments,R=this,X=Sn,At){if(V===r)return ze(X);if(he)return Go(V),V=Nr(rn,n),we(X)}return V===r&&(V=Nr(rn,n)),j}return st.cancel=ot,st.flush=Vn,st}var Sd=en(function(e,n){return xo(e,1,n)}),Dd=en(function(e,n,s){return xo(e,ht(n)||0,s)});function Rd(e){return Rt(e,w)}function hi(e,n){if(typeof e!="function"||n!=null&&typeof n!="function")throw new ut(p);var s=function(){var T=arguments,R=n?n.apply(this,T):T[0],M=s.cache;if(M.has(R))return M.get(R);var j=e.apply(this,T);return s.cache=M.set(R,j)||M,j};return s.cache=new(hi.Cache||St),s}hi.Cache=St;function mi(e){if(typeof e!="function")throw new ut(p);return function(){var n=arguments;switch(n.length){case 0:return!e.call(this);case 1:return!e.call(this,n[0]);case 2:return!e.call(this,n[0],n[1]);case 3:return!e.call(this,n[0],n[1],n[2])}return!e.apply(this,n)}}function Nd(e){return Is(2,e)}var wd=_u(function(e,n){n=n.length==1&&Ve(n[0])?En(n[0],rt(Be())):En(Bn(n,1),rt(Be()));var s=n.length;return en(function(T){for(var R=-1,M=zn(T.length,s);++R<M;)T[R]=n[R].call(this,T[R]);return tt(e,this,T)})}),ya=en(function(e,n){var s=Bt(n,dr(ya));return Rt(e,x,r,n,s)}),Ls=en(function(e,n){var s=Bt(n,dr(Ls));return Rt(e,D,r,n,s)}),Id=Nt(function(e,n){return Rt(e,I,r,r,r,n)});function Od(e,n){if(typeof e!="function")throw new ut(p);return n=n===r?n:Je(n),en(e,n)}function qd(e,n){if(typeof e!="function")throw new ut(p);return n=n==null?0:In(Je(n),0),en(function(s){var T=s[n],R=Yt(s,0,n);return T&&Mt(R,T),tt(e,this,R)})}function kd(e,n,s){var T=!0,R=!0;if(typeof e!="function")throw new ut(p);return Pn(s)&&(T="leading"in s?!!s.leading:T,R="trailing"in s?!!s.trailing:R),Us(e,n,{leading:T,maxWait:n,trailing:R})}function Ud(e){return ws(e,1)}function Ld(e,n){return ya(ta(n),e)}function Md(){if(!arguments.length)return[];var e=arguments[0];return Ve(e)?e:[e]}function Bd(e){return dt(e,f)}function Fd(e,n){return n=typeof n=="function"?n:r,dt(e,f,n)}function zd(e){return dt(e,m|f)}function Hd(e,n){return n=typeof n=="function"?n:r,dt(e,m|f,n)}function jd(e,n){return n==null||Po(e,n,Un(n))}function vt(e,n){return e===n||e!==e&&n!==n}var Yd=li(Yi),Wd=li(function(e,n){return e>=n}),Qt=No(function(){return arguments}())?No:function(e){return xn(e)&&fn.call(e,"callee")&&!mo.call(e,"callee")},Ve=ee.isArray,Gd=Za?rt(Za):eu;function Zn(e){return e!=null&&_i(e.length)&&!It(e)}function Cn(e){return xn(e)&&Zn(e)}function Kd(e){return e===!0||e===!1||xn(e)&&Gn(e)==Dn}var Wt=sp||Ra,Vd=Xa?rt(Xa):nu;function $d(e){return xn(e)&&e.nodeType===1&&!wr(e)}function Zd(e){if(e==null)return!0;if(Zn(e)&&(Ve(e)||typeof e=="string"||typeof e.splice=="function"||Wt(e)||fr(e)||Qt(e)))return!e.length;var n=Hn(e);if(n==nn||n==un)return!e.size;if(Rr(e))return!Ki(e).length;for(var s in e)if(fn.call(e,s))return!1;return!0}function Xd(e,n){return Cr(e,n)}function Jd(e,n,s){s=typeof s=="function"?s:r;var T=s?s(e,n):r;return T===r?Cr(e,n,r,s):!!T}function va(e){if(!xn(e))return!1;var n=Gn(e);return n==je||n==jn||typeof e.message=="string"&&typeof e.name=="string"&&!wr(e)}function Qd(e){return typeof e=="number"&&yo(e)}function It(e){if(!Pn(e))return!1;var n=Gn(e);return n==Ln||n==Ze||n==Rn||n==mt}function Ms(e){return typeof e=="number"&&e==Je(e)}function _i(e){return typeof e=="number"&&e>-1&&e%1==0&&e<=W}function Pn(e){var n=typeof e;return e!=null&&(n=="object"||n=="function")}function xn(e){return e!=null&&typeof e=="object"}var Bs=Ja?rt(Ja):ru;function ef(e,n){return e===n||Gi(e,n,pa(n))}function nf(e,n,s){return s=typeof s=="function"?s:r,Gi(e,n,pa(n),s)}function tf(e){return Fs(e)&&e!=+e}function rf(e){if(Fu(e))throw new Ke(c);return wo(e)}function af(e){return e===null}function of(e){return e==null}function Fs(e){return typeof e=="number"||xn(e)&&Gn(e)==Yn}function wr(e){if(!xn(e)||Gn(e)!=bn)return!1;var n=Yr(e);if(n===null)return!0;var s=fn.call(n,"constructor")&&n.constructor;return typeof s=="function"&&s instanceof s&&Fr.call(s)==np}var Aa=Qa?rt(Qa):iu;function sf(e){return Ms(e)&&e>=-W&&e<=W}var zs=eo?rt(eo):au;function yi(e){return typeof e=="string"||!Ve(e)&&xn(e)&&Gn(e)==gn}function at(e){return typeof e=="symbol"||xn(e)&&Gn(e)==xt}var fr=no?rt(no):ou;function lf(e){return e===r}function pf(e){return xn(e)&&Hn(e)==Qn}function uf(e){return xn(e)&&Gn(e)==hr}var cf=li(Vi),df=li(function(e,n){return e<=n});function Hs(e){if(!e)return[];if(Zn(e))return yi(e)?_t(e):$n(e);if(yr&&e[yr])return jl(e[yr]());var n=Hn(e),s=n==nn?ki:n==un?Ur:gr;return s(e)}function Ot(e){if(!e)return e===0?e:0;if(e=ht(e),e===G||e===-G){var n=e<0?-1:1;return n*te}return e===e?e:0}function Je(e){var n=Ot(e),s=n%1;return n===n?s?n-s:n:0}function js(e){return e?$t(Je(e),0,ge):0}function ht(e){if(typeof e=="number")return e;if(at(e))return oe;if(Pn(e)){var n=typeof e.valueOf=="function"?e.valueOf():e;e=Pn(n)?n+"":n}if(typeof e!="string")return e===0?e:+e;e=so(e);var s=fe.test(e);return s||_e.test(e)?Cl(e.slice(2),s?2:8):me.test(e)?oe:+e}function Ys(e){return bt(e,Xn(e))}function ff(e){return e?$t(Je(e),-W,W):e===0?e:0}function cn(e){return e==null?"":it(e)}var gf=ur(function(e,n){if(Rr(n)||Zn(n)){bt(n,Un(n),e);return}for(var s in n)fn.call(n,s)&&br(e,s,n[s])}),Ws=ur(function(e,n){bt(n,Xn(n),e)}),vi=ur(function(e,n,s,T){bt(n,Xn(n),e,T)}),hf=ur(function(e,n,s,T){bt(n,Un(n),e,T)}),mf=Nt(zi);function _f(e,n){var s=pr(e);return n==null?s:bo(s,n)}var yf=en(function(e,n){e=yn(e);var s=-1,T=n.length,R=T>2?n[2]:r;for(R&&Kn(n[0],n[1],R)&&(T=1);++s<T;)for(var M=n[s],j=Xn(M),V=-1,X=j.length;++V<X;){var se=j[V],le=e[se];(le===r||vt(le,or[se])&&!fn.call(e,se))&&(e[se]=M[se])}return e}),vf=en(function(e){return e.push(r,ls),tt(Gs,r,e)});function Af(e,n){return ro(e,Be(n,3),Et)}function Tf(e,n){return ro(e,Be(n,3),ji)}function Ef(e,n){return e==null?e:Hi(e,Be(n,3),Xn)}function bf(e,n){return e==null?e:Do(e,Be(n,3),Xn)}function Pf(e,n){return e&&Et(e,Be(n,3))}function xf(e,n){return e&&ji(e,Be(n,3))}function Cf(e){return e==null?[]:ei(e,Un(e))}function Sf(e){return e==null?[]:ei(e,Xn(e))}function Ta(e,n,s){var T=e==null?r:Zt(e,n);return T===r?s:T}function Df(e,n){return e!=null&&cs(e,n,Zp)}function Ea(e,n){return e!=null&&cs(e,n,Xp)}var Rf=rs(function(e,n,s){n!=null&&typeof n.toString!="function"&&(n=zr.call(n)),e[n]=s},Pa(Jn)),Nf=rs(function(e,n,s){n!=null&&typeof n.toString!="function"&&(n=zr.call(n)),fn.call(e,n)?e[n].push(s):e[n]=[s]},Be),wf=en(xr);function Un(e){return Zn(e)?To(e):Ki(e)}function Xn(e){return Zn(e)?To(e,!0):su(e)}function If(e,n){var s={};return n=Be(n,3),Et(e,function(T,R,M){Dt(s,n(T,R,M),T)}),s}function Of(e,n){var s={};return n=Be(n,3),Et(e,function(T,R,M){Dt(s,R,n(T,R,M))}),s}var qf=ur(function(e,n,s){ni(e,n,s)}),Gs=ur(function(e,n,s,T){ni(e,n,s,T)}),kf=Nt(function(e,n){var s={};if(e==null)return s;var T=!1;n=En(n,function(M){return M=jt(M,e),T||(T=M.length>1),M}),bt(e,sa(e),s),T&&(s=dt(s,m|d|f,Du));for(var R=n.length;R--;)Qi(s,n[R]);return s});function Uf(e,n){return Ks(e,mi(Be(n)))}var Lf=Nt(function(e,n){return e==null?{}:pu(e,n)});function Ks(e,n){if(e==null)return{};var s=En(sa(e),function(T){return[T]});return n=Be(n),Mo(e,s,function(T,R){return n(T,R[0])})}function Mf(e,n,s){n=jt(n,e);var T=-1,R=n.length;for(R||(R=1,e=r);++T<R;){var M=e==null?r:e[Pt(n[T])];M===r&&(T=R,M=s),e=It(M)?M.call(e):M}return e}function Bf(e,n,s){return e==null?e:Sr(e,n,s)}function Ff(e,n,s,T){return T=typeof T=="function"?T:r,e==null?e:Sr(e,n,s,T)}var Vs=os(Un),$s=os(Xn);function zf(e,n,s){var T=Ve(e),R=T||Wt(e)||fr(e);if(n=Be(n,4),s==null){var M=e&&e.constructor;R?s=T?new M:[]:Pn(e)?s=It(M)?pr(Yr(e)):{}:s={}}return(R?pt:Et)(e,function(j,V,X){return n(s,j,V,X)}),s}function Hf(e,n){return e==null?!0:Qi(e,n)}function jf(e,n,s){return e==null?e:jo(e,n,ta(s))}function Yf(e,n,s,T){return T=typeof T=="function"?T:r,e==null?e:jo(e,n,ta(s),T)}function gr(e){return e==null?[]:qi(e,Un(e))}function Wf(e){return e==null?[]:qi(e,Xn(e))}function Gf(e,n,s){return s===r&&(s=n,n=r),s!==r&&(s=ht(s),s=s===s?s:0),n!==r&&(n=ht(n),n=n===n?n:0),$t(ht(e),n,s)}function Kf(e,n,s){return n=Ot(n),s===r?(s=n,n=0):s=Ot(s),e=ht(e),Jp(e,n,s)}function Vf(e,n,s){if(s&&typeof s!="boolean"&&Kn(e,n,s)&&(n=s=r),s===r&&(typeof n=="boolean"?(s=n,n=r):typeof e=="boolean"&&(s=e,e=r)),e===r&&n===r?(e=0,n=1):(e=Ot(e),n===r?(n=e,e=0):n=Ot(n)),e>n){var T=e;e=n,n=T}if(s||e%1||n%1){var R=vo();return zn(e+R*(n-e+xl("1e-"+((R+"").length-1))),n)}return Zi(e,n)}var $f=cr(function(e,n,s){return n=n.toLowerCase(),e+(s?Zs(n):n)});function Zs(e){return ba(cn(e).toLowerCase())}function Xs(e){return e=cn(e),e&&e.replace(Xe,Ml).replace(hl,"")}function Zf(e,n,s){e=cn(e),n=it(n);var T=e.length;s=s===r?T:$t(Je(s),0,T);var R=s;return s-=n.length,s>=0&&e.slice(s,R)==n}function Xf(e){return e=cn(e),e&&sn.test(e)?e.replace(Fe,Bl):e}function Jf(e){return e=cn(e),e&&$.test(e)?e.replace(ne,"\\$&"):e}var Qf=cr(function(e,n,s){return e+(s?"-":"")+n.toLowerCase()}),eg=cr(function(e,n,s){return e+(s?" ":"")+n.toLowerCase()}),ng=es("toLowerCase");function tg(e,n,s){e=cn(e),n=Je(n);var T=n?ar(e):0;if(!n||T>=n)return e;var R=(n-T)/2;return si(Vr(R),s)+e+si(Kr(R),s)}function rg(e,n,s){e=cn(e),n=Je(n);var T=n?ar(e):0;return n&&T<n?e+si(n-T,s):e}function ig(e,n,s){e=cn(e),n=Je(n);var T=n?ar(e):0;return n&&T<n?si(n-T,s)+e:e}function ag(e,n,s){return s||n==null?n=0:n&&(n=+n),cp(cn(e).replace(re,""),n||0)}function og(e,n,s){return(s?Kn(e,n,s):n===r)?n=1:n=Je(n),Xi(cn(e),n)}function sg(){var e=arguments,n=cn(e[0]);return e.length<3?n:n.replace(e[1],e[2])}var lg=cr(function(e,n,s){return e+(s?"_":"")+n.toLowerCase()});function pg(e,n,s){return s&&typeof s!="number"&&Kn(e,n,s)&&(n=s=r),s=s===r?ge:s>>>0,s?(e=cn(e),e&&(typeof n=="string"||n!=null&&!Aa(n))&&(n=it(n),!n&&ir(e))?Yt(_t(e),0,s):e.split(n,s)):[]}var ug=cr(function(e,n,s){return e+(s?" ":"")+ba(n)});function cg(e,n,s){return e=cn(e),s=s==null?0:$t(Je(s),0,e.length),n=it(n),e.slice(s,s+n.length)==n}function dg(e,n,s){var T=k.templateSettings;s&&Kn(e,n,s)&&(n=r),e=cn(e),n=vi({},n,T,ss);var R=vi({},n.imports,T.imports,ss),M=Un(R),j=qi(R,M),V,X,se=0,le=n.interpolate||Re,he="__p += '",xe=Ui((n.escape||Re).source+"|"+le.source+"|"+(le===kn?ln:Re).source+"|"+(n.evaluate||Re).source+"|$","g"),we="//# sourceURL="+(fn.call(n,"sourceURL")?(n.sourceURL+"").replace(/\s/g," "):"lodash.templateSources["+ ++Al+"]")+`
`;e.replace(xe,function(He,rn,on,ot,Vn,st){return on||(on=ot),he+=e.slice(se,st).replace(Fn,Fl),rn&&(V=!0,he+=`' +
__e(`+rn+`) +
'`),Vn&&(X=!0,he+=`';
`+Vn+`;
__p += '`),on&&(he+=`' +
((__t = (`+on+`)) == null ? '' : __t) +
'`),se=st+He.length,He}),he+=`';
`;var ze=fn.call(n,"variable")&&n.variable;if(!ze)he=`with (obj) {
`+he+`
}
`;else if(Me.test(ze))throw new Ke(a);he=(X?he.replace(Oe,""):he).replace(Ue,"$1").replace(Ne,"$1;"),he="function("+(ze||"obj")+`) {
`+(ze?"":`obj || (obj = {});
`)+"var __t, __p = ''"+(V?", __e = _.escape":"")+(X?`, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
`:`;
`)+he+`return __p
}`;var Qe=Qs(function(){return pn(M,we+"return "+he).apply(r,j)});if(Qe.source=he,va(Qe))throw Qe;return Qe}function fg(e){return cn(e).toLowerCase()}function gg(e){return cn(e).toUpperCase()}function hg(e,n,s){if(e=cn(e),e&&(s||n===r))return so(e);if(!e||!(n=it(n)))return e;var T=_t(e),R=_t(n),M=lo(T,R),j=po(T,R)+1;return Yt(T,M,j).join("")}function mg(e,n,s){if(e=cn(e),e&&(s||n===r))return e.slice(0,co(e)+1);if(!e||!(n=it(n)))return e;var T=_t(e),R=po(T,_t(n))+1;return Yt(T,0,R).join("")}function _g(e,n,s){if(e=cn(e),e&&(s||n===r))return e.replace(re,"");if(!e||!(n=it(n)))return e;var T=_t(e),R=lo(T,_t(n));return Yt(T,R).join("")}function yg(e,n){var s=O,T=B;if(Pn(n)){var R="separator"in n?n.separator:R;s="length"in n?Je(n.length):s,T="omission"in n?it(n.omission):T}e=cn(e);var M=e.length;if(ir(e)){var j=_t(e);M=j.length}if(s>=M)return e;var V=s-ar(T);if(V<1)return T;var X=j?Yt(j,0,V).join(""):e.slice(0,V);if(R===r)return X+T;if(j&&(V+=X.length-V),Aa(R)){if(e.slice(V).search(R)){var se,le=X;for(R.global||(R=Ui(R.source,cn(qe.exec(R))+"g")),R.lastIndex=0;se=R.exec(le);)var he=se.index;X=X.slice(0,he===r?V:he)}}else if(e.indexOf(it(R),V)!=V){var xe=X.lastIndexOf(R);xe>-1&&(X=X.slice(0,xe))}return X+T}function vg(e){return e=cn(e),e&&Le.test(e)?e.replace(Se,Kl):e}var Ag=cr(function(e,n,s){return e+(s?" ":"")+n.toUpperCase()}),ba=es("toUpperCase");function Js(e,n,s){return e=cn(e),n=s?r:n,n===r?Hl(e)?Zl(e):Ol(e):e.match(n)||[]}var Qs=en(function(e,n){try{return tt(e,r,n)}catch(s){return va(s)?s:new Ke(s)}}),Tg=Nt(function(e,n){return pt(n,function(s){s=Pt(s),Dt(e,s,_a(e[s],e))}),e});function Eg(e){var n=e==null?0:e.length,s=Be();return e=n?En(e,function(T){if(typeof T[1]!="function")throw new ut(p);return[s(T[0]),T[1]]}):[],en(function(T){for(var R=-1;++R<n;){var M=e[R];if(tt(M[0],this,T))return tt(M[1],this,T)}})}function bg(e){return Kp(dt(e,m))}function Pa(e){return function(){return e}}function Pg(e,n){return e==null||e!==e?n:e}var xg=ts(),Cg=ts(!0);function Jn(e){return e}function xa(e){return Io(typeof e=="function"?e:dt(e,m))}function Sg(e){return qo(dt(e,m))}function Dg(e,n){return ko(e,dt(n,m))}var Rg=en(function(e,n){return function(s){return xr(s,e,n)}}),Ng=en(function(e,n){return function(s){return xr(e,s,n)}});function Ca(e,n,s){var T=Un(n),R=ei(n,T);s==null&&!(Pn(n)&&(R.length||!T.length))&&(s=n,n=e,e=this,R=ei(n,Un(n)));var M=!(Pn(s)&&"chain"in s)||!!s.chain,j=It(e);return pt(R,function(V){var X=n[V];e[V]=X,j&&(e.prototype[V]=function(){var se=this.__chain__;if(M||se){var le=e(this.__wrapped__),he=le.__actions__=$n(this.__actions__);return he.push({func:X,args:arguments,thisArg:e}),le.__chain__=se,le}return X.apply(e,Mt([this.value()],arguments))})}),e}function wg(){return Mn._===this&&(Mn._=tp),this}function Sa(){}function Ig(e){return e=Je(e),en(function(n){return Uo(n,e)})}var Og=ia(En),qg=ia(to),kg=ia(Ri);function el(e){return ca(e)?Ni(Pt(e)):uu(e)}function Ug(e){return function(n){return e==null?r:Zt(e,n)}}var Lg=is(),Mg=is(!0);function Da(){return[]}function Ra(){return!1}function Bg(){return{}}function Fg(){return""}function zg(){return!0}function Hg(e,n){if(e=Je(e),e<1||e>W)return[];var s=ge,T=zn(e,ge);n=Be(n),e-=ge;for(var R=Oi(T,n);++s<e;)n(s);return R}function jg(e){return Ve(e)?En(e,Pt):at(e)?[e]:$n(As(cn(e)))}function Yg(e){var n=++ep;return cn(e)+n}var Wg=oi(function(e,n){return e+n},0),Gg=aa("ceil"),Kg=oi(function(e,n){return e/n},1),Vg=aa("floor");function $g(e){return e&&e.length?Qr(e,Jn,Yi):r}function Zg(e,n){return e&&e.length?Qr(e,Be(n,2),Yi):r}function Xg(e){return ao(e,Jn)}function Jg(e,n){return ao(e,Be(n,2))}function Qg(e){return e&&e.length?Qr(e,Jn,Vi):r}function eh(e,n){return e&&e.length?Qr(e,Be(n,2),Vi):r}var nh=oi(function(e,n){return e*n},1),th=aa("round"),rh=oi(function(e,n){return e-n},0);function ih(e){return e&&e.length?Ii(e,Jn):0}function ah(e,n){return e&&e.length?Ii(e,Be(n,2)):0}return k.after=Cd,k.ary=ws,k.assign=gf,k.assignIn=Ws,k.assignInWith=vi,k.assignWith=hf,k.at=mf,k.before=Is,k.bind=_a,k.bindAll=Tg,k.bindKey=Os,k.castArray=Md,k.chain=Ds,k.chunk=Ku,k.compact=Vu,k.concat=$u,k.cond=Eg,k.conforms=bg,k.constant=Pa,k.countBy=rd,k.create=_f,k.curry=qs,k.curryRight=ks,k.debounce=Us,k.defaults=yf,k.defaultsDeep=vf,k.defer=Sd,k.delay=Dd,k.difference=Zu,k.differenceBy=Xu,k.differenceWith=Ju,k.drop=Qu,k.dropRight=ec,k.dropRightWhile=nc,k.dropWhile=tc,k.fill=rc,k.filter=ad,k.flatMap=ld,k.flatMapDeep=pd,k.flatMapDepth=ud,k.flatten=Ps,k.flattenDeep=ic,k.flattenDepth=ac,k.flip=Rd,k.flow=xg,k.flowRight=Cg,k.fromPairs=oc,k.functions=Cf,k.functionsIn=Sf,k.groupBy=cd,k.initial=lc,k.intersection=pc,k.intersectionBy=uc,k.intersectionWith=cc,k.invert=Rf,k.invertBy=Nf,k.invokeMap=fd,k.iteratee=xa,k.keyBy=gd,k.keys=Un,k.keysIn=Xn,k.map=fi,k.mapKeys=If,k.mapValues=Of,k.matches=Sg,k.matchesProperty=Dg,k.memoize=hi,k.merge=qf,k.mergeWith=Gs,k.method=Rg,k.methodOf=Ng,k.mixin=Ca,k.negate=mi,k.nthArg=Ig,k.omit=kf,k.omitBy=Uf,k.once=Nd,k.orderBy=hd,k.over=Og,k.overArgs=wd,k.overEvery=qg,k.overSome=kg,k.partial=ya,k.partialRight=Ls,k.partition=md,k.pick=Lf,k.pickBy=Ks,k.property=el,k.propertyOf=Ug,k.pull=hc,k.pullAll=Cs,k.pullAllBy=mc,k.pullAllWith=_c,k.pullAt=yc,k.range=Lg,k.rangeRight=Mg,k.rearg=Id,k.reject=vd,k.remove=vc,k.rest=Od,k.reverse=ha,k.sampleSize=Td,k.set=Bf,k.setWith=Ff,k.shuffle=Ed,k.slice=Ac,k.sortBy=xd,k.sortedUniq=Sc,k.sortedUniqBy=Dc,k.split=pg,k.spread=qd,k.tail=Rc,k.take=Nc,k.takeRight=wc,k.takeRightWhile=Ic,k.takeWhile=Oc,k.tap=Vc,k.throttle=kd,k.thru=di,k.toArray=Hs,k.toPairs=Vs,k.toPairsIn=$s,k.toPath=jg,k.toPlainObject=Ys,k.transform=zf,k.unary=Ud,k.union=qc,k.unionBy=kc,k.unionWith=Uc,k.uniq=Lc,k.uniqBy=Mc,k.uniqWith=Bc,k.unset=Hf,k.unzip=ma,k.unzipWith=Ss,k.update=jf,k.updateWith=Yf,k.values=gr,k.valuesIn=Wf,k.without=Fc,k.words=Js,k.wrap=Ld,k.xor=zc,k.xorBy=Hc,k.xorWith=jc,k.zip=Yc,k.zipObject=Wc,k.zipObjectDeep=Gc,k.zipWith=Kc,k.entries=Vs,k.entriesIn=$s,k.extend=Ws,k.extendWith=vi,Ca(k,k),k.add=Wg,k.attempt=Qs,k.camelCase=$f,k.capitalize=Zs,k.ceil=Gg,k.clamp=Gf,k.clone=Bd,k.cloneDeep=zd,k.cloneDeepWith=Hd,k.cloneWith=Fd,k.conformsTo=jd,k.deburr=Xs,k.defaultTo=Pg,k.divide=Kg,k.endsWith=Zf,k.eq=vt,k.escape=Xf,k.escapeRegExp=Jf,k.every=id,k.find=od,k.findIndex=Es,k.findKey=Af,k.findLast=sd,k.findLastIndex=bs,k.findLastKey=Tf,k.floor=Vg,k.forEach=Rs,k.forEachRight=Ns,k.forIn=Ef,k.forInRight=bf,k.forOwn=Pf,k.forOwnRight=xf,k.get=Ta,k.gt=Yd,k.gte=Wd,k.has=Df,k.hasIn=Ea,k.head=xs,k.identity=Jn,k.includes=dd,k.indexOf=sc,k.inRange=Kf,k.invoke=wf,k.isArguments=Qt,k.isArray=Ve,k.isArrayBuffer=Gd,k.isArrayLike=Zn,k.isArrayLikeObject=Cn,k.isBoolean=Kd,k.isBuffer=Wt,k.isDate=Vd,k.isElement=$d,k.isEmpty=Zd,k.isEqual=Xd,k.isEqualWith=Jd,k.isError=va,k.isFinite=Qd,k.isFunction=It,k.isInteger=Ms,k.isLength=_i,k.isMap=Bs,k.isMatch=ef,k.isMatchWith=nf,k.isNaN=tf,k.isNative=rf,k.isNil=of,k.isNull=af,k.isNumber=Fs,k.isObject=Pn,k.isObjectLike=xn,k.isPlainObject=wr,k.isRegExp=Aa,k.isSafeInteger=sf,k.isSet=zs,k.isString=yi,k.isSymbol=at,k.isTypedArray=fr,k.isUndefined=lf,k.isWeakMap=pf,k.isWeakSet=uf,k.join=dc,k.kebabCase=Qf,k.last=gt,k.lastIndexOf=fc,k.lowerCase=eg,k.lowerFirst=ng,k.lt=cf,k.lte=df,k.max=$g,k.maxBy=Zg,k.mean=Xg,k.meanBy=Jg,k.min=Qg,k.minBy=eh,k.stubArray=Da,k.stubFalse=Ra,k.stubObject=Bg,k.stubString=Fg,k.stubTrue=zg,k.multiply=nh,k.nth=gc,k.noConflict=wg,k.noop=Sa,k.now=gi,k.pad=tg,k.padEnd=rg,k.padStart=ig,k.parseInt=ag,k.random=Vf,k.reduce=_d,k.reduceRight=yd,k.repeat=og,k.replace=sg,k.result=Mf,k.round=th,k.runInContext=Z,k.sample=Ad,k.size=bd,k.snakeCase=lg,k.some=Pd,k.sortedIndex=Tc,k.sortedIndexBy=Ec,k.sortedIndexOf=bc,k.sortedLastIndex=Pc,k.sortedLastIndexBy=xc,k.sortedLastIndexOf=Cc,k.startCase=ug,k.startsWith=cg,k.subtract=rh,k.sum=ih,k.sumBy=ah,k.template=dg,k.times=Hg,k.toFinite=Ot,k.toInteger=Je,k.toLength=js,k.toLower=fg,k.toNumber=ht,k.toSafeInteger=ff,k.toString=cn,k.toUpper=gg,k.trim=hg,k.trimEnd=mg,k.trimStart=_g,k.truncate=yg,k.unescape=vg,k.uniqueId=Yg,k.upperCase=Ag,k.upperFirst=ba,k.each=Rs,k.eachRight=Ns,k.first=xs,Ca(k,function(){var e={};return Et(k,function(n,s){fn.call(k.prototype,s)||(e[s]=n)}),e}(),{chain:!1}),k.VERSION=t,pt(["bind","bindKey","curry","curryRight","partial","partialRight"],function(e){k[e].placeholder=k}),pt(["drop","take"],function(e,n){an.prototype[e]=function(s){s=s===r?1:In(Je(s),0);var T=this.__filtered__&&!n?new an(this):this.clone();return T.__filtered__?T.__takeCount__=zn(s,T.__takeCount__):T.__views__.push({size:zn(s,ge),type:e+(T.__dir__<0?"Right":"")}),T},an.prototype[e+"Right"]=function(s){return this.reverse()[e](s).reverse()}}),pt(["filter","map","takeWhile"],function(e,n){var s=n+1,T=s==F||s==z;an.prototype[e]=function(R){var M=this.clone();return M.__iteratees__.push({iteratee:Be(R,3),type:s}),M.__filtered__=M.__filtered__||T,M}}),pt(["head","last"],function(e,n){var s="take"+(n?"Right":"");an.prototype[e]=function(){return this[s](1).value()[0]}}),pt(["initial","tail"],function(e,n){var s="drop"+(n?"":"Right");an.prototype[e]=function(){return this.__filtered__?new an(this):this[s](1)}}),an.prototype.compact=function(){return this.filter(Jn)},an.prototype.find=function(e){return this.filter(e).head()},an.prototype.findLast=function(e){return this.reverse().find(e)},an.prototype.invokeMap=en(function(e,n){return typeof e=="function"?new an(this):this.map(function(s){return xr(s,e,n)})}),an.prototype.reject=function(e){return this.filter(mi(Be(e)))},an.prototype.slice=function(e,n){e=Je(e);var s=this;return s.__filtered__&&(e>0||n<0)?new an(s):(e<0?s=s.takeRight(-e):e&&(s=s.drop(e)),n!==r&&(n=Je(n),s=n<0?s.dropRight(-n):s.take(n-e)),s)},an.prototype.takeRightWhile=function(e){return this.reverse().takeWhile(e).reverse()},an.prototype.toArray=function(){return this.take(ge)},Et(an.prototype,function(e,n){var s=/^(?:filter|find|map|reject)|While$/.test(n),T=/^(?:head|last)$/.test(n),R=k[T?"take"+(n=="last"?"Right":""):n],M=T||/^find/.test(n);!R||(k.prototype[n]=function(){var j=this.__wrapped__,V=T?[1]:arguments,X=j instanceof an,se=V[0],le=X||Ve(j),he=function(rn){var on=R.apply(k,Mt([rn],V));return T&&xe?on[0]:on};le&&s&&typeof se=="function"&&se.length!=1&&(X=le=!1);var xe=this.__chain__,we=!!this.__actions__.length,ze=M&&!xe,Qe=X&&!we;if(!M&&le){j=Qe?j:new an(this);var He=e.apply(j,V);return He.__actions__.push({func:di,args:[he],thisArg:r}),new ct(He,xe)}return ze&&Qe?e.apply(this,V):(He=this.thru(he),ze?T?He.value()[0]:He.value():He)})}),pt(["pop","push","shift","sort","splice","unshift"],function(e){var n=Mr[e],s=/^(?:push|sort|unshift)$/.test(e)?"tap":"thru",T=/^(?:pop|shift)$/.test(e);k.prototype[e]=function(){var R=arguments;if(T&&!this.__chain__){var M=this.value();return n.apply(Ve(M)?M:[],R)}return this[s](function(j){return n.apply(Ve(j)?j:[],R)})}}),Et(an.prototype,function(e,n){var s=k[n];if(s){var T=s.name+"";fn.call(lr,T)||(lr[T]=[]),lr[T].push({name:n,func:s})}}),lr[ai(r,C).name]=[{name:"wrapper",func:r}],an.prototype.clone=yp,an.prototype.reverse=vp,an.prototype.value=Ap,k.prototype.at=$c,k.prototype.chain=Zc,k.prototype.commit=Xc,k.prototype.next=Jc,k.prototype.plant=ed,k.prototype.reverse=nd,k.prototype.toJSON=k.prototype.valueOf=k.prototype.value=td,k.prototype.first=k.prototype.head,yr&&(k.prototype[yr]=Qc),k},Lr=Xl();Mn._=Lr,g=function(){return Lr}.call(y,o,y,P),g!==r&&(P.exports=g)}).call(this)},5977:(P,y,o)=>{"use strict";const g=o(9939),r=Symbol("max"),t=Symbol("length"),l=Symbol("lengthCalculator"),c=Symbol("allowStale"),p=Symbol("maxAge"),a=Symbol("dispose"),u=Symbol("noDisposeOnSet"),h=Symbol("lruList"),i=Symbol("cache"),m=Symbol("updateAgeOnGet"),d=()=>1;class f{constructor(x){if(typeof x=="number"&&(x={max:x}),x||(x={}),x.max&&(typeof x.max!="number"||x.max<0))throw new TypeError("max must be a non-negative number");const D=this[r]=x.max||1/0,N=x.length||d;if(this[l]=typeof N!="function"?d:N,this[c]=x.stale||!1,x.maxAge&&typeof x.maxAge!="number")throw new TypeError("maxAge must be a number");this[p]=x.maxAge||0,this[a]=x.dispose,this[u]=x.noDisposeOnSet||!1,this[m]=x.updateAgeOnGet||!1,this.reset()}set max(x){if(typeof x!="number"||x<0)throw new TypeError("max must be a non-negative number");this[r]=x||1/0,A(this)}get max(){return this[r]}set allowStale(x){this[c]=!!x}get allowStale(){return this[c]}set maxAge(x){if(typeof x!="number")throw new TypeError("maxAge must be a non-negative number");this[p]=x,A(this)}get maxAge(){return this[p]}set lengthCalculator(x){typeof x!="function"&&(x=d),x!==this[l]&&(this[l]=x,this[t]=0,this[h].forEach(D=>{D.length=this[l](D.value,D.key),this[t]+=D.length})),A(this)}get lengthCalculator(){return this[l]}get length(){return this[t]}get itemCount(){return this[h].length}rforEach(x,D){D=D||this;for(let N=this[h].tail;N!==null;){const I=N.prev;S(this,x,N,D),N=I}}forEach(x,D){D=D||this;for(let N=this[h].head;N!==null;){const I=N.next;S(this,x,N,D),N=I}}keys(){return this[h].toArray().map(x=>x.key)}values(){return this[h].toArray().map(x=>x.value)}reset(){this[a]&&this[h]&&this[h].length&&this[h].forEach(x=>this[a](x.key,x.value)),this[i]=new Map,this[h]=new g,this[t]=0}dump(){return this[h].map(x=>_(this,x)?!1:{k:x.key,v:x.value,e:x.now+(x.maxAge||0)}).toArray().filter(x=>x)}dumpLru(){return this[h]}set(x,D,N){if(N=N||this[p],N&&typeof N!="number")throw new TypeError("maxAge must be a number");const I=N?Date.now():0,w=this[l](D,x);if(this[i].has(x)){if(w>this[r])return C(this,this[i].get(x)),!1;const U=this[i].get(x).value;return this[a]&&(this[u]||this[a](x,U.value)),U.now=I,U.maxAge=N,U.value=D,this[t]+=w-U.length,U.length=w,this.get(x),A(this),!0}const O=new E(x,D,w,I,N);return O.length>this[r]?(this[a]&&this[a](x,D),!1):(this[t]+=O.length,this[h].unshift(O),this[i].set(x,this[h].head),A(this),!0)}has(x){if(!this[i].has(x))return!1;const D=this[i].get(x).value;return!_(this,D)}get(x){return v(this,x,!0)}peek(x){return v(this,x,!1)}pop(){const x=this[h].tail;return x?(C(this,x),x.value):null}del(x){C(this,this[i].get(x))}load(x){this.reset();const D=Date.now();for(let N=x.length-1;N>=0;N--){const I=x[N],w=I.e||0;if(w===0)this.set(I.k,I.v);else{const O=w-D;O>0&&this.set(I.k,I.v,O)}}}prune(){this[i].forEach((x,D)=>v(this,D,!1))}}const v=(b,x,D)=>{const N=b[i].get(x);if(N){const I=N.value;if(_(b,I)){if(C(b,N),!b[c])return}else D&&(b[m]&&(N.value.now=Date.now()),b[h].unshiftNode(N));return I.value}},_=(b,x)=>{if(!x||!x.maxAge&&!b[p])return!1;const D=Date.now()-x.now;return x.maxAge?D>x.maxAge:b[p]&&D>b[p]},A=b=>{if(b[t]>b[r])for(let x=b[h].tail;b[t]>b[r]&&x!==null;){const D=x.prev;C(b,x),x=D}},C=(b,x)=>{if(x){const D=x.value;b[a]&&b[a](D.key,D.value),b[t]-=D.length,b[i].delete(D.key),b[h].removeNode(x)}};class E{constructor(x,D,N,I,w){this.key=x,this.value=D,this.length=N,this.now=I,this.maxAge=w||0}}const S=(b,x,D,N)=>{let I=D.value;_(b,I)&&(C(b,D),b[c]||(I=void 0)),I&&x.call(N,I.value,I.key,b)};P.exports=f},6731:()=>{(function(P){var y="\\b(?:BASH|BASHOPTS|BASH_ALIASES|BASH_ARGC|BASH_ARGV|BASH_CMDS|BASH_COMPLETION_COMPAT_DIR|BASH_LINENO|BASH_REMATCH|BASH_SOURCE|BASH_VERSINFO|BASH_VERSION|COLORTERM|COLUMNS|COMP_WORDBREAKS|DBUS_SESSION_BUS_ADDRESS|DEFAULTS_PATH|DESKTOP_SESSION|DIRSTACK|DISPLAY|EUID|GDMSESSION|GDM_LANG|GNOME_KEYRING_CONTROL|GNOME_KEYRING_PID|GPG_AGENT_INFO|GROUPS|HISTCONTROL|HISTFILE|HISTFILESIZE|HISTSIZE|HOME|HOSTNAME|HOSTTYPE|IFS|INSTANCE|JOB|LANG|LANGUAGE|LC_ADDRESS|LC_ALL|LC_IDENTIFICATION|LC_MEASUREMENT|LC_MONETARY|LC_NAME|LC_NUMERIC|LC_PAPER|LC_TELEPHONE|LC_TIME|LESSCLOSE|LESSOPEN|LINES|LOGNAME|LS_COLORS|MACHTYPE|MAILCHECK|MANDATORY_PATH|NO_AT_BRIDGE|OLDPWD|OPTERR|OPTIND|ORBIT_SOCKETDIR|OSTYPE|PAPERSIZE|PATH|PIPESTATUS|PPID|PS1|PS2|PS3|PS4|PWD|RANDOM|REPLY|SECONDS|SELINUX_INIT|SESSION|SESSIONTYPE|SESSION_MANAGER|SHELL|SHELLOPTS|SHLVL|SSH_AUTH_SOCK|TERM|UID|UPSTART_EVENTS|UPSTART_INSTANCE|UPSTART_JOB|UPSTART_SESSION|USER|WINDOWID|XAUTHORITY|XDG_CONFIG_DIRS|XDG_CURRENT_DESKTOP|XDG_DATA_DIRS|XDG_GREETER_DATA_DIR|XDG_MENU_PREFIX|XDG_RUNTIME_DIR|XDG_SEAT|XDG_SEAT_PATH|XDG_SESSION_DESKTOP|XDG_SESSION_ID|XDG_SESSION_PATH|XDG_SESSION_TYPE|XDG_VTNR|XMODIFIERS)\\b",o={pattern:/(^(["']?)\w+\2)[ \t]+\S.*/,lookbehind:!0,alias:"punctuation",inside:null},g={bash:o,environment:{pattern:RegExp("\\$"+y),alias:"constant"},variable:[{pattern:/\$?\(\([\s\S]+?\)\)/,greedy:!0,inside:{variable:[{pattern:/(^\$\(\([\s\S]+)\)\)/,lookbehind:!0},/^\$\(\(/],number:/\b0x[\dA-Fa-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:[Ee]-?\d+)?/,operator:/--|\+\+|\*\*=?|<<=?|>>=?|&&|\|\||[=!+\-*/%<>^&|]=?|[?~:]/,punctuation:/\(\(?|\)\)?|,|;/}},{pattern:/\$\((?:\([^)]+\)|[^()])+\)|`[^`]+`/,greedy:!0,inside:{variable:/^\$\(|^`|\)$|`$/}},{pattern:/\$\{[^}]+\}/,greedy:!0,inside:{operator:/:[-=?+]?|[!\/]|##?|%%?|\^\^?|,,?/,punctuation:/[\[\]]/,environment:{pattern:RegExp("(\\{)"+y),lookbehind:!0,alias:"constant"}}},/\$(?:\w+|[#?*!@$])/],entity:/\\(?:[abceEfnrtv\\"]|O?[0-7]{1,3}|x[0-9a-fA-F]{1,2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8})/};P.languages.bash={shebang:{pattern:/^#!\s*\/.*/,alias:"important"},comment:{pattern:/(^|[^"{\\$])#.*/,lookbehind:!0},"function-name":[{pattern:/(\bfunction\s+)[\w-]+(?=(?:\s*\(?:\s*\))?\s*\{)/,lookbehind:!0,alias:"function"},{pattern:/\b[\w-]+(?=\s*\(\s*\)\s*\{)/,alias:"function"}],"for-or-select":{pattern:/(\b(?:for|select)\s+)\w+(?=\s+in\s)/,alias:"variable",lookbehind:!0},"assign-left":{pattern:/(^|[\s;|&]|[<>]\()\w+(?=\+?=)/,inside:{environment:{pattern:RegExp("(^|[\\s;|&]|[<>]\\()"+y),lookbehind:!0,alias:"constant"}},alias:"variable",lookbehind:!0},string:[{pattern:/((?:^|[^<])<<-?\s*)(\w+)\s[\s\S]*?(?:\r?\n|\r)\2/,lookbehind:!0,greedy:!0,inside:g},{pattern:/((?:^|[^<])<<-?\s*)(["'])(\w+)\2\s[\s\S]*?(?:\r?\n|\r)\3/,lookbehind:!0,greedy:!0,inside:{bash:o}},{pattern:/(^|[^\\](?:\\\\)*)"(?:\\[\s\S]|\$\([^)]+\)|\$(?!\()|`[^`]+`|[^"\\`$])*"/,lookbehind:!0,greedy:!0,inside:g},{pattern:/(^|[^$\\])'[^']*'/,lookbehind:!0,greedy:!0},{pattern:/\$'(?:[^'\\]|\\[\s\S])*'/,greedy:!0,inside:{entity:g.entity}}],environment:{pattern:RegExp("\\$?"+y),alias:"constant"},variable:g.variable,function:{pattern:/(^|[\s;|&]|[<>]\()(?:add|apropos|apt|aptitude|apt-cache|apt-get|aspell|automysqlbackup|awk|basename|bash|bc|bconsole|bg|bzip2|cal|cat|cfdisk|chgrp|chkconfig|chmod|chown|chroot|cksum|clear|cmp|column|comm|composer|cp|cron|crontab|csplit|curl|cut|date|dc|dd|ddrescue|debootstrap|df|diff|diff3|dig|dir|dircolors|dirname|dirs|dmesg|du|egrep|eject|env|ethtool|expand|expect|expr|fdformat|fdisk|fg|fgrep|file|find|fmt|fold|format|free|fsck|ftp|fuser|gawk|git|gparted|grep|groupadd|groupdel|groupmod|groups|grub-mkconfig|gzip|halt|head|hg|history|host|hostname|htop|iconv|id|ifconfig|ifdown|ifup|import|install|ip|jobs|join|kill|killall|less|link|ln|locate|logname|logrotate|look|lpc|lpr|lprint|lprintd|lprintq|lprm|ls|lsof|lynx|make|man|mc|mdadm|mkconfig|mkdir|mke2fs|mkfifo|mkfs|mkisofs|mknod|mkswap|mmv|more|most|mount|mtools|mtr|mutt|mv|nano|nc|netstat|nice|nl|nohup|notify-send|npm|nslookup|op|open|parted|passwd|paste|pathchk|ping|pkill|pnpm|popd|pr|printcap|printenv|ps|pushd|pv|quota|quotacheck|quotactl|ram|rar|rcp|reboot|remsync|rename|renice|rev|rm|rmdir|rpm|rsync|scp|screen|sdiff|sed|sendmail|seq|service|sftp|sh|shellcheck|shuf|shutdown|sleep|slocate|sort|split|ssh|stat|strace|su|sudo|sum|suspend|swapon|sync|tac|tail|tar|tee|time|timeout|top|touch|tr|traceroute|tsort|tty|umount|uname|unexpand|uniq|units|unrar|unshar|unzip|update-grub|uptime|useradd|userdel|usermod|users|uudecode|uuencode|v|vdir|vi|vim|virsh|vmstat|wait|watch|wc|wget|whereis|which|who|whoami|write|xargs|xdg-open|yarn|yes|zenity|zip|zsh|zypper)(?=$|[)\s;|&])/,lookbehind:!0},keyword:{pattern:/(^|[\s;|&]|[<>]\()(?:if|then|else|elif|fi|for|while|in|case|esac|function|select|do|done|until)(?=$|[)\s;|&])/,lookbehind:!0},builtin:{pattern:/(^|[\s;|&]|[<>]\()(?:\.|:|break|cd|continue|eval|exec|exit|export|getopts|hash|pwd|readonly|return|shift|test|times|trap|umask|unset|alias|bind|builtin|caller|command|declare|echo|enable|help|let|local|logout|mapfile|printf|read|readarray|source|type|typeset|ulimit|unalias|set|shopt)(?=$|[)\s;|&])/,lookbehind:!0,alias:"class-name"},boolean:{pattern:/(^|[\s;|&]|[<>]\()(?:true|false)(?=$|[)\s;|&])/,lookbehind:!0},"file-descriptor":{pattern:/\B&\d\b/,alias:"important"},operator:{pattern:/\d?<>|>\||\+=|=[=~]?|!=?|<<[<-]?|[&\d]?>>|\d[<>]&?|[<>][&=]?|&[>&]?|\|[&|]?/,inside:{"file-descriptor":{pattern:/^\d/,alias:"important"}}},punctuation:/\$?\(\(?|\)\)?|\.\.|[{}[\];\\]/,number:{pattern:/(^|\s)(?:[1-9]\d*|0)(?:[.,]\d+)?\b/,lookbehind:!0}},o.inside=P.languages.bash;for(var r=["comment","function-name","for-or-select","assign-left","string","environment","function","keyword","builtin","boolean","file-descriptor","operator","punctuation","number"],t=g.variable[1].inside,l=0;l<r.length;l++)t[r[l]]=P.languages.bash[r[l]];P.languages.shell=P.languages.bash})(Prism)},374:()=>{(function(P){P.languages.http={"request-line":{pattern:/^(?:GET|HEAD|POST|PUT|DELETE|CONNECT|OPTIONS|TRACE|PATCH|PRI|SEARCH)\s(?:https?:\/\/|\/)\S*\sHTTP\/[0-9.]+/m,inside:{method:{pattern:/^[A-Z]+\b/,alias:"property"},"request-target":{pattern:/^(\s)(?:https?:\/\/|\/)\S*(?=\s)/,lookbehind:!0,alias:"url",inside:P.languages.uri},"http-version":{pattern:/^(\s)HTTP\/[0-9.]+/,lookbehind:!0,alias:"property"}}},"response-status":{pattern:/^HTTP\/[0-9.]+ \d+ .+/m,inside:{"http-version":{pattern:/^HTTP\/[0-9.]+/,alias:"property"},"status-code":{pattern:/^(\s)\d+(?=\s)/,lookbehind:!0,alias:"number"},"reason-phrase":{pattern:/^(\s).+/,lookbehind:!0,alias:"string"}}},"header-name":{pattern:/^[\w-]+:(?=.)/m,alias:"keyword"}};var y=P.languages,o={"application/javascript":y.javascript,"application/json":y.json||y.javascript,"application/xml":y.xml,"text/xml":y.xml,"text/html":y.html,"text/css":y.css},g={"application/json":!0,"application/xml":!0};function r(p){var a=p.replace(/^[a-z]+\//,""),u="\\w+/(?:[\\w.-]+\\+)+"+a+"(?![+\\w.-])";return"(?:"+p+"|"+u+")"}var t;for(var l in o)if(o[l]){t=t||{};var c=g[l]?r(l):l;t[l.replace(/\//g,"-")]={pattern:RegExp("(content-type:\\s*"+c+"(?:(?:\\r\\n?|\\n).+)*)(?:\\r?\\n|\\r){2}[\\s\\S]*","i"),lookbehind:!0,inside:o[l]}}t&&P.languages.insertBefore("http","header-name",t)})(Prism)},6780:()=>{Prism.languages.json={property:{pattern:/(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?=\s*:)/,lookbehind:!0,greedy:!0},string:{pattern:/(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?!\s*:)/,lookbehind:!0,greedy:!0},comment:{pattern:/\/\/.*|\/\*[\s\S]*?(?:\*\/|$)/,greedy:!0},number:/-?\b\d+(?:\.\d+)?(?:e[+-]?\d+)?\b/i,punctuation:/[{}[\],]/,operator:/:/,boolean:/\b(?:true|false)\b/,null:{pattern:/\bnull\b/,alias:"keyword"}},Prism.languages.webmanifest=Prism.languages.json},9900:()=>{Prism.languages.python={comment:{pattern:/(^|[^\\])#.*/,lookbehind:!0},"string-interpolation":{pattern:/(?:f|rf|fr)(?:("""|''')[\s\S]*?\1|("|')(?:\\.|(?!\2)[^\\\r\n])*\2)/i,greedy:!0,inside:{interpolation:{pattern:/((?:^|[^{])(?:\{\{)*)\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}])+\})+\})+\}/,lookbehind:!0,inside:{"format-spec":{pattern:/(:)[^:(){}]+(?=\}$)/,lookbehind:!0},"conversion-option":{pattern:/![sra](?=[:}]$)/,alias:"punctuation"},rest:null}},string:/[\s\S]+/}},"triple-quoted-string":{pattern:/(?:[rub]|rb|br)?("""|''')[\s\S]*?\1/i,greedy:!0,alias:"string"},string:{pattern:/(?:[rub]|rb|br)?("|')(?:\\.|(?!\1)[^\\\r\n])*\1/i,greedy:!0},function:{pattern:/((?:^|\s)def[ \t]+)[a-zA-Z_]\w*(?=\s*\()/g,lookbehind:!0},"class-name":{pattern:/(\bclass\s+)\w+/i,lookbehind:!0},decorator:{pattern:/(^[\t ]*)@\w+(?:\.\w+)*/im,lookbehind:!0,alias:["annotation","punctuation"],inside:{punctuation:/\./}},keyword:/\b(?:and|as|assert|async|await|break|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|nonlocal|not|or|pass|print|raise|return|try|while|with|yield)\b/,builtin:/\b(?:__import__|abs|all|any|apply|ascii|basestring|bin|bool|buffer|bytearray|bytes|callable|chr|classmethod|cmp|coerce|compile|complex|delattr|dict|dir|divmod|enumerate|eval|execfile|file|filter|float|format|frozenset|getattr|globals|hasattr|hash|help|hex|id|input|int|intern|isinstance|issubclass|iter|len|list|locals|long|map|max|memoryview|min|next|object|oct|open|ord|pow|property|range|raw_input|reduce|reload|repr|reversed|round|set|setattr|slice|sorted|staticmethod|str|sum|super|tuple|type|unichr|unicode|vars|xrange|zip)\b/,boolean:/\b(?:True|False|None)\b/,number:/\b0(?:b(?:_?[01])+|o(?:_?[0-7])+|x(?:_?[a-f0-9])+)\b|(?:\b\d+(?:_\d+)*(?:\.(?:\d+(?:_\d+)*)?)?|\B\.\d+(?:_\d+)*)(?:e[+-]?\d+(?:_\d+)*)?j?\b/i,operator:/[-+%=]=?|!=|\*\*?=?|\/\/?=?|<[<=>]?|>[=>]?|[&|^~]/,punctuation:/[{}[\];(),.:]/},Prism.languages.python["string-interpolation"].inside.interpolation.inside.rest=Prism.languages.python,Prism.languages.py=Prism.languages.python},5866:(P,y,o)=>{var g=typeof window!="undefined"?window:typeof WorkerGlobalScope!="undefined"&&self instanceof WorkerGlobalScope?self:{};/**
 * Prism: Lightweight, robust, elegant syntax highlighting
 *
 * @license MIT <https://opensource.org/licenses/MIT>
 * @author Lea Verou <https://lea.verou.me>
 * @namespace
 * @public
 */var r=function(t){var l=/\blang(?:uage)?-([\w-]+)\b/i,c=0,p={},a={manual:t.Prism&&t.Prism.manual,disableWorkerMessageHandler:t.Prism&&t.Prism.disableWorkerMessageHandler,util:{encode:function E(S){return S instanceof u?new u(S.type,E(S.content),S.alias):Array.isArray(S)?S.map(E):S.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/\u00a0/g," ")},type:function(E){return Object.prototype.toString.call(E).slice(8,-1)},objId:function(E){return E.__id||Object.defineProperty(E,"__id",{value:++c}),E.__id},clone:function E(S,b){b=b||{};var x,D;switch(a.util.type(S)){case"Object":if(D=a.util.objId(S),b[D])return b[D];x={},b[D]=x;for(var N in S)S.hasOwnProperty(N)&&(x[N]=E(S[N],b));return x;case"Array":return D=a.util.objId(S),b[D]?b[D]:(x=[],b[D]=x,S.forEach(function(I,w){x[w]=E(I,b)}),x);default:return S}},getLanguage:function(E){for(;E&&!l.test(E.className);)E=E.parentElement;return E?(E.className.match(l)||[,"none"])[1].toLowerCase():"none"},currentScript:function(){if(typeof document=="undefined")return null;if("currentScript"in document&&1<2)return document.currentScript;try{throw new Error}catch(x){var E=(/at [^(\r\n]*\((.*):[^:]+:[^:]+\)$/i.exec(x.stack)||[])[1];if(E){var S=document.getElementsByTagName("script");for(var b in S)if(S[b].src==E)return S[b]}return null}},isActive:function(E,S,b){for(var x="no-"+S;E;){var D=E.classList;if(D.contains(S))return!0;if(D.contains(x))return!1;E=E.parentElement}return!!b}},languages:{plain:p,plaintext:p,text:p,txt:p,extend:function(E,S){var b=a.util.clone(a.languages[E]);for(var x in S)b[x]=S[x];return b},insertBefore:function(E,S,b,x){x=x||a.languages;var D=x[E],N={};for(var I in D)if(D.hasOwnProperty(I)){if(I==S)for(var w in b)b.hasOwnProperty(w)&&(N[w]=b[w]);b.hasOwnProperty(I)||(N[I]=D[I])}var O=x[E];return x[E]=N,a.languages.DFS(a.languages,function(B,U){U===O&&B!=E&&(this[B]=N)}),N},DFS:function E(S,b,x,D){D=D||{};var N=a.util.objId;for(var I in S)if(S.hasOwnProperty(I)){b.call(S,I,S[I],x||I);var w=S[I],O=a.util.type(w);O==="Object"&&!D[N(w)]?(D[N(w)]=!0,E(w,b,null,D)):O==="Array"&&!D[N(w)]&&(D[N(w)]=!0,E(w,b,I,D))}}},plugins:{},highlightAll:function(E,S){a.highlightAllUnder(document,E,S)},highlightAllUnder:function(E,S,b){var x={callback:b,container:E,selector:'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'};a.hooks.run("before-highlightall",x),x.elements=Array.prototype.slice.apply(x.container.querySelectorAll(x.selector)),a.hooks.run("before-all-elements-highlight",x);for(var D=0,N;N=x.elements[D++];)a.highlightElement(N,S===!0,x.callback)},highlightElement:function(E,S,b){var x=a.util.getLanguage(E),D=a.languages[x];E.className=E.className.replace(l,"").replace(/\s+/g," ")+" language-"+x;var N=E.parentElement;N&&N.nodeName.toLowerCase()==="pre"&&(N.className=N.className.replace(l,"").replace(/\s+/g," ")+" language-"+x);var I=E.textContent,w={element:E,language:x,grammar:D,code:I};function O(U){w.highlightedCode=U,a.hooks.run("before-insert",w),w.element.innerHTML=w.highlightedCode,a.hooks.run("after-highlight",w),a.hooks.run("complete",w),b&&b.call(w.element)}if(a.hooks.run("before-sanity-check",w),N=w.element.parentElement,N&&N.nodeName.toLowerCase()==="pre"&&!N.hasAttribute("tabindex")&&N.setAttribute("tabindex","0"),!w.code){a.hooks.run("complete",w),b&&b.call(w.element);return}if(a.hooks.run("before-highlight",w),!w.grammar){O(a.util.encode(w.code));return}if(S&&t.Worker){var B=new Worker(a.filename);B.onmessage=function(U){O(U.data)},B.postMessage(JSON.stringify({language:w.language,code:w.code,immediateClose:!0}))}else O(a.highlight(w.code,w.grammar,w.language))},highlight:function(E,S,b){var x={code:E,grammar:S,language:b};return a.hooks.run("before-tokenize",x),x.tokens=a.tokenize(x.code,x.grammar),a.hooks.run("after-tokenize",x),u.stringify(a.util.encode(x.tokens),x.language)},tokenize:function(E,S){var b=S.rest;if(b){for(var x in b)S[x]=b[x];delete S.rest}var D=new m;return d(D,D.head,E),i(E,D,S,D.head,0),v(D)},hooks:{all:{},add:function(E,S){var b=a.hooks.all;b[E]=b[E]||[],b[E].push(S)},run:function(E,S){var b=a.hooks.all[E];if(!(!b||!b.length))for(var x=0,D;D=b[x++];)D(S)}},Token:u};t.Prism=a;function u(E,S,b,x){this.type=E,this.content=S,this.alias=b,this.length=(x||"").length|0}u.stringify=function E(S,b){if(typeof S=="string")return S;if(Array.isArray(S)){var x="";return S.forEach(function(O){x+=E(O,b)}),x}var D={type:S.type,content:E(S.content,b),tag:"span",classes:["token",S.type],attributes:{},language:b},N=S.alias;N&&(Array.isArray(N)?Array.prototype.push.apply(D.classes,N):D.classes.push(N)),a.hooks.run("wrap",D);var I="";for(var w in D.attributes)I+=" "+w+'="'+(D.attributes[w]||"").replace(/"/g,"&quot;")+'"';return"<"+D.tag+' class="'+D.classes.join(" ")+'"'+I+">"+D.content+"</"+D.tag+">"};function h(E,S,b,x){E.lastIndex=S;var D=E.exec(b);if(D&&x&&D[1]){var N=D[1].length;D.index+=N,D[0]=D[0].slice(N)}return D}function i(E,S,b,x,D,N){for(var I in b)if(!(!b.hasOwnProperty(I)||!b[I])){var w=b[I];w=Array.isArray(w)?w:[w];for(var O=0;O<w.length;++O){if(N&&N.cause==I+","+O)return;var B=w[O],U=B.inside,L=!!B.lookbehind,F=!!B.greedy,Y=B.alias;if(F&&!B.pattern.global){var z=B.pattern.toString().match(/[imsuy]*$/)[0];B.pattern=RegExp(B.pattern.source,z+"g")}for(var G=B.pattern||B,W=x.next,te=D;W!==S.tail&&!(N&&te>=N.reach);te+=W.value.length,W=W.next){var oe=W.value;if(S.length>E.length)return;if(!(oe instanceof u)){var ge=1,Q;if(F){if(Q=h(G,te,E,L),!Q)break;var mn=Q.index,Te=Q.index+Q[0].length,Pe=te;for(Pe+=W.value.length;mn>=Pe;)W=W.next,Pe+=W.value.length;if(Pe-=W.value.length,te=Pe,W.value instanceof u)continue;for(var Ge=W;Ge!==S.tail&&(Pe<Te||typeof Ge.value=="string");Ge=Ge.next)ge++,Pe+=Ge.value.length;ge--,oe=E.slice(te,Pe),Q.index-=te}else if(Q=h(G,0,oe,L),!Q)continue;var mn=Q.index,Rn=Q[0],Dn=oe.slice(0,mn),Nn=oe.slice(mn+Rn.length),jn=te+oe.length;N&&jn>N.reach&&(N.reach=jn);var je=W.prev;Dn&&(je=d(S,je,Dn),te+=Dn.length),f(S,je,ge);var Ln=new u(I,U?a.tokenize(Rn,U):Rn,Y,Rn);if(W=d(S,je,Ln),Nn&&d(S,W,Nn),ge>1){var Ze={cause:I+","+O,reach:jn};i(E,S,b,W.prev,te,Ze),N&&Ze.reach>N.reach&&(N.reach=Ze.reach)}}}}}}function m(){var E={value:null,prev:null,next:null},S={value:null,prev:E,next:null};E.next=S,this.head=E,this.tail=S,this.length=0}function d(E,S,b){var x=S.next,D={value:b,prev:S,next:x};return S.next=D,x.prev=D,E.length++,D}function f(E,S,b){for(var x=S.next,D=0;D<b&&x!==E.tail;D++)x=x.next;S.next=x,x.prev=S,E.length-=D}function v(E){for(var S=[],b=E.head.next;b!==E.tail;)S.push(b.value),b=b.next;return S}if(!t.document)return t.addEventListener&&(a.disableWorkerMessageHandler||t.addEventListener("message",function(E){var S=JSON.parse(E.data),b=S.language,x=S.code,D=S.immediateClose;t.postMessage(a.highlight(x,a.languages[b],b)),D&&t.close()},!1)),a;var _=a.util.currentScript();_&&(a.filename=_.src,_.hasAttribute("data-manual")&&(a.manual=!0));function A(){a.manual||a.highlightAll()}if(!a.manual){var C=document.readyState;C==="loading"||C==="interactive"&&_&&_.defer?document.addEventListener("DOMContentLoaded",A):window.requestAnimationFrame?window.requestAnimationFrame(A):window.setTimeout(A,16)}return a}(g);P.exports&&(P.exports=r),typeof o.g!="undefined"&&(o.g.Prism=r),r.languages.markup={comment:{pattern:/<!--(?:(?!<!--)[\s\S])*?-->/,greedy:!0},prolog:{pattern:/<\?[\s\S]+?\?>/,greedy:!0},doctype:{pattern:/<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,greedy:!0,inside:{"internal-subset":{pattern:/(^[^\[]*\[)[\s\S]+(?=\]>$)/,lookbehind:!0,greedy:!0,inside:null},string:{pattern:/"[^"]*"|'[^']*'/,greedy:!0},punctuation:/^<!|>$|[[\]]/,"doctype-tag":/^DOCTYPE/i,name:/[^\s<>'"]+/}},cdata:{pattern:/<!\[CDATA\[[\s\S]*?\]\]>/i,greedy:!0},tag:{pattern:/<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,greedy:!0,inside:{tag:{pattern:/^<\/?[^\s>\/]+/,inside:{punctuation:/^<\/?/,namespace:/^[^\s>\/:]+:/}},"special-attr":[],"attr-value":{pattern:/=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,inside:{punctuation:[{pattern:/^=/,alias:"attr-equals"},/"|'/]}},punctuation:/\/?>/,"attr-name":{pattern:/[^\s>\/]+/,inside:{namespace:/^[^\s>\/:]+:/}}}},entity:[{pattern:/&[\da-z]{1,8};/i,alias:"named-entity"},/&#x?[\da-f]{1,8};/i]},r.languages.markup.tag.inside["attr-value"].inside.entity=r.languages.markup.entity,r.languages.markup.doctype.inside["internal-subset"].inside=r.languages.markup,r.hooks.add("wrap",function(t){t.type==="entity"&&(t.attributes.title=t.content.replace(/&amp;/,"&"))}),Object.defineProperty(r.languages.markup.tag,"addInlined",{value:function(l,c){var p={};p["language-"+c]={pattern:/(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,lookbehind:!0,inside:r.languages[c]},p.cdata=/^<!\[CDATA\[|\]\]>$/i;var a={"included-cdata":{pattern:/<!\[CDATA\[[\s\S]*?\]\]>/i,inside:p}};a["language-"+c]={pattern:/[\s\S]+/,inside:r.languages[c]};var u={};u[l]={pattern:RegExp(/(<__[^>]*>)(?:<!\[CDATA\[(?:[^\]]|\](?!\]>))*\]\]>|(?!<!\[CDATA\[)[\s\S])*?(?=<\/__>)/.source.replace(/__/g,function(){return l}),"i"),lookbehind:!0,greedy:!0,inside:a},r.languages.insertBefore("markup","cdata",u)}}),Object.defineProperty(r.languages.markup.tag,"addAttribute",{value:function(t,l){r.languages.markup.tag.inside["special-attr"].push({pattern:RegExp(/(^|["'\s])/.source+"(?:"+t+")"+/\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))/.source,"i"),lookbehind:!0,inside:{"attr-name":/^[^\s=]+/,"attr-value":{pattern:/=[\s\S]+/,inside:{value:{pattern:/(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,lookbehind:!0,alias:[l,"language-"+l],inside:r.languages[l]},punctuation:[{pattern:/^=/,alias:"attr-equals"},/"|'/]}}}})}}),r.languages.html=r.languages.markup,r.languages.mathml=r.languages.markup,r.languages.svg=r.languages.markup,r.languages.xml=r.languages.extend("markup",{}),r.languages.ssml=r.languages.xml,r.languages.atom=r.languages.xml,r.languages.rss=r.languages.xml,function(t){var l=/(?:"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n])*')/;t.languages.css={comment:/\/\*[\s\S]*?\*\//,atrule:{pattern:/@[\w-](?:[^;{\s]|\s+(?![\s{]))*(?:;|(?=\s*\{))/,inside:{rule:/^@[\w-]+/,"selector-function-argument":{pattern:/(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/,lookbehind:!0,alias:"selector"},keyword:{pattern:/(^|[^\w-])(?:and|not|only|or)(?![\w-])/,lookbehind:!0}}},url:{pattern:RegExp("\\burl\\((?:"+l.source+"|"+/(?:[^\\\r\n()"']|\\[\s\S])*/.source+")\\)","i"),greedy:!0,inside:{function:/^url/i,punctuation:/^\(|\)$/,string:{pattern:RegExp("^"+l.source+"$"),alias:"url"}}},selector:{pattern:RegExp(`(^|[{}\\s])[^{}\\s](?:[^{};"'\\s]|\\s+(?![\\s{])|`+l.source+")*(?=\\s*\\{)"),lookbehind:!0},string:{pattern:l,greedy:!0},property:{pattern:/(^|[^-\w\xA0-\uFFFF])(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i,lookbehind:!0},important:/!important\b/i,function:{pattern:/(^|[^-a-z0-9])[-a-z0-9]+(?=\()/i,lookbehind:!0},punctuation:/[(){};:,]/},t.languages.css.atrule.inside.rest=t.languages.css;var c=t.languages.markup;c&&(c.tag.addInlined("style","css"),c.tag.addAttribute("style","css"))}(r),r.languages.clike={comment:[{pattern:/(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,lookbehind:!0,greedy:!0},{pattern:/(^|[^\\:])\/\/.*/,lookbehind:!0,greedy:!0}],string:{pattern:/(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,greedy:!0},"class-name":{pattern:/(\b(?:class|interface|extends|implements|trait|instanceof|new)\s+|\bcatch\s+\()[\w.\\]+/i,lookbehind:!0,inside:{punctuation:/[.\\]/}},keyword:/\b(?:if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,boolean:/\b(?:true|false)\b/,function:/\b\w+(?=\()/,number:/\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,operator:/[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/,punctuation:/[{}[\];(),.:]/},r.languages.javascript=r.languages.extend("clike",{"class-name":[r.languages.clike["class-name"],{pattern:/(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:prototype|constructor))/,lookbehind:!0}],keyword:[{pattern:/((?:^|\})\s*)catch\b/,lookbehind:!0},{pattern:/(^|[^.]|\.\.\.\s*)\b(?:as|assert(?=\s*\{)|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/,lookbehind:!0}],function:/#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,number:/\b(?:(?:0[xX](?:[\dA-Fa-f](?:_[\dA-Fa-f])?)+|0[bB](?:[01](?:_[01])?)+|0[oO](?:[0-7](?:_[0-7])?)+)n?|(?:\d(?:_\d)?)+n|NaN|Infinity)\b|(?:\b(?:\d(?:_\d)?)+\.?(?:\d(?:_\d)?)*|\B\.(?:\d(?:_\d)?)+)(?:[Ee][+-]?(?:\d(?:_\d)?)+)?/,operator:/--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/}),r.languages.javascript["class-name"][0].pattern=/(\b(?:class|interface|extends|implements|instanceof|new)\s+)[\w.\\]+/,r.languages.insertBefore("javascript","keyword",{regex:{pattern:/((?:^|[^$\w\xA0-\uFFFF."'\])\s]|\b(?:return|yield))\s*)\/(?:\[(?:[^\]\\\r\n]|\\.)*\]|\\.|[^/\\\[\r\n])+\/[dgimyus]{0,7}(?=(?:\s|\/\*(?:[^*]|\*(?!\/))*\*\/)*(?:$|[\r\n,.;:})\]]|\/\/))/,lookbehind:!0,greedy:!0,inside:{"regex-source":{pattern:/^(\/)[\s\S]+(?=\/[a-z]*$)/,lookbehind:!0,alias:"language-regex",inside:r.languages.regex},"regex-delimiter":/^\/|\/$/,"regex-flags":/^[a-z]+$/}},"function-variable":{pattern:/#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/,alias:"function"},parameter:[{pattern:/(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/,lookbehind:!0,inside:r.languages.javascript},{pattern:/(^|[^$\w\xA0-\uFFFF])(?!\s)[_$a-z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i,lookbehind:!0,inside:r.languages.javascript},{pattern:/(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/,lookbehind:!0,inside:r.languages.javascript},{pattern:/((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/,lookbehind:!0,inside:r.languages.javascript}],constant:/\b[A-Z](?:[A-Z_]|\dx?)*\b/}),r.languages.insertBefore("javascript","string",{hashbang:{pattern:/^#!.*/,greedy:!0,alias:"comment"},"template-string":{pattern:/`(?:\\[\s\S]|\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}|(?!\$\{)[^\\`])*`/,greedy:!0,inside:{"template-punctuation":{pattern:/^`|`$/,alias:"string"},interpolation:{pattern:/((?:^|[^\\])(?:\\{2})*)\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}/,lookbehind:!0,inside:{"interpolation-punctuation":{pattern:/^\$\{|\}$/,alias:"punctuation"},rest:r.languages.javascript}},string:/[\s\S]+/}}}),r.languages.markup&&(r.languages.markup.tag.addInlined("script","javascript"),r.languages.markup.tag.addAttribute(/on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)/.source,"javascript")),r.languages.js=r.languages.javascript,function(){if(typeof r=="undefined"||typeof document=="undefined")return;Element.prototype.matches||(Element.prototype.matches=Element.prototype.msMatchesSelector||Element.prototype.webkitMatchesSelector);var t="Loading\u2026",l=function(_,A){return"\u2716 Error "+_+" while fetching file: "+A},c="\u2716 Error: File does not exist or is empty",p={js:"javascript",py:"python",rb:"ruby",ps1:"powershell",psm1:"powershell",sh:"bash",bat:"batch",h:"c",tex:"latex"},a="data-src-status",u="loading",h="loaded",i="failed",m="pre[data-src]:not(["+a+'="'+h+'"]):not(['+a+'="'+u+'"])',d=/\blang(?:uage)?-([\w-]+)\b/i;function f(_,A){var C=_.className;C=C.replace(d," ")+" language-"+A,_.className=C.replace(/\s+/g," ").trim()}r.hooks.add("before-highlightall",function(_){_.selector+=", "+m}),r.hooks.add("before-sanity-check",function(_){var A=_.element;if(A.matches(m)){_.code="",A.setAttribute(a,u);var C=A.appendChild(document.createElement("CODE"));C.textContent=t;var E=A.getAttribute("data-src"),S=_.language;if(S==="none"){var b=(/\.(\w+)$/.exec(E)||[,"none"])[1];S=p[b]||b}f(C,S),f(A,S);var x=r.plugins.autoloader;x&&x.loadLanguages(S);var D=new XMLHttpRequest;D.open("GET",E,!0),D.onreadystatechange=function(){D.readyState==4&&(D.status<400&&D.responseText?(A.setAttribute(a,h),C.textContent=D.responseText,r.highlightElement(C)):(A.setAttribute(a,i),D.status>=400?C.textContent=l(D.status,D.statusText):C.textContent=c))},D.send(null)}}),r.plugins.fileHighlight={highlight:function(A){for(var C=(A||document).querySelectorAll(m),E=0,S;S=C[E++];)r.highlightElement(S)}};var v=!1;r.fileHighlight=function(){v||(console.warn("Prism.fileHighlight is deprecated. Use `Prism.plugins.fileHighlight.highlight` instead."),v=!0),r.plugins.fileHighlight.highlight.apply(this,arguments)}}()},8840:(P,y)=>{"use strict";var o=Object.prototype.hasOwnProperty,g;function r(p){try{return decodeURIComponent(p.replace(/\+/g," "))}catch(a){return null}}function t(p){try{return encodeURIComponent(p)}catch(a){return null}}function l(p){for(var a=/([^=?#&]+)=?([^&]*)/g,u={},h;h=a.exec(p);){var i=r(h[1]),m=r(h[2]);i===null||m===null||i in u||(u[i]=m)}return u}function c(p,a){a=a||"";var u=[],h,i;typeof a!="string"&&(a="?");for(i in p)if(o.call(p,i)){if(h=p[i],!h&&(h===null||h===g||isNaN(h))&&(h=""),i=t(i),h=t(h),i===null||h===null)continue;u.push(i+"="+h)}return u.length?a+u.join("&"):""}y.stringify=c,y.parse=l},3697:P=>{"use strict";P.exports=function(o,g){if(g=g.split(":")[0],o=+o,!o)return!1;switch(g){case"http":case"ws":return o!==80;case"https":case"wss":return o!==443;case"ftp":return o!==21;case"gopher":return o!==70;case"file":return!1}return o!==0}},1530:(P,y,o)=>{const g=Symbol("SemVer ANY");class r{static get ANY(){return g}constructor(m,d){if(d=t(d),m instanceof r){if(m.loose===!!d.loose)return m;m=m.value}a("comparator",m,d),this.options=d,this.loose=!!d.loose,this.parse(m),this.semver===g?this.value="":this.value=this.operator+this.semver.version,a("comp",this)}parse(m){const d=this.options.loose?l[c.COMPARATORLOOSE]:l[c.COMPARATOR],f=m.match(d);if(!f)throw new TypeError(`Invalid comparator: ${m}`);this.operator=f[1]!==void 0?f[1]:"",this.operator==="="&&(this.operator=""),f[2]?this.semver=new u(f[2],this.options.loose):this.semver=g}toString(){return this.value}test(m){if(a("Comparator.test",m,this.options.loose),this.semver===g||m===g)return!0;if(typeof m=="string")try{m=new u(m,this.options)}catch(d){return!1}return p(m,this.operator,this.semver,this.options)}intersects(m,d){if(!(m instanceof r))throw new TypeError("a Comparator is required");if((!d||typeof d!="object")&&(d={loose:!!d,includePrerelease:!1}),this.operator==="")return this.value===""?!0:new h(m.value,d).test(this.value);if(m.operator==="")return m.value===""?!0:new h(this.value,d).test(m.semver);const f=(this.operator===">="||this.operator===">")&&(m.operator===">="||m.operator===">"),v=(this.operator==="<="||this.operator==="<")&&(m.operator==="<="||m.operator==="<"),_=this.semver.version===m.semver.version,A=(this.operator===">="||this.operator==="<=")&&(m.operator===">="||m.operator==="<="),C=p(this.semver,"<",m.semver,d)&&(this.operator===">="||this.operator===">")&&(m.operator==="<="||m.operator==="<"),E=p(this.semver,">",m.semver,d)&&(this.operator==="<="||this.operator==="<")&&(m.operator===">="||m.operator===">");return f||v||_&&A||C||E}}P.exports=r;const t=o(6112),{re:l,t:c}=o(2331),p=o(9970),a=o(6051),u=o(4908),h=o(6754)},6754:(P,y,o)=>{class g{constructor(L,F){if(F=l(F),L instanceof g)return L.loose===!!F.loose&&L.includePrerelease===!!F.includePrerelease?L:new g(L.raw,F);if(L instanceof c)return this.raw=L.value,this.set=[[L]],this.format(),this;if(this.options=F,this.loose=!!F.loose,this.includePrerelease=!!F.includePrerelease,this.raw=L,this.set=L.split(/\s*\|\|\s*/).map(Y=>this.parseRange(Y.trim())).filter(Y=>Y.length),!this.set.length)throw new TypeError(`Invalid SemVer Range: ${L}`);if(this.set.length>1){const Y=this.set[0];if(this.set=this.set.filter(z=>!f(z[0])),this.set.length===0)this.set=[Y];else if(this.set.length>1){for(const z of this.set)if(z.length===1&&v(z[0])){this.set=[z];break}}}this.format()}format(){return this.range=this.set.map(L=>L.join(" ").trim()).join("||").trim(),this.range}toString(){return this.range}parseRange(L){L=L.trim();const Y=`parseRange:${Object.keys(this.options).join(",")}:${L}`,z=t.get(Y);if(z)return z;const G=this.options.loose,W=G?u[h.HYPHENRANGELOOSE]:u[h.HYPHENRANGE];L=L.replace(W,O(this.options.includePrerelease)),p("hyphen replace",L),L=L.replace(u[h.COMPARATORTRIM],i),p("comparator trim",L,u[h.COMPARATORTRIM]),L=L.replace(u[h.TILDETRIM],m),L=L.replace(u[h.CARETTRIM],d),L=L.split(/\s+/).join(" ");const te=G?u[h.COMPARATORLOOSE]:u[h.COMPARATOR],oe=L.split(" ").map(Pe=>A(Pe,this.options)).join(" ").split(/\s+/).map(Pe=>w(Pe,this.options)).filter(this.options.loose?Pe=>!!Pe.match(te):()=>!0).map(Pe=>new c(Pe,this.options)),ge=oe.length,Q=new Map;for(const Pe of oe){if(f(Pe))return[Pe];Q.set(Pe.value,Pe)}Q.size>1&&Q.has("")&&Q.delete("");const Te=[...Q.values()];return t.set(Y,Te),Te}intersects(L,F){if(!(L instanceof g))throw new TypeError("a Range is required");return this.set.some(Y=>_(Y,F)&&L.set.some(z=>_(z,F)&&Y.every(G=>z.every(W=>G.intersects(W,F)))))}test(L){if(!L)return!1;if(typeof L=="string")try{L=new a(L,this.options)}catch(F){return!1}for(let F=0;F<this.set.length;F++)if(B(this.set[F],L,this.options))return!0;return!1}}P.exports=g;const r=o(5977),t=new r({max:1e3}),l=o(6112),c=o(1530),p=o(6051),a=o(4908),{re:u,t:h,comparatorTrimReplace:i,tildeTrimReplace:m,caretTrimReplace:d}=o(2331),f=U=>U.value==="<0.0.0-0",v=U=>U.value==="",_=(U,L)=>{let F=!0;const Y=U.slice();let z=Y.pop();for(;F&&Y.length;)F=Y.every(G=>z.intersects(G,L)),z=Y.pop();return F},A=(U,L)=>(p("comp",U,L),U=b(U,L),p("caret",U),U=E(U,L),p("tildes",U),U=D(U,L),p("xrange",U),U=I(U,L),p("stars",U),U),C=U=>!U||U.toLowerCase()==="x"||U==="*",E=(U,L)=>U.trim().split(/\s+/).map(F=>S(F,L)).join(" "),S=(U,L)=>{const F=L.loose?u[h.TILDELOOSE]:u[h.TILDE];return U.replace(F,(Y,z,G,W,te)=>{p("tilde",U,Y,z,G,W,te);let oe;return C(z)?oe="":C(G)?oe=`>=${z}.0.0 <${+z+1}.0.0-0`:C(W)?oe=`>=${z}.${G}.0 <${z}.${+G+1}.0-0`:te?(p("replaceTilde pr",te),oe=`>=${z}.${G}.${W}-${te} <${z}.${+G+1}.0-0`):oe=`>=${z}.${G}.${W} <${z}.${+G+1}.0-0`,p("tilde return",oe),oe})},b=(U,L)=>U.trim().split(/\s+/).map(F=>x(F,L)).join(" "),x=(U,L)=>{p("caret",U,L);const F=L.loose?u[h.CARETLOOSE]:u[h.CARET],Y=L.includePrerelease?"-0":"";return U.replace(F,(z,G,W,te,oe)=>{p("caret",U,z,G,W,te,oe);let ge;return C(G)?ge="":C(W)?ge=`>=${G}.0.0${Y} <${+G+1}.0.0-0`:C(te)?G==="0"?ge=`>=${G}.${W}.0${Y} <${G}.${+W+1}.0-0`:ge=`>=${G}.${W}.0${Y} <${+G+1}.0.0-0`:oe?(p("replaceCaret pr",oe),G==="0"?W==="0"?ge=`>=${G}.${W}.${te}-${oe} <${G}.${W}.${+te+1}-0`:ge=`>=${G}.${W}.${te}-${oe} <${G}.${+W+1}.0-0`:ge=`>=${G}.${W}.${te}-${oe} <${+G+1}.0.0-0`):(p("no pr"),G==="0"?W==="0"?ge=`>=${G}.${W}.${te}${Y} <${G}.${W}.${+te+1}-0`:ge=`>=${G}.${W}.${te}${Y} <${G}.${+W+1}.0-0`:ge=`>=${G}.${W}.${te} <${+G+1}.0.0-0`),p("caret return",ge),ge})},D=(U,L)=>(p("replaceXRanges",U,L),U.split(/\s+/).map(F=>N(F,L)).join(" ")),N=(U,L)=>{U=U.trim();const F=L.loose?u[h.XRANGELOOSE]:u[h.XRANGE];return U.replace(F,(Y,z,G,W,te,oe)=>{p("xRange",U,Y,z,G,W,te,oe);const ge=C(G),Q=ge||C(W),Te=Q||C(te),Pe=Te;return z==="="&&Pe&&(z=""),oe=L.includePrerelease?"-0":"",ge?z===">"||z==="<"?Y="<0.0.0-0":Y="*":z&&Pe?(Q&&(W=0),te=0,z===">"?(z=">=",Q?(G=+G+1,W=0,te=0):(W=+W+1,te=0)):z==="<="&&(z="<",Q?G=+G+1:W=+W+1),z==="<"&&(oe="-0"),Y=`${z+G}.${W}.${te}${oe}`):Q?Y=`>=${G}.0.0${oe} <${+G+1}.0.0-0`:Te&&(Y=`>=${G}.${W}.0${oe} <${G}.${+W+1}.0-0`),p("xRange return",Y),Y})},I=(U,L)=>(p("replaceStars",U,L),U.trim().replace(u[h.STAR],"")),w=(U,L)=>(p("replaceGTE0",U,L),U.trim().replace(u[L.includePrerelease?h.GTE0PRE:h.GTE0],"")),O=U=>(L,F,Y,z,G,W,te,oe,ge,Q,Te,Pe,Ge)=>(C(Y)?F="":C(z)?F=`>=${Y}.0.0${U?"-0":""}`:C(G)?F=`>=${Y}.${z}.0${U?"-0":""}`:W?F=`>=${F}`:F=`>=${F}${U?"-0":""}`,C(ge)?oe="":C(Q)?oe=`<${+ge+1}.0.0-0`:C(Te)?oe=`<${ge}.${+Q+1}.0-0`:Pe?oe=`<=${ge}.${Q}.${Te}-${Pe}`:U?oe=`<${ge}.${Q}.${+Te+1}-0`:oe=`<=${oe}`,`${F} ${oe}`.trim()),B=(U,L,F)=>{for(let Y=0;Y<U.length;Y++)if(!U[Y].test(L))return!1;if(L.prerelease.length&&!F.includePrerelease){for(let Y=0;Y<U.length;Y++)if(p(U[Y].semver),U[Y].semver!==c.ANY&&U[Y].semver.prerelease.length>0){const z=U[Y].semver;if(z.major===L.major&&z.minor===L.minor&&z.patch===L.patch)return!0}return!1}return!0}},4908:(P,y,o)=>{const g=o(6051),{MAX_LENGTH:r,MAX_SAFE_INTEGER:t}=o(8330),{re:l,t:c}=o(2331),p=o(6112),{compareIdentifiers:a}=o(9388);class u{constructor(i,m){if(m=p(m),i instanceof u){if(i.loose===!!m.loose&&i.includePrerelease===!!m.includePrerelease)return i;i=i.version}else if(typeof i!="string")throw new TypeError(`Invalid Version: ${i}`);if(i.length>r)throw new TypeError(`version is longer than ${r} characters`);g("SemVer",i,m),this.options=m,this.loose=!!m.loose,this.includePrerelease=!!m.includePrerelease;const d=i.trim().match(m.loose?l[c.LOOSE]:l[c.FULL]);if(!d)throw new TypeError(`Invalid Version: ${i}`);if(this.raw=i,this.major=+d[1],this.minor=+d[2],this.patch=+d[3],this.major>t||this.major<0)throw new TypeError("Invalid major version");if(this.minor>t||this.minor<0)throw new TypeError("Invalid minor version");if(this.patch>t||this.patch<0)throw new TypeError("Invalid patch version");d[4]?this.prerelease=d[4].split(".").map(f=>{if(/^[0-9]+$/.test(f)){const v=+f;if(v>=0&&v<t)return v}return f}):this.prerelease=[],this.build=d[5]?d[5].split("."):[],this.format()}format(){return this.version=`${this.major}.${this.minor}.${this.patch}`,this.prerelease.length&&(this.version+=`-${this.prerelease.join(".")}`),this.version}toString(){return this.version}compare(i){if(g("SemVer.compare",this.version,this.options,i),!(i instanceof u)){if(typeof i=="string"&&i===this.version)return 0;i=new u(i,this.options)}return i.version===this.version?0:this.compareMain(i)||this.comparePre(i)}compareMain(i){return i instanceof u||(i=new u(i,this.options)),a(this.major,i.major)||a(this.minor,i.minor)||a(this.patch,i.patch)}comparePre(i){if(i instanceof u||(i=new u(i,this.options)),this.prerelease.length&&!i.prerelease.length)return-1;if(!this.prerelease.length&&i.prerelease.length)return 1;if(!this.prerelease.length&&!i.prerelease.length)return 0;let m=0;do{const d=this.prerelease[m],f=i.prerelease[m];if(g("prerelease compare",m,d,f),d===void 0&&f===void 0)return 0;if(f===void 0)return 1;if(d===void 0)return-1;if(d===f)continue;return a(d,f)}while(++m)}compareBuild(i){i instanceof u||(i=new u(i,this.options));let m=0;do{const d=this.build[m],f=i.build[m];if(g("prerelease compare",m,d,f),d===void 0&&f===void 0)return 0;if(f===void 0)return 1;if(d===void 0)return-1;if(d===f)continue;return a(d,f)}while(++m)}inc(i,m){switch(i){case"premajor":this.prerelease.length=0,this.patch=0,this.minor=0,this.major++,this.inc("pre",m);break;case"preminor":this.prerelease.length=0,this.patch=0,this.minor++,this.inc("pre",m);break;case"prepatch":this.prerelease.length=0,this.inc("patch",m),this.inc("pre",m);break;case"prerelease":this.prerelease.length===0&&this.inc("patch",m),this.inc("pre",m);break;case"major":(this.minor!==0||this.patch!==0||this.prerelease.length===0)&&this.major++,this.minor=0,this.patch=0,this.prerelease=[];break;case"minor":(this.patch!==0||this.prerelease.length===0)&&this.minor++,this.patch=0,this.prerelease=[];break;case"patch":this.prerelease.length===0&&this.patch++,this.prerelease=[];break;case"pre":if(this.prerelease.length===0)this.prerelease=[0];else{let d=this.prerelease.length;for(;--d>=0;)typeof this.prerelease[d]=="number"&&(this.prerelease[d]++,d=-2);d===-1&&this.prerelease.push(0)}m&&(this.prerelease[0]===m?isNaN(this.prerelease[1])&&(this.prerelease=[m,0]):this.prerelease=[m,0]);break;default:throw new Error(`invalid increment argument: ${i}`)}return this.format(),this.raw=this.version,this}}P.exports=u},5754:(P,y,o)=>{const g=o(853),r=(t,l)=>{const c=g(t.trim().replace(/^[=v]+/,""),l);return c?c.version:null};P.exports=r},9970:(P,y,o)=>{const g=o(518),r=o(2134),t=o(4054),l=o(218),c=o(6291),p=o(990),a=(u,h,i,m)=>{switch(h){case"===":return typeof u=="object"&&(u=u.version),typeof i=="object"&&(i=i.version),u===i;case"!==":return typeof u=="object"&&(u=u.version),typeof i=="object"&&(i=i.version),u!==i;case"":case"=":case"==":return g(u,i,m);case"!=":return r(u,i,m);case">":return t(u,i,m);case">=":return l(u,i,m);case"<":return c(u,i,m);case"<=":return p(u,i,m);default:throw new TypeError(`Invalid operator: ${h}`)}};P.exports=a},2722:(P,y,o)=>{const g=o(4908),r=o(853),{re:t,t:l}=o(2331),c=(p,a)=>{if(p instanceof g)return p;if(typeof p=="number"&&(p=String(p)),typeof p!="string")return null;a=a||{};let u=null;if(!a.rtl)u=p.match(t[l.COERCE]);else{let h;for(;(h=t[l.COERCERTL].exec(p))&&(!u||u.index+u[0].length!==p.length);)(!u||h.index+h[0].length!==u.index+u[0].length)&&(u=h),t[l.COERCERTL].lastIndex=h.index+h[1].length+h[2].length;t[l.COERCERTL].lastIndex=-1}return u===null?null:r(`${u[2]}.${u[3]||"0"}.${u[4]||"0"}`,a)};P.exports=c},5727:(P,y,o)=>{const g=o(4908),r=(t,l,c)=>{const p=new g(t,c),a=new g(l,c);return p.compare(a)||p.compareBuild(a)};P.exports=r},7961:(P,y,o)=>{const g=o(7570),r=(t,l)=>g(t,l,!0);P.exports=r},7570:(P,y,o)=>{const g=o(4908),r=(t,l,c)=>new g(t,c).compare(new g(l,c));P.exports=r},1205:(P,y,o)=>{const g=o(853),r=o(518),t=(l,c)=>{if(r(l,c))return null;{const p=g(l),a=g(c),u=p.prerelease.length||a.prerelease.length,h=u?"pre":"",i=u?"prerelease":"";for(const m in p)if((m==="major"||m==="minor"||m==="patch")&&p[m]!==a[m])return h+m;return i}};P.exports=t},518:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)===0;P.exports=r},4054:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)>0;P.exports=r},218:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)>=0;P.exports=r},2572:(P,y,o)=>{const g=o(4908),r=(t,l,c,p)=>{typeof c=="string"&&(p=c,c=void 0);try{return new g(t,c).inc(l,p).version}catch(a){return null}};P.exports=r},6291:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)<0;P.exports=r},990:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)<=0;P.exports=r},7626:(P,y,o)=>{const g=o(4908),r=(t,l)=>new g(t,l).major;P.exports=r},7710:(P,y,o)=>{const g=o(4908),r=(t,l)=>new g(t,l).minor;P.exports=r},2134:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(t,l,c)!==0;P.exports=r},853:(P,y,o)=>{const{MAX_LENGTH:g}=o(8330),{re:r,t}=o(2331),l=o(4908),c=o(6112),p=(a,u)=>{if(u=c(u),a instanceof l)return a;if(typeof a!="string"||a.length>g||!(u.loose?r[t.LOOSE]:r[t.FULL]).test(a))return null;try{return new l(a,u)}catch(i){return null}};P.exports=p},6282:(P,y,o)=>{const g=o(4908),r=(t,l)=>new g(t,l).patch;P.exports=r},5092:(P,y,o)=>{const g=o(853),r=(t,l)=>{const c=g(t,l);return c&&c.prerelease.length?c.prerelease:null};P.exports=r},9174:(P,y,o)=>{const g=o(7570),r=(t,l,c)=>g(l,t,c);P.exports=r},8048:(P,y,o)=>{const g=o(5727),r=(t,l)=>t.sort((c,p)=>g(p,c,l));P.exports=r},8608:(P,y,o)=>{const g=o(6754),r=(t,l,c)=>{try{l=new g(l,c)}catch(p){return!1}return l.test(t)};P.exports=r},2788:(P,y,o)=>{const g=o(5727),r=(t,l)=>t.sort((c,p)=>g(c,p,l));P.exports=r},7214:(P,y,o)=>{const g=o(853),r=(t,l)=>{const c=g(t,l);return c?c.version:null};P.exports=r},1207:(P,y,o)=>{const g=o(2331);P.exports={re:g.re,src:g.src,tokens:g.t,SEMVER_SPEC_VERSION:o(8330).SEMVER_SPEC_VERSION,SemVer:o(4908),compareIdentifiers:o(9388).compareIdentifiers,rcompareIdentifiers:o(9388).rcompareIdentifiers,parse:o(853),valid:o(7214),clean:o(5754),inc:o(2572),diff:o(1205),major:o(7626),minor:o(7710),patch:o(6282),prerelease:o(5092),compare:o(7570),rcompare:o(9174),compareLoose:o(7961),compareBuild:o(5727),sort:o(2788),rsort:o(8048),gt:o(4054),lt:o(6291),eq:o(518),neq:o(2134),gte:o(218),lte:o(990),cmp:o(9970),coerce:o(2722),Comparator:o(1530),Range:o(6754),satisfies:o(8608),toComparators:o(4453),maxSatisfying:o(9079),minSatisfying:o(5976),minVersion:o(7601),validRange:o(8237),outside:o(6783),gtr:o(6128),ltr:o(8408),intersects:o(4009),simplifyRange:o(4417),subset:o(4835)}},8330:P=>{const y="2.0.0",o=256,g=Number.MAX_SAFE_INTEGER||9007199254740991,r=16;P.exports={SEMVER_SPEC_VERSION:y,MAX_LENGTH:o,MAX_SAFE_INTEGER:g,MAX_SAFE_COMPONENT_LENGTH:r}},6051:P=>{const y=typeof process=="object"&&process.env&&process.env.NODE_DEBUG&&/\bsemver\b/i.test(process.env.NODE_DEBUG)?(...o)=>console.error("SEMVER",...o):()=>{};P.exports=y},9388:P=>{const y=/^[0-9]+$/,o=(r,t)=>{const l=y.test(r),c=y.test(t);return l&&c&&(r=+r,t=+t),r===t?0:l&&!c?-1:c&&!l?1:r<t?-1:1},g=(r,t)=>o(t,r);P.exports={compareIdentifiers:o,rcompareIdentifiers:g}},6112:P=>{const y=["includePrerelease","loose","rtl"],o=g=>g?typeof g!="object"?{loose:!0}:y.filter(r=>g[r]).reduce((r,t)=>(r[t]=!0,r),{}):{};P.exports=o},2331:(P,y,o)=>{const{MAX_SAFE_COMPONENT_LENGTH:g}=o(8330),r=o(6051);y=P.exports={};const t=y.re=[],l=y.src=[],c=y.t={};let p=0;const a=(u,h,i)=>{const m=p++;r(m,h),c[u]=m,l[m]=h,t[m]=new RegExp(h,i?"g":void 0)};a("NUMERICIDENTIFIER","0|[1-9]\\d*"),a("NUMERICIDENTIFIERLOOSE","[0-9]+"),a("NONNUMERICIDENTIFIER","\\d*[a-zA-Z-][a-zA-Z0-9-]*"),a("MAINVERSION",`(${l[c.NUMERICIDENTIFIER]})\\.(${l[c.NUMERICIDENTIFIER]})\\.(${l[c.NUMERICIDENTIFIER]})`),a("MAINVERSIONLOOSE",`(${l[c.NUMERICIDENTIFIERLOOSE]})\\.(${l[c.NUMERICIDENTIFIERLOOSE]})\\.(${l[c.NUMERICIDENTIFIERLOOSE]})`),a("PRERELEASEIDENTIFIER",`(?:${l[c.NUMERICIDENTIFIER]}|${l[c.NONNUMERICIDENTIFIER]})`),a("PRERELEASEIDENTIFIERLOOSE",`(?:${l[c.NUMERICIDENTIFIERLOOSE]}|${l[c.NONNUMERICIDENTIFIER]})`),a("PRERELEASE",`(?:-(${l[c.PRERELEASEIDENTIFIER]}(?:\\.${l[c.PRERELEASEIDENTIFIER]})*))`),a("PRERELEASELOOSE",`(?:-?(${l[c.PRERELEASEIDENTIFIERLOOSE]}(?:\\.${l[c.PRERELEASEIDENTIFIERLOOSE]})*))`),a("BUILDIDENTIFIER","[0-9A-Za-z-]+"),a("BUILD",`(?:\\+(${l[c.BUILDIDENTIFIER]}(?:\\.${l[c.BUILDIDENTIFIER]})*))`),a("FULLPLAIN",`v?${l[c.MAINVERSION]}${l[c.PRERELEASE]}?${l[c.BUILD]}?`),a("FULL",`^${l[c.FULLPLAIN]}$`),a("LOOSEPLAIN",`[v=\\s]*${l[c.MAINVERSIONLOOSE]}${l[c.PRERELEASELOOSE]}?${l[c.BUILD]}?`),a("LOOSE",`^${l[c.LOOSEPLAIN]}$`),a("GTLT","((?:<|>)?=?)"),a("XRANGEIDENTIFIERLOOSE",`${l[c.NUMERICIDENTIFIERLOOSE]}|x|X|\\*`),a("XRANGEIDENTIFIER",`${l[c.NUMERICIDENTIFIER]}|x|X|\\*`),a("XRANGEPLAIN",`[v=\\s]*(${l[c.XRANGEIDENTIFIER]})(?:\\.(${l[c.XRANGEIDENTIFIER]})(?:\\.(${l[c.XRANGEIDENTIFIER]})(?:${l[c.PRERELEASE]})?${l[c.BUILD]}?)?)?`),a("XRANGEPLAINLOOSE",`[v=\\s]*(${l[c.XRANGEIDENTIFIERLOOSE]})(?:\\.(${l[c.XRANGEIDENTIFIERLOOSE]})(?:\\.(${l[c.XRANGEIDENTIFIERLOOSE]})(?:${l[c.PRERELEASELOOSE]})?${l[c.BUILD]}?)?)?`),a("XRANGE",`^${l[c.GTLT]}\\s*${l[c.XRANGEPLAIN]}$`),a("XRANGELOOSE",`^${l[c.GTLT]}\\s*${l[c.XRANGEPLAINLOOSE]}$`),a("COERCE",`(^|[^\\d])(\\d{1,${g}})(?:\\.(\\d{1,${g}}))?(?:\\.(\\d{1,${g}}))?(?:$|[^\\d])`),a("COERCERTL",l[c.COERCE],!0),a("LONETILDE","(?:~>?)"),a("TILDETRIM",`(\\s*)${l[c.LONETILDE]}\\s+`,!0),y.tildeTrimReplace="$1~",a("TILDE",`^${l[c.LONETILDE]}${l[c.XRANGEPLAIN]}$`),a("TILDELOOSE",`^${l[c.LONETILDE]}${l[c.XRANGEPLAINLOOSE]}$`),a("LONECARET","(?:\\^)"),a("CARETTRIM",`(\\s*)${l[c.LONECARET]}\\s+`,!0),y.caretTrimReplace="$1^",a("CARET",`^${l[c.LONECARET]}${l[c.XRANGEPLAIN]}$`),a("CARETLOOSE",`^${l[c.LONECARET]}${l[c.XRANGEPLAINLOOSE]}$`),a("COMPARATORLOOSE",`^${l[c.GTLT]}\\s*(${l[c.LOOSEPLAIN]})$|^$`),a("COMPARATOR",`^${l[c.GTLT]}\\s*(${l[c.FULLPLAIN]})$|^$`),a("COMPARATORTRIM",`(\\s*)${l[c.GTLT]}\\s*(${l[c.LOOSEPLAIN]}|${l[c.XRANGEPLAIN]})`,!0),y.comparatorTrimReplace="$1$2$3",a("HYPHENRANGE",`^\\s*(${l[c.XRANGEPLAIN]})\\s+-\\s+(${l[c.XRANGEPLAIN]})\\s*$`),a("HYPHENRANGELOOSE",`^\\s*(${l[c.XRANGEPLAINLOOSE]})\\s+-\\s+(${l[c.XRANGEPLAINLOOSE]})\\s*$`),a("STAR","(<|>)?=?\\s*\\*"),a("GTE0","^\\s*>=\\s*0.0.0\\s*$"),a("GTE0PRE","^\\s*>=\\s*0.0.0-0\\s*$")},6128:(P,y,o)=>{const g=o(6783),r=(t,l,c)=>g(t,l,">",c);P.exports=r},4009:(P,y,o)=>{const g=o(6754),r=(t,l,c)=>(t=new g(t,c),l=new g(l,c),t.intersects(l));P.exports=r},8408:(P,y,o)=>{const g=o(6783),r=(t,l,c)=>g(t,l,"<",c);P.exports=r},9079:(P,y,o)=>{const g=o(4908),r=o(6754),t=(l,c,p)=>{let a=null,u=null,h=null;try{h=new r(c,p)}catch(i){return null}return l.forEach(i=>{h.test(i)&&(!a||u.compare(i)===-1)&&(a=i,u=new g(a,p))}),a};P.exports=t},5976:(P,y,o)=>{const g=o(4908),r=o(6754),t=(l,c,p)=>{let a=null,u=null,h=null;try{h=new r(c,p)}catch(i){return null}return l.forEach(i=>{h.test(i)&&(!a||u.compare(i)===1)&&(a=i,u=new g(a,p))}),a};P.exports=t},7601:(P,y,o)=>{const g=o(4908),r=o(6754),t=o(4054),l=(c,p)=>{c=new r(c,p);let a=new g("0.0.0");if(c.test(a)||(a=new g("0.0.0-0"),c.test(a)))return a;a=null;for(let u=0;u<c.set.length;++u){const h=c.set[u];let i=null;h.forEach(m=>{const d=new g(m.semver.version);switch(m.operator){case">":d.prerelease.length===0?d.patch++:d.prerelease.push(0),d.raw=d.format();case"":case">=":(!i||t(d,i))&&(i=d);break;case"<":case"<=":break;default:throw new Error(`Unexpected operation: ${m.operator}`)}}),i&&(!a||t(a,i))&&(a=i)}return a&&c.test(a)?a:null};P.exports=l},6783:(P,y,o)=>{const g=o(4908),r=o(1530),{ANY:t}=r,l=o(6754),c=o(8608),p=o(4054),a=o(6291),u=o(990),h=o(218),i=(m,d,f,v)=>{m=new g(m,v),d=new l(d,v);let _,A,C,E,S;switch(f){case">":_=p,A=u,C=a,E=">",S=">=";break;case"<":_=a,A=h,C=p,E="<",S="<=";break;default:throw new TypeError('Must provide a hilo val of "<" or ">"')}if(c(m,d,v))return!1;for(let b=0;b<d.set.length;++b){const x=d.set[b];let D=null,N=null;if(x.forEach(I=>{I.semver===t&&(I=new r(">=0.0.0")),D=D||I,N=N||I,_(I.semver,D.semver,v)?D=I:C(I.semver,N.semver,v)&&(N=I)}),D.operator===E||D.operator===S||(!N.operator||N.operator===E)&&A(m,N.semver))return!1;if(N.operator===S&&C(m,N.semver))return!1}return!0};P.exports=i},4417:(P,y,o)=>{const g=o(8608),r=o(7570);P.exports=(t,l,c)=>{const p=[];let a=null,u=null;const h=t.sort((f,v)=>r(f,v,c));for(const f of h)g(f,l,c)?(u=f,a||(a=f)):(u&&p.push([a,u]),u=null,a=null);a&&p.push([a,null]);const i=[];for(const[f,v]of p)f===v?i.push(f):!v&&f===h[0]?i.push("*"):v?f===h[0]?i.push(`<=${v}`):i.push(`${f} - ${v}`):i.push(`>=${f}`);const m=i.join(" || "),d=typeof l.raw=="string"?l.raw:String(l);return m.length<d.length?m:l}},4835:(P,y,o)=>{const g=o(6754),r=o(1530),{ANY:t}=r,l=o(8608),c=o(7570),p=(i,m,d={})=>{if(i===m)return!0;i=new g(i,d),m=new g(m,d);let f=!1;e:for(const v of i.set){for(const _ of m.set){const A=a(v,_,d);if(f=f||A!==null,A)continue e}if(f)return!1}return!0},a=(i,m,d)=>{if(i===m)return!0;if(i.length===1&&i[0].semver===t){if(m.length===1&&m[0].semver===t)return!0;d.includePrerelease?i=[new r(">=0.0.0-0")]:i=[new r(">=0.0.0")]}if(m.length===1&&m[0].semver===t){if(d.includePrerelease)return!0;m=[new r(">=0.0.0")]}const f=new Set;let v,_;for(const N of i)N.operator===">"||N.operator===">="?v=u(v,N,d):N.operator==="<"||N.operator==="<="?_=h(_,N,d):f.add(N.semver);if(f.size>1)return null;let A;if(v&&_){if(A=c(v.semver,_.semver,d),A>0)return null;if(A===0&&(v.operator!==">="||_.operator!=="<="))return null}for(const N of f){if(v&&!l(N,String(v),d)||_&&!l(N,String(_),d))return null;for(const I of m)if(!l(N,String(I),d))return!1;return!0}let C,E,S,b,x=_&&!d.includePrerelease&&_.semver.prerelease.length?_.semver:!1,D=v&&!d.includePrerelease&&v.semver.prerelease.length?v.semver:!1;x&&x.prerelease.length===1&&_.operator==="<"&&x.prerelease[0]===0&&(x=!1);for(const N of m){if(b=b||N.operator===">"||N.operator===">=",S=S||N.operator==="<"||N.operator==="<=",v){if(D&&N.semver.prerelease&&N.semver.prerelease.length&&N.semver.major===D.major&&N.semver.minor===D.minor&&N.semver.patch===D.patch&&(D=!1),N.operator===">"||N.operator===">="){if(C=u(v,N,d),C===N&&C!==v)return!1}else if(v.operator===">="&&!l(v.semver,String(N),d))return!1}if(_){if(x&&N.semver.prerelease&&N.semver.prerelease.length&&N.semver.major===x.major&&N.semver.minor===x.minor&&N.semver.patch===x.patch&&(x=!1),N.operator==="<"||N.operator==="<="){if(E=h(_,N,d),E===N&&E!==_)return!1}else if(_.operator==="<="&&!l(_.semver,String(N),d))return!1}if(!N.operator&&(_||v)&&A!==0)return!1}return!(v&&S&&!_&&A!==0||_&&b&&!v&&A!==0||D||x)},u=(i,m,d)=>{if(!i)return m;const f=c(i.semver,m.semver,d);return f>0?i:f<0||m.operator===">"&&i.operator===">="?m:i},h=(i,m,d)=>{if(!i)return m;const f=c(i.semver,m.semver,d);return f<0?i:f>0||m.operator==="<"&&i.operator==="<="?m:i};P.exports=p},4453:(P,y,o)=>{const g=o(6754),r=(t,l)=>new g(t,l).set.map(c=>c.map(p=>p.value).join(" ").trim().split(" "));P.exports=r},8237:(P,y,o)=>{const g=o(6754),r=(t,l)=>{try{return new g(t,l).range||"*"}catch(c){return null}};P.exports=r},200:(P,y,o)=>{"use strict";var g=o(3697),r=o(8840),t=/^[A-Za-z][A-Za-z0-9+-.]*:\/\//,l=/^([a-z][a-z0-9.+-]*:)?(\/\/)?([\\/]+)?([\S\s]*)/i,c=/^[a-zA-Z]:/,p="[\\x09\\x0A\\x0B\\x0C\\x0D\\x20\\xA0\\u1680\\u180E\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200A\\u202F\\u205F\\u3000\\u2028\\u2029\\uFEFF]",a=new RegExp("^"+p+"+");function u(E){return(E||"").toString().replace(a,"")}var h=[["#","hash"],["?","query"],function(S,b){return d(b.protocol)?S.replace(/\\/g,"/"):S},["/","pathname"],["@","auth",1],[NaN,"host",void 0,1,1],[/:(\d+)$/,"port",void 0,1],[NaN,"hostname",void 0,1,1]],i={hash:1,query:1};function m(E){var S;typeof window!="undefined"?S=window:typeof o.g!="undefined"?S=o.g:typeof self!="undefined"?S=self:S={};var b=S.location||{};E=E||b;var x={},D=typeof E,N;if(E.protocol==="blob:")x=new _(unescape(E.pathname),{});else if(D==="string"){x=new _(E,{});for(N in i)delete x[N]}else if(D==="object"){for(N in E)N in i||(x[N]=E[N]);x.slashes===void 0&&(x.slashes=t.test(E.href))}return x}function d(E){return E==="file:"||E==="ftp:"||E==="http:"||E==="https:"||E==="ws:"||E==="wss:"}function f(E,S){E=u(E),S=S||{};var b=l.exec(E),x=b[1]?b[1].toLowerCase():"",D=!!b[2],N=!!b[3],I=0,w;return D?N?(w=b[2]+b[3]+b[4],I=b[2].length+b[3].length):(w=b[2]+b[4],I=b[2].length):N?(w=b[3]+b[4],I=b[3].length):w=b[4],x==="file:"?I>=2&&(w=w.slice(2)):d(x)?w=b[4]:x?D&&(w=w.slice(2)):I>=2&&d(S.protocol)&&(w=b[4]),{protocol:x,slashes:D||d(x),slashesCount:I,rest:w}}function v(E,S){if(E==="")return S;for(var b=(S||"/").split("/").slice(0,-1).concat(E.split("/")),x=b.length,D=b[x-1],N=!1,I=0;x--;)b[x]==="."?b.splice(x,1):b[x]===".."?(b.splice(x,1),I++):I&&(x===0&&(N=!0),b.splice(x,1),I--);return N&&b.unshift(""),(D==="."||D==="..")&&b.push(""),b.join("/")}function _(E,S,b){if(E=u(E),!(this instanceof _))return new _(E,S,b);var x,D,N,I,w,O,B=h.slice(),U=typeof S,L=this,F=0;for(U!=="object"&&U!=="string"&&(b=S,S=null),b&&typeof b!="function"&&(b=r.parse),S=m(S),D=f(E||"",S),x=!D.protocol&&!D.slashes,L.slashes=D.slashes||x&&S.slashes,L.protocol=D.protocol||S.protocol||"",E=D.rest,(D.protocol==="file:"&&(D.slashesCount!==2||c.test(E))||!D.slashes&&(D.protocol||D.slashesCount<2||!d(L.protocol)))&&(B[3]=[/(.*)/,"pathname"]);F<B.length;F++){if(I=B[F],typeof I=="function"){E=I(E,L);continue}N=I[0],O=I[1],N!==N?L[O]=E:typeof N=="string"?~(w=E.indexOf(N))&&(typeof I[2]=="number"?(L[O]=E.slice(0,w),E=E.slice(w+I[2])):(L[O]=E.slice(w),E=E.slice(0,w))):(w=N.exec(E))&&(L[O]=w[1],E=E.slice(0,w.index)),L[O]=L[O]||x&&I[3]&&S[O]||"",I[4]&&(L[O]=L[O].toLowerCase())}b&&(L.query=b(L.query)),x&&S.slashes&&L.pathname.charAt(0)!=="/"&&(L.pathname!==""||S.pathname!=="")&&(L.pathname=v(L.pathname,S.pathname)),L.pathname.charAt(0)!=="/"&&d(L.protocol)&&(L.pathname="/"+L.pathname),g(L.port,L.protocol)||(L.host=L.hostname,L.port=""),L.username=L.password="",L.auth&&(I=L.auth.split(":"),L.username=I[0]||"",L.password=I[1]||""),L.origin=L.protocol!=="file:"&&d(L.protocol)&&L.host?L.protocol+"//"+L.host:"null",L.href=L.toString()}function A(E,S,b){var x=this;switch(E){case"query":typeof S=="string"&&S.length&&(S=(b||r.parse)(S)),x[E]=S;break;case"port":x[E]=S,g(S,x.protocol)?S&&(x.host=x.hostname+":"+S):(x.host=x.hostname,x[E]="");break;case"hostname":x[E]=S,x.port&&(S+=":"+x.port),x.host=S;break;case"host":x[E]=S,/:\d+$/.test(S)?(S=S.split(":"),x.port=S.pop(),x.hostname=S.join(":")):(x.hostname=S,x.port="");break;case"protocol":x.protocol=S.toLowerCase(),x.slashes=!b;break;case"pathname":case"hash":if(S){var D=E==="pathname"?"/":"#";x[E]=S.charAt(0)!==D?D+S:S}else x[E]=S;break;default:x[E]=S}for(var N=0;N<h.length;N++){var I=h[N];I[4]&&(x[I[1]]=x[I[1]].toLowerCase())}return x.origin=x.protocol!=="file:"&&d(x.protocol)&&x.host?x.protocol+"//"+x.host:"null",x.href=x.toString(),x}function C(E){(!E||typeof E!="function")&&(E=r.stringify);var S,b=this,x=b.protocol;x&&x.charAt(x.length-1)!==":"&&(x+=":");var D=x+(b.slashes||d(b.protocol)?"//":"");return b.username&&(D+=b.username,b.password&&(D+=":"+b.password),D+="@"),D+=b.host+b.pathname,S=typeof b.query=="object"?E(b.query):b.query,S&&(D+=S.charAt(0)!=="?"?"?"+S:S),b.hash&&(D+=b.hash),D}_.prototype={set:A,toString:C},_.extractProtocol=f,_.location=m,_.trimLeft=u,_.qs=r,P.exports=_},5269:P=>{"use strict";P.exports=function(y){y.prototype[Symbol.iterator]=function*(){for(let o=this.head;o;o=o.next)yield o.value}}},9939:(P,y,o)=>{"use strict";P.exports=g,g.Node=c,g.create=g;function g(p){var a=this;if(a instanceof g||(a=new g),a.tail=null,a.head=null,a.length=0,p&&typeof p.forEach=="function")p.forEach(function(i){a.push(i)});else if(arguments.length>0)for(var u=0,h=arguments.length;u<h;u++)a.push(arguments[u]);return a}g.prototype.removeNode=function(p){if(p.list!==this)throw new Error("removing node which does not belong to this list");var a=p.next,u=p.prev;return a&&(a.prev=u),u&&(u.next=a),p===this.head&&(this.head=a),p===this.tail&&(this.tail=u),p.list.length--,p.next=null,p.prev=null,p.list=null,a},g.prototype.unshiftNode=function(p){if(p!==this.head){p.list&&p.list.removeNode(p);var a=this.head;p.list=this,p.next=a,a&&(a.prev=p),this.head=p,this.tail||(this.tail=p),this.length++}},g.prototype.pushNode=function(p){if(p!==this.tail){p.list&&p.list.removeNode(p);var a=this.tail;p.list=this,p.prev=a,a&&(a.next=p),this.tail=p,this.head||(this.head=p),this.length++}},g.prototype.push=function(){for(var p=0,a=arguments.length;p<a;p++)t(this,arguments[p]);return this.length},g.prototype.unshift=function(){for(var p=0,a=arguments.length;p<a;p++)l(this,arguments[p]);return this.length},g.prototype.pop=function(){if(!!this.tail){var p=this.tail.value;return this.tail=this.tail.prev,this.tail?this.tail.next=null:this.head=null,this.length--,p}},g.prototype.shift=function(){if(!!this.head){var p=this.head.value;return this.head=this.head.next,this.head?this.head.prev=null:this.tail=null,this.length--,p}},g.prototype.forEach=function(p,a){a=a||this;for(var u=this.head,h=0;u!==null;h++)p.call(a,u.value,h,this),u=u.next},g.prototype.forEachReverse=function(p,a){a=a||this;for(var u=this.tail,h=this.length-1;u!==null;h--)p.call(a,u.value,h,this),u=u.prev},g.prototype.get=function(p){for(var a=0,u=this.head;u!==null&&a<p;a++)u=u.next;if(a===p&&u!==null)return u.value},g.prototype.getReverse=function(p){for(var a=0,u=this.tail;u!==null&&a<p;a++)u=u.prev;if(a===p&&u!==null)return u.value},g.prototype.map=function(p,a){a=a||this;for(var u=new g,h=this.head;h!==null;)u.push(p.call(a,h.value,this)),h=h.next;return u},g.prototype.mapReverse=function(p,a){a=a||this;for(var u=new g,h=this.tail;h!==null;)u.push(p.call(a,h.value,this)),h=h.prev;return u},g.prototype.reduce=function(p,a){var u,h=this.head;if(arguments.length>1)u=a;else if(this.head)h=this.head.next,u=this.head.value;else throw new TypeError("Reduce of empty list with no initial value");for(var i=0;h!==null;i++)u=p(u,h.value,i),h=h.next;return u},g.prototype.reduceReverse=function(p,a){var u,h=this.tail;if(arguments.length>1)u=a;else if(this.tail)h=this.tail.prev,u=this.tail.value;else throw new TypeError("Reduce of empty list with no initial value");for(var i=this.length-1;h!==null;i--)u=p(u,h.value,i),h=h.prev;return u},g.prototype.toArray=function(){for(var p=new Array(this.length),a=0,u=this.head;u!==null;a++)p[a]=u.value,u=u.next;return p},g.prototype.toArrayReverse=function(){for(var p=new Array(this.length),a=0,u=this.tail;u!==null;a++)p[a]=u.value,u=u.prev;return p},g.prototype.slice=function(p,a){a=a||this.length,a<0&&(a+=this.length),p=p||0,p<0&&(p+=this.length);var u=new g;if(a<p||a<0)return u;p<0&&(p=0),a>this.length&&(a=this.length);for(var h=0,i=this.head;i!==null&&h<p;h++)i=i.next;for(;i!==null&&h<a;h++,i=i.next)u.push(i.value);return u},g.prototype.sliceReverse=function(p,a){a=a||this.length,a<0&&(a+=this.length),p=p||0,p<0&&(p+=this.length);var u=new g;if(a<p||a<0)return u;p<0&&(p=0),a>this.length&&(a=this.length);for(var h=this.length,i=this.tail;i!==null&&h>a;h--)i=i.prev;for(;i!==null&&h>p;h--,i=i.prev)u.push(i.value);return u},g.prototype.splice=function(p,a,...u){p>this.length&&(p=this.length-1),p<0&&(p=this.length+p);for(var h=0,i=this.head;i!==null&&h<p;h++)i=i.next;for(var m=[],h=0;i&&h<a;h++)m.push(i.value),i=this.removeNode(i);i===null&&(i=this.tail),i!==this.head&&i!==this.tail&&(i=i.prev);for(var h=0;h<u.length;h++)i=r(this,i,u[h]);return m},g.prototype.reverse=function(){for(var p=this.head,a=this.tail,u=p;u!==null;u=u.prev){var h=u.prev;u.prev=u.next,u.next=h}return this.head=a,this.tail=p,this};function r(p,a,u){var h=a===p.head?new c(u,null,a,p):new c(u,a,a.next,p);return h.next===null&&(p.tail=h),h.prev===null&&(p.head=h),p.length++,h}function t(p,a){p.tail=new c(a,p.tail,null,p),p.head||(p.head=p.tail),p.length++}function l(p,a){p.head=new c(a,null,p.head,p),p.tail||(p.tail=p.head),p.length++}function c(p,a,u,h){if(!(this instanceof c))return new c(p,a,u,h);this.list=h,this.value=p,a?(a.next=this,this.prev=a):this.prev=null,u?(u.prev=this,this.next=u):this.next=null}try{o(5269)(g)}catch(p){}}},Na={};function dn(P){var y=Na[P];if(y!==void 0)return y.exports;var o=Na[P]={id:P,loaded:!1,exports:{}};return tl[P].call(o.exports,o,o.exports,dn),o.loaded=!0,o.exports}(()=>{dn.n=P=>{var y=P&&P.__esModule?()=>P.default:()=>P;return dn.d(y,{a:y}),y}})(),(()=>{dn.d=(P,y)=>{for(var o in y)dn.o(y,o)&&!dn.o(P,o)&&Object.defineProperty(P,o,{enumerable:!0,get:y[o]})}})(),(()=>{dn.g=function(){if(typeof globalThis=="object")return globalThis;try{return this||new Function("return this")()}catch(P){if(typeof window=="object")return window}}()})(),(()=>{dn.o=(P,y)=>Object.prototype.hasOwnProperty.call(P,y)})(),(()=>{dn.nmd=P=>(P.paths=[],P.children||(P.children=[]),P)})();var oh={};(()=>{var qn;"use strict";var P=dn(3766),y=dn.n(P),o=dn(8242),g=dn(1207),r=dn.n(g),t=dn(6566),l=dn.n(t),c=dn(6997),p=dn(9984),a=dn(4582),u=dn(9121),h=dn(6690),i=dn(5866),m=dn.n(i),d=dn(6731),f=dn(6780),v=dn(374),_=dn(9900),A=dn(200);function C(ce){for(var J=[],ue=0;ue<ce.length;){var Ce=ce[ue];if(Ce==="*"||Ce==="+"||Ce==="?"){J.push({type:"MODIFIER",index:ue,value:ce[ue++]});continue}if(Ce==="\\"){J.push({type:"ESCAPED_CHAR",index:ue++,value:ce[ue++]});continue}if(Ce==="{"){J.push({type:"OPEN",index:ue,value:ce[ue++]});continue}if(Ce==="}"){J.push({type:"CLOSE",index:ue,value:ce[ue++]});continue}if(Ce===":"){for(var ie="",ye=ue+1;ye<ce.length;){var pe=ce.charCodeAt(ye);if(pe>=48&&pe<=57||pe>=65&&pe<=90||pe>=97&&pe<=122||pe===95){ie+=ce[ye++];continue}break}if(!ie)throw new TypeError("Missing parameter name at "+ue);J.push({type:"NAME",index:ue,value:ie}),ue=ye;continue}if(Ce==="("){var Ee=1,Oe="",ye=ue+1;if(ce[ye]==="?")throw new TypeError('Pattern cannot start with "?" at '+ye);for(;ye<ce.length;){if(ce[ye]==="\\"){Oe+=ce[ye++]+ce[ye++];continue}if(ce[ye]===")"){if(Ee--,Ee===0){ye++;break}}else if(ce[ye]==="("&&(Ee++,ce[ye+1]!=="?"))throw new TypeError("Capturing groups are not allowed at "+ye);Oe+=ce[ye++]}if(Ee)throw new TypeError("Unbalanced pattern at "+ue);if(!Oe)throw new TypeError("Missing pattern at "+ue);J.push({type:"PATTERN",index:ue,value:Oe}),ue=ye;continue}J.push({type:"CHAR",index:ue,value:ce[ue++]})}return J.push({type:"END",index:ue,value:""}),J}function E(ce,J){J===void 0&&(J={});for(var ue=C(ce),Ce=J.prefixes,ie=Ce===void 0?"./":Ce,ye="[^"+N(J.delimiter||"/#?")+"]+?",pe=[],Ee=0,Oe=0,Ue="",Ne=function($){if(Oe<ue.length&&ue[Oe].type===$)return ue[Oe++].value},Se=function($){var re=Ne($);if(re!==void 0)return re;var de=ue[Oe],Ae=de.type,be=de.index;throw new TypeError("Unexpected "+Ae+" at "+be+", expected "+$)},Fe=function(){for(var $="",re;re=Ne("CHAR")||Ne("ESCAPED_CHAR");)$+=re;return $};Oe<ue.length;){var Le=Ne("CHAR"),sn=Ne("NAME"),An=Ne("PATTERN");if(sn||An){var We=Le||"";ie.indexOf(We)===-1&&(Ue+=We,We=""),Ue&&(pe.push(Ue),Ue=""),pe.push({name:sn||Ee++,prefix:We,suffix:"",pattern:An||ye,modifier:Ne("MODIFIER")||""});continue}var kn=Le||Ne("ESCAPED_CHAR");if(kn){Ue+=kn;continue}Ue&&(pe.push(Ue),Ue="");var q=Ne("OPEN");if(q){var We=Fe(),H=Ne("NAME")||"",K=Ne("PATTERN")||"",ne=Fe();Se("CLOSE"),pe.push({name:H||(K?Ee++:""),pattern:H&&!K?ye:K,prefix:We,suffix:ne,modifier:Ne("MODIFIER")||""});continue}Se("END")}return pe}function S(ce,J){return b(E(ce,J),J)}function b(ce,J){J===void 0&&(J={});var ue=I(J),Ce=J.encode,ie=Ce===void 0?function(Oe){return Oe}:Ce,ye=J.validate,pe=ye===void 0?!0:ye,Ee=ce.map(function(Oe){if(typeof Oe=="object")return new RegExp("^(?:"+Oe.pattern+")$",ue)});return function(Oe){for(var Ue="",Ne=0;Ne<ce.length;Ne++){var Se=ce[Ne];if(typeof Se=="string"){Ue+=Se;continue}var Fe=Oe?Oe[Se.name]:void 0,Le=Se.modifier==="?"||Se.modifier==="*",sn=Se.modifier==="*"||Se.modifier==="+";if(Array.isArray(Fe)){if(!sn)throw new TypeError('Expected "'+Se.name+'" to not repeat, but got an array');if(Fe.length===0){if(Le)continue;throw new TypeError('Expected "'+Se.name+'" to not be empty')}for(var An=0;An<Fe.length;An++){var We=ie(Fe[An],Se);if(pe&&!Ee[Ne].test(We))throw new TypeError('Expected all "'+Se.name+'" to match "'+Se.pattern+'", but got "'+We+'"');Ue+=Se.prefix+We+Se.suffix}continue}if(typeof Fe=="string"||typeof Fe=="number"){var We=ie(String(Fe),Se);if(pe&&!Ee[Ne].test(We))throw new TypeError('Expected "'+Se.name+'" to match "'+Se.pattern+'", but got "'+We+'"');Ue+=Se.prefix+We+Se.suffix;continue}if(!Le){var kn=sn?"an array":"a string";throw new TypeError('Expected "'+Se.name+'" to be '+kn)}}return Ue}}function x(ce,J){var ue=[],Ce=L(ce,ue,J);return D(Ce,ue,J)}function D(ce,J,ue){ue===void 0&&(ue={});var Ce=ue.decode,ie=Ce===void 0?function(ye){return ye}:Ce;return function(ye){var pe=ce.exec(ye);if(!pe)return!1;for(var Ee=pe[0],Oe=pe.index,Ue=Object.create(null),Ne=function(Fe){if(pe[Fe]===void 0)return"continue";var Le=J[Fe-1];Le.modifier==="*"||Le.modifier==="+"?Ue[Le.name]=pe[Fe].split(Le.prefix+Le.suffix).map(function(sn){return ie(sn,Le)}):Ue[Le.name]=ie(pe[Fe],Le)},Se=1;Se<pe.length;Se++)Ne(Se);return{path:Ee,index:Oe,params:Ue}}}function N(ce){return ce.replace(/([.+*?=^!:${}()[\]|/\\])/g,"\\$1")}function I(ce){return ce&&ce.sensitive?"":"i"}function w(ce,J){if(!J)return ce;for(var ue=/\((?:\?<(.*?)>)?(?!\?)/g,Ce=0,ie=ue.exec(ce.source);ie;)J.push({name:ie[1]||Ce++,prefix:"",suffix:"",modifier:"",pattern:""}),ie=ue.exec(ce.source);return ce}function O(ce,J,ue){var Ce=ce.map(function(ie){return L(ie,J,ue).source});return new RegExp("(?:"+Ce.join("|")+")",I(ue))}function B(ce,J,ue){return U(E(ce,ue),J,ue)}function U(ce,J,ue){ue===void 0&&(ue={});for(var Ce=ue.strict,ie=Ce===void 0?!1:Ce,ye=ue.start,pe=ye===void 0?!0:ye,Ee=ue.end,Oe=Ee===void 0?!0:Ee,Ue=ue.encode,Ne=Ue===void 0?function($){return $}:Ue,Se="["+N(ue.endsWith||"")+"]|$",Fe="["+N(ue.delimiter||"/#?")+"]",Le=pe?"^":"",sn=0,An=ce;sn<An.length;sn++){var We=An[sn];if(typeof We=="string")Le+=N(Ne(We));else{var kn=N(Ne(We.prefix)),q=N(Ne(We.suffix));if(We.pattern)if(J&&J.push(We),kn||q)if(We.modifier==="+"||We.modifier==="*"){var H=We.modifier==="*"?"?":"";Le+="(?:"+kn+"((?:"+We.pattern+")(?:"+q+kn+"(?:"+We.pattern+"))*)"+q+")"+H}else Le+="(?:"+kn+"("+We.pattern+")"+q+")"+We.modifier;else Le+="("+We.pattern+")"+We.modifier;else Le+="(?:"+kn+q+")"+We.modifier}}if(Oe)ie||(Le+=Fe+"?"),Le+=ue.endsWith?"(?="+Se+")":"$";else{var K=ce[ce.length-1],ne=typeof K=="string"?Fe.indexOf(K[K.length-1])>-1:K===void 0;ie||(Le+="(?:"+Fe+"(?="+Se+"))?"),ne||(Le+="(?="+Fe+"|"+Se+")")}return new RegExp(Le,I(ue))}function L(ce,J,ue){return ce instanceof RegExp?w(ce,J):Array.isArray(ce)?O(ce,J,ue):B(ce,J,ue)}class F{hydrate(J,ue){const Ce=J,ie=new A(J),ye=[];return L(ie.pathname,ye),ye.forEach(pe=>{J=J.replace(":"+pe.name,encodeURIComponent(ue[pe.name]))}),J+=J.indexOf("?")===-1?"?":"&",Object.keys(ue).forEach(pe=>{Ce.indexOf(":"+pe)===-1&&(J+=pe+"="+encodeURIComponent(ue[pe])+"&")}),J.replace(/[?&]$/,"")}}function Y(){y()(".sample-request-send").off("click"),y()(".sample-request-send").on("click",function(ce){ce.preventDefault();const J=y()(this).parents("article"),ue=J.data("group"),Ce=J.data("name"),ie=J.data("version");te(ue,Ce,ie,y()(this).data("type"))}),y()(".sample-request-clear").off("click"),y()(".sample-request-clear").on("click",function(ce){ce.preventDefault();const J=y()(this).parents("article"),ue=J.data("group"),Ce=J.data("name"),ie=J.data("version");oe(ue,Ce,ie)})}function z(ce){return ce.replace(/{(.+?)}/g,":$1")}function G(ce,J){const ue=ce.find(".sample-request-url").val(),Ce=new F,ie=z(ue);return Ce.hydrate(ie,J)}function W(ce){const J={};["header","query","body"].forEach(Ce=>{const ie={};try{ce.find(y()(`[data-family="${Ce}"]:visible`)).each((ye,pe)=>{const Ee=pe.dataset.name;let Oe=pe.value;if(pe.type==="checkbox")if(pe.checked)Oe="on";else return!0;if(!Oe&&!pe.dataset.optional&&pe.type!=="checkbox")return y()(pe).addClass("border-danger"),!0;ie[Ee]=Oe})}catch(ye){return}J[Ce]=ie});const ue=ce.find(y()('[data-family="body-json"]'));return ue.is(":visible")?(J.body=ue.val(),J.header["Content-Type"]="application/json"):J.header["Content-Type"]="multipart/form-data",J}function te(ce,J,ue,Ce){const ie=y()(`article[data-group="${ce}"][data-name="${J}"][data-version="${ue}"]`),ye=W(ie),pe={};if(pe.url=G(ie,ye.query),pe.headers=ye.header,pe.headers["Content-Type"]==="application/json")pe.data=ye.body;else if(pe.headers["Content-Type"]==="multipart/form-data"){const Ue=new FormData;for(const[Ne,Se]of Object.entries(ye.body))Ue.append(Ne,Se);pe.data=Ue,pe.processData=!1,(Ce==="get"||Ce==="delete")&&delete pe.headers["Content-Type"]}pe.type=Ce,pe.success=Ee,pe.error=Oe,y().ajax(pe),ie.find(".sample-request-response").fadeTo(200,1),ie.find(".sample-request-response-json").html("Loading...");function Ee(Ue,Ne,Se){let Fe;try{Fe=JSON.parse(Se.responseText),Fe=JSON.stringify(Fe,null,4)}catch(Le){Fe=Se.responseText}ie.find(".sample-request-response-json").text(Fe),m().highlightAll()}function Oe(Ue,Ne,Se){let Fe="Error "+Ue.status+": "+Se,Le;try{Le=JSON.parse(Ue.responseText),Le=JSON.stringify(Le,null,4)}catch(sn){Le=Ue.responseText}Le&&(Fe+=`
`+Le),ie.find(".sample-request-response").is(":visible")&&ie.find(".sample-request-response").fadeTo(1,.1),ie.find(".sample-request-response").fadeTo(250,1),ie.find(".sample-request-response-json").text(Fe),m().highlightAll()}}function oe(ce,J,ue){const Ce=y()('article[data-group="'+ce+'"][data-name="'+J+'"][data-version="'+ue+'"]');Ce.find(".sample-request-response-json").html(""),Ce.find(".sample-request-response").hide(),Ce.find(".sample-request-input").each((ye,pe)=>{pe.value=pe.placeholder!==pe.dataset.name?pe.placeholder:""});const ie=Ce.find(".sample-request-url");ie.val(ie.prop("defaultValue"))}const Yn={ca:{"Allowed values:":"Valors permesos:","Compare all with predecessor":"Comparar tot amb versi\xF3 anterior","compare changes to:":"comparar canvis amb:","compared to":"comparat amb","Default value:":"Valor per defecte:",Description:"Descripci\xF3",Field:"Camp",General:"General","Generated with":"Generat amb",Name:"Nom","No response values.":"Sense valors en la resposta.",optional:"opcional",Parameter:"Par\xE0metre","Permission:":"Permisos:",Response:"Resposta",Send:"Enviar","Send a Sample Request":"Enviar una petici\xF3 d'exemple","show up to version:":"mostrar versi\xF3:","Size range:":"Tamany de rang:",Type:"Tipus",url:"url"},cs:{"Allowed values:":"Povolen\xE9 hodnoty:","Compare all with predecessor":"Porovnat v\u0161e s p\u0159edchoz\xEDmi verzemi","compare changes to:":"porovnat zm\u011Bny s:","compared to":"porovnat s","Default value:":"V\xFDchoz\xED hodnota:",Description:"Popis",Field:"Pole",General:"Obecn\xE9","Generated with":"Vygenerov\xE1no pomoc\xED",Name:"N\xE1zev","No response values.":"Nebyly vr\xE1ceny \u017E\xE1dn\xE9 hodnoty.",optional:"voliteln\xE9",Parameter:"Parametr","Permission:":"Opr\xE1vn\u011Bn\xED:",Response:"Odpov\u011B\u010F",Send:"Odeslat","Send a Sample Request":"Odeslat uk\xE1zkov\xFD po\u017Eadavek","show up to version:":"zobrazit po verzi:","Size range:":"Rozsah velikosti:",Type:"Typ",url:"url"},de:{"Allowed values:":"Erlaubte Werte:","Compare all with predecessor":"Vergleiche alle mit ihren Vorg\xE4ngern","compare changes to:":"vergleiche \xC4nderungen mit:","compared to":"verglichen mit","Default value:":"Standardwert:",Description:"Beschreibung",Field:"Feld",General:"Allgemein","Generated with":"Erstellt mit",Name:"Name","No response values.":"Keine R\xFCckgabewerte.",optional:"optional",Parameter:"Parameter","Permission:":"Berechtigung:",Response:"Antwort",Send:"Senden","Send a Sample Request":"Eine Beispielanfrage senden","show up to version:":"zeige bis zur Version:","Size range:":"Gr\xF6\xDFenbereich:",Type:"Typ",url:"url"},es:{"Allowed values:":"Valores permitidos:","Compare all with predecessor":"Comparar todo con versi\xF3n anterior","compare changes to:":"comparar cambios con:","compared to":"comparado con","Default value:":"Valor por defecto:",Description:"Descripci\xF3n",Field:"Campo",General:"General","Generated with":"Generado con",Name:"Nombre","No response values.":"Sin valores en la respuesta.",optional:"opcional",Parameter:"Par\xE1metro","Permission:":"Permisos:",Response:"Respuesta",Send:"Enviar","Send a Sample Request":"Enviar una petici\xF3n de ejemplo","show up to version:":"mostrar a versi\xF3n:","Size range:":"Tama\xF1o de rango:",Type:"Tipo",url:"url"},en:{},fr:{"Allowed values:":"Valeurs autoris\xE9es :",Body:"Corps","Compare all with predecessor":"Tout comparer avec ...","compare changes to:":"comparer les changements \xE0 :","compared to":"comparer \xE0","Default value:":"Valeur par d\xE9faut :",Description:"Description",Field:"Champ",General:"G\xE9n\xE9ral","Generated with":"G\xE9n\xE9r\xE9 avec",Header:"En-t\xEAte",Headers:"En-t\xEAtes",Name:"Nom","No response values.":"Aucune valeur de r\xE9ponse.","No value":"Aucune valeur",optional:"optionnel",Parameter:"Param\xE8tre",Parameters:"Param\xE8tres","Permission:":"Permission :","Query Parameter(s)":"Param\xE8tre(s) de la requ\xEAte","Query Parameters":"Param\xE8tres de la requ\xEAte","Request Body":"Corps de la requ\xEAte",required:"requis",Response:"R\xE9ponse",Send:"Envoyer","Send a Sample Request":"Envoyer une requ\xEAte repr\xE9sentative","show up to version:":"Montrer \xE0 partir de la version :","Size range:":"Ordre de grandeur :",Type:"Type",url:"url"},it:{"Allowed values:":"Valori permessi:","Compare all with predecessor":"Confronta tutto con versioni precedenti","compare changes to:":"confronta modifiche con:","compared to":"confrontato con","Default value:":"Valore predefinito:",Description:"Descrizione",Field:"Campo",General:"Generale","Generated with":"Creato con",Name:"Nome","No response values.":"Nessun valore di risposta.",optional:"opzionale",Parameter:"Parametro","Permission:":"Permessi:",Response:"Risposta",Send:"Invia","Send a Sample Request":"Invia una richiesta di esempio","show up to version:":"mostra alla versione:","Size range:":"Intervallo dimensione:",Type:"Tipo",url:"url"},nl:{"Allowed values:":"Toegestane waarden:","Compare all with predecessor":"Vergelijk alle met voorgaande versie","compare changes to:":"vergelijk veranderingen met:","compared to":"vergelijk met","Default value:":"Standaard waarde:",Description:"Omschrijving",Field:"Veld",General:"Algemeen","Generated with":"Gegenereerd met",Name:"Naam","No response values.":"Geen response waardes.",optional:"optioneel",Parameter:"Parameter","Permission:":"Permissie:",Response:"Antwoorden",Send:"Sturen","Send a Sample Request":"Stuur een sample aanvragen","show up to version:":"toon tot en met versie:","Size range:":"Maatbereik:",Type:"Type",url:"url"},pl:{"Allowed values:":"Dozwolone warto\u015Bci:","Compare all with predecessor":"Por\xF3wnaj z poprzednimi wersjami","compare changes to:":"por\xF3wnaj zmiany do:","compared to":"por\xF3wnaj do:","Default value:":"Warto\u015B\u0107 domy\u015Blna:",Description:"Opis",Field:"Pole",General:"Generalnie","Generated with":"Wygenerowano z",Name:"Nazwa","No response values.":"Brak odpowiedzi.",optional:"opcjonalny",Parameter:"Parametr","Permission:":"Uprawnienia:",Response:"Odpowied\u017A",Send:"Wy\u015Blij","Send a Sample Request":"Wy\u015Blij przyk\u0142adowe \u017C\u0105danie","show up to version:":"poka\u017C do wersji:","Size range:":"Zakres rozmiaru:",Type:"Typ",url:"url"},pt:{"Allowed values:":"Valores permitidos:","Compare all with predecessor":"Compare todos com antecessores","compare changes to:":"comparar altera\xE7\xF5es com:","compared to":"comparado com","Default value:":"Valor padr\xE3o:",Description:"Descri\xE7\xE3o",Field:"Campo",General:"Geral","Generated with":"Gerado com",Name:"Nome","No response values.":"Sem valores de resposta.",optional:"opcional",Parameter:"Par\xE2metro","Permission:":"Permiss\xE3o:",Response:"Resposta",Send:"Enviar","Send a Sample Request":"Enviar um Exemplo de Pedido","show up to version:":"aparecer para a vers\xE3o:","Size range:":"Faixa de tamanho:",Type:"Tipo",url:"url"},ro:{"Allowed values:":"Valori permise:","Compare all with predecessor":"Compar\u0103 toate cu versiunea precedent\u0103","compare changes to:":"compar\u0103 cu versiunea:","compared to":"comparat cu","Default value:":"Valoare implicit\u0103:",Description:"Descriere",Field:"C\xE2mp",General:"General","Generated with":"Generat cu",Name:"Nume","No response values.":"Nici o valoare returnat\u0103.",optional:"op\u021Bional",Parameter:"Parametru","Permission:":"Permisiune:",Response:"R\u0103spuns",Send:"Trimite","Send a Sample Request":"Trimite o cerere de prob\u0103","show up to version:":"arat\u0103 p\xE2n\u0103 la versiunea:","Size range:":"Interval permis:",Type:"Tip",url:"url"},ru:{"Allowed values:":"\u0414\u043E\u043F\u0443\u0441\u0442\u0438\u043C\u044B\u0435 \u0437\u043D\u0430\u0447\u0435\u043D\u0438\u044F:","Compare all with predecessor":"\u0421\u0440\u0430\u0432\u043D\u0438\u0442\u044C \u0441 \u043F\u0440\u0435\u0434\u044B\u0434\u0443\u0449\u0435\u0439 \u0432\u0435\u0440\u0441\u0438\u0435\u0439","compare changes to:":"\u0441\u0440\u0430\u0432\u043D\u0438\u0442\u044C \u0441:","compared to":"\u0432 \u0441\u0440\u0430\u0432\u043D\u0435\u043D\u0438\u0438 \u0441","Default value:":"\u041F\u043E \u0443\u043C\u043E\u043B\u0447\u0430\u043D\u0438\u044E:",Description:"\u041E\u043F\u0438\u0441\u0430\u043D\u0438\u0435",Field:"\u041D\u0430\u0437\u0432\u0430\u043D\u0438\u0435",General:"\u041E\u0431\u0449\u0430\u044F \u0438\u043D\u0444\u043E\u0440\u043C\u0430\u0446\u0438\u044F","Generated with":"\u0421\u0433\u0435\u043D\u0435\u0440\u0438\u0440\u043E\u0432\u0430\u043D\u043E \u0441 \u043F\u043E\u043C\u043E\u0449\u044C\u044E",Name:"\u041D\u0430\u0437\u0432\u0430\u043D\u0438\u0435","No response values.":"\u041D\u0435\u0442 \u0437\u043D\u0430\u0447\u0435\u043D\u0438\u0439 \u0434\u043B\u044F \u043E\u0442\u0432\u0435\u0442\u0430.",optional:"\u043D\u0435\u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u044B\u0439",Parameter:"\u041F\u0430\u0440\u0430\u043C\u0435\u0442\u0440","Permission:":"\u0420\u0430\u0437\u0440\u0435\u0448\u0435\u043D\u043E:",Response:"\u041E\u0442\u0432\u0435\u0442",Send:"\u041E\u0442\u043F\u0440\u0430\u0432\u0438\u0442\u044C","Send a Sample Request":"\u041E\u0442\u043F\u0440\u0430\u0432\u0438\u0442\u044C \u0442\u0435\u0441\u0442\u043E\u0432\u044B\u0439 \u0437\u0430\u043F\u0440\u043E\u0441","show up to version:":"\u043F\u043E\u043A\u0430\u0437\u0430\u0442\u044C \u0432\u0435\u0440\u0441\u0438\u044E:","Size range:":"\u041E\u0433\u0440\u0430\u043D\u0438\u0447\u0435\u043D\u0438\u044F:",Type:"\u0422\u0438\u043F",url:"URL"},tr:{"Allowed values:":"\u0130zin verilen de\u011Ferler:","Compare all with predecessor":"T\xFCm\xFCn\xFC \xF6ncekiler ile kar\u015F\u0131la\u015Ft\u0131r","compare changes to:":"de\u011Fi\u015Fiklikleri kar\u015F\u0131la\u015Ft\u0131r:","compared to":"kar\u015F\u0131la\u015Ft\u0131r","Default value:":"Varsay\u0131lan de\u011Fer:",Description:"A\xE7\u0131klama",Field:"Alan",General:"Genel","Generated with":"Olu\u015Fturan",Name:"\u0130sim","No response values.":"D\xF6n\xFC\u015F verisi yok.",optional:"opsiyonel",Parameter:"Parametre","Permission:":"\u0130zin:",Response:"D\xF6n\xFC\u015F",Send:"G\xF6nder","Send a Sample Request":"\xD6rnek istek g\xF6nder","show up to version:":"bu versiyona kadar g\xF6ster:","Size range:":"Boyut aral\u0131\u011F\u0131:",Type:"Tip",url:"url"},vi:{"Allowed values:":"Gi\xE1 tr\u1ECB ch\u1EA5p nh\u1EADn:","Compare all with predecessor":"So s\xE1nh v\u1EDBi t\u1EA5t c\u1EA3 phi\xEAn b\u1EA3n tr\u01B0\u1EDBc","compare changes to:":"so s\xE1nh s\u1EF1 thay \u0111\u1ED5i v\u1EDBi:","compared to":"so s\xE1nh v\u1EDBi","Default value:":"Gi\xE1 tr\u1ECB m\u1EB7c \u0111\u1ECBnh:",Description:"Ch\xFA th\xEDch",Field:"Tr\u01B0\u1EDDng d\u1EEF li\u1EC7u",General:"T\u1ED5ng quan","Generated with":"\u0110\u01B0\u1EE3c t\u1EA1o b\u1EDFi",Name:"T\xEAn","No response values.":"Kh\xF4ng c\xF3 k\u1EBFt qu\u1EA3 tr\u1EA3 v\u1EC1.",optional:"T\xF9y ch\u1ECDn",Parameter:"Tham s\u1ED1","Permission:":"Quy\u1EC1n h\u1EA1n:",Response:"K\u1EBFt qu\u1EA3",Send:"G\u1EEDi","Send a Sample Request":"G\u1EEDi m\u1ED9t y\xEAu c\u1EA7u m\u1EABu","show up to version:":"hi\u1EC3n th\u1ECB phi\xEAn b\u1EA3n:","Size range:":"K\xEDch c\u1EE1:",Type:"Ki\u1EC3u",url:"li\xEAn k\u1EBFt"},zh:{"Allowed values:":"\u5141\u8BB8\u503C:",Body:"\u8EAB\u4F53","Compare all with predecessor":"\u4E0E\u6240\u6709\u8F83\u65E9\u7684\u6BD4\u8F83","compare changes to:":"\u5C06\u5F53\u524D\u7248\u672C\u4E0E\u6307\u5B9A\u7248\u672C\u6BD4\u8F83:","compared to":"\u76F8\u6BD4\u4E8E","Default value:":"\u9ED8\u8BA4\u503C:",Description:"\u63CF\u8FF0",Field:"\u5B57\u6BB5",General:"\u6982\u8981","Generated with":"\u57FA\u4E8E",Name:"\u540D\u79F0","No response values.":"\u65E0\u8FD4\u56DE\u503C.",optional:"\u53EF\u9009",Parameter:"\u53C2\u6570",Parameters:"\u53C2\u6570",Headers:"\u5934\u90E8\u53C2\u6570","Permission:":"\u6743\u9650:",Response:"\u8FD4\u56DE",required:"\u5FC5\u9700\u7684",Send:"\u53D1\u9001","Send a Sample Request":"\u53D1\u9001\u793A\u4F8B\u8BF7\u6C42","show up to version:":"\u663E\u793A\u5230\u6307\u5B9A\u7248\u672C:","Size range:":"\u53D6\u503C\u8303\u56F4:",Type:"\u7C7B\u578B",url:"\u7F51\u5740"}},kt=((qn=window.navigator.language)!=null?qn:"en-GB").toLowerCase().substr(0,2);let bn=Yn[kt]?Yn[kt]:Yn.en;function Wn(ce){const J=bn[ce];return J===void 0?ce:J}function mt(ce){bn=Yn[ce]}const{defaultsDeep:On}=o,un=(ce,J)=>{const ue=(Ce,ie,ye,pe)=>({[ie]:ye+1<pe.length?Ce:J});return ce.reduceRight(ue,{})},gn=ce=>{let J={};return ce.forEach(ue=>{const Ce=un(ue[0].split("."),ue[1]);J=On(J,Ce)}),xt(J)};function xt(ce){return JSON.stringify(ce,null,4)}function er(ce){const J=[];return ce.forEach(ue=>{let Ce;switch(ue.type.toLowerCase()){case"string":Ce=ue.defaultValue||"";break;case"boolean":Ce=Boolean(ue.defaultValue)||!1;break;case"number":Ce=parseInt(ue.defaultValue||0,10);break;case"date":Ce=ue.defaultValue||new Date().toLocaleDateString(window.navigator.language);break}J.push([ue.field,Ce])}),gn(J)}var Qn=dn(1155);class hr extends Qn{constructor(J){super();this.testMode=J}diffMain(J,ue,Ce,ie){return super.diff_main(this._stripHtml(J),this._stripHtml(ue),Ce,ie)}diffPrettyHtml(J){const ue=[],Ce=/&/g,ie=/</g,ye=/>/g,pe=/\n/g;for(let Ee=0;Ee<J.length;Ee++){const Oe=J[Ee][0],Ne=J[Ee][1].replace(Ce,"&amp;").replace(ie,"&lt;").replace(ye,"&gt;").replace(pe,"&para;<br>");switch(Oe){case Qn.DIFF_INSERT:ue[Ee]="<ins>"+Ne+"</ins>";break;case Qn.DIFF_DELETE:ue[Ee]="<del>"+Ne+"</del>";break;case Qn.DIFF_EQUAL:ue[Ee]="<span>"+Ne+"</span>";break}}return ue.join("")}diffCleanupSemantic(J){return this.diff_cleanupSemantic(J)}_stripHtml(J){if(this.testMode)return J;const ue=document.createElement("div");return ue.innerHTML=J,ue.textContent||ue.innerText||""}}function tn(){l().registerHelper("markdown",function(ie){return ie&&(ie=ie.replace(/((\[(.*?)\])?\(#)((.+?):(.+?))(\))/mg,function(ye,pe,Ee,Oe,Ue,Ne,Se){const Fe=Oe||Ne+"/"+Se;return'<a href="#api-'+Ne+"-"+Se+'">'+Fe+"</a>"}),ie)}),l().registerHelper("setInputType",function(ie){switch(ie){case"File":case"Email":case"Color":case"Number":case"Date":return ie[0].toLowerCase()+ie.substring(1);case"Boolean":return"checkbox";default:return"text"}});let ce;l().registerHelper("startTimer",function(ie){return ce=new Date,""}),l().registerHelper("stopTimer",function(ie){return console.log(new Date-ce),""}),l().registerHelper("__",function(ie){return Wn(ie)}),l().registerHelper("cl",function(ie){return console.log(ie),""}),l().registerHelper("underscoreToSpace",function(ie){return ie.replace(/(_+)/g," ")}),l().registerHelper("removeDblQuotes",function(ie){return ie.replace(/"/g,"")}),l().registerHelper("assign",function(ie){if(arguments.length>0){const ye=typeof arguments[1];let pe=null;(ye==="string"||ye==="number"||ye==="boolean")&&(pe=arguments[1]),l().registerHelper(ie,function(){return pe})}return""}),l().registerHelper("nl2br",function(ie){return ue(ie)}),l().registerHelper("ifCond",function(ie,ye,pe,Ee){switch(ye){case"==":return ie==pe?Ee.fn(this):Ee.inverse(this);case"===":return ie===pe?Ee.fn(this):Ee.inverse(this);case"!=":return ie!=pe?Ee.fn(this):Ee.inverse(this);case"!==":return ie!==pe?Ee.fn(this):Ee.inverse(this);case"<":return ie<pe?Ee.fn(this):Ee.inverse(this);case"<=":return ie<=pe?Ee.fn(this):Ee.inverse(this);case">":return ie>pe?Ee.fn(this):Ee.inverse(this);case">=":return ie>=pe?Ee.fn(this):Ee.inverse(this);case"&&":return ie&&pe?Ee.fn(this):Ee.inverse(this);case"||":return ie||pe?Ee.fn(this):Ee.inverse(this);default:return Ee.inverse(this)}});const J={};l().registerHelper("subTemplate",function(ie,ye){J[ie]||(J[ie]=l().compile(document.getElementById("template-"+ie).innerHTML));const pe=J[ie],Ee=y().extend({},this,ye.hash);return new(l()).SafeString(pe(Ee))}),l().registerHelper("toLowerCase",function(ie){return ie&&typeof ie=="string"?ie.toLowerCase():""}),l().registerHelper("splitFill",function(ie,ye,pe){const Ee=ie.split(ye);return new Array(Ee.length).join(pe)+Ee[Ee.length-1]});function ue(ie){return(""+ie).replace(/(?:^|<\/pre>)[^]*?(?:<pre>|$)/g,ye=>ye.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br>$2"))}l().registerHelper("each_compare_list_field",function(ie,ye,pe){const Ee=pe.hash.field,Oe=[];ie&&ie.forEach(function(Ne){const Se=Ne;Se.key=Ne[Ee],Oe.push(Se)});const Ue=[];return ye&&ye.forEach(function(Ne){const Se=Ne;Se.key=Ne[Ee],Ue.push(Se)}),Ce("key",Oe,Ue,pe)}),l().registerHelper("each_compare_keys",function(ie,ye,pe){const Ee=[];ie&&Object.keys(ie).forEach(function(Ne){const Se={};Se.value=ie[Ne],Se.key=Ne,Ee.push(Se)});const Oe=[];return ye&&Object.keys(ye).forEach(function(Ne){const Se={};Se.value=ye[Ne],Se.key=Ne,Oe.push(Se)}),Ce("key",Ee,Oe,pe)}),l().registerHelper("body2json",function(ie,ye){return er(ie)}),l().registerHelper("each_compare_field",function(ie,ye,pe){return Ce("field",ie,ye,pe)}),l().registerHelper("each_compare_title",function(ie,ye,pe){return Ce("title",ie,ye,pe)}),l().registerHelper("reformat",function(ie,ye){if(ye==="json")try{return JSON.stringify(JSON.parse(ie.trim()),null,"    ")}catch(pe){}return ie}),l().registerHelper("showDiff",function(ie,ye,pe){let Ee="";if(ie===ye)Ee=ie;else{if(!ie)return ye;if(!ye)return ie;const Oe=new hr,Ue=Oe.diffMain(ye,ie);Oe.diffCleanupSemantic(Ue),Ee=Oe.diffPrettyHtml(Ue),Ee=Ee.replace(/&para;/gm,"")}return pe==="nl2br"&&(Ee=ue(Ee)),Ee});function Ce(ie,ye,pe,Ee){const Oe=[];let Ue=0;ye&&ye.forEach(function(Fe){let Le=!1;if(pe&&pe.forEach(function(sn){if(Fe[ie]===sn[ie]){const An={typeSame:!0,source:Fe,compare:sn,index:Ue};Oe.push(An),Le=!0,Ue++}}),!Le){const sn={typeIns:!0,source:Fe,index:Ue};Oe.push(sn),Ue++}}),pe&&pe.forEach(function(Fe){let Le=!1;if(ye&&ye.forEach(function(sn){sn[ie]===Fe[ie]&&(Le=!0)}),!Le){const sn={typeDel:!0,compare:Fe,index:Ue};Oe.push(sn),Ue++}});let Ne="";const Se=Oe.length;for(const Fe in Oe)parseInt(Fe,10)===Se-1&&(Oe[Fe]._last=!0),Ne=Ne+Ee.fn(Oe[Fe]);return Ne}}document.addEventListener("DOMContentLoaded",()=>{et(),Y(),m().highlightAll()});function et(){let ce=[{type:"post",url:"BasicAuth",title:"Authorization User (Basic)",version:"0.1.0",name:"BasicAuth",group:"App",permission:[{name:"Basic Auth"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:"<p>Accept-Encoding: gzip, deflate</p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},filename:"ApiDocData.php",groupTitle:"App"},{type:"get, post",url:"/health-check",title:"Get health check",version:"0.1.0",name:"HealthCheck_Sales",group:"App",permission:[{name:"Authorized User"}],description:"<p>If username is empty in config file then HttpBasicAuth is disabled.</p>",header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"Array",optional:!1,field:"data",description:"<p>components health check passed statuses (&quot;true&quot; or &quot;false&quot;)</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
    "mysql": true,
    "postgresql": true,
    "redis": true
}`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"ServiceUnavailable",description:"<p>HTTP 503</p>"}]},examples:[{title:"Error-Response:",content:` HTTP/1.1 503 Service Unavailable
 {
     "mysql": true,
     "postgresql": false,
     "redis": true
 }


@return array | string
@throws \\Throwable`,type:"json"}]},filename:"HealthController.php",groupTitle:"App"},{type:"get, post",url:"/health-check/metrics",title:"Get health check metrics text",version:"0.1.0",name:"HealthCheck_Sales_Metrics",group:"App",permission:[{name:"Authorized User"}],description:"<p>If username is empty in config file then HttpBasicAuth is disabled.</p>",header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"string",optional:!1,field:"metrics",description:"<p>in plain text format containing components health statuses (&quot;1&quot; for OK, &quot;0&quot; for failed)</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
healthcheck_status{name="mysql"} 1
healthcheck_status{name="postgresql"} 1
healthcheck_status{name="redis"} 1`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"ServiceUnavailable",description:"<p>HTTP 503</p>"}]},examples:[{title:"Error-Response:",content:` HTTP/1.1 503 Service Unavailable
 healthcheck_status{name="mysql"} 1
 healthcheck_status{name="postgresql"} 0
 healthcheck_status{name="redis"} 1


@return string
@throws \\Throwable`,type:"json"}]},filename:"HealthController.php",groupTitle:"App"},{type:"get, post",url:"/v1/app/test",title:"API Test action",version:"0.1.0",name:"TestApp",group:"App",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:` HTTP/1.1 200 OK
 {
     "message": "Server Name: api.host.test",
     "code": 0,
     "date": "2018-05-30",
     "time": "16:01:17",
     "ip": "127.0.0.1",
     "get": [],
     "post": [],
     "files": [],
     "headers": {
         "Accept-Language": "ru,en-US;q=0.9,en;q=0.8,zh;q=0.7,zh-TW;q=0.6,zh-CN;q=0.5,ko;q=0.4,de;q=0.3",
         "Accept-Encoding": "gzip, deflate",
         "Dnt": "1",
         "Accept": "*\\/*",
         "Postman-Token": "6ce239ad-5e05-cc88-13d1-ba2ff5538720",
         "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViYQ==",
         "Cache-Control": "no-cache",
         "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36",
         "Connection": "keep-alive",
         "Host": "api.bookair.zeit.test"
     }
 }


@return array`,type:"json"}]},filename:"v1/controllers/AppController.php",groupTitle:"App"},{type:"get",url:"/v2/case-category/list",title:"Get CaseCategory",version:"0.2.0",name:"CaseCategoryList",group:"CaseCategory",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "case-category": [
             {
                 "cc_id": 1,
                 "cc_key": "add_infant",
                 "cc_name": "Add infant",
                 "cc_dep_id": 3,
                 "cc_updated_dt": null
             },
             {
                 "cc_id": 2,
                 "cc_key": "insurance_add_remove",
                 "cc_name": "Insurance Add/Remove",
                 "cc_dep_id": 3,
                 "cc_updated_dt": "2019-09-26 15:14:01"
             }
         ]
     },
     "technical": {
         "action": "v2/case-category/list",
         "response_id": 11926631,
         "request_dt": "2020-03-16 11:26:34",
         "response_dt": "2020-03-16 11:26:34",
         "execution_time": 0.076,
         "memory_usage": 506728
     },
     "request": []
 }`,type:"json"},{title:"Not Modified-Response (304):",content:`
HTTP/1.1 304 Not Modified
Cache-Control: public, max-age=3600
Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT`,type:"json"}]},error:{examples:[{title:"Error-Response (405):",content:`
HTTP/1.1 405 Method Not Allowed
  {
      "name": "Method Not Allowed",
      "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
      "code": 0,
      "status": 405,
      "type": "yii\\\\web\\\\MethodNotAllowedHttpException"
  }`,type:"json"}]},filename:"v2/controllers/CaseCategoryController.php",groupTitle:"CaseCategory"},{type:"post",url:"/v2/cases/create",title:"Create Case",version:"0.2.0",name:"CreateCase",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"160",optional:!1,field:"contact_email",description:"<p>Client Email required if contact phone or chat_visitor_id or order_uid are not set</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"contact_phone",description:"<p>Client Phone required if contact email or chat_visitor_id or order_uid are not set</p>"},{group:"Parameter",type:"string",size:"20",optional:!0,field:"contact_name",description:"<p>Client Name</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"chat_visitor_id",description:"<p>Client chat_visitor_id required if contact phone or email or order_uid are not set</p>"},{group:"Parameter",type:"int",optional:!0,field:"category_id",description:"<p>Case category id (Required if &quot;category_key&quot; is empty)</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"category_key",description:"<p>Case category key (Required if &quot;category_id&quot; is empty - takes precedence over &quot;category_id&quot;. See list in api &quot;/v2/case-category/list&quot;)</p>"},{group:"Parameter",type:"string",size:"5..7",optional:!1,field:"order_uid",description:"<p>Order uid (symbols and numbers only) required if contact phone or email or chat_visitor_id are not set</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"project_key",description:"<p>Project Key (if not exist project assign API User)</p>"},{group:"Parameter",type:"string",size:"255",optional:!0,field:"subject",description:"<p>Subject</p>"},{group:"Parameter",type:"string",size:"65000",optional:!0,field:"description",description:"<p>Description</p>"},{group:"Parameter",type:"array[]",optional:!0,field:"order_info",description:"<p>Order Info (key =&gt; value, key: string, value: string)</p>"}]},examples:[{title:"Request-Example:",content:`{
      "contact_email": "test@test.com",
      "contact_phone": "+37369636690",
      "category_key": "voluntary_exchange",
      "category_id": null,
      "order_uid": "12WS09W",
      "subject": "Subject text",
      "description": "Description text",
      "project_key": "project_key",
      "order_info": {
          "Departure Date":"2020-03-07",
          "Departure Airport":"LON"
      }
  }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
      "status": 200,
      "message": "OK",
      "data": {
          "case_id": 2354356,
          "case_gid": "708ddf3e44ec477f8807d8b5f748bb6c",
          "client_uuid": "5d0cd25a-7f22-4b18-9547-e19a3e7d0c9a"
      },
      "technical": {
          "action": "v2/cases/create",
          "response_id": 11934216,
          "request_dt": "2020-03-17 08:31:30",
          "response_dt": "2020-03-17 08:31:30",
          "execution_time": 0.156,
          "memory_usage": 979248
      },
      "request": {
          "contact_email": "test@test.com",
          "contact_phone": "+37369636690",
          "category_id": 12,
          "order_uid": "12WS09W",
          "subject": "Subject text",
          "description": "Description text",
          "project_key": "project_key",
          "order_info": {
              "Departure Date": "2020-03-07",
              "Departure Airport": "LON"
          }
      }
  }`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          "contact_email": [
              "Contact Email cannot be blank."
          ],
          "contact_phone": [
              "The format of Contact Phone is invalid."
          ],
          "order_uid": [
              "Order Uid should contain at most 7 characters."
          ]
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Saving error",
      "errors": [
          "Saving error"
      ],
      "code": 21101,
      "technical": {
          ...
      },
      "request": {
          ...
      }
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found Case data on POST request"
      ],
      "code": 21300,
      "technical": {
          ...
      },
      "request": {
          ...
      }
}`,type:"json"}]},filename:"v2/controllers/CasesController.php",groupTitle:"Cases"},{type:"get",url:"/v2/case/find-list-by-email",title:"Get Cases GID list by Email",version:"0.2.0",name:"findCasesListByEmail",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"320",optional:!1,field:"contact_email",description:"<p>Client Email required</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!1,field:"active_only",description:"<p>1 for requesting active cases only (depends on Department-&gt;object-&gt;case-&gt;trashActiveDaysLimit or global trash_cases_active_days_limit Site setting), 0 for all cases</p>"},{group:"Parameter",type:"string",optional:!0,field:"department_key",description:"<p>Department key</p>"},{group:"Parameter",type:"string",optional:!0,field:"project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"int",optional:!0,field:"results_limit",description:"<p>Limits number of cases in results list</p>"}]},examples:[{title:"Request-Example:",content:`{
      "contact_email": "test@test.test",
      "active_only": 0,
      "department_key": "support",
      "project_key": "ovago",
      "results_limit": 10
  }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "data": [
            "24f12d06267aaa8e8ff86c5059efdf86",
            "20e1c76c70f86063ded79b6d389f490d",
            "c5f3f405ea489bd6e6a1f3886086c9d9",
    ],
    "technical": {
        "action": "v2/case/find-list-by-email",
        "response_id": 753,
        "request_dt": "2021-09-02 13:52:53",
        "response_dt": "2021-09-02 13:52:53",
        "execution_time": 0.029,
        "memory_usage": 568056
    },
    "request": []
}`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "contact_email": [
            "Contact Email is not a valid email address."
        ]
    },
    "code": "21303",
    "technical": {
        "action": "v2/case/find-list-by-email",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Validation error",
      "errors": {
          "contact_email": [
              "Client Email not found in DB."
          ]
      },
      "code": 21303,
      "technical": {
          ...
      },
      "request": []
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found GET request params"
      ],
      "code": 21302,
      "technical": {
          ...
      },
      "request":  []
}

@return \\webapi\\src\\response\\Response
@throws \\Throwable`,type:"json"}]},filename:"v2/controllers/CaseController.php",groupTitle:"Cases"},{type:"get",url:"/v2/case/find-list-by-phone",title:"Get Cases GID list by Phone",version:"0.2.0",name:"findCasesListByPhone",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"20",optional:!1,field:"contact_phone",description:"<p>Client Phone required</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!1,field:"active_only",description:"<p>1 for requesting active cases only (depends on Department-&gt;object-&gt;case-&gt;trashActiveDaysLimit or global trash_cases_active_days_limit Site setting), 0 for all cases</p>"},{group:"Parameter",type:"string",optional:!0,field:"department_key",description:"<p>Department key</p>"},{group:"Parameter",type:"string",optional:!0,field:"project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"int",optional:!0,field:"results_limit",description:"<p>Limits number of cases in results list</p>"}]},examples:[{title:"Request-Example:",content:`{
      "contact_phone": "+18888888888",
      "active_only": 1,
      "department_key": "support",
      "project_key": "ovago",
      "results_limit": 10
  }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "data": [
            "24f12d06267aaa8e8ff86c5059efdf86",
            "20e1c76c70f86063ded79b6d389f490d",
            "c5f3f405ea489bd6e6a1f3886086c9d9",
    ],
    "technical": {
        "action": "v2/case/find-list-by-phone",
        "response_id": 753,
        "request_dt": "2021-09-02 13:52:53",
        "response_dt": "2021-09-02 13:52:53",
        "execution_time": 0.029,
        "memory_usage": 568056
    },
    "request": []
}`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "contact_phone": [
            "The format of Contact Phone is invalid."
        ]
    },
    "code": "21303",
    "technical": {
        "action": "v2/case/find-list-by-phone",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Validation error",
      "errors": {
          "contact_phone": [
              "Client Phone number not found in DB."
          ]
      },
      "code": 21303,
      "technical": {
          ...
      },
      "request": []
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found  GET request params"
      ],
      "code": 21302,
      "technical": {
          ...
      },
      "request":  []
}

@return \\webapi\\src\\response\\Response
@throws \\Throwable`,type:"json"}]},filename:"v2/controllers/CaseController.php",groupTitle:"Cases"},{type:"get",url:"/v2/case/get",title:"Get Case",version:"0.2.0",name:"getCaseDataByCaseGid",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"50",optional:!1,field:"gid",description:"<p>Case GID required</p>"}]},examples:[{title:"Request-Example:",content:`{
      "gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "data": {
                "id": "88473",
                "gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
                "created_dt": "2020-02-26 15:26:25",
                "updated_dt": "2020-02-26 17:07:18",
                "last_action_dt": "2020-02-27 15:08:39",
                "category_id": "16",
                "order_uid": "P6QWNH",
                "project_name": "ARANGRANT",
                "next_flight": "2022-05-22",
                "status_name": "Processing"
    },
    "technical": {
        "action": "v2/case/get",
        "response_id": 753,
        "request_dt": "2021-09-02 13:52:53",
        "response_dt": "2021-09-02 13:52:53",
        "execution_time": 0.029,
        "memory_usage": 568056
    },
    "request": []
}`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": [
            "Case with this gid not found."
    ],
    "code": "21304",
    "technical": {
        "action": "v2/case/get",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "gid": [
            "Case Gid should contain at most 50 characters."
        ]
    },
    "code": "21303",
    "technical": {
        "action": "v2/case/get",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found  GET request params"
      ],
      "code": 21302,
      "technical": {
          ...
      },
      "request":  []
}

@return \\webapi\\src\\response\\Response
@throws \\Throwable`,type:"json"}]},filename:"v2/controllers/CaseController.php",groupTitle:"Cases"},{type:"get",url:"/v2/case/get-list-by-email",title:"Get Cases by Email",version:"0.2.0",name:"getCasesListByEmail",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"320",optional:!1,field:"contact_email",description:"<p>Client Email required</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!1,field:"active_only",description:"<p>1 for requesting active cases only (depends on Department-&gt;object-&gt;case-&gt;trashActiveDaysLimit or global trash_cases_active_days_limit Site setting), 0 for all cases</p>"},{group:"Parameter",type:"string",optional:!0,field:"department_key",description:"<p>Department key</p>"},{group:"Parameter",type:"string",optional:!0,field:"project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"int",optional:!0,field:"results_limit",description:"<p>Limits number of cases in results list</p>"}]},examples:[{title:"Request-Example:",content:`{
      "contact_email": "test@test.test",
      "active_only": 1,
      "department_key": "support",
      "project_key": "ovago",
      "results_limit": 10
  }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "data": [
            {
                "id": "88473",
                "gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
                "created_dt": "2020-02-26 15:26:25",
                "updated_dt": "2020-02-26 17:07:18",
                "last_action_dt": "2020-02-27 15:08:39",
                "category_id": "16",
                "order_uid": "P6QWNH",
                "project_name": "OVAGO",
                "next_flight": "2022-05-22",
                "status_name": "Processing"
            },
            {
                "id": "130705",
                "gid": "37129b222479f0468d6355fcf4bd0235",
                "created_dt": "2020-03-24 09:14:28",
                "updated_dt": "2020-03-24 11:00:34",
                "last_action_dt": "2020-03-24 11:00:34",
                "category_id": "16",
                "order_uid": null,
                "project_name": "OVAGO",
                "next_flight": null,
                "status_name": "Processing"
            }
    ],
    "technical": {
        "action": "v2/case/get-list-by-email",
        "response_id": 753,
        "request_dt": "2021-09-02 13:52:53",
        "response_dt": "2021-09-02 13:52:53",
        "execution_time": 0.029,
        "memory_usage": 568056
    },
    "request": []
}`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "contact_email": [
            "Contact Email is not a valid email address."
        ]
    },
    "code": "21303",
    "technical": {
        "action": "v2/case/get-list-by-email",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Validation error",
      "errors": {
          "contact_email": [
              "Client Email not found in DB."
          ]
      },
      "code": 21303,
      "technical": {
          ...
      },
      "request": []
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found  GET request params"
      ],
      "code": 21302,
      "technical": {
          ...
      },
      "request":  []
}

@return \\webapi\\src\\response\\Response
@throws \\Throwable`,type:"json"}]},filename:"v2/controllers/CaseController.php",groupTitle:"Cases"},{type:"get",url:"/v2/case/get-list-by-phone",title:"Get Cases by Phone",version:"0.2.0",name:"getCasesListByPhone",group:"Cases",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"20",optional:!1,field:"contact_phone",description:"<p>Client Phone required</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!1,field:"active_only",description:"<p>1 for requesting active cases only (depends on Department-&gt;object-&gt;case-&gt;trashActiveDaysLimit or global trash_cases_active_days_limit Site setting), 0 for all cases</p>"},{group:"Parameter",type:"string",optional:!0,field:"department_key",description:"<p>Department key</p>"},{group:"Parameter",type:"string",optional:!0,field:"project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"int",optional:!0,field:"results_limit",description:"<p>Limits number of cases in results list</p>"}]},examples:[{title:"Request-Example:",content:`{
      "contact_phone": "+18888888888",
      "active_only": 0,
      "department_key": "support",
      "project_key": "ovago",
      "results_limit": 10
  }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "data": [
            {
                "id": "88473",
                "gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
                "created_dt": "2020-02-26 15:26:25",
                "updated_dt": "2020-02-26 17:07:18",
                "last_action_dt": "2020-02-27 15:08:39",
                "category_id": "16",
                "order_uid": "P6QWNH",
                "project_name": "OVAGO",
                "next_flight": "2022-05-22",
                "status_name": "Processing"
            },
            {
                "id": "130705",
                "gid": "37129b222479f0468d6355fcf4bd0235",
                "created_dt": "2020-03-24 09:14:28",
                "updated_dt": "2020-03-24 11:00:34",
                "last_action_dt": "2020-03-24 11:00:34",
                "category_id": "16",
                "order_uid": null,
                "project_name": "OVAGO",
                "next_flight": null,
                "status_name": "Processing"
            }
    ],
    "technical": {
        "action": "v2/case/get-list-by-phone",
        "response_id": 753,
        "request_dt": "2021-09-02 13:52:53",
        "response_dt": "2021-09-02 13:52:53",
        "execution_time": 0.029,
        "memory_usage": 568056
    },
    "request": []
}`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "contact_phone": [
            "The format of Contact Phone is invalid."
        ]
    },
    "code": "21303",
    "technical": {
        "action": "v2/case/get-list-by-phone",
        "response_id": 754,
        "request_dt": "2021-09-02 14:01:22",
        "response_dt": "2021-09-02 14:01:22",
        "execution_time": 0.028,
        "memory_usage": 306800
    },
    "request": []
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Validation error",
      "errors": {
          "contact_phone": [
              "Client Phone number not found in DB."
          ]
      },
      "code": 21303,
      "technical": {
          ...
      },
      "request": []
}`,type:"json"},{title:"Error-Response(Load data error) (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found  GET request params"
      ],
      "code": 21302,
      "technical": {
          ...
      },
      "request":  []
}

@return \\webapi\\src\\response\\Response
@throws \\Throwable`,type:"json"}]},filename:"v2/controllers/CaseController.php",groupTitle:"Cases"},{type:"post",url:"/v2/client-account/create",title:"Create Client Account",version:"0.2.0",name:"CreateClientAccount",group:"ClientAccount",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"36",optional:!0,field:"uuid",description:"<p>Client Uuid</p>"},{group:"Parameter",type:"int",optional:!0,field:"hid",description:"<p>Origin Id</p>"},{group:"Parameter",type:"string",size:"100",optional:!1,field:"username",description:"<p>Username</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"nationality_country_code",description:"<p>Nationality country code</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD",optional:!0,field:"dob",description:"<p>Dob</p>"},{group:"Parameter",type:"int",size:"1..2",optional:!0,field:"gender",description:"<p>Gender</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"phone",description:"<p>Phone</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!0,field:"subscription",description:"<p>Subscription</p>"},{group:"Parameter",type:"string",size:"5",optional:!0,field:"language_id",description:"<p>Language</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"currency_code",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"timezone",description:"<p>Timezone</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"created_ip",description:"<p>Created ip</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!0,field:"enabled",description:"<p>Enabled</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!0,field:"origin_created_dt",description:"<p>Origin Created dt</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!0,field:"origin_updated_dt",description:"<p>Origin Updated dt</p>"}]},examples:[{title:"Request-Example:",content:`{
         "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
         "hid": 2,
         "username": "example",
         "first_name": "",
         "middle_name": "",
         "last_name": "",
         "nationality_country_code": "",
         "dob": "2001-09-09",
         "gender": 1,
         "phone": "",
         "subscription": 1,
         "language_id": "en-PI",
         "currency_code": "EUR",
         "timezone": "",
         "created_ip": "127.0.0.1",
         "enabled": 1,
         "origin_created_dt": "2020-11-19 10:45:17",
         "origin_updated_dt": "2020-11-04 05:25:18"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
      "status": 200,
      "message": "OK",
      "data": {
         "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
      },
      "technical": {
          "action": "/v2/client-account/create",
          "response_id": 11934216,
          "request_dt": "2020-03-17 08:31:30",
          "response_dt": "2020-03-17 08:31:30",
          "execution_time": 0.156,
          "memory_usage": 979248
      },
      "request": {
          "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
      }
  }`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          ...
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"}]},filename:"v2/controllers/ClientAccountController.php",groupTitle:"ClientAccount"},{type:"post",url:"/v2/client-account/get",title:"Get Client Account",version:"0.2.0",name:"GetClientAccount",group:"ClientAccount",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"36",optional:!1,field:"uuid",description:"<p>Client Uuid</p>"}]},examples:[{title:"Request-Example:",content:`{
     "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
      "status": 200,
      "message": "OK",
      "data": {
         "ca_uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
         "ca_hid": 2,
         "ca_username": "example",
         "ca_first_name": "",
         "ca_middle_name": "",
         "ca_last_name": "",
         "ca_nationality_country_code": "",
         "ca_dob": "2001-09-09",
         "ca_gender": 1,
         "ca_phone": "",
         "ca_subscription": 1,
         "ca_language_id": "en-PI",
         "ca_currency_code": "EUR",
         "ca_timezone": "",
         "ca_created_ip": "",
         "ca_enabled": 1,
         "ca_origin_created_dt": "2020-11-19 10:45:17",
         "ca_origin_updated_dt": "2020-11-04 05:25:18"
      },
      "technical": {
          "action": "/v2/client-account/get",
          "response_id": 11934216,
          "request_dt": "2020-03-17 08:31:30",
          "response_dt": "2020-03-17 08:31:30",
          "execution_time": 0.156,
          "memory_usage": 979248
      },
      "request": {
          "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
      }
  }`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          ...
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"}]},filename:"v2/controllers/ClientAccountController.php",groupTitle:"ClientAccount"},{type:"post",url:"/v2/client-account/update",title:"Update Client Account",version:"0.2.0",name:"UpdateClientAccount",group:"ClientAccount",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"36",optional:!1,field:"uuid",description:"<p>Client Uuid</p>"},{group:"Parameter",type:"int",optional:!0,field:"hid",description:"<p>Origin Id</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"username",description:"<p>Username</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"nationality_country_code",description:"<p>Nationality country code</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD",optional:!0,field:"dob",description:"<p>Dob</p>"},{group:"Parameter",type:"int",size:"1..2",optional:!0,field:"gender",description:"<p>Gender</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"phone",description:"<p>Phone</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!0,field:"subscription",description:"<p>Subscription</p>"},{group:"Parameter",type:"string",size:"5",optional:!0,field:"language_id",description:"<p>Language</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"currency_code",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"timezone",description:"<p>Timezone</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"created_ip",description:"<p>Created ip</p>"},{group:"Parameter",type:"int",size:"0..1",optional:!0,field:"enabled",description:"<p>Enabled</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!0,field:"origin_created_dt",description:"<p>Origin Created dt</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!0,field:"origin_updated_dt",description:"<p>Origin Updated dt</p>"}]},examples:[{title:"Request-Example:",content:`{
         "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
         "hid": 2,
         "username": "example",
         "first_name": "",
         "middle_name": "",
         "last_name": "",
         "nationality_country_code": "",
         "dob": "2001-09-09",
         "gender": 1,
         "phone": "",
         "subscription": 1,
         "language_id": "en-PI",
         "currency_code": "EUR",
         "timezone": "",
         "created_ip": "127.0.0.1",
         "enabled": 1,
         "origin_created_dt": "2020-11-19 10:45:17",
         "origin_updated_dt": "2020-11-04 05:25:18"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
      "status": 200,
      "message": "OK",
      "data": {
         "ClientAccount updated successfully": 123
      },
      "technical": {
          "action": "/v2/client-account/update",
          "response_id": 11934216,
          "request_dt": "2020-03-17 08:31:30",
          "response_dt": "2020-03-17 08:31:30",
          "execution_time": 0.156,
          "memory_usage": 979248
      },
      "request": {
          "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
      }
  }`,type:"json"}]},error:{examples:[{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          ...
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"}]},filename:"v2/controllers/ClientAccountController.php",groupTitle:"ClientAccount"},{type:"post",url:"/v1/client-chat-request/feedback",title:"Client Chat Feedback",version:"0.1.0",name:"ClientChatFeedback",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{examples:[{title:"Request-Example LEAVE_FEEDBACK:",content:`{
     "event": "LEAVE_FEEDBACK",
     "data": {
         "rid": "20a20989-4d26-42f4-9a1c-2948ce4c4d56",
         "comment": "Hello, this is my feedback",
         "rating": 4,
         "visitor": {
             "id": "1c1d90ff-5489-45f5-b19b-2181a65ce898",
             "project": "ovago"
         }
     }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
 "status":400,
 "message":"Some errors occurred while creating client chat request",
 "code":"13104",
 "errors":["Event is invalid."]
}`,type:"json"}]},filename:"v1/controllers/ClientChatRequestController.php",groupTitle:"ClientChat"},{type:"get",url:"/v1/client-chat-request/chat-form",title:"Client Chat Form",version:"0.1.0",name:"ClientChatForm",group:"ClientChat",permission:[{name:"Authorized User"}],parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"100",optional:!1,field:"form_key",description:"<p>Form Key</p>"},{group:"Parameter",type:"string",size:"5",optional:!1,field:"language_id",description:"<p>Language ID (en-US)</p>"},{group:"Parameter",type:"int",allowedValues:["0","1"],optional:!0,field:"cache",description:"<p>Cache (not required, default eq 1)</p>"}]},examples:[{title:"Request-Example:",content:`{
    "form_key": "example_form",
    "language_id": "ru-RU",
    "cache": 1
}`,type:"get"}]},header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
"status": 200,
"message": "OK",
"data": {
        "data_form": [
            {
                "type": "textarea",
                "name": "example_name",
                "className": "form-control",
                "label": "Please, describe problem",
                "required": true,
                "rows": 5
            },
            {
                "type": "select",
                "name": "destination",
                "className": "form-control",
                "label": "\u041A\u0443\u0434\u0430 \u043B\u0435\u0442\u0438\u043C?",
                "values": [
                    "label": "\u0410\u043C\u0441\u0442\u0435\u0440\u0434\u0430\u043C",
                    "value": "AMS",
                    "selected": true
                ],
                [
                    "label": "\u041C\u0430\u0433\u0430\u0434\u0430\u043D",
                    "value": "GDX",
                    "selected": false
                ]
            },
            {
                "type": "button",
                "name": "button-123",
                "className": "btn-success btn",
                "label": "Submit"
            }
        ],
        "from_cache" : true
     }`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
  {
    "status": 400,
    "message": "Validate failed",
    "code": "13110",
    "errors": []
}`,type:"json"}]},filename:"v1/controllers/ClientChatRequestController.php",groupTitle:"ClientChat"},{type:"get",url:"/v1/client-chat-request/project-config",title:"Project Config",version:"0.1.0",name:"ClientChatProjectConfig",group:"ClientChat",permission:[{name:"Authorized User"}],parameter:{fields:{Parameter:[{group:"Parameter",type:"int",optional:!0,field:"project_id",description:"<p>Project ID</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"project_key",description:"<p>Project Key (Priority)</p>"},{group:"Parameter",type:"string",size:"5",optional:!0,field:"language_id",description:"<p>Language ID (ru-RU)</p>"},{group:"Parameter",type:"int",allowedValues:["0","1"],optional:!0,field:"nocache",description:"<p>W/o cache</p>"}]},examples:[{title:"Request-Example:",content:`{
    "project_id": 1,
    "project_key": "ovago",
    "language_id": "ru-RU",
    "nocache": 1
}`,type:"get"}]},header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK

{
"status": 200,
"message": "OK",
"data": {
"endpoint": "chatbot.travel-dev.com",
"enabled": true,
"project": "WOWFARE",
"projectKey": "wowfare",
"notificationSound": "https://cdn.travelinsides.com/npmstatic/assets/chime.mp3",
"theme": {
"theme": "linear-gradient(270deg, #0AAB99 0%, #1E71D1 100%)",
"primary": "#0C89DF",
"primaryDark": "#0066BA",
"accent": "#0C89DF",
"accentDark": "#0066BA"
},
"settings": {
},

"channels": [
        {
            "id": 2,
            "name": "Channel 2",
            "priority": 1,
            "default": false,
            "enabled": true,
            "settings": {
                "max_dialog_count": 4,
                "feedback_rating_enabled": false,
                "feedback_message_enabled": true,
                "history_email_enabled": false,
                "history_download_enabled": true
            }
        },
        {
            "id": 3,
            "name": "Channel 11",
            "priority": 2,
            "default": true,
            "enabled": true,
            "settings": {
                "max_dialog_count": 1,
                "feedback_rating_enabled": true,
                "feedback_message_enabled": true,
                "history_email_enabled": true,
                "history_download_enabled": true
            }
        }
    ],
    "language_id": "ru-RU",
        "translations": {
            "connection_lost": {
                "title": "Connection Lost",
                "subtitle": "Trying to reconnect"
            },
            "waiting_for_response": "Waiting for response",
            "waiting_for_agent": "Waiting for an agent",
            "video_reply": "Video message",
            "audio_reply": "Audio message",
            "image_reply": "Image message",
            "new_message": "New message",
            "agent": "Agent",
            "textarea_placeholder": "Type a message...",
            "registration": {
                "title": "Welcome",
                "subtitle": "Be sure to leave a message",
                "name": "Name",
                "name_placeholder": "Your name",
                "email": "Email",
                "email_placeholder": "Your email",
                "department": "Department",
                "department_placeholder": "Choose a department",
                "start_chat": "Start chat"
            },
            "conversations": {
                "no_conversations": "No conversations yet",
                "no_archived_conversations": "No archived conversations yet",
                "history": "Conversation history",
                "active": "Active",
                "archived": "Archived Chats",
                "start_new": "New Chat"
            },
            "file_upload": {
                "file_too_big": "This file is too big. Max file size is {{size}}",
                "file_too_big_alt": "No archived conversations yetThis file is too large",
                "generic_error": "Failed to upload, please try again",
                "not_allowed": "This file type is not supported",
                "drop_file": "Drop file here to upload it",
                "upload_progress": "Uploading file..."
            },
            "department": {
                "sales": "Sales",
                "support": "Support",
                "exchange": "Exchange"
            }
        },
        "cache": true
    }
    }`,type:"json"},{title:"Not Modified-Response (304):",content:`
HTTP/1.1 304 Not Modified
Cache-Control: public, max-age=3600
Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
  {
"status": 400,
"message": "Project Config not found",
"code": "13108",
"errors": []
}`,type:"json"}]},filename:"v1/controllers/ClientChatRequestController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat-request/create",title:"Client Chat Request",version:"0.1.0",name:"ClientChatRequest",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{examples:[{title:"Request-Example ROOM_CONNECTED:",content:`{
            "event": "ROOM_CONNECTED",
            "data": {
                "rid": "d83ef2d3-30bf-4636-a2c6-7f5b4b0e81a4",
                "geo": {
                    "ip": "92.115.180.30",
                    "version": "IPv4",
                    "city": "Chisinau",
                    "region": "Chi\\u0219in\\u0103u Municipality",
                    "region_code": "CU",
                    "country": "MD",
                    "country_name": "Republic of Moldova",
                    "country_code": "MD",
                    "country_code_iso3": "MDA",
                    "country_capital": "Chisinau",
                    "country_tld": ".md",
                    "continent_code": "EU",
                    "in_eu": false,
                    "postal": "MD-2000",
                    "latitude": 47.0056,
                    "longitude": 28.8575,
                    "timezone": "Europe\\/Chisinau",
                    "utc_offset": "+0300",
                    "country_calling_code": "+373",
                    "currency": "MDL",
                    "currency_name": "Leu",
                    "languages": "ro,ru,gag,tr",
                    "country_area": 33843,
                    "country_population": 3545883,
                    "asn": "AS8926",
                    "org": "Moldtelecom SA"
                },
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "fef46d63-8a30-4eec-89eb-62f1bfc0ffcd",
                    "username": "Test Usrename",
                    "name": "Test Name",
                    "uuid": "54d87707-bb54-46e3-9eca-8f776c7bcacf",
                    "project": "ovago",
                    "channel": "1",
                    "email": "test@techork.com",
                    "leadIds": [
                        234556,
                        357346
                    ],
                    "caseIds": [
                        345464634,
                        345634634
                    ]
                },
                "sources": {
                    "crossSystemXp": "123465.1"
                },
                "page": {
                    "url": "https:\\/\\/dev-ovago.travel-dev.com\\/search\\/WAS-FRA%2F2021-03-22%2F2021-03-28",
                    "title": "Air Ticket Booking - Find Cheap Flights and Airfare Deals - Ovago.com",
                    "referrer": "https:\\/\\/dev-ovago.travel-dev.com\\/search\\/WAS-FRA%2F2021-03-22%2F2021-03-28"
                },
                "system": {
                    "user_agent": "Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/85.0.4183.102 Safari\\/537.36",
                    "language": "en-US",
                    "resolution": "1920x1080"
                },
                "custom": {
                    "event": {
                        "eventName": "UPDATE",
                        "eventProps": []
                    }
                }
            }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
 "status":400,
 "message":"Some errors occurred while creating client chat request",
 "code":"13104",
 "errors":["Event is invalid."]
}`,type:"json"}]},filename:"v1/controllers/ClientChatRequestController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat-request/create-message",title:"Create Message",version:"0.1.0",name:"ClientChatRequestCreateMessage",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{examples:[{title:"Request-Example AGENT_UTTERED:",content:`{
            "event": "AGENT_UTTERED",
            "data": {
                "id": "G6CBYkRYBotjaPPSu",
                "rid": "e19bf809-12c9-4981-89d0-da2f5d071890",
                "token": "56976e05-1916-44fb-a074-5a8d0358019b",
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "56976e05-1916-44fb-a074-5a8d0358019b",
                    "username": "guest-1219",
                    "phone": null,
                    "token": "56976e05-1916-44fb-a074-5a8d0358019b"
                },
                "agent": {
                    "name": "vadim_larsen_admin",
                    "username": "vadim_larsen_admin",
                    "email": "vadim.larsen@techork.com"
                },
                "msg": "test",
                "timestamp": 1602587182948,
                "u": {
                    "_id": "MszwfgYRGB9Tpw5Et",
                    "username": "vadim.larsen"
                },
                    "agentId": "MszwfgYRGB9Tpw5Et"
                }
}`,type:"json"},{title:"Request-Example GUEST_UTTERED with Attachment:",content:`{
            "event": "GUEST_UTTERED",
            "data": {
                "id": "93ea7e9d-04cc-4f96-8bbf-d8b646113fd7",
                "rid": "88c395e3-fe19-4fe2-99dc-b0a1874efbdd",
                "token": "9728d3b4-5754-4339-9b0f-1c75edc727e9",
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "9728d3b4-5754-4339-9b0f-1c75edc727e9",
                    "name": "Henry Fonda",
                    "username": "guest-1220",
                    "phone": null,
                    "token": "9728d3b4-5754-4339-9b0f-1c75edc727e9"
                },
                "agent": {
                    "name": "bot",
                    "username": "bot",
                    "email": "bot@techork.com"
                },
                "msg": "Hi",
                "timestamp": 1602588445024,
                "u": {
                    "_id": "cYNGwXX6L8cN3eb2Q",
                    "username": "guest-1220",
                    "name": "Henry Fonda"
                }
            }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
 "status":400,
 "message":"Some errors occurred while creating client chat request",
 "code":"13104",
 "errors":["Event is invalid."]
}`,type:"json"}]},filename:"v1/controllers/ClientChatRequestController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat/link-cases",title:"Client Chat Link Cases",version:"0.1.0",name:"ClientChat_Link_Cases",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 150",optional:!1,field:"rid",description:"<p>Chat Room Id <code>Required</code></p>"},{group:"Parameter",type:"array",optional:!1,field:"caseIds",description:"<p>Cases Ids <code>Required</code></p>"}]},examples:[{title:"Request-Example:",content:`{
     "rid": "e0ea61ca-ce03-497a-b740-asf4as6fcv",
     "caseIds": [
         235344,
         345567,
         345466
     ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"},{title:"Success-Response-With-Warning:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
   "warning": [
        "Case(254254) already linked to chat"
   ]
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Case id not exist: 235344"
            ],
            "code": "13101"
        }`,type:"json"}]},filename:"v1/controllers/ClientChatController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat/link-leads",title:"Client Chat Link Leads",version:"0.1.0",name:"ClientChat_Link_Leads",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 150",optional:!1,field:"rid",description:"<p>Chat Room Id <code>Required</code></p>"},{group:"Parameter",type:"array",optional:!1,field:"leadIds",description:"<p>Lead Ids <code>Required</code></p>"}]},examples:[{title:"Request-Example:",content:`{
            "rid": "e0ea61ca-ce03-497a-b740-asf4as6fcv",
     "leadIds": [
         235344,
         345567,
         345466
     ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"},{title:"Success-Response-With-Warning:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
   "warning": [
        "Lead(254254) already linked to chat"
   ]
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Lead id not exist: 345567"
            ],
            "code": "13101"
        }`,type:"json"}]},filename:"v1/controllers/ClientChatController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat/subscribe",title:"Client Chat Subscribe",version:"0.1.0",name:"ClientChat_Subscribe",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"subscription_uid",description:"<p>Subscription Unique id <code>Required</code></p>"},{group:"Parameter",type:"date",optional:!0,field:"expired_date",description:"<p>Subscription expiration date <code>format yyyy-mm-dd</code></p>"}]},examples:[{title:"Request-Example Flizzard Subscription:",content:`{
            "chat_visitor_id": "5779293e-dd0f-476f-b0aa-bbbb",
            "subscription_uid": "aksdjAICl5mm590vml",
            "chat_room_id": "9e06ff33-a3b3-4fa0-aa88-asdw2f45gted54yh",
            "expired_date": "2021-10-25",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Visitor subscription saving error: Subscription uid with type has already been taken"
            ],
            "code": "13101"
        }`,type:"json"}]},filename:"v1/controllers/ClientChatController.php",groupTitle:"ClientChat"},{type:"post",url:"/v1/client-chat/unsubscribe",title:"Client Chat Unsubscribe",version:"0.1.0",name:"ClientChat_Unsubscribe",group:"ClientChat",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"subscription_uid",description:"<p>Subscription Unique id <code>Required</code></p>"}]},examples:[{title:"Request-Example:",content:`{
            "subscription_uid": "asgfaposj-34ffd-t34fge",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200
   "message": "Ok"
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Subscription not found by uid: asgfaposj-34ffd-t34fge"
            ],
            "code": "13101"
        }`,type:"json"}]},filename:"v1/controllers/ClientChatController.php",groupTitle:"ClientChat"},{type:"get",url:"/v1/client/info",title:"Client Info",version:"0.1.0",name:"Client",group:"Client",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!1,field:"client_uuid",description:"<p>Client UUID</p>"},{group:"Parameter",type:"string",optional:!1,field:"project_key",description:"<p>Project key</p>"}]},examples:[{title:"Request-Example:",content:`
{
     "client_uuid": "af5241f1-094f-4fde-ada3-bd72986216f0",
     "project_key": "ovago"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "first_name": "Client first name",
         "last_name": "Client last name",
         "created": "2020-09-24 11:29:15"
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 200 OK

{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "client_uuid": [
            "Client Uuid cannot be blank."
       ],
       "project_key": [
            "Project Key is invalid."
        ]
    },
    "code": "11602"
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 200 OK

{
    "status": 400,
    "message": "Load data error",
    "errors": {
         "Not found Client data on request"
    },
    "code": "11601"
}`,type:"json"},{title:"Error-Response (404):",content:`
HTTP/1.1 200 OK

{
    "status": 404,
    "message": "Client not found",
    "code": "11100",
    "errors": []
}`,type:"json"}]},filename:"v1/controllers/ClientController.php",groupTitle:"Client"},{type:"post",url:"/v2/client-email/subscribe",title:"Client Email Subscribe",version:"0.2.0",name:"Client_Email_Subscribe",group:"ClientEmail",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"160",optional:!1,field:"email",description:"<p>Email</p>"}]}},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "email" : "example@email.com"
     },
     "technical": {
         "action": "v2/client-email/subscribe",
         "response_id": 11926631,
         "request_dt": "2020-03-16 11:26:34",
         "response_dt": "2020-03-16 11:26:34",
         "execution_time": 0.076,
         "memory_usage": 506728
     },
     "request": []
 }`,type:"json"},{title:"Not Modified-Response (304):",content:`
HTTP/1.1 304 Not Modified
Cache-Control: public, max-age=3600
Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT`,type:"json"}]},error:{examples:[{title:"Error-Response (405):",content:`
HTTP/1.1 405 Method Not Allowed
  {
      "name": "Method Not Allowed",
      "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
      "code": 0,
      "status": 405,
      "type": "yii\\\\web\\\\MethodNotAllowedHttpException"
  }`,type:"json"},{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          "email": [
              "Contact Email cannot be blank."
          ]
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"}]},filename:"v2/controllers/ClientEmailController.php",groupTitle:"ClientEmail"},{type:"post",url:"/v2/client-email/unsubscribe",title:"Client Email Unsubscribe",version:"0.2.0",name:"Client_Email_Unsubscribe",group:"ClientEmail",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"160",optional:!1,field:"email",description:"<p>Email</p>"}]}},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "email" : "example@email.com"
     },
     "technical": {
         "action": "v2/client-email/unsubscribe",
         "response_id": 11926631,
         "request_dt": "2020-03-16 11:26:34",
         "response_dt": "2020-03-16 11:26:34",
         "execution_time": 0.076,
         "memory_usage": 506728
     },
     "request": []
 }`,type:"json"},{title:"Not Modified-Response (304):",content:`
HTTP/1.1 304 Not Modified
Cache-Control: public, max-age=3600
Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT`,type:"json"}]},error:{examples:[{title:"Error-Response (405):",content:`
HTTP/1.1 405 Method Not Allowed
  {
      "name": "Method Not Allowed",
      "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
      "code": 0,
      "status": 405,
      "type": "yii\\\\web\\\\MethodNotAllowedHttpException"
  }`,type:"json"},{title:"Error-Response(Validation error) (422):",content:`
HTTP/1.1 422 Unprocessable entity
  {
      "status": 422,
      "message": "Validation error",
      "errors": {
          "email": [
              "Contact Email cannot be blank."
          ]
      },
      "code": "21301",
      "technical": {
         ...
      },
      "request": {
         ...
      }
  }`,type:"json"}]},filename:"v2/controllers/ClientEmailController.php",groupTitle:"ClientEmail"},{type:"post",url:"/v1/communication/email",title:"Communication Email",version:"0.1.0",name:"CommunicationEmail",group:"Communication",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:` HTTP/1.1 200 OK
 {
     "type": "update_email_status",
     "eq_id": 127,
     "eq_status_id": 5,
 }


@return array
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException
@throws \\yii\\web\\BadRequestHttpException`,type:"json"}]},filename:"v1/controllers/CommunicationController.php",groupTitle:"Communication"},{type:"post",url:"/v1/communication/sms",title:"Communication SMS",version:"0.1.0",name:"CommunicationSms",group:"Communication",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:` HTTP/1.1 200 OK
 {
     "type": "update_sms_status",
     "sq_id": 127,
     "sq_status_id": 5,
 }


@return array
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException
@throws \\yii\\web\\BadRequestHttpException`,type:"json"}]},filename:"v1/controllers/CommunicationController.php",groupTitle:"Communication"},{type:"post",url:"/v1/communication/voice",title:"Communication Voice",version:"0.1.0",name:"CommunicationVoice",group:"Communication",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:` HTTP/1.1 200 OK
 {
     "type": "update_sms_status"
 }

@return array
@throws BadRequestHttpException
@throws UnprocessableEntityHttpException
@throws \\Throwable
@throws \\yii\\db\\StaleObjectException`,type:"json"}]},filename:"v1/controllers/CommunicationController.php",groupTitle:"Communication"},{type:"post",url:"/v2/coupon/edit",title:"Coupon edit",version:"0.1.0",name:"Coupon_edit",group:"Coupon",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"code",description:"<p>Coupon Code</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"c_start_date",description:"<p>Start Date</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"c_exp_date",description:"<p>Expiration Date</p>"},{group:"Parameter",type:"bool",optional:!0,field:"c_disabled",description:"<p>Disabled</p>"},{group:"Parameter",type:"bool",optional:!0,field:"c_public",description:"<p>Public</p>"}]},examples:[{title:"Request-Example:",content:` {
    "code": "D2EYEWH64BDGD3Y",
    "c_disabled": false,
    "c_public": false,
    "c_start_date": "2021-07-15",
    "c_exp_date": "2021-07-20"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
          "coupon": {
             "c_code": "HPCCZH68PNQB5FY",
             "c_amount": "25.00",
             "c_currency_code": "USD",
             "c_percent": null,
             "c_reusable": 1,
             "c_reusable_count": 1,
             "c_public": 0,
             "c_status_id": 2,
             "c_disabled": null,
             "c_type_id": 1,
             "c_created_dt": "2021-07-12 07:16:25",
             "c_used_count": 0,
             "startDate": null,
             "expDate": "2022-08-12",
             "statusName": "Send",
             "typeName": "Voucher"
         }
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Coupon not found",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/CouponController.php",groupTitle:"Coupon"},{type:"post",url:"/v2/coupon/info",title:"Coupon info",version:"0.1.0",name:"Coupon_info",group:"Coupon",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"code",description:"<p>Coupon Code</p>"}]},examples:[{title:"Request-Example:",content:` {
           "code": "D2EYEWH64BDGD3Y"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
          "coupon": {
             "c_id": 9,
             "c_code": "HPCCZH68PNQB5FY",
             "c_amount": "25.00",
             "c_currency_code": "USD",
             "c_percent": null,
             "c_exp_date": "2022-07-12 00:00:00",
             "c_start_date": null,
             "c_reusable": 0,
             "c_reusable_count": null,
             "c_public": 0,
             "c_status_id": 2,
             "c_disabled": null,
             "c_type_id": 1,
             "c_created_dt": "2021-07-12 07:16:25",
             "statusName": "Send",
             "typeName": "Voucher"
         }
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Coupon not found",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}
.`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/CouponController.php",groupTitle:"Coupon"},{type:"post",url:"/v2/coupon/use",title:"Coupon use",version:"0.1.0",name:"Coupon_use",group:"Coupon",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"code",description:"<p>Coupon Code</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"clientIp",description:"<p>Client Ip</p>"},{group:"Parameter",type:"string",size:"500",optional:!0,field:"clientUserAgent",description:"<p>Client UserAgent</p>"}]},examples:[{title:"Request-Example:",content:` {
    "code": "D2EYEWH64BDGD3Y",
    "clientIp": "127.0.0.1",
    "clientUserAgent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
         "result": true,
         "couponInfo": {
              "c_reusable": 1,
              "c_reusable_count": 5,
              "c_disabled": 0,
              "c_used_count": 0,
              "startDate": "2021-07-14",
              "expDate": "2021-12-25",
              "statusName": "Used"
          }
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Coupon not found",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}
.`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/CouponController.php",groupTitle:"Coupon"},{type:"post",url:"/v2/coupon/validate",title:"Coupon validate",version:"0.1.0",name:"Coupon_validate",group:"Coupon",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"code",description:"<p>Coupon Code</p>"}]},examples:[{title:"Request-Example:",content:` {
           "code": "D2EYEWH64BDGD3Y"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
          "isValid": true,
          "couponInfo": {
              "c_reusable": 1,
              "c_reusable_count": 5,
              "c_disabled": 0,
              "c_used_count": 0,
              "startDate": "2021-07-14",
              "expDate": "2021-12-25",
              "statusName": "New"
          }
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Coupon not found",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}
.`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/CouponController.php",groupTitle:"Coupon"},{type:"post",url:"/v2/coupon/create",title:"Create coupon",version:"0.1.0",name:"Create_coupon",group:"Coupon",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"int",optional:!1,field:"amount",description:"<p>Amount</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"currencyCode",description:"<p>Currency Code (USD)</p>"},{group:"Parameter",type:"int",optional:!0,field:"percent",description:"<p>Percent</p>"},{group:"Parameter",type:"bool",optional:!0,field:"reusable",description:"<p>Reusable</p>"},{group:"Parameter",type:"int",optional:!0,field:"reusableCount",description:"<p>Reusable Count</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"startDate",description:"<p>Start Date</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"expirationDate",description:"<p>Expiration Date</p>"},{group:"Parameter",type:"bool",optional:!0,field:"public",description:"<p>Public</p>"},{group:"Parameter",type:"object",optional:!0,field:"product",description:"<p>Product additional info</p>"},{group:"Parameter",type:"object",optional:!0,field:"product.flight",description:"<p>Product type key</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"product.flight.departure_airport_iata",description:"<p>Departure airport iata</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"product.flight.arrival_airport_iata",description:"<p>Arrival airport iata</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"product.flight.marketing_airline",description:"<p>Marketing airline</p>"},{group:"Parameter",type:"string",size:"1",optional:!0,field:"product.flight.cabin_class",description:"<p>Cabin class</p>"}]},examples:[{title:"Request-Example:",content:` {
           "amount": 25,
           "currencyCode": "USD",
           "percent": "",
           "reusableCount": 3,
           "startDate": "2021-12-20",
           "expirationDate": "2021-12-25",
           "product": {
               "flight": {
                   "departure_airport_iata": "KIV"
               }
           }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
           "coupon": {
                    "c_status_id": 1,
                    "c_type_id": 1,
                    "c_code": "KLCVZWDZGCCNFJE",
                    "c_amount": 25,
                    "c_currency_code": "USD",
                    "c_public": false,
                    "c_reusable": false,
                    "c_reusable_count": 3,
                    "c_percent": 0,
                    "c_created_dt": "2021-07-16 08:37:02",
                    "startDate": "2021-06-20",
                    "expDate": "2022-07-16",
                    "statusName": "Send",
                    "typeName": "Voucher"
                },
                "serviceResponse": {
                    "dec_coupon": "",
                    "enc_coupon": "KLCVZWDZGCCNFJE",
                    "exp_date": "2022-07-16",
                    "amount": 25,
                    "currency": "USD",
                    "public": false,
                    "reusable": false,
                    "valid": true
                },
                "warning": [
                    "Input param \\"reusable\\" (1) rewritten by result service (0)",
                    "Input param \\"expirationDate\\" (2021-12-25) rewritten by result service (2022-07-16)"
                ]
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Coupon create is failed",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}
.`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/CouponController.php",groupTitle:"Coupon"},{type:"post",url:"/v2/department-phone-project/get",title:"Get Department Phone Project",version:"0.2.0",name:"GetDepartmentPhoneProject",group:"DepartmentPhoneProject",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"int",optional:!1,field:"project_id",description:"<p>Project ID</p>"},{group:"Parameter",type:"string",allowedValues:["Sales","Exchange","Support"],optional:!0,field:"department",description:"<p>Department</p>"}]},examples:[{title:"Request-Example:",content:`
{
    "project_id": 6,
    "department": "Sales"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200
       "message": "OK",
       "data": {
           "phones": [
               {
                   "phone": "+15211111111",
                   "cid": "WOWMAC",
                   "department_id": 1,
                   "department": "Sales",
                   "language_id": "en-US",
                   "updated_dt": "2019-01-08 11:44:57"
               },
               {
                   "phone": "+15222222222",
                   "cid": "WSUDCV",
                   "department_id": 3,
                   "department": "Support",
                   "language_id": "fr-FR",
                   "updated_dt": "2019-01-09 11:50:25"
              }
           ]
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": {
            "project_id": [
                "Project Id cannot be blank."
            ],
            "department": [
                "Department is invalid."
            ]
       },
       "code": "14301",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
           "Not found Department Phone Project data on POST request"
      ],
      "code": "14300",
      "request": {
          ...
      },
      "technical": {
          ...
     }
}`,type:"json"}]},filename:"v2/controllers/DepartmentPhoneProjectController.php",groupTitle:"DepartmentPhoneProject"},{type:"post",url:"/v1/flight/fail",title:"Flight Oder Fail",version:"0.1.0",name:"Flight_Oder_Fail",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"orderUid",description:"<p>Order Uid</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"description",description:"<p>Description</p>"}]},examples:[{title:"Request-Example:",content:`{
          "orderUid": "or6061be5ec5c0e",
          "description": "Example reason failing"
       }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "resultMessage": "Order Uid(or6061be5ec5c0e) successful failed"
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`HTTP/1.1 200 OK
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "orderUid": [
            "orderUid cannot be blank"
       ]
    },
    "code": "15801"
}`,type:"json"},{title:"Error-Response (404):",content:`HTTP/1.1 200 OK
{
    "status": 404,
    "message": "Order not found",
    "code": "15300",
    "errors": []
}`,type:"json"}]},filename:"v1/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v1/flight/replace",title:"Flight Replace",version:"0.1.0",name:"Flight_Replace",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"15",optional:!1,field:"fareId",description:"<p>Fare Id (Order identity)</p>"},{group:"Parameter",type:"object",optional:!1,field:"flights",description:"<p>Flights data array</p>"},{group:"Parameter",type:"object",optional:!0,field:"payments",description:"<p>Payments data array</p>"},{group:"Parameter",type:"object",optional:!0,field:"options",description:"<p>Options data array</p>"}]},examples:[{title:"Request-Example:",content:`
{
          "fareId": "or6061be5ec5c0e",
          "parentBookingId": "OE96040",
          "parentId": 205975,
          "sameItinerary": true,
          "flights": [
              {
                  "appKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
                  "uniqueId": "OE96040",
                  "status": 6,
                  "pnr": "",
                  "gds": "",
                  "flightType": "RT",
                  "validatingCarrier": "PR",
                  "bookingInfo": [
                      {
                          "bookingId": "OE96040",
                          "pnr": "Q3PM1G",
                          "gds": "S",
                          "validatingCarrier": "PR",
                          "status": 6,
                          "state": "Rejected",
                          "passengers": {
                              "1": {
                                  "fullName": "Arthur Davis",
                                  "first_name": "Arthur",
                                  "middle_name": "",
                                  "last_name": "Davis",
                                  "birth_date": "1963-04-07",
                                  "nationality": "US",
                                  "gender": "M",
                                  "aGender": "Mr.",
                                  "tktNumber": null,
                                  "paxType": "ADT"
                              }
                          },
                          "airlinesCode": [
                              {
                                  "code": "PR",
                                  "airline": "Philippine Airlines",
                                  "recordLocator": "Q3PM1G"
                              }
                          ],
                          "insurance": []
                      }
                  ],
                  "trips": [
                      {
                          "segments": [
                              {
                                  "segmentId": 1001959,
                                  "passengers":{
                                      "p1":{
                                          "fullname":"Tester Testerov",
                                          "products":{
                                              "bag":"test_30kg",
                                              "seat":"24E"
                                          }
                                      }
                                  },
                                  "airline": "PR",
                                  "airlineName": "Philippine Airlines",
                                  "mainAirline": "PR",
                                  "arrivalAirport": "MNL",
                                  "arrivalTime": "2021-05-15 04:00:00",
                                  "departureAirport": "LAX",
                                  "departureTime": "2021-05-13 22:30:00",
                                  "bookingClass": "U",
                                  "flightNumber": 103,
                                  "statusCode": "HK",
                                  "operatingAirline": "Philippine Airlines",
                                  "operatingAirlineCode": "PR",
                                  "cabin": "Economy",
                                  "departureCity": "Los Angeles",
                                  "arrivalCity": "Manila",
                                  "departureCountry": "US",
                                  "arrivalCountry": "PH",
                                  "departureAirportName": "Los Angeles International Airport",
                                  "arrivalAirportName": "Ninoy Aquino International Airport",
                                  "flightDuration": 870,
                                  "layoverDuration": 0,
                                  "airlineRecordLocator": "Q3PM1G",
                                  "aircraft": "773",
                                  "baggage": 2,
                                  "carryOn": true,
                                  "marriageGroup": "773",
                                  "fareCode": "U9XBUS",
                                  "mileage": 7305
                              },
                              {
                                  "segmentId": 1001960,
                                  "passengers":{
                                      "p1":{
                                          "fullname":"Tester Testerov",
                                          "products":{
                                              "bag":"test_30kg",
                                              "seat":"25E"
                                          }
                                      }
                                  },
                                  "airline": "PR",
                                  "airlineName": "Philippine Airlines",
                                  "mainAirline": "PR",
                                  "arrivalAirport": "TPE",
                                  "arrivalTime": "2021-05-15 08:40:00",
                                  "departureAirport": "MNL",
                                  "departureTime": "2021-05-15 06:30:00",
                                  "bookingClass": "U",
                                  "flightNumber": 890,
                                  "statusCode": "HK",
                                  "operatingAirline": "Philippine Airlines",
                                  "operatingAirlineCode": "PR",
                                  "cabin": "Economy",
                                  "departureCity": "Manila",
                                  "arrivalCity": "Taipei",
                                  "departureCountry": "PH",
                                  "arrivalCountry": "TW",
                                  "departureAirportName": "Ninoy Aquino International Airport",
                                  "arrivalAirportName": "Taiwan Taoyuan International Airport",
                                  "flightDuration": 130,
                                  "layoverDuration": 150,
                                  "airlineRecordLocator": "Q3PM1G",
                                  "aircraft": "321",
                                  "baggage": 2,
                                  "carryOn": true,
                                  "marriageGroup": "321",
                                  "fareCode": "U9XBUS",
                                  "mileage": 728
                              }
                          ]
                      }
                  ],
                  "price": {
                      "tickets": 1,
                      "selling": 767.75,
                      "currentProfit": 0,
                      "fare": 446,
                      "net": 717.75,
                      "taxes": 321.75,
                      "tips": 0,
                      "currency": "USD",
                      "detail": {
                          "ADT": {
                              "selling": 767.75,
                              "fare": 446,
                              "baseTaxes": 271.75,
                              "taxes": 321.75,
                              "tickets": 1,
                              "insurance": 0
                          }
                      }
                  },
                  "departureTime": "2021-05-13 22:30:00",
                  "invoiceUri": "\\/checkout\\/download\\/OE96040\\/invoice",
                  "eTicketUri": "\\/checkout\\/download\\/OE96040\\/e-ticket",
                  "scheduleChange": "No"
              }
          ],
          "trips": [],
          "payments": [
              {
                  "pay_amount": 200.21,
                  "pay_currency": "USD",
                  "pay_auth_id": 728282,
                  "pay_type": "Capture",
                  "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                  "pay_date": "2021-03-25",
                  "pay_method_key": "card",
                  "pay_description": "example description",
                  "creditCard": {
                      "holder_name": "Tester holder",
                      "number": "111**********111",
                      "type": "Visa",
                      "expiration": "07 / 23",
                      "cvv": "123"
                  },
                  "billingInfo": {
                      "first_name": "Hobbit",
                      "middle_name": "Hard",
                      "last_name": "Lover",
                      "address": "1013 Weda Cir",
                      "country_id": "US",
                      "city": "Gotham City",
                      "state": "KY",
                      "zip": "99999",
                      "phone": "+19074861000",
                      "email": "barabara@test.com"
                  }
              }
          ],
          "options": [
              {
                "pqo_key": "cfar",
                "pqo_name": "CFAR option",
                "pqo_price": 750.21,
                "pqo_markup": 100.21,
                "pqo_description": "CFAR option: Cancel before limit",
                "pqo_request_data": "{\\"type\\":\\"standard\\",\\"amount\\":750.21,\\"options\\":[{\\"name\\":\\"Cancel before limit\\",\\"type\\":\\"before\\",\\"limit\\":0,\\"value\\":\\"60\\"}],\\"paxCount\\":3,\\"isActivated\\":true,\\"amountPerPax\\":250.07}"
              },
              {
                "pqo_key": "package",
                "pqo_name": "Package option",
                "pqo_price": 89.85,
                "pqo_markup": 0,
                "pqo_description": "Package option: Exchange and Refund Processing Fee",
                "pqo_request_data": "{\\"type\\":\\"standard\\",\\"amount\\":89.85,\\"options\\":[{\\"name\\":\\"24 Hour Free Cancellation\\",\\"type\\":\\"VOID\\",\\"value\\":\\"included\\",\\"special\\":true}],\\"paxCount\\":3,\\"isActivated\\":true,\\"amountPerPax\\":29.95}"
              }
          ]
      }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "resultMessage": "Order Uid(or6061be5ec5c0e) successful processed"
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`HTTP/1.1 200 OK
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "orderUid": [
            "orderUid cannot be blank"
       ]
    },
    "code": "15801"
}`,type:"json"},{title:"Error-Response (404):",content:`HTTP/1.1 200 OK
{
    "status": 404,
    "message": "Order not found",
    "code": "15300",
    "errors": []
}`,type:"json"}]},filename:"v1/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v1/flight/ticket-issue",title:"Flight Ticket Issue",version:"0.1.0",name:"Flight_Ticket_Issue",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"255",optional:!1,field:"fareId",description:"<p>Fare Id (Order identity)</p>"},{group:"Parameter",type:"object",optional:!1,field:"flights",description:"<p>Flights data array</p>"},{group:"Parameter",type:"object",optional:!0,field:"payments",description:"<p>Payments data array</p>"},{group:"Parameter",type:"object",optional:!0,field:"options",description:"<p>Options data array</p>"}]},examples:[{title:"Request-Example:",content:`
{
          "fareId": "or6061be5ec5c0e",
          "parentBookingId": "OE96041",
          "parentId": 205975,
          "sameItinerary": true,
          "flights": [
              {
                  "appKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
                  "uniqueId": "OE96040",
                  "status": 3,
                  "pnr": "Q3PM1G",
                  "gds": "S",
                  "flightType": "RT",
                  "validatingCarrier": "PR",
                  "bookingInfo": [
                      {
                          "bookingId": "OE96040",
                          "pnr": "Q3PM1G",
                          "gds": "S",
                          "validatingCarrier": "PR",
                          "status": 3,
                          "state": "Success",
                          "passengers": {
                              "1": {
                                  "fullName": "Arthur Davis",
                                  "first_name": "Arthur",
                                  "middle_name": "",
                                  "last_name": "Davis",
                                  "birth_date": "1963-04-07",
                                  "nationality": "US",
                                  "gender": "M",
                                  "aGender": "Mr.",
                                  "tktNumber": "tktNumber",
                                  "paxType": "ADT"
                              }
                          },
                          "airlinesCode": [
                              {
                                  "code": "PR",
                                  "airline": "Philippine Airlines",
                                  "recordLocator": "Q3PM1G"
                              }
                          ],
                          "insurance": []
                      }
                  ],
                  "trips": [
                      {
                          "segments": [
                              {
                                  "segmentId": 1001959,
                                  "passengers":{
                                      "p1":{
                                          "fullname":"Tester Testerov",
                                          "products":{
                                              "bag":"test_30kg",
                                              "seat":"24E"
                                          }
                                      }
                                  },
                                  "airline": "PR",
                                  "airlineName": "Philippine Airlines",
                                  "mainAirline": "PR",
                                  "arrivalAirport": "MNL",
                                  "arrivalTime": "2021-05-15 04:00:00",
                                  "departureAirport": "LAX",
                                  "departureTime": "2021-05-13 22:30:00",
                                  "bookingClass": "U",
                                  "flightNumber": 103,
                                  "statusCode": "HK",
                                  "operatingAirline": "Philippine Airlines",
                                  "operatingAirlineCode": "PR",
                                  "cabin": "Economy",
                                  "departureCity": "Los Angeles",
                                  "arrivalCity": "Manila",
                                  "departureCountry": "US",
                                  "arrivalCountry": "PH",
                                  "departureAirportName": "Los Angeles International Airport",
                                  "arrivalAirportName": "Ninoy Aquino International Airport",
                                  "flightDuration": 870,
                                  "layoverDuration": 0,
                                  "airlineRecordLocator": "Q3PM1G",
                                  "aircraft": "773",
                                  "baggage": 2,
                                  "carryOn": true,
                                  "marriageGroup": "773",
                                  "fareCode": "U9XBUS",
                                  "mileage": 7305
                              },
                              {
                                  "segmentId": 1001960,
                                  "passengers":{
                                      "p1":{
                                          "fullname":"Tester Testerov",
                                          "products":{
                                              "bag":"test_30kg",
                                              "seat":"25E"
                                          }
                                      }
                                  },
                                  "airline": "PR",
                                  "airlineName": "Philippine Airlines",
                                  "mainAirline": "PR",
                                  "arrivalAirport": "TPE",
                                  "arrivalTime": "2021-05-15 08:40:00",
                                  "departureAirport": "MNL",
                                  "departureTime": "2021-05-15 06:30:00",
                                  "bookingClass": "U",
                                  "flightNumber": 890,
                                  "statusCode": "HK",
                                  "operatingAirline": "Philippine Airlines",
                                  "operatingAirlineCode": "PR",
                                  "cabin": "Economy",
                                  "departureCity": "Manila",
                                  "arrivalCity": "Taipei",
                                  "departureCountry": "PH",
                                  "arrivalCountry": "TW",
                                  "departureAirportName": "Ninoy Aquino International Airport",
                                  "arrivalAirportName": "Taiwan Taoyuan International Airport",
                                  "flightDuration": 130,
                                  "layoverDuration": 150,
                                  "airlineRecordLocator": "Q3PM1G",
                                  "aircraft": "321",
                                  "baggage": 2,
                                  "carryOn": true,
                                  "marriageGroup": "321",
                                  "fareCode": "U9XBUS",
                                  "mileage": 728
                              }
                          ]
                      }
                  ],
                  "price": {
                      "tickets": 1,
                      "selling": 767.75,
                      "currentProfit": 0,
                      "fare": 446,
                      "net": 717.75,
                      "taxes": 321.75,
                      "tips": 0,
                      "currency": "USD",
                      "detail": {
                          "ADT": {
                              "selling": 767.75,
                              "fare": 446,
                              "baseTaxes": 271.75,
                              "taxes": 321.75,
                              "tickets": 1,
                              "insurance": 0
                          }
                      }
                  },
                  "departureTime": "2021-05-13 22:30:00",
                  "invoiceUri": "\\/checkout\\/download\\/OE96040\\/invoice",
                  "eTicketUri": "\\/checkout\\/download\\/OE96040\\/e-ticket",
                  "scheduleChange": "No"
              }
          ],
          "trips": [],
          "payments": [
              {
                  "pay_amount": 200.21,
                  "pay_currency": "USD",
                  "pay_auth_id": 728282,
                  "pay_type": "Capture",
                  "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                  "pay_date": "2021-03-25",
                  "pay_method_key": "card",
                  "pay_description": "example description",
                  "creditCard": {
                      "holder_name": "Tester holder",
                      "number": "111**********111",
                      "type": "Visa",
                      "expiration": "07 / 23",
                      "cvv": "123"
                  },
                  "billingInfo": {
                      "first_name": "Hobbit",
                      "middle_name": "Hard",
                      "last_name": "Lover",
                      "address": "1013 Weda Cir",
                      "country_id": "US",
                      "city": "Gotham City",
                      "state": "KY",
                      "zip": "99999",
                      "phone": "+19074861000",
                      "email": "barabara@test.com"
                  }
              }
          ],
          "options": [
              {
                "pqo_key": "cfar",
                "pqo_name": "CFAR option",
                "pqo_price": 750.21,
                "pqo_markup": 100.21,
                "pqo_description": "CFAR option: Cancel before limit",
                "pqo_request_data": "{\\"type\\":\\"standard\\",\\"amount\\":750.21,\\"options\\":[{\\"name\\":\\"Cancel before limit\\",\\"type\\":\\"before\\",\\"limit\\":0,\\"value\\":\\"60\\"}],\\"paxCount\\":3,\\"isActivated\\":true,\\"amountPerPax\\":250.07}"
              },
              {
                "pqo_key": "package",
                "pqo_name": "Package option",
                "pqo_price": 89.85,
                "pqo_markup": 0,
                "pqo_description": "Package option: Exchange and Refund Processing Fee",
                "pqo_request_data": "{\\"type\\":\\"standard\\",\\"amount\\":89.85,\\"options\\":[{\\"name\\":\\"24 Hour Free Cancellation\\",\\"type\\":\\"VOID\\",\\"value\\":\\"included\\",\\"special\\":true}],\\"paxCount\\":3,\\"isActivated\\":true,\\"amountPerPax\\":29.95}"
              }
          ]
      }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "resultMessage": "Order Uid(or6061be5ec5c0e) successful processed"
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`HTTP/1.1 200 OK
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "orderUid": [
            "orderUid cannot be blank"
       ]
    },
    "code": "15801"
}`,type:"json"},{title:"Error-Response (404):",content:`HTTP/1.1 200 OK
{
    "status": 404,
    "message": "Order not found",
    "code": "15300",
    "errors": []
}`,type:"json"}]},filename:"v1/controllers/FlightController.php",groupTitle:"Flight"},{type:"get",url:"/v2/flight/product-quote-get",title:"Get product quote",version:"0.1.0",name:"ProductQuoteGet",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"32",optional:!1,field:"product_quote_gid",description:"<p>Product Quote gid</p>"},{group:"Parameter",type:"string[]",optional:!0,field:"with",description:"<p>Array (&quot;quote_list&quot;, &quot;last_change&quot;)</p>"}]},examples:[{title:"Request-Example:",content:` {
    "product_quote_gid": "2bd12377691f282e11af12937674e3d1",
    "with": ["quote_list", "last_change"],
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
        {
            "status": 200,
            "message": "OK",
            "product_quote": {
                "pq_gid": "1865ef55f3c6c01dca1f4f3128e82733",
                "pq_name": "test",
                "pq_order_id": 35,
                "pq_description": null,
                "pq_status_id": 10,
                "pq_price": 430.46,
                "pq_origin_price": 326.9,
                "pq_client_price": 430.46,
                "pq_service_fee_sum": 14.56,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Declined",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 2,
                    "fq_source_id": null,
                    "fq_product_quote_id": 184,
                    "gds": "T",
                    "pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "validatingCarrier": "AF",
                    "fq_fare_type_id": 1,
                    "fq_last_ticket_date": "2021-03-25",
                    "fq_origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkxMDAwL0tJVkxPTjIwMjEtMDMtMjUqQUZ+I0FGNjYwMiNBRjE4ODkjQUYxMzgwfmxjOmVuX3Vz\\",\\"routingId\\":2,\\"prices\\":{\\"lastTicketDate\\":\\"2021-03-25\\",\\"totalPrice\\":326.9,\\"totalTax\\":55.9,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":1,\\"baseFare\\":271,\\"pubBaseFare\\":271,\\"baseTax\\":55.9,\\"markup\\":0,\\"comm\\":0,\\"price\\":326.9,\\"tax\\":55.9,\\"oBaseFare\\":{\\"amount\\":271,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":55.9,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":false,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-03-25 05:25\\",\\"arrivalTime\\":\\"2021-03-25 06:40\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"6602\\",\\"bookingClass\\":\\"E\\",\\"duration\\":75,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"OTP\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"AT7\\",\\"marketingAirline\\":\\"AF\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":215,\\"cabin\\":\\"Y\\",\\"brandId\\":\\"657936\\",\\"brandName\\":\\"Economy Standard\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"ES50BBST\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2021-03-25 14:25\\",\\"arrivalTime\\":\\"2021-03-25 16:35\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"1889\\",\\"bookingClass\\":\\"E\\",\\"duration\\":190,\\"departureAirportCode\\":\\"OTP\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"CDG\\",\\"arrivalAirportTerminal\\":\\"2E\\",\\"operatingAirline\\":\\"AF\\",\\"airEquipType\\":\\"319\\",\\"marketingAirline\\":\\"AF\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":1147,\\"cabin\\":\\"Y\\",\\"brandId\\":\\"657936\\",\\"brandName\\":\\"Economy Standard\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"ES50BBST\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false},{\\"segmentId\\":3,\\"departureTime\\":\\"2021-03-25 21:20\\",\\"arrivalTime\\":\\"2021-03-25 21:45\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"1380\\",\\"bookingClass\\":\\"E\\",\\"duration\\":85,\\"departureAirportCode\\":\\"CDG\\",\\"departureAirportTerminal\\":\\"2E\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"AF\\",\\"airEquipType\\":\\"318\\",\\"marketingAirline\\":\\"AF\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":214,\\"cabin\\":\\"Y\\",\\"brandId\\":\\"657936\\",\\"brandName\\":\\"Economy Standard\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"ES50BBST\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":1100}],\\"maxSeats\\":9,\\"paxCnt\\":1,\\"validatingCarrier\\":\\"AF\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"661f0376-d209-4216-a0d1-97c8f7cf5746\\",\\"availabilitySources\\":\\"S,S,S\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":9}},\\"ngsFeatures\\":{\\"stars\\":1,\\"name\\":\\"Economy Standard\\",\\"list\\":[]},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTEwMDB8S0lWTE9OMjAyMS0wMy0yNQ==\\",\\"lang\\":\\"en\\",\\"rank\\":5.9333334,\\"cheapest\\":false,\\"fastest\\":false,\\"best\\":false,\\"bags\\":1,\\"country\\":\\"us\\"},\\"price\\":326.9,\\"originRate\\":1,\\"stops\\":[2],\\"time\\":[{\\"departure\\":\\"2021-03-25 05:25\\",\\"arrival\\":\\"2021-03-25 21:45\\"}],\\"bagFilter\\":1,\\"airportChange\\":false,\\"technicalStopCnt\\":0,\\"duration\\":[1100],\\"totalDuration\\":1100,\\"topCriteria\\":\\"\\",\\"rank\\":5.9333334}",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "1",
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 44,
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
                            "uid": "fqt6047ae8cde4af",
                            "key": null,
                            "duration": 1100,
                            "segments": [
                                {
                                    "uid": "fqs6047ae8cdf8d9",
                                    "departureTime": "2021-03-25 05:25",
                                    "arrivalTime": "2021-03-25 06:40",
                                    "flightNumber": 6602,
                                    "bookingClass": "E",
                                    "duration": 75,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "OTP",
                                    "arrivalAirportTerminal": "",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "AF",
                                    "airEquipType": "AT7",
                                    "marriageGroup": "I",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 215,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Bucharest",
                                    "stop": 1,
                                    "stops": [
                                        {
                                            "qss_quote_segment_id": 9,
                                            "locationCode": "SCL",
                                            "equipment": "",
                                            "elapsedTime": 120,
                                            "duration": 120,
                                            "departureDateTime": "2021-09-09 00:00",
                                            "arrivalDateTime": "2021-09-08 00:00"
                                        }
                                    ],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 9,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6047ae8ce16d5",
                                    "departureTime": "2021-03-25 14:25",
                                    "arrivalTime": "2021-03-25 16:35",
                                    "flightNumber": 1889,
                                    "bookingClass": "E",
                                    "duration": 190,
                                    "departureAirportCode": "OTP",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "CDG",
                                    "arrivalAirportTerminal": "2E",
                                    "operatingAirline": "AF",
                                    "marketingAirline": "AF",
                                    "airEquipType": "319",
                                    "marriageGroup": "I",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 1147,
                                    "departureLocation": "Bucharest",
                                    "arrivalLocation": "Paris",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 10,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6047ae8ce248c",
                                    "departureTime": "2021-03-25 21:20",
                                    "arrivalTime": "2021-03-25 21:45",
                                    "flightNumber": 1380,
                                    "bookingClass": "E",
                                    "duration": 85,
                                    "departureAirportCode": "CDG",
                                    "departureAirportTerminal": "2E",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "2",
                                    "operatingAirline": "AF",
                                    "marketingAirline": "AF",
                                    "airEquipType": "318",
                                    "marriageGroup": "O",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 214,
                                    "departureLocation": "Paris",
                                    "arrivalLocation": "London",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 11,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
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
                            "qpp_fare": "271.00",
                            "qpp_tax": "55.90",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "89.00",
                            "qpp_origin_fare": "271.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "55.90",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "271.00",
                            "qpp_client_tax": "55.90",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp604741cd064a1",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047ae79a875c",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047ae8cdbb37",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                }
            },
            "quote_list": [
                {
                    "relation_type": "Voluntary Exchange",
                    "relation_type_id": 5, "(1-replace, 2-clone, 3-alternative, 4-reProtection, 5-voluntary exchange)"
                    "recommended": true,
                    "pq_gid": "289ddd4b911e88d7bf1eb14be44754d7",
                    "pq_name": "test",
                    "pq_order_id": 35,
                    "pq_description": null,
                    "pq_status_id": 1,
                    "pq_price": 0,
                    "pq_origin_price": 0,
                    "pq_client_price": 0,
                    "pq_service_fee_sum": 0,
                    "pq_origin_currency": null,
                    "pq_client_currency": "USD",
                    "pq_status_name": "New",
                    "pq_files": [],
                    "data": {
                        "changePricing" : {
                            "baseFare": 10.01,
                            "baseTax": 10.01,
                            "markup": 10.01,
                            "price": 30.01
                        },
                        "fq_flight_id": 2,
                        "fq_source_id": null,
                        "fq_product_quote_id": 191,
                        "gds": "S",
                        "pcc": "8KI0",
                        "fq_gds_offer_id": null,
                        "fq_type_id": 3,
                        "fq_cabin_class": "E",
                        "fq_trip_type_id": 1,
                        "validatingCarrier": "PR",
                        "fq_fare_type_id": 2,
                        "fq_last_ticket_date": null,
                        "fq_origin_search_data": "{\\"gds\\":\\"S\\",\\"pcc\\":\\"8KI0\\",\\"trips\\":[{\\"duration\\":848,\\"segments\\":[{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":600,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-06-11 07:30:00\\",\\"airEquipType\\":null,\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"8727\\",\\"departureTime\\":\\"2021-06-10 21:30:00\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"CDG\\",\\"departureAirportCode\\":\\"ROB\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null},{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":160,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-06-11 12:55:00\\",\\"airEquipType\\":null,\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"8395\\",\\"departureTime\\":\\"2021-06-11 10:15:00\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"LAX\\",\\"departureAirportCode\\":\\"CDG\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null},{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":88,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-06-11 19:14:00\\",\\"airEquipType\\":null,\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"3580\\",\\"departureTime\\":\\"2021-06-11 17:46:00\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"SMF\\",\\"departureAirportCode\\":\\"LAX\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null}]},{\\"duration\\":1233,\\"segments\\":[{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":127,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-09-10 12:34\\",\\"airEquipType\\":\\"E7W\\",\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"3864\\",\\"departureTime\\":\\"2021-09-10 10:27\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"SEA\\",\\"departureAirportCode\\":\\"SMF\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null},{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":201,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-09-10 13:34\\",\\"airEquipType\\":\\"739\\",\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"759\\",\\"departureTime\\":\\"2021-09-10 08:13\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"MSP\\",\\"departureAirportCode\\":\\"SEA\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null},{\\"meal\\":null,\\"stop\\":0,\\"cabin\\":\\"Y\\",\\"stops\\":[],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":510,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-09-11 08:15\\",\\"airEquipType\\":\\"333\\",\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"42\\",\\"departureTime\\":\\"2021-09-10 16:45\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"CDG\\",\\"departureAirportCode\\":\\"MSP\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null},{\\"meal\\":null,\\"stop\\":1,\\"cabin\\":\\"Y\\",\\"stops\\":[{\\"duration\\":85,\\"equipment\\":null,\\"elapsedTime\\":null,\\"locationCode\\":\\"BKO\\",\\"arrivalDateTime\\":\\"2021-09-11 13:55\\",\\"departureDateTime\\":\\"2021-09-11 15:20\\"}],\\"baggage\\":[],\\"brandId\\":null,\\"mileage\\":null,\\"duration\\":395,\\"fareCode\\":null,\\"arrivalTime\\":\\"2021-09-11 16:50\\",\\"airEquipType\\":\\"359\\",\\"bookingClass\\":\\"E\\",\\"flightNumber\\":\\"7351\\",\\"departureTime\\":\\"2021-09-11 10:15\\",\\"marriageGroup\\":\\"\\",\\"marketingAirline\\":\\"DL\\",\\"operatingAirline\\":null,\\"arrivalAirportCode\\":\\"ROB\\",\\"departureAirportCode\\":\\"CDG\\",\\"arrivalAirportTerminal\\":null,\\"departureAirportTerminal\\":null}]}],\\"fareType\\":\\"SR\\",\\"itineraryDump\\":[\\"DL8727E 10JUN ROBCDG TK  930P  730A+ 11JUN TH\\/FR\\",\\"DL8395E 11JUN CDGLAX HK 1015A 1255P FR\\",\\"DL3580E 11JUN LAXSMF HK  546P  714P FR\\",\\"DL3864E 10SEP SMFSEA TK 1027A 1234P FR\\",\\"DL 759E 10SEP SEAMSP TK  813A  134P FR\\",\\"DL  42E 10SEP MSPCDG TK  445P  815A+ 11SEP FR\\/SA\\",\\"DL7351E 11SEP CDGROB HK 1015A  450P SA\\",\\"DL7351E 11SEP BKOROB HK  320P  450P SA\\"],\\"validatingCarrier\\":\\"PR\\"}",
                        "fq_json_booking": null,
                        "fq_ticket_json": null,
                        "itineraryDump": [
                            "1  DL8727E  10JUN  ROBCDG    930P    730A+  11JUN  TH/FR",
                            "2  DL8395E  11JUN  CDGLAX  1015A  1255P  FR",
                            "3  DL3580E  11JUN  LAXSMF    546P    714P  FR",
                            "4  DL3864E  10SEP  SMFSEA  1027A  1234P  FR",
                            "5  DL  759E  10SEP  SEAMSP    813A    134P  FR",
                            "6  DL    42E  10SEP  MSPCDG    445P    815A+  11SEP  FR/SA",
                            "7  DL7351E  11SEP  CDGROB  1015A    450P  SA"
                        ],
                        "booking_id": "1",
                        "fq_type_name": "ReProtection",
                        "fq_fare_type_name": "Private",
                        "fareType": "SR",
                        "flight": {
                            "fl_product_id": 44,
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
                                "uid": "fqt6116010ce3d6b",
                                "key": null,
                                "duration": 848,
                                "segments": [
                                    {
                                        "uid": "fqs6116010ce9306",
                                        "departureTime": "2021-06-10 21:30",
                                        "arrivalTime": "2021-06-11 07:30",
                                        "flightNumber": 8727,
                                        "bookingClass": "E",
                                        "duration": 600,
                                        "departureAirportCode": "ROB",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "CDG",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Monrovia",
                                        "arrivalLocation": "Paris",
                                        "stop": 0,
                                        "stops": []
                                    },
                                    {
                                        "uid": "fqs6116010ceb91e",
                                        "departureTime": "2021-06-11 10:15",
                                        "arrivalTime": "2021-06-11 12:55",
                                        "flightNumber": 8395,
                                        "bookingClass": "E",
                                        "duration": 160,
                                        "departureAirportCode": "CDG",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "LAX",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Paris",
                                        "arrivalLocation": "Los Angeles",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010cebd9a",
                                        "departureTime": "2021-06-11 17:46",
                                        "arrivalTime": "2021-06-11 19:14",
                                        "flightNumber": 3580,
                                        "bookingClass": "E",
                                        "duration": 88,
                                        "departureAirportCode": "LAX",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "SMF",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Los Angeles",
                                        "arrivalLocation": "Sacramento",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    }
                                ]
                            },
                            {
                                "uid": "fqt6116010cec0cf",
                                "key": null,
                                "duration": 1233,
                                "segments": [
                                    {
                                        "uid": "fqs6116010cec45b",
                                        "departureTime": "2021-09-10 10:27",
                                        "arrivalTime": "2021-09-10 12:34",
                                        "flightNumber": 3864,
                                        "bookingClass": "E",
                                        "duration": 127,
                                        "departureAirportCode": "SMF",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "SEA",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "E7W",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Sacramento",
                                        "arrivalLocation": "Seattle",
                                        "stop": 0,
                                        "stops": []
                                    },
                                    {
                                        "uid": "fqs6116010cec885",
                                        "departureTime": "2021-09-10 08:13",
                                        "arrivalTime": "2021-09-10 13:34",
                                        "flightNumber": 759,
                                        "bookingClass": "E",
                                        "duration": 201,
                                        "departureAirportCode": "SEA",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "MSP",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "739",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Seattle",
                                        "arrivalLocation": "Minneapolis",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010ceccdb",
                                        "departureTime": "2021-09-10 16:45",
                                        "arrivalTime": "2021-09-11 08:15",
                                        "flightNumber": 42,
                                        "bookingClass": "E",
                                        "duration": 510,
                                        "departureAirportCode": "MSP",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "CDG",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "333",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Minneapolis",
                                        "arrivalLocation": "Paris",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010ced118",
                                        "departureTime": "2021-09-11 10:15",
                                        "arrivalTime": "2021-09-11 16:50",
                                        "flightNumber": 7351,
                                        "bookingClass": "E",
                                        "duration": 395,
                                        "departureAirportCode": "CDG",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "ROB",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "359",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Paris",
                                        "arrivalLocation": "Monrovia",
                                        "stop": 1,
                                        "stops": [
                                            {
                                                "qss_quote_segment_id": 26,
                                                "locationCode": "BKO",
                                                "equipment": null,
                                                "elapsedTime": null,
                                                "duration": 85,
                                                "departureDateTime": "2021-09-11 15:20",
                                                "arrivalDateTime": "2021-09-11 13:55"
                                            }
                                        ],
                                        "baggage": []
                                    }
                                ]
                            }
                        ],
                        "pax_prices": [
                            {
                                "qpp_fare": "877.00",
                                "qpp_tax": "464.28",
                                "qpp_system_mark_up": "50.00",
                                "qpp_agent_mark_up": "0.00",
                                "qpp_origin_fare": null,
                                "qpp_origin_currency": "USD",
                                "qpp_origin_tax": null,
                                "qpp_client_currency": "USD",
                                "qpp_client_fare": null,
                                "qpp_client_tax": null,
                                "paxType": "ADT"
                            }
                        ],
                        "paxes": [
                            {
                                "fp_uid": "fp604741cd064a1",
                                "fp_pax_id": null,
                                "fp_pax_type": "ADT",
                                "fp_first_name": null,
                                "fp_last_name": null,
                                "fp_middle_name": null,
                                "fp_dob": null
                            }
                        ]
                    }
                }
            ],
            "last_change": {
                "pqc_id": 1,
                "pqc_pq_id": 645,
                "pqc_case_id": 135814,
                "pqc_decision_user": 464,
                "pqc_status_id": 6,
                "pqc_decision_type_id": 1,
                "pqc_created_dt": "2021-08-17 11:44:34",
                "pqc_updated_dt": "2021-08-26 10:09:03",
                "pqc_decision_dt": "2021-08-24 14:33:39",
                "pqc_is_automate": 0
            }
        }`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 200 Ok
{
            "status": 422,
            "message": "Product Quote not found",
            "errors": [
                "Product Quote not found"
            ],
        }`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 200 Ok
{
            "status": 500,
            "message": "Internal Server Error",
            "errors": []
        }`,type:"json"},{title:"Note:",content:`[
     In "quote_list" show by status restriction from settings - "exchange_quote_confirm_status_list"
]`,type:"html"}]},filename:"v2/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v2/flight/reprotection-create",title:"ReProtection Create",version:"0.1.0",name:"ReProtection_Create",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"10",optional:!1,field:"booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"bool",optional:!0,field:"is_automate",description:"<p>Is automate (default false)</p>"},{group:"Parameter",type:"object",optional:!0,field:"flight_quote",description:"<p>Flight quote</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"flight_quote.gds",description:"<p>Gds</p>"},{group:"Parameter",type:"string",size:"10",optional:!1,field:"flight_quote.pcc",description:"<p>pcc</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"flight_quote.fareType",description:"<p>ValidatingCarrier</p>"},{group:"Parameter",type:"object",optional:!1,field:"flight_quote.trips",description:"<p>Trips</p>"},{group:"Parameter",type:"int",optional:!0,field:"flight_quote.trips.duration",description:"<p>Trip Duration</p>"},{group:"Parameter",type:"object",optional:!1,field:"flight_quote.trips.segments",description:"<p>Segments</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"flight_quote.trips.segments.departureTime",description:"<p>DepartureTime</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"flight_quote.trips.segments.arrivalTime",description:"<p>ArrivalTime</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"flight_quote.trips.segments.departureAirportCode",description:"<p>Departure Airport Code IATA</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"flight_quote.trips.segments.arrivalAirportCode",description:"<p>Arrival Airport Code IATA</p>"},{group:"Parameter",type:"int",optional:!0,field:"flight_quote.trips.segments.flightNumber",description:"<p>Flight Number</p>"},{group:"Parameter",type:"string",size:"1",optional:!0,field:"flight_quote.trips.segments.bookingClass",description:"<p>BookingClass</p>"},{group:"Parameter",type:"int",optional:!0,field:"flight_quote.trips.segments.duration",description:"<p>Segment duration</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"flight_quote.trips.segments.departureAirportTerminal",description:"<p>Departure Airport Terminal Code</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"flight_quote.trips.segments.arrivalAirportTerminal",description:"<p>Arrival Airport Terminal Code</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"flight_quote.trips.segments.operatingAirline",description:"<p>Operating Airline</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"flight_quote.trips.segments.marketingAirline",description:"<p>Marketing Airline</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"flight_quote.trips.segments.airEquipType",description:"<p>AirEquipType</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"flight_quote.trips.segments.marriageGroup",description:"<p>MarriageGroup</p>"},{group:"Parameter",type:"int",optional:!0,field:"flight_quote.trips.segments.mileage",description:"<p>Mileage</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"flight_quote.trips.segments.meal",description:"<p>Meal</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"flight_quote.trips.segments.fareCode",description:"<p>Fare Code</p>"}]},examples:[{title:"Request-Example:",content:` {
    "booking_id": "XXXYYYZ",
    "is_automate": false,
    "project_key":"ovago",
    "flight_quote":{
               "gds": "S",
               "pcc": "8KI0",
               "validatingCarrier": "PR",
               "fareType": "SR",
               "itineraryDump":[
                   "DL8727E 10JUN ROBCDG TK  930P  730A+ 11JUN TH/FR",
                   "DL8395E 11JUN CDGLAX HK 1015A 1255P FR",
                   "DL3580E 11JUN LAXSMF HK  546P  714P FR",
                   "DL3864E 10SEP SMFSEA TK 1027A 1234P FR",
                   "DL 759E 10SEP SEAMSP TK  813A  134P FR",
                   "DL  42E 10SEP MSPCDG TK  445P  815A+ 11SEP FR/SA",
                   "DL7351E 11SEP CDGROB HK 1015A  450P SA",
                   "DL7351E 11SEP BKOROB HK  320P  450P SA"
               ],
               "trips":[
                   {
                       "duration":848,
                       "segments":[
                           {
                               "departureTime":"2021-06-10 21:30",
                               "arrivalTime":"2021-06-11 07:30",
                               "flightNumber":"8727",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":600,
                               "departureAirportCode":"ROB",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"CDG",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":null,
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           },
                           {
                               "departureTime":"2021-06-11 10:15",
                               "arrivalTime":"2021-06-11 12:55",
                               "flightNumber":"8395",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":160,
                               "departureAirportCode":"CDG",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"LAX",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":null,
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           },
                           {
                               "departureTime":"2021-06-11 17:46",
                               "arrivalTime":"2021-06-11 19:14",
                               "flightNumber":"3580",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":88,
                               "departureAirportCode":"LAX",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"SMF",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":null,
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           }
                       ]
                   },
                   {
                       "duration":1233,
                       "segments":[
                           {
                               "departureTime":"2021-09-10 10:27",
                               "arrivalTime":"2021-09-10 12:34",
                               "flightNumber":"3864",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":127,
                               "departureAirportCode":"SMF",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"SEA",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":"E7W",
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           },
                           {
                               "departureTime":"2021-09-10 08:13",
                               "arrivalTime":"2021-09-10 13:34",
                               "flightNumber":"759",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":201,
                               "departureAirportCode":"SEA",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"MSP",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":"739",
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           },
                           {
                               "departureTime":"2021-09-10 16:45",
                               "arrivalTime":"2021-09-11 08:15",
                               "flightNumber":"42",
                               "bookingClass":"E",
                               "stop":0,
                               "stops":[

                               ],
                               "duration":510,
                               "departureAirportCode":"MSP",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"CDG",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":"333",
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           },
                           {
                               "departureTime":"2021-09-11 10:15",
                               "arrivalTime":"2021-09-11 16:50",
                               "flightNumber":"7351",
                               "bookingClass":"E",
                               "stop":1,
                               "stops":[
                                   {
                                       "locationCode":"BKO",
                                       "departureDateTime":"2021-09-11 15:20",
                                       "arrivalDateTime":"2021-09-11 13:55",
                                       "duration":85,
                                       "elapsedTime":null,
                                       "equipment":null
                                   }
                               ],
                               "duration":395,
                               "departureAirportCode":"CDG",
                               "departureAirportTerminal":null,
                               "arrivalAirportCode":"ROB",
                               "arrivalAirportTerminal":null,
                               "operatingAirline":null,
                               "airEquipType":"359",
                               "marketingAirline":"DL",
                               "marriageGroup":"",
                               "mileage":null,
                               "cabin":"Y",
                               "meal":null,
                               "fareCode":null,
                               "baggage":[

                               ],
                               "brandId":null
                           }
                       ]
                   }
               ]
           }
         }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
           "resultMessage": "FlightRequest created",
           "id" => 12345
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "FlightRequest save is failed.",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v2/flight/reprotection-decision",title:"Reprotection decision",version:"0.2.0",name:"ReProtection_Decision",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"booking_id",description:"<p>Booking ID</p>"},{group:"Parameter",type:"string",allowedValues:['"confirm"','"modify"','"refund"'],optional:!1,field:"type",description:"<p>Re-protection Type</p>"},{group:"Parameter",type:"string",size:"32",optional:!0,field:"reprotection_quote_gid",description:"<p>Re-protection Product Quote GID (required for type = &quot;confirm&quot;, &quot;modify&quot;)</p>"},{group:"Parameter",type:"string",optional:!0,field:"flight_product_quote",description:"<p>Flight Quote Data (required for type = &quot;modify&quot;)</p>"}]},examples:[{title:"Request-Example:",content:` {
    "booking_id": "W12RT56",
    "type": "confirm",
    "reprotection_quote_gid": "94f95e797313c99d85d955373e408788",
    "flight_product_quote": "{}" // todo
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
           "success" => true
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "type": [
              "Type cannot be blank."
            ]
       ],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422) Code 101:",content:`HTTP/1.1 422 Error
{
       "status": 422,
       "message": "Error",
       "data": [
             "success": false,
             "error": "Product Quote Change status is not in \\"pending\\". Current status Canceled"
       ],
       "code": 101,
       "errors": [],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v2/flight/reprotection-exchange",title:"ReProtection exchange",version:"0.2.0",name:"ReProtection_Exchange",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"booking_id",description:"<p>Booking ID</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"email",description:"<p>Email</p>"},{group:"Parameter",type:"string",size:"20",optional:!0,field:"phone",description:"<p>Phone</p>"},{group:"Parameter",type:"object",optional:!0,field:"flight_request",description:"<p>Flight Request</p>"}]},examples:[{title:"Request-Example:",content:`{
    "booking_id": "XXXYYYZ",
    "email": "example@mail.com",
    "phone": "+13736911111",
    "flight_request": {"exampleKey" : "exampleValue"}
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
           "success" => true,
           "warnings": []
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "type": [
              "Type cannot be blank."
            ]
       ],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422) Code 101:",content:`HTTP/1.1 422 Error
{
       "status": 422,
       "message": "Error",
       "data": [
             "success": false,
             "error": "Product Quote Change status is not in \\"pending\\". Current status Canceled"
       ],
       "code": 101,
       "errors": [],
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightController.php",groupTitle:"Flight"},{type:"get",url:"/v2/flight/reprotection-get",title:"Get flight reprotection",version:"0.1.0",name:"ReProtection_Get",group:"Flight",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"32",optional:!1,field:"flight_product_quote_gid",description:"<p>Flight Product Quote gid</p>"}]},examples:[{title:"Request-Example:",content:` {
    "flight_product_quote_gid": "2bd12377691f282e11af12937674e3d1",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
            "status": 200,
            "message": "OK",
            "origin_product_quote": {
                "pq_gid": "22c3c0c2982108117d1952f317f568a3",
                "pq_name": "",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 1554.4,
                "pq_origin_price": 1414.4,
                "pq_client_price": 1554.4,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 344,
                    "fq_source_id": null,
                    "fq_product_quote_id": 775,
                    "gds": "T",
                    "pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 3,
                    "validatingCarrier": "OS",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjItMDEtMTIvTE9ORlJBMjAyMi0wMS0xNS9GUkFLSVYyMDIyLTAxLTI0Kk9TfiNPUzY1NiNPUzQ1NSNMSDkwNSNMSDE0NzR+bGM6ZW5fdXM=\\",\\"routingId\\":1,\\"prices\\":{\\"lastTicketDate\\":\\"2021-07-31\\",\\"totalPrice\\":1414.4,\\"totalTax\\":872.4,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":2,\\"baseFare\\":197,\\"pubBaseFare\\":197,\\"baseTax\\":296.8,\\"markup\\":0,\\"comm\\":0,\\"price\\":493.8,\\"tax\\":296.8,\\"oBaseFare\\":{\\"amount\\":197,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":296.8,\\"currency\\":\\"USD\\"}},\\"CHD\\":{\\"codeAs\\":\\"CHD\\",\\"cnt\\":1,\\"baseFare\\":148,\\"pubBaseFare\\":148,\\"baseTax\\":278.8,\\"markup\\":0,\\"comm\\":0,\\"price\\":426.8,\\"tax\\":278.8,\\"oBaseFare\\":{\\"amount\\":148,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":278.8,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":true,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"ex\\",\\"applicability\\":\\"before\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"ex\\",\\"applicability\\":\\"after\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2022-01-12 16:00\\",\\"arrivalTime\\":\\"2022-01-12 16:45\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"656\\",\\"bookingClass\\":\\"K\\",\\"duration\\":105,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"VIE\\",\\"arrivalAirportTerminal\\":\\"3\\",\\"operatingAirline\\":\\"OS\\",\\"airEquipType\\":\\"E95\\",\\"marketingAirline\\":\\"OS\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":583,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"K03CLSE8\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2022-01-12 17:15\\",\\"arrivalTime\\":\\"2022-01-12 18:40\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"455\\",\\"bookingClass\\":\\"K\\",\\"duration\\":145,\\"departureAirportCode\\":\\"VIE\\",\\"departureAirportTerminal\\":\\"3\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"OS\\",\\"airEquipType\\":\\"321\\",\\"marketingAirline\\":\\"OS\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":774,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"K03CLSE8\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":280},{\\"tripId\\":2,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2022-01-15 11:30\\",\\"arrivalTime\\":\\"2022-01-15 14:05\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"905\\",\\"bookingClass\\":\\"Q\\",\\"duration\\":95,\\"departureAirportCode\\":\\"LHR\\",\\"departureAirportTerminal\\":\\"2\\",\\"arrivalAirportCode\\":\\"FRA\\",\\"arrivalAirportTerminal\\":\\"1\\",\\"operatingAirline\\":\\"LH\\",\\"airEquipType\\":\\"32N\\",\\"marketingAirline\\":\\"LH\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":390,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"Q03CLSE0\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":95},{\\"tripId\\":3,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2022-01-24 09:45\\",\\"arrivalTime\\":\\"2022-01-24 13:05\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"1474\\",\\"bookingClass\\":\\"Q\\",\\"duration\\":140,\\"departureAirportCode\\":\\"FRA\\",\\"departureAirportTerminal\\":\\"1\\",\\"arrivalAirportCode\\":\\"KIV\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"CL\\",\\"opName\\":\\"LUFTHANSA CITYLINE GMBH\\",\\"airEquipType\\":\\"E90\\",\\"marketingAirline\\":\\"LH\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":953,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"Q03CLSE0\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":140}],\\"maxSeats\\":9,\\"paxCnt\\":3,\\"validatingCarrier\\":\\"OS\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"MC\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"23d8e32d-b8eb-4578-9928-4674761747d6\\",\\"availabilitySources\\":\\"Q,Q,S,S\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":9}},\\"ngsFeatures\\":{\\"stars\\":1,\\"name\\":\\"BASIC\\",\\"list\\":[]},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMi0wMS0xMnxMT05GUkEyMDIyLTAxLTE1fEZSQUtJVjIwMjItMDEtMjQ=\\",\\"lang\\":\\"en\\",\\"rank\\":10,\\"cheapest\\":true,\\"fastest\\":true,\\"best\\":true,\\"bags\\":1,\\"country\\":\\"us\\",\\"prod_types\\":[\\"PUB\\"]},\\"price\\":493.8,\\"originRate\\":1,\\"stops\\":[1,0,0],\\"time\\":[{\\"departure\\":\\"2022-01-12 16:00\\",\\"arrival\\":\\"2022-01-12 18:40\\"},{\\"departure\\":\\"2022-01-15 11:30\\",\\"arrival\\":\\"2022-01-15 14:05\\"},{\\"departure\\":\\"2022-01-24 09:45\\",\\"arrival\\":\\"2022-01-24 13:05\\"}],\\"bagFilter\\":1,\\"airportChange\\":false,\\"technicalStopCnt\\":0,\\"duration\\":[280,95,140],\\"totalDuration\\":515,\\"topCriteria\\":\\"fastestbestcheapest\\",\\"rank\\":10}",
                    "fq_last_ticket_date": "2021-07-31",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "O230850",
                    "fq_type_name": "Base",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 688,
                        "fl_trip_type_id": 3,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 1,
                        "fl_infants": 0,
                        "fl_trip_type_name": "Multi destination",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "uid": "fqt6103c94699a2e",
                            "key": null,
                            "duration": 280,
                            "segments": [
                                {
                                    "uid": "fqs6103c9469c3c8",
                                    "departureTime": "2022-01-12 16:00",
                                    "arrivalTime": "2022-01-12 16:45",
                                    "flightNumber": 656,
                                    "bookingClass": "K",
                                    "duration": 105,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "VIE",
                                    "arrivalAirportTerminal": "3",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "E95",
                                    "marriageGroup": "I",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "mileage": 583,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Vienna",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 1,
                                    "stops": [
                                        {
                                            "qss_quote_segment_id": 9,
                                            "locationCode": "SCL",
                                            "equipment": "",
                                            "elapsedTime": 120,
                                            "duration": 120,
                                            "departureDateTime": "2021-09-09 00:00",
                                            "arrivalDateTime": "2021-09-08 00:00"
                                        }
                                    ],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1076,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1076,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6103c9469e37b",
                                    "departureTime": "2022-01-12 17:15",
                                    "arrivalTime": "2022-01-12 18:40",
                                    "flightNumber": 455,
                                    "bookingClass": "K",
                                    "duration": 145,
                                    "departureAirportCode": "VIE",
                                    "departureAirportTerminal": "3",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "2",
                                    "fqs_operating_airline": "OS",
                                    "fqs_marketing_airline": "OS",
                                    "airEquipType": "321",
                                    "marriageGroup": "O",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "mileage": 774,
                                    "departureLocation": "Vienna",
                                    "arrivalLocation": "London",
                                    "cabin": "E",
                                    "operatingAirline": "OS",
                                    "marketingAirline": "OS",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1077,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1077,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "uid": "fqt6103c9469f378",
                            "key": null,
                            "duration": 95,
                            "segments": [
                                {
                                    "uid": "fqs6103c9469fa85",
                                    "departureTime": "2022-01-15 11:30",
                                    "arrivalTime": "2022-01-15 14:05",
                                    "flightNumber": 905,
                                    "bookingClass": "Q",
                                    "duration": 95,
                                    "departureAirportCode": "LHR",
                                    "departureAirportTerminal": "2",
                                    "arrivalAirportCode": "FRA",
                                    "arrivalAirportTerminal": "1",
                                    "fqs_operating_airline": "LH",
                                    "fqs_marketing_airline": "LH",
                                    "airEquipType": "32N",
                                    "marriageGroup": "O",
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "Q03CLSE0",
                                    "mileage": 390,
                                    "departureLocation": "London",
                                    "arrivalLocation": "Frankfurt am Main",
                                    "operatingAirline": "LH",
                                    "marketingAirline": "LH",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1078,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1078,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "uid": "fqt6103c946a08d6",
                            "key": null,
                            "duration": 140,
                            "segments": [
                                {
                                    "uid": "fqs6103c946a0d33",
                                    "departureTime": "2022-01-24 09:45",
                                    "arrivalTime": "2022-01-24 13:05",
                                    "flightNumber": 1474,
                                    "bookingClass": "Q",
                                    "duration": 140,
                                    "departureAirportCode": "FRA",
                                    "departureAirportTerminal": "1",
                                    "arrivalAirportCode": "KIV",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "E90",
                                    "marriageGroup": "O",
                                    "meal": "",
                                    "fareCode": "Q03CLSE0",
                                    "mileage": 953,
                                    "departureLocation": "Frankfurt am Main",
                                    "arrivalLocation": "Chisinau",
                                    "cabin": "E",
                                    "operatingAirline": "LH",
                                    "marketingAirline": "LH",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1079,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1079,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "197.00",
                            "qpp_tax": "296.80",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "70.00",
                            "qpp_origin_fare": "197.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "296.80",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "197.00",
                            "qpp_client_tax": "296.80",
                            "paxType": "ADT"
                        },
                        {
                            "qpp_fare": "148.00",
                            "qpp_tax": "278.80",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "148.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "278.80",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "148.00",
                            "qpp_client_tax": "278.80",
                            "paxType": "CHD"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6103c94694091",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6103c946948e9",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6103c94695639",
                            "fp_pax_id": null,
                            "fp_pax_type": "CHD",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                }
            },
            "reprotection_product_quote": {
                "pq_gid": "2bd12377691f282e11af12937674e3d1",
                "pq_name": "",
                "pq_order_id": 544,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 274.7,
                "pq_origin_price": 259.86,
                "pq_client_price": 274.7,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 343,
                    "fq_source_id": null,
                    "fq_product_quote_id": 774,
                    "gds": "C",
                    "pcc": "default",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "validatingCarrier": "RO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDctMjkqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\\",\\"routingId\\":2,\\"prices\\":{\\"lastTicketDate\\":\\"2021-07-28 23:59\\",\\"totalPrice\\":302.9,\\"totalTax\\":81.5,\\"comm\\":0,\\"isCk\\":true,\\"CkAmount\\":14.84,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":14.84},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":2,\\"baseFare\\":110.7,\\"pubBaseFare\\":110.7,\\"baseTax\\":33.33,\\"markup\\":7.42,\\"comm\\":0,\\"CkAmount\\":7.42,\\"price\\":151.45,\\"tax\\":40.75,\\"oBaseFare\\":{\\"amount\\":92,\\"currency\\":\\"EUR\\"},\\"oBaseTax\\":{\\"amount\\":27.7,\\"currency\\":\\"EUR\\"},\\"oCkAmount\\":{\\"amount\\":6.17,\\"currency\\":\\"EUR\\"}}},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-07-29 09:30\\",\\"arrivalTime\\":\\"2021-07-29 10:45\\",\\"stop\\":0,\\"stops\\":null,\\"flightNumber\\":\\"202\\",\\"bookingClass\\":\\"E\\",\\"duration\\":75,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"OTP\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"AT7\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"\\",\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"EOWSVRMD\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2021-07-29 12:20\\",\\"arrivalTime\\":\\"2021-07-29 14:05\\",\\"stop\\":0,\\"stops\\":null,\\"flightNumber\\":\\"391\\",\\"bookingClass\\":\\"E\\",\\"duration\\":225,\\"departureAirportCode\\":\\"OTP\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"318\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"\\",\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"EOWSVRGB\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":395}],\\"maxSeats\\":3,\\"paxCnt\\":2,\\"validatingCarrier\\":\\"RO\\",\\"gds\\":\\"C\\",\\"pcc\\":\\"default\\",\\"cons\\":\\"AER\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\",\\"EUR\\"],\\"currencyRates\\":{\\"EURUSD\\":{\\"from\\":\\"EUR\\",\\"to\\":\\"USD\\",\\"rate\\":1.20328},\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"cockpit\\":{\\"itineraryIds\\":[\\"D3537439481d_ROUNDTRIP_0_0_0_0\\"],\\"fareIds\\":[\\"D3537439481d_ROUNDTRIP_0\\"],\\"webServiceLogId\\":\\"EM483101d9441a09d\\",\\"sessionId\\":\\"3af91858-e306-4b40-83af-108c593f2a36\\",\\"type\\":\\"C\\"}},\\"ngsFeatures\\":{\\"stars\\":1,\\"name\\":\\"BASIC\\",\\"list\\":[]},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wNy0yOQ==\\",\\"lang\\":\\"en\\",\\"rank\\":8.987654,\\"cheapest\\":false,\\"fastest\\":false,\\"best\\":false,\\"bags\\":1,\\"country\\":\\"us\\",\\"prod_types\\":[\\"PUB\\"]},\\"price\\":151.45,\\"originRate\\":1,\\"stops\\":[1],\\"time\\":[{\\"departure\\":\\"2021-07-29 09:30\\",\\"arrival\\":\\"2021-07-29 14:05\\"}],\\"bagFilter\\":1,\\"airportChange\\":false,\\"technicalStopCnt\\":0,\\"duration\\":[395],\\"totalDuration\\":395,\\"topCriteria\\":\\"\\",\\"rank\\":8.987654}",
                    "fq_last_ticket_date": "2021-07-28",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "O230851",
                    "fq_type_name": "Base",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 687,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "uid": "fqt61015f35534ec",
                            "key": null,
                            "duration": 395,
                            "segments": [
                                {
                                    "uid": "fqs61015f3554892",
                                    "departureTime": "2021-07-29 09:30",
                                    "arrivalTime": "2021-07-29 10:45",
                                    "flightNumber": 202,
                                    "bookingClass": "E",
                                    "duration": 75,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "OTP",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "AT7",
                                    "marriageGroup": "",
                                    "meal": "",
                                    "fareCode": "EOWSVRMD",
                                    "mileage": null,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Bucharest",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1074,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs61015f35565ef",
                                    "departureTime": "2021-07-29 12:20",
                                    "arrivalTime": "2021-07-29 14:05",
                                    "flightNumber": 391,
                                    "bookingClass": "E",
                                    "duration": 225,
                                    "departureAirportCode": "OTP",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "318",
                                    "marriageGroup": "",
                                    "meal": "",
                                    "fareCode": "EOWSVRGB",
                                    "mileage": null,
                                    "departureLocation": "Bucharest",
                                    "arrivalLocation": "London",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1075,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "99.86",
                            "qpp_tax": "30.07",
                            "qpp_system_mark_up": "7.42",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "110.70",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "33.33",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "99.86",
                            "qpp_client_tax": "30.07",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp61015f33cccbd",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f33cd1f4",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f354f612",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f354f948",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                }
            },
            "order": {
                "or_id": 544,
                "or_gid": "3b78e38c2ae14e4ad282cf3abc652140",
                "or_uid": "or61015f39e2d71",
                "or_name": "Order 1",
                "or_description": null,
                "or_status_id": 2,
                "or_pay_status_id": 1,
                "or_app_total": "274.70",
                "or_app_markup": "14.84",
                "or_agent_markup": "0.00",
                "or_client_total": "274.70",
                "or_client_currency": "USD",
                "or_client_currency_rate": "1.00000",
                "or_status_name": "Pending",
                "or_pay_status_name": "Not paid",
                "or_client_currency_symbol": "USD",
                "or_files": [],
                "or_request_uid": null,
                "billing_info": []
            },
            "order_contacts": []
        }`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 200 Ok
{
            "status": 422,
            "message": "Product Quote not found",
            "errors": [
                "Product Quote not found"
            ],
            "code": 0
        }`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 200 Ok
{
            "status": 500,
            "message": "Internal Server Error",
            "code": 8,
            "errors": []
        }`,type:"json"}]},filename:"v2/controllers/FlightController.php",groupTitle:"Flight"},{type:"post",url:"/v2/flight-quote-exchange/confirm",title:"Flight Voluntary Exchange Confirm",version:"0.2.0",name:"Flight_Voluntary_Exchange_Confirm",group:"Flight_Voluntary_Exchange",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"booking_id",description:"<p>Booking ID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"quote_gid",description:"<p>Product Quote GID</p>"},{group:"Parameter",type:"object",optional:!0,field:"billing",description:"<p>Billing</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.last_name",description:"<p>Last name</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"billing.middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.company_name",description:"<p>Company</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"billing.address_line1",description:"<p>Address line 1</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"billing.address_line2",description:"<p>Address line 2</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"billing.country_id",description:"<p>Country code (for example &quot;US&quot;)</p>"},{group:"Parameter",type:"string",optional:!1,field:"billing.country",description:"<p>Country name</p>"},{group:"Parameter",type:"string",size:"10",optional:!0,field:"billing.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"billing.contact_phone",description:"<p>Contact phone</p>"},{group:"Parameter",type:"string",size:"160",optional:!1,field:"billing.contact_email",description:"<p>Contact email</p>"},{group:"Parameter",type:"string",size:"60",optional:!0,field:"billing.contact_name",description:"<p>Contact name</p>"},{group:"Parameter",type:"object",optional:!0,field:"payment_request",description:"<p>Payment request</p>"},{group:"Parameter",type:"number",optional:!1,field:"payment_request.amount",description:"<p>Customer must pay for initiate refund process</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"payment_request.currency",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"payment_request.method_key",description:"<p>Method key (for example &quot;card&quot;)</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data",description:"<p>Method data</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data.card",description:"<p>Card (for credit card)</p>"},{group:"Parameter",type:"string",size:"..20",optional:!1,field:"payment_request.method_data.card.number",description:"<p>Number</p>"},{group:"Parameter",type:"string",size:"..50",optional:!0,field:"payment_request.method_data.card.holder_name",description:"<p>Holder name</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_month",description:"<p>Month</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_year",description:"<p>Year</p>"},{group:"Parameter",type:"string",size:"..4",optional:!1,field:"payment_request.method_data.card.cvv",description:"<p>CVV</p>"}]},examples:[{title:"Request-Example:",content:` {
    "booking_id":"XXXYYYZ",
    "quote_gid": "2f2887a061f8069f7ada8af9e062f0f4",
    "billing": {
          "first_name": "John",
          "last_name": "Doe",
          "middle_name": "",
          "address_line1": "1013 Weda Cir",
          "address_line2": "",
          "country_id": "US",
          "country" : "United States",
          "city": "Mayfield",
          "state": "KY",
          "zip": "99999",
          "company_name": "",
          "contact_phone": "+19074861000",
          "contact_email": "test@test.com",
          "contact_name": "Test Name"
    },
    "payment_request": {
          "method_key": "card",
          "currency": "USD",
          "method_data": {
              "card": {
                  "number": "4111555577778888",
                  "holder_name": "Test test",
                  "expiration_month": 10,
                  "expiration_year": 23,
                  "cvv": "123"
              }
          },
          "amount": 112.25
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
                    "resultMessage": "Processing was successful",
                    "originQuoteGid" : "a1275b33cda3bbcbeea2d684475a7e8a",
                    "changeQuoteGid" : "5c63db4e9d4d24f480088fd5e194e4f5",
                    "productQuoteChangeGid" : "ee61d0abb62d96879e2c29ddde403650",
                    "caseGid" : "e7dce13b4e6a5f3ccc2cec9c21fa3255"
               },
       "code": "13200",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "code": "13106",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "booking_id": [
              "booking_id cannot be blank."
            ]
       ],
       "code": "13107",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Codes designation",content:`[
     13101 - Api User has no related project
     13106 - Post has not loaded
     13107 - Validation Failed

     13113 - Product Quote not available for exchange
     13130 - Request to Back Office is failed

     150406 - Prepare Data for Request is failed; CRM processing errors

     601 - BO Server Error: i.e. request timeout
     602 - BO response body is empty
     603 - BO response type is invalid (not array)
     604 - BO wrong endpoint
]`,type:"html"}]},filename:"v2/controllers/FlightQuoteExchangeController.php",groupTitle:"Flight_Voluntary_Exchange"},{type:"post",url:"/v2/flight-quote-exchange/create",title:"Flight Voluntary Exchange Create",version:"0.2.0",name:"Flight_Voluntary_Exchange_Create",group:"Flight_Voluntary_Exchange",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"bookingId",description:"<p>Booking ID</p>"},{group:"Parameter",type:"string",size:"150",optional:!1,field:"apiKey",description:"<p>ApiKey (Project API Key)</p>"},{group:"Parameter",type:"object",optional:!1,field:"exchange",description:"<p>Exchange Data Info</p>"},{group:"Parameter",type:"object",optional:!1,field:"exchange.prices",description:"<p>Prices</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.prices.totalPrice",description:"<p>Total Price (total for exchange pay)</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.prices.comm",description:"<p>Comm</p>"},{group:"Parameter",type:"bool",optional:!1,field:"exchange.prices.isCk",description:"<p>isCk</p>"},{group:"Parameter",type:"object",optional:!1,field:"exchange.tickets",description:"<p>Tickets</p>"},{group:"Parameter",type:"string",optional:!1,field:"exchange.tickets.numRef",description:"<p>NumRef</p>"},{group:"Parameter",type:"string",optional:!1,field:"exchange.tickets.firstName",description:"<p>FirstName</p>"},{group:"Parameter",type:"string",optional:!1,field:"exchange.tickets.lastName",description:"<p>LastName</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.tickets.paxType",description:"<p>paxType</p>"},{group:"Parameter",type:"string",optional:!1,field:"exchange.tickets.number",description:"<p>Number</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.passengers",description:"<p>Passengers</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.passengers.ADT",description:"<p>Pax Type (ADT,CHD,INF)</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.passengers.ADT.codeAs",description:"<p>Pax Type Code</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.passengers.ADT.cnt",description:"<p>Cnt</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.baseFare",description:"<p>Base Fare (diffFare)</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.pubBaseFare",description:"<p>Pub Base Fare</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.baseTax",description:"<p>Base Tax (airlinePenalty)</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.markup",description:"<p>Markup (processingFee)</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.comm",description:"<p>Comm</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.price",description:"<p>Price (total for exchange pay)</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.tax",description:"<p>Tax</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.passengers.ADT.oBaseFare",description:"<p>oBaseFare</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.oBaseFare.amount",description:"<p>oBaseFare Amount</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.passengers.ADT.oBaseFare.currency",description:"<p>oBaseFare Currency</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.passengers.ADT.oBaseTax",description:"<p>oBaseTax</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.oBaseTax.amount",description:"<p>oBaseTax Amount</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.passengers.ADT.oBaseTax.currency",description:"<p>oBaseTax Currency</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.passengers.ADT.oExchangeFareDiff",description:"<p>oExchangeFareDiff</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.passengers.ADT.oExchangeFareDiff.amount",description:"<p>oExchangeFareDiff Amount</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.passengers.ADT.oExchangeFareDiff.currency",description:"<p>oExchangeFareDiff Currency</p>"},{group:"Parameter",type:"object",optional:!1,field:"exchange.passengers.ADT.oExchangeTaxDiff",description:"<p>oExchangeTaxDiff</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"exchange.trips",description:"<p>Trips</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.tripId",description:"<p>Trip Id</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"exchange.trips.segments",description:"<p>Segments</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.segments.segmentId",description:"<p>Segment Id</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"exchange.trips.segments.departureTime",description:"<p>DepartureTime</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"exchange.trips.segments.arrivalTime",description:"<p>ArrivalTime</p>"},{group:"Parameter",type:"int",optional:!0,field:"exchange.trips.segments.stop",description:"<p>Stop</p>"},{group:"Parameter",type:"object[]",optional:!0,field:"exchange.trips.segments.stops",description:"<p>Stops</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.trips.segments.stops.locationCode",description:"<p>Location Code</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"exchange.trips.segments.stops.departureDateTime",description:"<p>Departure DateTime</p>"},{group:"Parameter",type:"string",size:"format Y-m-d H:i",optional:!1,field:"exchange.trips.segments.stops.arrivalDateTime",description:"<p>Departure DateTime</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.segments.stops.duration",description:"<p>Duration</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.segments.stops.elapsedTime",description:"<p>Elapsed Time</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.segments.stops.equipment",description:"<p>Equipment</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.trips.segments.departureAirportCode",description:"<p>Departure Airport Code IATA</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.trips.segments.arrivalAirportCode",description:"<p>Arrival Airport Code IATA</p>"},{group:"Parameter",type:"string",size:"5}",optional:!1,field:"exchange.trips.segments.flightNumber",description:"<p>Flight Number</p>"},{group:"Parameter",type:"string",size:"1",optional:!1,field:"exchange.trips.segments.bookingClass",description:"<p>BookingClass</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.trips.segments.duration",description:"<p>Segment duration</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.trips.segments.departureAirportTerminal",description:"<p>Departure Airport Terminal Code</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"exchange.trips.segments.arrivalAirportTerminal",description:"<p>Arrival Airport Terminal Code</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"exchange.trips.segments.operatingAirline",description:"<p>Operating Airline</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"exchange.trips.segments.marketingAirline",description:"<p>Marketing Airline</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"exchange.trips.segments.airEquipType",description:"<p>AirEquipType</p>"},{group:"Parameter",type:"string",size:"3",optional:!0,field:"exchange.trips.segments.marriageGroup",description:"<p>MarriageGroup</p>"},{group:"Parameter",type:"int",optional:!0,field:"exchange.trips.segments.mileage",description:"<p>Mileage</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"exchange.trips.segments.meal",description:"<p>Meal</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"exchange.trips.segments.fareCode",description:"<p>Fare Code</p>"},{group:"Parameter",type:"bool",optional:!0,field:"exchange.trips.segments.recheckBaggage",description:"<p>Recheck Baggage</p>"},{group:"Parameter",type:"int",optional:!1,field:"exchange.paxCnt",description:"<p>Pax Cnt</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"exchange.validatingCarrier",description:"<p>ValidatingCarrier</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"exchange.gds",description:"<p>Gds</p>"},{group:"Parameter",type:"string",size:"10",optional:!1,field:"exchange.pcc",description:"<p>pcc</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"exchange.fareType",description:"<p>Fare Type</p>"},{group:"Parameter",type:"string",size:"1",optional:!1,field:"exchange.cabin",description:"<p>Cabin</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.cons",description:"<p>Consolidator</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.currency",description:"<p>Currency</p>"},{group:"Parameter",type:"array[]",optional:!0,field:"exchange.currencies",description:"<p>Currencies (For example [USD])</p>"},{group:"Parameter",type:"object[]",optional:!0,field:"exchange.currencyRates",description:"<p>CurrencyRates</p>"},{group:"Parameter",type:"string",size:"6",optional:!1,field:"exchange.currencyRates.USDUSD",description:"<p>Currency Codes</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.currencyRates.USDUSD.from",description:"<p>Currency Code</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"exchange.currencyRates.USDUSD.to",description:"<p>Currency Code</p>"},{group:"Parameter",type:"number",optional:!1,field:"exchange.currencyRates.USDUSD.rate",description:"<p>Rate</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.keys",description:"<p>Keys</p>"},{group:"Parameter",type:"object",optional:!0,field:"exchange.meta",description:"<p>Meta</p>"},{group:"Parameter",type:"object",optional:!0,field:"billing",description:"<p>Billing</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.last_name",description:"<p>Last name</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"billing.middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.company_name",description:"<p>Company</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"billing.address_line1",description:"<p>Address line 1</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"billing.address_line2",description:"<p>Address line 2</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"billing.country_id",description:"<p>Country code (for example &quot;US&quot;)</p>"},{group:"Parameter",type:"string",optional:!1,field:"billing.country",description:"<p>Country name</p>"},{group:"Parameter",type:"string",size:"10",optional:!0,field:"billing.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"billing.contact_phone",description:"<p>Contact phone</p>"},{group:"Parameter",type:"string",size:"160",optional:!1,field:"billing.contact_email",description:"<p>Contact email</p>"},{group:"Parameter",type:"string",size:"60",optional:!0,field:"billing.contact_name",description:"<p>Contact name</p>"},{group:"Parameter",type:"object",optional:!0,field:"payment_request",description:"<p>Payment request</p>"},{group:"Parameter",type:"number",optional:!1,field:"payment_request.amount",description:"<p>Customer must pay for initiate refund process</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"payment_request.currency",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"payment_request.method_key",description:"<p>Method key (for example &quot;card&quot;)</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data",description:"<p>Method data</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data.card",description:"<p>Card (for credit card)</p>"},{group:"Parameter",type:"string",size:"..20",optional:!1,field:"payment_request.method_data.card.number",description:"<p>Number</p>"},{group:"Parameter",type:"string",size:"..50",optional:!0,field:"payment_request.method_data.card.holder_name",description:"<p>Holder name</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_month",description:"<p>Month</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_year",description:"<p>Year</p>"},{group:"Parameter",type:"string",size:"..4",optional:!1,field:"payment_request.method_data.card.cvv",description:"<p>CVV</p>"}]},examples:[{title:"Request-Example:",content:` {
    "bookingId": "XXXYYYZ",
    "apiKey": "test-api-key",
    "exchange": {
        "trips": [
            {
                "tripId": 1,
                "segments": [
                    {
                        "segmentId": 1,
                        "departureTime": "2022-01-10 20:15",
                        "arrivalTime": "2022-01-11 21:10",
                        "stop": 0,
                        "stops": [
                            {
                                "locationCode": "LFW",
                                "departureDateTime": "2022-01-11 12:35",
                                "arrivalDateTime": "2022-01-11 11:35",
                                "duration": 60,
                                "elapsedTime": 620,
                                "equipment": "787"
                            }
                        ],
                        "flightNumber": "513",
                        "bookingClass": "H",
                        "duration": 1015,
                        "departureAirportCode": "JFK",
                        "departureAirportTerminal": "8",
                        "arrivalAirportCode": "ADD",
                        "arrivalAirportTerminal": "2",
                        "operatingAirline": "ET",
                        "airEquipType": "787",
                        "marketingAirline": "ET",
                        "marriageGroup": "O",
                        "cabin": "Y",
                        "meal": "DL",
                        "fareCode": "HLESUS",
                        "recheckBaggage": false
                    },
                    {
                        "segmentId": 2,
                        "departureTime": "2022-01-11 23:15",
                        "arrivalTime": "2022-01-12 01:20",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "308",
                        "bookingClass": "H",
                        "duration": 125,
                        "departureAirportCode": "ADD",
                        "departureAirportTerminal": "2",
                        "arrivalAirportCode": "NBO",
                        "arrivalAirportTerminal": "1C",
                        "operatingAirline": "ET",
                        "airEquipType": "738",
                        "marketingAirline": "ET",
                        "marriageGroup": "I",
                        "cabin": "Y",
                        "meal": "D",
                        "fareCode": "HLESUS",
                        "recheckBaggage": false
                    }
                ],
                "duration": 1265
            }
        ],
        "tickets": [
            {
                "numRef": "1.1",
                "firstName": "PAULA ANNE",
                "lastName": "ALVAREZ",
                "paxType": "ADT",
                "number": "123456789"
            },
            {
                "numRef": "2.1",
                "firstName": "ANNE",
                "lastName": "ALVAREZ",
                "paxType": "ADT",
                "number": "987654321"
            }
        ],
        "passengers": {
            "ADT": {
                "codeAs": "JCB",
                "cnt": 1,
                "baseFare": 32.12,
                "pubBaseFare": 32.12,
                "baseTax": 300,
                "markup": 0,
                "comm": 0,
                "price": 332.12,
                "tax": 300,
                "oBaseFare": {
                    "amount": 32.120003,
                    "currency": "USD"
                },
                "oBaseTax": {
                    "amount": 300,
                    "currency": "USD"
                },
                "oExchangeFareDiff": {
                    "amount": 8,
                    "currency": "USD"
                },
                "oExchangeTaxDiff": {
                    "amount": 24.12,
                    "currency": "USD"
                }
            }
        },
        "validatingCarrier": "AA",
        "gds": "S",
        "pcc": "G9MJ",
        "cons": "GTT",
        "fareType": "SR",
        "cabin": "Y",
        "currency": "USD",
        "currencies": [
            "USD"
        ],
        "currencyRates": {
            "USDUSD": {
                "from": "USD",
                "to": "USD",
                "rate": 1
            }
        },
        "keys": {},
        "meta": {}
    },
    "billing": {
          "first_name": "John",
          "last_name": "Doe",
          "middle_name": "",
          "address_line1": "1013 Weda Cir",
          "address_line2": "",
          "country_id": "US",
          "country" : "United States",
          "city": "Mayfield",
          "state": "KY",
          "zip": "99999",
          "company_name": "",
          "contact_phone": "+19074861000",
          "contact_email": "test@test.com",
          "contact_name": "Test Name"
    },
    "payment_request": {
          "method_key": "card",
          "currency": "USD",
          "method_data": {
              "card": {
                  "number": "4111555577778888",
                  "holder_name": "Test test",
                  "expiration_month": 10,
                  "expiration_year": 23,
                  "cvv": "123"
              }
          },
          "amount": 112.25
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
                    "resultMessage": "Processing was successful",
                    "originQuoteGid" : "a1275b33cda3bbcbeea2d684475a7e8a",
                    "changeQuoteGid" : "5c63db4e9d4d24f480088fd5e194e4f5",
                    "productQuoteChangeGid" : "ee61d0abb62d96879e2c29ddde403650",
                    "caseGid" : "e7dce13b4e6a5f3ccc2cec9c21fa3255"
               },
       "code": "13200",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (Bad Request):",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "code": "13106",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (Bad Request):",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Error",
       "errors": [
          "Not found Project with current user: xxx"
       ],
       "code": "13101",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (Unprocessable entity):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "bookingId": [
              "bookingId cannot be blank."
            ]
       ],
       "code": "13107",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (Unprocessable entity):",content:`HTTP/1.1 422 Unprocessable entity
{
     "status": 422,
     "message": "Error",
     "errors": [
         "Product Quote not available for exchange"
     ],
     "code": "13113",
     "technical": {
        ...
     },
     "request": {
        ...
     }
}`,type:"json"},{title:"Error-Response (Unprocessable entity):",content:`HTTP/1.1 422 Unprocessable entity
{
     "status": 422,
     "message": "Error",
     "errors": [
         "Case saving error"
     ],
     "code": "21101",
     "technical": {
        ...
     },
     "request": {
        ...
     }
}`,type:"json"},{title:"Error-Response (Internal Server Error):",content:`HTTP/1.1 500 Internal Server Error
{
     "status": 500,
     "message": "Error",
     "errors": [
         "Server Error"
     ],
     "code": 0,
     "technical": {
        ...
     },
     "request": {
        ...
     }
}`,type:"json"},{title:"Codes designation",content:`[
     13101 - Api User has no related project
     13106 - Post has not loaded
     13107 - Validation Failed

     13113 - Product Quote not available for exchange

     15401 - Case creation failed; CRM processing error
     15402 - Case Sale creation failed; CRM processing error
     15403 - Client creation failed; CRM processing error
     15404 - Order creation failed; CRM processing error
     15405 - Origin Product Quote creation failed; CRM processing errors

     601 - BO Server Error: i.e. request timeout
     602 - BO response body is empty
     603 - BO response type is invalid (not array)
     604 - BO wrong endpoint
]`,type:"html"}]},filename:"v2/controllers/FlightQuoteExchangeController.php",groupTitle:"Flight_Voluntary_Exchange"},{type:"post",url:"/v2/flight-quote-exchange/info",title:"Flight Voluntary Exchange Info",version:"0.2.0",name:"Flight_Voluntary_Exchange_Info",group:"Flight_Voluntary_Exchange",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"booking_id",description:"<p>Booking ID</p>"}]},examples:[{title:"Request-Example:",content:`{
    "booking_id": "XXXYYYZ"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
                "bookingId": "XXXYYYZ",
                "quote_gid" : "48c82774ead469ad311c1e6112562726",
                "key": "51_U1NTMTAxKlkxMDAwL0pGS05CTzIwMjItMDEtMTAvTkJPSkZLMjAyMi0wMS0zMSp+I0VUNTEzI0VUMzA4I0VUMzA5I0VUNTEyfmxjOmVuX3VzOkVYXzE3Yz123456789",
                "prices": {
                    "totalPrice": 332.12,
                    "comm": 0,
                    "isCk": false
                },
                "passengers": {
                    "ADT": {
                        "codeAs": "JCB",
                        "cnt": 1,
                        "baseFare": 32.12,
                        "pubBaseFare": 32.12,
                        "baseTax": 300,
                        "markup": 0,
                        "comm": 0,
                        "price": 332.12,
                        "tax": 300,
                        "oBaseFare": {
                            "amount": 32.120003,
                            "currency": "USD"
                        },
                        "oBaseTax": {
                            "amount": 300,
                            "currency": "USD"
                        },
                        "oExchangeFareDiff": {
                            "amount": 8,
                            "currency": "USD"
                        },
                        "oExchangeTaxDiff": {
                            "amount": 24.12,
                            "currency": "USD"
                        }
                    }
                },
                "trips": [
                    {
                        "tripId": 1,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-10 20:15",
                                "arrivalTime": "2022-01-11 21:10",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-11 12:35",
                                        "arrivalDateTime": "2022-01-11 11:35",
                                        "duration": 60,
                                        "elapsedTime": 620,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "513",
                                "bookingClass": "H",
                                "duration": 1015,
                                "departureAirportCode": "JFK",
                                "departureAirportTerminal": "8",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "DL",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-11 23:15",
                                "arrivalTime": "2022-01-12 01:20",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "308",
                                "bookingClass": "H",
                                "duration": 125,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "NBO",
                                "arrivalAirportTerminal": "1C",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "D",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1265
                    },
                    {
                        "tripId": 2,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-31 05:00",
                                "arrivalTime": "2022-01-31 07:15",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "309",
                                "bookingClass": "E",
                                "duration": 135,
                                "departureAirportCode": "NBO",
                                "departureAirportTerminal": "1C",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "B",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-31 08:30",
                                "arrivalTime": "2022-01-31 18:15",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-31 12:15",
                                        "arrivalDateTime": "2022-01-31 11:00",
                                        "duration": 75,
                                        "elapsedTime": 330,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "512",
                                "bookingClass": "E",
                                "duration": 1065,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "JFK",
                                "arrivalAirportTerminal": "8",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "LD",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1275
                    }
                ],
                "paxCnt": 1,
                "validatingCarrier": "",
                "gds": "S",
                "pcc": "G9MJ",
                "cons": "GTT",
                "fareType": "SR",
                "cabin": "Y",
                "currency": "USD",
                "currencies": [
                    "USD"
                ],
                "currencyRates": {
                    "USDUSD": {
                        "from": "USD",
                        "to": "USD",
                        "rate": 1
                    }
                },
                "keys": {},
                "meta": {
                    "eip": 0,
                    "noavail": false,
                    "searchId": "U1NTMTAxWTEwMDB8SkZLTkJPMjAyMi0wMS0xMHxOQk9KRksyMDIyLTAxLTMx",
                    "lang": "en",
                    "rank": 0,
                    "cheapest": false,
                    "fastest": false,
                    "best": false,
                    "country": "us"
                },
                "billing": {
                      "first_name": "John",
                      "last_name": "Doe",
                      "middle_name": "",
                      "address_line1": "1013 Weda Cir",
                      "address_line2": "",
                      "country_id": "US",
                      "city": "Mayfield",
                      "state": "KY",
                      "zip": "99999",
                      "company_name": "",
                      "contact_phone": "+19074861000",
                      "contact_email": "test@test.com",
                      "contact_name": "Test Name"
                },
                "payment_request": {
                      "method_key": "cc",
                      "currency": "USD",
                      "method_data": {
                          "card": {
                              "number": "4111555577778888",
                              "holder_name": "Test test",
                              "expiration_month": 10,
                              "expiration_year": 23,
                              "cvv": "1234"
                          }
                      },
                      "amount": 112.25
                }
            },
       "code": "13200",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "code": "13106",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "booking_id": [
              "booking_id cannot be blank."
            ]
       ],
       "code": "13107",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightQuoteExchangeController.php",groupTitle:"Flight_Voluntary_Exchange"},{type:"post",url:"/v2/flight-quote-exchange/view",title:"Flight Voluntary Exchange View",version:"0.2.0",name:"Flight_Voluntary_Exchange_View",group:"Flight_Voluntary_Exchange",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"7..10",optional:!1,field:"booking_id",description:"<p>Booking ID</p>"}]},examples:[{title:"Request-Example:",content:`{
    "booking_id": "XXXYYYZ"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
                    "productQuoteChange": {
                        "id": 950326,
                        "productQuoteId": 950326,
                        "productQuoteGid": "b1ae27497b6eaab24a39fc1370069bd4",
                        "caseId": 35618,
                        "caseGid": "e7dce13b4e6a5f3ccc2cec9c21fa3255",
                        "statusId": 4,
                        "statusName": "Complete",
                        "decisionTypeId": null,
                        "decisionTypeName": "Undefined",
                        "isAutomate": 1,
                        "createdDt": "2021-09-21 03:28:33",
                        "updatedDt": "2021-09-28 09:11:38"
                    }
               },
       "code": "13200",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "code": "13106",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "booking_id": [
              "booking_id cannot be blank."
            ]
       ],
       "code": "13107",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightQuoteExchangeController.php",groupTitle:"Flight_Voluntary_Exchange"},{type:"post",url:"/v2/flight-quote-exchange/get-change",title:"Flight Voluntary Product Quote Change Info",version:"0.2.0",name:"Flight_Voluntary_Product_Quote_Change",group:"Flight_Voluntary_Exchange",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"32",optional:!1,field:"change_gid",description:"<p>Change gid</p>"}]},examples:[{title:"Request-Example:",content:`{
    "change_gid": "16b2506459becec5e038b829568de2bb"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
                "bookingId": "XXXYYYZ",
                "quote_gid" : "48c82774ead469ad311c1e6112562726",
                "key": "51_U1NTMTAxKlkxMDAwL0pGS05CTzIwMjItMDEtMTAvTkJPSkZLMjAyMi0wMS0zMSp+I0VUNTEzI0VUMzA4I0VUMzA5I0VUNTEyfmxjOmVuX3VzOkVYXzE3Yz123456789",
                "prices": {
                    "totalPrice": 332.12,
                    "comm": 0,
                    "isCk": false
                },
                "passengers": {
                    "ADT": {
                        "codeAs": "JCB",
                        "cnt": 1,
                        "baseFare": 32.12,
                        "pubBaseFare": 32.12,
                        "baseTax": 300,
                        "markup": 0,
                        "comm": 0,
                        "price": 332.12,
                        "tax": 300,
                        "oBaseFare": {
                            "amount": 32.120003,
                            "currency": "USD"
                        },
                        "oBaseTax": {
                            "amount": 300,
                            "currency": "USD"
                        },
                        "oExchangeFareDiff": {
                            "amount": 8,
                            "currency": "USD"
                        },
                        "oExchangeTaxDiff": {
                            "amount": 24.12,
                            "currency": "USD"
                        }
                    }
                },
                "trips": [
                    {
                        "tripId": 1,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-10 20:15",
                                "arrivalTime": "2022-01-11 21:10",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-11 12:35",
                                        "arrivalDateTime": "2022-01-11 11:35",
                                        "duration": 60,
                                        "elapsedTime": 620,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "513",
                                "bookingClass": "H",
                                "duration": 1015,
                                "departureAirportCode": "JFK",
                                "departureAirportTerminal": "8",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "DL",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-11 23:15",
                                "arrivalTime": "2022-01-12 01:20",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "308",
                                "bookingClass": "H",
                                "duration": 125,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "NBO",
                                "arrivalAirportTerminal": "1C",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "D",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1265
                    },
                    {
                        "tripId": 2,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-31 05:00",
                                "arrivalTime": "2022-01-31 07:15",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "309",
                                "bookingClass": "E",
                                "duration": 135,
                                "departureAirportCode": "NBO",
                                "departureAirportTerminal": "1C",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "B",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-31 08:30",
                                "arrivalTime": "2022-01-31 18:15",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-31 12:15",
                                        "arrivalDateTime": "2022-01-31 11:00",
                                        "duration": 75,
                                        "elapsedTime": 330,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "512",
                                "bookingClass": "E",
                                "duration": 1065,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "JFK",
                                "arrivalAirportTerminal": "8",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "LD",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1275
                    }
                ],
                "paxCnt": 1,
                "validatingCarrier": "",
                "gds": "S",
                "pcc": "G9MJ",
                "cons": "GTT",
                "fareType": "SR",
                "cabin": "Y",
                "currency": "USD",
                "currencies": [
                    "USD"
                ],
                "currencyRates": {
                    "USDUSD": {
                        "from": "USD",
                        "to": "USD",
                        "rate": 1
                    }
                },
                "keys": {},
                "meta": {
                    "eip": 0,
                    "noavail": false,
                    "searchId": "U1NTMTAxWTEwMDB8SkZLTkJPMjAyMi0wMS0xMHxOQk9KRksyMDIyLTAxLTMx",
                    "lang": "en",
                    "rank": 0,
                    "cheapest": false,
                    "fastest": false,
                    "best": false,
                    "country": "us"
                },
                "billing": {
                      "first_name": "John",
                      "last_name": "Doe",
                      "middle_name": "",
                      "address_line1": "1013 Weda Cir",
                      "address_line2": "",
                      "country_id": "US",
                      "city": "Mayfield",
                      "state": "KY",
                      "zip": "99999",
                      "company_name": "",
                      "contact_phone": "+19074861000",
                      "contact_email": "test@test.com",
                      "contact_name": "Test Name"
                },
                "payment_request": {
                      "method_key": "cc",
                      "currency": "USD",
                      "method_data": {
                          "card": {
                              "number": "4111555577778888",
                              "holder_name": "Test test",
                              "expiration_month": 10,
                              "expiration_year": 23,
                              "cvv": "1234"
                          }
                      },
                      "amount": 112.25
                }
            },
       "code": "13200",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Load data error",
       "errors": [
          "Not found data on POST request"
       ],
       "code": "13106",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response:",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": 422,
       "message": "Validation error",
       "errors": [
           "booking_id": [
              "booking_id cannot be blank."
            ]
       ],
       "code": "13107",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/FlightQuoteExchangeController.php",groupTitle:"Flight_Voluntary_Exchange"},{type:"post",url:"/v1/flight-quote-refund/confirm",title:"Flight Voluntary Refund Confirm",version:"1.0.0",name:"Flight_Voluntary_Refund_Confirm",group:"Flight_Voluntary_Refund",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"0..10",optional:!1,field:"bookingId",description:"<p>Booking ID</p>"},{group:"Parameter",type:"string",size:"..32",optional:!1,field:"refundGid",description:"<p>Refund GID</p>"},{group:"Parameter",type:"string",size:"..32",optional:!1,field:"orderId",description:"<p>OTA Refund Order Id</p>"},{group:"Parameter",type:"object",optional:!1,field:"billing",description:"<p>Billing</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.last_name",description:"<p>Last name</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"billing.middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.company_name",description:"<p>Company</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"billing.address_line1",description:"<p>Address line 1</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"billing.address_line2",description:"<p>Address line 2</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"billing.country_id",description:"<p>Country code (for example &quot;US&quot;)</p>"},{group:"Parameter",type:"string",optional:!1,field:"billing.country",description:"<p>Country (for example &quot;United States&quot;)</p>"},{group:"Parameter",type:"string",size:"10",optional:!1,field:"billing.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"billing.contact_phone",description:"<p>Contact phone</p>"},{group:"Parameter",type:"string",size:"160",optional:!1,field:"billing.contact_email",description:"<p>Contact email</p>"},{group:"Parameter",type:"string",size:"60",optional:!0,field:"billing.contact_name",description:"<p>Contact name</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request",description:"<p>Payment request</p>"},{group:"Parameter",type:"number",optional:!1,field:"payment_request.amount",description:"<p>Customer must pay for initiate refund process</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"payment_request.currency",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"payment_request.method_key",description:"<p>Method key (for example &quot;card&quot;)</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data",description:"<p>Method data</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data.card",description:"<p>Card (for credit card)</p>"},{group:"Parameter",type:"string",size:"..20",optional:!1,field:"payment_request.method_data.card.number",description:"<p>Number</p>"},{group:"Parameter",type:"string",size:"..50",optional:!1,field:"payment_request.method_data.card.holder_name",description:"<p>Holder name</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_month",description:"<p>Month</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_year",description:"<p>Year</p>"},{group:"Parameter",type:"string",size:"..4",optional:!1,field:"payment_request.method_data.card.cvv",description:"<p>CVV</p>"}]},examples:[{title:"Request-Example:",content:`{
    "bookingId": "XXXXXXX",
    "refundGid": "6fcb275a1cd60b3a1e93bdda093e383b",
    "orderId": "RET-12321AD",
    "billing": {
        "first_name": "John",
        "last_name": "Doe",
        "middle_name": "",
        "address_line1": "1013 Weda Cir",
        "address_line2": "",
        "country_id": "US",
        "country": "United States",
        "city": "Mayfield",
        "state": "KY",
        "zip": "99999",
        "company_name": "",
        "contact_phone": "+19074861000",
        "contact_email": "test@test.com",
        "contact_name": "Test Name"
    },
    "payment_request": {
        "method_key": "card",
        "currency": "USD",
        "method_data": {
            "card": {
                "number": "4111555577778888",
                "holder_name": "Test test",
                "expiration_month": 10,
                "expiration_year": 23,
                "cvv": "1234"
            }
        },
        "amount": 112.25
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "code": "13200",
    "saleData": {
         "id": 12345,
         "bookingId": "P12OJ12"
    },
    "refund": {
        "id": 54321,
        "orderId": "RET-12321AD"
    }
}`,type:"json"}]},error:{examples:[{title:"Error-Response Load Data:",content:`HTTP/1.1 200 OK
 {
     "status": 400,
     "message": "Load data error",
     "name": "Client Error: Bad Request",
     "code": 13106,
     "type": "app",
     "errors": []
 }`,type:"json"},{title:"Error-Response Validation:",content:`HTTP/1.1 200 OK
{
  "status": 422,
  "message": "Validation error",
  "name": "Client Error: Unprocessable Entity",
  "errors": {
     "bookingId": [
         "Booking Id should contain at most 10 characters."
     ]
  },
  "code": 13107,
  "type": "app"
}`,type:"json"},{title:"Error-Response Error From BO:",content:`HTTP/1.1 200 OK
{
     "status": 422,
     "message": "FlightRequest is not found.",
     "name": "BO Request Failed",
     "code": "15411",
     "errors": [],
     "type": "app_bo"
}`,type:"json"},{title:"Codes designation",content:`[
     13101 - Api User has no related project
     13104 - Request is not POST
     13106 - Post has not loaded
     13107 - Validation Failed
     13112 - Not found refund in pending status by booking and gid
     13113 - Flight Request already processing; This feature helps to handle duplicate requests
     15411 - Request to BO failed; See tab "Error From BO"
     15412 - BO endpoint is not set; This is system crm error
     150001 - Flight Request saving failed; This is system crm error
     601 - BO Server Error: i.e. request timeout
     602 - BO response body is empty
     603 - BO response type is invalid (not array)
]`,type:"html"}]},filename:"v1/controllers/FlightQuoteRefundController.php",groupTitle:"Flight_Voluntary_Refund"},{type:"post",url:"/v1/flight-quote-refund/create",title:"Flight Voluntary Refund Create",version:"1.0.0",name:"Flight_Voluntary_Refund_Create",group:"Flight_Voluntary_Refund",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"0..50",optional:!1,field:"bookingId",description:"<p>Booking ID</p>"},{group:"Parameter",type:"object",optional:!1,field:"refund",description:"<p>Refund Data</p>"},{group:"Parameter",type:"string",size:"..3",optional:!1,field:"refund.currency",description:"<p>Currency</p>"},{group:"Parameter",type:"string",size:"..32",optional:!1,field:"refund.orderId",description:"<p>OTA Order Id</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.processingFee",description:"<p>Processing fee</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.penaltyAmount",description:"<p>Airline penalty amount</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.totalRefundAmount",description:"<p>Total refund amount</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.totalPaid",description:"<p>Total booking amount</p>"},{group:"Parameter",type:"object",optional:!1,field:"refund.tickets",description:"<p>Refund Tickets Array</p>"},{group:"Parameter",type:"string",optional:!1,field:"refund.tickets.number",description:"<p>Ticket Number</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.tickets.airlinePenalty",description:"<p>Airline penalty</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.tickets.processingFee",description:"<p>Processing fee</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.tickets.refundable",description:"<p>Refund amount</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.tickets.selling",description:"<p>Selling price</p>"},{group:"Parameter",type:"string",optional:!1,field:"refund.tickets.status",description:"<p>Status For BO</p>"},{group:"Parameter",type:"bool",optional:!0,field:"refund.tickets.refundAllowed",description:"<p>Refund Allowed</p>"},{group:"Parameter",type:"object",optional:!1,field:"refund.auxiliaryOptions",description:"<p>Auxiliary Options Array</p>"},{group:"Parameter",type:"string",optional:!1,field:"refund.auxiliaryOptions.type",description:"<p>Auxiliary Options Type</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.auxiliaryOptions.amount",description:"<p>Selling price</p>"},{group:"Parameter",type:"number",optional:!1,field:"refund.auxiliaryOptions.refundable",description:"<p>Refundable price</p>"},{group:"Parameter",type:"string",optional:!1,field:"refund.auxiliaryOptions.status",description:"<p>Status For BO</p>"},{group:"Parameter",type:"bool",optional:!1,field:"refund.auxiliaryOptions.refundAllow",description:"<p>Refund Allowed</p>"},{group:"Parameter",type:"object",optional:!0,field:"refund.auxiliaryOptions.details",description:"<p>Details</p>"},{group:"Parameter",type:"object",optional:!0,field:"refund.auxiliaryOptions.amountPerPax",description:"<p>Amount Per Pax</p>"},{group:"Parameter",type:"object",optional:!1,field:"billing",description:"<p>Billing</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.first_name",description:"<p>First name</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.last_name",description:"<p>Last name</p>"},{group:"Parameter",type:"string",size:"30",optional:!0,field:"billing.middle_name",description:"<p>Middle name</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.company_name",description:"<p>Company</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"billing.address_line1",description:"<p>Address line 1</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"billing.address_line2",description:"<p>Address line 2</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"billing.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"billing.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"2",optional:!1,field:"billing.country_id",description:"<p>Country code (for example &quot;US&quot;)</p>"},{group:"Parameter",type:"string",optional:!1,field:"billing.country",description:"<p>Country (for example &quot;United States&quot;)</p>"},{group:"Parameter",type:"string",size:"10",optional:!1,field:"billing.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"billing.contact_phone",description:"<p>Contact phone</p>"},{group:"Parameter",type:"string",size:"160",optional:!1,field:"billing.contact_email",description:"<p>Contact email</p>"},{group:"Parameter",type:"string",size:"60",optional:!0,field:"billing.contact_name",description:"<p>Contact name</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request",description:"<p>Payment request</p>"},{group:"Parameter",type:"number",optional:!1,field:"payment_request.amount",description:"<p>Customer must pay for initiate refund process</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"payment_request.currency",description:"<p>Currency code</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"payment_request.method_key",description:"<p>Method key (for example &quot;card&quot;)</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data",description:"<p>Method data</p>"},{group:"Parameter",type:"object",optional:!1,field:"payment_request.method_data.card",description:"<p>Card (for credit card)</p>"},{group:"Parameter",type:"string",size:"..20",optional:!1,field:"payment_request.method_data.card.number",description:"<p>Number</p>"},{group:"Parameter",type:"string",size:"..50",optional:!1,field:"payment_request.method_data.card.holder_name",description:"<p>Holder name</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_month",description:"<p>Month</p>"},{group:"Parameter",type:"int",optional:!1,field:"payment_request.method_data.card.expiration_year",description:"<p>Year</p>"},{group:"Parameter",type:"string",size:"..4",optional:!1,field:"payment_request.method_data.card.cvv",description:"<p>CVV</p>"}]},examples:[{title:"Request-Example:",content:`{
    "bookingId": "XXXXXXX",
    "refund": {
        "orderId": "RET-12321AD",
        "processingFee": 12.5,
        "penaltyAmount": 100.00,
        "totalRefundAmount": 112.5,
        "totalPaid": 305.50,
        "currency": "USD",
        "tickets": [
            {
                "number": "465723459",
                "airlinePenalty": 25.36,
                "processingFee": 25,
                "refundable": 52.65,
                "selling": 150,
                "status": "issued",
                "refundAllowed": true
            }
        ],
        "auxiliaryOptions": [
            {
                "type": "package",
                "amount": 25.00,
                "refundable": 15.00,
                "status": "paid",
                "refundAllow": true,
                "details": {},
                "amountPerPax": {
                    "1111111111": 5.45
                }
            }
        ]
    },
    "billing": {
        "first_name": "John",
        "last_name": "Doe",
        "middle_name": "",
        "address_line1": "1013 Weda Cir",
        "address_line2": "",
        "country_id": "US",
        "country": "United States",
        "city": "Mayfield",
        "state": "KY",
        "zip": "99999",
        "company_name": "",
        "contact_phone": "+19074861000",
        "contact_email": "test@test.com",
        "contact_name": "Test Name"
    },
    "payment_request": {
        "method_key": "card",
        "currency": "USD",
        "method_data": {
            "card": {
                "number": "4111555577778888",
                "holder_name": "Test test",
                "expiration_month": 10,
                "expiration_year": 23,
                "cvv": "1234"
            }
        },
        "amount": 112.25
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "code": "13200",
    "saleData": {
         "id": 12345,
         "bookingId": "P12OJ12"
    },
    "refund": {
        "id": 54321,
        "orderId": "RET-12321AD"
    }
}`,type:"json"}]},error:{examples:[{title:"Error-Response Load Data:",content:`HTTP/1.1 200 OK
 {
     "status": 400,
     "message": "Load data error",
     "name": "Client Error: Bad Request",
     "code": 13106,
     "type": "app",
     "errors": []
 }`,type:"json"},{title:"Error-Response Validation:",content:`HTTP/1.1 200 OK
{
  "status": 422,
  "message": "Validation error",
  "name": "Client Error: Unprocessable Entity",
  "errors": {
     "bookingId": [
         "Booking Id should contain at most 10 characters."
     ]
  },
  "code": 13107,
  "type": "app"
}`,type:"json"},{title:"Error-Response Error From BO:",content:`HTTP/1.1 200 OK
{
     "status": 422,
     "message": "FlightRequest is not found.",
     "name": "BO Request Failed",
     "code": "15411",
     "errors": [],
     "type": "app_bo"
}`,type:"json"},{title:"Codes designation",content:`[
     13101 - Api User has no related project
     13104 - Request is not POST
     13106 - Post has not loaded
     13107 - Validation Failed
     13113 - Flight Request already processing; This feature helps to handle duplicate requests

     15401 - Case creation failed; This is system crm error
     15402 - Case Sale creation failed; This is system crm error
     15403 - Client creation failed; This is system crm error
     15404 - Order creation failed; This is system crm error
     15405 - Origin Product Quote creation failed; This is system crm error
     15409 - Quote not available for refund due to exists active refund or change
     15410 - Quote not available for refund due to status of product quote not in changeable list
     15411 - Request to BO failed; See tab "Error From BO"
     15412 - BO endpoint is not set; This is system crm error
     150001 - Flight Request saving failed; This is system crm error

     601 - BO Server Error: i.e. request timeout
     602 - BO response body is empty
     603 - BO response type is invalid (not array)
]`,type:"html"}]},filename:"v1/controllers/FlightQuoteRefundController.php",groupTitle:"Flight_Voluntary_Refund"},{type:"post",url:"/v1/flight-quote-refund/info",title:"Voluntary Refund Info",version:"1.0.0",name:"Flight_Voluntary_Refund_Info",group:"Flight_Voluntary_Refund",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"0..10",optional:!1,field:"bookingId",description:"<p>Booking ID</p>"}]},examples:[{title:"Request-Example:",content:`{
    "bookingId": "XXXXXXX"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
 {
      "status": 200,
      "message": "OK",
      "data": {
          "refund": {
              "totalPaid": 300,
              "totalAirlinePenalty": 150,
              "totalProcessingFee": 30,
              "totalRefundable": 150,
              "refundCost": 0,
              "currency": "USD",
              "tickets": [
                  {
                      "number": "fake-22222",
                      "airlinePenalty": 345.47,
                      "processingFee": 0,
                      "refundable": 128,
                      "selling": 473.47,
                      "currency": "USD",
                      "status": "refunded",
                      "refundAllowed": false
                  }
              ],
              "auxiliaryOptions": [
                  {
                      "type": "auto_check_in",
                      "amount": 21.9,
                      "amountPerPax": [],
                      "refundable": 21.9,
                      "details": [],
                      "status": "paid",
                      "refundAllow": true
                  },
                  {
                      "type": "flexible_ticket",
                      "amount": 106.06,
                      "amountPerPax": [],
                      "refundable": 0,
                      "details": [],
                      "status": "paid",
                      "refundAllow": false
                  }
              ]
          }
      },
      "code": "13200"
  }`,type:"json"}]},error:{examples:[{title:"Error-Response Load Data:",content:`HTTP/1.1 200 OK
{
    "status": 400,
    "message": "Load data error",
    "name": "Client Error: Bad Request",
    "code": 13106,
    "type": "app",
    "errors": []
}`,type:"json"},{title:"Error-Response Validation:",content:`HTTP/1.1 200 OK
{
  "status": 422,
  "message": "Validation error",
  "name": "Client Error: Unprocessable Entity",
  "errors": {
  "bookingId": [
         "Booking Id should contain at most 10 characters."
     ]
  },
  "code": 13107,
  "type": "app"
}`,type:"json"},{title:"Codes designation",content:`[
     13104 - Request is not POST
     13106 - Post has not loaded
     13107 - Validation Failed
     13112 - ProductQuoteRefund not found by BookingId
]`,type:"html"}]},filename:"v1/controllers/FlightQuoteRefundController.php",groupTitle:"Flight_Voluntary_Refund"},{type:"post",url:"/v1/lead/create",title:"Create Lead",version:"0.1.0",name:"CreateLead",group:"Leads",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project (if not use Basic-Authorization)</p>"},{group:"Parameter",type:"object",optional:!1,field:"lead",description:"<p>Lead data array</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.source_id",description:"<p>Source ID</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"lead.sub_sources_code",description:"<p>Source Code</p>"},{group:"Parameter",type:"int",size:"1..9",optional:!1,field:"lead.adults",description:"<p>Adult count</p>"},{group:"Parameter",type:"string",size:"1",allowedValues:["E-ECONOMY","B-BUSINESS","F-FIRST","P-PREMIUM"],optional:!1,field:"lead.cabin",description:"<p>Cabin</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"lead.emails",description:"<p>Array of Emails (string)</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"lead.phones",description:"<p>Array of Phones (string)</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"lead.flights",description:"<p>Array of Flights</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.origin",description:"<p>Flight Origin location Airport IATA-code</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.destination",description:"<p>Flight Destination location Airport IATA-code</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!1,field:"lead.flights.departure",description:"<p>Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)</p>"},{group:"Parameter",type:"string",size:"2",allowedValues:["OW-ONE_WAY","RT-ROUND_TRIP","MC-MULTI_DESTINATION"],optional:!0,field:"lead.trip_type",description:"<p>Trip type (if empty - autocomplete)</p>"},{group:"Parameter",type:"int",allowedValues:["1-PENDING","2-PROCESSING","4-REJECT","5-FOLLOW_UP","8-ON_HOLD","10-SOLD","11-TRASH","12-BOOKED","13-SNOOZE"],optional:!0,field:"lead.status",description:"<p>Status</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.children",description:"<p>Children count</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.infants",description:"<p>Infant count</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"lead.uid",description:"<p>UID value</p>"},{group:"Parameter",type:"text",optional:!0,field:"lead.notes_for_experts",description:"<p>Notes for expert</p>"},{group:"Parameter",type:"text",optional:!0,field:"lead.request_ip_detail",description:"<p>Request IP detail (autocomplete)</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.request_ip",description:"<p>Request IP</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.snooze_for",description:"<p>Snooze for</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.rating",description:"<p>Rating</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.discount_id",description:"<p>Discount Id</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_first_name",description:"<p>Client first name</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_last_name",description:"<p>Client last name</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_middle_name",description:"<p>Client middle name</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"lead.user_language",description:"<p>User language</p>"},{group:"Parameter",type:"bool",optional:!0,field:"lead.is_test",description:"<p>Is test lead (default false)</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:mm:ss",optional:!0,field:"lead.expire_at",description:"<p>Expire at</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"lead.visitor_log",description:"<p>Array of Visitor log</p>"},{group:"Parameter",type:"string",size:"10",optional:!1,field:"lead.visitor_log.vl_source_cid",description:""},{group:"Parameter",type:"string",size:"36",optional:!1,field:"lead.visitor_log.vl_ga_client_id",description:""},{group:"Parameter",type:"string",size:"36",optional:!1,field:"lead.visitor_log.vl_ga_user_id",description:""},{group:"Parameter",type:"int",optional:!1,field:"lead.visitor_log.vl_customer_id",description:""},{group:"Parameter",type:"string",size:"100",optional:!1,field:"lead.visitor_log.vl_gclid",description:""},{group:"Parameter",type:"string",size:"255",optional:!1,field:"lead.visitor_log.vl_dclid",description:""},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.visitor_log.vl_utm_source",description:""},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.visitor_log.vl_utm_medium",description:""},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.visitor_log.vl_utm_campaign",description:""},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.visitor_log.vl_utm_term",description:""},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.visitor_log.vl_utm_content",description:""},{group:"Parameter",type:"string",size:"500",optional:!1,field:"lead.visitor_log.vl_referral_url",description:""},{group:"Parameter",type:"string",size:"500",optional:!1,field:"lead.visitor_log.vl_location_url",description:""},{group:"Parameter",type:"string",size:"500",optional:!1,field:"lead.visitor_log.vl_user_agent",description:""},{group:"Parameter",type:"string",size:"39",optional:!1,field:"lead.visitor_log.vl_ip_address",description:""},{group:"Parameter",type:"object[]",optional:!0,field:"lead.lead_data",description:"<p>Array of Lead Data</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.lead_data.field_key",description:"<p>Lead Data Key</p>"},{group:"Parameter",type:"string",size:"500",optional:!0,field:"lead.lead_data.field_value",description:"<p>Lead Data Value</p>"},{group:"Parameter",type:"object[]",optional:!0,field:"lead.client_data",description:"<p>Array of Client Data</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.client_data.field_key",description:"<p>Client Data Key</p>"},{group:"Parameter",type:"string",size:"500",optional:!0,field:"lead.client_data.field_value",description:"<p>Client Data Value</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:mm:ss",optional:!1,field:"lead.visitor_log.vl_visit_dt",description:""},{group:"Parameter",type:"object",optional:!1,field:"Client",description:""},{group:"Parameter",type:"string",optional:!0,field:"Client.name",description:"<p>Client name</p>"},{group:"Parameter",type:"string",optional:!0,field:"Client.phone",description:"<p>Client phone</p>"},{group:"Parameter",type:"string",optional:!0,field:"Client.email",description:"<p>Client email</p>"},{group:"Parameter",type:"string",optional:!0,field:"Client.client_ip",description:"<p>Client client_ip</p>"},{group:"Parameter",type:"string",optional:!0,field:"Client.uuid",description:"<p>Client uuid</p>"}]},examples:[{title:"Request-Example:",content:`{
   "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
   "lead": {
       "flights": [
           {
               "origin": "KIV",
               "destination": "DME",
               "departure": "2018-10-13 13:50:00",
           },
           {
               "origin": "DME",
               "destination": "KIV",
               "departure": "2018-10-18 10:54:00",
           }
       ],
       "emails": [
         "email1@gmail.com",
         "email2@gmail.com",
       ],
       "phones": [
         "+373-69-487523",
         "022-45-7895-89",
       ],
       "source_id": 38,
       "sub_sources_code": "BBM101",
       "adults": 1,
       "client_first_name": "Alexandr",
       "client_last_name": "Freeman",
       "user_language": "en-GB",
       "is_test": true,
       "expire_at": "2020-01-20 12:12:12",
       "lead_data": [
              {
                 "field_key": "example_key",
                 "field_value": "example_value"
             }
       ],
      "client_data": [
              {
                 "field_key": "example_key",
                 "field_value": "example_value"
             }
       ],
       "visitor_log": [
              {
                  "vl_source_cid": "string_abc",
                  "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
                  "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
                  "vl_customer_id": "3",
                  "vl_gclid": "gclid=TeSter-123#bookmark",
                  "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
                  "vl_utm_source": "newsletter4",
                  "vl_utm_medium": "string_abc",
                  "vl_utm_campaign": "string_abc",
                  "vl_utm_term": "string_abc",
                  "vl_utm_content": "string_abc",
                  "vl_referral_url": "string_abc",
                  "vl_location_url": "string_abc",
                  "vl_user_agent": "string_abc",
                  "vl_ip_address": "127.0.0.1",
                  "vl_visit_dt": "2020-02-14 12:00:00"
              },
              {
                  "vl_source_cid": "string_abc",
                  "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
                  "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
                  "vl_customer_id": "3",
                  "vl_gclid": "gclid=TeSter-123#bookmark",
                  "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
                  "vl_utm_source": "newsletter4",
                  "vl_utm_medium": "string_abc",
                  "vl_utm_campaign": "string_abc",
                  "vl_utm_term": "string_abc",
                  "vl_utm_content": "string_abc",
                  "vl_referral_url": "string_abc",
                  "vl_location_url": "string_abc",
                  "vl_user_agent": "string_abc",
                  "vl_ip_address": "127.0.0.1",
                  "vl_visit_dt": "2020-02-14 12:00:00"
              }
       ]
   },
   "Client": {
       "name": "Alexandr",
       "phone": "+373-69-487523",
       "email": "email1@gmail.com",
       "client_ip": "127.0.0.1",
       "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
   }
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"Integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"},{group:"Success 200",type:"Array",optional:!1,field:"data",description:"<p>Data Array</p>"}]},examples:[{title:"Success-Response:",content:`    HTTP/1.1 200 OK
{
  "status": 200,
  "name": "Success",
  "code": 0,
  "message": "",
  "data": {
      "response": {
          "lead": {
              "client_id": 11,
              "employee_id": null,
              "status": 1,
              "uid": "5b73b80eaf69b",
              "gid": "65df1546edccce15518e929e5af1a4",
              "project_id": 6,
              "source_id": "38",
              "trip_type": "RT",
              "cabin": "E",
              "adults": "1",
              "children": 0,
              "infants": 0,
              "notes_for_experts": null,
              "created": "2018-08-15 05:20:14",
              "updated": "2018-08-15 05:20:14",
              "request_ip": "127.0.0.1",
              "request_ip_detail": "{\\"ip\\":\\"127.0.0.1\\",\\"city\\":\\"North Pole\\",\\"postal\\":\\"99705\\",\\"state\\":\\"Alaska\\",\\"state_code\\":\\"AK\\",\\"country\\":\\"United States\\",\\"country_code\\":\\"US\\",\\"location\\":\\"64.7548317,-147.3431046\\",\\"timezone\\":{\\"id\\":\\"America\\\\/Anchorage\\",\\"location\\":\\"61.21805,-149.90028\\",\\"country_code\\":\\"US\\",\\"country_name\\":\\"United States of America\\",\\"iso3166_1_alpha_2\\":\\"US\\",\\"iso3166_1_alpha_3\\":\\"USA\\",\\"un_m49_code\\":\\"840\\",\\"itu\\":\\"USA\\",\\"marc\\":\\"xxu\\",\\"wmo\\":\\"US\\",\\"ds\\":\\"USA\\",\\"phone_prefix\\":\\"1\\",\\"fifa\\":\\"USA\\",\\"fips\\":\\"US\\",\\"gual\\":\\"259\\",\\"ioc\\":\\"USA\\",\\"currency_alpha_code\\":\\"USD\\",\\"currency_country_name\\":\\"UNITED STATES\\",\\"currency_minor_unit\\":\\"2\\",\\"currency_name\\":\\"US Dollar\\",\\"currency_code\\":\\"840\\",\\"independent\\":\\"Yes\\",\\"capital\\":\\"Washington\\",\\"continent\\":\\"NA\\",\\"tld\\":\\".us\\",\\"languages\\":\\"en-US,es-US,haw,fr\\",\\"geoname_id\\":\\"6252001\\",\\"edgar\\":\\"\\"},\\"datetime\\":{\\"date\\":\\"08\\\\/14\\\\/2018\\",\\"date_time\\":\\"08\\\\/14\\\\/2018 21:20:15\\",\\"date_time_txt\\":\\"Tuesday, August 14, 2018 21:20:15\\",\\"date_time_wti\\":\\"Tue, 14 Aug 2018 21:20:15 -0800\\",\\"date_time_ymd\\":\\"2018-08-14T21:20:15-08:00\\",\\"time\\":\\"21:20:15\\",\\"month\\":\\"8\\",\\"month_wilz\\":\\"08\\",\\"month_abbr\\":\\"Aug\\",\\"month_full\\":\\"August\\",\\"month_days\\":\\"31\\",\\"day\\":\\"14\\",\\"day_wilz\\":\\"14\\",\\"day_abbr\\":\\"Tue\\",\\"day_full\\":\\"Tuesday\\",\\"year\\":\\"2018\\",\\"year_abbr\\":\\"18\\",\\"hour_12_wolz\\":\\"9\\",\\"hour_12_wilz\\":\\"09\\",\\"hour_24_wolz\\":\\"21\\",\\"hour_24_wilz\\":\\"21\\",\\"hour_am_pm\\":\\"pm\\",\\"minutes\\":\\"20\\",\\"seconds\\":\\"15\\",\\"week\\":\\"33\\",\\"offset_seconds\\":\\"-28800\\",\\"offset_minutes\\":\\"-480\\",\\"offset_hours\\":\\"-8\\",\\"offset_gmt\\":\\"-08:00\\",\\"offset_tzid\\":\\"America\\\\/Anchorage\\",\\"offset_tzab\\":\\"AKDT\\",\\"offset_tzfull\\":\\"Alaska Daylight Time\\",\\"tz_string\\":\\"AKST+9AKDT,M3.2.0\\\\/2,M11.1.0\\\\/2\\",\\"dst\\":\\"true\\",\\"dst_observes\\":\\"true\\",\\"timeday_spe\\":\\"evening\\",\\"timeday_gen\\":\\"evening\\"}}",
              "offset_gmt": "-08.00",
              "snooze_for": null,
              "rating": null,
              "id": 7
          },
          "flights": [
              {
                  "origin": "BOS",
                  "destination": "LGW",
                  "departure": "2018-09-19"
              },
              {
                  "origin": "LGW",
                  "destination": "BOS",
                  "departure": "2018-09-22"
              }
          ],
          "emails": [
              "chalpet@gmail.com",
              "chalpet2@gmail.com"
          ],
          "phones": [
              "+373-69-98-698",
              "+373-69-98-698"
          ],
         "client": {
             "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
             "client_id": 331968,
             "first_name": "Johann",
             "middle_name": "Sebastian",
             "last_name": "Bach",
             "phones": [
                "+13152572166"
             ],
             "emails": [
                "example@test.com",
                "bah@gmail.com"
             ]
          },
         "leadDataInserted": [
             {
                 "ld_field_key": "kayakclickid",
                 "ld_field_value": "example_value",
                 "ld_id": 3
             }
         ],
         "clientDataInserted": [
             {
                 "cd_field_key": "example_key",
                 "cd_field_value": "example_value",
             }
         ],
         "warnings": []
      },
      "request": {
          "client_id": null,
          "employee_id": null,
          "status": null,
          "uid": null,
          "project_id": 6,
          "source_id": "38",
          "trip_type": null,
          "cabin": null,
          "adults": "1",
          "children": null,
          "infants": null,
          "notes_for_experts": null,
          "created": null,
          "updated": null,
          "request_ip": null,
          "request_ip_detail": null,
          "offset_gmt": null,
          "snooze_for": null,
          "rating": null,
          "flights": [
              {
                  "origin": "BOS",
                  "destination": "LGW",
                  "departure": "2018-09-19"
              },
              {
                  "origin": "LGW",
                  "destination": "BOS",
                  "departure": "2018-09-22"
              }
          ],
          "emails": [
              "chalpet@gmail.com",
              "chalpet2@gmail.com"
          ],
          "phones": [
              "+373-69-98-698",
              "+373-69-98-698"
          ],
          "client_first_name": "Alexandr",
          "client_last_name": "Freeman"
      }
  },
  "action": "v1/lead/create",
  "response_id": 42,
  "request_dt": "2018-08-15 05:20:14",
  "response_dt": "2018-08-15 05:20:15"
}`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`     HTTP/1.1 422 Unprocessable entity
     {
         "name": "Unprocessable entity",
         "message": "Flight [0]: Destination should contain at most 3 characters.",
         "code": 5,
         "status": 422,
         "type": "yii\\\\web\\\\UnprocessableEntityHttpException"
     }

@return array
@throws BadRequestHttpException
@throws UnprocessableEntityHttpException
@throws \\Throwable`,type:"json"}]},filename:"v1/controllers/LeadController.php",groupTitle:"Leads"},{type:"post",url:"/v2/lead/create",title:"Create Lead Alternative",version:"0.2.0",name:"CreateLeadAlternative",group:"Leads",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"object",optional:!1,field:"lead",description:"<p>Lead data array</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"lead.source_code",description:"<p>Source Code</p>"},{group:"Parameter",type:"string",optional:!0,field:"lead.department_key",description:"<p>Department Key</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.project_key",description:"<p>Project key</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!1,field:"lead.adults",description:"<p>Adult count</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.children",description:"<p>Children count (by default 0)</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.infants",description:"<p>Infants count (by default 0)</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.request_ip",description:"<p>Request IP</p>"},{group:"Parameter",type:"string",size:"32",optional:!0,field:"lead.discount_id",description:"<p>Discount ID</p>"},{group:"Parameter",type:"string",size:"15",optional:!0,field:"lead.uid",description:"<p>UID value</p>"},{group:"Parameter",type:"text",optional:!0,field:"lead.user_agent",description:"<p>User agent info</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"lead.flights",description:"<p>Flights</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.origin",description:"<p>Flight Origin location Airport IATA-code</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.destination",description:"<p>Flight Destination location Airport IATA-code</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD",optional:!1,field:"lead.flights.departure",description:"<p>Flight Departure DateTime (format YYYY-MM-DD)</p>"},{group:"Parameter",type:"object",optional:!1,field:"lead.client",description:"<p>Client</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"lead.client.phone",description:"<p>Client phone or Client email or Client chat_visitor_id is required</p>"},{group:"Parameter",type:"string",size:"160",optional:!1,field:"lead.client.email",description:"<p>Client email or Client phone or Client chat_visitor_id is required</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"lead.client.chat_visitor_id",description:"<p>Client chat_visitor_id or Client email or Client phone is required</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.client.uuid",description:"<p>Client uuid</p>"},{group:"Parameter",type:"int",size:"2",allowedValues:["14-BOOK_FAILED","15-ALTERNATIVE","1-PENDING"],optional:!0,field:"lead.status",description:"<p>Status (by default 1-PENDING)</p>"},{group:"Parameter",type:"string",size:"1",allowedValues:["E-ECONOMY","B-BUSINESS","F-FIRST","P-PREMIUM"],optional:!0,field:"lead.cabin",description:"<p>Cabin (by default E)</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.flight_id",description:"<p>BO Flight ID</p>"},{group:"Parameter",type:"string",size:"5",optional:!1,field:"lead.user_language",description:"<p>User Language</p>"},{group:"Parameter",type:"bool",optional:!0,field:"lead.is_test",description:"<p>Is test lead (default false)</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:mm:ss",optional:!0,field:"lead.expire_at",description:"<p>Expire at</p>"},{group:"Parameter",type:"object[]",optional:!0,field:"lead.lead_data",description:"<p>Array of Lead Data</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.lead_data.field_key",description:"<p>Lead Data Key</p>"},{group:"Parameter",type:"string",size:"500",optional:!0,field:"lead.lead_data.field_value",description:"<p>Lead Data Value</p>"},{group:"Parameter",type:"object[]",optional:!0,field:"lead.client_data",description:"<p>Array of Client Data</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.client_data.field_key",description:"<p>Client Data Key</p>"},{group:"Parameter",type:"string",size:"500",optional:!0,field:"lead.client_data.field_value",description:"<p>Client Data Value</p>"}]},examples:[{title:"Request-Example:",content:`
{
     "lead": {
          "client": {
              "phone": "+37369333333",
              "email": "email@email.com",
              "uuid" : "af5246f1-094f-4fde-ada3-bd7298621613",
              "chat_visitor_id" : "6b811a3e-41c4-4d49-a99a-afw3e4rtf3tfregf"
          },
          "uid": "WD6q53PO3b",
          "status": 14,
          "source_code": "JIVOCH",
          "project_key": "ovago",
          "department_key": "exchange",
          "cabin": "E",
          "adults": 2,
          "children": 2,
          "infants": 2,
          "request_ip": "12.12.12.12",
          "discount_id": "123123",
          "user_agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36",
          "flight_id": 12457,
          "user_language": "en-GB",
          "is_test": true,
          "expire_at": "2020-01-20 12:12:12",
          "flights": [
              {
                  "origin": "NYC",
                  "destination": "LON",
                  "departure": "2019-12-16"
              },
              {
                  "origin": "LON",
                  "destination": "NYC",
                  "departure": "2019-12-17"
              },
              {
                  "origin": "LON",
                  "destination": "NYC",
                  "departure": "2019-12-18"
              }
          ],
         "lead_data": [
              {
                 "field_key": "example_key",
                 "field_value": "example_value"
             }
         ],
         "client_data": [
              {
                 "field_key": "example_key",
                 "field_value": "example_value"
             }
         ]
      }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
      "status": 200,
      "message": "OK",
      "data": {
         "lead": {
              "id": 370949,
              "uid": "WD6q53PO3b",
              "gid": "63e1505f4a8a87e6651048e3e3eae4e1",
              "client_id": 1034,
              "client": {
                 "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
                 "client_id": 331968,
                 "first_name": "Johann",
                 "middle_name": "Sebastian",
                 "last_name": "Bach",
                 "phones": [
                     "+13152572166"
                 ],
                 "emails": [
                     "example@test.com",
                     "bah@gmail.com"
                 ]
             },
             "leadDataInserted": [
                 {
                     "ld_field_key": "kayakclickid",
                     "ld_field_value": "example_value",
                     "ld_id": 3
                 }
             ],
             "clientDataInserted": [
                 {
                     "cd_field_key": "example_key",
                     "cd_field_value": "example_value",
                 }
             ],
             "warnings": []
         }
      }
      "request": {
          "lead": {
             "client": {
                  "phone": "+37369636963",
                  "email": "example@test.com",
                  "uuid" : "af5246f1-094f-4fde-ada3-bd7298621613"
              },
              "uid": "WD6q53PO3b",
              "status": 14,
              "source_code": "JIVOCH",
              "cabin": "E",
              "adults": 2,
              "children": 2,
              "infants": 2,
              "request_ip": "12.12.12.12",
              "discount_id": "123123",
              "user_agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36",
              "flight_id": 12457,
              "user_language": "en-GB",
              "is_test": true,
              "expire_at": "2020-01-20 12:12:12",
              "flights": [
                  {
                      "origin": "NYC",
                      "destination": "LON",
                      "departure": "2019-12-16"
                  },
                  {
                      "origin": "LON",
                      "destination": "NYC",
                      "departure": "2019-12-17"
                  },
                  {
                      "origin": "LON",
                      "destination": "NYC",
                      "departure": "2019-12-18"
                  }
              ]
          }
      },
      "technical": {
          "action": "v2/lead/create",
          "response_id": 11930215,
          "request_dt": "2019-12-30 12:22:20",
          "response_dt": "2019-12-30 12:22:21",
          "execution_time": 0.055,
          "memory_usage": 1394416
      }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Validation error",
      "errors": {
          "children": [
              "Children must be no greater than 9."
          ],
          "flights[0][origin]": [
              "IATA (NY) not found."
          ],
          "flights[2][departure]": [
              "The format of Departure is invalid."
          ],
          "client[phone]": [
             "The format of Phone is invalid."
          ]
      },
      "code": 10301,
      "request": {
          ...
      },
      "technical": {
          ...
     }
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found Lead data on POST request"
      ],
      "code": 10300,
      "request": {
          ...
      },
      "technical": {
          ...
     }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
      "status": 422,
      "message": "Saving error",
      "errors": [
          "Saving error"
      ],
      "code": 10101,
      "request": {
          ...
      },
      "technical": {
          ...
     }
}`,type:"json"}]},filename:"v2/controllers/LeadController.php",groupTitle:"Leads"},{type:"post",url:"/v1/lead/get",title:"Get Lead",version:"0.1.0",name:"GetLead",group:"Leads",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project (if not use Basic-Authorization)</p>"},{group:"Parameter",type:"object",optional:!1,field:"lead",description:"<p>Lead data array</p>"},{group:"Parameter",type:"int",optional:!1,field:"lead.lead_id",description:"<p>Lead ID</p>"},{group:"Parameter",type:"int",optional:!1,field:"lead.source_id",description:"<p>Source ID</p>"},{group:"Parameter",type:"string",optional:!1,field:"lead.uid",description:"<p>Uid</p>"}]},examples:[{title:"Request-Example:",content:`{
   "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
   "lead": {
       "lead_id": 302,
       "source_id": 38,
       "uid": "5fe2081025a25"
   }
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"Integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"},{group:"Success 200",type:"Array",optional:!1,field:"data",description:"<p>Data Array</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
            "status": 200,
            "name": "Success",
            "code": 0,
            "message": "",
            "data": {
                "response": {
                    "lead": {
                        "id": 371058,
                        "client_id": 333094,
                        "employee_id": 501,
                        "status": 2,
                        "uid": "61234c87a90ee",
                        "project_id": 2,
                        "source_id": 18,
                        "trip_type": "RT",
                        "cabin": "E",
                        "adults": 1,
                        "children": 0,
                        "infants": 0,
                        "notes_for_experts": null,
                        "created": "2021-08-23 07:21:43",
                        "updated": "2021-08-23 07:22:24",
                        "request_ip": null,
                        "request_ip_detail": null,
                        "offset_gmt": null,
                        "snooze_for": null,
                        "rating": 0,
                        "called_expert": 0,
                        "discount_id": null,
                        "bo_flight_id": null,
                        "additional_information": null,
                        "l_answered": 0,
                        "clone_id": null,
                        "description": null,
                        "final_profit": null,
                        "tips": "0.00",
                        "gid": "4da708ecb49cdf2f0ccffacd5f0afeeb",
                        "agents_processing_fee": 70,
                        "l_call_status_id": 0,
                        "l_pending_delay_dt": null,
                        "l_client_first_name": "Test",
                        "l_client_last_name": "",
                        "l_client_phone": "+12015550123",
                        "l_client_email": "xxx@gmail.com",
                        "l_client_lang": null,
                        "l_client_ua": null,
                        "l_request_hash": "5c2d61ef547d4318f3befd6f62662433",
                        "l_duplicate_lead_id": null,
                        "l_init_price": null,
                        "l_last_action_dt": "2021-08-24 09:06:50",
                        "l_dep_id": 1,
                        "l_delayed_charge": null,
                        "l_type_create": 1,
                        "l_is_test": 0,
                        "hybrid_uid": null,
                        "l_visitor_log_id": 28,
                        "l_status_dt": "2021-08-23 07:21:43",
                        "l_expiration_dt": null,
                        "l_type": null
                    },
                    "flights": [
                        {
                            "id": 698035,
                            "lead_id": 371058,
                            "origin": "YWK",
                            "destination": "YZV",
                            "departure": "2021-11-01",
                            "created": "2021-08-23 07:22:24",
                            "updated": "2021-08-23 07:23:18",
                            "flexibility": 0,
                            "flexibility_type": "-",
                            "origin_label": null,
                            "destination_label": null
                        },
                        {
                            "id": 698036,
                            "lead_id": 371058,
                            "origin": "YZV",
                            "destination": "YWK",
                            "departure": "2021-11-06",
                            "created": "2021-08-23 07:22:24",
                            "updated": "2021-08-23 07:23:18",
                            "flexibility": 0,
                            "flexibility_type": "-",
                            "origin_label": null,
                            "destination_label": null
                        }
                    ],
                    "emails": [
                        {
                            "id": 130813,
                            "client_id": 333094,
                            "email": "xxx@gmail.com",
                            "created": "2021-08-23 07:21:43",
                            "updated": "2021-08-23 07:21:43",
                            "comments": null,
                            "type": null,
                            "ce_title": null
                        }
                    ],
                    "phones": [
                        {
                            "id": 342561,
                            "client_id": 333094,
                            "phone": "+12012345678",
                            "created": "2021-05-04 06:01:34",
                            "updated": "2021-05-04 06:01:34",
                            "comments": null,
                            "is_sms": 0,
                            "validate_dt": null,
                            "type": null,
                            "cp_title": null,
                            "cp_cpl_uid": null
                        }
                    ],
                    "client": {
                        "id": 333094,
                        "first_name": "Bilbo",
                        "middle_name": "Underhill",
                        "last_name": "Baggins",
                        "created": "2021-05-04 06:01:34",
                        "updated": "2021-05-04 06:01:34",
                        "uuid": "0cbe8947-0b91-4d25-a154-f85d773a3998",
                        "parent_id": 70135,
                        "is_company": 0,
                        "is_public": 0,
                        "company_name": null,
                        "description": null,
                        "disabled": 0,
                        "rating": null,
                        "cl_type_id": 1,
                        "cl_type_create": 2,
                        "cl_project_id": 2,
                        "cl_ca_id": null,
                        "cl_ppn": null,
                        "cl_excluded": 0,
                        "cl_ip": null,
                        "cl_locale": null,
                        "cl_marketing_country": null,
                        "cl_call_recording_disabled": 0
                    },
                    "lead_data": [
                        {
                            "key": "cross_system_xp",
                            "value": "example123"
                        }
                    ]
                }
            },
            "action": "v1/lead/get",
            "response_id": 8,
            "request_dt": "2021-09-15 07:38:09",
            "response_dt": "2021-09-15 07:38:09",
            "execution_time": 0.039,
            "memory_usage": 637944
        }`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`     HTTP/1.1 404 Not Found
     {
         "name": "Not Found",
         "message": "Not found lead ID: 302",
         "code": 9,
         "status": 404,
         "type": "yii\\\\web\\\\NotFoundHttpException"
     }


@return mixed
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException`,type:"json"}]},filename:"v1/controllers/LeadController.php",groupTitle:"Leads"},{type:"post",url:"/v1/lead-request/adwords",title:"Lead create from request",version:"0.1.0",name:"Lead_create_adwords",group:"Leads",parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"50",optional:!1,field:"google_key",description:"<p>Google key</p>"},{group:"Parameter",type:"bool",optional:!0,field:"is_test",description:"<p>Is test</p>"},{group:"Parameter",type:"object",optional:!1,field:"user_column_data",description:"<p>A repeated key-value tuple transmitting user submitted data</p>"}]},examples:[{title:"Request-Example:",content:`
 {
   "google_key":"examplekey",
   "is_test":true,
   "user_column_data": [
        {
          "string_value":"john@doe.com",
          "column_id": "EMAIL"
        },
        {
          "string_value":"+11234567890",
          "column_id":"PHONE_NUMBER"
        }
  ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
     "status": 200,
     "message": "OK",
     "data": {
         "resultMessage": "LeadRequest created. ID(123)"
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`HTTP/1.1 200 OK
{
    "status": 422,
    "message": "Validation error",
    "errors": {
        "email": [
            "Email cannot be blank"
       ]
    }
}`,type:"json"}]},filename:"v1/controllers/LeadRequestController.php",groupTitle:"Leads"},{type:"post",url:"/v1/lead/update",title:"Update Lead",version:"0.1.0",name:"UpdateLead",group:"Leads",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project (if not use Basic-Authorization)</p>"},{group:"Parameter",type:"object",optional:!1,field:"lead",description:"<p>Lead data array</p>"},{group:"Parameter",type:"int",optional:!1,field:"lead.lead_id",description:"<p>Lead ID</p>"},{group:"Parameter",type:"int",optional:!1,field:"lead.source_id",description:"<p>Source ID</p>"},{group:"Parameter",type:"int",size:"1..9",optional:!1,field:"lead.adults",description:"<p>Adult count</p>"},{group:"Parameter",type:"string",size:"1",allowedValues:["E-ECONOMY","B-BUSINESS","F-FIRST","P-PREMIUM"],optional:!1,field:"lead.cabin",description:"<p>Cabin</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"lead.emails",description:"<p>Array of Emails (string)</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"lead.phones",description:"<p>Array of Phones (string)</p>"},{group:"Parameter",type:"object[]",optional:!1,field:"lead.flights",description:"<p>Array of Flights</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.origin",description:"<p>Flight Origin location Airport IATA-code</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"lead.flights.destination",description:"<p>Flight Destination location Airport IATA-code</p>"},{group:"Parameter",type:"datetime",size:"YYYY-MM-DD HH:II:SS",optional:!1,field:"lead.flights.departure",description:"<p>Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)</p>"},{group:"Parameter",type:"string",size:"2",allowedValues:["OW-ONE_WAY","RT-ROUND_TRIP","MC-MULTI_DESTINATION"],optional:!0,field:"lead.trip_type",description:"<p>Trip type (if empty - autocomplete)</p>"},{group:"Parameter",type:"int",allowedValues:["1-PENDING","2-PROCESSING","4-REJECT","5-FOLLOW_UP","8-ON_HOLD","10-SOLD","11-TRASH","12-BOOKED","13-SNOOZE"],optional:!0,field:"lead.status",description:"<p>Status</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.children",description:"<p>Children count</p>"},{group:"Parameter",type:"int",size:"0..9",optional:!0,field:"lead.infants",description:"<p>Infant count</p>"},{group:"Parameter",type:"string",size:"40",optional:!0,field:"lead.uid",description:"<p>UID value</p>"},{group:"Parameter",type:"text",optional:!0,field:"lead.notes_for_experts",description:"<p>Notes for expert</p>"},{group:"Parameter",type:"text",optional:!0,field:"lead.request_ip_detail",description:"<p>Request IP detail (autocomplete)</p>"},{group:"Parameter",type:"string",size:"50",optional:!0,field:"lead.request_ip",description:"<p>Request IP</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.snooze_for",description:"<p>Snooze for</p>"},{group:"Parameter",type:"int",optional:!0,field:"lead.rating",description:"<p>Rating</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_first_name",description:"<p>Client first name</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_last_name",description:"<p>Client last name</p>"},{group:"Parameter",type:"string",size:"3..100",optional:!0,field:"lead.client_middle_name",description:"<p>Client middle name</p>"}]},examples:[{title:"Request-Example:",content:`{
   "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
   "lead": {
       "lead_id": 38,
       "flights": [
           {
               "origin": "KIV",
               "destination": "DME",
               "departure": "2018-10-13 13:50:00",
           },
           {
               "origin": "DME",
               "destination": "KIV",
               "departure": "2018-10-18 10:54:00",
           }
       ],
       "emails": [
         "email1@gmail.com",
         "email2@gmail.com",
       ],
       "phones": [
         "+373-69-487523",
         "022-45-7895-89",
       ],
       "source_id": 38,
       "adults": 1,
       "client_first_name": "Alexandr",
       "client_last_name": "Freeman"
   }
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"Integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"},{group:"Success 200",type:"Array",optional:!1,field:"data",description:"<p>Data Array</p>"}]},examples:[{title:"Success-Response:",content:"HTTP/1.1 200 OK",type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`     HTTP/1.1 422 Unprocessable entity
     {
         "name": "Unprocessable entity",
         "message": "Flight [0]: Destination should contain at most 3 characters.",
         "code": 5,
         "status": 422,
         "type": "yii\\\\web\\\\UnprocessableEntityHttpException"
     }


@return mixed
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException
@throws \\yii\\db\\Exception`,type:"json"}]},filename:"v1/controllers/LeadController.php",groupTitle:"Leads"},{type:"post",url:"/v1/lead/call-expert",title:"Update Lead Call Expert",version:"0.1.0",name:"UpdateLeadCallExpert",group:"Leads",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"object",optional:!1,field:"call",description:"<p>CallExpert data array</p>"},{group:"Parameter",type:"int",optional:!1,field:"call.lce_id",description:"<p>Call Expert ID</p>"},{group:"Parameter",type:"int",allowedValues:["1-PENDING","2-PROCESSING","3-DONE","4-CANCEL"],optional:!1,field:"call.lce_status_id",description:"<p>Status Id</p>"},{group:"Parameter",type:"text",optional:!1,field:"call.lce_response_text",description:"<p>Response text from Expert (Required on lce_status_id = 3)</p>"},{group:"Parameter",type:"string",size:"30",optional:!1,field:"call.lce_expert_username",description:"<p>Expert Username (Required on lce_status_id = 3)</p>"},{group:"Parameter",type:"int",optional:!0,field:"call.lce_expert_user_id",description:"<p>Expert Id</p>"},{group:"Parameter",type:"array[]",optional:!0,field:"call.lce_response_lead_quotes",description:"<p>Array of UID quotes (string)</p>"}]},examples:[{title:"Request-Example:",content:`{
   "call": {
       "lce_id": 38,
       "lce_response_text": "Message from expert",
       "lce_expert_username": "Alex",
       "lce_expert_user_id": 12,
       "lce_response_lead_quotes": [
             "5ccbe7a458765",
             "5ccbe797a6a22"
         ],
       "lce_status_id": 2
   }
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"String",optional:!1,field:"status",description:"<p>Response Status</p>"},{group:"Success 200",type:"String",optional:!1,field:"name",description:"<p>Response Name</p>"},{group:"Success 200",type:"Integer",optional:!1,field:"code",description:"<p>Response Code</p>"},{group:"Success 200",type:"String",optional:!1,field:"message",description:"<p>Response Message</p>"},{group:"Success 200",type:"Array",optional:!1,field:"data",description:"<p>Response Data Array</p>"},{group:"Success 200",type:"String",optional:!1,field:"action",description:"<p>Response API action</p>"},{group:"Success 200",type:"Integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
 "status": 200,
 "name": "Success",
 "code": 0,
 "message": "",
 "data": {
     "response": {
         "lce_id": 8,
         "lce_lead_id": 113947,
         "lce_request_text": "12\\r\\n2\\r\\nqwe qwe qwe qwe qwe fasd asd fasdf\\r\\n",
         "lce_request_dt": "2019-05-03 14:08:20",
         "lce_response_text": "Test expert text",
         "lce_response_lead_quotes": "[\\"5ccbe7a458765\\", \\"5ccbe797a6a22\\"]",
         "lce_response_dt": "2019-05-07 09:14:01",
         "lce_status_id": 3,
         "lce_agent_user_id": 167,
         "lce_expert_user_id": "2",
         "lce_expert_username": "Alex",
         "lce_updated_dt": "2019-05-07 09:14:01"
     }
 },
 "action": "v1/lead/call-expert",
 "response_id": 457671,
 "request_dt": "2019-05-07 09:14:01",
 "response_dt": "2019-05-07 09:14:01"
}`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`

HTTP/1.1 401 Unauthorized
 {
     "name": "Unauthorized",
     "message": "Your request was made with invalid credentials.",
     "code": 0,
     "status": 401,
     "type": "yii\\\\web\\\\UnauthorizedHttpException"
 }


HTTP/1.1 400 Bad Request
 {
     "name": "Bad Request",
     "message": "Not found LeadCallExpert data on POST request",
     "code": 6,
     "status": 400,
     "type": "yii\\\\web\\\\BadRequestHttpException"
 }


HTTP/1.1 404 Not Found
 {
     "name": "Not Found",
     "message": "Not found LeadCallExpert ID: 100",
     "code": 9,
     "status": 404,
     "type": "yii\\\\web\\\\NotFoundHttpException"
 }


HTTP/1.1 422 Unprocessable entity
 {
     "name": "Unprocessable entity",
     "message": "Response Text cannot be blank.; Expert Username cannot be blank.",
     "code": 5,
     "status": 422,
     "type": "yii\\\\web\\\\UnprocessableEntityHttpException"
 }

@return array
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException`,type:"json"}]},filename:"v1/controllers/LeadController.php",groupTitle:"Leads"},{type:"post",url:"/v2/offer/confirm-alternative",title:"Confirm Alternative Offer",version:"0.2.0",name:"ConfirmAlternativeOffer",group:"Offer",permission:[{name:"Authorized User"}],description:"<p>Offer can only be confirmed if it is in the Pending status</p>",header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 32",optional:!1,field:"gid",description:"<p>Offer gid</p>"}]},examples:[{title:"Request-Example:",content:`
{
    "gid": "04d3fe3fc74d0514ee93e208a52bcf90",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
 {
            "status": 200,
            "message": "OK",
        }`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "Error",
            "errors": [
                "Not found Offer"
            ],
            "code": "18402"
        }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Validation Error
{
            "status": 422,
            "message": "Validation error",
            "errors": {
                "gid": [
                    "Gid should contain at most 32 characters."
                ]
            },
            "code": "18401"
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Validation Error
{
            "status": 422,
            "message": "Error",
            "errors": [
                "Offer does not contain quotes that can be confirmed"
            ],
            "code": "18404"
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
            "status": 400,
            "message": "Load data error",
            "errors": [
                "Not found Offer data on POST request"
            ],
            "code": "18400"
        }`,type:"json"}]},filename:"v2/controllers/OfferController.php",groupTitle:"Offer"},{type:"post",url:"/v2/offer/view",title:"View Offer",version:"0.2.0",name:"ViewOffer",group:"Offer",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!1,field:"offerGid",description:"<p>Offer gid</p>"},{group:"Parameter",type:"object",optional:!1,field:"visitor",description:"<p>Visitor</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"visitor.id",description:"<p>Visitor Id</p>"},{group:"Parameter",type:"string",optional:!1,field:"visitor.ipAddress",description:"<p>Visitor Ip Address</p>"},{group:"Parameter",type:"string",size:"255",optional:!1,field:"visitor.userAgent",description:"<p>Visitor User Agent</p>"}]},examples:[{title:"Request-Example:",content:`
{
    "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
    "visitor": {
        "id": "hdsjfghsd5489tertwhf289hfgkewr",
        "ipAddress": "12.12.13.22",
        "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "offer": {
        "of_gid": "ea6dc06421db46b5a77e8505d0934f38",
        "of_uid": "of604642e300c54",
        "of_name": "Offer 2",
        "of_lead_id": 513111,
        "of_status_id": 1,
        "of_client_currency": "USD",
        "of_client_currency_rate": 1,
        "of_app_total": 343.5,
        "of_client_total": 343.5,
        "of_status_name": "New",
        "quotes": [
            {
                "pq_gid": "f81636da78e007fcc6653d26a3650285",
                "pq_name": "",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 343.5,
                "pq_origin_price": 343.5,
                "pq_client_price": 343.5,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 47,
                    "fq_source_id": null,
                    "fq_product_quote_id": 159,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMDktMTYqTE9+I0xPNTE2I0xPMjgxfmxjOmVuX3Vz\\",\\"routingId\\":1,\\"prices\\":{\\"lastTicketDate\\":\\"2021-03-11\\",\\"totalPrice\\":343.5,\\"totalTax\\":184.5,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":2,\\"baseFare\\":58,\\"pubBaseFare\\":58,\\"baseTax\\":61.5,\\"markup\\":0,\\"comm\\":0,\\"price\\":119.5,\\"tax\\":61.5,\\"oBaseFare\\":{\\"amount\\":58,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":61.5,\\"currency\\":\\"USD\\"}},\\"CHD\\":{\\"codeAs\\":\\"CHD\\",\\"cnt\\":1,\\"baseFare\\":43,\\"pubBaseFare\\":43,\\"baseTax\\":61.5,\\"markup\\":0,\\"comm\\":0,\\"price\\":104.5,\\"tax\\":61.5,\\"oBaseFare\\":{\\"amount\\":43,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":61.5,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":true,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"ex\\",\\"applicability\\":\\"before\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"ex\\",\\"applicability\\":\\"after\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-09-16 18:25\\",\\"arrivalTime\\":\\"2021-09-16 19:15\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"516\\",\\"bookingClass\\":\\"S\\",\\"duration\\":110,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"WAW\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"LO\\",\\"airEquipType\\":\\"DH4\\",\\"marketingAirline\\":\\"LO\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":508,\\"cabin\\":\\"Y\\",\\"cabinIsBasic\\":true,\\"brandId\\":\\"685421\\",\\"brandName\\":\\"ECONOMY SAVER\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"S1SAV14\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":0},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":0}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2021-09-17 07:30\\",\\"arrivalTime\\":\\"2021-09-17 09:25\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"281\\",\\"bookingClass\\":\\"S\\",\\"duration\\":175,\\"departureAirportCode\\":\\"WAW\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"LO\\",\\"airEquipType\\":\\"738\\",\\"marketingAirline\\":\\"LO\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":893,\\"cabin\\":\\"Y\\",\\"cabinIsBasic\\":true,\\"brandId\\":\\"685421\\",\\"brandName\\":\\"ECONOMY SAVER\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"S1SAV14\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":0},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":0}},\\"recheckBaggage\\":false}],\\"duration\\":1020}],\\"maxSeats\\":7,\\"paxCnt\\":3,\\"validatingCarrier\\":\\"LO\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"b58ab976-7391-40b0-a1d2-44a2821d44cf\\",\\"availabilitySources\\":\\"S,S\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":7}},\\"ngsFeatures\\":{\\"stars\\":1,\\"name\\":\\"ECONOMY SAVER\\",\\"list\\":[]},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0wOS0xNg==\\",\\"lang\\":\\"en\\",\\"rank\\":6,\\"cheapest\\":true,\\"fastest\\":false,\\"best\\":false,\\"bags\\":0,\\"country\\":\\"us\\"},\\"price\\":119.5,\\"originRate\\":1,\\"stops\\":[1],\\"time\\":[{\\"departure\\":\\"2021-09-16 18:25\\",\\"arrival\\":\\"2021-09-17 09:25\\"}],\\"bagFilter\\":\\"\\",\\"airportChange\\":false,\\"technicalStopCnt\\":0,\\"duration\\":[1020],\\"totalDuration\\":1020,\\"topCriteria\\":\\"cheapest\\",\\"rank\\":6}",
                    "fq_last_ticket_date": "2021-03-11",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 76,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 1,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "fqt_id": 100,
                            "fqt_uid": "fqt6046483f5c6cf",
                            "fqt_key": null,
                            "fqt_duration": 1020,
                            "segments": [
                                {
                                    "fqs_uid": "fqs6046483e349c6",
                                    "fqs_departure_dt": "2021-09-16 18:25:00",
                                    "fqs_arrival_dt": "2021-09-16 19:15:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 516,
                                    "fqs_booking_class": "S",
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
                                    "fqs_fare_code": "S1SAV14",
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
                                            "qsb_flight_quote_segment_id": 255,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 255,
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
                                    "fqs_uid": "fqs6046483e37fc7",
                                    "fqs_departure_dt": "2021-09-17 07:30:00",
                                    "fqs_arrival_dt": "2021-09-17 09:25:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 281,
                                    "fqs_booking_class": "S",
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
                                    "fqs_fare_code": "S1SAV14",
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
                                            "qsb_flight_quote_segment_id": 256,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 0,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 256,
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
                            "qpp_fare": "58.00",
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "58.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "58.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "ADT"
                        },
                        {
                            "qpp_fare": "43.00",
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "CHD"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6046483b5f034",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6046483b61c29",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6046483b64835",
                            "fp_pax_id": null,
                            "fp_pax_type": "CHD",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                },
                "product": {
                    "pr_gid": "",
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513111,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null,
                    "holder": {
                        "ph_first_name": "test",
                        "ph_last_name": "test",
                        "ph_email": "test@test.test",
                        "ph_phone_number": "+19074861000"
                    }
                },
                "productQuoteOptions": [],
                "origin": {
                    "pq_gid": "eebad5110d96b60fee6d2084c866ce28",
                    "pq_name": "ROO.ST & ROO.ST",
                    "pq_order_id": 526,
                    "pq_description": null,
                    "pq_status_id": 8,
                    "pq_price": 7065.34,
                    "pq_origin_price": 6292.6,
                    "pq_client_price": 7065.34,
                    "pq_service_fee_sum": 238.93,
                    "pq_origin_currency": "USD",
                    "pq_client_currency": "USD",
                    "pq_status_name": "Error",
                    "pq_files": [],
                    "data": {
                        "hq_hash_key": "f293c1629f74b2938d41cdea92769ffe",
                        "hq_destination_name": "Chisinau",
                        "hq_hotel_name": "Cosmos Hotel",
                        "hq_request_hash": "9de433bc355aed187eca25f7628a480e",
                        "hq_booking_id": null,
                        "hq_json_booking": null,
                        "hq_check_in_date": "2021-09-10",
                        "hq_check_out_date": "2021-09-30",
                        "hq_nights": 20,
                        "hotel_request": {
                            "ph_check_in_date": "2021-09-10",
                            "ph_check_out_date": "2021-09-30",
                            "ph_destination_code": "KIV",
                            "ph_destination_label": "Moldova, Chisinau",
                            "ph_holder_name": null,
                            "ph_holder_surname": null,
                            "destination_city": "Chisinau"
                        },
                        "hotel": {
                            "hl_name": "Cosmos Hotel",
                            "hl_star": "",
                            "hl_category_name": "3 STARS",
                            "hl_destination_name": "Chisinau",
                            "hl_zone_name": "Chisinau",
                            "hl_country_code": "MD",
                            "hl_state_code": "MD",
                            "hl_description": "The hotel is situated in the heart of Chisinau, the capital of Moldova. It is perfectly located for access to the business centre, cultural institutions and much more. Chisinau Airport is only 15 minutes away and the railway station is less than 5 minutes away from the hotel.\\n\\nThe city hotel offers a choice of 150 rooms, 24-hour reception and check-out services in the lobby, luggage storage, a hotel safe, currency exchange facility and a cloakroom. There is lift access to the upper floors as well as an on-site restaurant and conference facilities. Internet access, a laundry service (fees apply) and free parking in the car park are also on offer to guests during their stay.\\n\\nAll the rooms are furnished with double or king-size beds and provide an en suite bathroom with a shower. Air conditioning, central heating, satellite TV, a telephone, mini fridge, radio and free wireless Internet access are also on offer.\\n\\nThere is a golf course about 12 km from the hotel.\\n\\nThe hotel's restaurant offers a wide selection of local and European cuisine. Breakfast is served as a buffet and lunch and dinner can be chosen \xE0 la carte.",
                            "hl_address": "NEGRUZZI, 2",
                            "hl_postal_code": "MD2001",
                            "hl_city": "CHISINAU",
                            "hl_email": "info@hotel-cosmos.com",
                            "hl_web": null,
                            "hl_phone_list": [
                                {
                                    "type": "PHONEBOOKING",
                                    "number": "+37322890054"
                                },
                                {
                                    "type": "PHONEHOTEL",
                                    "number": "+37322837505"
                                },
                                {
                                    "type": "FAXNUMBER",
                                    "number": "+37322542744"
                                }
                            ],
                            "hl_image_list": [
                                {
                                    "url": "14/148030/148030a_hb_a_001.jpg",
                                    "type": "GEN"
                                }
                            ],
                            "hl_image_base_url": null,
                            "json_booking": null
                        },
                        "rooms": [
                            {
                                "hqr_room_name": "Room Standard",
                                "hqr_class": "NOR",
                                "hqr_amount": 188.78,
                                "hqr_currency": "USD",
                                "hqr_board_name": "BED AND BREAKFAST",
                                "hqr_rooms": 1,
                                "hqr_adults": 1,
                                "hqr_children": null,
                                "hqr_cancellation_policies": []
                            },
                            {
                                "hqr_room_name": "Room Standard",
                                "hqr_class": "NRF",
                                "hqr_amount": 125.85,
                                "hqr_currency": "USD",
                                "hqr_board_name": "ROOM ONLY",
                                "hqr_rooms": 1,
                                "hqr_adults": 2,
                                "hqr_children": null,
                                "hqr_cancellation_policies": [
                                    {
                                        "from": "2021-12-31T21:59:00:00:00",
                                        "amount": 72.46
                                    },
                                    {
                                        "from": "2021-12-05T21:59:00+00:00",
                                        "amount": 134.92
                                    }
                                ]
                            }
                        ]
                    },
                    "product": {
                        "pr_gid": "337b7f7fe27143e543c31b0b60688de0",
                        "pr_type_id": 2,
                        "pr_name": null,
                        "pr_lead_id": null,
                        "pr_description": null,
                        "pr_status_id": null,
                        "pr_service_fee_percent": null,
                        "holder": {
                            "ph_first_name": "Test 2",
                            "ph_last_name": "Test 2",
                            "ph_middle_name": null,
                            "ph_email": "test+2@test.test",
                            "ph_phone_number": "+19074861000"
                        }
                    },
                    "productQuoteOptions": []
                }
            }
        ],
        "lead_data": [
           {
               "ld_field_key": "kayakclickid",
               "ld_field_value": "example_value132"
           }
        ]
    },
    "technical": {
        "action": "v2/offer/view",
        "response_id": 496,
        "request_dt": "2021-03-08 15:57:33",
        "response_dt": "2021-03-08 15:57:33",
        "execution_time": 0.104,
        "memory_usage": 1290648
    },
    "request": {
        "offerGid": "ea6dc06421db46b5a77e8505d0934f38"
    }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Error",
    "errors": [
        "Not found Offer"
    ],
    "code": "18302",
    "technical": {
        "action": "v2/offer/view",
        "response_id": 11933860,
        "request_dt": "2020-02-03 13:07:10",
        "response_dt": "2020-02-03 13:07:10",
        "execution_time": 0.015,
        "memory_usage": 151792
    },
    "request": {
        "offerGid": "04d3fe3fc74d0514ee93e208a5x2bcf90",
        "visitor": {
            "id": "hdsjfghsd5489tertwhf289hfgkewr",
            "ipAddress": "12.12.12.12",
            "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
        }
    }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
       "visitor.ipAddress": [
            "Ip Address cant be array."
        ]
    },
    "code": "18301",
    "technical": {
         "action": "v2/offer/view",
         "response_id": 11933854,
         "request_dt": "2020-02-03 12:44:13",
         "response_dt": "2020-02-03 12:44:13",
         "execution_time": 0.013,
         "memory_usage": 127680
    },
    "request": {
         "offerGid": "04d3fe3fc74d0514ee93e208a52bcf90",
         "visitor": {
             "id": "hdsjfghsd5489tertwhf289hfgkewr",
             "ipAddress": [],
             "userAgent": "mozilea/asdfsdf/ as/dfgsdf gsdf gsdgf/ds"
         }
    }
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
    "status": 400,
    "message": "Load data error",
    "errors": [
        "Not found Offer data on POST request"
    ],
    "code": "18300",
    "technical": {
        "action": "v2/offer/view",
        "response_id": 11933856,
        "request_dt": "2020-02-03 12:49:20",
        "response_dt": "2020-02-03 12:49:20",
        "execution_time": 0.017,
        "memory_usage": 114232
    },
    "request": []
}`,type:"json"}]},filename:"v2/controllers/OfferController.php",groupTitle:"Offer"},{type:"post",url:"/v2/order/cancel",title:"Cancel Order",version:"0.2.0",name:"CancelOrder",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!1,field:"gid",description:"<p>Order gid</p>"}]},examples:[{title:"Request-Example:",content:`
{
    "gid": "04d3fe3fc74d0514ee93e208a52bcf90"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
   "status": 200,
   "message": "OK",
   "code": 0,
   "technical": {
       "action": "v2/order/cancel",
       "response_id": 15629,
       "request_dt": "2021-04-01 09:03:11",
       "response_dt": "2021-04-01 09:03:11",
       "execution_time": 0.019,
       "memory_usage": 186192
   },
   "request": {
      "gid": "04d3fe3fc74d0514ee93e208a52bcf90"
   }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
      "status": 400,
      "message": "Load data error",
      "errors": [
          "Not found data on POST request"
      ],
      "code": 10,
      "request": {
          ...
      },
      "technical": {
          ...
     }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Validation error",
    "errors": {
         "gid": [
           "Gid is invalid."
        ]
    },
    "code": 20,
    "technical": {
          ...
    },
    "request": {
          ...
    }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Error",
    "errors": {
        "The order is not available for processing."
    },
    "code": 30,
    "technical": {
          ...
    },
    "request": {
          ...
    }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Error",
    "errors": {
        "Unable to process flight cancellation."
    },
    "code": 40,
    "technical": {
          ...
    },
    "request": {
          ...
    }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
    "status": 422,
    "message": "Error",
    "errors": {
        "Unable to process hotel cancellation."
    },
    "code": 50,
    "technical": {
          ...
    },
    "request": {
          ...
    }
}`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"post",url:"/v2/order/create",title:"Create Order",version:"0.2.0",name:"CreateOrder",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
     "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     "Accept-Encoding": "Accept-Encoding: gzip, deflate"
 }`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 10",optional:!1,field:"sourceCid",description:"<p>Source cid</p>"},{group:"Parameter",type:"string",size:"max 32",optional:!1,field:"offerGid",description:"<p>Offer gid</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!1,field:"languageId",description:"<p>Language Id</p>"},{group:"Parameter",type:"string",size:"max 2",optional:!1,field:"marketCountry",description:"<p>Market Country</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"productQuotes",description:"<p>Product Quotes</p>"},{group:"Parameter",type:"string",size:"max 32",optional:!1,field:"productQuotes.gid",description:"<p>Product Quote Gid</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"productQuotes.productOptions",description:"<p>Quote Options</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"productQuotes.productOptions.productOptionKey",description:"<p>Product option key</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"productQuotes.productOptions.name",description:"<p>Name</p>"},{group:"Parameter",type:"string",optional:!0,field:"productQuotes.productOptions.description",description:"<p>Description</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productOptions.price",description:"<p>Price</p>"},{group:"Parameter",type:"string",optional:!1,field:"productQuotes.productOptions.json_data",description:"<p>Original data</p>"},{group:"Parameter",type:"Object",optional:!1,field:"productQuotes.productHolder",description:"<p>Holder Info</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"productQuotes.productHolder.firstName",description:"<p>Holder first name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"productQuotes.productHolder.lastName",description:"<p>Holder last name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"productQuotes.productHolder.middleName",description:"<p>Holder middle name</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"productQuotes.productHolder.email",description:"<p>Holder email</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"productQuotes.productHolder.phone",description:"<p>Holder phone</p>"},{group:"Parameter",type:"Object[]",optional:!0,field:"productQuotes.productHolder.data",description:"<p>Quote options</p>"},{group:"Parameter",type:"string",optional:!1,field:"productQuotes.productHolder.data.segment_uid",description:"<p>Segment uid</p>"},{group:"Parameter",type:"string",optional:!1,field:"productQuotes.productHolder.data.pax_uid",description:"<p>Pax uid</p>"},{group:"Parameter",type:"string",optional:!1,field:"productQuotes.productHolder.data.trip_uid",description:"<p>Trip uid</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.total",description:"<p>Total</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!1,field:"productQuotes.productHolder.data.currency",description:"<p>Currency</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.usd_total",description:"<p>Total price in usd</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.base_price",description:"<p>Base price in usd</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.markup_amount",description:"<p>Markup amount</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.usd_base_price",description:"<p>Base price in usd</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"productQuotes.productHolder.data.usd_markup_amount",description:"<p>Markup amount in usd</p>"},{group:"Parameter",type:"string",size:"max 255",optional:!1,field:"productQuotes.productHolder.data.display_name",description:"<p>Display name</p>"},{group:"Parameter",type:"Object",optional:!1,field:"payment",description:"<p>Payment</p>"},{group:"Parameter",type:"string",optional:!1,field:"payment.type",description:"<p>Type</p>"},{group:"Parameter",type:"string",size:"max 255",optional:!1,field:"payment.transactionId",description:"<p>Transaction Id</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!1,field:"payment.date",description:"<p>Date</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"payment.amount",description:"<p>Amount</p>"},{group:"Parameter",type:"string",size:"max 3",optional:!1,field:"payment.currency",description:"<p>Currency</p>"},{group:"Parameter",type:"Object",optional:!0,field:"billingInfo",description:"<p>BillingInfo</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"billingInfo.address",description:"<p>Address</p>"},{group:"Parameter",type:"string",size:"max 2",optional:!1,field:"billingInfo.country_id",description:"<p>Country Id</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!1,field:"billingInfo.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"max 10",optional:!1,field:"billingInfo.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"billingInfo.phone",description:"<p>Phone <code>Deprecated</code></p>"},{group:"Parameter",type:"string",size:"max 160",optional:!1,field:"billingInfo.email",description:"<p>Email <code>Deprecated</code></p>"},{group:"Parameter",type:"Object",optional:!1,field:"creditCard",description:"<p>Credit Card</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"creditCard.holder_name",description:"<p>Holder Name</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"creditCard.number",description:"<p>Credit Card Number</p>"},{group:"Parameter",type:"string",optional:!0,field:"creditCard.type",description:"<p>Credit Card type</p>"},{group:"Parameter",type:"string",size:"max 18",optional:!1,field:"creditCard.expiration",description:"<p>Credit Card expiration</p>"},{group:"Parameter",type:"string",size:"max 4",optional:!1,field:"creditCard.cvv",description:"<p>Credit Card cvv</p>"},{group:"Parameter",type:"Object",optional:!0,field:"Tips",description:"<p>Tips</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"Tips.total_amount",description:"<p>Total Amount</p>"},{group:"Parameter",type:"Object",optional:!1,field:"Paxes[]",description:"<p>Paxes</p>"},{group:"Parameter",type:"string",optional:!1,field:"Paxes.uid",description:"<p>Uid</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"Paxes.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"Paxes.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"Paxes.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"Paxes.nationality",description:"<p>Nationality</p>"},{group:"Parameter",type:"string",size:"max 1",optional:!0,field:"Paxes.gender",description:"<p>Gender</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"Paxes.birth_date",description:"<p>Birth Date</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!0,field:"Paxes.email",description:"<p>Email</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"Paxes.language",description:"<p>Language</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"Paxes.citizenship",description:"<p>Citizenship</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"contactsInfo",description:"<p>BillingInfo</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"contactsInfo.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"contactsInfo.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"contactsInfo.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!0,field:"contactsInfo.phone",description:"<p>Phone number</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"contactsInfo.email",description:"<p>Email</p>"},{group:"Parameter",type:"Object",optional:!1,field:"Request",description:"<p>Request Data for BO</p>"}]},examples:[{title:"Request-Example:",content:`{
    "sourceCid": "OVA102",
    "offerGid": "73c8bf13111feff52794883446461740",
    "languageId": "en-US",
    "marketCountry": "US",
    "productQuotes": [
        {
            "gid": "aebf921f5a64a7ac98d4942ace67e498",
            "productOptions": [
                {
                    "productOptionKey": "travelGuard",
                    "name": "Travel Guard",
                    "description": "",
                    "price": 20,
                    "json_data": "",
                    "data": [
                        {
                            "segment_uid": "fqs604635abf02ae",
                            "pax_uid": "fp604635abe9c6a",
                            "trip_uid": "fqt604635abed0e0",
                            "total": 2.00,
                            "currency": "USD",
                            "usd_total": 2.00,
                            "base_price": 2.00,
                            "markup_amount": 0,
                            "usd_base_price": 2.00,
                            "usd_markup_amount": 0,
                            "display_name": "Seat: 18E, CQ 7602"
                        }
                    ]

                }
            ],
            "productHolder": {
                "firstName": "Test",
                "lastName": "Test",
                "middleName": "",
                "email": "test@test.test",
                "phone": "+19074861000"
            }
        },
        {
            "gid": "6fcfc43e977dabffe6a979ebdaddfvr2",
            "productHolder": {
                "firstName": "Test 2",
                "lastName": "Test 2",
                "email": "test2@test.test",
                "phone": "+19074861002"
            }
        }
    ],
    "payment": {
        "type": "card",
        "transactionId": 1234567890,
        "date": "2021-03-20",
        "amount": 821.49,
        "currency": "USD"
    },
    "billingInfo": {
        "first_name": "Barbara Elmore",
        "middle_name": "",
        "last_name": "T",
        "address": "1013 Weda Cir",
        "country_id": "US",
        "city": "Mayfield",
        "state": "KY",
        "zip": "99999",
        "phone": "+19074861000",
        "email": "mike.kane@techork.com"
    },
    "creditCard": {
        "holder_name": "Barbara Elmore",
        "number": "1111111111111111",
        "type": "Visa",
        "expiration": "07 / 23",
        "cvv": "324"
    },
    "tips": {
        "total_amount": 20
    },
    "paxes": [
        {
            "uid": "fp6047195e67b7a",
            "first_name": "Test name",
            "last_name": "Test last name",
            "middle_name": "Test middle name",
            "nationality": "US",
            "gender": "M",
            "birth_date": "1963-04-07",
            "email": "mike.kane@techork.com",
            "language": "en-US",
            "citizenship": "US"
        }
    ],
    "contactsInfo": [
        {
            "first_name": "Barbara",
            "last_name": "Elmore",
            "middle_name": "",
            "phone": "+19074861000",
            "email": "barabara@test.com"
        },
        {
            "first_name": "John",
            "last_name": "Doe",
            "middle_name": "",
            "phone": "+19074865678",
            "email": "john@test.com"
        }
    ],
    "Request": {
        "offerGid": "85a06c376a083f47e56b286b1265c160",
        "offerUid": "of60264c1484090",
        "apiKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
        "source": "I1B1L1",
        "subSource": "-",
        "totalOrderAmount": 821.49,
        "FlightRequest": {
            "productGid": "c6ae37ae73380c773cadf28fc0af9db2",
            "uid": "OE96040",
            "email": "mike.kane@techork.com",
            "marker": null,
            "client_ip_address": "92.115.180.30",
            "trip_protection_amount": "0",
            "insurance_code": "P7",
            "is_facilitate": 0,
            "delay_change": false,
            "is_b2b": false,
            "uplift": false,
            "alipay": false,
            "user_country": "us",
            "user_language": "en-US",
            "user_time_format": "h:mm a",
            "user_month_date_format": {
                "long": "EEE MMM d",
                "short": "MMM d",
                "fullDateLong": "EEE MMM d",
                "fullDateShort": "MMM d, YYYY"
            },
            "currency_symbol": "$",
            "pnr": null
        },
        "HotelRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "DriverRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "AttractionRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "CruiseRequest": {
            "productGid": "cdd82f2616f600f71a68e9399c51276e"
        },
        "Card": {
            "user_id": null,
            "nickname": "B****** E***** T",
            "number": "************6444",
            "type": "Visa",
            "expiration_date": "07 / 2023",
            "first_name": "Barbara Elmore",
            "middle_name": "",
            "last_name": "T",
            "address": "1013 Weda Cir",
            "country_id": "US",
            "city": "Mayfield",
            "state": "KY",
            "zip": "99999",
            "phone": "+19074861000",
            "deleted": null,
            "cvv": "***",
            "auth_attempts": null,
            "country": "United States",
            "calling": "",
            "client_ip_address": "92.115.180.30",
            "email": "mike.kane@techork.com",
            "document": null
        },
        "AirRouting": {
            "results": [
                {
                    "gds": "S",
                    "key": "2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=",
                    "pcc": "8KI0",
                    "cons": "GTT",
                    "keys": {
                        "services": {
                            "support": {
                                "amount": 75
                            }
                        },
                        "seatHoldSeg": {
                            "trip": 0,
                            "seats": 9,
                            "segment": 0
                        },
                        "verification": {
                            "headers": {
                                "X-Client-Ip": "92.115.180.30",
                                "X-Kiv-Cust-Ip": "92.115.180.30",
                                "X-Kiv-Cust-ipv": "0",
                                "X-Kiv-Cust-ssid": "ovago-dev-0484692",
                                "X-Kiv-Cust-direct": "true",
                                "X-Kiv-Cust-browser": "desktop"
                            }
                        }
                    },
                    "meta": {
                        "eip": 0,
                        "bags": 2,
                        "best": false,
                        "lang": "en",
                        "rank": 6,
                        "group1": "LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75",
                        "country": "us",
                        "fastest": false,
                        "noavail": false,
                        "cheapest": true,
                        "searchId": "T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw"
                    },
                    "cabin": "Y",
                    "trips": [
                        {
                            "tripId": 1,
                            "duration": 1150,
                            "segments": [
                                {
                                    "meal": "D",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 7305,
                                    "duration": 870,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 1,
                                    "arrivalTime": "2021-05-15 04:00",
                                    "airEquipType": "773",
                                    "bookingClass": "U",
                                    "flightNumber": "103",
                                    "departureTime": "2021-05-13 22:30",
                                    "marriageGroup": "O",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "MNL",
                                    "departureAirportCode": "LAX",
                                    "arrivalAirportTerminal": "2",
                                    "departureAirportTerminal": "B"
                                },
                                {
                                    "meal": "B",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 728,
                                    "duration": 130,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 2,
                                    "arrivalTime": "2021-05-15 08:40",
                                    "airEquipType": "321",
                                    "bookingClass": "U",
                                    "flightNumber": "890",
                                    "departureTime": "2021-05-15 06:30",
                                    "marriageGroup": "I",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "TPE",
                                    "departureAirportCode": "MNL",
                                    "arrivalAirportTerminal": "1",
                                    "departureAirportTerminal": "1"
                                }
                            ]
                        },
                        {
                            "tripId": 2,
                            "duration": 1490,
                            "segments": [
                                {
                                    "meal": "H",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 728,
                                    "duration": 145,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 1,
                                    "arrivalTime": "2021-06-20 12:05",
                                    "airEquipType": "321",
                                    "bookingClass": "U",
                                    "flightNumber": "891",
                                    "departureTime": "2021-06-20 09:40",
                                    "marriageGroup": "O",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "MNL",
                                    "departureAirportCode": "TPE",
                                    "arrivalAirportTerminal": "2",
                                    "departureAirportTerminal": "1"
                                },
                                {
                                    "meal": "D",
                                    "stop": 0,
                                    "cabin": "Y",
                                    "stops": [],
                                    "baggage": {
                                        "ADT": {
                                            "carryOn": true,
                                            "airlineCode": "PR",
                                            "allowPieces": 2,
                                            "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                            "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                        }
                                    },
                                    "mileage": 7305,
                                    "duration": 805,
                                    "fareCode": "U9XBUS",
                                    "segmentId": 2,
                                    "arrivalTime": "2021-06-20 19:30",
                                    "airEquipType": "773",
                                    "bookingClass": "U",
                                    "flightNumber": "102",
                                    "departureTime": "2021-06-20 21:05",
                                    "marriageGroup": "I",
                                    "recheckBaggage": false,
                                    "marketingAirline": "PR",
                                    "operatingAirline": "PR",
                                    "arrivalAirportCode": "LAX",
                                    "departureAirportCode": "MNL",
                                    "arrivalAirportTerminal": "B",
                                    "departureAirportTerminal": "1"
                                }
                            ]
                        }
                    ],
                    "paxCnt": 1,
                    "prices": {
                        "comm": 0,
                        "isCk": false,
                        "ccCap": 16.900002,
                        "markup": 50,
                        "oMarkup": {
                            "amount": 50,
                            "currency": "USD"
                        },
                        "markupId": 8833,
                        "totalTax": 321.75,
                        "markupUid": "1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e",
                        "totalPrice": 767.75,
                        "lastTicketDate": "2021-03-31"
                    },
                    "currency": "USD",
                    "fareType": "SR",
                    "maxSeats": 9,
                    "tripType": "RT",
                    "penalties": {
                        "list": [
                            {
                                "type": "re",
                                "permitted": false,
                                "applicability": "before"
                            },
                            {
                                "type": "re",
                                "permitted": false,
                                "applicability": "after"
                            },
                            {
                                "type": "ex",
                                "amount": 425,
                                "oAmount": {
                                    "amount": 425,
                                    "currency": "USD"
                                },
                                "permitted": true,
                                "applicability": "before"
                            },
                            {
                                "type": "ex",
                                "amount": 425,
                                "oAmount": {
                                    "amount": 425,
                                    "currency": "USD"
                                },
                                "permitted": true,
                                "applicability": "after"
                            }
                        ],
                        "refund": false,
                        "exchange": true
                    },
                    "routingId": 1,
                    "currencies": [
                        "USD"
                    ],
                    "founded_dt": "2021-02-25 13:44:54.570",
                    "passengers": {
                        "ADT": {
                            "cnt": 1,
                            "tax": 321.75,
                            "comm": 0,
                            "ccCap": 16.900002,
                            "price": 767.75,
                            "codeAs": "JCB",
                            "markup": 50,
                            "occCap": {
                                "amount": 16.900002,
                                "currency": "USD"
                            },
                            "baseTax": 271.75,
                            "oMarkup": {
                                "amount": 50,
                                "currency": "USD"
                            },
                            "baseFare": 446,
                            "oBaseTax": {
                                "amount": 271.75,
                                "currency": "USD"
                            },
                            "oBaseFare": {
                                "amount": 446,
                                "currency": "USD"
                            },
                            "pubBaseFare": 446
                        }
                    },
                    "ngsFeatures": {
                        "list": null,
                        "name": "",
                        "stars": 3
                    },
                    "currencyRates": {
                        "CADUSD": {
                            "to": "USD",
                            "from": "CAD",
                            "rate": 0.78417
                        },
                        "DKKUSD": {
                            "to": "USD",
                            "from": "DKK",
                            "rate": 0.16459
                        },
                        "EURUSD": {
                            "to": "USD",
                            "from": "EUR",
                            "rate": 1.23967
                        },
                        "GBPUSD": {
                            "to": "USD",
                            "from": "GBP",
                            "rate": 1.37643
                        },
                        "KRWUSD": {
                            "to": "USD",
                            "from": "KRW",
                            "rate": 0.00091
                        },
                        "MYRUSD": {
                            "to": "USD",
                            "from": "MYR",
                            "rate": 0.25006
                        },
                        "SEKUSD": {
                            "to": "USD",
                            "from": "SEK",
                            "rate": 0.12221
                        },
                        "TWDUSD": {
                            "to": "USD",
                            "from": "TWD",
                            "rate": 0.03592
                        },
                        "USDCAD": {
                            "to": "CAD",
                            "from": "USD",
                            "rate": 1.30086
                        },
                        "USDDKK": {
                            "to": "DKK",
                            "from": "USD",
                            "rate": 6.19797
                        },
                        "USDEUR": {
                            "to": "EUR",
                            "from": "USD",
                            "rate": 0.83926
                        },
                        "USDGBP": {
                            "to": "GBP",
                            "from": "USD",
                            "rate": 0.75587
                        },
                        "USDKRW": {
                            "to": "KRW",
                            "from": "USD",
                            "rate": 1117.1008
                        },
                        "USDMYR": {
                            "to": "MYR",
                            "from": "USD",
                            "rate": 4.07943
                        },
                        "USDSEK": {
                            "to": "SEK",
                            "from": "USD",
                            "rate": 8.34736
                        },
                        "USDTWD": {
                            "to": "TWD",
                            "from": "USD",
                            "rate": 28.96525
                        },
                        "USDUSD": {
                            "to": "USD",
                            "from": "USD",
                            "rate": 1
                        }
                    },
                    "validatingCarrier": "PR"
                }
            ],
            "additionalInfo": {
                "cabin": {
                    "C": "Business",
                    "F": "First",
                    "J": "Premium Business",
                    "P": "Premium First",
                    "S": "Premium Economy",
                    "Y": "Economy"
                },
                "airline": {
                    "PR": {
                        "name": "Philippine Airlines"
                    }
                },
                "airport": {
                    "LAX": {
                        "city": "Los Angeles",
                        "name": "Los Angeles International Airport",
                        "country": "United States"
                    },
                    "MNL": {
                        "city": "Manila",
                        "name": "Ninoy Aquino International Airport",
                        "country": "Philippines"
                    },
                    "TPE": {
                        "city": "Taipei",
                        "name": "Taiwan Taoyuan International Airport",
                        "country": "Taiwan"
                    }
                },
                "general": {
                    "tripType": "rt"
                }
            }
        },
        "Passengers": {
            "Flight": [
                {
                    "id": null,
                    "user_id": null,
                    "first_name": "Arthur",
                    "middle_name": "",
                    "last_name": "Davis",
                    "birth_date": "1963-04-07",
                    "gender": "M",
                    "seats": null,
                    "assistance": null,
                    "nationality": "US",
                    "passport_id": null,
                    "passport_valid_date": null,
                    "email": null,
                    "codeAs": null
                }
            ],
            "Hotel": [
                {
                    "first_name": "mike",
                    "last_name": "kane"
                }
            ],
            "Driver": [
                {
                    "first_name": "mike",
                    "last_name": "kane",
                    "age": "30-69",
                    "birth_date": "1973-04-07"
                }
            ],
            "Attraction": [
                {
                    "first_name": "mike",
                    "last_name": "kane",
                    "language_service": "US"
                }
            ],
            "Cruise": [
                {
                    "first_name": "Arthur",
                    "last_name": "Davis",
                    "citizenship": "US",
                    "birth_date": "1963-04-07",
                    "gender": "M"
                }
            ]
        },
        "Insurance": {
            "total_amount": "20",
            "record_id": "396393",
            "passengers": [
                {
                    "nameRef": "0",
                    "amount": 20
                }
            ]
        },
        "Tip": {
            "total_amount": 20
        },
        "AuxiliarProducts": {
            "Flight": {
                "basket": {
                    "1c3df555-a2dc-4813-a055-2a8bf56fd8f1": {
                        "basket_item_id": "1c3df555-a2dc-4813-a055-2a8bf56fd8f1",
                        "benefits": [],
                        "display_name": "10kg Bag",
                        "price": {
                            "base": {
                                "amount": 2000,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 1820,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            },
                            "fees": [],
                            "markups": [
                                {
                                    "amount": 600,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 546,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "markup_type": "markup"
                                }
                            ],
                            "taxes": [
                                {
                                    "amount": 200,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 182,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "tax_type": "tax"
                                }
                            ],
                            "total": {
                                "amount": 2400,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 2184,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            }
                        },
                        "product_details": {
                            "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
                            "passenger_id": "p1",
                            "size": 150,
                            "size_unit": "cm",
                            "weight": 10,
                            "weight_unit": "kg"
                        },
                        "product_id": "741bcc97-c2fe-4820-b14d-f11f32e6fadb",
                        "product_type": "bag",
                        "quantity": 1,
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
                        "validity": {
                            "state": "valid",
                            "valid_from": "2020-05-22T16:34:08Z",
                            "valid_to": "2020-05-22T16:49:08Z"
                        }
                    },
                    "2654f3f9-8990-4d2e-bdea-3b341ad5d1de": {
                        "basket_item_id": "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
                        "benefits": [],
                        "display_name": "Seat 15C",
                        "price": {
                            "base": {
                                "amount": 2000,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 1820,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            },
                            "fees": [],
                            "markups": [
                                {
                                    "amount": 400,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": {
                                        "amount": 364,
                                        "currency": "USD",
                                        "decimal_places": 2
                                    },
                                    "markup_type": "markup"
                                }
                            ],
                            "taxes": [
                                {
                                    "amount": 200,
                                    "currency": "USD",
                                    "decimal_places": 2,
                                    "in_original_currency": [],
                                    "tax_type": "tax"
                                }
                            ],
                            "total": {
                                "amount": 2600,
                                "currency": "USD",
                                "decimal_places": 2,
                                "in_original_currency": {
                                    "amount": 2366,
                                    "currency": "USD",
                                    "decimal_places": 2
                                }
                            }
                        },
                        "product_details": {
                            "column": "C",
                            "passenger_id": "p1",
                            "row": 15,
                            "segment_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
                        },
                        "product_id": "a17e10ca-0c9a-4691-9922-d664a3b52382",
                        "product_type": "seat",
                        "quantity": 1,
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252",
                        "validity": {
                            "state": "valid",
                            "valid_from": "2020-05-22T16:34:08Z",
                            "valid_to": "2020-05-22T16:49:08Z"
                        }
                    },
                    "5d5e1bce-4577-4118-abcb-155823d8b4a3": [],
                    "6acd57ba-ccb7-4e86-85e7-b3e586caeae2": [],
                    "dffac4ba-73b9-4b1b-9334-001817fff0cf": [],
                    "e960eff9-7628-4645-99d8-20a6e22f6419": []
                },
                "country": "US",
                "currency": "USD",
                "journeys": [
                    {
                        "journey_id": "aab8980e-b263-4624-ad40-d6e5e364b4e9",
                        "segments": [
                            {
                                "arrival_airport": "LHR",
                                "arrival_time": "2020-07-07T22:30:00Z",
                                "departure_airport": "EDI",
                                "departure_time": "2020-07-07T21:10:00Z",
                                "fare_basis": "OTZ0RO/Y",
                                "fare_class": "O",
                                "fare_family": "Basic Economy",
                                "marketing_airline": "BA",
                                "marketing_flight_number": "1465",
                                "number_of_stops": 0,
                                "operating_airline": "BA",
                                "operating_flight_number": "1465",
                                "segment_id": "938d8e82-dd7c-4d85-8ab4-38fea8753f6f"
                            }
                        ]
                    },
                    {
                        "journey_id": "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba",
                        "segments": [
                            {
                                "arrival_airport": "EDI",
                                "arrival_time": "2020-07-14T08:35:00Z",
                                "departure_airport": "LGW",
                                "departure_time": "2020-07-14T07:05:00Z",
                                "fare_basis": "NALZ0KO/Y",
                                "fare_class": "N",
                                "fare_family": "Basic Economy",
                                "marketing_airline": "BA",
                                "marketing_flight_number": "2500",
                                "number_of_stops": 0,
                                "operating_airline": "BA",
                                "operating_flight_number": "2500",
                                "segment_id": "7d693cb0-d6d8-49f0-9489-866b3d789215"
                            }
                        ]
                    }
                ],
                "language": "en-US",
                "orders": [],
                "passengers": [
                    {
                        "first_names": "Vincent Willem",
                        "passenger_id": "ee850c82-e150-4f35-b0c7-228064c2964b",
                        "surname": "Van Gogh"
                    }
                ],
                "tickets": [
                    {
                        "basket_item_ids": [
                            "dffac4ba-73b9-4b1b-9334-001817fff0cf",
                            "e960eff9-7628-4645-99d8-20a6e22f6419",
                            "6acd57ba-ccb7-4e86-85e7-b3e586caeae2",
                            "5d5e1bce-4577-4118-abcb-155823d8b4a3"
                        ],
                        "journey_ids": [
                            "aab8980e-b263-4624-ad40-d6e5e364b4e9"
                        ],
                        "state": "in_basket",
                        "ticket_basket_item_id": "dffac4ba-73b9-4b1b-9334-001817fff0cf",
                        "ticket_id": "8c1c9fc8-d968-4733-93a8-6067bac2543f"
                    },
                    {
                        "basket_item_ids": [
                            "2654f3f9-8990-4d2e-bdea-3b341ad5d1de",
                            "1c3df555-a2dc-4813-a055-2a8bf56fd8f1"
                        ],
                        "journey_ids": [
                            "1770bf8f-0c1c-4ba5-99f5-56e446fe79ba"
                        ],
                        "offered_price": {
                            "currency": "USD",
                            "decimal_places": 2,
                            "total": 20000
                        },
                        "state": "offered",
                        "ticket_id": "e8558737-2ec0-436f-89ec-00e7a20b3252"
                    }
                ],
                "trip_access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
                "trip_id": "23259b86-3208-44c9-85cc-4b116a822bff",
                "trip_state_hash": "69abcc117863186292bdf5f1c0d94db1e5227210935e6abe039cfb017cbefbee"
            },
            "Hotel": [],
            "Driver": [],
            "Attraction": [],
            "Cruise": []
        },
        "Payment": {
            "type": "CARD",
            "transaction_id": "1234567890",
            "card_id": 234567,
            "auth_id": 123456
        }
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
"status": 200,
"message": "OK",
"data": {
"order_gid": "ef75bfa7cc60af154c22c43e3732350f"
},
"technical": {
"action": "v2/order/create",
"response_id": 327,
"request_dt": "2021-02-27 08:49:46",
"response_dt": "2021-02-27 08:49:46",
"execution_time": 0.094,
"memory_usage": 1356920
}
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
"status": 422,
"message": "Validation error",
"errors": {
"payment.type": [
"Type is invalid."
]
},
"code": 0,
"technical": {
"action": "v2/order/create",
"response_id": 328,
"request_dt": "2021-02-27 08:52:06",
"response_dt": "2021-02-27 08:52:06",
"execution_time": 0.021,
"memory_usage": 437656
}
}`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"post",url:"/v2/order/create-c2b",title:"Create Order c2b flow",version:"1.0.0",name:"CreateOrderClickToBook",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
     "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     "Accept-Encoding": "Accept-Encoding: gzip, deflate"
 }`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"max 10",optional:!1,field:"sourceCid",description:"<p>Source cid</p>"},{group:"Parameter",type:"string",size:"max 7",optional:!1,field:"bookingId",description:"<p>Booking id</p>"},{group:"Parameter",type:"string",size:"max 255",optional:!1,field:"fareId",description:"<p>Unique value of order</p>"},{group:"Parameter",type:"string",allowedValues:['"success"','"failed"'],optional:!1,field:"status",description:"<p>Status</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!1,field:"languageId",description:"<p>Language Id</p>"},{group:"Parameter",type:"string",size:"max 2",optional:!1,field:"marketCountry",description:"<p>Market Country</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"quotes",description:"<p>Product quotes</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.productKey",description:"<p>Product key</p>"},{group:"Parameter",type:"string",allowedValues:['"booked"','"failed"'],optional:!1,field:"quotes.status",description:"<p>Status</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.originSearchData",description:"<p>Product quote origin search data</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.quoteOtaId",description:"<p>Product quote custom id</p>"},{group:"Parameter",type:"Object",optional:!1,field:"quotes.holder",description:"<p>Holder Info</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"quotes.holder.firstName",description:"<p>Holder first name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"quotes.holder.lastName",description:"<p>Holder last name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"quotes.holder.middleName",description:"<p>Holder middle name</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"quotes.holder.email",description:"<p>Holder email</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"quotes.holder.phone",description:"<p>Holder phone</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"quotes.options",description:"<p>Quote Options</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"quotes.options.productOptionKey",description:"<p>Product option key</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"quotes.options.name",description:"<p>Name</p>"},{group:"Parameter",type:"string",optional:!0,field:"quotes.options.description",description:"<p>Description</p>"},{group:"Parameter",type:"Decimal",optional:!1,field:"quotes.options.price",description:"<p>Price</p>"},{group:"Parameter",type:"Object",optional:!0,field:"quotes.flightPaxData",description:"<p>[]      Flight pax data</p>"},{group:"Parameter",type:"string",allowedValues:['"ADT"','"CHD"','"INF"'],optional:!1,field:"quotes.flightPaxData.type",description:"<p>Pax type</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"quotes.flightPaxData.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"quotes.flightPaxData.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"quotes.flightPaxData.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"quotes.flightPaxData.nationality",description:"<p>Nationality</p>"},{group:"Parameter",type:"string",size:"max 1",optional:!0,field:"quotes.flightPaxData.gender",description:"<p>Gender</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"quotes.flightPaxData.birth_date",description:"<p>Birth Date</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!0,field:"quotes.flightPaxData.email",description:"<p>Email</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"quotes.flightPaxData.language",description:"<p>Language</p>"},{group:"Parameter",type:"string",size:"max 5",optional:!0,field:"quotes.flightPaxData.citizenship",description:"<p>Citizenship</p>"},{group:"Parameter",type:"Object",optional:!0,field:"quotes.hotelPaxData",description:"<p>[]      Flight pax data</p>"},{group:"Parameter",type:"string",allowedValues:['"ADT"','"CHD"'],optional:!1,field:"quotes.hotelPaxData.type",description:"<p>Pax type</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"quotes.hotelPaxData.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"quotes.hotelPaxData.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"format yyyy-mm-dd",optional:!0,field:"quotes.hotelPaxData.birth_date",description:"<p>Birth Date</p>"},{group:"Parameter",type:"integer",optional:!0,field:"quotes.hotelPaxData.age",description:"<p>Age</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.hotelPaxData.hotelRoomKey",description:"<p>Hotel Room Key</p>"},{group:"Parameter",type:"Object",optional:!1,field:"quotes.hotelRequest",description:"<p>Hotel Request data <code>required for hotel quotes</code></p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.hotelRequest.destinationName",description:"<p>Destination Name</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.hotelRequest.destinationCode",description:"<p>Destination Code</p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.hotelRequest.checkIn",description:"<p>Check In Date <code>format: yyyy-mm-dd</code></p>"},{group:"Parameter",type:"string",optional:!1,field:"quotes.hotelRequest.checkOut",description:"<p>Check Out Date <code>format: yyyy-mm-dd</code></p>"},{group:"Parameter",type:"Object",optional:!0,field:"billingInfo",description:"<p>BillingInfo</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!0,field:"billingInfo.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!0,field:"billingInfo.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!0,field:"billingInfo.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"billingInfo.address",description:"<p>Address</p>"},{group:"Parameter",type:"string",size:"max 2",optional:!0,field:"billingInfo.country_id",description:"<p>Country Id</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!0,field:"billingInfo.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!0,field:"billingInfo.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"max 10",optional:!0,field:"billingInfo.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!0,field:"billingInfo.phone",description:"<p>Phone <code>Deprecated</code></p>"},{group:"Parameter",type:"string",size:"max 160",optional:!0,field:"billingInfo.email",description:"<p>Email <code>Deprecated</code></p>"},{group:"Parameter",type:"Object",optional:!0,field:"creditCard",description:"<p>Credit Card</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"creditCard.holder_name",description:"<p>Holder Name</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"creditCard.number",description:"<p>Credit Card Number</p>"},{group:"Parameter",type:"string",optional:!0,field:"creditCard.type",description:"<p>Credit Card type</p>"},{group:"Parameter",type:"string",size:"max 18",optional:!1,field:"creditCard.expiration",description:"<p>Credit Card expiration</p>"},{group:"Parameter",type:"string",size:"max 4",optional:!1,field:"creditCard.cvv",description:"<p>Credit Card cvv</p>"},{group:"Parameter",type:"Object[]",optional:!1,field:"contactsInfo",description:"<p>BillingInfo</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"contactsInfo.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"contactsInfo.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"contactsInfo.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!0,field:"contactsInfo.phone",description:"<p>Phone number</p>"},{group:"Parameter",type:"string",size:"max 100",optional:!1,field:"contactsInfo.email",description:"<p>Email</p>"},{group:"Parameter",type:"Object",optional:!0,field:"payment",description:"<p>Payment info</p>"},{group:"Parameter",type:"string",size:"max 3",optional:!0,field:"payment.clientCurrency",description:"<p>Client currency</p>"}]},examples:[{title:"Request-Example:",content:`
{
            "sourceCid": "ACHUY23AS",
            "bookingId": "WCJ12C",
            "fareId": "A0EA9F-5cc2ce331e8bb3.16383647",
            "status": "success",
            "languageId": "en-US",
            "marketCountry": "US",
            "quotes": [
                {
                    "status": "booked",
                    "productKey": "flight",
                    "originSearchData": "{\\"key\\":\\"2_QldLMTAxKlkxMDAwL0pGS1BBUjIwMjEtMDgtMDcqREx+I0RMOTE4MH5sYzplbl91cw==\\",\\"routingId\\":1,\\"prices\\":{\\"lastTicketDate\\":\\"2021-04-05\\",\\"totalPrice\\":354.2,\\"totalTax\\":229.2,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":1,\\"baseFare\\":125,\\"pubBaseFare\\":125,\\"baseTax\\":229.2,\\"markup\\":0,\\"comm\\":0,\\"price\\":354.2,\\"tax\\":229.2,\\"oBaseFare\\":{\\"amount\\":125,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":229.2,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":true,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"ex\\",\\"applicability\\":\\"before\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"ex\\",\\"applicability\\":\\"after\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-08-07 16:30\\",\\"arrivalTime\\":\\"2021-08-08 05:55\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"9180\\",\\"bookingClass\\":\\"E\\",\\"duration\\":445,\\"departureAirportCode\\":\\"JFK\\",\\"departureAirportTerminal\\":\\"1\\",\\"arrivalAirportCode\\":\\"CDG\\",\\"arrivalAirportTerminal\\":\\"2E\\",\\"operatingAirline\\":\\"AF\\",\\"airEquipType\\":\\"77W\\",\\"marketingAirline\\":\\"DL\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":3629,\\"cabin\\":\\"Y\\",\\"cabinIsBasic\\":true,\\"brandId\\":\\"686562\\",\\"brandName\\":\\"BASIC ECONOMY\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"VH7L09B1\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":0}},\\"recheckBaggage\\":false}],\\"duration\\":445}],\\"maxSeats\\":9,\\"paxCnt\\":1,\\"validatingCarrier\\":\\"DL\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"9cbb17ae-40dd-4d94-83be-2f0eed47e9ad\\",\\"availabilitySources\\":\\"S\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":9}},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"QldLMTAxWTEwMDB8SkZLUEFSMjAyMS0wOC0wNw==\\",\\"lang\\":\\"en\\",\\"rank\\":10,\\"cheapest\\":true,\\"fastest\\":false,\\"best\\":true,\\"bags\\":0,\\"country\\":\\"us\\",\\"prod_types\\":[\\"PUB\\"]}}",
                    "options": [
                        {
                            "productOptionKey": "travelGuard",
                            "name": "Travel Guard",
                            "description": "",
                            "price": 20
                        }
                    ],
                    "flightPaxData": [
                        {
                            "first_name": "Test name",
                            "last_name": "Test last name",
                            "middle_name": "Test middle name",
                            "nationality": "US",
                            "gender": "M",
                            "birth_date": "1963-04-07",
                            "email": "mike.kane@techork.com",
                            "language": "en-US",
                            "citizenship": "US",
                            "type": "ADT"
                        }
                    ],
                    "quoteOtaId": "asdff43fsgfdsv343ddx",
                    "holder": {
                        "firstName": "Test",
                        "lastName": "Test",
                        "middleName": "Test",
                        "email": "test@test.test",
                        "phone": "+19074861000"
                    }
                },
                {
                    "status": "booked",
                    "productKey": "hotel",
                    "originSearchData": "{\\"categoryName\\":\\"3 STARS\\",\\"destinationName\\":\\"Chisinau\\",\\"zoneName\\":\\"Chisinau\\",\\"minRate\\":135.92,\\"maxRate\\":285.94,\\"currency\\":\\"USD\\",\\"code\\":148030,\\"name\\":\\"Cosmos Hotel\\",\\"description\\":\\"The hotel is situated in the heart of Chisinau, the capital of Moldova. It is perfectly located for access to the business centre, cultural institutions and much more. Chisinau Airport is only 15 minutes away and the railway station is less than 5 minutes away from the hotel.\\\\n\\\\nThe city hotel offers a choice of 150 rooms, 24-hour reception and check-out services in the lobby, luggage storage, a hotel safe, currency exchange facility and a cloakroom. There is lift access to the upper floors as well as an on-site restaurant and conference facilities. Internet access, a laundry service (fees apply) and free parking in the car park are also on offer to guests during their stay.\\\\n\\\\nAll the rooms are furnished with double or king-size beds and provide an en suite bathroom with a shower. Air conditioning, central heating, satellite TV, a telephone, mini fridge, radio and free wireless Internet access are also on offer.\\\\n\\\\nThere is a golf course about 12 km from the hotel.\\\\n\\\\nThe hotel restaurant offers a wide selection of local and European cuisine. Breakfast is served as a buffet and lunch and dinner can be chosen la carte.\\",\\"countryCode\\":\\"MD\\",\\"stateCode\\":\\"MD\\",\\"destinationCode\\":\\"KIV\\",\\"zoneCode\\":1,\\"latitude\\":47.014293,\\"longitude\\":28.853371,\\"categoryCode\\":\\"3EST\\",\\"categoryGroupCode\\":\\"GRUPO3\\",\\"accomodationType\\":{\\"code\\":\\"HOTEL\\"},\\"boardCodes\\":[\\"BB\\",\\"AI\\",\\"HB\\",\\"FB\\",\\"RO\\"],\\"segmentCodes\\":[],\\"address\\":\\"NEGRUZZI, 2\\",\\"postalCode\\":\\"MD2001\\",\\"city\\":\\"CHISINAU\\",\\"email\\":\\"info@hotel-cosmos.com\\",\\"phones\\":[{\\"type\\":\\"PHONEBOOKING\\",\\"number\\":\\"+37322890054\\"},{\\"type\\":\\"PHONEHOTEL\\",\\"number\\":\\"+37322837505\\"},{\\"type\\":\\"FAXNUMBER\\",\\"number\\":\\"+37322542744\\"}],\\"images\\":[{\\"url\\":\\"14/148030/148030a_hb_a_001.jpg\\",\\"type\\":\\"GEN\\"}],\\"web\\":\\"http://hotel-cosmos.com/\\",\\"lastUpdate\\":\\"2020-11-23\\",\\"s2C\\":\\"1*\\",\\"ranking\\":14,\\"serviceType\\":\\"HOTELBEDS\\",\\"groupKey\\":\\"2118121725\\",\\"totalAmount\\":341.32,\\"totalMarkup\\":26.69,\\"totalPublicAmount\\":347.99,\\"totalSavings\\":6.67,\\"totalEarnings\\":3.34,\\"rates\\":[{\\"code\\":\\"ROO.ST\\",\\"name\\":\\"Room Standard\\",\\"key\\":\\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|BB|B2B|1~1~0||N@06~~24ebc~-829367492~N~~~NOR~C98A4E21F1184B3161702850635900AWUS0000029001400030824ebc\\",\\"class\\":\\"NOR\\",\\"allotment\\":3,\\"type\\":\\"RECHECK\\",\\"paymentType\\":\\"AT_WEB\\",\\"boardCode\\":\\"BB\\",\\"boardName\\":\\"BED AND BREAKFAST\\",\\"rooms\\":1,\\"adults\\":1,\\"markup\\":16.62,\\"amount\\":205.4,\\"publicAmmount\\":209.55,\\"savings\\":4.15,\\"earnings\\":2.08},{\\"code\\":\\"ROO.ST\\",\\"name\\":\\"Room Standard\\",\\"key\\":\\"20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d\\",\\"class\\":\\"NOR\\",\\"allotment\\":3,\\"type\\":\\"RECHECK\\",\\"paymentType\\":\\"AT_WEB\\",\\"boardCode\\":\\"RO\\",\\"boardName\\":\\"ROOM ONLY\\",\\"rooms\\":1,\\"adults\\":2,\\"markup\\":10.07,\\"amount\\":135.92,\\"publicAmmount\\":138.44,\\"savings\\":2.52,\\"earnings\\":1.26}]}",

                    "quoteOtaId": "asdfw43wfdswef3x",
                    "holder": {
                        "firstName": "Test 2",
                        "lastName": "Test 2",
                        "email": "test+2@test.test",
                        "phone": "+19074861000"
                    },
                    "hotelPaxData": [
                        {
                            "hotelRoomKey": "20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d",
                            "first_name": "Test",
                            "last_name": "Test",
                            "birth_date": "1963-04-07",
                            "age": "45",
                            "type": "ADT"
                        },
                        {
                            "hotelRoomKey": "20210608|20210616|W|504|148030|ROO.ST|ID_B2B_76|RO|B2B|1~2~0||N@06~~2557d~-972866252~N~~~NOR~C98A4E21F1184B3161702850635900AWUS000002900140003082557d",
                            "first_name": "Mary",
                            "last_name": "Smith",
                            "birth_date": "1963-04-07",
                            "age": "32",
                            "type": "ADT"
                        }
                    ],
                    "hotelRequest": {
                        "destinationCode": "BGO",
                        "destinationName": "Norway, Bergen",
                        "checkIn": "2021-09-10",
                        "checkOut": "2021-09-30"
                    }
                }
            ],
            "creditCard": {
                "holder_name": "Barbara Elmore",
                "number": "1111111111111111",
                "type": "Visas",
                "expiration": "07 / 23",
                "cvv": "324"
            },
            "billingInfo": {
                "first_name": "Barbara Elmore",
                "middle_name": "",
                "last_name": "T",
                "address": "1013 Weda Cir",
                "country_id": "US",
                "city": "Mayfield",
                "state": "KY",
                "zip": "99999",
                "phone": "+19074861000", -- deprecated, will be removed soon
                "email": "barabara@test.com" -- deprecated, will be removed soon
            },
            "contactsInfo": [
                {
                    "first_name": "Barbara",
                    "last_name": "Elmore",
                    "middle_name": "",
                    "phone": "+19074861000",
                    "email": "barabara@test.com"
                },
                {
                    "first_name": "John",
                    "last_name": "Doe",
                    "middle_name": "",
                    "phone": "+19074865678",
                    "email": "john@test.com"
                }
            ],
            "payment": {
                "clientCurrency": "USD"
            }
        }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
            "status": 200,
            "message": "OK",
            "data": {
                "order_gid": "1588da7b87cd3b91cc1df4aed0d7aeba"
            }
        }`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "Validation error",
            "errors": {
                "quotes.0.productKey": [
                    "Product type not found by key: flights"
                ]
            },
            "code": 0
        }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "test",
            "detailError": {
                "product": "Flight",
                "quoteOtaId": "asdff43fsgfdsv343ddx"
            },
            "code": 15901,
            "errors": []
        }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "Validation error",
            "errors": {
                "fareId": [
                    "Fare Id \\"A0EA9F-5cc2ce331e8bb3.16383647\\" has already been taken."
                ]
            },
            "code": 0
        }

@return ErrorResponse|SuccessResponse`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"post",url:"/v2/order/create-proxy",title:"Create Order Proxy",version:"0.2.0",name:"CreateOrderProxy",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": "Success",
       "success": {
          "recordLocator": "ORZ7I4",
          "caseNumber": "OVAGO-282667-TSMITH-AMADEUS-010220-I1B1L1",
          "totalPrice": "573.75"
       },
       "failure": [],
       "priceInfo": [],
       "errors": [],
       "source": {
          "type": 1,
          "status": 200
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`
HTTP/1.1 200 OK
{
       "status": "Failed",
       "success": [],
       "failure": {
             "message": "Price Increase"
       },
       "priceInfo": {
          "totalPrice": 1389.87,
          "totalTax": 684.58,
          "fareType": "PUB",
          "bookingClass": "WWWW",
          "currency": "USD",
          "detail": {
              "ADT": {
                  "quantity": 2,
              "totalFare": 448.29,
              "baseTax": 342.29,
              "baseFare": 106,
            }
          }
       },
       "errors": [],
       "source": {
          "type": 1,
          "status": 200
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (500):",content:`
HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (404):",content:`
HTTP/1.1 404 Not Found
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 404
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"get",url:"/v2/order/get-file",title:"Get File",version:"0.2.0",name:"GetFile",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!1,field:"uid",description:"<p>File UID</p>"}]}},error:{examples:[{title:"Error-Response (404):",content:`
HTTP/1.1 404 Not Found
{
  "name": "Not Found",
  "message": "File is not found.",
  "code": 0,
  "status": 404,
  "type": "yii\\\\web\\\\NotFoundHttpException"
}`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"post",url:"/v2/order/view",title:"View Order",version:"0.1.0",name:"ViewOrder",group:"Order",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!1,field:"gid",description:"<p>Order gid</p>"}]},examples:[{title:"Request-Example:",content:`
{
    "gid": "04d3fe3fc74d0514ee93e208a52bcf90",
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
    "order": {
        "or_id": 110,
        "or_gid": "a0758d1d8ded3efe62c465ad36987200",
        "or_uid": "or6047198783406",
        "or_name": "Order 1",
        "or_description": null,
        "or_status_id": 3,
        "or_pay_status_id": 1,
        "or_app_total": "229.00",
        "or_app_markup": null,
        "or_agent_markup": null,
        "or_client_total": "229.00",
        "or_client_currency": "USD",
        "or_client_currency_rate": "1.00000",
        "or_status_name": "Processing",
        "or_pay_status_name": "Not paid",
        "or_client_currency_symbol": "USD",
        "or_files": [],
        "or_request_uid": "OE96040",
        "billing_info": [
            {
                "bi_first_name": "Barbara Elmore",
                "bi_last_name": "T",
                "bi_middle_name": "",
                "bi_company_name": null,
                "bi_address_line1": "1013 Weda Cir",
                "bi_address_line2": null,
                "bi_city": "Mayfield",
                "bi_state": "KY",
                "bi_country": "US",
                "bi_zip": "99999",
                "bi_contact_phone": "+19074861000", -- deprecated, will be removed soon
                "bi_contact_email": "mike.kane@techork.com", -- deprecated, will be removed soon
                "bi_contact_name": null, -- deprecated, will be removed soon
                "bi_payment_method_id": 1,
                "bi_country_name": "United States of America",
                "bi_payment_method_name": "Credit / Debit Card"
            }
        ],
        "quotes": [
            {
                "pq_gid": "80e1ebef3057d60ff3870fe0a1eb83ee",
                "pq_name": "",
                "pq_order_id": 110,
                "pq_description": null,
                "pq_status_id": 3,
                "pq_price": 209,
                "pq_origin_price": 209,
                "pq_client_price": 209,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Applied",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 49,
                    "fq_source_id": null,
                    "fq_product_quote_id": 162,
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "LO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDktMTcqTE9+I0xPNTE0I0xPMjgxfmxjOmVuX3Vz\\",\\"routingId\\":1,\\"prices\\":{\\"lastTicketDate\\":\\"2021-03-11\\",\\"totalPrice\\":209,\\"totalTax\\":123,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":2,\\"baseFare\\":43,\\"pubBaseFare\\":43,\\"baseTax\\":61.5,\\"markup\\":0,\\"comm\\":0,\\"price\\":104.5,\\"tax\\":61.5,\\"oBaseFare\\":{\\"amount\\":43,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":61.5,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":true,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"ex\\",\\"applicability\\":\\"before\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"ex\\",\\"applicability\\":\\"after\\",\\"permitted\\":true,\\"amount\\":0},{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-09-17 14:30\\",\\"arrivalTime\\":\\"2021-09-17 15:20\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"514\\",\\"bookingClass\\":\\"V\\",\\"duration\\":110,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"WAW\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"LO\\",\\"airEquipType\\":\\"DH4\\",\\"marketingAirline\\":\\"LO\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":508,\\"cabin\\":\\"Y\\",\\"cabinIsBasic\\":true,\\"brandId\\":\\"685421\\",\\"brandName\\":\\"ECONOMY SAVER\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"V1SAV28\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":0}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2021-09-18 07:30\\",\\"arrivalTime\\":\\"2021-09-18 09:25\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"281\\",\\"bookingClass\\":\\"V\\",\\"duration\\":175,\\"departureAirportCode\\":\\"WAW\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"LO\\",\\"airEquipType\\":\\"738\\",\\"marketingAirline\\":\\"LO\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":893,\\"cabin\\":\\"Y\\",\\"cabinIsBasic\\":true,\\"brandId\\":\\"685421\\",\\"brandName\\":\\"ECONOMY SAVER\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"V1SAV28\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":0}},\\"recheckBaggage\\":false}],\\"duration\\":1255}],\\"maxSeats\\":5,\\"paxCnt\\":2,\\"validatingCarrier\\":\\"LO\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"b3355dee-c859-4617-bca4-50046effc830\\",\\"availabilitySources\\":\\"S,S\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":5}},\\"ngsFeatures\\":{\\"stars\\":1,\\"name\\":\\"ECONOMY SAVER\\",\\"list\\":[]},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wOS0xNw==\\",\\"lang\\":\\"en\\",\\"rank\\":6,\\"cheapest\\":true,\\"fastest\\":false,\\"best\\":false,\\"bags\\":0,\\"country\\":\\"us\\"},\\"price\\":104.5,\\"originRate\\":1,\\"stops\\":[1],\\"time\\":[{\\"departure\\":\\"2021-09-17 14:30\\",\\"arrival\\":\\"2021-09-18 09:25\\"}],\\"bagFilter\\":\\"\\",\\"airportChange\\":false,\\"technicalStopCnt\\":0,\\"duration\\":[1255],\\"totalDuration\\":1255,\\"topCriteria\\":\\"cheapest\\",\\"rank\\":6}",
                    "fq_last_ticket_date": "2021-03-11",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "flight": {
                        "fl_product_id": 78,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "fqt_id": 103,
                            "fqt_uid": "fqt6047195e6a882",
                            "fqt_key": null,
                            "fqt_duration": 1255,
                            "segments": [
                                {
                                    "fqs_uid": "fqs6047195e6be4b",
                                    "fqs_departure_dt": "2021-09-17 14:30:00",
                                    "fqs_arrival_dt": "2021-09-17 15:20:00",
                                    "fqs_stop": 0,
                                    "fqs_flight_number": 514,
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
                                            "qsb_flight_quote_segment_id": 261,
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
                                    "fqs_uid": "fqs6047195e6d5a0",
                                    "fqs_departure_dt": "2021-09-18 07:30:00",
                                    "fqs_arrival_dt": "2021-09-18 09:25:00",
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
                                            "qsb_flight_quote_segment_id": 262,
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
                            "qpp_tax": "61.50",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "43.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "61.50",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "43.00",
                            "qpp_client_tax": "61.50",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6047195e6767d",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": "Alex",
                            "fp_last_name": "Grub",
                            "fp_middle_name": "",
                            "fp_dob": "1963-04-07"
                        },
                        {
                            "fp_uid": "fp6047195e67b7a",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": "Test name",
                            "fp_last_name": "Test last name",
                            "fp_middle_name": "Test middle name",
                            "fp_dob": "1963-04-07"
                        },
                        {
                            "fp_uid": "fp6047302b6966f",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047302b69a86",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp60473031c44c4",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp60473031c47b9",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                },
                "product": {
                    "pr_gid": null,
                    "pr_type_id": 1,
                    "pr_name": "",
                    "pr_lead_id": 513110,
                    "pr_description": "",
                    "pr_status_id": null,
                    "pr_service_fee_percent": null,
                    "holder": {
                        "ph_first_name": "test",
                        "ph_last_name": "test",
                        "ph_email": "test@test.test",
                        "ph_phone_number": "+19074861000"
                    }
                },
                "productQuoteOptions": [
                    {
                        "pqo_name": "Travel Guard",
                        "pqo_description": "",
                        "pqo_status_id": null,
                        "pqo_price": 20,
                        "pqo_client_price": 20,
                        "pqo_extra_markup": null,
                        "pqo_request_data": null,
                        "productOption": {
                            "po_key": "travelGuard",
                            "po_name": "Travel Guard",
                            "po_description": ""
                        }
                    }
                ]
            }
        ]
    },
    "technical": {
        "action": "v2/order/view",
        "response_id": 507,
        "request_dt": "2021-03-09 12:10:22",
        "response_dt": "2021-03-09 12:10:23",
        "execution_time": 0.122,
        "memory_usage": 1563368
    },
    "request": {
        "gid": "a0758d1d8ded3efe62c465ad36987200"
    }
}`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
"status": 422,
"message": "Error",
"errors": [
"Order is not found"
],
"code": 12100,
"technical": {
"action": "v2/order/view",
"response_id": 397,
"request_dt": "2021-03-01 17:40:41",
"response_dt": "2021-03-01 17:40:41",
"execution_time": 0.017,
"memory_usage": 212976
},
"request": {
"gid": "5287f7f7ff5a28789518db64e946ea67s"
}
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
    "status": 400,
    "message": "Load data error",
    "errors": [
        "Not found Order data on POST request"
    ],
    "code": "18300",
    "technical": {
        "action": "v2/order/view",
        "response_id": 11933856,
        "request_dt": "2020-02-03 12:49:20",
        "response_dt": "2020-02-03 12:49:20",
        "execution_time": 0.017,
        "memory_usage": 114232
    },
    "request": []
}`,type:"json"}]},filename:"v2/controllers/OrderController.php",groupTitle:"Order"},{type:"post",url:"/v2/payment/update-bo",title:"Create/Update payments from BO",version:"0.1.0",name:"Update_payment",group:"Payment",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"255",optional:!1,field:"fareId",description:"<p>Fare Id (Order identity)</p>"},{group:"Parameter",type:"object",optional:!1,field:"payments",description:"<p>Payments data array</p>"},{group:"Parameter",type:"float",optional:!1,field:"payments.pay_amount",description:"<p>Payment amount</p>"},{group:"Parameter",type:"string",size:"3",optional:!1,field:"payments.pay_currency",description:"<p>Payment currency code (for example USD)</p>"},{group:"Parameter",type:"date",optional:!1,field:"payments.pay_date",description:"<p>Payment date (format Y-m-d)</p>"},{group:"Parameter",type:"string",size:"1..10",allowedValues:["Capture","Refund","Authorize"],optional:!1,field:"payments.pay_type",description:"<p>Payment Type (&quot;Capture&quot;,&quot;Refund&quot;,&quot;Authorize&quot;)</p>"},{group:"Parameter",type:"string",optional:!1,field:"payments.pay_code",description:"<p>Payment Identity</p>"},{group:"Parameter",type:"int",optional:!1,field:"payments.pay_auth_id",description:"<p>Payment transaction ID</p>"},{group:"Parameter",type:"string",size:"100",optional:!0,field:"payments.pay_method_key",description:"<p>Payment method key (by default &quot;card&quot;)</p>"},{group:"Parameter",type:"string",size:"255",optional:!0,field:"payments.pay_description",description:"<p>Payment description</p>"},{group:"Parameter",type:"object",optional:!1,field:"billingInfo",description:"<p>Billing Info</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.first_name",description:"<p>First Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.last_name",description:"<p>Last Name</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.middle_name",description:"<p>Middle Name</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!1,field:"billingInfo.address",description:"<p>Address</p>"},{group:"Parameter",type:"string",size:"max 2",optional:!1,field:"billingInfo.country_id",description:"<p>Country Id</p>"},{group:"Parameter",type:"string",size:"max 30",optional:!1,field:"billingInfo.city",description:"<p>City</p>"},{group:"Parameter",type:"string",size:"max 40",optional:!1,field:"billingInfo.state",description:"<p>State</p>"},{group:"Parameter",type:"string",size:"max 10",optional:!1,field:"billingInfo.zip",description:"<p>Zip</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"billingInfo.phone",description:"<p>Phone</p>"},{group:"Parameter",type:"string",size:"max 160",optional:!1,field:"billingInfo.email",description:"<p>Email</p>"},{group:"Parameter",type:"object",optional:!1,field:"creditCard",description:"<p>Credit Card</p>"},{group:"Parameter",type:"string",size:"max 50",optional:!0,field:"creditCard.holder_name",description:"<p>Holder Name</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!1,field:"creditCard.number",description:"<p>Credit Card Number</p>"},{group:"Parameter",type:"string",size:"max 20",optional:!0,field:"creditCard.type",description:"<p>Credit Card type (Visa,Master Card,American Express,Discover,Diners Club,JCB)</p>"},{group:"Parameter",type:"string",size:"max 18",optional:!1,field:"creditCard.expiration",description:"<p>Credit Card expiration</p>"},{group:"Parameter",type:"string",size:"max 4",optional:!1,field:"creditCard.cvv",description:"<p>Credit Card cvv</p>"}]},examples:[{title:"Request-Example:",content:` {
           "fareId": "or6061be5ec5c0e",
           "payments":[
               {
                   "pay_amount": 200.21,
                   "pay_currency": "USD",
                   "pay_auth_id": 728282,
                   "pay_type": "Capture",
                   "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                   "pay_date": "2021-03-25",
                   "pay_method_key":"card",
                   "pay_description": "example description",
                   "creditCard": {
                       "holder_name": "Tester holder",
                       "number": "111**********111",
                       "type": "Visa",
                       "expiration": "07 / 23",
                       "cvv": "123"
                   },
                   "billingInfo": {
                       "first_name": "Hobbit",
                       "middle_name": "Hard",
                       "last_name": "Lover",
                       "address": "1013 Weda Cir",
                       "country_id": "US",
                       "city": "Gotham City",
                       "state": "KY",
                       "zip": "99999",
                       "phone": "+19074861000",
                       "email": "barabara@test.com"
                   }
               },
               {
                   "pay_amount":200.21,
                   "pay_currency":"USD",
                   "pay_auth_id": 728283,
                   "pay_type": "Refund",
                   "pay_code":"xx_XXXXXXXXXXXXXXXXXXXX",
                   "pay_date":"2021-03-25",
                   "pay_method_key":"card",
                   "pay_description": "client is fraud",
                   "creditCard": {
                       "holder_name": "Tester holder",
                       "number": "111**********111",
                       "type": "Visa",
                       "expiration": "07 / 23",
                       "cvv": "321"
                   },
                   "billingInfo": {
                       "first_name": "Eater",
                       "middle_name": "Fresh",
                       "last_name": "Sausage",
                       "address": "1013 Weda Cir",
                       "country_id": "US",
                       "city": "Gotham City",
                       "state": "KY",
                       "zip": "99999",
                       "phone": "+19074861000",
                       "email": "test@test.com"
                   }
               }
           ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
       "status": 200,
       "message": "OK",
       "data": {
           "resultMessage": "Transaction processed codes(728282,728283)"
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
       "status": 400,
       "message": "Payment save is failed. Transaction already exist. Code:(728283)",
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (500):",content:`HTTP/1.1 500 Internal Server Error
{
       "status": "Failed",
       "source": {
           "type": 1,
           "status": 500
       },
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"},{title:"Error-Response (422):",content:`HTTP/1.1 422 Unprocessable entity
{
       "status": "Failed",
       "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
       "errors": [
             "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
       ],
       "code": 0,
       "technical": {
          ...
       },
       "request": {
          ...
       }
}`,type:"json"}]},filename:"v2/controllers/PaymentController.php",groupTitle:"Payment"},{type:"post",url:"/v1/quote/create",title:"Create Quote",version:"0.1.0",name:"CreateQuote",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project</p>"},{group:"Parameter",type:"object",optional:!1,field:"Lead",description:"<p>Lead data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"Lead.uid",description:"<p>uid</p>"},{group:"Parameter",type:"int",optional:!0,field:"Lead.market_info_id",description:"<p>market_info_id</p>"},{group:"Parameter",type:"int",optional:!0,field:"Lead.bo_flight_id",description:"<p>bo_flight_id</p>"},{group:"Parameter",type:"float",optional:!0,field:"Lead.final_profit",description:"<p>final_profit</p>"},{group:"Parameter",type:"object",optional:!1,field:"Quote",description:"<p>Quote data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.uid",description:"<p>uid</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.record_locator",description:"<p>record_locator</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.pcc",description:"<p>pcc</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.cabin",description:"<p>cabin</p>"},{group:"Parameter",type:"string",size:"1",optional:!1,field:"Quote.gds",description:"<p>gds</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.trip_type",description:"<p>trip_type</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.main_airline_code",description:"<p>main_airline_code</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.reservation_dump",description:"<p>reservation_dump</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.status",description:"<p>status</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.check_payment",description:"<p>check_payment</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.fare_type",description:"<p>fare_type</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.employee_name",description:"<p>employee_name</p>"},{group:"Parameter",type:"bool",optional:!0,field:"Quote.created_by_seller",description:"<p>created_by_seller</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.type_id",description:"<p>type_id</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.prod_types[]",description:"<p>Quote labels</p>"},{group:"Parameter",type:"object",optional:!1,field:"QuotePrice[]",description:"<p>QuotePrice data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"QuotePrice.uid",description:"<p>uid</p>"},{group:"Parameter",type:"string",optional:!0,field:"QuotePrice.passenger_type",description:"<p>passenger_type</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.selling",description:"<p>selling</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.net",description:"<p>net</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.fare",description:"<p>fare</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.taxes",description:"<p>taxes</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.mark_up",description:"<p>mark_up</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.extra_mark_up",description:"<p>extra_mark_up</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.service_fee",description:"<p>service_fee</p>"}]},examples:[{title:"Request-Example:",content:`{
     "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     "Lead": {
         "uid": "5de486f15f095",
         "market_info_id": 52,
         "bo_flight_id": 0,
         "final_profit": 0
     },
     "Quote": {
         "uid": "5f207ec201b99",
         "record_locator": null,
         "pcc": "0RY9",
         "cabin": "E",
         "gds": "S",
         "trip_type": "RT",
         "main_airline_code": "UA",
         "reservation_dump": "1 KL6123V 15OCT Q MCOAMS SS1   801P 1100A  16OCT F /DCKL /E \\n 2 KL1009L 18OCT S AMSLHR SS1  1015A 1045A /DCKL /E",
         "status": 1,
         "check_payment": "1",
         "fare_type": "TOUR",
         "employee_name": "Barry",
         "created_by_seller": false,
         "type_id" : 0,
         "prod_types" : ["SEP", "TOUR"]
     },
     "QuotePrice": [
         {
             "uid": "expert.5f207ec222c86",
             "passenger_type": "ADT",
             "selling": 696.19,
             "net": 622.65,
             "fare": 127,
             "taxes": 495.65,
             "mark_up": 50,
             "extra_mark_up": 0,
             "service_fee": 23.54
         }
     ]
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"string",optional:!1,field:"status",description:"<p>Status</p>"},{group:"Success 200",type:"string",optional:!1,field:"action",description:"<p>Action</p>"},{group:"Success 200",type:"integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
 {
     "status": "Success",
     "action": "v1/quote/create",
     "response_id": 11926893,
     "request_dt": "2020-09-22 05:05:54",
     "response_dt": "2020-09-22 05:05:54",
     "execution_time": 0.193,
     "memory_usage": 1647440
 }`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`  HTTP/1.1 404 Not Found
  {
      "name": "Not Found",
      "message": "Already Exist Quote UID: 5f207ec201b19",
      "code": 2,
      "status": 404,
      "type": "yii\\\\web\\\\NotFoundHttpException"
  }


@return array
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException`,type:"json"}]},filename:"v1/controllers/QuoteController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/quote/create-data",title:"Create Flight Quote by origin search data",version:"1.0.0",name:"CreateQuoteData",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"Integer",optional:!1,field:"lead_id",description:"<p>Lead Id</p>"},{group:"Parameter",type:"String",optional:!1,field:"origin_search_data",description:"<p>Origin Search Data from air search service <code>Valid JSON</code></p>"},{group:"Parameter",type:"String",size:"..max 50",optional:!0,field:"provider_project_key",description:"<p>Project Key</p>"}]},examples:[{title:"Request-Example:",content:`{
            "lead_id": 513145,
            "origin_search_data": "{\\"key\\":\\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\\",\\"routingId\\":1,\\"prices\\":{\\"lastTicketDate\\":\\"2021-05-05\\",\\"totalPrice\\":408.9,\\"totalTax\\":99.9,\\"comm\\":0,\\"isCk\\":false,\\"markupId\\":0,\\"markupUid\\":\\"\\",\\"markup\\":0},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"JWZ\\",\\"cnt\\":2,\\"baseFare\\":103,\\"pubBaseFare\\":103,\\"baseTax\\":33.3,\\"markup\\":0,\\"comm\\":0,\\"price\\":136.3,\\"tax\\":33.3,\\"oBaseFare\\":{\\"amount\\":103,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":33.3,\\"currency\\":\\"USD\\"}},\\"CHD\\":{\\"codeAs\\":\\"JWC\\",\\"cnt\\":1,\\"baseFare\\":103,\\"pubBaseFare\\":103,\\"baseTax\\":33.3,\\"markup\\":0,\\"comm\\":0,\\"price\\":136.3,\\"tax\\":33.3,\\"oBaseFare\\":{\\"amount\\":103,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":33.3,\\"currency\\":\\"USD\\"}}},\\"penalties\\":{\\"exchange\\":true,\\"refund\\":false,\\"list\\":[{\\"type\\":\\"ex\\",\\"applicability\\":\\"before\\",\\"permitted\\":true,\\"amount\\":72,\\"oAmount\\":{\\"amount\\":72,\\"currency\\":\\"USD\\"}},{\\"type\\":\\"ex\\",\\"applicability\\":\\"after\\",\\"permitted\\":true,\\"amount\\":72,\\"oAmount\\":{\\"amount\\":72,\\"currency\\":\\"USD\\"}},{\\"type\\":\\"re\\",\\"applicability\\":\\"before\\",\\"permitted\\":false},{\\"type\\":\\"re\\",\\"applicability\\":\\"after\\",\\"permitted\\":false}]},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2021-11-17 09:30\\",\\"arrivalTime\\":\\"2021-11-17 10:45\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"202\\",\\"bookingClass\\":\\"E\\",\\"duration\\":75,\\"departureAirportCode\\":\\"KIV\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"OTP\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"AT7\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":215,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"EOWSVRMD\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2021-11-17 12:20\\",\\"arrivalTime\\":\\"2021-11-17 14:05\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"391\\",\\"bookingClass\\":\\"E\\",\\"duration\\":225,\\"departureAirportCode\\":\\"OTP\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"LHR\\",\\"arrivalAirportTerminal\\":\\"4\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"73H\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":1292,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"EOWSVRGB\\",\\"baggage\\":{\\"ADT\\":{\\"carryOn\\":true,\\"allowPieces\\":1},\\"CHD\\":{\\"carryOn\\":true,\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":395}],\\"maxSeats\\":3,\\"paxCnt\\":3,\\"validatingCarrier\\":\\"RO\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"DVI\\",\\"cons\\":\\"GTT\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"keys\\":{\\"travelport\\":{\\"traceId\\":\\"908f70b5-cbe1-4800-89e2-1f0496cc1502\\",\\"availabilitySources\\":\\"A,A\\",\\"type\\":\\"T\\"},\\"seatHoldSeg\\":{\\"trip\\":0,\\"segment\\":0,\\"seats\\":3}},\\"meta\\":{\\"eip\\":0,\\"noavail\\":false,\\"searchId\\":\\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0xMS0xNw==\\",\\"lang\\":\\"en\\",\\"group1\\":\\"KIVLON:RORO:0:408.90\\",\\"rank\\":10,\\"cheapest\\":true,\\"fastest\\":false,\\"best\\":true,\\"bags\\":1,\\"country\\":\\"us\\",\\"prod_types\\":[\\"PUB\\"]}}",
            "provider_project_key": "hop2"
        }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
            "status": 200,
            "message": "OK",
            "data": {
                "quote_uid": "609259bfe52b9"
            }
        }`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "Validation error",
            "errors": {
                "lead_id": [
                    "Lead Id is invalid."
                ]
            },
            "code": 0
        }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Validation Error
{
            "status": 422,
            "message": "Error",
            "errors": [
                "Not found project relation by key: ovago"
            ],
            "code": 0
        }`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request

{
            "status": 400,
            "message": "Load data error",
            "errors": [
                "Not found data on POST request"
            ],
            "code": 0
        }`,type:"json"}]},filename:"v1/controllers/QuoteController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/quote/create-key",title:"Create Flight Quote by key",version:"1.0.0",name:"CreateQuoteKey",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"Integer",optional:!1,field:"lead_id",description:"<p>Lead Id</p>"},{group:"Parameter",type:"String",optional:!1,field:"offer_search_key",description:"<p>Search key</p>"},{group:"Parameter",type:"String",size:"..max 50",optional:!0,field:"provider_project_key",description:"<p>Project Key</p>"}]},examples:[{title:"Request-Example:",content:`{
            "lead_id": 513146,
            "offer_search_key": "2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz",
            "provider_project_key": "hop2"
        }`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
            "status": 200,
            "message": "OK",
            "data": {
                "quote_uid": "609259bfe52b9"
            }
        }`,type:"json"}]},error:{examples:[{title:"Error-Response (422):",content:`
HTTP/1.1 422 Unprocessable entity
{
            "status": 422,
            "message": "Validation error",
            "errors": {
                "lead_id": [
                    "Lead Id is invalid."
                ]
            },
            "code": 0
        }`,type:"json"},{title:"Error-Response (422):",content:`
HTTP/1.1 422 Validation Error
{
            "status": 422,
            "message": "Error",
            "errors": [
                "Not found project relation by key: ovago"
            ],
            "code": 0
        }`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request

{
            "status": 400,
            "message": "Load data error",
            "errors": [
                "Not found data on POST request"
            ],
            "code": 0
        }`,type:"json"}]},filename:"v1/controllers/QuoteController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/quote/get-info",title:"Get Quote",version:"0.1.0",name:"GetQuote",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"13",optional:!1,field:"uid",description:"<p>Quote UID</p>"},{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project (if not use Basic-Authorization)</p>"},{group:"Parameter",type:"string",optional:!0,field:"clientIP",description:"<p>Client IP address</p>"},{group:"Parameter",type:"bool",optional:!0,field:"clientUseProxy",description:"<p>Client Use Proxy</p>"},{group:"Parameter",type:"string",optional:!0,field:"clientUserAgent",description:"<p>Client User Agent</p>"}]},examples:[{title:"Request-Example:",content:`{
     "uid": "5b6d03d61f078",
     "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"string",optional:!1,field:"status",description:"<p>Status</p>"},{group:"Success 200",type:"object",optional:!1,field:"itinerary",description:"<p>Itinerary List</p>"},{group:"Success 200",type:"array",optional:!1,field:"errors",description:"<p>Errors</p>"},{group:"Success 200",type:"string",optional:!1,field:"uid",description:"<p>Quote UID</p>"},{group:"Success 200",type:"integer",optional:!1,field:"lead_id",description:"<p>Lead ID</p>"},{group:"Success 200",type:"string",optional:!1,field:"lead_uid",description:"<p>Lead UID</p>"},{group:"Success 200",type:"integer",optional:!1,field:"client_id",description:"<p>Client ID</p>"},{group:"Success 200",type:"integer",optional:!1,field:"lead_type",description:"<p><code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code></p>"},{group:"Success 200",type:"string",optional:!1,field:"agentName",description:"<p>Agent Name</p>"},{group:"Success 200",type:"string",optional:!1,field:"agentEmail",description:"<p>Agent Email</p>"},{group:"Success 200",type:"string",optional:!1,field:"agentDirectLine",description:"<p>Agent DirectLine</p>"},{group:"Success 200",type:"string",optional:!1,field:"action",description:"<p>Action</p>"},{group:"Success 200",type:"integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p> <p>&quot;errors&quot;: [], &quot;uid&quot;: &quot;5b7424e858e91&quot;, &quot;agentName&quot;: &quot;admin&quot;, &quot;agentEmail&quot;: &quot;assistant@wowfare.com&quot;, &quot;agentDirectLine&quot;: &quot;+1 888 946 3882&quot;, &quot;action&quot;: &quot;v1/quote/get-info&quot;,</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
  "status": "Success",
  "itinerary": {
      "typeId": 2,
      "typeName": "Alternative",
      "tripType": "OW",
      "mainCarrier": "WOW air",
      "trips": [
          {
              "segments": [
                  {
                      "carrier": "WW",
                      "airlineName": "WOW air",
                      "departureAirport": "BOS",
                      "arrivalAirport": "KEF",
                      "departureDateTime": {
                          "date": "2018-09-19 19:00:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "arrivalDateTime": {
                          "date": "2018-09-20 04:30:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "flightNumber": "126",
                      "bookingClass": "O",
                      "departureCity": "Boston",
                      "arrivalCity": "Reykjavik",
                      "flightDuration": 330,
                      "layoverDuration": 0,
                      "cabin": "E",
                      "departureCountry": "United States",
                      "arrivalCountry": "Iceland"
                  },
                  {
                      "carrier": "WW",
                      "airlineName": "WOW air",
                      "departureAirport": "KEF",
                      "arrivalAirport": "LGW",
                      "departureDateTime": {
                          "date": "2018-09-20 15:30:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "arrivalDateTime": {
                          "date": "2018-09-20 19:50:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "flightNumber": "814",
                      "bookingClass": "N",
                      "departureCity": "Reykjavik",
                      "arrivalCity": "London",
                      "flightDuration": 200,
                      "layoverDuration": 660,
                      "cabin": "E",
                      "departureCountry": "Iceland",
                      "arrivalCountry": "United Kingdom"
                  }
              ],
              "totalDuration": 1190,
              "routing": "BOS-KEF-LGW",
              "title": "Boston - London"
          }
      ],
      "price": {
          "detail": {
              "ADT": {
                  "selling": 350.2,
                  "fare": 237,
                  "taxes": 113.2,
                  "tickets": 1
              }
          },
          "tickets": 1,
          "selling": 350.2,
          "amountPerPax": 350.2,
          "fare": 237,
          "mark_up": 0,
          "taxes": 113.2,
          "currency": "USD",
          "isCC": false
      }
  },
 "itineraryOrigin": {
     "uid": "5f207ec202212",
     "typeId": 1,
     "typeName": "Original",
     "tripType": "OW",
     "mainCarrier": "WOW air",
     "trips": [
          {
              "segments": [
                  {
                      "carrier": "WW",
                      "airlineName": "WOW air",
                      "departureAirport": "BOS",
                      "arrivalAirport": "KEF",
                      "departureDateTime": {
                          "date": "2018-09-19 19:00:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "arrivalDateTime": {
                          "date": "2018-09-20 04:30:00.000000",
                          "timezone_type": 3,
                          "timezone": "UTC"
                      },
                      "flightNumber": "126",
                      "bookingClass": "O",
                      "departureCity": "Boston",
                      "arrivalCity": "Reykjavik",
                      "flightDuration": 330,
                      "layoverDuration": 0,
                      "cabin": "E",
                      "departureCountry": "United States",
                      "arrivalCountry": "Iceland"
                  }
              ],
              "totalDuration": 1190,
              "routing": "BOS-KEF",
              "title": "Boston - London"
          }
      ],
      "price": {
          "detail": {
              "ADT": {
                  "selling": 350.2,
                  "fare": 237,
                  "taxes": 113.2,
                  "tickets": 1
              }
          },
          "tickets": 1,
          "selling": 350.2,
          "amountPerPax": 350.2,
          "fare": 237,
          "mark_up": 0,
          "taxes": 113.2,
          "currency": "USD",
          "isCC": false
      }
  },
  "errors": [],
  "uid": "5b7424e858e91",
  "lead_id": 123456,
  "lead_uid": "00jhk0017",
  "client_id": 1034,
  "client": {
      "id": 1034,
      "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
   },
  "lead_delayed_charge": 0,
  "lead_status": "sold",
  "lead_type": 2,
  "booked_quote_uid": "5b8ddfc56a15c",
  "source_code": "38T556",
  "check_payment": true,
  "agentName": "admin",
  "agentEmail": "assistant@wowfare.com",
  "agentDirectLine": "+1 888 946 3882",
  "visitor_log": {
      "vl_source_cid": "string_abc",
      "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
      "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
      "vl_customer_id": "3",
      "vl_gclid": "gclid=TeSter-123#bookmark",
      "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
      "vl_utm_source": "newsletter4",
      "vl_utm_medium": "string_abc",
      "vl_utm_campaign": "string_abc",
      "vl_utm_term": "string_abc",
      "vl_utm_content": "string_abc",
      "vl_referral_url": "string_abc",
      "vl_location_url": "string_abc",
      "vl_user_agent": "string_abc",
      "vl_ip_address": "127.0.0.1",
      "vl_visit_dt": "2020-02-14 12:00:00",
      "vl_created_dt": "2020-02-28 17:17:33"
  },
 "lead": {
      "additionalInformation": [
          {
             "pnr": "example_pnr",
              "bo_sale_id": "example_sale_id",
             "vtf_processed": null,
             "tkt_processed": null,
             "exp_processed": null,
             "passengers": [],
             "paxInfo": []
         }
     ],
     "lead_data": [
         {
             "ld_field_key": "kayakclickid",
             "ld_field_value": "example_value132"
         }
     ]
 },
  "action": "v1/quote/get-info",
  "response_id": 173,
  "request_dt": "2018-08-16 06:42:03",
  "response_dt": "2018-08-16 06:42:03"
}`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`  HTTP/1.1 404 Not Found
  {
      "name": "Not Found",
      "message": "Not found Quote UID: 30",
      "code": 2,
      "status": 404,
      "type": "yii\\\\web\\\\NotFoundHttpException"
  }


@return array
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException`,type:"json"}]},filename:"v1/controllers/QuoteController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/offer-email/send-quote",title:"Offer email Send Quote",version:"0.1.0",name:"SendQuote",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"13",optional:!1,field:"quote_uid",description:"<p>Quote UID</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"template_key",description:"<p>Template key</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"email_from",description:"<p>Email from</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"email_from_name",description:"<p>Email from name</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"email_to",description:"<p>Email to</p>"},{group:"Parameter",type:"json",optional:!0,field:"additional_data",description:"<p>Additional data</p>"},{group:"Parameter",type:"string",size:"5",optional:!0,field:"language_id",description:"<p>Language Id</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"market_country_code",description:"<p>Market country code</p>"}]},examples:[{title:"Request-Example:",content:`{
   "quote_uid": "60910028642b8",
   "template_key": "cl_offer",
   "email_from": "from@test.com",
   "email_from_name": "Tester",
   "email_to": "to@test.com",
   "language_id": "en-US",
   "market_country_code": "RU",
   "additional_data": [
       {
           "code": "PR",
           "airline": "Philippine Airlines"
       }
   ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200,
   "message": "OK",
   "data": {
       "result": "Email sending. Mail ID(427561)"
   }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 404 Not Found
{
   "status": 422,
   "message": "Validation error",
   "errors": {
       "quote_uid": [
           "Quote not found by Uid(60910028642b1)"
       ]
   },
   "code": 0
}`,type:"json"},{title:"Error-Response (400):",content:`HTTP/1.1 400 Bad Request
{
   "name": "Bad Request",
   "message": "POST data request is empty",
   "code": 2,
   "status": 400,
   "type": "yii\\\\web\\\\BadRequestHttpException"
}`,type:"json"}]},filename:"v1/controllers/OfferEmailController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/offer-sms/send-quote",title:"Offer sms Send Quote",version:"0.1.0",name:"SendSmsQuote",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"13",optional:!1,field:"quote_uid",description:"<p>Quote UID</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"template_key",description:"<p>Template key</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"sms_from",description:"<p>Sms from</p>"},{group:"Parameter",type:"string",size:"50",optional:!1,field:"sms_to",description:"<p>Sms to</p>"},{group:"Parameter",type:"json",optional:!0,field:"additional_data",description:"<p>Additional data</p>"},{group:"Parameter",type:"string",size:"5",optional:!0,field:"language_id",description:"<p>Language Id</p>"},{group:"Parameter",type:"string",size:"2",optional:!0,field:"market_country_code",description:"<p>Market country code</p>"}]},examples:[{title:"Request-Example:",content:`{
   "quote_uid": "60910028642b8",
   "template_key": "sms_client_offer",
   "sms_from": "+16082175601",
   "sms_to": "+16082175602",
   "language_id": "en-US",
   "market_country_code": "RU",
   "additional_data": [
       {
           "code": "PR",
           "airline": "Philippine Airlines"
       }
   ]
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
   "status": 200,
   "message": "OK",
   "data": {
       "result": "Sms sending. Mail ID(427561)"
   }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 404 Not Found
{
   "status": 422,
   "message": "Validation error",
   "errors": {
       "quote_uid": [
           "Quote not found by Uid(60910028642b1)"
       ]
   },
   "code": 0
}`,type:"json"},{title:"Error-Response (400):",content:`HTTP/1.1 400 Bad Request
{
   "name": "Bad Request",
   "message": "POST data request is empty",
   "code": 2,
   "status": 400,
   "type": "yii\\\\web\\\\BadRequestHttpException"
}`,type:"json"}]},filename:"v1/controllers/OfferSmsController.php",groupTitle:"Quotes"},{type:"post",url:"/v1/quote/update",title:"Update Quote",version:"0.1.0",name:"UpdateQuote",group:"Quotes",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project</p>"},{group:"Parameter",type:"object",optional:!1,field:"Quote",description:"<p>Quote data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.uid",description:"<p>uid</p>"},{group:"Parameter",type:"bool",optional:!0,field:"Quote.needSync",description:"<p>needSync</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.main_airline_code",description:"<p>main_airline_code</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.reservation_dump",description:"<p>reservation_dump</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.status",description:"<p>status</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.check_payment",description:"<p>check_payment</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.fare_type",description:"<p>fare_type</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.employee_name",description:"<p>employee_name</p>"},{group:"Parameter",type:"bool",optional:!0,field:"Quote.created_by_seller",description:"<p>created_by_seller</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.type_id",description:"<p>type_id</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.prod_types[]",description:"<p>Quote labels</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.baggage[]",description:"<p>Quote baggage</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.baggage.segment[]",description:"<p>Quote baggage segment</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.baggage.free_baggage[]",description:"<p>Quote baggage segment</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.baggage.free_baggage.piece",description:"<p>Quote free baggage piece number</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.baggage.free_baggage.weight",description:"<p>Quote free baggage weight</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.baggage.free_baggage.height",description:"<p>Quote free baggage height</p>"},{group:"Parameter",type:"object",optional:!0,field:"Quote.baggage.paid_baggage[]",description:"<p>Quote paid baggage</p>"},{group:"Parameter",type:"int",optional:!0,field:"Quote.baggage.paid_baggage.piece",description:"<p>Quote paid baggage piece number</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.baggage.paid_baggage.weight",description:"<p>Quote paid baggage weight</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.baggage.paid_baggage.height",description:"<p>Quote paid baggage height</p>"},{group:"Parameter",type:"string",optional:!0,field:"Quote.baggage.paid_baggage.price",description:"<p>Quote paid baggage price</p>"},{group:"Parameter",type:"object",optional:!0,field:"Lead[]",description:"<p>Lead data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"Lead.uid",description:"<p>uid</p>"},{group:"Parameter",type:"int",optional:!0,field:"Lead.market_info_id",description:"<p>market_info_id</p>"},{group:"Parameter",type:"int",optional:!0,field:"Lead.bo_flight_id",description:"<p>bo_flight_id</p>"},{group:"Parameter",type:"float",optional:!0,field:"Lead.final_profit",description:"<p>final_profit</p>"},{group:"Parameter",type:"array",optional:!0,field:"Lead.additional_information[]",description:"<p>additional information array</p>"},{group:"Parameter",type:"object",optional:!0,field:"QuotePrice[]",description:"<p>QuotePrice data array</p>"},{group:"Parameter",type:"string",optional:!0,field:"QuotePrice.uid",description:"<p>uid</p>"},{group:"Parameter",type:"string",optional:!0,field:"QuotePrice.passenger_type",description:"<p>passenger_type</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.selling",description:"<p>selling</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.net",description:"<p>net</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.fare",description:"<p>fare</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.taxes",description:"<p>taxes</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.mark_up",description:"<p>mark_up</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.extra_mark_up",description:"<p>extra_mark_up</p>"},{group:"Parameter",type:"float",optional:!0,field:"QuotePrice.service_fee",description:"<p>service_fee</p>"}]},examples:[{title:"Request-Example:",content:`{
     "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     "Quote": {
         "uid": "5f207ec201b99",
         "record_locator": null,
         "pcc": "0RY9",
         "cabin": "E",
         "gds": "S",
         "trip_type": "RT",
         "main_airline_code": "UA",
         "reservation_dump": "1 KL6123V 15OCT Q MCOAMS SS1   801P 1100A  16OCT F /DCKL /E \\n 2 KL1009L 18OCT S AMSLHR SS1  1015A 1045A /DCKL /E",
         "status": 1,
         "check_payment": "1",
         "fare_type": "TOUR",
         "employee_name": "Barry",
         "created_by_seller": false,
         "type_id" : 0,
         "baggage" : [],
         "prod_types" : ["SEP", "TOUR"]
     },
     "Lead": {
         "uid": "5de486f15f095",
         "market_info_id": 52,
         "bo_flight_id": 0,
         "final_profit": 0
     },
     "QuotePrice": [
         {
             "uid": "expert.5f207ec222c86",
             "passenger_type": "ADT",
             "selling": 696.19,
             "net": 622.65,
             "fare": 127,
             "taxes": 495.65,
             "mark_up": 50,
             "extra_mark_up": 0,
             "service_fee": 23.54
         }
     ]
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"string",optional:!1,field:"status",description:"<p>Status</p>"},{group:"Success 200",type:"array",optional:!1,field:"errors",description:"<p>Errors</p>"},{group:"Success 200",type:"string",optional:!1,field:"action",description:"<p>Action</p>"},{group:"Success 200",type:"integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
 {
     "status": "Success",
     "errors":[],
     "action": "v1/quote/update",
     "response_id": 11926893,
     "request_dt": "2020-09-22 05:05:54",
     "response_dt": "2020-09-22 05:05:54",
     "execution_time": 0.193,
     "memory_usage": 1647440
 }`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 404 Not Found
{
    "name": "Not Found",
    "message": "Not found Quote UID: 5f207ec201b19",
    "code": 2,
    "status": 404,
    "type": "yii\\\\web\\\\NotFoundHttpException"
}`,type:"json"},{title:"Error-Response (400):",content:`
HTTP/1.1 400 Bad Request
{
 "status":400,
 "message":"Quote.uid is required",
 "code":"1",
 "errors":[]
}


@return mixed
@throws BadRequestHttpException
@throws NotFoundHttpException
@throws UnprocessableEntityHttpException
@throws \\yii\\db\\Exception`,type:"json"}]},filename:"v1/controllers/QuoteController.php",groupTitle:"Quotes"},{type:"post",url:"/v2/quote/get-info",title:"Get Quote",version:"0.2.0",name:"GetQuote",group:"Quotes_v2",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"13",optional:!1,field:"uid",description:"<p>Quote UID</p>"},{group:"Parameter",type:"string",optional:!0,field:"apiKey",description:"<p>API Key for Project (if not use Basic-Authorization)</p>"},{group:"Parameter",type:"string",optional:!0,field:"clientIP",description:"<p>Client IP address</p>"},{group:"Parameter",type:"bool",optional:!0,field:"clientUseProxy",description:"<p>Client Use Proxy</p>"},{group:"Parameter",type:"string",optional:!0,field:"clientUserAgent",description:"<p>Client User Agent</p>"}]},examples:[{title:"Request-Example:",content:`{
     "uid": "5b6d03d61f078",
     "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
}`,type:"json"}]},success:{fields:{"Success 200":[{group:"Success 200",type:"string",optional:!1,field:"status",description:"<p>Status</p>"},{group:"Success 200",type:"object",optional:!1,field:"result",description:"<p>Result of itinerary and pricing</p>"},{group:"Success 200",type:"array",optional:!1,field:"errors",description:"<p>Errors</p>"},{group:"Success 200",type:"string",optional:!1,field:"uid",description:"<p>Quote UID</p>"},{group:"Success 200",type:"integer",optional:!1,field:"lead_id",description:"<p>Lead ID</p>"},{group:"Success 200",type:"string",optional:!1,field:"lead_uid",description:"<p>Lead UID</p>"},{group:"Success 200",type:"integer",optional:!1,field:"lead_type",description:"<p><code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code></p>"},{group:"Success 200",type:"string",optional:!1,field:"agentName",description:"<p>Agent Name</p>"},{group:"Success 200",type:"string",optional:!1,field:"agentEmail",description:"<p>Agent Email</p>"},{group:"Success 200",type:"string",optional:!1,field:"agentDirectLine",description:"<p>Agent DirectLine</p>"},{group:"Success 200",type:"string",optional:!1,field:"action",description:"<p>Action</p>"},{group:"Success 200",type:"integer",optional:!1,field:"response_id",description:"<p>Response Id</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"request_dt",description:"<p>Request Date &amp; Time</p>"},{group:"Success 200",type:"DateTime",optional:!1,field:"response_dt",description:"<p>Response Date &amp; Time</p> <p>&quot;errors&quot;: [], &quot;uid&quot;: &quot;5b7424e858e91&quot;, &quot;agentName&quot;: &quot;admin&quot;, &quot;agentEmail&quot;: &quot;assistant@wowfare.com&quot;, &quot;agentDirectLine&quot;: &quot;+1 888 946 3882&quot;, &quot;action&quot;: &quot;v2/quote/get-info&quot;,</p>"}]},examples:[{title:"Success-Response:",content:`HTTP/1.1 200 OK
{
  "status": "Success",
  "result": {
      "prices": {
          "totalPrice": 2056.98,
          "totalTax": 1058.98,
          "isCk": true
      },
      "passengers": {
          "ADT": {
              "cnt": 2,
              "price": 1028.49,
              "tax": 529.49,
              "baseFare": 499,
              "mark_up": 20,
              "extra_mark_up": 10
          },
          "INF": {
              "cnt": 1,
              "price": 0,
              "tax": 0,
              "baseFare": 0,
              "mark_up": 0,
              "extra_mark_up": 0
          }
      },
      "trips": [
          {
              "tripId": 1,
              "segments": [
                  {
                      "segmentId": 1,
                      "departureTime": "2019-12-06 16:20",
                      "arrivalTime": "2019-12-06 17:57",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "7312",
                      "bookingClass": "T",
                      "duration": 97,
                      "departureAirportCode": "IND",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "YYZ",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 1,
                      "baggage": {
                          "": {
                              "allowPieces": 2,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  },
                  {
                      "segmentId": 2,
                      "departureTime": "2019-12-06 20:45",
                      "arrivalTime": "2019-12-07 09:55",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "880",
                      "bookingClass": "T",
                      "duration": 430,
                      "departureAirportCode": "YYZ",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "CDG",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 2,
                      "baggage": {
                          "": {
                              "allowPieces": 2,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  },
                  {
                      "segmentId": 3,
                      "departureTime": "2019-12-07 13:40",
                      "arrivalTime": "2019-12-07 19:05",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "6692",
                      "bookingClass": "T",
                      "duration": 265,
                      "departureAirportCode": "CDG",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "IST",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 2,
                      "baggage": {
                          "": {
                              "allowPieces": 2,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  }
              ],
              "duration": 1185
          },
          {
              "tripId": 2,
              "segments": [
                  {
                      "segmentId": 1,
                      "departureTime": "2019-12-25 09:15",
                      "arrivalTime": "2019-12-25 10:35",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "6681",
                      "bookingClass": "T",
                      "duration": 140,
                      "departureAirportCode": "IST",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "GVA",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 1,
                      "baggage": {
                          "": {
                              "allowPieces": 1,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS \u2021",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 \u2021 MD\xAB KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  },
                  {
                      "segmentId": 2,
                      "departureTime": "2019-12-25 12:00",
                      "arrivalTime": "2019-12-25 17:34",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "835",
                      "bookingClass": "T",
                      "duration": 694,
                      "departureAirportCode": "GVA",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "YYZ",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 2,
                      "baggage": {
                          "": {
                              "allowPieces": 1,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS \u2021",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 \u2021 MD\xAB KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  },
                  {
                      "segmentId": 3,
                      "departureTime": "2019-12-25 20:55",
                      "arrivalTime": "2019-12-25 22:37",
                      "stop": 0,
                      "stops": null,
                      "flightNumber": "7313",
                      "bookingClass": "T",
                      "duration": 102,
                      "departureAirportCode": "YYZ",
                      "departureAirportTerminal": "",
                      "arrivalAirportCode": "IND",
                      "arrivalAirportTerminal": "",
                      "operatingAirline": "AC",
                      "airEquipType": null,
                      "marketingAirline": "AC",
                      "cabin": "Y",
                      "ticket_id": 2,
                      "baggage": {
                          "": {
                              "allowPieces": 1,
                              "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS \u2021",
                              "allowMaxWeight": "UP TO 50 POUNDS/23 \u2021 MD\xAB KILOGRAMS",
                              "charge": {
                                  "price": 100,
                                  "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                  "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                  "firstPiece": 1,
                                  "lastPiece": 1
                              }
                          }
                      }
                  }
              ],
              "duration": 1222
          }
      ],
      "validatingCarrier": "AC",
      "fareType": "PUB",
      "tripType": "RT",
      "currency": "USD",
      "currencyRate": 1
  },
  "errors": [],
  "uid": "5cb97d1c78486",
  "lead_id": 92322,
  "lead_uid": "5cb8735a502f5",
  "lead_expiration_dt": "2021-02-23 20:12:12",
  "lead_delayed_charge": 0,
  "lead_status": null,
  "lead_type": 2,
  "booked_quote_uid": null,
  "source_code": "38T556",
  "agentName": "admin",
  "agentEmail": "admin@wowfare.com",
  "agentDirectLine": "",
  "generalEmail": "info@wowfare.com",
  "generalDirectLine": "+37379731662",
  "typeId": 2,
  "typeName": "Alternative",
  "client": {
      "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
      "client_id": 331968,
      "first_name": "Johann",
      "middle_name": "Sebastian",
      "last_name": "Bach",
      "phones": [
          "+13152572166"
      ],
      "emails": [
          "example@test.com",
          "bah@gmail.com"
      ]
  },
  "quote": {
      "id": 382366,
      "uid": "5d43e1ec36372",
      "lead_id": 178363,
      "employee_id": 167,
      "record_locator": "",
      "pcc": "DFWG32100",
      "cabin": "E",
      "gds": "A",
      "trip_type": "OW",
      "main_airline_code": "SU",
      "reservation_dump": "1  SU1845T  22AUG  KIVSVO    255A    555A  TH",
      "status": 5,
      "check_payment": 1,
      "fare_type": "PUB",
      "created": "2019-08-02 07:10:36",
      "updated": "2019-08-05 08:58:18",
      "created_by_seller": 1,
      "employee_name": "alex.connor2",
      "last_ticket_date": "2019-08-09 00:00:00",
      "service_fee_percent": null,
      "pricing_info": null,
      "alternative": 1,
      "tickets": "[{\\"key\\":\\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\\",\\"routingId\\":0,\\"prices\\":{\\"lastTicketDate\\":\\"2019-08-11\\",\\"totalPrice\\":392.73,\\"totalTax\\":272.73,\\"markup\\":50,\\"markupId\\":0,\\"isCk\\":false,\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"JWZ\\",\\"cnt\\":1,\\"price\\":392.73,\\"tax\\":272.73,\\"baseFare\\":120,\\"pubBaseFare\\":120,\\"baseTax\\":222.73,\\"markup\\":50,\\"refundPenalty\\":\\"\\",\\"changePenalty\\":\\"Percentage: 100.00%\\",\\"endorsementPenalty\\":\\"\\",\\"publishFare\\":false,\\"fareDescription\\":\\"\\",\\"oBaseFare\\":{\\"amount\\":120,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":222.73,\\"currency\\":\\"USD\\"},\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}}},\\"maxSeats\\":0,\\"validatingCarrier\\":\\"UA\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"fareType\\":\\"SR\\",\\"tripType\\":\\"RT\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"trips\\":[{\\"tripId\\":1,\\"segmentIds\\":[1]},{\\"tripId\\":2,\\"segmentIds\\":[3]}]},{\\"key\\":\\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\\",\\"routingId\\":0,\\"prices\\":{\\"lastTicketDate\\":\\"2019-08-09\\",\\"totalPrice\\":305.3,\\"totalTax\\":184.3,\\"markup\\":50,\\"markupId\\":0,\\"isCk\\":false,\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":1,\\"price\\":305.3,\\"tax\\":184.3,\\"baseFare\\":121,\\"pubBaseFare\\":121,\\"baseTax\\":134.3,\\"markup\\":50,\\"refundPenalty\\":\\"Percentage: 100.00%\\",\\"changePenalty\\":\\"Percentage: 100.00%\\",\\"endorsementPenalty\\":\\"\\",\\"publishFare\\":false,\\"fareDescription\\":\\"\\",\\"oBaseFare\\":{\\"amount\\":121,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":134.3,\\"currency\\":\\"USD\\"},\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}}},\\"maxSeats\\":0,\\"validatingCarrier\\":\\"LX\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"RT\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"trips\\":[{\\"tripId\\":1,\\"segmentIds\\":[2,3]},{\\"tripId\\":2,\\"segmentIds\\":[1,2]}]}]",
      "origin_search_data": "{\\"key\\":\\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\\",\\"routingId\\":306,\\"gdsOfferId\\":\\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\\",\\"prices\\":{\\"lastTicketDate\\":\\"2019-11-23\\",\\"totalPrice\\":670.35,\\"totalTax\\":367.35,\\"markup\\":100,\\"markupId\\":0,\\"isCk\\":false,\\"oMarkup\\":{\\"amount\\":100,\\"currency\\":\\"USD\\"}},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"JWZ\\",\\"cnt\\":1,\\"price\\":670.35,\\"tax\\":367.35,\\"baseFare\\":303,\\"pubBaseFare\\":303,\\"baseTax\\":267.35,\\"markup\\":100,\\"refundPenalty\\":\\"Amount: USD375.00 Percentage: 100.00%\\",\\"changePenalty\\":\\"Amount: USD260.00 Percentage: 100.00%\\",\\"endorsementPenalty\\":\\" \\",\\"publishFare\\":false,\\"fareDescription\\":\\"\\",\\"oBaseFare\\":{\\"amount\\":303,\\"currency\\":\\"USD\\"},\\"oPubBaseFare\\":{\\"amount\\":303,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":267.35,\\"currency\\":\\"USD\\"},\\"oMarkup\\":{\\"amount\\":100,\\"currency\\":\\"USD\\"}}},\\"trips\\":[{\\"tripId\\":1,\\"segments\\":[{\\"segmentId\\":1,\\"departureTime\\":\\"2019-11-21 09:45\\",\\"arrivalTime\\":\\"2019-11-22 06:00\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"704\\",\\"bookingClass\\":\\"N\\",\\"duration\\":735,\\"departureAirportCode\\":\\"JFK\\",\\"departureAirportTerminal\\":\\"7\\",\\"arrivalAirportCode\\":\\"DOH\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"QR\\",\\"airEquipType\\":\\"351\\",\\"marketingAirline\\":\\"QR\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":6689,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"NLUSN1RO\\",\\"baggage\\":{\\"ADT\\":{\\"allowPieces\\":2}},\\"recheckBaggage\\":false},{\\"segmentId\\":2,\\"departureTime\\":\\"2019-11-22 07:10\\",\\"arrivalTime\\":\\"2019-11-22 11:25\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"227\\",\\"bookingClass\\":\\"N\\",\\"duration\\":315,\\"departureAirportCode\\":\\"DOH\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"SOF\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"QR\\",\\"airEquipType\\":\\"320\\",\\"marketingAirline\\":\\"QR\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":1999,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"NLUSN1RO\\",\\"baggage\\":{\\"ADT\\":{\\"allowPieces\\":2}},\\"recheckBaggage\\":false},{\\"segmentId\\":3,\\"departureTime\\":\\"2019-11-22 19:45\\",\\"arrivalTime\\":\\"2019-11-22 20:50\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"298\\",\\"bookingClass\\":\\"T\\",\\"duration\\":65,\\"departureAirportCode\\":\\"SOF\\",\\"departureAirportTerminal\\":\\"2\\",\\"arrivalAirportCode\\":\\"OTP\\",\\"arrivalAirportTerminal\\":\\"\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"AT7\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"I\\",\\"mileage\\":185,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"TOWSVR\\",\\"baggage\\":{\\"ADT\\":{\\"allowPieces\\":1}},\\"recheckBaggage\\":true},{\\"segmentId\\":4,\\"departureTime\\":\\"2019-11-23 08:35\\",\\"arrivalTime\\":\\"2019-11-23 10:15\\",\\"stop\\":0,\\"stops\\":[],\\"flightNumber\\":\\"301\\",\\"bookingClass\\":\\"T\\",\\"duration\\":160,\\"departureAirportCode\\":\\"OTP\\",\\"departureAirportTerminal\\":\\"\\",\\"arrivalAirportCode\\":\\"FRA\\",\\"arrivalAirportTerminal\\":\\"2\\",\\"operatingAirline\\":\\"RO\\",\\"airEquipType\\":\\"73W\\",\\"marketingAirline\\":\\"RO\\",\\"marriageGroup\\":\\"O\\",\\"mileage\\":903,\\"cabin\\":\\"Y\\",\\"meal\\":\\"\\",\\"fareCode\\":\\"TOWSVR\\",\\"baggage\\":{\\"ADT\\":{\\"allowPieces\\":1}},\\"recheckBaggage\\":false}],\\"duration\\":2550}],\\"maxSeats\\":0,\\"validatingCarrier\\":\\"RO\\",\\"gds\\":\\"G\\",\\"pcc\\":\\"NA\\",\\"cons\\":\\"GIS\\",\\"fareType\\":\\"NA\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"currencies\\":[\\"USD\\"],\\"currencyRates\\":{\\"USDUSD\\":{\\"from\\":\\"USD\\",\\"to\\":\\"USD\\",\\"rate\\":1}},\\"tickets\\":[{\\"key\\":\\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\\",\\"routingId\\":0,\\"prices\\":{\\"lastTicketDate\\":\\"2019-11-21\\",\\"totalPrice\\":388.8,\\"totalTax\\":267.8,\\"markup\\":50,\\"markupId\\":0,\\"isCk\\":false,\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"JWZ\\",\\"cnt\\":1,\\"price\\":388.8,\\"tax\\":267.8,\\"baseFare\\":121,\\"pubBaseFare\\":121,\\"baseTax\\":217.8,\\"markup\\":50,\\"refundPenalty\\":\\"Amount: USD375.00 \\",\\"changePenalty\\":\\"Amount: USD260.00\\",\\"endorsementPenalty\\":\\"\\",\\"publishFare\\":false,\\"fareDescription\\":\\"\\",\\"oBaseFare\\":{\\"amount\\":121,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":217.8,\\"currency\\":\\"USD\\"},\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}}},\\"maxSeats\\":0,\\"validatingCarrier\\":\\"QR\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"fareType\\":\\"SR\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"trips\\":[{\\"tripId\\":1,\\"segmentIds\\":[1,2]}]},{\\"key\\":\\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\\",\\"routingId\\":0,\\"prices\\":{\\"lastTicketDate\\":\\"2019-10-19\\",\\"totalPrice\\":265.6,\\"totalTax\\":83.6,\\"markup\\":50,\\"markupId\\":0,\\"isCk\\":false,\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}},\\"passengers\\":{\\"ADT\\":{\\"codeAs\\":\\"ADT\\",\\"cnt\\":1,\\"price\\":265.6,\\"tax\\":83.6,\\"baseFare\\":182,\\"pubBaseFare\\":182,\\"baseTax\\":33.6,\\"markup\\":50,\\"refundPenalty\\":\\"Percentage: 100.00%\\",\\"changePenalty\\":\\"Percentage: 100.00%\\",\\"endorsementPenalty\\":\\"\\",\\"publishFare\\":false,\\"fareDescription\\":\\"\\",\\"oBaseFare\\":{\\"amount\\":182,\\"currency\\":\\"USD\\"},\\"oBaseTax\\":{\\"amount\\":33.6,\\"currency\\":\\"USD\\"},\\"oMarkup\\":{\\"amount\\":50,\\"currency\\":\\"USD\\"}}},\\"maxSeats\\":0,\\"validatingCarrier\\":\\"RO\\",\\"gds\\":\\"T\\",\\"pcc\\":\\"E9V\\",\\"fareType\\":\\"PUB\\",\\"tripType\\":\\"OW\\",\\"cabin\\":\\"Y\\",\\"currency\\":\\"USD\\",\\"trips\\":[{\\"tripId\\":1,\\"segmentIds\\":[3,4]}]}]}",
      "typeId": 2,
      "typeName": "Alternative"
  },
  "itineraryOrigin": {
     "uid": "5f207ec202212",
     "typeId": 1,
     "typeName": "Original"
  },
  "visitor_log": {
      "vl_source_cid": "string_abc",
      "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
      "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
      "vl_customer_id": "3",
      "vl_gclid": "gclid=TeSter-123#bookmark",
      "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
      "vl_utm_source": "newsletter4",
      "vl_utm_medium": "string_abc",
      "vl_utm_campaign": "string_abc",
      "vl_utm_term": "string_abc",
      "vl_utm_content": "string_abc",
      "vl_referral_url": "string_abc",
      "vl_location_url": "string_abc",
      "vl_user_agent": "string_abc",
      "vl_ip_address": "127.0.0.1",
      "vl_visit_dt": "2020-02-14 12:00:00",
      "vl_created_dt": "2020-02-28 17:17:33"
  },
  "lead": {
      "additionalInformation": [
          {
             "pnr": "example_pnr",
              "bo_sale_id": "example_sale_id",
             "vtf_processed": null,
             "tkt_processed": null,
             "exp_processed": null,
             "passengers": [],
             "paxInfo": []
         }
     ],
     "lead_data": [
         {
             "ld_field_key": "kayakclickid",
             "ld_field_value": "example_value132"
         }
     ]
  },
  "action": "v2/quote/get-info",
  "response_id": 298939,
  "request_dt": "2019-04-25 13:12:44",
  "response_dt": "2019-04-25 13:12:44"
}`,type:"json"}]},error:{fields:{"Error 4xx":[{group:"Error 4xx",optional:!1,field:"UserNotFound",description:"<p>The id of the User was not found.</p>"}]},examples:[{title:"Error-Response:",content:`HTTP/1.1 404 Not Found
{
    "name": "Not Found",
    "message": "Not found Quote UID: 30",
    "code": 2,
    "status": 404,
    "type": "yii\\\\web\\\\NotFoundHttpException"
}`,type:"json"}]},filename:"v2/controllers/QuoteController.php",groupTitle:"Quotes_v2"},{type:"get",url:"/v2/user-group/list",title:"Get User Groups",version:"0.2.0",name:"UserGroupList",group:"UserGroup",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"},{group:"Header",type:"string",optional:!1,field:"Accept-Encoding",description:""},{group:"Header",type:"string",optional:!1,field:"If-Modified-Since",description:"<p>Format <code> day-name, day month year hour:minute:second GMT</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"},{title:"Header-Example (If-Modified-Since):",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate",
    "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
  {
      "status": 200,
      "message": "OK",
      "data": {
          "user-group": [
              {
                  "ug_id": 1,
                  "ug_key": "ug1",
                  "ug_name": "Bucuresti Team",
                 "ug_disable": 0,
                  "ug_updated_dt": "2018-12-18 09:17:45"
              },
              {
                  "ug_id": 2,
                  "ug_key": "ug2",
                  "ug_name": "100J Team",
                 "ug_disable": 0,
                  "ug_updated_dt": "2018-12-18 09:17:59"
              },
              {
                  "ug_id": 3,
                  "ug_key": "ug3",
                  "ug_name": "Pro Team",
                  "ug_disable": 1,
                  "ug_updated_dt": "2018-12-18 09:18:10"
              },
          ]
      },
      "technical": {
          "action": "v2/user-group/list",
          "response_id": 8080269,
          "request_dt": "2020-02-27 15:00:43",
          "response_dt": "2020-02-27 15:00:43",
          "execution_time": 0.006,
          "memory_usage": 189944
      },
      "request": []
  }`,type:"json"},{title:"Not Modified-Response (304):",content:`
HTTP/1.1 304 Not Modified
Cache-Control: public, max-age=3600
Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT`,type:"json"}]},error:{examples:[{title:"Error-Response (405):",content:`
HTTP/1.1 405 Method Not Allowed
  {
      "name": "Method Not Allowed",
      "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
      "code": 0,
      "status": 405,
      "type": "yii\\\\web\\\\MethodNotAllowedHttpException"
  }`,type:"json"}]},filename:"v2/controllers/UserGroupController.php",groupTitle:"UserGroup"},{type:"post",url:"/v2/bo/wh",title:"WebHook Flight Refund (BackOffice)",version:"0.1.0",name:"BackOffice_WebHook_Flight_Refund",group:"WebHooks_Incoming",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",allowedValues:["flight_refund"],optional:!1,field:"type",description:"<p>Message Type action</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:"<p>Any Data</p>"},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"}]},examples:[{title:"Request-Example Flight Refund:",content:`{
    "type": "flight_refund",
    "data": {
        "booking_id": "C4RB44",
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
     "data": {
         "success": true
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
     "status": 400,
     "message": "Load data error",
     "errors": [
         "Not found data on POST request"
      ]
}`,type:"json"}]},filename:"v2/controllers/BoController.php",groupTitle:"WebHooks_Incoming"},{type:"post",url:"/v2/bo/wh",title:"WebHook Reprotection Update (BackOffice)",version:"0.1.0",name:"BackOffice_WebHook_Reprotection_Update",group:"WebHooks_Incoming",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",allowedValues:["reprotection_update"],optional:!1,field:"type",description:"<p>Message Type action</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:"<p>Any Data</p>"},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"data.project_key",description:"<p>Project Key (&quot;ovago&quot;, &quot;hop2&quot;)</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.reprotection_quote_gid",description:"<p>Reprotection quote GID</p>"}]},examples:[{title:"Request-Example Reprotection Update:",content:`{
    "type": "reprotection_update",
    "data": {
        "booking_id": "C4RB44",
        "project_key": "ovago",
        "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179"
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
     "data": {
         "success": true
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
     "status": 400,
     "message": "Load data error",
     "errors": [
         "Not found data on POST request"
      ]
}`,type:"json"}]},filename:"v2/controllers/BoController.php",groupTitle:"WebHooks_Incoming"},{type:"post",url:"/v2/bo/wh",title:"WebHook Voluntary Flight Exchange (BackOffice)",version:"0.1.0",name:"BackOffice_WebHook_Voluntary_Flight_Exchange",group:"WebHooks_Incoming",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",allowedValues:["flight_exchange"],optional:!1,field:"type",description:"<p>Type action</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:"<p>Any Data</p>"},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"data.project_key",description:"<p>Project Key (ovago, hop2)</p>"},{group:"Parameter",type:"string",size:"20",allowedValues:["Processing","Exchanged","Canceled"],optional:!1,field:"data.status",description:"<p>Exchange status</p>"}]},examples:[{title:"Request-Example Voluntary Flight Exchange:",content:`{
    "type": "flight_exchange",
    "data": {
        "booking_id": "C4RB44",
        "project_key": "ovago",
        "status": "Exchanged", // allowed values Pending, Processing, Exchanged, Canceled
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
     "data": {
         "success": true
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
     "status": 400,
     "message": "Load data error",
     "errors": [
         "Not found data on POST request"
      ]
}`,type:"json"}]},filename:"v2/controllers/BoController.php",groupTitle:"WebHooks_Incoming"},{type:"post",url:"/v2/bo/wh",title:"WebHook Voluntary Flight Refund (BackOffice)",version:"0.1.0",name:"BackOffice_WebHook_Voluntary_Flight_Refund",group:"WebHooks_Incoming",permission:[{name:"Authorized User"}],header:{fields:{Header:[{group:"Header",type:"string",optional:!1,field:"Authorization",description:"<p>Credentials <code>base64_encode(Username:Password)</code></p>"}]},examples:[{title:"Header-Example:",content:`{
    "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
    "Accept-Encoding": "Accept-Encoding: gzip, deflate"
}`,type:"json"}]},parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",allowedValues:["voluntary_flight_refund"],optional:!1,field:"type",description:"<p>Message Type action</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:"<p>Any Data</p>"},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"20",optional:!1,field:"data.project_key",description:"<p>Project Key (&quot;ovago&quot;, &quot;hop2&quot;)</p>"},{group:"Parameter",type:"string",size:"20",allowedValues:["Processing","Refunded","Canceled"],optional:!1,field:"data.status",description:"<p>Refund status</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.orderId",description:"<p>Refund Order Id</p>"}]},examples:[{title:"Request-Example Voluntary Flight Refund:",content:`{
    "type": "voluntary_flight_refund",
    "data": {
        "booking_id": "C4RB44",
        "project_key": "ovago",
        "status": "Refunded", // allowed values Processing, Refunded, Canceled
        "orderId": "RT-SHCN37D" // OTA Refund order id
    }
}`,type:"json"}]},success:{examples:[{title:"Success-Response:",content:`
HTTP/1.1 200 OK
{
    "status": 200,
    "message": "OK",
     "data": {
         "success": true
     }
}`,type:"json"}]},error:{examples:[{title:"Error-Response:",content:`HTTP/1.1 400 Bad Request
{
     "status": 400,
     "message": "Load data error",
     "errors": [
         "Not found data on POST request"
      ]
}`,type:"json"}]},filename:"v2/controllers/BoController.php",groupTitle:"WebHooks_Incoming"},{type:"post",url:"flight/schedule-change",title:"WebHook Hybrid OTA ( flight/schedule-change )",version:"0.1.0",name:"Flight_schedule-change",group:"WebHooks_Outgoing",permission:[{name:"Basic Auth (#App:BasicAuth)"}],parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",optional:!1,field:"type",description:"<p>Type of message</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:""},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.reprotection_quote_gid",description:"<p>Reprotection quote GID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.case_gid",description:"<p>Case GID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.product_quote_gid",description:"<p>Product quote GID</p>"}]},examples:[{title:"Request message Example:",content:`{
    "type": "flight/schedule-change",
    "data": {
        "booking_id": "C4RB44",
        "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179"
        "case_gid": "1569a42c916c811e2033142d8ae54176"
        "product_quote_gid": "5569a42c916c811e2033142d8ae54170"
    }
}`,type:"json"}]},filename:"ApiDocData.php",groupTitle:"WebHooks_Outgoing"},{type:"post",url:"flight/voluntary-exchange/update",title:"WebHook Hybrid OTA ( flight/voluntary-exchange/update )",version:"0.1.0",name:"Flight_voluntary-exchange_update",group:"WebHooks_Outgoing",permission:[{name:"Basic Auth"}],parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",optional:!1,field:"type",description:"<p>Type of message</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:""},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.product_quote_gid",description:"<p>Product quote GID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.exchange_gid",description:"<p>Exchange GID</p>"},{group:"Parameter",type:"string",size:"32",allowedValues:["Pending","Exchanged","Canceled"],optional:!1,field:"data.exchange_status",description:"<p>Exchange Client status</p>"}]},examples:[{title:"Request message Example:",content:`{
    "type": "flight/voluntary-exchange/update",
    "data": {
        "booking_id": "C4RB44",
        "product_quote_gid": "4569a42c916c811e2033142d8ae54179"
        "exchange_gid": "1569a42c916c811e2033142d8ae54176"
        "exchange_status": "Exchanged"
    }
}`,type:"json"}]},filename:"ApiDocData.php",groupTitle:"WebHooks_Outgoing"},{type:"post",url:"flight/voluntary-refund/update",title:"WebHook Hybrid OTA ( flight/voluntary-refund/update )",version:"0.1.0",name:"Flight_voluntary-refund_update",group:"WebHooks_Outgoing",permission:[{name:"Basic Auth"}],parameter:{fields:{Parameter:[{group:"Parameter",type:"string",size:"30",optional:!1,field:"type",description:"<p>Type of message</p>"},{group:"Parameter",type:"array[]",optional:!1,field:"data",description:""},{group:"Parameter",type:"string",size:"8",optional:!1,field:"data.booking_id",description:"<p>Booking Id</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.product_quote_gid",description:"<p>Product quote GID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.refund_gid",description:"<p>Refund GID</p>"},{group:"Parameter",type:"string",size:"32",optional:!1,field:"data.refund_order_id",description:"<p>Refund Client status</p>"},{group:"Parameter",type:"string",size:"32",allowedValues:["Pending","Processing","Refunded","Canceled"],optional:!1,field:"data.refund_status",description:"<p>Refund Client status</p>"}]},examples:[{title:"Request message Example:",content:`{
    "type": "flight/voluntary-refund/update",
    "data": {
        "booking_id": "C4RB44",
        "product_quote_gid": "4569a42c916c811e2033142d8ae54179"
        "refund_gid": "1569a42c916c811e2033142d8ae54176"
        "refund_order_id": "XXXXXXXXX"
        "refund_status": "Processing"
    }
}`,type:"json"}]},filename:"ApiDocData.php",groupTitle:"WebHooks_Outgoing"}];const J={name:"CRM Sales API",version:"1.0.0",description:"apiDoc CRM Sales",title:"CRM Sales API",url:"",template:{withCompare:!0},sampleUrl:!1,defaultVersion:"0.0.0",apidoc:"0.3.0",generator:{name:"apidoc",time:"Mon Dec 27 2021 21:56:52 GMT+0200 (Eastern European Standard Time)",url:"https://apidocjs.com",version:"0.50.2"}};tn();const ue=l().compile(y()("#template-header").html()),Ce=l().compile(y()("#template-footer").html()),ie=l().compile(y()("#template-article").html()),ye=l().compile(y()("#template-compare-article").html()),pe=l().compile(y()("#template-generator").html()),Ee=l().compile(y()("#template-project").html()),Oe=l().compile(y()("#template-sections").html()),Ue=l().compile(y()("#template-sidenav").html());J.template||(J.template={}),J.template.withCompare==null&&(J.template.withCompare=!0),J.template.withGenerator==null&&(J.template.withGenerator=!0),J.template.forceLanguage&&mt(J.template.forceLanguage),J.template.aloneDisplay==null&&(J.template.aloneDisplay=!1);const Ne=(0,o.groupBy)(ce,me=>me.group),Se={};y().each(Ne,(me,fe)=>{Se[me]=(0,o.groupBy)(fe,ve=>ve.name)});const Fe=[];y().each(Se,(me,fe)=>{let ve=[];y().each(fe,(_e,Ye)=>{const Xe=Ye[0].title;Xe&&ve.push(Xe.toLowerCase()+"#~#"+_e)}),ve.sort(),J.order&&(ve=ln(ve,J.order,"#~#")),ve.forEach(_e=>{const Xe=_e.split("#~#")[1];fe[Xe].forEach(Re=>{Fe.push(Re)})})}),ce=Fe;let Le={};const sn={};let An={};An[J.version]=1,y().each(ce,(me,fe)=>{Le[fe.group]=1,sn[fe.group]=fe.groupTitle||fe.group,An[fe.version]=1}),Le=Object.keys(Le),Le.sort(),J.order&&(Le=qe(sn,J.order)),An=Object.keys(An),An.sort(r().compare),An.reverse();const We=[];Le.forEach(me=>{We.push({group:me,isHeader:!0,title:sn[me]});let fe="";ce.forEach(ve=>{ve.group===me&&(fe!==ve.name?We.push({title:ve.title,group:me,name:ve.name,type:ve.type,version:ve.version,url:ve.url}):We.push({title:ve.title,group:me,hidden:!0,name:ve.name,type:ve.type,version:ve.version,url:ve.url}),fe=ve.name)})});function kn(me,fe,ve){let _e=!1;if(!fe)return _e;const Ye=fe.match(/<h(1|2).*?>(.+?)<\/h(1|2)>/gi);return Ye&&Ye.forEach(function(Xe){const Re=Xe.substring(2,3),Fn=Xe.replace(/<.+?>/g,""),nt=Xe.match(/id="api-([^-]+)(?:-(.+))?"/),Tt=nt?nt[1]:null,Ut=nt?nt[2]:null;Re==="1"&&Fn&&Tt&&(me.splice(ve,0,{group:Tt,isHeader:!0,title:Fn,isFixed:!0}),ve++,_e=!0),Re==="2"&&Fn&&Tt&&Ut&&(me.splice(ve,0,{group:Tt,name:Ut,isHeader:!1,title:Fn,isFixed:!1,version:"1.0"}),ve++)}),_e}let q;if(J.header&&(q=kn(We,J.header.content,0),q||We.unshift({group:"_header",isHeader:!0,title:J.header.title==null?Wn("General"):J.header.title,isFixed:!0})),J.footer){const me=We.length;q=kn(We,J.footer.content,We.length),!q&&J.footer.title!=null&&We.splice(me,0,{group:"_footer",isHeader:!0,title:J.footer.title,isFixed:!0})}const H=J.title?J.title:"apiDoc: "+J.name+" - "+J.version;y()(document).attr("title",H),y()("#loader").remove();const K={nav:We};y()("#sidenav").append(Ue(K)),y()("#generator").append(pe(J)),(0,o.extend)(J,{versions:An}),y()("#project").append(Ee(J)),J.header&&y()("#header").append(ue(J.header)),J.footer&&(y()("#footer").append(Ce(J.footer)),J.template.aloneDisplay&&document.getElementById("api-_footer").classList.add("hide"));const ne={};let $="";Le.forEach(function(me){const fe=[];let ve="",_e={},Ye=me,Xe="";ne[me]={},ce.forEach(function(Re){me===Re.group&&(ve!==Re.name?(ce.forEach(function(Fn){me===Fn.group&&Re.name===Fn.name&&(Object.prototype.hasOwnProperty.call(ne[Re.group],Re.name)||(ne[Re.group][Re.name]=[]),ne[Re.group][Re.name].push(Fn.version))}),_e={article:Re,versions:ne[Re.group][Re.name]}):_e={article:Re,hidden:!0,versions:ne[Re.group][Re.name]},J.sampleUrl&&J.sampleUrl===!0&&(J.sampleUrl=window.location.origin),J.url&&_e.article.url.substr(0,4).toLowerCase()!=="http"&&(_e.article.url=J.url+_e.article.url),Ie(_e,Re),Re.groupTitle&&(Ye=Re.groupTitle),Re.groupDescription&&(Xe=Re.groupDescription),fe.push({article:ie(_e),group:Re.group,name:Re.name,aloneDisplay:J.template.aloneDisplay}),ve=Re.name)}),_e={group:me,title:Ye,description:Xe,articles:fe,aloneDisplay:J.template.aloneDisplay},$+=Oe(_e)}),y()("#sections").append($),J.template.aloneDisplay||(document.body.dataset.spy="scroll",y()("body").scrollspy({target:"#scrollingNav"})),y()(".form-control").on("focus change",function(){y()(this).removeClass("border-danger")}),y()(".sidenav").find("a").on("click",function(me){me.preventDefault();const fe=this.getAttribute("href");if(J.template.aloneDisplay){const ve=document.querySelector(".sidenav > li.active");ve&&ve.classList.remove("active"),this.parentNode.classList.add("active")}else{const ve=document.querySelector(fe);ve&&y()("html,body").animate({scrollTop:ve.offsetTop},400)}window.location.hash=fe});function re(me){let fe=!1;return y().each(me,ve=>{fe=fe||(0,o.some)(me[ve],_e=>_e.type)}),fe}function de(){y()('button[data-toggle="popover"]').popover().click(function(fe){fe.preventDefault()});const me=y()("#version strong").html();if(y()("#sidenav li").removeClass("is-new"),J.template.withCompare&&y()("#sidenav li[data-version='"+me+"']").each(function(){const fe=y()(this).data("group"),ve=y()(this).data("name"),_e=y()("#sidenav li[data-group='"+fe+"'][data-name='"+ve+"']").length,Ye=y()("#sidenav li[data-group='"+fe+"'][data-name='"+ve+"']").index(y()(this));(_e===1||Ye===_e-1)&&y()(this).addClass("is-new")}),y()(".nav-tabs-examples a").click(function(fe){fe.preventDefault(),y()(this).tab("show")}),y()(".nav-tabs-examples").find("a:first").tab("show"),y()(".sample-request-content-type-switch").change(function(){y()(this).val()==="body-form-data"?(y()("#sample-request-body-json-input-"+y()(this).data("id")).hide(),y()("#sample-request-body-form-input-"+y()(this).data("id")).show()):(y()("#sample-request-body-form-input-"+y()(this).data("id")).hide(),y()("#sample-request-body-json-input-"+y()(this).data("id")).show())}),J.template.aloneDisplay&&(y()(".show-group").click(function(){const fe="."+y()(this).attr("data-group")+"-group",ve="."+y()(this).attr("data-group")+"-article";y()(".show-api-group").addClass("hide"),y()(fe).removeClass("hide"),y()(".show-api-article").addClass("hide"),y()(ve).removeClass("hide")}),y()(".show-api").click(function(){const fe=this.getAttribute("href").substring(1),ve=document.getElementById("version").textContent.trim(),_e=`.${this.dataset.name}-article`,Ye=`[id="${fe}-${ve}"]`,Xe=`.${this.dataset.group}-group`;y()(".show-api-group").addClass("hide"),y()(Xe).removeClass("hide"),y()(".show-api-article").addClass("hide");let Re=y()(_e);y()(Ye).length&&(Re=y()(Ye).parent()),Re.removeClass("hide"),fe.match(/_(header|footer)/)&&document.getElementById(fe).classList.remove("hide")})),J.template.aloneDisplay||y()("body").scrollspy("refresh"),J.template.aloneDisplay){const fe=window.location.hash;if(fe!=null&&fe.length!==0){const ve=document.getElementById("version").textContent.trim(),_e=document.querySelector(`li .${fe.slice(1)}-init`),Ye=document.querySelector(`li[data-version="${ve}"] .show-api.${fe.slice(1)}-init`);let Xe=_e;Ye&&(Xe=Ye),Xe.click()}}}function Ae(me){typeof me=="undefined"?me=y()("#version strong").html():y()("#version strong").html(me),y()("article").addClass("hide"),y()("#sidenav li:not(.nav-fixed)").addClass("hide");const fe={};document.querySelectorAll("article[data-version]").forEach(ve=>{const _e=ve.dataset.group,Ye=ve.dataset.name,Xe=ve.dataset.version,Re=_e+Ye;!fe[Re]&&r().lte(Xe,me)&&(fe[Re]=!0,document.querySelector(`article[data-group="${_e}"][data-name="${Ye}"][data-version="${Xe}"]`).classList.remove("hide"),document.querySelector(`#sidenav li[data-group="${_e}"][data-name="${Ye}"][data-version="${Xe}"]`).classList.remove("hide"),document.querySelector(`#sidenav li.nav-header[data-group="${_e}"]`).classList.remove("hide"))}),y()("article[data-version]").each(function(ve){const _e=y()(this).data("group");y()("section#api-"+_e).removeClass("hide"),y()("section#api-"+_e+" article:visible").length===0?y()("section#api-"+_e).addClass("hide"):y()("section#api-"+_e).removeClass("hide")})}if(Ae(),y()("#versions li.version a").on("click",function(me){me.preventDefault(),Ae(y()(this).html())}),y()("#compareAllWithPredecessor").on("click",ke),y()("article .versions li.version a").on("click",be),y().urlParam=function(me){const fe=new RegExp("[\\?&amp;]"+me+"=([^&amp;#]*)").exec(window.location.href);return fe&&fe[1]?fe[1]:null},y().urlParam("compare")&&y()("#compareAllWithPredecessor").trigger("click"),window.location.hash){const me=decodeURI(window.location.hash);y()(me).length>0&&y()("html,body").animate({scrollTop:parseInt(y()(me).offset().top)},0)}y()("#scrollingNav .sidenav-search input.search").focus(),y()('[data-action="filter-search"]').on("keyup",me=>{const fe=me.currentTarget.value;y()(".sidenav").find("a.nav-list-item").each((ve,_e)=>{y()(_e).show(),_e.innerText.toLowerCase().includes(fe)||y()(_e).hide()})}),y()("span.search-reset").on("click",function(){y()("#scrollingNav .sidenav-search input.search").val("").focus(),y()(".sidenav").find("a.nav-list-item").show()});function be(me){me.preventDefault();const fe=y()(this).parents("article"),ve=y()(this).html(),_e=fe.find(".version"),Ye=_e.find("strong").html();_e.find("strong").html(ve);const Xe=fe.data("group"),Re=fe.data("name"),Fn=fe.data("version"),nt=fe.data("compare-version");if(nt!==ve&&!(!nt&&Fn===ve)){if(nt&&ne[Xe][Re][0]===ve||Fn===ve)$e(Xe,Re,Fn);else{let Tt={},Ut={};y().each(Se[Xe][Re],function(wa,nr){nr.version===Fn&&(Tt=nr),nr.version===ve&&(Ut=nr)});const _n={article:Tt,compare:Ut,versions:ne[Xe][Re]};_n.article.id=_n.article.group+"-"+_n.article.name+"-"+_n.article.version,_n.article.id=_n.article.id.replace(/\./g,"_"),_n.compare.id=_n.compare.group+"-"+_n.compare.name+"-"+_n.compare.version,_n.compare.id=_n.compare.id.replace(/\./g,"_");let hn=Tt;hn.parameter&&hn.parameter.fields&&(_n._hasTypeInParameterFields=re(hn.parameter.fields)),hn.error&&hn.error.fields&&(_n._hasTypeInErrorFields=re(hn.error.fields)),hn.success&&hn.success.fields&&(_n._hasTypeInSuccessFields=re(hn.success.fields)),hn.info&&hn.info.fields&&(_n._hasTypeInInfoFields=re(hn.info.fields)),hn=Ut,_n._hasTypeInParameterFields!==!0&&hn.parameter&&hn.parameter.fields&&(_n._hasTypeInParameterFields=re(hn.parameter.fields)),_n._hasTypeInErrorFields!==!0&&hn.error&&hn.error.fields&&(_n._hasTypeInErrorFields=re(hn.error.fields)),_n._hasTypeInSuccessFields!==!0&&hn.success&&hn.success.fields&&(_n._hasTypeInSuccessFields=re(hn.success.fields)),_n._hasTypeInInfoFields!==!0&&hn.info&&hn.info.fields&&(_n._hasTypeInInfoFields=re(hn.info.fields));const Ir=ye(_n);fe.after(Ir),fe.next().find(".versions li.version a").on("click",be),y()("#sidenav li[data-group='"+Xe+"'][data-name='"+Re+"'][data-version='"+Ye+"']").addClass("has-modifications"),fe.remove()}m().highlightAll()}}function ke(me){me.preventDefault(),y()("article:visible .versions").each(function(){const ve=y()(this).parents("article").data("version");let _e=null;y()(this).find("li.version a").each(function(){y()(this).html()<ve&&!_e&&(_e=y()(this))}),_e&&_e.trigger("click")})}function Ie(me,fe){me.id=me.article.group+"-"+me.article.name+"-"+me.article.version,me.id=me.id.replace(/\./g,"_"),fe.header&&fe.header.fields&&(me._hasTypeInHeaderFields=re(fe.header.fields)),fe.parameter&&fe.parameter.fields&&(me._hasTypeInParameterFields=re(fe.parameter.fields)),fe.error&&fe.error.fields&&(me._hasTypeInErrorFields=re(fe.error.fields)),fe.success&&fe.success.fields&&(me._hasTypeInSuccessFields=re(fe.success.fields)),fe.info&&fe.info.fields&&(me._hasTypeInInfoFields=re(fe.info.fields)),me.template=J.template}function Me(me,fe,ve){let _e={};y().each(Se[me][fe],function(Xe,Re){Re.version===ve&&(_e=Re)});const Ye={article:_e,versions:ne[me][fe]};return Ie(Ye,_e),ie(Ye)}function $e(me,fe,ve){const _e=y()("article[data-group='"+me+"'][data-name='"+fe+"']:visible"),Ye=Me(me,fe,ve);_e.after(Ye),_e.next().find(".versions li.version a").on("click",be),y()("#sidenav li[data-group='"+me+"'][data-name='"+fe+"'][data-version='"+ve+"']").removeClass("has-modifications"),_e.remove()}function ln(me,fe,ve){const _e=[];return fe.forEach(function(Ye){ve?me.forEach(function(Xe){const Re=Xe.split(ve);(Re[0]===Ye||Re[1]===Ye)&&_e.push(Xe)}):me.forEach(function(Xe){Xe===Ye&&_e.push(Ye)})}),me.forEach(function(Ye){_e.indexOf(Ye)===-1&&_e.push(Ye)}),_e}function qe(me,fe){const ve=[];return fe.forEach(_e=>{Object.keys(me).forEach(Ye=>{me[Ye].replace(/_/g," ")===_e&&ve.push(Ye)})}),Object.keys(me).forEach(_e=>{ve.indexOf(_e)===-1&&ve.push(_e)}),ve}de()}})()})();
