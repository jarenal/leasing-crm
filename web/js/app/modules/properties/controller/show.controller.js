define(function(require, exports, module){
	"use strict";

	var debug = require("debug");
	var domReady = require("domReady");
	var static_data = require("static_data");
	debug.log('static_data...', static_data);
	require("compiled/common");
	require("compiled/properties");
	require("checkbox");
	var $ = require("jquery");
	var _ = require("underscore");
	var fos_js_routes = require("fos_js_routes");
	var ko = require("knockout");
	var PropertyModel = require("app/model/PropertyModel");
	var bootstrap = require("bootstrap");
	var bootstrap_table = require("bootstrap_table");
	var dust = require("dust.core");
	var error_handler = require("error_handler");
	var property;
	var moment = require("moment.min");
	require("misc_functions");

	domReady(function () {

		debug.log('DOM ready!');

		/*******************************************************/
		/*** COMMON FUNCTIONS ***************************************/
		/*******************************************************/
	    function dateAndTimeFormatter(value, row, index){
	        if(value)
	        {
	            return moment(value, moment.ISO_8601).format("DD/MM/YYYY HH:mm");
	        }
	        else
	        {
	            return "N/A";
	        }
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

		/*******************************************************/
		/*** TABLE FUNCTIONS ***********************************/
		/*******************************************************/
	    function operateTasksFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-ticket"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-ticket"><i class="glyphicon glyphicon-pencil"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var operateTasksEvents = {
	        'click .btn-show-ticket': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_ticket_show", {id: row.id}));
	        },
	        'click .btn-edit-ticket': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_ticket_edit", {id: row.id}));
	        },
	    };

		/*******************************************************/
		/*** END FILES TABLE ***********************************/
		/*******************************************************/

		/*******************************************************/
		/*** LEASE AGREEMENTS TABLE ****************************/
		/*******************************************************/

	    function operateLeaseAgreementsFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-leaseagreement"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-leaseagreement"><i class="glyphicon glyphicon-pencil"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var operateLeaseAgreementsEvents = {
	        'click .btn-show-leaseagreement': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_leaseagreement_show", {id: row.id}));
	        },
	        'click .btn-edit-leaseagreement': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_leaseagreement_edit", {id: row.id}));
	        },
	    };

		/*******************************************************/
		/*** END LEASE AGREEMENTS TABLE ************************/
		/*******************************************************/

		/*******************************************************/
		/*** TENANCIES TABLE ***********************************/
		/*******************************************************/
	    function operateTenanciesFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-show-tenancy"><i class="glyphicon glyphicon-eye-open"></i></a>',
				'<a href="#" class="btn btn-default btn-xs btn-edit-tenancy"><i class="glyphicon glyphicon-pencil"></i></a>',
				'</div>'
	        ].join('');
	    }

	    var operateTenanciesEvents = {
	        'click .btn-show-tenancy': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_tenancy_show", {id: row.id}));
	        },
	        'click .btn-edit-tenancy': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_tenancy_edit", {id: row.id}));
	        }
	    };
		/*******************************************************/
		/*** TENANCIES TABLE ***********************************/
		/*******************************************************/

		/*******************************************************/
		/*** PROPERTY EVENTS ***********************************/
		/*******************************************************/

		// Parking events
	    function updateParking(e){
	    	debug.log(e, "Click parking radiobutton!!");
	    	var value = $("input[name='property\\[parking\\]']:checked").val();
	    	debug.log(value, "parking radiobutton value ");

	    	if(value==15)
	    	{
	    		debug.log("Deshabilitamos parking for");
	    		$("#property_parking_for").attr("disabled","disabled");
	    		property.fields.parking_for("0");
	    	}
	    	else
	    	{
	    		debug.log("Habilitamos parking for");
	    		$("#property_parking_for").removeAttr("disabled");
	    	}
	    }

	    // Previous crimes events
	    function updatePreviousCrimes(e){
	    	if(static_data.property.previous_crimes_near)
	    	{
	    		$('#previous_crimes_description_row').slideDown();
	    	}
	    	else
	    	{
	    		$('#previous_crimes_description_row').slideUp();
	    	}
	    }

	    /*******************************************************/
	    /*** END PROPERTY EVENTS *******************************/
	    /*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.property === 'object')
		{
			property = new PropertyModel(static_data.property);
		}
		else
		{
			property = new PropertyModel();
		}

		debug.log("Loading show_property...");

		// Loading Template
		dust.render("properties/view/show_property", {},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(property, document.getElementById('fields-container'));

				$('#housing-requirements-fieldset label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});

				updateParking();
				updatePreviousCrimes();

				$('#housing-requirements-fieldset label.cbx-label input').attr("disabled","disabled");
				$('#housing-requirements-fieldset label.cbx-label input').checkboxX('refresh');

				if(static_data.view==='show')
				{
					if(typeof static_data.property == 'object')
					{
						if(static_data.property.id)
						{
						    $('#table-documents').bootstrapTable({
						        method: 'get',
						        url: Routing.generate('api_get_files_documents_by_property', {idproperty: static_data.property.id}),
						        cache: false,
						        striped: true,
						        pagination: true,
						        pageSize: 10,
						        pageList: [5,10,15],
						        search: false,
						        showColumns: false,
						        showRefresh: false,
						        minimumCountColumns: 2,
						        clickToSelect: false,
						        sortable: false,
						        ajaxOptions: {async: false},
						        responseHandler: function(res) {
									return res;
								},
						        columns: [{
						            field: 'id',
						            title: 'ID',
						            align: 'right',
						            valign: 'middle',
						            visible: true,
						            width: 50,
						        }, {
						            field: 'name',
						            title: 'Name',
						            align: 'center',
						            valign: 'middle',
						            visible: true,
						            width: '100%',
						        }, {
						            field: 'operate',
						            title: 'Actions',
						            align: 'center',
						            valign: 'middle',
						            clickToSelect: false,
						            formatter: operateFilesFormatter,
						            events: operateFilesEvents,
						            width: 100,
								}]
						    }).on('load-success.bs.table', function(e, data){
						    	// On load success event...
						    });

						    $('#table-images').bootstrapTable({
						        method: 'get',
						        url: Routing.generate('api_get_files_images_by_property', {idproperty: static_data.property.id}),
						        cache: false,
						        striped: true,
						        pagination: true,
						        pageSize: 10,
						        pageList: [5,10,15],
						        search: false,
						        showColumns: false,
						        showRefresh: false,
						        minimumCountColumns: 2,
						        clickToSelect: false,
						        sortable: false,
						        ajaxOptions: {async: false},
						        responseHandler: function(res) {
									return res;
								},
						        columns: [{
						            field: 'id',
						            title: 'ID',
						            align: 'right',
						            valign: 'middle',
						            visible: true,
						            width: 50,
						        }, {
						            field: 'path',
						            title: 'Image',
						            align: 'center',
						            valign: 'middle',
						            visible: true,
						            formatter: showImage,
						            width: 100,
						        }, {
						            field: 'name',
						            title: 'Name',
						            align: 'center',
						            valign: 'middle',
						            visible: true,
						            width: '100%',
						        }, {
						            field: 'operate',
						            title: 'Actions',
						            align: 'center',
						            valign: 'middle',
						            clickToSelect: false,
						            formatter: operateFilesFormatter,
						            events: operateFilesEvents,
						            width: 100,
								}]
						    }).on('load-success.bs.table', function(e, data){
						    	// On load success event...
						    });
						}
					}
				}

		});

        $('#table-tasks').bootstrapTable({
            method: 'get',
            url: Routing.generate('api_get_tickets_by_property', {property: static_data.property.id}),
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
            ajaxOptions: {async: false},
            rowStyle: tasksRowStyles,
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
                field: 'title',
                title: 'Title',
                align: 'center',
                valign: 'middle',
                sortable: true,
                visible: true,
            }, {
                field: 'createdAt',
                title: 'Created at',
                align: 'center',
                valign: 'middle',
                sortable: true,
                visible: true,
                formatter: dateAndTimeFormatter,
            }, {
                field: 'duedateAt',
                title: 'Due date',
                align: 'center',
                valign: 'middle',
                sortable: true,
                visible: true,
                formatter: dateFormatter,
            },  {
                field: 'status_name',
                title: 'Status',
                align: 'center',
                valign: 'middle',
                sortable: true,
                visible: true,
            }, {
                field: 'operate',
                title: 'Actions',
                align: 'center',
                valign: 'middle',
                clickToSelect: false,
                formatter: operateTasksFormatter,
                events: operateTasksEvents
			}]
        }).on('load-success.bs.table', function(e, data){
        	debug.log('....................................table tasks loaded: now...', Date.now());
        });

	    $('#table-leaseagreements').bootstrapTable({
	        method: 'get',
	        url: Routing.generate('api_get_leaseagreements_by_property', {property: static_data.property.id}),
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
	            field: 'address',
	            title: 'Property',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'startDate',
	            title: 'Start date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'endDate',
	            title: 'End date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        },  {
	            field: 'reviewDate',
	            title: 'Review date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'owner',
	            title: 'Owner',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'operate',
	            title: 'Actions',
	            align: 'center',
	            valign: 'middle',
	            clickToSelect: false,
	            formatter: operateLeaseAgreementsFormatter,
	            events: operateLeaseAgreementsEvents
			}]
	    }).on('load-success.bs.table', function(e, data){
	    	debug.log('....................................data table loaded: now...', Date.now());
	    });

	    $('#table-tenancies').bootstrapTable({
	        method: 'get',
	        url: Routing.generate('api_get_tenancies_by_property', {property: static_data.property.id}),
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
	            field: 'address',
	            title: 'Property',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'startDate',
	            title: 'Start date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'endDate',
	            title: 'End date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        },  {
	            field: 'reviewDate',
	            title: 'Review date',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	            formatter: dateFormatter,
	        }, {
	            field: 'owner',
	            title: 'Owner',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'operate',
	            title: 'Actions',
	            align: 'center',
	            valign: 'middle',
	            clickToSelect: false,
	            formatter: operateTenanciesFormatter,
	            events: operateTenanciesEvents
			}]
	    }).on('load-success.bs.table', function(e, data){
	    	debug.log('....................................data table loaded: now...', Date.now());
	    });

	    property.readyForModifications();
		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
