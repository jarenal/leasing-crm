define(function(require, exports, module){

	"use strict";

	var _ = require("underscore");
	var _string = require("underscore.string");
	var $ = require("jquery");
	var debug = require("debug");
	var dust = require("dust.core");

	var error_handler = {
		display: function(ex, $target, $context){

			var text = '';

			if(typeof ex === 'object')
			{
				if('status' in ex) // ajax response
				{
					text += _string.sprintf("Error: %s - %s (%s)", ex.status, ex.statusText, ex.url);
					debug.warn(text);
				}
				else // rest of cases
				{
					text += _string.sprintf("Error: %s - %s", ex.code, ex.message);
					debug.warn(text);
					text += "<br>";

					_.each(ex.errors, function(value, key, list){
						var error = _.pairs(value);
						var message = "";
						message = _string.sprintf("<strong>%s</strong>: %s", error[0][0], error[0][1]);
						debug.warn(message);
						text += '<br>&nbsp;&nbsp;&nbsp;&nbsp;- '+message;
					});
				}
			}

			dust.render("common/view/modal_alert_messages", {message: text, title: 'warning'}, function(err, out) {
				$target.html(out).modal({});
			});

		},
	};

	return error_handler;


});
