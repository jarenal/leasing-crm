define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var _ = require("underscore");

	var BaseModel = require("app/model/BaseModel");

	function AreaModel(data){
		this.fields = {};
		this.fields.token = ko.observable(_.uniqueId("area-"));
		this.fields.id = ko.observable("");
		this.fields.distance = ko.observable("");
		this.fields.postcode = ko.observable("");

		if(typeof data==='object')
		{
			debug.log('data is an objetct!', data);
			this.fields.id(data.id);
			this.fields.distance(data.distance);
			this.fields.postcode(data.postcode);
		}
	}

	AreaModel.prototype = new BaseModel();

	return AreaModel;		

	
});
