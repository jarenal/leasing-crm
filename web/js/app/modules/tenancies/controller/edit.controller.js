define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/tenancies");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		require("custom-binding-handlers");
		var TenancyModel = require("app/model/TenancyModel");

		// for to avoid conflicts between jquery.ui and bootstrap plugins.
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var bootstrap_table = require("bootstrap_table");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var tenancy;

		/*******************************************************/
		/*** TENANCY EVENTS *************************************/
		/*******************************************************/

	    // On save Lease agreement
	    $(document).off("click", "#btn-save-tenancy").on("click", "#btn-save-tenancy", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save tenancy!!!");
	    	tenancy.save(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					switch(jqXHR.type)
					{
						case "POST":
							// Save event for show alert in contact index screen
							var regx = /updates/;

							if(regx.test(jqXHR.url))
								sessionStorage.setItem("tenancy_updated", 1);
							else
								sessionStorage.setItem("tenancy_created", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							//sessionStorage.setItem("contact_updated", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_tenancy_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-tenancy

	    /*******************************************************/
	    /*** END TENANCY EVENTS ************************/
	    /*******************************************************/

		/*******************************************************/
		/*** TENANTS TABLE *************************************/
		/*******************************************************/
		function removeTenantRow(e, value, row, index)
		{
			e.preventDefault();
			debug.log(row, 'removeTenantRow: row to remove');
			tenancy.removeTenant(row);
		}

	    function tenantsOperateFormatter(value, row, index) {
	        return [
	  			'<div class="btn-toolbar">',
				'<a href="#" class="btn btn-default btn-xs btn-remove-tenant"><i class="glyphicon glyphicon-remove"></i></a>',
				'</div>'
	        ].join('');
	    }

	    window.tenantsOperateEvents = {
	        'click .btn-remove-tenant': removeTenantRow,
	    };
		/*******************************************************/
		/*** END TENANTS TABLE *********************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		tenancy = new TenancyModel();

		/*
		if(typeof static_data.tenancy === 'object')
		{
			tenancy = new TenancyModel(static_data.tenancy);
		}
		else
		{
			tenancy = new TenancyModel();
		}*/

		debug.log('_o>_o>_o>_o>_o>_o>_o>_o>_o>_o> APPLY BINDINGS START');
		ko.applyBindings(tenancy, document.getElementById('fields-container'));
		debug.log('_o>_o>_o>_o>_o>_o>_o>_o>_o>_o> APPLY BINDINGS END');

		if(typeof static_data.tenancy === 'object')
		{
			tenancy.setData(static_data.tenancy);
		}

		// to fill combox (edit view only)
		if(static_data.view==='edit' || static_data.view==='new')
		{
			if(typeof static_data.tenancy == 'object')
			{
				if(typeof static_data.tenancy.property == 'object')
				{
					if(static_data.tenancy.property.fulltitle)
					{
						$("#tenancy_property_combobox").val(static_data.tenancy.property.fulltitle);
					}
				}

				if(typeof static_data.tenancy.owner == 'object')
				{
					if(static_data.tenancy.owner.fullname)
					{
						debug.log('=== we have fullname at owner');
						$("#tenancy_owner_combobox").val(static_data.tenancy.owner.fullname);
					}
				}
			}
		}

		$('#table-tenants').bootstrapTable({
			data: tenancy.fields.tenants(),
            cache: false,
            height: 'auto',
            striped: true,
            pagination: false,
            pageSize: 99,
            search: false,
            showColumns: false,
            showRefresh: false,
            minimumCountColumns: 2,
            clickToSelect: false,
            uniqueId: 'id',
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
                field: 'operate',
                title: 'Actions',
                align: 'center',
                valign: 'middle',
                clickToSelect: false,
                formatter: tenantsOperateFormatter,
                events: tenantsOperateEvents
			}]
        });

		tenancy.readyForModifications();

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
