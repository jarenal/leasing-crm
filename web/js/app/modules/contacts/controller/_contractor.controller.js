define(function(require, exports, module){
	"use strict";

	return function(){
		var debug = require("debug");
		var static_data = require("static_data");
		debug.log('static_data', static_data);
		require("compiled/common");
		require("compiled/contacts");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var ContractorModel = require("app/model/ContractorModel");
		var contractor;
		var AreaModel = require("app/model/AreaModel");
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var fileinput = require("fileinput");

		/*******************************************************/
		/*** CONTRACTOR EVENTS *********************************/
		/*******************************************************/

		// Load organisation events handler.
		//organisationController();

	    // On save Contractor
	    $(document).off("click", "#btn-save-contractor").on("click", "#btn-save-contractor", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save Contractor!!!");

	    	contractor.save(function(result, textStatus, jqXHR){
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
								sessionStorage.setItem("contact_updated", 1);
							else
								sessionStorage.setItem("contact_created", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							//sessionStorage.setItem("contact_updated", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_contact_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-contractor

		// On choose contact method
		function onChangeContactMethod(e)
		{
			debug.log('On choose contact_method');
			var idmethod = parseInt($('#contact_method').val());

			if(idmethod==4)
			{
				$('#contact_method_other_row').slideDown();
			}
			else
			{
				$('#contact_method_other_row').slideUp();
			}
		}

		$(document).off('change', '#contact_method').on('change', '#contact_method', onChangeContactMethod);

		/*******************************************************/
		/*** END CONTRACTOR EVENTS *****************************/
		/*******************************************************/

		/*******************************************************/
		/*** AREAS EVENTS **************************************/
		/*******************************************************/

		/*** Area states ***/
		function stopEditAreas()
		{
    		$('form.form-horizontal fieldset').removeAttr("disabled");
    		$('#fields-container > fieldset').removeAttr("disabled");
    		$('#areas-form-container fieldset').attr("disabled","disabled");

    		$('#btn-stop-edit-areas').hide();
    		$('#btn-new-area').hide();
    		$('#btn-start-edit-areas').show();
		}

		function startEditAreas()
		{
			$('form.form-horizontal fieldset').attr("disabled","disabled");
			$('#fields-container > fieldset').attr("disabled","disabled");
			$('form.form-horizontal fieldset#area-fieldset').removeAttr("disabled");
			$('#areas-form-container fieldset').removeAttr("disabled");

			$('#btn-stop-edit-areas').show();
			$('#btn-new-area').show();
			$('#btn-start-edit-areas').hide();
		}

		/*** End Area states ***/

		// Area: On New Area
		$(document).off("click", "#btn-new-area").on("click", "#btn-new-area", function(e, old_area, autosave){
			e.preventDefault();

			debug.log("New area event!", old_area);

			dust.render("contacts/view/new_area", {}, function(err, out) {

				var new_area = new AreaModel(old_area?old_area:undefined);
			  	$('#areas-form-container').append('<fieldset id="'+new_area.fields.token()+'" style="display: none">'+out+'</fieldset>');
				if(autosave)
				{
					$('#'+new_area.fields.token()).find('.btn-save-area').trigger('click');
				}
				$('#'+new_area.fields.token()).slideDown(500, function(){
					ko.cleanNode(document.getElementById(new_area.fields.token()));
					ko.applyBindings(new_area, document.getElementById(new_area.fields.token()));
					contractor.addArea(new_area);
				});
			});
		});

		// Area: On Save Area
		$(document).off("click", ".btn-save-area").on("click", ".btn-save-area", function(e){
			e.preventDefault();
			debug.log("Save Area event!");

			var $fieldset = $(this).parents('fieldset').first();
			$fieldset.find('.area-amount').attr("disabled", "disabled");
			$fieldset.find('.area-desired-return').attr("disabled", "disabled");
			$fieldset.find('.area-distance').attr("disabled", "disabled");
			$fieldset.find('.area-postcode').attr("disabled", "disabled");
			$fieldset.find('.btn-save-area').hide();
			$fieldset.find('.btn-edit-area').show();
			$fieldset.find('.btn-remove-area').hide();
		});

		// Area: On Edit Area
		$(document).off("click", ".btn-edit-area").on("click", ".btn-edit-area", function(e){
			e.preventDefault();
			debug.log("On edit Area event!");

			var $fieldset = $(this).parents('fieldset').first();

			$fieldset.find('.area-amount').removeAttr("disabled", "disabled");
			$fieldset.find('.area-desired-return').removeAttr("disabled", "disabled");
			$fieldset.find('.area-distance').removeAttr("disabled", "disabled");
			$fieldset.find('.area-postcode').removeAttr("disabled", "disabled");
			$fieldset.find('.btn-save-area').show();
			$fieldset.find('.btn-edit-area').hide();
			$fieldset.find('.btn-remove-area').show();
		});

		// Area: On Remove Area
		$(document).off("click", ".btn-remove-area").on("click", ".btn-remove-area", function(e){
			e.preventDefault();
			debug.log("Remove Area event!");

			var $fieldset = $(this).parents('fieldset').first();
			var token = $fieldset.find(".area-token").val();
			debug.log('token area',token);

			ko.cleanNode(document.getElementById(token));

			contractor.removeArea(token);

			$fieldset.remove();

		});

		// Area: On STOP edit areas
		$(document).off("click", "#btn-stop-edit-areas").on("click", "#btn-stop-edit-areas", function(e){
			e.preventDefault();
			debug.log("On STOP edit areas event!!!");

			var total = $('#area-fieldset .area-postcode').not("[disabled|=disabled]").length;
			if(total>0)
			{
				$("#modal-message").html("");
				dust.render("common/view/modal_box", {title: "Attention.", message: "There are unsaved changes. Please, save all the areas before continue."}, function(err, out) {
					$('#modal-message').html(out).modal({});

				});
			}
			else
			{
	    		stopEditAreas();
			}
		});

		// Area: On START edit areas
		$(document).off("click", "#btn-start-edit-areas").on("click", "#btn-start-edit-areas", function(e){
			e.preventDefault();
			debug.log("On START edit areas event!!!");

			startEditAreas();
		});

		/*******************************************************/
		/*** END AREAS EVENTS **********************************/
		/*******************************************************/

		/*******************************************************/
		/*** START FILES EVENTS ********************************/
		/*******************************************************/
		$(document).off('click', '#btn-remove-file-certification').on('click', '#btn-remove-file-certification', function(e){
			e.preventDefault();
			debug.log('click remove file_certification!!!');
			$("#modal-message-file-deletion").html("");
			dust.render("contacts/view/modal_box_delete_file_confirmation", {title: 'Caution!!!', filename: $(this).data('filename'), token: $(this).data('token'), type: $(this).data('type')}, function(err, out) {
				$('#modal-message-file-deletion').html(out).modal({});
			});
		});

		$(document).off('click', '#btn-remove-file-insurance').on('click', '#btn-remove-file-insurance', function(e){
			e.preventDefault();
			debug.log('click remove file_insurance!!!');
			$("#modal-message-file-deletion").html("");
			dust.render("contacts/view/modal_box_delete_file_confirmation", {title: 'Caution!!!', filename: $(this).data('filename'), token: $(this).data('token'), type: $(this).data('type')}, function(err, out) {
				$('#modal-message-file-deletion').html(out).modal({});
			});
		});

		$(document).off('click', '#btn-confirm-file-deletion').on('click', '#btn-confirm-file-deletion', function(e){
			e.preventDefault();
			debug.log('Confirmado.');
			$('#modal-message-file-deletion').modal('hide');
    		contractor.deleteFile(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

                    if(result.code)
                        throw {code: result.code, message: result.message, errors: result.errors};

	    			switch(result.data.type)
	    			{
	    				case "certification":
							$('.input-certification-file-edit').show();
							$('.input-certification-file-readonly').hide();
	    				break;
	    				case "insurance":
							$('.input-insurance-file-edit').show();
							$('.input-insurance-file-readonly').hide();
	    				break;
	    			}

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
			}, undefined, $(this).data('token'), $(this).data('filename'), $(this).data('type'));
		});

		/*******************************************************/
		/*** END FILES EVENTS **********************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.contact === 'object')
		{
			contractor = new ContractorModel(static_data.contact);
		}
		else
		{
			contractor = new ContractorModel();
		}

		// Loading Template
		debug.log("Loading form_contractor...");


		dust.render("contacts/view/form_contractor", {contact_statuses: static_data.contact_statuses[3],
														contact_titles: static_data.contact_titles,
														contractor_services: static_data.contractor_services,
														contact: static_data.contact,
														contact_methods: static_data.contact_methods,
			},
			function(err, out) {

				// The next line always inside this function for avoid multiple bindings.
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('_-_-_-_-_-_-_-_-_out form contractor-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

				ko.applyBindings(contractor, document.getElementById('fields-container'));

				// Seleccionamos la organizacion (solo en vista edit)
				if(static_data.view === 'edit')
				{
					if(typeof static_data.contact.organisation == 'object')
					{
						if(static_data.contact.organisation.name)
						{
							$(".custom-combobox-input").val(static_data.contact.organisation.name);
						}
					}
				}

				// Creamos nueva area vacia (solo para vista new)
				if(static_data.view==='new')
				{
					debug.log('*** New view ***');
					$("#btn-new-area").trigger('click');
				}

				// Load checkbox X plugin
			  	var checkbox = require("checkbox");
			  	$('#works-fieldset label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});

			  	// Load Areas in edit view
				if(typeof static_data.contact === 'object')
				{
					if(typeof static_data.contact.areas === 'object')
					{
					  	if(static_data.contact.areas.length > 0)
					  	{
							$.each(static_data.contact.areas, function(i, item){
								debug.log('#'+i+' area: ', item);
								$("#btn-new-area").trigger('click', [item, true]);
							});
					  	}
					}
				}

				stopEditAreas();

				// Uplad file buttons
				$("#contact_certification_file").fileinput({
					mainClass: "input-certification-file-edit",
					browseClass: "btn btn-warning btn-lg",
					removeClass: "btn btn-danger btn-lg",
					showCaption: true,
					showPreview: false,
					showRemove: true,
					showUpload: false,
				});

				$("#contact_insurance_file").fileinput({
					mainClass: "input-insurance-file-edit",
					browseClass: "btn btn-warning btn-lg",
					removeClass: "btn btn-danger btn-lg",
					showCaption: true,
					showPreview: false,
					showRemove: true,
					showUpload: false,
				});

				// It's fired when the remove button is pressed for clearing the file preview.
				$('#contact_certification_file').on('fileclear', function(event) {
				    // We have to update the field manually, otherwise the computed field 'file_certification' will not be updated.
				    contractor.fields.input_file_certification("");
				});

				// div.input-group:nth-child(1)
				if(static_data.view==='edit')
				{
					if(static_data.contact.file_certification)
					{
						$('.input-certification-file-edit').hide();
						$('.input-certification-file-readonly').show();
					}
					if(static_data.contact.file_insurance)
					{
						$('.input-insurance-file-edit').hide();
						$('.input-insurance-file-readonly').show();
					}
				}

				contractor.readyForModifications();

		});

		onChangeContactMethod();

		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/

	};
});
