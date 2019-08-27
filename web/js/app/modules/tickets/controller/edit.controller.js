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
		var bootstrap_table = require("bootstrap_table");
		require("custom-binding-handlers");
		require("jquery_scrollto");

		/* for to use nesting bindings
		ko.bindingHandlers.stopBinding = {
		    init: function() {
		        return { controlsDescendantBindings: true };
		    }
		};

		ko.virtualElements.allowedBindings.stopBinding = true;
		*/

		/* For to use knockout validation
		require("knockout-app-validation"); */

		var TicketModel = require("app/model/TicketModel");
		var TicketCommentModel = require("app/model/TicketCommentModel");

		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var ticket;

		/*******************************************************/
		/*** TICKET EVENTS *************************************/
		/*******************************************************/

	    // On save Ticket
	    $(document).off("click", "#btn-save-ticket").on("click", "#btn-save-ticket", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save ticket!!!");

	    	ticket.save(function(result, textStatus, jqXHR){
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
							sessionStorage.setItem("ticket_updated", 1);
						break;
						default:
							sessionStorage.setItem("ticket_created", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_ticket_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-ticket

	    // On add update
	    $(document).off('click', '#btn-add-update').on('click', '#btn-add-update', function(e){
	    	e.preventDefault();
	    	debug.log('Click on add update!!');

			$("#modal-add-update").html("");

			var comment = new TicketCommentModel();

			if(typeof static_data === 'object')
			{
				if(typeof static_data.ticket === 'object')
					if('id' in static_data.ticket)
					{
						comment.fields.ticket(static_data.ticket.id);
					}
			}

			dust.render("tickets/view/modal_add_update", {modal_title: 'Add update', static_data: {}}, function(err, out) {
				$('#modal-add-update').html(out).modal();

				var form_modal = $('#modal-add-update .form-modal-popup').get(0);
				// ko binding
				ko.cleanNode(form_modal);
				ko.applyBindings(comment, form_modal);

				// Save sourceEntity
			    $(document).off('click', '#modal-add-update .btn-success').on('click', '#modal-add-update .btn-success', function(e){
			    	e.preventDefault();

			    	comment.save(function(result, textStatus, jqXHR){
			    		//debug.log('result organisation.save()', result);
			    		try
			    		{
							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							// We reset organisation form and come back to search organisation.
							comment.reset();

							$('#modal-add-update').modal('hide');

							ticket.addComment(result.data);

							var $panel = $('div.panel-primary').last();
							$panel.find('.panel-collapse').collapse('show');
							$(document).scrollTo($panel, 800, {offset: {top: -100}, onAfter: function(target, settings){

								target.effect('highlight',600).effect('highlight', 600);
							}});


			    		}
			    		catch(ex)
			    		{
							error_handler.display(ex, $("#alert-modal-messages"), $('#modal-add-update'));
			    		}
			    	});
				});
			});
	    });

	    /*******************************************************/
	    /*** END TICKET EVENTS *******************************/
	    /*******************************************************/

		/*******************************************************/
		/*** RELATED CONTACTS TABLE ****************************/
		/*******************************************************/
		function removeRelatedContactRow(e, value, row, index)
		{
			e.preventDefault();
			debug.log(row, 'removeRelatedContactRow: row to remove');
			ticket.removeRelatedContact(row);
		}

	    function relatedContactsOperateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-remove-related-contact"><i class="glyphicon glyphicon-remove"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var relatedContactsOperateEvents = {
	        'click .btn-remove-related-contact': removeRelatedContactRow,
	    };
		/*******************************************************/
		/*** END RELATED CONTACTS TABLE ************************/
		/*******************************************************/

		/*******************************************************/
		/*** RELATED PROPERTIES TABLE ****************************/
		/*******************************************************/
		function removeRelatedPropertyRow(e, value, row, index)
		{
			e.preventDefault();
			debug.log(row, 'removeRelatedPropertyRow: row to remove');
			ticket.removeRelatedProperty(row);
		}

	    function relatedPropertiesOperateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-remove-related-property"><i class="glyphicon glyphicon-remove"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var relatedPropertiesOperateEvents = {
	        'click .btn-remove-related-property': removeRelatedPropertyRow,
	    };
		/*******************************************************/
		/*** END RELATED PROPERTIES TABLE **********************/
		/*******************************************************/

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

		debug.log("Loading form_ticket...");

		// Loading Template
		dust.render("tickets/view/form_ticket", {ticket_statuses: static_data.ticket_statuses, ticket_types: static_data.ticket_types, ticket_data: static_data.ticket},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(ticket, document.getElementById('fields-container'));

				// to fill combox (edit view only)
				if(static_data.view==='edit')
				{
					if(typeof static_data.ticket == 'object')
					{
						if(typeof static_data.ticket.reported_by == 'object')
						{
							if(static_data.ticket.reported_by.fullname)
							{
								$("#ticket_reported_by_combobox").val(static_data.ticket.reported_by.fullname);
							}
						}

						if(typeof static_data.ticket.assign_to == 'object')
						{
							if(static_data.ticket.assign_to.fullname)
							{
								$("#ticket_assign_to_combobox").val(static_data.ticket.assign_to.fullname);
							}
						}

						if(typeof static_data.ticket.parent == 'object')
						{
							if(static_data.ticket.parent.fulltitle)
							{
								$("#ticket_parent_combobox").val(static_data.ticket.parent.fulltitle);
							}
						}

						$('#ticket_description').attr('disabled', 'disabled');
						$('#ticket_action_needed').attr('disabled', 'disabled');
						$('#ticket_time_spent').attr('disabled', 'disabled');
						$('#ticket_time_spent_unit').attr('disabled', 'disabled');

					}
				}

				$('#table-related-contacts').bootstrapTable({
					data: ticket.fields.relatedContacts(),
	                cache: false,
	                height: 'auto',
	                striped: true,
	                pagination: false,
	                pageSize: 99,
	                search: false,
	                showColumns: false,
	                showRefresh: false,
	                minimumCountColumns: 2,
	                clickToSelect: false,
	                uniqueId: 'id',
	                responseHandler: function(res) {
						return res.data;
					},
	                columns: [{
	                    field: 'id',
	                    title: 'ID',
	                    align: 'right',
	                    valign: 'bottom',
	                    sortable: true,
	                    visible: true,
	                }, {
	                    field: 'name',
	                    title: 'Name',
	                    align: 'center',
	                    valign: 'middle',
	                    sortable: true,
	                    visible: true,
	                }, {
	                    field: 'operate',
	                    title: 'Actions',
	                    align: 'center',
	                    valign: 'middle',
	                    clickToSelect: false,
	                    formatter: relatedContactsOperateFormatter,
	                    events: relatedContactsOperateEvents
        			}]
	            });

				$('#table-related-properties').bootstrapTable({
					data: ticket.fields.relatedProperties(),
	                cache: false,
	                height: 'auto',
	                striped: true,
	                pagination: false,
	                pageSize: 99,
	                search: false,
	                showColumns: false,
	                showRefresh: false,
	                minimumCountColumns: 2,
	                clickToSelect: false,
	                uniqueId: 'id',
	                responseHandler: function(res) {
						return res.data;
					},
	                columns: [{
	                    field: 'id',
	                    title: 'ID',
	                    align: 'right',
	                    valign: 'bottom',
	                    sortable: true,
	                    visible: true,
	                }, {
	                    field: 'name',
	                    title: 'Name',
	                    align: 'center',
	                    valign: 'middle',
	                    sortable: true,
	                    visible: true,
	                }, {
	                    field: 'operate',
	                    title: 'Actions',
	                    align: 'center',
	                    valign: 'middle',
	                    clickToSelect: false,
	                    formatter: relatedPropertiesOperateFormatter,
	                    events: relatedPropertiesOperateEvents
        			}]
	            });

	            ticket.readyForModifications();

		});

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
