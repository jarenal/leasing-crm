define(function(require, exports, module){
	"use strict";

	var $ = require("jquery");
	var fos_js_routes = require("fos_js_routes");
	var error_handler = require("error_handler");
	var ko = require("knockout");
	//var waitingDialog = require("bootstrap-waitingfor");

	function BaseModel(){

	}

	BaseModel.prototype.api = function(route, method, params, success, error, route_params, useFormData){

		var url = Routing.generate(route, route_params?route_params:undefined);
		var error_callback = function(jqXHR, textStatus, errorThrown){
				var ex = {code: jqXHR.status, message: jqXHR.statusText, errors: [{url: jqXHR.url}]};
				error_handler.display(ex, $('#modal-alert-messages'));
		};

		if(typeof error == 'function')
			error_callback = error;

	    $.ajax(url, {
	        type: method,
	        accepts: 'application/json',
	        dataType: 'json',
	        error: error_callback,
	        success: success,
	        cache: false,
	        data: params,
		    beforeSend: function(jqXHR, settings) {
		        jqXHR.url = settings.url;
		        jqXHR.type = settings.type;
		        //$('#loading-modal').show();
		        //waitingDialog.show('Working, wait please...', {dialogSize: 'm', progressType: 'success', idmodal: "working-modal"});
		    },
		    complete: function(jqXHR, textStatus){
		    	//waitingDialog.hide('working-modal');
		    	$('#loading-modal').hide();
		    },
		    processData: useFormData ? false : true,
		    contentType: useFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
	    });

	};

	BaseModel.prototype.isUniqueEmail = function(){
		var self = this;
		debug.log(self, 'BaseModel.prototype.isUniqueEmail: executed!');

		self.api("api_get_contact_by_email",
					"GET",
					{},
					function(result, textStatus, jqXHR){
						try
						{
							debug.log(result, "api_get_contact_by_email: success");

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							if(typeof result.data.contact === 'object')
							{
								if(result.data.contact)
								{
									result.data.contact.url = Routing.generate('app_backend_contact_show', {id: result.data.contact.id});
									dust.render("common/view/modal_contact_exist", {title: "Attention, this email exist!", subtitle: "This email already exist and belong to another contact.", contact: result.data.contact}, function(err, out) {
										if(err)
									  		debug.error(err);

										$('#modal-contact-exist').html(out).modal({});
									});
								}
							}
						}
			    		catch(ex)
			    		{
							error_handler.display(ex, $('#modal-alert-messages'));
			    		}
					},
					function(jqXHR, textStatus, errorThrown){
						debug.log(jqXHR, "api_get_contact_by_email: error!");
						error_handler.display(jqXHR, $('#modal-alert-messages'));
					},
					{email: self.fields.email()}
		);

	};

	BaseModel.prototype.isUniqueFullname = function(){
		var self = this;
		debug.log(self, 'BaseModel.prototype.isUniqueFullname: executed!');

		self.api("api_get_contact_by_fullname",
					"GET",
					{},
					function(result, textStatus, jqXHR){
						try
						{
							debug.log(result, "api_get_contact_by_fullname: success");

							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							if(typeof result.data.contact === 'object')
							{
								if(result.data.contact)
								{
									result.data.contact.url = Routing.generate('app_backend_contact_show', {id: result.data.contact.id});
									dust.render("common/view/modal_contact_exist", {title: "Attention, these name and surname exist!", subtitle: "This name and surname already exist and belong to another contact.", contact: result.data.contact}, function(err, out) {
										if(err)
									  		debug.error(err);

										$('#modal-contact-exist').html(out).modal({});
									});
								}
							}
						}
			    		catch(ex)
			    		{
							error_handler.display(ex, $('#modal-alert-messages'));
			    		}
					},
					function(jqXHR, textStatus, errorThrown){
						debug.log(jqXHR, "api_get_contact_by_fullname: error!");
						error_handler.display(jqXHR, $('#modal-alert-messages'));
					},
					{fullname: self.fields.name()+" "+self.fields.surname()}
		);

	};

	// Common method for all Model for reset modifications during setup (edit view)
	BaseModel.prototype.readyForModifications = function(){
		var self = this;

		if('isModified' in self.fields)
		{
			debug.log('--- readyForModifications');
			self.fields.isModified(false);
		}
	};

	return BaseModel;
});
