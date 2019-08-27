define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		var static_data = require("static_data");
		require("compiled/common");
		require("compiled/breakdown");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var BreakdownModel = require("app/model/BreakdownModel");
		var _ = require("underscore");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    var moment = require("moment.min");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-breakdown"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-breakdown"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-breakdown"><i class="glyphicon glyphicon-remove"></i></a>',
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
	        'click .btn-show-breakdown': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_breakdown_show", {id: row.id}));
	        },
	        'click .btn-edit-breakdown': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_breakdown_edit", {id: row.id}));
	        },
	        'click .btn-remove-breakdown': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("breakdown/view/modal_box_delete_confirmation", {title: "Attention.", name: '#'+row.id, id: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	    };

	    var breakdown_created = sessionStorage.getItem("breakdown_created") || 0;
	    var breakdown_updated = sessionStorage.getItem("breakdown_updated") || 0;

	    if(parseInt(breakdown_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The rent breakdown was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("breakdown_created", 0);
				});
			});
		}

	    if(parseInt(breakdown_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The rent breakdown was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("breakdown_updated", 0);
				});
			});
		}

		var route_get_breakdown = Routing.generate('api_get_breakdowns');

		debug.log(static_data, "static_data");
		if(typeof static_data === 'object')
		{
			if(typeof static_data.tenancy === 'object')
			{
				if('id' in static_data.tenancy)
				{
					if(static_data.tenancy.id)
					{
						route_get_breakdown = Routing.generate("api_get_breakdowns_by_tenancy", {tenancy: static_data.tenancy.id});
					}
				}
			}
		}

	    $('#table-breakdown').bootstrapTable({
	        method: 'get',
	        url: route_get_breakdown,
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
	            field: 'startDate',
	            title: 'Start date',
	            align: 'center',
	            valign: 'bottom',
	            sortable: true,
	            visible: true,
	            formatter:dateFormatter,
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

	    $(document).off('click', '#btn-delete-breakdown').on('click', '#btn-delete-breakdown', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idrecord = $(this).data('idrecord');
	    	debug.log('idrecord', idrecord);

    		var entity = new BreakdownModel();
    		entity.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove entity id: ', idrecord);
	    			$('#table-breakdown').bootstrapTable('remove', {field: 'id', values: [idrecord]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The rent breakdown was deleted successfully", type: 'success'}, function(err, out) {
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
