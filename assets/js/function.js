(function($){
	"use strict";
	function waitingUI(target){
		target.block({ message: '<img src="' + ecu_ajax_object.plugin_url + '/assets/images/loading.gif" />' });  
	}
	function hidewaitingUI(target){
		target.unblock();
	}
	$(document).ready(function(){

		//Search button clicked
		$(".ecu-filter-button").on("click",function(){
			var parent = $("#" + $(this).attr("parent"));
			var make = parent.find(".ecu-make").children("option:selected").attr("slug");
			var model = parent.find(".ecu-model").children("option:selected").attr("slug");
			var engine = parent.find(".ecu-engine").children("option:selected").attr("slug");
			var year = parent.find(".ecu-year").children("option:selected").attr("slug");
			var new_link = ecu_ajax_object.site_url + "/product-category";
			if(make !== ""){
				new_link += "/" + make;
				if(model !== ""){
					new_link += "/" + model;
					if(engine !== ""){
						new_link += "/" + engine;
						if(year !== ""){
							new_link += "/" + year;
						}
					}
				}
				window.location.href = new_link;
			}

		});
		//Make/Model/Engine/Year change
		$(".ecu-make, .ecu-model, .ecu-year, .ecu-engine").on("change", function(){
			var parent = $("#" + $(this).attr("parent"));
			var selector_model = parent.find(".ecu-model");
			var selector_make = parent.find(".ecu-make");
			var selector_year = parent.find(".ecu-year");
			var selector_engine = parent.find(".ecu-engine");
			var id = parseInt($(this).val());
			var selector = null;

			var default_value = '';
			switch ($(this).attr("name")){
				case 'ecu-make':
					default_value = "Select Model";
					selector = selector_model;
					selector_engine.html("<option slug='' value='0'>Select Engine</option>\n");
					selector_year.html("<option slug='' value='0'>Select Year</option>\n");
					if(id===0){
						selector_model.html("<option slug='' value='0'>" + default_value + "</option>\n");
						return;
					}
					break;
				case 'ecu-model':
					default_value = "Select Engine";
					selector = selector_engine;
					selector_year.html("<option slug='' value='0'>Select Year</option>\n");
					if(id===0){
						selector_engine.html("<option slug='' value='0'>" + default_value + "</option>\n");
						selector_engine.trigger("change");
						return;
					}
					break;
				case 'ecu-engine':
					default_value = "Select Year";
					selector = selector_year;
					if(id===0){
						selector_year.html("<option slug='' value='0'>" + default_value + "</option>\n");
						selector_year.trigger("change");
						return;
					}
					break;
				case 'ecu-year':
					selector_year.removeClass("selected");
					parent.find('.ecu-mmy-filter-apply button').focus();
					return;
					break;
				default:
			}
			if(id===0){
				return; //do nothing when nothing selected
			}
			waitingUI(selector.parent());
			$.ajax({
				url: ecu_ajax_object.ajax_url,
				type: "POST",
				contentType: "application/x-www-form-urlencoded; charset=UTF-8",
				data: {
					action: "change_selector",
					parent: id,
				},
				success: function(data){
					var info = JSON.parse(data);
					var elements = "<option slug='' value='0'>" + default_value + "</option>\n";
					info.forEach(function(element){
						elements += `<option slug=${element.slug} value=${element.term_id}>${element.name}</option>\n`;
					});
					selector.html(elements);
					$(".ecu-mmy-filter-selector").each(function(){
						$(this).removeClass("selected");
					});
					selector.addClass("selected");
					hidewaitingUI(selector.parent());
				},
				error: function(jqXHR, exception) {
					console.log("Failed");
					hidewaitingUI(selector.parent());
				}
			});
		});
	});
})(jQuery);