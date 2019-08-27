define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");

	function ContactModel(){

		var self = this;

		self.fields = {};

		self.fields.id = ko.observable("");
		self.fields.name = ko.observable("");
		self.fields.surname = ko.observable("");
		self.fields.email = ko.observable("");
		self.fields.landline = ko.observable("");
		self.fields.mobile = ko.observable("");
		self.fields.address = ko.observable("");
		self.fields.postcode = ko.observable("");
		self.fields.town = ko.observable("");
		self.fields.contact_type = ko.observable("");
		self.fields.contact_title = ko.observable("");
		self.fields.organisation = ko.observable("");
	}

	ContactModel.prototype = new BaseModel();

	ContactModel.prototype.getContacts = function(success, error){
		this.api("api_get_contacts", "GET", {}, success, error);
	};

	return ContactModel;	
});
