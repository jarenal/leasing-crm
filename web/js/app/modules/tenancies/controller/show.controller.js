define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");
	var bootstrap_table = require("bootstrap_table");
	var moment = require("moment.min");

    function operateFormatter(value, row, index) {
        return [
  			'<div class="btn-toolbar">',
			'<a href="#" class="btn btn-default btn-xs btn-show-breakdown"><i class="glyphicon glyphicon-eye-open"></i></a>',
			'<a href="#" class="btn btn-default btn-xs btn-edit-breakdown"><i class="glyphicon glyphicon-pencil"></i></a>',
			'</div>'
        ].join('');
    }

    function dateFormatter(value, row, index){
    	if(value)
    	{
	    	return moment(value, moment.ISO_8601).format("DD/MM/YYYY");
    	}
    	else
    	{
    		return "N/A";
    	}
    }

    var operateEvents = {
        'click .btn-show-breakdown': function (e, value, row, index) {
        	e.preventDefault();
            location.assign(Routing.generate("app_backend_breakdown_show", {id: row.id}));
        },
        'click .btn-edit-breakdown': function (e, value, row, index) {
        	e.preventDefault();
            location.assign(Routing.generate("app_backend_breakdown_edit", {id: row.id}));
        },
    };

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var TenancyModel = require("app/model/TenancyModel");
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var tenancy;

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model

		tenancy = new TenancyModel();

		if(typeof static_data.tenancy === 'object')
		{
			tenancy.setData(static_data.tenancy);
		}

		ko.applyBindings(tenancy, document.getElementById('fields-container'));

		tenancy.readyForModifications();

	    $('#table-breakdowns').bootstrapTable({
	        method: 'get',
	        url: Routing.generate("api_get_breakdowns_by_tenancy", {tenancy: static_data.tenancy.id}),
	        cache: false,
	        height: 'auto',
	        striped: true,
	        pagination: true,
	        pageSize: 50,
	        pageList: [10, 25, 50, 100, 200],
	        search: true,
	        showColumns: true,
	        showRefresh: true,
	        minimumCountColumns: 2,
	        clickToSelect: false,
	        responseHandler: function(res) {
				return res.data;
			},
	        columns: [{
	            field: 'id',
	            title: 'ID',
	            align: 'right',
	            valign: 'bottom',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'startDate',
	            title: 'Start date',
	            align: 'center',
	            valign: 'bottom',
	            sortable: true,
	            visible: true,
	            formatter:dateFormatter,
	        }, {
	            field: 'operate',
	            title: 'Actions',
	            align: 'center',
	            valign: 'middle',
	            clickToSelect: false,
	            formatter: operateFormatter,
	            events: operateEvents
			}]
	    }).on('load-success.bs.table', function(e, data){
	    	debug.log('....................................data table loaded: now...', Date.now());
	    });
		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
