define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/organisations");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var OrganisationModel = require("app/model/OrganisationModel");
		var bootstrap = require("bootstrap");
		var bootstrap_table = require("bootstrap_table");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var organisation;

	    function operateFormatter(value, row, index) {
	    	var template = [];

	    	template.push('<div class="btn-toolbar">');
	    	template.push('<a href="#" class="btn btn-default btn-xs btn-show-contact"><i class="glyphicon glyphicon-eye-open"></i></a>');
	    	template.push('<a href="#" class="btn btn-default btn-xs btn-edit-contact"><i class="glyphicon glyphicon-pencil"></i></a>');
	    	template.push('</div>');

	        return template.join('');
	    }

	    window.operateEvents = {
	        'click .btn-show-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_contact_show", {id: row.id}));
	        },
	        'click .btn-edit-contact': function (e, value, row, index) {
	        	e.preventDefault();
	            location.assign(Routing.generate("app_backend_contact_edit", {id: row.id}));
	        },
	    };

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.organisation === 'object')
		{
			organisation = new OrganisationModel(static_data.organisation);
		}
		else
		{
			organisation = new OrganisationModel();
		}

		debug.log("Loading show_organisation...");

		// Loading Template
		dust.render("organisations/view/show_organisation", {},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(organisation, document.getElementById('fields-container'));

				organisation.readyForModifications();

		});

	    $('#table-contacts').bootstrapTable({
	        method: 'get',
	        url: Routing.generate('api_get_contacts_by_organisation', {organisation: static_data.organisation.id}),
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
	            field: 'name',
	            title: 'Name',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'surname',
	            title: 'Surname',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'fullname',
	            title: 'Full name',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'email',
	            title: 'Email',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'status',
	            title: 'Status',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'landline',
	            title: 'Landline',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'mobile',
	            title: 'Mobile',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'address',
	            title: 'Address',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'postcode',
	            title: 'Postcode',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'town',
	            title: 'Town',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'contact_type',
	            title: 'Type',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: true,
	        }, {
	            field: 'contact_title',
	            title: 'Title',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
	        }, {
	            field: 'organisation',
	            title: 'Organisation',
	            align: 'center',
	            valign: 'middle',
	            sortable: true,
	            visible: false,
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
	    	//waitingDialog.hide("loading-modal");
	    });

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
