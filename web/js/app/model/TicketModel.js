define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	var moment = require("moment.min");
	require("knockout-app-validation");

	function TicketModel(data){

		debug.log('^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ TicketModel - START ');
		var self = this;
		self.fields = {};

		self.fields.id = ko.observable("");
		self.fields.title = ko.observable("").extend({logChange: self});
		self.fields.description = ko.observable("").extend({logChange: self});
		self.fields.action_needed = ko.observable("");
		self.fields.date_reported = ko.observable(moment().format("DD/MM/YYYY"));
		self.fields.duedate_at = ko.observable("").extend({logChange: self});
		self.fields.status = ko.observable("");
		self.fields.reported_by = ko.observable("");
		self.fields.assign_to = ko.observable("").extend({logChange: self});
		self.fields.ticket_type = ko.observable("").extend({logChange: self});
		self.fields.parent = ko.observable("").extend({logChange: self});
		self.fields.time_spent = ko.observable("");
		self.fields.time_spent_unit = ko.observable("");
		self.fields.comments = ko.observableArray([]);
		//self.fields.address = ko.observable("").extend({required: "The address is required."});

		// Related contacts
		self.fields.relatedContacts = ko.observableArray([]);
		self.fields.search_related_contact = ko.observable("");
		self.fields.emptyRelatedContactSearch = function(){
			if(self.fields.search_related_contact())
				return false;
			else
				return true;
		};

		self.fields.related_contacts = ko.computed(function(){
			var contacts = [];
			_.each(self.fields.relatedContacts(), function(contact, key, list){
				contacts.push(contact.id);
			}, this);
			return contacts;
		},this);

		// Related properties
		self.fields.relatedProperties = ko.observableArray([]);
		self.fields.search_related_property = ko.observable("");
		self.fields.emptyRelatedPropertySearch = function(){
			if(self.fields.search_related_property())
				return false;
			else
				return true;
		};

		self.fields.related_properties = ko.computed(function(){
			var properties = [];
			_.each(self.fields.relatedProperties(), function(property, key, list){
				properties.push(property.id);
			}, this);
			return properties;
		},this);

		self.fields.formHasError = function(){

			var result = _.find(self.fields, function(field){
				if(_.has(field, "hasError"))
				{
					debug.log(field, "---> current field has hasError!!!");
					debug.log(field.hasError(), "---> current field hasError value is....");

					if(field.hasError() === true)
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

		self.fields.duedate_at.subscribe(function(newValue){
			debug.log(newValue, 'duedate_at newValue =======================');
		});

		self.fields.parent.subscribe(function(newValue){
			debug.log(newValue, 'parent newValue =======================');
		});

		self.fields.reported_by.subscribe(function(newValue){
			debug.log(newValue, 'reported_by newValue =======================');
		});

		self.fields.assign_to.subscribe(function(newValue){
			debug.log(newValue, 'assign_to newValue =======================');
		});

		self.fields.search_related_contact.subscribe(function(newValue){
			debug.log(newValue, 'search_related_contact newValue =======================');
		});

		self.fields.isModified = ko.observable(false);

		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

		// I add read_by property checking, because when we use TicketModel in another screens with static_data (Property risk assessments for example), try to load the property data in this model.
		if((typeof data === 'object') && ('read_by' in data))
		{
			debug.log('tenemos data', data);
			self.fields.id(data.id);

			// Basic details
			self.fields.title(data.title);
			self.fields.description(data.description);
			self.fields.action_needed(data.action_needed);
			if('date_reported' in data)
			{
				if(data.date_reported)
					self.fields.date_reported(data.date_reported);
			}

			self.fields.duedate_at(data.duedate_at);

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

			if(typeof data.reported_by === 'object')
			{
				self.fields.reported_by(data.reported_by.id);
				self.fields.reported_by_name = data.reported_by.fullname;
				self.fields.reported_by_link = self.getRouteByContactType(data.reported_by);
			}

			if(typeof data.assign_to === 'object')
			{
				self.fields.assign_to(data.assign_to.id);
				self.fields.assign_to_name = data.assign_to.fullname;
				self.fields.assign_to_link = self.getRouteByContactType(data.assign_to);
			}

			if(typeof data.ticket_type === 'object')
			{
				self.fields.ticket_type(data.ticket_type.id);
				self.fields.ticket_type_name = data.ticket_type.name;
			}

			if(typeof data.parent === 'object')
			{
				self.fields.parent(data.parent.id);
				self.fields.parent_title = data.parent.fulltitle;
				self.fields.parent_link = Routing.generate("app_backend_ticket_show", {id: data.parent.id});
			}

			if(typeof data.status === 'object')
			{
				self.fields.status(data.status.id);
				self.fields.status_name = data.status.name;
			}

			self.fields.time_spent(data.time_spent);
			self.fields.time_spent_unit(data.time_spent_unit);

			if(data.time_spent)
			{
				self.fields.full_time_spent = data.time_spent+" "+data.time_spent_unit;
			}

			_.each(data.related_contacts, function(contact, key, list){
				debug.log('push new contact:', contact.id);
				var temp_contact = {};
				temp_contact.id = contact.id;
				temp_contact.name = contact.fullname+" ("+contact.contact_type.name+")";
				self.addRelatedContact(temp_contact);
			}, this);

			_.each(data.related_properties, function(property, key, list){
				debug.log('push new property:', property.id);
				var temp_property = {};
				temp_property.id = property.id;
				temp_property.name = property.fulltitle;
				self.addRelatedProperty(temp_property);
			}, this);

			_.each(data.comments, function(comment, key, list){
				debug.log('push new comment:', comment.id);
				self.addComment(comment);
			}, this);

		}

		debug.log('^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ TicketModel - END ');
	}

	TicketModel.prototype = new BaseModel();

	TicketModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');

		if(self.fields.id()) // put
		{
			self.api("api_put_ticket", "PUT", {ticket: ko.toJS(self.fields)}, success, error, {idticket: self.fields.id()});
		}
		else // post
		{
			self.api("api_post_ticket", "POST", {ticket: ko.toJS(self.fields)}, success, error);
		}
	};

	TicketModel.prototype.delete = function(success, error, id){
		var self = this;
		var idticket;

		if(self.fields.id())
		{
			idticket = self.fields.id();
		}
		else
		{
			idticket = id;
		}
		self.api("api_delete_ticket", "DELETE", {}, success, error, {idticket: idticket});
	};

	TicketModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");
		self.fields.title("");
		self.fields.description("");
		self.fields.action_needed("");
		self.fields.duedate_at("");
		self.fields.reported_by("");
		self.fields.assign_to("");
		self.fields.parent("");
		self.fields.time_spent("");
		self.fields.time_spent_unit("");
		self.fields.relatedContacts([]);
		self.fields.search_related_contact("");
	};

	TicketModel.prototype.getRouteByContactType = function(record){
		var self = this;

		var route;

		switch(record.dtype)
		{
			default:
			route = "app_backend_contact_show";
			break;
		}

		return Routing.generate(route, {id: record.id});
	};

	TicketModel.prototype.addRelatedContact = function(new_contact){
		var self = this,item;
		debug.log(new_contact, "TicketModel::addRelatedContact: new_contact:");
		if(!new_contact)
		{
			debug.log("Click addRelatedContact!");
			item = self.fields.search_related_contact();

			self.removeRelatedContact(item);
		}
		else
		{
			item = new_contact;
		}


		self.fields.relatedContacts.push(item);
		self.fields.search_related_contact("");
		if($('#table-related-contacts').length)
		{
			$('#table-related-contacts').bootstrapTable('load', self.fields.relatedContacts());
			$("#search_related_contact_combobox").val("");
		}


	};

	TicketModel.prototype.removeRelatedContact = function(item){
		var self = this;
		debug.log(item, 'removeRelatedContact: item to remove');

        for (var i = 0; i < self.fields.relatedContacts().length; i++) {
            if (self.fields.relatedContacts()[i].name == item.name) {
                debug.log(i, 'removeRelatedContact: index to remove');
                self.fields.relatedContacts.splice(i, 1);
            }
            else{
            	debug.log(i, 'removeRelatedContact: index keeped');
            }
        }

        $('#table-related-contacts').bootstrapTable('load', self.fields.relatedContacts());
	};

	TicketModel.prototype.addRelatedProperty = function(new_property){
		var self = this,item;
		debug.log(new_property, "TicketModel::addRelatedProperty: new_property:");
		if(!new_property)
		{
			debug.log("Click addRelatedProperty!");
			item = self.fields.search_related_property();

			self.removeRelatedProperty(item);
		}
		else
		{
			item = new_property;
		}

		if(item.name=="N/A")
			return false;

		self.fields.relatedProperties.push(item);
		self.fields.search_related_property("");
		if($('#table-related-properties').length)
		{
			$('#table-related-properties').bootstrapTable('load', self.fields.relatedProperties());
			$("#search_related_property_combobox").val("");			
		}

	};

	TicketModel.prototype.removeRelatedProperty = function(item){
		var self = this;
		debug.log(item, 'removeRelatedProperty: item to remove');

        for (var i = 0; i < self.fields.relatedProperties().length; i++) {
            if (self.fields.relatedProperties()[i].name == item.name) {
                debug.log(i, 'removeRelatedProperty: index to remove');
                self.fields.relatedProperties.splice(i, 1);
            }
            else{
            	debug.log(i, 'removeRelatedProperty: index keeped');
            }
        }

        $('#table-related-properties').bootstrapTable('load', self.fields.relatedProperties());
	};

	TicketModel.prototype.addComment = function(comment){
		var self = this;
		var index = self.fields.comments().length + 1;
		var temp_comment = {};
		temp_comment.id = comment.id;
		temp_comment.title = '#'+index+' - '+comment.created_by.contact.fullname + ' (' + comment.update_date + ')';
		temp_comment.description = comment.description;
		temp_comment.action_needed = comment.action_needed;
		temp_comment.time_spent = comment.time_spent;
		temp_comment.time_spent_unit = comment.time_spent_unit;
		self.fields.comments.push(temp_comment);
	};

	return TicketModel;
});
