define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function OtherModel(data){

		var self = this;
		self.fields = {};

		self.fields.id = ko.observable("");
		self.fields.contact_type = ko.observable("4");

		// Basic details
		self.fields.name = ko.observable("").extend({logChange: self, isUniqueFullname: self});
		self.fields.surname = ko.observable("").extend({logChange: self, isUniqueFullname: self});
		self.fields.contact_title = ko.observable("");
		self.fields.organisation = ko.observable("");

		// Contact details
		self.fields.email = ko.observable("").extend({logChange: self, isUniqueEmail:self});
		self.fields.landline = ko.observable("");
		self.fields.mobile = ko.observable("");
		self.fields.contact_method = ko.observable("");
		self.fields.contact_method_other = ko.observable("");

		// Address
		self.fields.address = ko.observable("");
		self.fields.postcode = ko.observable("");
		self.fields.town = ko.observable("");

		// Miscellaneous
		self.fields.job_title = ko.observable("");
		self.fields.other_type = ko.observable("");
		self.fields.comments = ko.observable("");

		// Administration
		self.fields.contact_status = ko.observable("8");

		self.fields.organisation.subscribe(function(newValue){
			debug.log(newValue, 'OtherModel organisation newValue ++=======================');
		});

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);
			self.fields.id(data.id);

			// Organisation
			if(typeof data.organisation == 'object')
			{
				this.fields.organisation(data.organisation.id);
			}

			// Basic details
			self.fields.contact_title(data.contact_title.id);
			self.fields.name(data.name);
			self.fields.surname(data.surname);

			// Contact details
			self.fields.email(data.email);
			self.fields.landline(data.landline);
			self.fields.mobile(data.mobile);
			self.fields.contact_method(data.contact_method?data.contact_method.id:"");
			self.fields.contact_method_other(data.contact_method_other);

			// Address
			self.fields.address(data.address);
			self.fields.postcode(data.postcode);
			self.fields.town(data.town);

			// Miscellaneous
			self.fields.job_title(data.job_title);
			if(typeof data.other_type=='object')
			{
				self.fields.other_type(data.other_type.id);
			}

			self.fields.comments(data.comments);

			// Administration
			self.fields.contact_status(data.contact_status.id);

			self.fields.created_by = ko.computed(function(){
				return data.created_by ? data.created_by.name : "";
			}, this);

			self.fields.updated_by = ko.computed(function(){
				return data.updated_by ? data.updated_by.name : "";
			}, this);

			self.fields.created_at = ko.computed(function(){
				var created_at = new Date(data.created_at);
				return created_at.toLocaleString("en-GB");
			}, this);

			self.fields.updated_at = ko.computed(function(){
				var updated_at = new Date(data.updated_at);
				return updated_at.toLocaleString("en-GB");
			}, this);


		}
	}

	OtherModel.prototype = new BaseModel();

	OtherModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		if(self.fields.id()) // put
		{
			self.api("api_put_other", "PUT", {other: ko.toJS(self.fields)}, success, error, {idcontact: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_other", "POST", {other: ko.toJS(self.fields)}, success, error);
		}
	};

	OtherModel.prototype.delete = function(success, error, id){
		var self = this;
		var idcontact;

		if(self.fields.id())
		{
			idcontact = self.fields.id();
		}
		else
		{
			idcontact = id;
		}
		self.api("api_delete_other", "DELETE", {}, success, error, {idcontact: idcontact});
	};

	OtherModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");

		// Basic details
		self.fields.contact_title("");
		self.fields.name("");
		self.fields.surname("");

		// Contact details
		self.fields.email("");
		self.fields.landline("");
		self.fields.mobile("");

		// Address
		self.fields.address("");
		self.fields.postcode("");
		self.fields.town("");

		// Miscellaneous
		self.fields.job_title("");
		self.fields.other_type("");
		self.fields.comments("");

		// Administration
		self.fields.contact_status("");
	};

	return OtherModel;
});
