define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var _ = require("underscore");

	var BaseModel = require("app/model/BaseModel");

	function FileModel(data){
		var self = this;
		self.fields = {};
		self.fields.id = ko.observable("");
		self.fields.token = ko.observable(_.uniqueId("file-"));
		self.fields.name = ko.observable("");
		self.fields.type = ko.observable("");

		if(typeof data==='object')
		{
			debug.log('data is an objetct!', data);
			self.fields.id(data.id);
			self.fields.name(data.name);
			self.fields.type(data.type);
		}
	}

	FileModel.prototype = new BaseModel();

	FileModel.prototype.delete = function(success, error, token){
		var self = this;
		self.api("api_delete_file", "DELETE", {}, success, error, {token: token});
	};

	FileModel.prototype.setTypeAndInput = function(type, element){
		var self = this;
		self.fields.type(type);

		switch(type)
		{
			case "I":
				self.fields.name.extend({files: {element: element, max_size: 1000000, message: "Warning! The file is upper to %s MB.", allowed_types: ["image/png", "image/jpeg"]}});
			break;
			default:
				self.fields.name.extend({files: {element: element, max_size: 1000000, message: "Warning! The file is upper to %s MB.", allowed_types: ["application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf"]}});
			break;
		}

	};

	return FileModel;

});
