define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var BaseModel = require("app/model/BaseModel");

	function ContactStatusModel(){

	}

	ContactStatusModel.prototype = new BaseModel();

	ContactStatusModel.prototype.getList = function(success, error){
		this.api("api_get_contact_statuses", "GET", {}, success, error);
	};

	return ContactStatusModel;	
});
