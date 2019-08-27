define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/contacts");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var combobox = require("jquery.combobox");
		var smartcombo = require('jquery.smartcombo');
		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var RACollectionModel = require("app/model/RACollectionModel");
		var RAItemModel = require("app/model/RAItemModel");
		var TicketModel = require("app/model/TicketModel");
		require("custom-binding-handlers");
		require("knockout-app-validation");

		ko.bindingHandlers.stopBinding = {
		    init: function() {
		        return { controlsDescendantBindings: true };
		    }
		};

		ko.virtualElements.allowedBindings.stopBinding = true;

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		var $categories = $('.category-container');

		$.each($categories, function(index1, category){

			var ra_category = new RACollectionModel();
			ko.cleanNode(category);
			ko.applyBindings(ra_category, category);

			var $questions = $(category).find('.risk-assessment-question');
			debug.log($questions, "questions in category "+index1);

			$.each($questions, function(index, question){
				var current_question = $(question).find("input[name=risk_assessment\\[question\\]]").val();

				if(static_data.contact.risks_assessments.length>0)
				{
					var data = static_data.contact.risks_assessments.find(function(item){

						if(item.question.id == current_question)
							return true;
					});
				}

				if(typeof data === 'undefined')
					var data = {};

				data.contact = {id: static_data.contact.id};

				if(typeof data.question === 'undefined')
				{
					debug.log(data.question, 'We don\'t have data.question so we take it from HTML');
					data.question = {id: current_question};
				}

				if(typeof data.answer === 'undefined')
					data.isSaved = false;
				else
					data.isSaved = true;

				debug.log(data, 'data before inject to RAItemModel');
				var ra_item = new RAItemModel(data);

				ko.cleanNode(question);
				ko.applyBindings(ra_item, question);

				ra_category.fields.questions.push(ra_item);

			});

		});

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
