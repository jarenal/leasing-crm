define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function LeaseAgreementModel(data){
		debug.log("######### LeaseAgreementModel: Start");

		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.token = ko.observable("");
		self.fields.property = ko.observable("").extend({required: {message: "The property is required."}, logChange: self});
		self.fields.start_date = ko.observable("").extend({required: {message: "The start date is required."}, logChange: self});
		self.fields.end_date = ko.observable("").extend({required: {message: "The end date is required."}, logChange: self});
		self.fields.review_date = ko.observable("").extend({logChange: self});
		self.fields.core_lease_charge_per_week = ko.observable("").extend({logChange: self});
		self.fields.input_lease_agreement = ko.observable("").extend({required: {message: "The lease agreement file is required."}, logChange: self, myInputFile: self});
		self.fields.input_management_agreement = ko.observable("").extend({required: {message: "The management agreement file is required."}, logChange: self, myInputFile: self});
		self.fields.owner = ko.observable("").extend({required: {message: "The owner is required."}, logChange: self});
		self.fields.isModified = ko.observable(false); // for to manage if the form was modified or no.
		self.fields.created_by = ko.observable("");
		self.fields.created_at = ko.observable("");
		self.fields.updated_by = ko.observable("");
		self.fields.updated_at = ko.observable("");

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

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

			if(typeof data.property === 'object')
			{
				self.fields.property(data.property.id);
				self.fields.property.fulltitle = data.property.fulltitle;
			}

			if('start_date' in data)
				self.fields.start_date(data.start_date);

			if('end_date' in data)
				self.fields.end_date(data.end_date);

			if('review_date' in data)
				self.fields.review_date(data.review_date);

			if('core_lease_charge_per_week' in data)
				self.fields.core_lease_charge_per_week(data.core_lease_charge_per_week);

			if('lease_agreement_file' in data)
			{
				if(data.lease_agreement_file){
					self.fields.input_lease_agreement.filename(data.lease_agreement_file);

					if('lease_agreement_permalink' in data)
					{
						if(data.lease_agreement_permalink)
							self.fields.input_lease_agreement.permalink(data.lease_agreement_permalink);
					}
				}
				else
				{
					self.fields.input_lease_agreement.hasError(true);
				}
			}

			if('management_agreement_file' in data)
			{
				if(data.management_agreement_file){
					self.fields.input_management_agreement.filename(data.management_agreement_file);

					if('lease_management_permalink' in data)
					{
						if(data.lease_management_permalink)
							self.fields.input_management_agreement.permalink(data.lease_management_permalink);
					}
				}
				else
				{
					self.fields.input_management_agreement.hasError(true);
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

		}

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

		debug.log("######### LeaseAgreementModel: End");
	}

	LeaseAgreementModel.prototype = new BaseModel();

	LeaseAgreementModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		var formData = new FormData();

		/************ Version para soportar ficheros */
		_.each(self.fields, function(field, key, list){
			debug.log(field, "key in leaseagreement["+key+"]");

			if('selected' in field)
			{
				debug.log('We have selected property in leaseagreement['+key+']');
				formData.append('leaseagreement['+key+'_file]', field.selected());
			}

			formData.append('leaseagreement['+key+']', field());
		});

		if(self.fields.id()) // put
			self.api("api_post_leaseagreement_update", "POST", formData, success, error, {id: self.fields.id()}, true);
		else // post
			self.api("api_post_leaseagreement", "POST", formData, success, error, null, true);
	};

	LeaseAgreementModel.prototype.delete = function(success, error, id){
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
		self.api("api_delete_leaseagreement", "DELETE", {}, success, error, {id: idrow});
	};

	LeaseAgreementModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");
	};

	LeaseAgreementModel.prototype.deleteFile = function(success, error, token, filename){
		var self = this;
		debug.log(token, "LeaseAgreementModel.deleteFile(): token");
		debug.log(filename, "LeaseAgreementModel.deleteFile(): filename");
		self.api("api_delete_leaseagreement_file", "DELETE", {}, success, error, {token: token, filename: filename});
	};

	return LeaseAgreementModel;
});
