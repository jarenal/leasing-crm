define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		require("compiled/common");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    var moment = require("moment.min");
	    var static_data = require("static_data");
	    var dust = require("dust.core");
	    var ChangePasswordModel = require('app/model/ChangePasswordModel');
	    var ko = require("knockout");
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

        $('#table-javascript').bootstrapTable({
            method: 'get',
            url: Routing.generate('api_get_tickets_by_user', {user: static_data.user.id}),
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

        debug.log('change_password', static_data.change_password);
        if(static_data.change_password)
        {
        	var viewModel = new ChangePasswordModel();
        	viewModel.fields.id(static_data.user.id);

        	$('#modal-message').html('<div id="change-password-container"></div>');
			dust.render("common/view/modal_change_password", {}, function(err, out) {
				ko.cleanNode(document.getElementById('change-password-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#change-password-container').html(out);
			  	$('#modal-message').modal({backdrop: 'static'});

				ko.applyBindings(viewModel, document.getElementById('change-password-container'));

			    // On save change password
			    $(document).off("click", "#btn-save-password").on("click", "#btn-save-password", function(e){
			    	e.preventDefault();
			    	debug.log("Click on save password!!!");

			    	viewModel.save(function(result, textStatus, jqXHR){
			    		try
			    		{
			    			debug.log('result', result);
			    			debug.log('textStatus', textStatus);
			    			debug.log('jqXHR', jqXHR);

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							ko.cleanNode(document.getElementById('change-password-container'));
							$('#modal-message').modal('hide').html('');
							viewModel.reset();


			    		}
			    		catch(ex)
			    		{
							error_handler.display(ex, $('#modal-alert-messages'));
			    		}
			    	});
			    });	// end btn-save-password

			});
        }

	});
});
