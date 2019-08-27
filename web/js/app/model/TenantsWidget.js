define(function(require, exports, module){
	"use strict";

	var ko = require("knockout");
	var debug = require("debug");

	function TenantsWidget(){

		var self = this;
		self.fields = {};

		self.fields.data = ko.observableArray([]);
		self.fields.search_tenant = ko.observable("");
		self.fields.emptySearch = function(){
			if(self.fields.search_tenant())
				return false;
			else
				return true;
		};
/*
		self.fields.addTenant = function(){
			debug.log("Click addTenant!");
			var item = self.fields.search_tenant();

			self.fields.removeTenant(item);

			self.fields.data.push(item);
			self.fields.search_tenant("");
			$('#table-tenants').bootstrapTable('load', self.fields.data());
			$("#combobox-search-tenant").val("");
		};*/

/*
		self.fields.removeTenant = function(item){
			debug.log(item, 'removeTenant: item to remove');
	        for (var i = 0; i < self.fields.data().length; i++) {
	            if (self.fields.data()[i].name == item.name) {
	                debug.log(i, 'removeTenant: index to remove');
	                self.fields.data.splice(i, 1);
	            }
	            else{
	            	debug.log(i, 'removeTenant: index keeped');
	            }
	        }
		};*/
/*
		self.fields.removeTenantRow = function (e, value, row, index) {
			e.preventDefault();
			debug.log(row, 'removeTenantRow: row to remove');
			self.fields.removeTenant(row);
			$('#table-tenants').bootstrapTable('load', self.fields.data());
			/*
			e.preventDefault();
			debug.log(row, 'removeTenantRow: row to remove');
            $('#table-tenants').bootstrapTable('removeByUniqueId', row.id);
            self.fields.removeTenant(row);

        };*/

		self.fields.data.subscribe(function(newValue) {
		    debug.log(newValue, "_._._._._._._._._._._._._._._._._._._._._ TenantsWidget data updated! ");
		});


	}
/*
	ko.bindingHandlers.tenantsWidgetRefreshData = {
	    update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
			//var value = ko.unwrap(valueAccessor());
			debug.log(element, "tenantsWidgetRefreshData!");
	    }
	};*/

	TenantsWidget.prototype.addTenant = function(){
		var self = this;

		debug.log("Click addTenant!");
		var item = self.fields.search_tenant();

		self.removeTenant(item);

		self.fields.data.push(item);
		self.fields.search_tenant("");
		$('#table-tenants').bootstrapTable('load', self.fields.data());
		$("#combobox-search-tenant").val("");

	};

	TenantsWidget.prototype.removeTenant = function(item){
		var self = this;
		debug.log(item, 'removeTenant: item to remove');

        for (var i = 0; i < self.fields.data().length; i++) {
            if (self.fields.data()[i].name == item.name) {
                debug.log(i, 'removeTenant: index to remove');
                self.fields.data.splice(i, 1);
            }
            else{
            	debug.log(i, 'removeTenant: index keeped');
            }
        }

        $('#table-tenants').bootstrapTable('load', self.fields.data());
	};


	return TenantsWidget;
});
