define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");
	require("misc_functions");

	function LandlordModel(data){

		var self = this;

		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.name = ko.observable("").extend({logChange: self, isUniqueFullname: self}); // I use logChange for avoid exit without save
		self.fields.surname = ko.observable("").extend({logChange: self, isUniqueFullname: self});
		self.fields.email = ko.observable("").extend({logChange: self, isUniqueEmail:self});
		self.fields.landline = ko.observable("");
		self.fields.mobile = ko.observable("");
		self.fields.contact_method = ko.observable("");
		self.fields.contact_method_other = ko.observable("");
		self.fields.address = ko.observable("");
		self.fields.postcode = ko.observable("");
		self.fields.town = ko.observable("");
		self.fields.contact_type = ko.observable("1");
		self.fields.contact_title = ko.observable("");
		self.fields.organisation = ko.observable("");
		self.fields.is_investor = ko.observable("0");
		self.fields.investments = ko.observableArray([]);
		self.fields.landlord_accreditation = ko.observable("");
		self.fields.accreditation_references = ko.observable("");
		self.fields.comments = ko.observable("");
		self.fields.contact_status = ko.observable("");

		self.fields.organisation.subscribe(function(newValue){
			debug.log(newValue, 'LandlordMOdel organisation newValue =======================');
		});

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);
			self.fields.id(data.id);
			self.fields.name(data.name);
			self.fields.surname(data.surname);
			self.fields.email(data.email);
			self.fields.landline(data.landline);
			self.fields.mobile(data.mobile);
			self.fields.contact_method(data.contact_method?data.contact_method.id:"");
			self.fields.contact_method_other(data.contact_method_other);
			self.fields.address(data.address);
			self.fields.postcode(data.postcode);
			self.fields.town(data.town);

			if(typeof data.contact_type==='object')
				self.fields.contact_type(data.contact_type.id);

			if(typeof data.contact_title==='object')
				self.fields.contact_title(data.contact_title.id);

			if(typeof data.organisation == 'object')
				self.fields.organisation(data.organisation.id);

			if(data.is_investor===true)
				debug.log('si es investor', data.is_investor?'1':'0');
			else
				debug.log('no es investor', data.is_investor?'1':'0');

			self.fields.is_investor(data.is_investor?'1':'0');

			if(typeof data.landlord_accreditation==='object')
				self.fields.landlord_accreditation(data.landlord_accreditation.id);

			self.fields.accreditation_references(data.accreditation_references);
			self.fields.comments(data.comments);

			if(typeof data.contact_status==='object')
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

	LandlordModel.prototype = new BaseModel();

	LandlordModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');

		if(self.fields.id()) // put
		{
			self.api("api_put_landlord", "PUT", {landlord: ko.toJS(self.fields)}, success, error, {idlandlord: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_landlord", "POST", {landlord: ko.toJS(self.fields)}, success, error);
		}
	};

	LandlordModel.prototype.delete = function(success, error, id){
		var idlandlord;
		var self = this;

		if(self.fields.id())
		{
			idlandlord = self.fields.id();
		}
		else
		{
			idlandlord = id;
		}
		self.api("api_delete_landlord", "DELETE", {}, success, error, {idlandlord: idlandlord});
	};

	LandlordModel.prototype.addInvestment = function(item){
		var self = this;
        if (typeof item === "object")
        {
            self.fields.investments.push(item.investment);
            debug.log('addInvestment: self.fields.investments', self.fields.investments);
        }

	};

	LandlordModel.prototype.removeInvestment = function(token){
		var self = this;
		debug.log('token on LandlordModel.removeInvestment()', token);
        if (token)
        {
        	self.fields.investments.remove(function(element){
        		debug.log('evaluamos... ', element.token());
        		if(element.token() == token)
        		{
        			debug.log('eliminamos...', element.token());
        			return true;
        		}
        		else
        		{
        			debug.log('conservamos...', element.token());
        		}
        	});
        }

	};

	return LandlordModel;

});
