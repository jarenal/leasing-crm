define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/breakdown");
		var $ = require("jquery");
		require("jquery_ui");
		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var bootstrap_table = require("bootstrap_table");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		require("custom-binding-handlers");
		var BreakdownModel = require("app/model/BreakdownModel");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var breakdown;

		/*******************************************************/
		/*** TENANCY EVENTS *************************************/
		/*******************************************************/

	    // On save Breakdown
	    $(document).off("click", "#btn-save-breakdown").on("click", "#btn-save-breakdown", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save breakdown!!!");
	    	breakdown.save(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					switch(jqXHR.type)
					{
						case "POST":
							// Save event for show alert in contact index screen
							var regx = /updates/;

							if(regx.test(jqXHR.url))
								sessionStorage.setItem("breakdown_updated", 1);
							else
								sessionStorage.setItem("breakdown_created", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							//sessionStorage.setItem("contact_updated", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_breakdown_index", {tenancy: static_data.breakdown.tenancy.id}));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-tenancy

	    /*******************************************************/
	    /*** END TENANCY EVENTS ************************/
	    /*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		breakdown = new BreakdownModel();

		if(static_data.view == 'new')
		{
			breakdown.setItems(static_data.items);
		}

		ko.applyBindings(breakdown, document.getElementById('fields-container'));

		if(typeof static_data.breakdown === 'object')
		{
			breakdown.setData(static_data.breakdown);
		}

		breakdown.readyForModifications();

		$('#loading-modal').hide();

		$('[data-toggle="tooltip"]').tooltip();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
