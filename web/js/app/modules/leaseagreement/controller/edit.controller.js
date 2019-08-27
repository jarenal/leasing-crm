define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/leaseagreement");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		require("custom-binding-handlers");
		var LeaseAgreementModel = require("app/model/LeaseAgreementModel");

		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var lease_agreement;

		/*******************************************************/
		/*** LEASE AGREEMENT EVENTS *************************************/
		/*******************************************************/

	    // On save Lease agreement
	    $(document).off("click", "#btn-save-leaseagreement").on("click", "#btn-save-leaseagreement", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save leaseagreement!!!");
	    	lease_agreement.save(function(result, textStatus, jqXHR){
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
								sessionStorage.setItem("leaseagreement_updated", 1);
							else
								sessionStorage.setItem("leaseagreement_created", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							//sessionStorage.setItem("contact_updated", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_leaseagreement_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-leaseagreement

	    /*******************************************************/
	    /*** END LEASE AGREEMENT EVENTS ************************/
	    /*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.lease_agreement === 'object')
		{
			lease_agreement = new LeaseAgreementModel(static_data.lease_agreement);
		}
		else
		{
			lease_agreement = new LeaseAgreementModel();
		}

		ko.applyBindings(lease_agreement, document.getElementById('fields-container'));

		// to fill combox (edit view only)
		if(static_data.view==='edit' || static_data.view==='new')
		{
			if(typeof static_data.lease_agreement == 'object')
			{
				if(typeof static_data.lease_agreement.property == 'object')
				{
					if(static_data.lease_agreement.property.fulltitle)
					{
						$("#leaseagreement_property_combobox").val(static_data.lease_agreement.property.fulltitle);
					}
				}

				if(typeof static_data.lease_agreement.owner == 'object')
				{
					if(static_data.lease_agreement.owner.fullname)
					{
						debug.log('=== we have fullname at owner');
						$("#leaseagreement_owner_combobox").val(static_data.lease_agreement.owner.fullname);
					}
				}
			}
		}

		lease_agreement.readyForModifications();

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
