define(function(require, exports, module){
	"use strict";

	return function(){
		var debug = require("debug");
		var static_data = require("static_data");
		debug.log('static_data', static_data);
		require("compiled/common");
		require("compiled/contacts");
		require("checkbox");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var OtherModel = require("app/model/OtherModel");
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var contact;

		/*******************************************************/
		/*** CONTACT EVENTS ************************************/
		/*******************************************************/

	    // On save Other contact
	    $(document).off("click", "#btn-save-contact").on("click", "#btn-save-contact", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save contact!!!");

	    	contact.save(function(result, textStatus, jqXHR){
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
							// Save event for show alert in contact index screen
							sessionStorage.setItem("contact_updated", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							sessionStorage.setItem("contact_created", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_contact_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-contact

		// On choose contact method
		function onChangeContactMethod(e)
		{
			debug.log('On choose contact_method');
			var idmethod = parseInt($('#contact_method').val());

			if(idmethod==4)
			{
				$('#contact_method_other_row').slideDown();
			}
			else
			{
				$('#contact_method_other_row').slideUp();
			}
		}

		$(document).off('change', '#contact_method').on('change', '#contact_method', onChangeContactMethod);

		/*******************************************************/
		/*** END CONTACT EVENTS ********************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.contact === 'object')
		{
			contact = new OtherModel(static_data.contact);
		}
		else
		{
			contact = new OtherModel();
		}

		debug.log("Loading form_other...");

		// Loading Template
		dust.render("contacts/view/form_other", {contact_statuses: static_data.contact_statuses[4],
														contact_titles: static_data.contact_titles,
														other_types: static_data.other_types,
														contact_methods: static_data.contact_methods,
												},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(contact, document.getElementById('fields-container'));

				// Seleccionamos la organizacion (solo en vista edit)
				if(static_data.view==='edit')
				{
					if(typeof static_data.contact.organisation == 'object')
					{
						if(static_data.contact.organisation.name)
						{
							$(".custom-combobox-input").val(static_data.contact.organisation.name);
						}
					}
				}

				contact.readyForModifications();

		});

		onChangeContactMethod();

		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	};

});
