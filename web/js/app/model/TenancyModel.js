define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function TenancyModel(){
		debug.log("######### TenancyModel: Start");

		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.token = ko.observable("");
		self.fields.start_date = ko.observable("").extend({required: {message: "The start date is required."}, logChange: self});
		self.fields.end_date = ko.observable("").extend({required: {message: "The end date is required."}, logChange: self});
		self.fields.review_date = ko.observable("");
		self.fields.tenancy_type = ko.observable("").extend({required: {message: "The tenancy type is required."}, logChange: self});

		self.fields.tenantsWidgetStatus = ko.computed(function(newValue){

			var enabled;

			switch(self.fields.tenancy_type())
			{
				case "Single":
				case "Shared":
					if(self.fields.tenants().length == 1)
						enabled=false;
					else
						enabled=true;
				break;
				case "Joint":
					enabled=true;
				break;
				default:
					enabled=false;
				break;
			}

			debug.log(enabled, "¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__tenantsWidgetStatus(): enabled");
			return enabled;
		});

		self.fields.tenantsWidgetStatus.subscribe(function(enabled){
			debug.log(enabled, "¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__ subscribe() -> tenantsWidgetStatus: enabled");
			var $parent =$('#search_tenant_combobox').parents('.custom-combobox');
			debug.log($parent, "¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__ subscribe() -> $parent");
			var $button =$parent.find('button');
			debug.log($button, "¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__¬¬__ subscribe() -> $button");

			if(enabled)
			{
				if('autocomplete' in $( "#search_tenant_combobox" ))
				{
					$( "#search_tenant_combobox" ).autocomplete( "enable" ).removeAttr("disabled");
					$button.removeAttr("disabled");
				}
			}
			else
			{
				if('autocomplete' in $( "#search_tenant_combobox" ))
				{
					$( "#search_tenant_combobox" ).autocomplete( "disable" ).attr("disabled","disabled").val("");
					$button.attr("disabled", "disabled");
				}
			}
		});


		self.fields.property = ko.observable("").extend({required: {message: "The property is required."}, logChange: self});

		self.fields.tenants = ko.observableArray([]).extend({required: {message: "The tenant or tenants are required."}, logChange: self});
		self.fields.search_tenant = ko.observable("");
		self.fields.emptySearch = function(){
			if(self.fields.search_tenant())
				return false;
			else
				return true;
		};

		self.fields.tenancy_agreement_file = ko.observable("").extend({required: {message: "The tenancy agreement file is required."}, logChange: self, myInputFile: self});
		self.fields.tenancy_agreement_visual_file = ko.observable("").extend({logChange: self, myInputFile: self});
		self.fields.service_level_agreement_file = ko.observable("").extend({myInputFile: self});
		self.fields.owner = ko.observable("").extend({required: {message: "The owner is required."}, logChange: self});
		self.fields.created_by = ko.observable("");
		self.fields.created_at = ko.observable("");
		self.fields.updated_by = ko.observable("");
		self.fields.updated_at = ko.observable("");
		self.fields.isModified = ko.observable(false); // for to manage if the form was modified or no.

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		self.fields.formHasError = function(){
			debug.log("formHasError was executed!");
			if(self.fields.isModified())
			{
				debug.log("formHasError(): The form was modified");

				var result;

				_.each(self.fields, function(field, key, list){
					if(_.has(field, "hasError"))
					{
						if(typeof result === 'undefined')
						{
							debug.log(field, "formHasError(): checking if '"+key+"' has an error...");
							if((field.hasError() === true) || (typeof field.hasError() === 'undefined'))
							{
								debug.log("~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Field "+key+" has an error ~~~~~~~~~~~~~~~~~");
								result = true;
							}
						}
					}

				});

				if(typeof result === 'undefined')
				{
					debug.log(result, "2-formHasError: 'result' is undefined so we return false");
					return false;
				}
				else{
					debug.log(result, "3-formHasError: 'result' in rest of cases we return true");
					return true;
				}
			}
			else
			{
				debug.log(true, "formHasError(): The form was not modified yet.");
				return true;
			}

		};

		debug.log("######### TenancyModel: End");
	}

	TenancyModel.prototype = new BaseModel();

	TenancyModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		var formData = new FormData();

		/************ Version para soportar ficheros */
		_.each(self.fields, function(field, key, list){
			debug.log(field, "key in tenancy["+key+"]");

			if('selected' in field)
			{
				debug.log('We have selected property in tenancy['+key+']');
				formData.append('tenancy['+key+']', field.selected());
			}
			else if(key=='tenants')
			{
				_.each(field(), function(tenant, key4, list4){
					formData.append('tenancy['+key+'][]', tenant.id);
				});
			}
			else
			{
				formData.append('tenancy['+key+']', field());
			}

		});

		if(self.fields.id()) // put
			self.api("api_post_tenancy_update", "POST", formData, success, error, {id: self.fields.id()}, true);
		else // post
			self.api("api_post_tenancy", "POST", formData, success, error, null, true);
	};

	TenancyModel.prototype.delete = function(success, error, id){
		var self = this;
		var idrow;

		if(self.fields.id())
		{
			idrow = self.fields.id();
		}
		else
		{
			idrow = id;
		}
		self.api("api_delete_tenancy", "DELETE", {}, success, error, {id: idrow});
	};

	TenancyModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");
	};

	TenancyModel.prototype.deleteFile = function(success, error, token, filename){
		var self = this;
		debug.log(token, "TenancyModel.deleteFile(): token");
		debug.log(filename, "TenancyModel.deleteFile(): filename");
		self.api("api_delete_tenancy_file", "DELETE", {}, success, error, {token: token, filename: filename});
	};

	TenancyModel.prototype.addTenant = function(new_tenant){
		var self = this,item;
		debug.log(new_tenant, "TenancyModel::addTenant: tenant:");
		if(!new_tenant)
		{
			debug.log("Click addTenant!");
			item = self.fields.search_tenant();

			self.removeTenant(item);
		}
		else
		{
			item = new_tenant;
		}


		self.fields.tenants.push(item);
		self.fields.search_tenant("");
		if('bootstrapTable' in $('#table-tenants'))
		{
			$('#table-tenants').bootstrapTable('load', self.fields.tenants());
			$("#search_tenant_combobox").val("");
		}
	};

	TenancyModel.prototype.removeTenant = function(item){
		var self = this;
		debug.log(item, 'removeTenant: item to remove');

        for (var i = 0; i < self.fields.tenants().length; i++) {
            if (self.fields.tenants()[i].name == item.name) {
                debug.log(i, 'removeTenant: index to remove');
                self.fields.tenants.splice(i, 1);
            }
            else{
            	debug.log(i, 'removeTenant: index keeped');
            }
        }

        $('#table-tenants').bootstrapTable('load', self.fields.tenants());
	};

	TenancyModel.prototype.setData = function(data){
		var self = this;

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);

			if('id' in data)
				if(data.id)
					self.fields.id(data.id);

			if('token' in data)
			{
				self.fields.token(data.token);
			}

			if('start_date' in data)
				self.fields.start_date(data.start_date);

			if('end_date' in data)
				self.fields.end_date(data.end_date);

			if('review_date' in data)
				self.fields.review_date(data.review_date);

			if('tenancy_type' in data)
				self.fields.tenancy_type(data.tenancy_type);


			if(typeof data.property === 'object')
			{
				self.fields.property(data.property.id);
				self.fields.property.fulltitle = data.property.fulltitle;
			}

			if('tenancy_agreement_file' in data)
			{
				if(data.tenancy_agreement_file){
					self.fields.tenancy_agreement_file.filename(data.tenancy_agreement_file);

					if('tenancy_agreement_file_permalink' in data)
					{
						if(data.tenancy_agreement_file_permalink)
							self.fields.tenancy_agreement_file.permalink(data.tenancy_agreement_file_permalink);
					}
				}
				else
				{
					self.fields.tenancy_agreement_file.hasError(true);
				}
			}

			if('tenancy_agreement_visual_file' in data)
			{
				if(data.tenancy_agreement_visual_file){
					self.fields.tenancy_agreement_visual_file.filename(data.tenancy_agreement_visual_file);

					if('tenancy_agreement_visual_file_permalink' in data)
					{
						if(data.tenancy_agreement_visual_file_permalink)
							self.fields.tenancy_agreement_visual_file.permalink(data.tenancy_agreement_visual_file_permalink);
					}
				}
			}

			if('service_level_agreement_file' in data)
			{
				if(data.service_level_agreement_file){
					self.fields.service_level_agreement_file.filename(data.service_level_agreement_file);

					if('service_level_agreement_file_permalink' in data)
					{
						if(data.service_level_agreement_file_permalink)
							self.fields.service_level_agreement_file.permalink(data.service_level_agreement_file_permalink);
					}
				}
			}

			if(typeof data.owner === 'object')
			{
				self.fields.owner(data.owner.id);
				self.fields.owner.fullname = data.owner.fullname;
			}

			if(data.created_by && data.created_at)
			{
				self.fields.created_by(data.created_by.name);
				var created_at = new Date(data.created_at);
				self.fields.created_at(created_at.toLocaleString("en-GB"));
			}

			if(data.updated_by && data.updated_at)
			{
				self.fields.updated_by(data.updated_by.name);
				var updated_at = new Date(data.updated_at);
				self.fields.updated_at(updated_at.toLocaleString("en-GB"));
			}

			if('tenants' in data)
			{
				if(data.tenants.length > 0)
				{
					_.each(data.tenants, function(tenant, key, list){
						debug.log('push new tenant:', tenant.id);
						var temp_tenant = {};
						temp_tenant.id = tenant.id;
						temp_tenant.name = tenant.fullname;
						self.addTenant(temp_tenant);
					}, this);
				}
			}

			self.fields.isModified(false);
		}
	};

	return TenancyModel;
});
