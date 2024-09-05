(()=>{"use strict";function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e,t){for(var a=0;a<t.length;a++){var r=t[a];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,i(r.key),r)}}function i(t){var i=function(t,i){if("object"!=e(t)||!t)return t;var a=t[Symbol.toPrimitive];if(void 0!==a){var r=a.call(t,i||"default");if("object"!=e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===i?String:Number)(t)}(t,"string");return"symbol"==e(i)?i:String(i)}var a=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.template=$("#currency_template").html(),this.totalItem=0,this.deletedItems=[],this.initData(),this.handleForm()}var i,a,r;return i=e,(a=[{key:"initData",value:function(){var e=this,t=$.parseJSON($("#currencies").html());$.each(t,(function(t,i){var a=e.template.replace(/__id__/gi,i.id).replace(/__position__/gi,i.order).replace(/__isPrefixSymbolChecked__/gi,1==i.is_prefix_symbol?"selected":"").replace(/__notIsPrefixSymbolChecked__/gi,0==i.is_prefix_symbol?"selected":"").replace(/__isDefaultChecked__/gi,1==i.is_default?"checked":"").replace(/__title__/gi,i.title).replace(/__decimals__/gi,i.decimals).replace(/__exchangeRate__/gi,i.exchange_rate).replace(/__symbol__/gi,i.symbol);$(".swatches-container .swatches-list").append(a),e.totalItem++}))}},{key:"addNewAttribute",value:function(){var e=this,t=e.template.replace(/__id__/gi,0).replace(/__position__/gi,e.totalItem).replace(/__isPrefixSymbolChecked__/gi,"").replace(/__notIsPrefixSymbolChecked__/gi,"").replace(/__isDefaultChecked__/gi,0==e.totalItem?"checked":"").replace(/__title__/gi,"").replace(/__decimals__/gi,0).replace(/__exchangeRate__/gi,1).replace(/__symbol__/gi,"");$(".swatches-container .swatches-list").append(t),e.totalItem++}},{key:"exportData",value:function(){var e=[];return $(".swatches-container .swatches-list li").each((function(t,i){var a=$(i);e.push({id:a.data("id"),is_default:a.find("[data-type=is_default] input[type=radio]").is(":checked")?1:0,order:a.index(),title:a.find("[data-type=title] input").val(),symbol:a.find("[data-type=symbol] input").val(),decimals:a.find("[data-type=decimals] input").val(),exchange_rate:a.find("[data-type=exchange_rate] input").val(),is_prefix_symbol:a.find("[data-type=is_prefix_symbol] select").val()})})),e}},{key:"handleForm",value:function(){var e=this;$(".swatches-container .swatches-list").sortable(),$("body").on("submit",".main-setting-form",(function(){var t=e.exportData();$("#currencies").val(JSON.stringify(t)),$("#deleted_currencies").val(JSON.stringify(e.deletedItems))})).on("click",".js-add-new-attribute",(function(t){t.preventDefault(),e.addNewAttribute()})).on("click",".swatches-container .swatches-list li .remove-item a",(function(t){t.preventDefault();var i=$(t.currentTarget).closest("li");e.deletedItems.push(i.data("id")),i.remove()}))}}])&&t(i.prototype,a),r&&t(i,r),Object.defineProperty(i,"prototype",{writable:!1}),e}();$(window).on("load",(function(){new a}))})();