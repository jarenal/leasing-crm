define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		require("compiled/common");
		require("compiled/contacts");
		var $ = require("jquery");
		var bootstrap = require("bootstrap");
		var fos_js_routes = require("fos_js_routes");
		//var ContactModel = require("app/model/ContactModel");
		var LandlordModel = require("app/model/LandlordModel");
		var ContractorModel = require("app/model/ContractorModel");
		var TenantModel = require("app/model/TenantModel");
		var OtherModel = require("app/model/OtherModel");
		var _ = require("underscore");
		//var waitingDialog = require("bootstrap-waitingfor");
	    var path = window.location.pathname;
	    debug.log(path, "path");
	    var bootstrap_table = require("bootstrap_table");
	    var error_handler = require("error_handler");

	    function operateFormatter(value, row, index) {
	    	var template = [];

	    	template.push('<div class="btn-toolbar">');
	    	template.push('<a href="#" class="btn btn-default btn-xs btn-show-contact"><i class="glyphicon glyphicon-eye-open"></i></a>');
	    	template.push('<a href="#" class="btn btn-default btn-xs btn-edit-contact"><i class="glyphicon glyphicon-pencil"></i></a>');
	    	template.push('<a href="#" class="btn btn-default btn-xs btn-remove-contact"><i class="glyphicon glyphicon-remove"></i></a>');
	    	if(row.contact_type_id==2 || row.contact_type_id==1)
	    		template.push('<a href="#" class="btn btn-default btn-xs btn-risks-assessments-contact" data-toggle="tooltip" title="Set risks assessments"><i class="fa fa-check-square-o"></i></a>');
	    	template.push('</div>');

	        return template.join('');
	    }

	    window.operateEvents = {
	        'click .btn-show-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_contact_show", {id: row.id}));
	        },
	        'click .btn-edit-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_contact_edit", {id: row.id}));
	        },
	        'click .btn-remove-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            debug.log('row to delte: ', row);

				$("#modal-message").html("");
				dust.render("contacts/view/modal_box_delete_confirmation", {title: "Attention.", fullname: row.fullname, idcontact: row.id, contact_type_id: row.contact_type_id}, function(err, out) {
					$('#modal-message').html(out).modal({});

				});
	        },
	        'click .btn-risks-assessments-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_contact_risksassessments", {id: row.id}));
	        },
	    };

	    var route_contacts_index = Routing.generate('app_backend_contact_index');
	    debug.log(route_contacts_index, "route_contacts_index");

	    var contact_created = sessionStorage.getItem("contact_created") || 0;
	    var contact_updated = sessionStorage.getItem("contact_updated") || 0;

	    if(parseInt(contact_created))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The contact was created successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("contact_created", 0);
				});
			});
		}
	    if(parseInt(contact_updated))
		{
			dust.render("common/view/alert_messages", {message: "Congrats! The contact was updated successfully", type: 'success'}, function(err, out) {
				$('#alert-messages').html(out);
				$('#alert-messages').slideDown(500, function(){
					$('#alert-messages').delay(5000).slideUp(500);
					sessionStorage.setItem("contact_updated", 0);
				});
			});
		}


	    if(path == route_contacts_index || path+'/' == route_contacts_index)
	    {
			            $('#table-javascript').bootstrapTable({
			                method: 'get',
			                url: Routing.generate('api_get_contacts'),
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
			                    field: 'surname',
			                    title: 'Surname',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'fullname',
			                    title: 'Full name',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
			                }, {
			                    field: 'email',
			                    title: 'Email',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'status',
			                    title: 'Status',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'landline',
			                    title: 'Landline',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
			                }, {
			                    field: 'mobile',
			                    title: 'Mobile',
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
			                    field: 'contact_type',
			                    title: 'Type',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: true,
			                }, {
			                    field: 'contact_title',
			                    title: 'Title',
			                    align: 'center',
			                    valign: 'middle',
			                    sortable: true,
			                    visible: false,
			                }, {
			                    field: 'organisation',
			                    title: 'Organisation',
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
			            	$('[data-toggle="tooltip"]').tooltip({container: 'body', placement: 'auto'});
			            	//waitingDialog.hide("loading-modal");
			            });

	    }

	    $(document).off('click', '#btn-delete-contact').on('click', '#btn-delete-contact', function(e){
	    	e.preventDefault();

			$('#modal-message').modal('hide');
	    	var idcontact = $(this).data('idcontact');
	    	debug.log('idcontact', idcontact);
	    	var contact_type_id = $(this).data('contact_type_id');
	    	debug.log('contact_type_id', contact_type_id);

            switch(contact_type_id)
            {
            	case 1:
            		var landlord = new LandlordModel();
            		landlord.delete(function(result, textStatus, jqXHR){
			    		try
			    		{
			    			debug.log('result', result);
			    			debug.log('textStatus', textStatus);
			    			debug.log('jqXHR', jqXHR);

			    			debug.log('remove contact id: ', idcontact);
			    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idcontact]});

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							dust.render("common/view/alert_messages", {message: "Congrats! The contact was deleted successfully", type: 'success'}, function(err, out) {
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
    				}, undefined, idcontact);
            	break;
            	case 2:
            		var tenant = new TenantModel();
            		tenant.delete(function(result, textStatus, jqXHR){
			    		try
			    		{
			    			debug.log('result', result);
			    			debug.log('textStatus', textStatus);
			    			debug.log('jqXHR', jqXHR);

			    			debug.log('remove contact id: ', idcontact);
			    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idcontact]});

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							dust.render("common/view/alert_messages", {message: "Congrats! The contact was deleted successfully", type: 'success'}, function(err, out) {
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
    				}, undefined, idcontact);
            	break;
            	case 3:
            		var contractor = new ContractorModel();
            		contractor.delete(function(result, textStatus, jqXHR){
			    		try
			    		{
			    			debug.log('result', result);
			    			debug.log('textStatus', textStatus);
			    			debug.log('jqXHR', jqXHR);

			    			debug.log('remove contact id: ', idcontact);
			    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idcontact]});

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							dust.render("common/view/alert_messages", {message: "Congrats! The contact was deleted successfully", type: 'success'}, function(err, out) {
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
    				}, undefined, idcontact);
            	break;
            	case 4:
            		var contact = new OtherModel();
            		contact.delete(function(result, textStatus, jqXHR){
			    		try
			    		{
			    			debug.log('result', result);
			    			debug.log('textStatus', textStatus);
			    			debug.log('jqXHR', jqXHR);

			    			debug.log('remove contact id: ', idcontact);
			    			$('#table-javascript').bootstrapTable('remove', {field: 'id', values: [idcontact]});

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							dust.render("common/view/alert_messages", {message: "Congrats! The contact was deleted successfully", type: 'success'}, function(err, out) {
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
    				}, undefined, idcontact);
            	break;
            	default:
            		debug.log("TODO not implemented");
            }
	    });

	});
});
