define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function PropertyModel(data){
		var self = this;
		self.fields = {};

		self.fields.id = ko.observable("");

		// Landlord
		self.fields.landlord = ko.observable("");
		self.fields.landlord_fullname = ko.observable("");
		self.fields.full_address = ko.observable("");

		// Local authority
		self.fields.local_authority = ko.observable("");
		self.fields.local_authority_name = ko.observable("");

		// Address
		//self.fields.address = ko.observable("").extend({required: "The address is required."});
		self.fields.address = ko.observable("").extend({logChange: self});
		self.fields.postcode = ko.observable("").extend({logChange: self});
		self.fields.town = ko.observable("").extend({logChange: self});

		// Housing features
		self.fields.available_date = ko.observable("");
		self.fields.features = ko.observableArray([]);
		self.fields.parking = ko.observable("15");
		self.fields.parking_for = ko.observable("0");
		self.fields.accessible = ko.observable("28");
		self.fields.willing_to_adapt = ko.observable("35");
		self.fields.special_design_features = ko.observable("");
		self.fields.previous_crimes_near = ko.observable("0");
		self.fields.previous_crimes_description = ko.observable("");
		self.fields.smoking = ko.observable("30");
		self.fields.pets = ko.observable("32");


		// Value
		self.fields.property_value = ko.observable("");
		self.fields.valuation_date = ko.observable("");
		self.fields.target_rent = ko.observable("");
		self.fields.mortgage_outstanding = ko.observable("0");
		self.fields.buy_to_let = ko.observable("0");

		// Files
		self.fields.files_documents = ko.observableArray([]);
		self.fields.files_images = ko.observableArray([]);

		// Miscellaneous
		self.fields.comments = ko.observable("");

		// Administration
		self.fields.land_registry_docs = ko.observable("0");
		self.fields.status = ko.observable("");
		self.fields.status_name = ko.observable("");

		// Computed getWillingToAdapt
		self.fields.getWillingToAdapt = ko.computed(function(){
			var output;
			switch(self.fields.willing_to_adapt())
			{
				case "33":
					output = "Fully wheelchair accessible";
				break;
				case "34":
					output = "Minor adaptations";
				break;
				case "35":
					output = "None";
				break;
				default:
					output = "Unknow";
				break;
			}
			return output;
		}, this);

		// Computed getAccesible
		self.fields.getAccesible = ko.computed(function(){
			var output;
			switch(self.fields.accessible())
			{
				case "26":
					output = "Fully wheelchair accessible";
				break;
				case "27":
					output = "Minor adaptations";
				break;
				case "28":
					output = "None";
				break;
				default:
					output = "Unknow";
				break;
			}
			return output;
		}, this);

		// Computed getParking
		self.fields.getParking = ko.computed(function(){

			var output;

			if(self.fields.parking()=='16')
			{
				output = "Yes, parking for <strong>"+self.fields.parking_for()+"</strong> ";
				if(self.fields.parking_for()>1)
					output += "cars";
				else
					output += "car";
			}
			else
			{
				output = "No";
			}

			return output;
		}, this);

		self.fields.documents = function(){
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ start documents computed', self.fields.files_documents());
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			var files = [];

			_.each(self.fields.files_documents(), function(file, key, list){
				debug.log("file", file.id());
				//files.push(file.id());
				if(file.id())
				{
					debug.log("++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ file.id() is TRUE");
					//var element = document.getElementById(file.id());
					//debug.log(element.files, '+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ element.files');
					debug.log($("#"+file.id()).get(0).files[0], 'jquery .get(0).files[0]');
					if($("#"+file.id()).get(0).files[0])
						files.push( $("#"+file.id()).get(0).files[0]);
				}
			});

			debug.log("files generated...", files);
			return files;
		};

		self.fields.images = function(){
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ start images computed', self.fields.files_images());
			debug.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			var files = [];

			_.each(self.fields.files_images(), function(file, key, list){
				debug.log("file", file.id());
				//files.push(file.id());
				if(file.id())
				{
					debug.log("++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ file.id() is TRUE");
					//var element = document.getElementById(file.id());
					//debug.log(element.files, '+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ element.files');
					debug.log($("#"+file.id()).get(0).files[0], 'jquery .get(0).files[0]');
					if($("#"+file.id()).get(0).files[0])
						files.push( $("#"+file.id()).get(0).files[0]);
				}
			});

			debug.log("files generated...", files);
			return files;
		};

		self.fields.files_documents.subscribe(function(newValue){
			debug.log(newValue, 'files_documents newValue =======================');
		});

		self.fields.files_images.subscribe(function(newValue){
			debug.log(newValue, 'files_images newValue =======================');
		});

		self.fields.formHasError = function(){
			var result = _.find(self.fields, function(property){
				//debug.log(property, "------------------------------------------------------------------------> current property");
				if(_.has(property, "hasError"))
				{
					debug.log(property, "------------------------------------------------------------------------> current property has hasError!!!");
					debug.log(property.hasError(), "------------------------------------------------------------------------> current property hasError value is....");

					if(property.hasError() === true)
					{
						return true;
					}
				}

			});

			//return self.fields.address.hasError();
			if(typeof result === 'undefined')
				return false;
			else
				return true;
		};

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		if(typeof data === 'object')
		{
			debug.log('we have data', data);
			self.fields.id(data.id);

			if(typeof data.landlord === 'object')
			{
				self.fields.landlord(data.landlord.id);
				self.fields.landlord_fullname(data.landlord.fullname);
				self.fields.full_address(data.address+", "+data.town+", "+data.postcode+" ("+data.landlord.fullname+")");
				self.fields.landlord_link = function(){
					return Routing.generate("app_backend_contact_show", {id: data.landlord.id});
				};
			}

			if(typeof data.local_authority === 'object')
			{
				self.fields.local_authority(data.local_authority.id);
				self.fields.local_authority_name(data.local_authority.name);
				self.fields.local_authority_link = function(){
					return Routing.generate("app_backend_organisation_show", {id: data.local_authority.id});
				};
			}

			// Address
			self.fields.address(data.address);
			self.fields.postcode(data.postcode);
			self.fields.town(data.town);

			// Housing features
			self.fields.available_date(data.available_date);
			self.fields.parking_for(data.parking_for);
			self.fields.special_design_features(data.special_design_features);
			self.fields.previous_crimes_near(data.previous_crimes_near?"1":"0");
			self.fields.previous_crimes_description(data.previous_crimes_description);

			// features
			_.each(data.features, function(feature, key, list){
				debug.log('push new feature:', feature.id);
				self.fields.features.push(feature.id.toString());
			}, this);

			// Parking
			_.each(['15','16'], function(item, key, list){
				var exist = self.fields.features.indexOf(item);
				if(exist >= 0)
					self.fields.parking(item);
			});

			// Accessible
			_.each(['26','27', '28'], function(item, key, list){
				var exist = self.fields.features.indexOf(item);
				if(exist >= 0)
					self.fields.accessible(item);
			});

			// Willing to adapt
			_.each(['33','34', '35'], function(item, key, list){
				var exist = self.fields.features.indexOf(item);
				if(exist >= 0)
					self.fields.willing_to_adapt(item);
			});

			// Smoking
			_.each(['29','30'], function(item, key, list){
				var exist = self.fields.features.indexOf(item);
				if(exist >= 0)
					self.fields.smoking(item);
			});

			// Pets
			_.each(['31','32'], function(item, key, list){
				var exist = self.fields.features.indexOf(item);
				if(exist >= 0)
					self.fields.pets(item);
			});

			// Value
			self.fields.property_value(data.property_value);
			self.fields.valuation_date(data.valuation_date);
			self.fields.target_rent(data.target_rent);
			self.fields.mortgage_outstanding(data.mortgage_outstanding?"1":"0");
			self.fields.buy_to_let(data.buy_to_let?"1":"0");

			// Miscellaneous
			self.fields.comments(data.comments);

			// Administration
			self.fields.land_registry_docs(data.land_registry_docs?"1":"0");
			self.fields.status(data.status.id);
			self.fields.status_name(data.status.name);

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

	PropertyModel.prototype = new BaseModel();

	PropertyModel.prototype.save = function(success, error){

		// Version para soportar ficheros
		var self = this;
		$(window).off('beforeunload');
		debug.log('self.fields', self.fields);
		var formData;

		formData = new FormData();
		_.each(self.fields, function(field, key, list){

			//debug.log(key, 'key+++++++++++++++++++++++++');
			//debug.log(field, 'field+++++++++++++++++++++++++');
			if(key=='features')
			{
				_.each(field(), function(feature, key2, list2){
					formData.append('property['+key+'][]', feature);
				});
			}
			else if(key=='documents' || key=='images')
			{
				debug.log('field().length', field().length);
				if(field().length>0)
				{
					_.each(field(), function(item_file, key3, list3){
						debug.log(item_file, '++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ item_file value is ==> ');
						formData.append('property['+key+'][]', item_file);
					});
				}
				else
				{
					formData.append('property['+key+']', "");
				}
			}
			else
			{
				formData.append('property['+key+']', field());
			}
		});

		if(self.fields.id()) // put
			self.api("api_post_property_update", "POST", formData, success, error, {idproperty: self.fields.id()}, true);
		else // post
			self.api("api_post_property", "POST", formData, success, error, null, true);

	};

	PropertyModel.prototype.delete = function(success, error, id){
		var self = this;
		var idproperty;

		if(self.fields.id())
		{
			idproperty = self.fields.id();
		}
		else
		{
			idproperty = id;
		}
		self.api("api_delete_property", "DELETE", {}, success, error, {idproperty: idproperty});
	};

	PropertyModel.prototype.reset = function(){
		var self = this;
/*
		self.fields.id("");
		self.fields.landlord("");
		self.fields.address("");
		self.fields.postcode("");
		self.fields.town("");
		self.fields.local_authority("");
		self.fields.available_date("");
		self.fields.parking_for("");
		self.fields.special_design_features("");
		self.fields.property_value("");
		self.fields.valuation_date("");
		self.fields.target_rent("");
		self.fields.features("");
		self.fields.previous_crimes_description("");
		self.fields.status("");*/

	};

	PropertyModel.prototype.addFileDocument = function(item){
		var self = this;

        if (typeof item === "object")
        {
            self.fields.files_documents.push(item.fields);
            debug.log('addFileDocument: self.fields.files_documents', self.fields.files_documents);
        }

	};

	PropertyModel.prototype.removeFileDocument = function(token){
		var self = this;
		debug.log('token on PropertyModel.removeFileDocument()', token);
        if (token)
        {
        	self.fields.files_documents.remove(function(element){
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

	PropertyModel.prototype.addFileImage = function(item){
		var self = this;

        if (typeof item === "object")
        {
            self.fields.files_images.push(item.fields);
            debug.log('addFileImage: self.fields.files_images', self.fields.files_images);
        }

	};

	PropertyModel.prototype.removeFileImage = function(token){
		var self = this;
		debug.log('token on PropertyModel.removeFileImage()', token);
        if (token)
        {
        	self.fields.files_images.remove(function(element){
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

	return PropertyModel;
});
