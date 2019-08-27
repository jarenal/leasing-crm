define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		require("compiled/common");
		require("compiled/tickets");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var TicketModel = require("app/model/TicketModel");
		var _ = require("underscore");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    var moment = require("moment.min");
	    require("misc_functions");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-ticket"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-ticket"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-ticket"><i class="glyphicon glyphicon-remove"></i></a>',
				'</div>'
	        ].join('');
	    }

	    function dateAndTimeFormatter(value, row, index){
	    	if(value)
	    	{
		    	return moment(value, moment.ISO_8601).format("DD/MM/YYYY HH:mm");
	    	}
	    	else
	    	{
	    		return "N/A";
	    	}
	    }

	    function dateFormatter(value, row, index){
	    	if(value)
	    	{
		    	return moment(value, moment.ISO_8601).format("DD/MM/YYYY");
	    	}
	    	else
	    	{
	    		return "N/A";
	    	}
	    }

	    var operateEvents = {
	        'click .btn-show-ticket': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_ticket_show", {id: row.id}));
	        },
	        'click .btn-edit-ticket': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_ticket_edit", {id: row.id}));
	        },
	        'click .btn-remove-ticket': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("tickets/view/modal_box_delete_ticket_confirmation", {title: "Attention.", name: row.title, idticket: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_ticket_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	    };

	    var route_tickets_index = Routing.generate('app_backend_ticket_index');
	    debug.log(route_tickets_index, "route_tickets_index");

	    var ticket_created = sessionStorage.getItem("ticket_created") || 0;
	    var ticket_updated = sessionStorage.getItem("ticket_updated") || 0;

	    if(parseInt(ticket_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The task was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("ticket_created", 0);
				});
			});
		}

	    if(parseInt(ticket_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The task was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("ticket_updated", 0);
				});
			});
		}

	    if(path == route_tickets_index || path+'/' == route_tickets_index)
	    {
		    $('#table-javascript').bootstrapTable({
		        method: 'get',
		        url: Routing.generate('api_get_tickets'),
		        cache: false,
		        height: 'auto',
		        striped: false,
		        pagination: true,
		        pageSize: 50,
		        pageList: [10, 25, 50, 100, 200],
		        search: true,
		        showColumns: true,
		        showRefresh: true,
		        minimumCountColumns: 2,
		        clickToSelect: false,
		        rowStyle: tasksRowStyles,
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
		            field: 'title',
		            title: 'Task title',
		            align: 'center',
		            valign: 'middle',
		            sortable: true,
		            visible: true,
		        }, {
		            field: 'createdAt',
		            title: 'Created at',
		            align: 'center',
		            valign: 'middle',
		            sortable: true,
		            visible: true,
		            formatter: dateAndTimeFormatter,
		        }, {
		            field: 'duedateAt',
		            title: 'Due date',
		            align: 'center',
		            valign: 'middle',
		            sortable: true,
		            visible: true,
		            formatter: dateFormatter,
		        },  {
		            field: 'status_name',
		            title: 'Status',
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
		            formatter: operateFormatter,
		            events: operateEvents
				}]
		    }).on('load-success.bs.table', function(e, data){
		    	debug.log('....................................data table loaded: now...', Date.now());
		    	$('#loading-modal').hide();
		    });

	    }

	    $(document).off('click', '#btn-delete-ticket').on('click', '#btn-delete-ticket', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idticket = $(this).data('idticket');
	    	debug.log('idticket', idticket);

    		var ticket = new TicketModel();
    		ticket.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove ticket id: ', idticket);
	    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idticket]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The task was deleted successfully", type: 'success'}, function(err, out) {
						$('#alert-messages').html(out);
						$('#alert-messages').slideDown(500, function(){
							$('#alert-messages').delay(5000).slideUp(500);
						});
					});
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
			}, undefined, idticket);
	    });

	});
});
