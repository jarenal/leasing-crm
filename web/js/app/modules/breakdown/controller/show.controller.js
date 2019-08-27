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
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		breakdown = new BreakdownModel();

		ko.applyBindings(breakdown, document.getElementById('fields-container'));

		if(typeof static_data.breakdown === 'object')
		{
			breakdown.setData(static_data.breakdown);
		}

		breakdown.readyForModifications();

		$('#loading-modal').hide();

		$('[data-toggle="tooltip"]').tooltip()
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
