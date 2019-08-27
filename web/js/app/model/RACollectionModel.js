define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");

	function RACollectionModel(data){

		var self = this;
		self.fields = {};
		self.fields.done = function(){
			var count = 0;
			_.each(self.fields.questions(), function(question, key, list){
				if(question.fields.isSaved())
					count=count+1;
			}, this);
			return count;
		};
		self.fields.questions = ko.observableArray([]);
		self.fields.total = function(){

			return self.fields.questions().length;
		};
	}

	return RACollectionModel;
});
