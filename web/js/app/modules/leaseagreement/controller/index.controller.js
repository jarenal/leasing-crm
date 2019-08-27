define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		var static_data = require("static_data");
		require("compiled/common");
		require("compiled/leaseagreement");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var LeaseAgreementModel = require("app/model/LeaseAgreementModel");
		var _ = require("underscore");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    var moment = require("moment.min");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-leaseagreement"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-leaseagreement"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-leaseagreement"><i class="glyphicon glyphicon-remove"></i></a>',
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
	        'click .btn-show-leaseagreement': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_leaseagreement_show", {id: row.id}));
	        },
	        'click .btn-edit-leaseagreement': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_leaseagreement_edit", {id: row.id}));
	        },
	        'click .btn-remove-leaseagreement': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("leaseagreement/view/modal_box_delete_confirmation", {title: "Attention.", name: row.address+' ('+dateFormatter(row.startDate)+' - '+dateFormatter(row.endDate)+')', id: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	    };

	    var route_index = Routing.generate('app_backend_leaseagreement_index');
	    debug.log(route_index, "route_index");

	    var leaseagreement_created = sessionStorage.getItem("leaseagreement_created") || 0;
	    var leaseagreement_updated = sessionStorage.getItem("leaseagreement_updated") || 0;

	    if(parseInt(leaseagreement_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The lease agreement was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("leaseagreement_created", 0);
				});
			});
		}

	    if(parseInt(leaseagreement_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The lease agreement was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("leaseagreement_updated", 0);
				});
			});
		}

		var route_get_leaseagreements = Routing.generate('api_get_leaseagreements');

		debug.log(static_data, "static_data");
		if(typeof static_data === 'object')
		{
			if(typeof static_data.property === 'object')
			{
				if('id' in static_data.property)
				{
					if(static_data.property.id)
					{
						route_get_leaseagreements = Routing.generate("api_get_leaseagreements_by_property", {property: static_data.property.id});
					}
				}
			}
		}

	    $('#table-javascript').bootstrapTable({
	        method: 'get',
	        url: route_get_leaseagreements,
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
	    });


	    $(document).off('click', '#btn-delete-leaseagreement').on('click', '#btn-delete-leaseagreement', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idrecord = $(this).data('idrecord');
	    	debug.log('idrecord', idrecord);

    		var entity = new LeaseAgreementModel();
    		entity.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove entity id: ', idrecord);
	    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idrecord]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The lease agreement was deleted successfully", type: 'success'}, function(err, out) {
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
