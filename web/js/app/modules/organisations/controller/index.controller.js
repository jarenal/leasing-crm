define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		require("compiled/common");
		require("compiled/organisations");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		var PropertyModel = require("app/model/OrganisationModel");
		var _ = require("underscore");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");

	    function operateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-organisation"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-organisation"><i class="glyphicon glyphicon-pencil"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-remove-organisation"><i class="glyphicon glyphicon-remove"></i></a>',
				'</div>'
	        ].join('');
	    }

	    window.operateEvents = {
	        'click .btn-show-organisation': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_organisation_show", {id: row.id}));
	        },
	        'click .btn-edit-organisation': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_organisation_edit", {id: row.id}));
	        },
	        'click .btn-remove-organisation': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("organisations/view/modal_box_delete_organisation_confirmation", {title: "Attention.", name: row.name, idorganisation: row.id}, function(err, out) {
					debug.log(out, 'out modal_box_delete_organisation_confirmation');
					$('#modal-message').html(out).modal({});

				});
	        },
	    };

	    var route_organisations_index = Routing.generate('app_backend_organisation_index');
	    debug.log(route_organisations_index, "route_organisations_index");

	    var organisation_created = sessionStorage.getItem("organisation_created") || 0;
	    var organisation_updated = sessionStorage.getItem("organisation_updated") || 0;

	    if(parseInt(organisation_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The organisation was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("organisation_created", 0);
				});
			});
		}

	    if(parseInt(organisation_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The organisation was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("organisation_updated", 0);
				});
			});
		}

	    if(path == route_organisations_index || path+'/' == route_organisations_index)
	    {
			            $('#table-javascript').bootstrapTable({
			                method: 'get',
			                url: Routing.generate('api_get_organisations'),
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
			                    field: 'name',
			                    title: 'Name',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'organisation_type',
			                    title: 'Type',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'phone',
			                    title: 'Phone',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'email',
			                    title: 'Email',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'address',
			                    title: 'Address',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
			                }, {
			                    field: 'postcode',
			                    title: 'Postcode',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
			                }, {
			                    field: 'town',
			                    title: 'Town',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
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

	    $(document).off('click', '#btn-delete-organisation').on('click', '#btn-delete-organisation', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idorganisation = $(this).data('idorganisation');
	    	debug.log('idorganisation', idorganisation);

    		var organisation = new PropertyModel();
    		organisation.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

	    			debug.log('remove organisation id: ', idorganisation);
	    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idorganisation]});

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					dust.render("common/view/alert_messages", {message: "Congrats! The organisation was deleted successfully", type: 'success'}, function(err, out) {
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
			}, undefined, idorganisation);
	    });

	});
});
