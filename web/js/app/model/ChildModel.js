define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var _ = require("underscore");

	var BaseModel = require("app/model/BaseModel");

	function ChildModel(data){
		this.fields = {};
		this.fields.token = ko.observable(_.uniqueId("child-"));
		this.fields.id = ko.observable("");
		this.fields.name = ko.observable("");
		this.fields.birthdate = ko.observable("");
		this.fields.guardianship = ko.observable("0");

		if(typeof data==='object')
		{
			debug.log('data is an objetct!', data);
			this.fields.id(data.id);
			this.fields.name(data.name);
			this.fields.birthdate(data.birthdate);
			this.fields.guardianship(data.guardianship?'1':'0');
		}

		this.fields.name.subscribe(function(newValue){
			debug.log(newValue, 'name newValue');
		});	
		this.fields.guardianship.subscribe(function(newValue){
			debug.log(newValue, '>>>>>>>subscribe: guardianship newValue');
		});		
	}

	ChildModel.prototype = new BaseModel();



	return ChildModel;		

	
});
