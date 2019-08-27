define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {

		var debug = require("debug");
		debug.log("DOM is ready!!");
		var $ = require("jquery");
		var fos_js_routes = require("fos_js_routes");
	    var error_handler = require("error_handler");
	    var error_handler = require("misc_functions");

	    // 60000 miliseconds repeat keepAlive function
        var keepaliveID = window.setInterval(keepAlive, 60000);

	});
});
