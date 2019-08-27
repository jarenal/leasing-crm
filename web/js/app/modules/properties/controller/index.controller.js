define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		require("compiled/common");
		require("compiled/properties");
		var $ = require("jquery");
		require("jquery_ui");
		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var PropertyModel = require("app/model/PropertyModel");
		var _ = require("underscore");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");
	    require("misc_functions");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-property"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-property"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-property"><i class="glyphicon glyphicon-remove"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-risks-assessments-property" data-toggle="tooltip" title="Set risks assessments"><i class="fa fa-check-square-o"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-lease-agreement-property" data-toggle="tooltip" title="Lease agreements management"><i class="fa fa-wpforms"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-tenancies-property" data-toggle="tooltip" title="Tenancies management"><i class="fa fa-key"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var operateEvents = {
	        'click .btn-show-property': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_property_show", {id: row.id}));
	        },
	        'click .btn-edit-property': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_property_edit", {id: row.id}));
	        },
	        'click .btn-remove-property': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("properties/view/modal_box_delete_property_confirmation", {title: "Attention.", address: row.address, idproperty: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_property_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	        'click .btn-risks-assessments-property': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_property_risksassessments", {id: row.id}));
	        },
	        'click .btn-lease-agreement-property': function (e, value, row, index) {
	        	e.preventDefault();

	        	if(parseInt(row.leases)>0)
	            	location.assign(Routing.generate("app_backend_leaseagreement_index", {property: row.id}));
	            else
	            	location.assign(Routing.generate("app_backend_leaseagreement_new", {property: row.id}));

	        },
	        'click .btn-tenancies-property': function (e, value, row, index) {
	        	e.preventDefault();

	        	if(parseInt(row.tenancies)>0)
	            	location.assign(Routing.generate("app_backend_tenancy_index", {property: row.id}));
	            else
	            	location.assign(Routing.generate("app_backend_tenancy_new", {property: row.id}));

	        }
	    };

	    var route_properties_index = Routing.generate('app_backend_property_index');
	    debug.log(route_properties_index, "route_properties_index");

	    var property_created = sessionStorage.getItem("property_created") || 0;
	    var property_updated = sessionStorage.getItem("property_updated") || 0;

	    if(parseInt(property_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The property was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("property_created", 0);
				});
			});
		}

	    if(parseInt(property_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The property was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("property_updated", 0);
				});
			});
		}

	    $(document).off('click', '#btn-delete-property').on('click', '#btn-delete-property', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idproperty = $(this).data('idproperty');
	    	debug.log('idproperty', idproperty);

    		var property = new PropertyModel();
    		property.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove property id: ', idproperty);
	    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idproperty]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The property was deleted successfully", type: 'success'}, function(err, out) {
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
			}, undefined, idproperty);
	    });

	    if(path == route_properties_index || path+'/' == route_properties_index)
	    {
			$('#table-javascript').bootstrapTable({
			    method: 'get',
			    url: Routing.generate('api_get_properties'),
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
			    rowStyle: propertiesRowStyles,
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
			        title: 'Address',
			        align: 'center',
			        valign: 'middle',
			        sortable: true,
			        visible: true,
			    }, {
			        field: 'postcode',
			        title: 'Postcode',
			        align: 'center',
			        valign: 'middle',
			        sortable: true,
			        visible: true,
			    }, {
			        field: 'status_name',
			        title: 'Status',
			        align: 'center',
			        valign: 'middle',
			        sortable: true,
			        visible: true,
			    },  {
			        field: 'town',
			        title: 'Town',
			        align: 'center',
			        valign: 'middle',
			        sortable: true,
			        visible: true,
			    }, {
			        field: 'landlord_name',
			        title: 'Landlord',
			        align: 'center',
			        valign: 'middle',
			        sortable: true,
			        visible: true,
			    }, {
			        field: 'local_authority_name',
			        title: 'Local authority',
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

	    }


	});
});
