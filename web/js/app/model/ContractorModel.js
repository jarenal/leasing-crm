define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var _ = require("underscore");
	var $ = require("jquery");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");
	/*
	ko.bindingHandlers.myfile = {
	    init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
	        debug.log('myfile init!');
	        $(element).on('change', function(e){
	        	debug.log('myfile change!!');
	        });
	    },
	    update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
	        debug.log('myfile update!');
	    }
	};	*/

	ko.extenders.myFileHandle = function(target, precision) {
	    var result = ko.computed(function(){
	        return "Hello Pepe!!";
	    });
	    return result;
	};

	function ContractorModel(data){
		var self = this;

		self.fields = {};

		// Contact fields
		self.fields.id = ko.observable("");
		self.fields.name = ko.observable("").extend({logChange: self, isUniqueFullname: self});
		self.fields.surname = ko.observable("").extend({logChange: self, isUniqueFullname: self});
		self.fields.email = ko.observable("").extend({logChange: self, isUniqueEmail:self});
		self.fields.landline = ko.observable("");
		self.fields.mobile = ko.observable("");
		self.fields.contact_method = ko.observable("");
		self.fields.contact_method_other = ko.observable("");
		self.fields.address = ko.observable("");
		self.fields.postcode = ko.observable("");
		self.fields.town = ko.observable("");
		self.fields.contact_type = ko.observable("3");
		self.fields.contact_status = ko.observable("");
		self.fields.contact_title = ko.observable("");
		self.fields.organisation = ko.observable("");
		self.fields.comments = ko.observable("");

		// Contractor fields
		self.fields.require_certification = ko.observable("0");
		self.fields.liability_insurance = ko.observable("0");
		self.fields.areas = ko.observableArray([]);
		self.fields.services = ko.observableArray([]);
		self.fields.input_file_certification = ko.observable("");
		self.fields.input_file_insurance = ko.observable("");

		// Certification file: Computed field for get the selected file from the user file system.
		self.fields.file_certification = ko.computed(function(){
			var kofield = self.fields.input_file_certification();
			debug.log('Certification file: ', kofield);

			if(kofield)
			{
				// Accessing selected file using File API (HTML5)
				var element = document.getElementById('contact_certification_file');
				debug.log(element.files, 'javascript element.files');
				debug.log($("#contact_certification_file").get(0).files, 'jquery element files');
				return $("#contact_certification_file").get(0).files[0];
			}
			else
			{
				return "";
			}
		});

		// Insurance file: Computed field for get the selected file from the user file system.
		self.fields.file_insurance = ko.computed(function(){
			var kofield = self.fields.input_file_insurance();
			debug.log('Insurance file: ', kofield);

			// If input_file_insurance() has value...
			if(kofield)
			{
				// Accessing selected file using File API (HTML5)
				var element = document.getElementById('contact_insurance_file');
				debug.log(element.files, 'javascript element.files');
				debug.log($("#contact_insurance_file").get(0).files, 'jquery element files');
				return $("#contact_insurance_file").get(0).files[0];
			}
			else
			{
				return "";
			}
		});

		self.fields.organisation.subscribe(function(newValue){
			debug.log(newValue, 'ContractorModel organisation newValue =======================');
		});

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);

			// Contact fields
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

			if(typeof data.contact_type === 'object')
				self.fields.contact_type(data.contact_type.id);

			if(typeof data.contact_status === 'object')
				self.fields.contact_status(data.contact_status.id);

			if(typeof data.contact_title === 'object')
				self.fields.contact_title(data.contact_title.id);

			if(typeof data.organisation === 'object')
				self.fields.organisation(data.organisation.id);

			self.fields.comments(data.comments);

			// Contractor fields
			self.fields.require_certification(data.require_certification?"1":"0");
			self.fields.liability_insurance(data.liability_insurance?"1":"0");

			_.each(data.services, function(service, key, list){
				debug.log('push new service:', service.id);
				self.fields.services.push(service.id.toString());
			}, this);

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

		self.fields.services.subscribe(function(newValue){
			debug.log(newValue, 'services newValue =======================');
		});
	}

	ContractorModel.prototype = new BaseModel();

	ContractorModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		var formData = new FormData();

		/************ Version para soportar ficheros */
		_.each(self.fields, function(field, key, list){
			if(key=='areas')
			{
				_.each(field(), function(area, key2, list2){
					debug.log(area, 'Recorriendo areas!!!');
					_.each(area, function(areafield, key3, list3){
						debug.log(areafield(), 'contractor['+key+']['+key2+']['+key3+']');
						formData.append('contractor['+key+']['+key2+']['+key3+']', areafield());
					});
				});
			}
			else if(key=='services')
			{
				_.each(field(), function(service, key2, list2){
					formData.append('contractor['+key+'][]', service);
				});
			}
			else
			{
				debug.log(key, "key in contractor[key]");
				formData.append('contractor['+key+']', field());
			}
		});

		if(self.fields.id()) // put
			self.api("api_post_contractor_update", "POST", formData, success, error, {idcontractor: self.fields.id()}, true);
		else // post
			self.api("api_post_contractor", "POST", formData, success, error, null, true);
	};

	ContractorModel.prototype.delete = function(success, error, id){
		var idcontractor;
		var self = this;

		if(self.fields.id())
		{
			idcontractor = self.fields.id();
		}
		else
		{
			idcontractor = id;
		}
		self.api("api_delete_contractor", "DELETE", {}, success, error, {idcontractor: idcontractor});
	};

	ContractorModel.prototype.addArea = function(item){
		var self = this;

        if (typeof item === "object")
        {
            self.fields.areas.push(item.fields);
            debug.log('addArea: self.fields.areas', self.fields.areas);
        }

	};

	ContractorModel.prototype.removeArea = function(token){
		var self = this;
		debug.log('token on ContractorModel.removeArea()', token);
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

	ContractorModel.prototype.deleteFile = function(success, error, token, filename, type){
		var self = this;
		self.api("api_delete_contractor_file_type", "DELETE", {}, success, error, {token: token, filename: filename, type: type});
	};

	return ContractorModel;

});
