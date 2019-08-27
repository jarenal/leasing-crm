define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		var $ = require("jquery");
		//var waitingDialog = require("bootstrap-waitingfor");
		//waitingDialog.hide("loading-modal");
		require("custom-binding-handlers");
		var landlordController = require('app/modules/contacts/controller/_landlord.controller');
		var contractorController = require('app/modules/contacts/controller/_contractor.controller');
		var tenantController = require('app/modules/contacts/controller/_tenant.controller');
		var otherController = require('app/modules/contacts/controller/_other.controller');

		function loadControllerByType(idtype)
		{
			$('#fields-container').html("");

			switch(idtype)
			{
				case 1:
					landlordController();
					break;
				case 2:
					tenantController();
					break;
				case 3:
					contractorController();
					break;
				case 4:
					otherController();
					break;
			}
		}

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// On choose contact type
		$(document).off('change', '#choose_contact_type').on('change', '#choose_contact_type', function(e){
			debug.log('On choose contact type... call to loadControllerByType()');
			var idtype = parseInt($(this).val());

			loadControllerByType(idtype);

		});

		// Seleccionamos el tipo de contacto (solo en vista edit)
		if(static_data.view==='edit')
		{
			if(parseInt(static_data.contact.contact_type.id)>0)
			{
				debug.log('call to loadControllerByType()');
				loadControllerByType(static_data.contact.contact_type.id);
			}
		}

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
