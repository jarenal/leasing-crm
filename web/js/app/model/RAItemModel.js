define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	var _ = require("underscore");

	function RAItemModel(data){

		var self = this;
		self.fields = {};

		self.fields.property = ko.observable("");
		self.fields.contact = ko.observable("");
		self.fields.question = ko.observable("");
		self.fields.answer = ko.observable("");
		self.fields.comments = ko.observable("").extend({required: {message: "The comments are required."}, logChange: self});
		self.fields.level_of_risk = ko.observable("").extend({required: {message: "Level of risk is required."}, logChange: self});
		self.fields.action_needed = ko.observable("0");
		self.fields.associated_task = ko.observable("").extend({required: {message: "The associated task is required.", depend_on: self.fields.action_needed, "validate_if_value": 1}, logChange: self});
		self.fields.isModified = ko.observable(false); // for to manage if the form was modified or no.
		self.fields.isSaved = ko.observable(false);

		self.fields.showMoreFields = function(){
			if(parseInt(self.fields.answer())===0)
				return true;
			else
				return false;
		};

		self.fields.showAssociatedTask = function(){
			if(parseInt(self.fields.action_needed())===1 && parseInt(self.fields.answer())===0)
				return true;
			else
				return false;
		};

		if(typeof data === 'object')
		{
			debug.log('We have data in RAItemModel', data);

			if('property' in data)
				self.fields.property(data.property.id);

			if('contact' in data)
				self.fields.contact(data.contact.id);

			if('question' in data)
				self.fields.question(data.question.id);

			if('answer' in data)
				self.fields.answer(data.answer?'1':'0');

			if('comments' in data)
				self.fields.comments(data.comments);

			if('level_of_risk' in data)
				self.fields.level_of_risk(data.level_of_risk);

			if('ticket' in data)
			{
				debug.log(data.ticket, ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> We have a ticket");
				self.fields.associated_task(data.ticket.id);
				self.fields['associated_task_'+data.question.id+'_combobox'] = data.ticket.fulltitle;
			}

			if('action_needed' in data)
				self.fields.action_needed(data.action_needed?'1':'0');

			if('isSaved' in data)
				self.fields.isSaved(data.isSaved);

		}

		self.fields.answer.subscribe(function(newValue){
			debug.log(newValue, 'self.fields.answer.subscribe: newValue');
		});

		self.fields.isSaved.subscribe(function(newValue){
			debug.log(newValue, 'self.fields.isSaved.subscribe: newValue');
		});
		/*
		self.fields.action_needed.subscribe(function(newValue){
			debug.log(newValue, '============================================================================================ action_needed newValue');
			debug.log(this.fields.comments(), '============================================================================================ comments after action_needed subscribe');
		}, self);*/

		self.fields.formHasError = function(){
			debug.log("formHasError was executed!");
			if(self.fields.isModified())
			{
				var result = _.find(self.fields, function(property){
					//debug.log(property, "------------------------------------------------------------------------> current property");
					if(_.has(property, "hasError"))
					{
						debug.log(property, "------------------------------------------------------------------------> current question has hasError!!!");
						debug.log(property.hasError(), "------------------------------------------------------------------------> current question hasError value is....");

						if((property.hasError() === true) || (typeof property.hasError() === 'undefined'))
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
			}
			else
			{
				return true;
			}

		};
	}

	RAItemModel.prototype = new BaseModel();

	RAItemModel.prototype.save = function(){
		var self = this;
		var api_route;
		var temp = ko.toJS(self.fields);
		debug.log(temp, "RAItemModel.prototype.save: temp");
		var parameters = {};
		parameters.action_needed = temp.action_needed;
		parameters.answer = temp.answer;
		parameters.associated_task = temp.associated_task;
		parameters.comments = temp.comments;
		parameters.level_of_risk = temp.level_of_risk;

		if(temp.property)
		{
			parameters.property = temp.property;
			api_route = "api_post_property_risk_assessment";
		}
		else if(temp.contact)
		{
			parameters.contact = temp.contact;
			api_route = "api_post_contact_risk_assessment";
		}

		parameters.question = temp.question;
		self.api(api_route, "POST", {risk_assessment: parameters}, function(){
					debug.log('save success!!');
					self.fields.isSaved(true);
				}, function(){});

		$('#modal-message-confirmation').modal('hide');
	};

	RAItemModel.prototype.popup = function(model, event){
		var self = this;


		debug.log(model, "RAItemModel.prototype.popup: model");
		debug.log(event, "RAItemModel.prototype.popup: event");
		debug.log(self.fields.answer(), "RAItemModel.prototype.popup: self.fields.answer()");

		if(self.fields.comments())
		{
			$("#modal-message-confirmation").html("");
			dust.render("common/view/modal_box_change_answer_confirmation", {}, function(err, out) {
				debug.log(out, 'out modal_box_delete_ticket_confirmation');
				$('#modal-message-confirmation').html(out).modal({});

				$(document).off('click', '#btn-confirm-answer').on('click', '#btn-confirm-answer', function(e){
					e.preventDefault();
					self.fields.answer('1');
					self.fields.comments("");
					self.fields.level_of_risk("");
					self.fields.level_of_risk("N/A");
					self.fields.action_needed('0');
					self.fields.associated_task("");
					self.save();
				});
			});

			self.fields.answer('0');
			return false;
		}
		else
		{
			self.fields.level_of_risk("N/A");
			self.save();
			return true;
		}

	};

	RAItemModel.prototype.startEdit = function(){
		var self = this;

		self.fields.isSaved(false);
	};

	/*
	RAItemModel.prototype.futureActionNeeded = function(data, event){
			var self = this;

			debug.log('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ futureActionNeeded change');
			debug.log(data, "data in futureActionNeeded");
			debug.log(event, "event in futureActionNeeded");

	    	debug.log(data.fields.action_needed(), '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ futureActionNeeded change');

			var padre = $(event.currentTarget).parents('fieldset');

	    	if(parseInt(data.fields.action_needed()))
	    	{
	    		debug.log("Habilitamos associated_task");
    			$(padre[0]).find(".custom-combobox-input").autocomplete( "enable" ).removeAttr("disabled");
	    	}
	    	else
	    	{
	    		debug.log("deshabilitamos associated_task");
	    		$(padre[0]).find(".custom-combobox-input").autocomplete( "disable" ).attr("disabled","disabled").val("");
	    		//tenant.fields.agency_support_provider("");
	    	}

    };*/

	return RAItemModel;
});
