define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var _ = require("underscore");

	var BaseModel = require("app/model/BaseModel");

	function InvestmentModel(data){
		this.investment = {};
		this.investment.token = ko.observable(_.uniqueId("investment-"));
		this.investment.id = ko.observable("");
		this.investment.amount = ko.observable("");
		this.investment.desired_return = ko.observable("");
		this.investment.distance = ko.observable("");
		this.investment.postcode = ko.observable("");

		if(typeof data==='object')
		{
			debug.log('investment is an objetct!', data);
			this.investment.id(data.id);
			this.investment.amount(data.amount);
			this.investment.desired_return(data.desired_return);
			this.investment.distance(data.distance);
			this.investment.postcode(data.postcode);
		}
	}

	InvestmentModel.prototype = new BaseModel();

	return InvestmentModel;		

	
});
