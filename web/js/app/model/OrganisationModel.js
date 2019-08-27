define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function OrganisationModel(data){
		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.name = ko.observable("").extend({logChange: self});
		self.fields.phone = ko.observable("").extend({logChange: self});
		self.fields.email = ko.observable("");
		self.fields.website = ko.observable("");
		self.fields.address = ko.observable("");
		self.fields.postcode = ko.observable("");
		self.fields.town = ko.observable("");
		self.fields.comments = ko.observable("");
		self.fields.organisation_type = ko.observable("");
		self.fields.organisation_type_name = ko.observable("");

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));


		if(typeof data === 'object')
		{
			debug.log('we have data', data);
			self.fields.id(data.id);
			self.fields.name(data.name);
			self.fields.phone(data.phone);
			self.fields.email(data.email);
			self.fields.website(data.website);
			self.fields.address(data.address);
			self.fields.postcode(data.postcode);
			self.fields.town(data.town);
			self.fields.comments(data.comments);

			if(typeof data.organisation_type === 'object')
			{
				self.fields.organisation_type(data.organisation_type.id);
				self.fields.organisation_type_name(data.organisation_type.name);
			}

			// Administration
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

	OrganisationModel.prototype = new BaseModel();

	OrganisationModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');

		if(self.fields.id()) // put
		{
			self.api("api_put_organisation", "PUT", {organisation: ko.toJS(self.fields)}, success, error, {idorganisation: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_organisation", "POST", {organisation: ko.toJS(self.fields)}, success, error);
		}
	};

	OrganisationModel.prototype.reset = function(){
		var self = this;
		self.fields.id("");
		self.fields.name("");
		self.fields.phone("");
		self.fields.email("");
		self.fields.website("");
		self.fields.address("");
		self.fields.postcode("");
		self.fields.town("");
		self.fields.organisation_type("");
	};

	OrganisationModel.prototype.delete = function(success, error, id){
		var self = this;
		var idorganisation;

		if(self.fields.id())
		{
			idorganisation = self.fields.id();
		}
		else
		{
			idorganisation = id;
		}
		self.api("api_delete_organisation", "DELETE", {}, success, error, {idorganisation: idorganisation});
	};

	return OrganisationModel;
});
