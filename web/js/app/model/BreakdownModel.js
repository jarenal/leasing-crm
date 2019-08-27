define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	var BreakdownItemModel = require("app/model/BreakdownItemModel");
	require("knockout-app-validation");
	var _ = require("underscore");
	require('math_functions');

	function BreakdownModel(){
		debug.log("######### BreakdownModel: Start");

		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.tenancy = ko.observable("").extend({required: {message: "The tenancy is required."}, logChange: self});
		self.fields.start_date = ko.observable("").extend({required: {message: "The start date is required."}, logChange: self});
		self.fields.recurring_rent_review = ko.observable("").extend({numeric: 0, required: {message: "The recurring rent review is required."}, logChange: self});
		self.fields.recurring_rent_review_timescale = ko.observable("").extend({required: {message: "The recurring rent review timescale is required."}, logChange: self});

		self.fields.items = ko.observableArray([]);
		self.fields.created_by = ko.observable("");
		self.fields.created_at = ko.observable("");
		self.fields.updated_by = ko.observable("");
		self.fields.updated_at = ko.observable("");
		self.fields.isModified = ko.observable(false); // for to manage if the form was modified or no.

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		self.fields.total_core_rent = ko.computed(function(){
			var total = 0;
			_.each(self.fields.items(), function(item, key, list){
				if($.inArray(item.id(), [1,2])>-1)
				{
					var item_value = parseFloat(item.value());
					if(!item_value)
						item_value = 0;

					total = total + item_value;
				}
			});
			return Math.round10(total,-2);
		}, this);

		self.fields.total_service_charges = ko.computed(function(){
			var total = 0;
			_.each(self.fields.items(), function(item, key, list){
				if($.inArray(item.id(), [1,2])==-1)
				{
					var item_value = parseFloat(item.value());
					if(!item_value)
						item_value = 0;

					total = total + item_value;
				}
			});
			return Math.round10(total,-2);
		}, this);

		self.fields.total = ko.computed(function(){
			var total = 0;
			var total_core_rent=0;
			var total_service_charges=0;

			if(self.fields.total_core_rent())
				total_core_rent=self.fields.total_core_rent();

			if(self.fields.total_service_charges())
				total_service_charges=self.fields.total_service_charges();

			total = total_core_rent+total_service_charges;

			return Math.round10(total, -2);
		}, this);

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

		debug.log("######### BreakdownModel: End");
	}

	BreakdownModel.prototype = new BaseModel();

	BreakdownModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');
		if(self.fields.id()) // put
		{
			self.api("api_put_breakdown", "PUT", {breakdown: ko.toJS(self.fields)}, success, error, {idbreakdown: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_breakdown", "POST", {breakdown: ko.toJS(self.fields)}, success, error);
		}
	};

	BreakdownModel.prototype.delete = function(success, error, id){
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
		self.api("api_delete_breakdown", "DELETE", {}, success, error, {id: idrow});
	};

	BreakdownModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");
	};

	BreakdownModel.prototype.setData = function(data){
		var self = this;

		if(typeof data === 'object')
		{
			debug.log('tenemos data', data);

			if('id' in data)
				if(data.id)
					self.fields.id(data.id);

			if(typeof data.tenancy === 'object')
			{
				if(data.tenancy.id)
					self.fields.tenancy(data.tenancy.id);
			}

			if(data.start_date)
				self.fields.start_date(data.start_date);

			if(data.recurring_rent_review)
				self.fields.recurring_rent_review(data.recurring_rent_review);

			if(data.recurring_rent_review_timescale)
				self.fields.recurring_rent_review_timescale(data.recurring_rent_review_timescale);

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

			if(typeof data.items === 'object')
			{
				_.each(data.items, function(item, key, list){
					var model = new BreakdownItemModel(self);
					model.id(item.item.id);
					model.name(item.item.name);
					model.description(item.item.description);
					model.value(item.amount);
					self.fields.items.push(model);
				});
			}

		}
	};

	BreakdownModel.prototype.setItems = function(items){
		var self = this;

		_.each(items, function(item, key, list){
			var model = new BreakdownItemModel(self);
			model.id(item.id);
			model.name(item.name);
			model.description(item.description);
			self.fields.items.push(model);
		});

	};

	return BreakdownModel;
});
