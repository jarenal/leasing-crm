define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");
	var static_data = require("static_data");
	var chktree = require('jquery.chktree');
	var ko = require("knockout");
	var TenantModel = require("app/model/TenantModel");
	var tenant;
	var checkbox = require("checkbox");
	var debug = require("debug");
	var $ = require("jquery");
	var bootstrap = require("bootstrap");
	var bootstrap_table = require("bootstrap_table");
	var moment = require("moment.min");
    require("misc_functions");

    // Common functions
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

    // Table Tasks
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

    // Table properties
    function operatePropertiesFormatter(value, row, index) {
        return [
            '<div class="btn-toolbar">',
            '<a href="#" class="btn btn-default btn-xs btn-show-property"><i class="glyphicon glyphicon-eye-open"></i></a>',
            '<a href="#" class="btn btn-default btn-xs btn-edit-property"><i class="glyphicon glyphicon-pencil"></i></a>',
            '</div>'
        ].join('');
    }

    var operatePropertiesEvents = {
        'click .btn-show-property': function (e, value, row, index) {
            e.preventDefault();
            location.assign(Routing.generate("app_backend_property_show", {id: row.id}));
        },
        'click .btn-edit-property': function (e, value, row, index) {
            e.preventDefault();
            location.assign(Routing.generate("app_backend_property_edit", {id: row.id}));
        },
    };

	domReady(function () {

		debug.log("DOM is ready in show_tenant !!");

        $('#table-tasks').bootstrapTable({
            method: 'get',
            url: Routing.generate('api_get_tickets_by_tenant', {tenant: static_data.contact.id}),
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

		if($('#conditions-container').length)
		{
			debug.log("We have #conditions-container");

			// Conditions Checkbox Plugin
			$('#conditions-container').chktree({chklist: static_data.tenant_conditions,
												label: {
													class: "checkbox-inline cbx-label"
												},
												input_chk: {
													id: "contact_condition_",
													type: "checkbox",
													name: "contact[conditions]",
													data_bind: "checked: fields.conditions"
												}
										});
			// Loading Model
			if(typeof static_data.contact === 'object')
			{
				tenant = new TenantModel(static_data.contact);
			}
			else
			{
				tenant = new TenantModel();
			}

			ko.cleanNode(document.getElementById('conditions-container'));
			// Scan for dynamic other fields in support section. (Always after create chktree!!!!)
			tenant.scan(static_data.contact);
			ko.applyBindings(tenant, document.getElementById('conditions-container'));

			//$('#conditions-container').chktree.refresh("contact_condition_");

			// Load checkbox X plugin
		  	$('#support-fieldset label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});

			$('#conditions-container').find("input").attr("disabled","disabled");
			$('#conditions-container').find("label").addClass("disabled","disabled");
			$('#support-fieldset label.cbx-label input').attr("disabled","disabled");

			$('#support-fieldset label.cbx-label input').checkboxX('refresh');

		}

        $('#table-attachments').bootstrapTable({
            method: 'get',
            url: Routing.generate('api_get_files_by_tenant', {idtenant: static_data.contact.id}),
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

        tenant.readyForModifications();
		$("#loading-modal").hide();

	});
});
