yii.validation=function(e){var a={isEmpty:function(a){return null==a||e.isArray(a)&&0===a.length||""===a},addMessage:function(e,a,s){e.push(a.replace(/\{value\}/g,s))},required:function(s,t,n){var i=!1;if(void 0===n.requiredValue){var r="string"==typeof s||s instanceof String;(n.strict&&void 0!==s||!n.strict&&!a.isEmpty(r?e.trim(s):s))&&(i=!0)}else(!n.strict&&s==n.requiredValue||n.strict&&s===n.requiredValue)&&(i=!0);i||a.addMessage(t,n.message,s)},boolean:function(e,s,t){t.skipOnEmpty&&a.isEmpty(e)||(!t.strict&&(e==t.trueValue||e==t.falseValue)||t.strict&&(e===t.trueValue||e===t.falseValue)||a.addMessage(s,t.message,e))},string:function(e,s,t){t.skipOnEmpty&&a.isEmpty(e)||("string"==typeof e?void 0===t.is||e.length==t.is?(void 0!==t.min&&e.length<t.min&&a.addMessage(s,t.tooShort,e),void 0!==t.max&&e.length>t.max&&a.addMessage(s,t.tooLong,e)):a.addMessage(s,t.notEqual,e):a.addMessage(s,t.message,e))},file:function(a,n,i){var r=s(a,n,i);e.each(r,function(e,a){t(a,n,i)})},image:function(n,i,r,o){var l=s(n,i,r);e.each(l,function(s,n){if(t(n,i,r),"undefined"!=typeof FileReader){var l=e.Deferred();a.validateImage(n,i,r,l,new FileReader,new Image),o.push(l)}})},validateImage:function(e,a,s,t,n,i){i.onload=function(){!function(e,a,s,t){t.minWidth&&a.width<t.minWidth&&s.push(t.underWidth.replace(/\{file\}/g,e.name));t.maxWidth&&a.width>t.maxWidth&&s.push(t.overWidth.replace(/\{file\}/g,e.name));t.minHeight&&a.height<t.minHeight&&s.push(t.underHeight.replace(/\{file\}/g,e.name));t.maxHeight&&a.height>t.maxHeight&&s.push(t.overHeight.replace(/\{file\}/g,e.name))}(e,i,a,s),t.resolve()},i.onerror=function(){a.push(s.notImage.replace(/\{file\}/g,e.name)),t.resolve()},n.onload=function(){i.src=this.result},n.onerror=function(){t.resolve()},n.readAsDataURL(e)},number:function(e,s,t){t.skipOnEmpty&&a.isEmpty(e)||("string"!=typeof e||t.pattern.test(e)?(void 0!==t.min&&e<t.min&&a.addMessage(s,t.tooSmall,e),void 0!==t.max&&e>t.max&&a.addMessage(s,t.tooBig,e)):a.addMessage(s,t.message,e))},range:function(s,t,n){if(!n.skipOnEmpty||!a.isEmpty(s))if(n.allowArray||!e.isArray(s)){var i=!0;e.each(e.isArray(s)?s:[s],function(a,s){return-1!=e.inArray(s,n.range)||(i=!1,!1)}),void 0===n.not&&(n.not=!1),n.not===i&&a.addMessage(t,n.message,s)}else a.addMessage(t,n.message,s)},regularExpression:function(e,s,t){t.skipOnEmpty&&a.isEmpty(e)||(!t.not&&!t.pattern.test(e)||t.not&&t.pattern.test(e))&&a.addMessage(s,t.message,e)},email:function(e,s,t){if(!t.skipOnEmpty||!a.isEmpty(e)){var n=!0,i=/^((?:"?([^"]*)"?\s)?)(?:\s+)?(?:(<?)((.+)@([^>]+))(>?))$/.exec(e);if(null===i)n=!1;else{var r=i[5],o=i[6];t.enableIDN&&(r=punycode.toASCII(r),o=punycode.toASCII(o),e=i[1]+i[3]+r+"@"+o+i[7]),n=!(r.length>64)&&(!((r+"@"+o).length>254)&&(t.pattern.test(e)||t.allowName&&t.fullPattern.test(e)))}n||a.addMessage(s,t.message,e)}},url:function(e,s,t){if(!t.skipOnEmpty||!a.isEmpty(e)){t.defaultScheme&&!/:\/\//.test(e)&&(e=t.defaultScheme+"://"+e);var n=!0;if(t.enableIDN){var i=/^([^:]+):\/\/([^\/]+)(.*)$/.exec(e);null===i?n=!1:e=i[1]+"://"+punycode.toASCII(i[2])+i[3]}n&&t.pattern.test(e)||a.addMessage(s,t.message,e)}},trim:function(s,t,n,i){var r=s.find(t.input);return r.is(":checkbox, :radio")?i:(i=r.val(),n.skipOnEmpty&&a.isEmpty(i)||(i=e.trim(i),r.val(i)),i)},captcha:function(s,t,n){if(!n.skipOnEmpty||!a.isEmpty(s)){var i=e("body").data(n.hashKey);i=null==i?n.hash:i[n.caseSensitive?0:1];for(var r=n.caseSensitive?s:s.toLowerCase(),o=r.length-1,l=0;o>=0;--o)l+=r.charCodeAt(o);l!=i&&a.addMessage(t,n.message,s)}},compare:function(s,t,n,i){if(!n.skipOnEmpty||!a.isEmpty(s)){var r,o=!0;if(void 0===n.compareAttribute)r=n.compareValue;else{var l=e("#"+n.compareAttribute);l.length||(l=i.find('[name="'+n.compareAttributeName+'"]')),r=l.val()}switch("number"===n.type&&(s=s?parseFloat(s):0,r=r?parseFloat(r):0),n.operator){case"==":o=s==r;break;case"===":o=s===r;break;case"!=":o=s!=r;break;case"!==":o=s!==r;break;case">":o=s>r;break;case">=":o=s>=r;break;case"<":o=s<r;break;case"<=":o=s<=r;break;default:o=!1}o||a.addMessage(t,n.message,s)}},ip:function(e,s,t){if(!t.skipOnEmpty||!a.isEmpty(e)){var n=null,i=null,r=new RegExp(t.ipParsePattern).exec(e);if(r&&(n=r[1]||null,e=r[2],i=r[4]||null),!0!==t.subnet||null!==i)if(!1!==t.subnet||null===i)if(!1!==t.negation||null===n)6==(-1===e.indexOf(":")?4:6)?(new RegExp(t.ipv6Pattern).test(e)||a.addMessage(s,t.messages.message,e),t.ipv6||a.addMessage(s,t.messages.ipv6NotAllowed,e)):(new RegExp(t.ipv4Pattern).test(e)||a.addMessage(s,t.messages.message,e),t.ipv4||a.addMessage(s,t.messages.ipv4NotAllowed,e));else a.addMessage(s,t.messages.message,e);else a.addMessage(s,t.messages.hasSubnet,e);else a.addMessage(s,t.messages.noSubnet,e)}}};function s(a,s,t){if("undefined"==typeof File)return[];var n=e(a.input,a.$form).get(0);if(void 0===n)return[];var i=n.files;return i?0===i.length?(t.skipOnEmpty||s.push(t.uploadRequired),[]):t.maxFiles&&t.maxFiles<i.length?(s.push(t.tooMany),[]):i:(s.push(t.message),[])}function t(e,a,s){if(s.extensions&&s.extensions.length>0){for(var t=!1,n=e.name.toLowerCase(),i=0;i<s.extensions.length;i++){var r=s.extensions[i].toLowerCase();if(""===r&&-1===n.indexOf(".")||n.substr(n.length-s.extensions[i].length-1)==="."+r){t=!0;break}}t||a.push(s.wrongExtension.replace(/\{file\}/g,e.name))}s.mimeTypes&&s.mimeTypes.length>0&&(function(e,a){for(var s=0,t=e.length;s<t;s++)if(new RegExp(e[s]).test(a))return!0;return!1}(s.mimeTypes,e.type)||a.push(s.wrongMimeType.replace(/\{file\}/g,e.name))),s.maxSize&&s.maxSize<e.size&&a.push(s.tooBig.replace(/\{file\}/g,e.name)),s.minSize&&s.minSize>e.size&&a.push(s.tooSmall.replace(/\{file\}/g,e.name))}return a}(jQuery);