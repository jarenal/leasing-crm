define(function(require, exports, module){
	"use strict";

	var domReady = require("domReady");

	domReady(function () {
		var debug = require("debug");
		debug.log('DOM ready!');
		var static_data = require("static_data");
		debug.log('static_data...', static_data);
		require("compiled/common");
		require("compiled/properties");
		require("checkbox");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");

		/*
		ko.bindingHandlers.stopBinding = {
		    init: function() {
		        return { controlsDescendantBindings: true };
		    }
		};

		ko.virtualElements.allowedBindings.stopBinding = true;*/

		var FileModel = require("app/model/FileModel");
		var PropertyModel = require("app/model/PropertyModel");
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var bootstrap_table = require("bootstrap_table");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var property;
		var fileinput = require("fileinput");
		var multiFile = require('jquery.multifile');
		require("knockout-app-validation");
		require("custom-binding-handlers");
		require("misc_functions");

		/*******************************************************/
		/*** FILES TABLE ***************************************/
		/*******************************************************/
		function operateFormatter(value, row, index) {
		    return [
					'<div class="btn-toolbar">',
					'<a href="#" class="btn btn-default btn-xs btn-open-file"><i class="glyphicon glyphicon-eye-open"></i></a>',
		            '<a href="#" class="btn btn-default btn-xs btn-remove-foto"><i class="glyphicon glyphicon-remove"></i></a>',
				    '</div>'
		    ].join('');
		}

		window.operateEvents = {
		    'click .btn-remove-foto': function (e, value, row, index) {
		    	e.preventDefault();
		        debug.log(row.id, 'Click in remove foto.');

				$("#modal-message-file-deletion").html("");
				dust.render("common/view/modal_delete_file", {title: 'Caution!!!', filename: row.name, token: row.token}, function(err, out) {
					$('#modal-message-file-deletion').html(out).modal({});
				});
		    },
		    'click .btn-open-file': function (e, value, row, index) {
		    	e.preventDefault();
		        debug.log(row.path, 'Click open file.');
				var newURL = window.location.origin + row.path;
		        window.open(newURL, "_blank");
		    },
		};

		/*******************************************************/
		/*** END FILES TABLE ***********************************/
		/*******************************************************/

		/*******************************************************/
		/*** PROPERTY EVENTS ***********************************/
		/*******************************************************/

	    // On save Property
	    $(document).off("click", "#btn-save-property").on("click", "#btn-save-property", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save property!!!");

	    	property.save(function(result, textStatus, jqXHR){
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
								sessionStorage.setItem("property_updated", 1);
							else
								sessionStorage.setItem("property_created", 1);
						break;
						default:
							// Save event for show alert in contact index screen
					}

					// go to index
					location.assign(Routing.generate("app_backend_property_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-property

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

	    $(document).off("click", "#housing-requirements-fieldset input[name='property\\[parking\\]']").on("click", "#housing-requirements-fieldset input[name='property\\[parking\\]']", updateParking);

	    // Previous crimes events
	    function updatePreviousCrimes(e){
	    	debug.log(e, "Click previous crimes radiobutton!!");
	    	var value = $("input[name='property\\[previous_crimes_near\\]']:checked").val();
	    	debug.log(value, "previous_crimes_near radiobutton value ");

	    	if(value==1)
	    	{
	    		$('#previous_crimes_description_row').slideDown();
	    	}
	    	else
	    	{
	    		$('#previous_crimes_description_row').slideUp();
	    	}
	    }

	    // Parking events
	    $(document).off("click", "#housing-requirements-fieldset input[name='property\\[previous_crimes_near\\]']").on("click", "#housing-requirements-fieldset input[name='property\\[previous_crimes_near\\]']", updatePreviousCrimes);

	    // Delete file
		$(document).off('click', '#btn-confirm-file-deletion').on('click', '#btn-confirm-file-deletion', function(e){
			e.preventDefault();
			debug.log('Confirmado.');
			$('#modal-message-file-deletion').modal('hide');
			var fileObj = new FileModel();
    		fileObj.delete(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

					$('#table-documents').bootstrapTable('refresh', {});
					$('#table-images').bootstrapTable('refresh', {});

	    			$(document).scrollTop(0);
					dust.render("common/view/alert_messages", {message: "Congrats! The file was deleted successfully", type: 'success'}, function(err, out) {
						$('#alert-messages').html(out);
						$('#alert-messages').slideDown(500, function(){
							$('#alert-messages').delay(5000).slideUp(500);
						});
					});
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
			}, undefined, $(this).data('token'));
		});

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

		debug.log("Loading form_property...");

		// Loading Template
		dust.render("properties/view/form_property", {property_statuses: static_data.property_statuses},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(property, document.getElementById('fields-container'));

				// to fill combox (edit view only)
				if(static_data.view==='edit')
				{
					if(typeof static_data.property == 'object')
					{
						if(typeof static_data.property.landlord == 'object')
						{
							if(static_data.property.landlord.fullname)
							{
								$("#property_landlord_combobox").val(static_data.property.landlord.fullname);
							}
						}

						if(typeof static_data.property.local_authority == 'object')
						{
							if(static_data.property.local_authority.name)
							{
								$("#property_local_authority_combobox").val(static_data.property.local_authority.name);
							}
						}

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
						            formatter: operateFormatter,
						            events: operateEvents,
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
						            formatter: operateFormatter,
						            events: operateEvents,
						            width: 100,
								}]
						    }).on('load-success.bs.table', function(e, data){
						    	// On load success event...
						    });
						}
					}
				}

				$('#housing-requirements-fieldset label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});

				$("#multi-documents-container").multiFile({
					afterAddFile: function(container){
						var element = container.find('input');
						ko.cleanNode(element.get(0));

						element.fileinput({
								browseClass: "btn btn-primary btn-lg",
								removeClass: "btn btn-danger btn-lg",
								mainClass: "margin-top-10",
								showCaption: true,
								showPreview: false,
								showRemove: true,
								showUpload: false,
								layoutTemplates: {
								    main1: '{preview}\n' +
							        '<div class="kv-upload-progress hide"></div>\n' +
							        '<div class="input-group {class}">\n' +
							        '   {caption}\n' +
							        '   <div class="input-group-btn">\n' +
							        '       {remove}\n' +
							        '       {cancel}\n' +
							        '       {upload}\n' +
							        '       {browse}\n' +
							        '		<a href="#" class="btn btn-danger btn-lg mf-btn-remove-row" role="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>\n' +
							        '   </div>\n' +
							        '</div>',
								}

							});

						var new_file = new FileModel();
						new_file.fields.id(element.attr("id"));
						new_file.setTypeAndInput("D", element.get(0));
						element.data("token", new_file.fields.token());
						ko.applyBindings(new_file, container.get(0));
						property.addFileDocument(new_file);

						// It's necessary when the user click [remove] button so we can notify the change to the file validation in FileModel.
						element.on('filecleared', function(event) {
						    debug.log("File filecleared.");
						    $(this).trigger('change');
						});
					},
					afterRemoveFile: function(container, element){
						debug.log("element on afterRemovefile ", element);
						var token = element.data("token");
						debug.log("token in afterRemovefile ", token);
						ko.cleanNode(container);
						property.removeFileDocument(token);
					}
				});

				$("#multi-images-container").multiFile({
					afterAddFile: function(container){
						var element = container.find('input');
						ko.cleanNode(element.get(0));

						element.fileinput({
								browseClass: "btn btn-primary btn-lg",
								removeClass: "btn btn-danger btn-lg",
								mainClass: "margin-top-10",
								showCaption: true,
								showPreview: false,
								showRemove: true,
								showUpload: false,
								layoutTemplates: {
								    main1: '{preview}\n' +
							        '<div class="kv-upload-progress hide"></div>\n' +
							        '<div class="input-group {class}">\n' +
							        '   {caption}\n' +
							        '   <div class="input-group-btn">\n' +
							        '       {remove}\n' +
							        '       {cancel}\n' +
							        '       {upload}\n' +
							        '       {browse}\n' +
							        '		<a href="#" class="btn btn-danger btn-lg mf-btn-remove-row" role="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>\n' +
							        '   </div>\n' +
							        '</div>',
								}

							});

						var new_file = new FileModel();
						new_file.fields.id(element.attr("id"));
						new_file.setTypeAndInput("I", element.get(0));
						element.data("token", new_file.fields.token());
						ko.applyBindings(new_file, container.get(0));
						property.addFileImage(new_file);

						// It's necessary when the user click [remove] button so we can notify the change to the file validation in FileModel.
						element.on('filecleared', function(event) {
						    debug.log("File filecleared.");
						    $(this).trigger('change');
						});
					},
					afterRemoveFile: function(container, element){
						debug.log("element on afterRemovefile ", element);
						var token = element.data("token");
						debug.log("token in afterRemovefile ", token);
						ko.cleanNode(container);
						property.removeFileImage(token);
					}
				});

				updateParking();
				updatePreviousCrimes();

				property.readyForModifications();
		});

		$('#loading-modal').hide();
		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	});
});
