define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");
	require("knockout-app-validation");

	function BreakdownItemModel(parent){

		var self = this;
		self.id = ko.observable("");
		self.name = ko.observable("");
		self.description = ko.observable("");
		self.value = ko.observable("").extend({logChange: parent});


	}

	return BreakdownItemModel;
});
