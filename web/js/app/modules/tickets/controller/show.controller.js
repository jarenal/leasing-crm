define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/tickets");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var TicketModel = require("app/model/TicketModel");
		var bootstrap = require("bootstrap");
		var bootstrap_table = require("bootstrap_table");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var ticket;

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.ticket === 'object')
		{
			ticket = new TicketModel(static_data.ticket);
		}
		else
		{
			ticket = new TicketModel();
		}

		debug.log("Loading show_ticket...");

		// Loading Template
		dust.render("tickets/view/show_ticket", {},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(ticket, document.getElementById('fields-container'));
				ticket.readyForModifications();

		});

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
