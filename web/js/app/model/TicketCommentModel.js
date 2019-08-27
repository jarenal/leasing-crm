define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	var moment = require("moment.min");

	function TicketCommentModel(data){

		var self = this;
		self.fields = {};

		self.fields.id = ko.observable("");
		self.fields.ticket = ko.observable("");
		self.fields.update_date = ko.observable(moment().format("DD/MM/YYYY"));
		self.fields.description = ko.observable("");
		self.fields.action_needed = ko.observable("");
		self.fields.time_spent = ko.observable("");
		self.fields.time_spent_unit = ko.observable("");

	}

	TicketCommentModel.prototype = new BaseModel();

	TicketCommentModel.prototype.save = function(success, error){
		var self = this;
		self.api("api_post_ticket_comment", "POST", {comment: ko.toJS(self.fields)}, success, error);
	};

	TicketCommentModel.prototype.reset = function(){
		var self = this;

		self.fields.id("");
		self.fields.ticket("");
		self.fields.update_date("");
		self.fields.description("");
		self.fields.action_needed("");
		self.fields.time_spent("");
		self.fields.time_spent_unit("");

	};


	return TicketCommentModel;
});
