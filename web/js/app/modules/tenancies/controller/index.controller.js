define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		var static_data = require("static_data");
		require("compiled/common");
		require("compiled/tenancies");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var TenancyModel = require("app/model/TenancyModel");
		var _ = require("underscore");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    var moment = require("moment.min");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-tenancy"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-tenancy"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-tenancy"><i class="glyphicon glyphicon-remove"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-rent-breakdown" data-toggle="tooltip" title="Rent breakdown management"><i class="fa fa-pie-chart"></i></a>',
				'</div>'
	        ].join('');
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
	        'click .btn-show-tenancy': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_tenancy_show", {id: row.id}));
	        },
	        'click .btn-edit-tenancy': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_tenancy_edit", {id: row.id}));
	        },
	        'click .btn-remove-tenancy': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("tenancies/view/modal_box_delete_confirmation", {title: "Attention.", name: '#'+row.id, id: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	        'click .btn-rent-breakdown': function (e, value, row, index) {
	        	e.preventDefault();

	        	if(parseInt(row.total_breakdowns)>0)
	            	location.assign(Routing.generate("app_backend_breakdown_index", {tenancy: row.id}));
	            else
	            	location.assign(Routing.generate("app_backend_breakdown_new", {tenancy: row.id}));
	        },
	    };

	    var route_index = Routing.generate('app_backend_tenancy_index');
	    debug.log(route_index, "route_index");

	    var tenancy_created = sessionStorage.getItem("tenancy_created") || 0;
	    var tenancy_updated = sessionStorage.getItem("tenancy_updated") || 0;

	    if(parseInt(tenancy_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The tenancy was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("tenancy_created", 0);
				});
			});
		}

	    if(parseInt(tenancy_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The tenancy was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("tenancy_updated", 0);
				});
			});
		}

		var route_get_tenancies = Routing.generate('api_get_tenancies');

		debug.log(static_data, "static_data");
		if(typeof static_data === 'object')
		{
			if(typeof static_data.property === 'object')
			{
				if('id' in static_data.property)
				{
					if(static_data.property.id)
					{
						route_get_tenancies = Routing.generate("api_get_tenancies_by_property", {property: static_data.property.id});
					}
				}
			}
		}

	    $('#table-tenancies').bootstrapTable({
	        method: 'get',
	        url: route_get_tenancies,
	        cache: false,
	        height: 'auto',
	        striped: true,
	        pagination: true,
	        pageSize: 50,
	        pageList: [10, 25, 50, 100, 200],
	        search: true,
	        showColumns: true,
	        showRefresh: true,
	        minimumCountColumns: 2,
	        clickToSelect: false,
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
	            field: 'address',
	            title: 'Property',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'startDate',
	            title: 'Start date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'endDate',
	            title: 'End date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        },  {
	            field: 'reviewDate',
	            title: 'Review date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'owner',
	            title: 'Owner',
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
	    	$('[data-toggle="tooltip"]').tooltip({container: 'body', placement: 'auto'});
	    });

	    $(document).off('click', '#btn-delete-tenancy').on('click', '#btn-delete-tenancy', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idrecord = $(this).data('idrecord');
	    	debug.log('idrecord', idrecord);

    		var entity = new TenancyModel();
    		entity.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove entity id: ', idrecord);
	    			$('#table-tenancies').bootstrapTable('remove', {field: 'id', values: [idrecord]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The tenancy was deleted successfully", type: 'success'}, function(err, out) {
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
			}, undefined, idrecord);
	    });

	});
});
