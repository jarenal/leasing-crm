define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	var BaseModel = require("app/model/BaseModel");
	require("knockout-app-validation");

	function ChangePasswordModel(data){
		debug.log("######### ChangePasswordModel: Start");

		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.current_password = ko.observable("").extend({required: {message: "The current password is required."}, logChange: self});
		self.fields.new_password = ko.observable("").extend({required: {message: "The new password is required."}, minValidator: {min: 8}, matchValues: {message: "New password doesn't match with confirm password.", targetField: 'confirm_new_password',viewModel: self}, logChange: self});
		self.fields.confirm_new_password = ko.observable("").extend({required: {message: "The confirm password is required."}, minValidator: {min: 8}, matchValues: {message: "Confirm password doesn't match with new password.", targetField: 'new_password',viewModel: self}, logChange: self});
		self.fields.isModified = ko.observable(false); // for to manage if the form was modified or no.
		// For avoid exit without save
		self.fields.isModified.subscribe((avoidExitWithoutSave).bind(self));

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

		debug.log("######### ChangePasswordModel: End");
	}

	ChangePasswordModel.prototype = new BaseModel();

	ChangePasswordModel.prototype.save = function(success, error){
		var self = this;
		$(window).off('beforeunload');

		if(self.fields.id()) // put
		{
			self.api("api_put_user_change_password", "PUT", {user: ko.toJS(self.fields)}, success, error, {iduser: self.fields.id()});
		}

	};

	ChangePasswordModel.prototype.reset = function(){
		var self = this;
		self.fields.id('');
		self.fields.current_password('');
		self.fields.new_password('');
		self.fields.confirm_new_password('');
		self.fields.isModified(false);
	}
	return ChangePasswordModel;
});
