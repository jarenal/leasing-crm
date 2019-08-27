define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/organisations");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var OrganisationModel = require("app/model/OrganisationModel");
		var organisation;

		/*******************************************************/
		/*** ORGANISATION EVENTS *******************************/
		/*******************************************************/

	    // On save Organisation
	    $(document).off("click", "#btn-save-organisation").on("click", "#btn-save-organisation", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save organisation!!!");

	    	organisation.save(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					switch(jqXHR.type)
					{
						case "PUT":
							// Save event for show alert in index screen
							sessionStorage.setItem("organisation_updated", 1);
						break;
						default:
							// Save event for show alert in index screen
							sessionStorage.setItem("organisation_created", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_organisation_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-organisation

		/*******************************************************/
		/*** END ORGANISATION EVENTS ***************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.organisation === 'object')
		{
			organisation = new OrganisationModel(static_data.organisation);
		}
		else
		{
			organisation = new OrganisationModel();
		}

		debug.log("Loading form_organisation...");

		// Loading Template
		dust.render("organisations/view/form_organisation", {static_data: static_data},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(organisation, document.getElementById('fields-container'));

				organisation.readyForModifications();
		});

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
