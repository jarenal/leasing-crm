define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var LeaseAgreementModel = require("app/model/LeaseAgreementModel");
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var lease_agreement;

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

		lease_agreement.readyForModifications();

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
