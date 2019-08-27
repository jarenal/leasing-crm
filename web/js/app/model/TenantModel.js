define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var $ = require("jquery");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function TenantModel(data){
		var self = this;

		self.fields = {};

		self.fields.id = ko.observable("");
		self.fields.contact_type = ko.observable("2");

		// Basic details
		self.fields.contact_title = ko.observable("");
		self.fields.name = ko.observable("").extend({logChange: self, isUniqueFullname: self}); // I use logChange for avoid exit without save
		self.fields.surname = ko.observable("").extend({logChange: self, isUniqueFullname: self});

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

		// General details
		self.fields.birthdate = ko.observable("");
		self.fields.gender = ko.observable("");
		self.fields.marital_status = ko.observable("");
		self.fields.nin = ko.observable("");
		self.fields.local_authority = ko.observable("");
		self.fields.social_services_contact = ko.observable("");

		// Children
		self.fields.children = ko.observableArray([]);

		// Support
		self.fields.need_night_support = ko.observable("0");
		self.fields.nights_support = ko.observableArray([]);
		self.fields.has_chc_budget = ko.observable("0");
		self.fields.support_package_hours = ko.observable("");
		self.fields.conditions = ko.observableArray([]);
		self.fields.others = ko.observableArray([]);
		self.fields.has_agency_support_provider = ko.observable("0");
		self.fields.agency_support_provider = ko.observable("");
		self.fields.contact_support_provider = ko.observable("");

		// Deputy
		self.fields.lack_capacity = ko.observable("0");
		self.fields.deputy = ko.observable("");

		// Housing requirements
		self.fields.housingRegister = ko.observable("0");
		self.fields.moveDate = ko.observable("");
		self.fields.areas = ko.observableArray([]);
		self.fields.outCounty = ko.observable("0");
		self.fields.specialDesignFeatures = ko.observable("");
		self.fields.tenantPersonality = ko.observable("");
		self.fields.willingToShare = ko.observable("0");
		self.fields.requirements = ko.observableArray([]);
		self.fields.parking = ko.observable("15");
		self.fields.garden = ko.observable("19");
		self.fields.garden_details = ko.observable("");
		self.fields.accessible = ko.observable("28");
		self.fields.smoking = ko.observable("30");
		self.fields.pets = ko.observable("32");
		self.fields.parkingFor = ko.observable("0");

		// Tenant history
		self.fields.drugHistorial = ko.observable("0");
		self.fields.drugHistorialDetails = ko.observable("");
		self.fields.sexualOffencesHistorial = ko.observable("0");
		self.fields.sexualOffencesHistorialDetails = ko.observable("");
		self.fields.arsonHistorial = ko.observable("0");
		self.fields.arsonHistorialDetails = ko.observable("");
		self.fields.evictionsHistorial = ko.observable("0");
		self.fields.evictionsHistorialDetails = ko.observable("");

		self.fields.violenceHistorial = ko.observable("0");
		self.fields.violenceHistorialDetails = ko.observable("");
		self.fields.antiSocialHistorial = ko.observable("0");
		self.fields.antiSocialHistorialDetails = ko.observable("");
		self.fields.rentArrearsHistorial = ko.observable("0");
		self.fields.rentArrearsHistorialDetails = ko.observable("");
		self.fields.vulnerabilityHistorial = ko.observable("0");
		self.fields.vulnerabilityHistorialDetails = ko.observable("");

		self.fields.tenantReferences = ko.observable("");

		// Miscellaneous
		self.fields.comments = ko.observable("");

		// Administration
		self.fields.lfl_contact = ko.observable("");
		self.fields.contact_status = ko.observable("");

		self.fields.local_authority.subscribe(function(newValue){
			debug.log(newValue, 'TenantModel local_authority newValue @@=======================');
		});

		self.fields.isModified = ko.observable(false);

		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);
			self.fields.id(data.id);

			// Basic details
			self.fields.contact_title(data.contact_title?data.contact_title.id:"");
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

			// General details
			self.fields.birthdate(data.birthdate);
			self.fields.gender(data.gender?data.gender.id:"");
			self.fields.marital_status(data.marital_status?data.marital_status.id:"");
			self.fields.nin(data.nin);

			if(typeof data.local_authority === 'object')
			{
				self.fields.local_authority(data.local_authority.id);
			}

			if(typeof data.social_services_contact === 'object')
			{
				self.fields.social_services_contact(data.social_services_contact.id);
			}

			// Children are updated from _tenant.controller.js in INIT block

			// Support
			_.each(data.nights_support, function(item, key, list){
				debug.log('push new night support', item.id);
				self.fields.nights_support.push(item.id.toString());
			}, this);

			self.fields.need_night_support(data.need_night_support?'1':'0');
			self.fields.has_chc_budget(data.has_chc_budget?'1':'0');
			self.fields.support_package_hours(data.support_package_hours);

			// Conditions
			_.each(data.tenant_has_condition, function(item, key, list){
				debug.log('push item condition:', item);
				self.fields.conditions.push(item.condition.id.toString());
				//if(item.condition.is_other)
					//eval('self.fields.other_'+item.condition.id+'("'+item.other+'")');
			}, this);

			// Support provider

			if(typeof data.agency_support_provider === 'object')
			{
				self.fields.agency_support_provider(data.agency_support_provider.id);
				self.fields.has_agency_support_provider("1");
			}
			else
			{
				self.fields.has_agency_support_provider("0");
			}

			if(typeof data.contact_support_provider === 'object')
			{
				self.fields.contact_support_provider(data.contact_support_provider.id);
			}

			// Deputy
			if(typeof data.deputy === 'object')
			{
				self.fields.deputy(data.deputy.id);
			}

			self.fields.lack_capacity(data.lack_capacity?"1":"0");

			// Housing requirements
			self.fields.housingRegister(data.housing_register?"1":"0");
			self.fields.moveDate(data.move_date);
			self.fields.outCounty(data.out_county?"1":"0");
			self.fields.specialDesignFeatures(data.special_design_features);
			self.fields.tenantPersonality(data.tenant_personality);
			self.fields.willingToShare(data.willing_to_share?"1":"0");

			_.each(data.requirements, function(requirement, key, list){
				debug.log('push new requirement:', requirement.id);
				self.fields.requirements.push(requirement.id.toString());
			}, this);

			// Parking
			_.each(['15','16'], function(item, key, list){
				var exist = self.fields.requirements.indexOf(item);
				if(exist >= 0)
					self.fields.parking(item);
			});

			// Garden
			_.each(['17','18', '19'], function(item, key, list){
				var exist = self.fields.requirements.indexOf(item);
				if(exist >= 0)
					self.fields.garden(item);
			});

			// Accessible
			_.each(['26','27', '28'], function(item, key, list){
				var exist = self.fields.requirements.indexOf(item);
				if(exist >= 0)
					self.fields.accessible(item);
			});

			// Smoking
			_.each(['29','30'], function(item, key, list){
				var exist = self.fields.requirements.indexOf(item);
				if(exist >= 0)
					self.fields.smoking(item);
			});

			// Pets
			_.each(['31','32'], function(item, key, list){
				var exist = self.fields.requirements.indexOf(item);
				if(exist >= 0)
					self.fields.pets(item);
			});

			// Parking for
			self.fields.parkingFor(data.parking_for);

			// Tenant history
			self.fields.drugHistorial(data.drug_historial?"1":"0");
			self.fields.drugHistorialDetails(data.drug_historial_details);
			self.fields.sexualOffencesHistorial(data.sexual_offences_historial?"1":"0");
			self.fields.sexualOffencesHistorialDetails(data.sexual_offences_historial_details);
			self.fields.arsonHistorial(data.arson_historial?"1":"0");
			self.fields.arsonHistorialDetails(data.arson_historial_details);
			self.fields.evictionsHistorial(data.evictions_historial?"1":"0");
			self.fields.evictionsHistorialDetails(data.evictions_historial_details);
			self.fields.violenceHistorial(data.violence_historial?"1":"0");
			self.fields.violenceHistorialDetails(data.violence_historial_details);
			self.fields.antiSocialHistorial(data.anti_social_historial?"1":"0");
			self.fields.antiSocialHistorialDetails(data.anti_social_historial_details);
			self.fields.rentArrearsHistorial(data.rent_arrears_historial?"1":"0");
			self.fields.rentArrearsHistorialDetails(data.rent_arrears_historial_details);
			self.fields.vulnerabilityHistorial(data.vulnerability_historial?"1":"0");
			self.fields.vulnerabilityHistorialDetails(data.vulnerability_historial_details);
			self.fields.tenantReferences(data.tenant_references);

			// Miscellaneous
			self.fields.comments(data.comments);

			// Administration
			if(typeof data.lfl_contact === 'object')
			{
				self.fields.lfl_contact(data.lfl_contact.id);
			}
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

	TenantModel.prototype = new BaseModel();

	TenantModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		if(self.fields.id()) // put
		{
			self.api("api_put_tenant", "PUT", {tenant: ko.toJS(self.fields)}, success, error, {idtenant: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_tenant", "POST", {tenant: ko.toJS(self.fields)}, success, error);
		}
	};

	TenantModel.prototype.delete = function(success, error, id){
		var self = this;
		var idtenant;

		if(self.fields.id())
		{
			idtenant = self.fields.id();
		}
		else
		{
			idtenant = id;
		}
		self.api("api_delete_tenant", "DELETE", {}, success, error, {idtenant: idtenant});
	};

	TenantModel.prototype.addChild = function(item){
		var self = this;

        if (typeof item === "object")
        {
            self.fields.children.push(item.fields);
            debug.log('addChild: self.fields.children', self.fields.children);
        }

	};

	TenantModel.prototype.removeChild = function(token){
		var self = this;
		debug.log('token on ContractorModel.removeChild()', token);
        if (token)
        {
        	self.fields.children.remove(function(element){
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

	// Scan for dynamic other fields in support section.
	TenantModel.prototype.scan = function(data){
		var self = this;
		debug.log('Starting scan!!!');
		var $others = $("input:text[data-bind*='other_']");
		debug.log("    others: ", $others);
		$.each($others, function(index, item){
			debug.log("      other ID: ", item.id);
			var idcondition = item.id.split("_",2);
			var idfinal = idcondition[1];
			self.fields[item.id] = ko.observable("");
			self.fields[item.id].subscribe(function(newValue){
				debug.log(newValue, "-------------------------------------------------------------------------------"+item.id+" subscribe newValue");
				self.fields.others.push({'+idfinal+': newValue});
			});
		});

		if(typeof data === 'object')
		{
			// Conditions
			_.each(data.tenant_has_condition, function(item, key, list){
				if(item.condition.is_other)
					self.fields['other_'+item.condition.id](item.other);
			}, this);
		}

	};

	TenantModel.prototype.addArea = function(item){
		var self = this;

        if (typeof item === "object")
        {
            self.fields.areas.push(item.fields);
            debug.log('addArea: self.fields.areas', self.fields.areas);
        }

	};

	TenantModel.prototype.removeArea = function(token){
		var self = this;
		debug.log('token on TenantModel.removeArea()', token);
        if (token)
        {
        	self.fields.areas.remove(function(element){
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

	return TenantModel;

});
