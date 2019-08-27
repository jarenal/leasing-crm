define(function(require, exports, module){
	"use strict";

	return function(){
		var debug = require("debug");
		var static_data = require("static_data");
		debug.log('static_data', static_data);
		require("compiled/common");
		require("compiled/contacts");
		require("checkbox");
		var $ = require("jquery");
		var _ = require("underscore");
		var fos_js_routes = require("fos_js_routes");
		var ko = require("knockout");
		var TenantModel = require("app/model/TenantModel");
		var tenant,property;
		var ChildModel = require("app/model/ChildModel");
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");
		var chktree = require('jquery.chktree');
		var PropertyModel = require("app/model/PropertyModel");
		var AreaModel = require("app/model/AreaModel");

		/*******************************************************/
		/*** TENANT EVENTS *************************************/
		/*******************************************************/

	    // On save Tenant
	    $(document).off("click", "#btn-save-tenant").on("click", "#btn-save-tenant", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save Tenant!!!");

	    	tenant.save(function(result, textStatus, jqXHR){
	    		try
	    		{
	    			debug.log('result', result);
	    			debug.log('textStatus', textStatus);
	    			debug.log('jqXHR', jqXHR);

					if(result.code)
						throw {code: result.code, message: result.message, errors: result.errors};

					switch(jqXHR.type)
					{
						case "PUT":
							// Save event for show alert in contact index screen
							sessionStorage.setItem("contact_updated", 1);
						break;
						default:
							// Save event for show alert in contact index screen
							sessionStorage.setItem("contact_created", 1);
					}

					// go to index
					location.assign(Routing.generate("app_backend_contact_index"));
	    		}
	    		catch(ex)
	    		{
					error_handler.display(ex, $('#modal-alert-messages'));
	    		}
	    	});
	    });	// end btn-save-tenant

	    // Tenant history events
	    $(document).off("click", "#tenant-history-fieldset input[type='radio']").on("click","#tenant-history-fieldset input[type='radio']",function(e){
	    	debug.log("Click on tenant-history radio buttons!!");
	    	var parents = $(this).parents('div.form-group.form-group-lg');
	    	var next = parents.first().next();
	    	debug.log(next, "parents");
	    	next.slideToggle();

	    });

	    function updateGardenDetails(e){
	    	debug.log(e, "Click garden radiobutton!!");
	    	var value = $("input[name='contact\\[garden\\]']:checked").val();
	    	debug.log(value, "garden radiobutton value ");

	    	if(value==19)
	    	{
	    		debug.log("Ocultamos garden details");
	    		$("#garden-details-box").slideUp();
	    	}
	    	else
	    	{
	    		debug.log("Comprobamos si garden details es visible o no");
	    		if(!$("#garden-details-box").is(":visible"))
	    		{
	    			debug.log("Mostramos garden details");
	    			$("#garden-details-box").slideDown();
	    		}
	    		else
	    		{
	    			debug.log("garde details ya es visible, no hacemos nada.");
	    		}
	    	}
	    }

	    function updateParking(e){
	    	debug.log(e, "Click parking radiobutton!!");
	    	var value = $("input[name='contact\\[parking\\]']:checked").val();
	    	debug.log(value, "parking radiobutton value ");

	    	if(value==15)
	    	{
	    		debug.log("Deshabilitamos parking for");
	    		$("#contact_parking_for").attr("disabled","disabled");
	    		tenant.fields.parkingFor("0");
	    	}
	    	else
	    	{
	    		debug.log("Habilitamos parking for");
	    		$("#contact_parking_for").removeAttr("disabled");
	    	}
	    }

	    // Garden events
	    $(document).off("click", "#housing-requirements-fieldset-2 input[name='contact\\[garden\\]']").on("click", "#housing-requirements-fieldset-2 input[name='contact\\[garden\\]']", updateGardenDetails);

	    // Parking events
	    $(document).off("click", "#housing-requirements-fieldset-2 input[name='contact\\[parking\\]']").on("click", "#housing-requirements-fieldset-2 input[name='contact\\[parking\\]']", updateParking);

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
		/*** END TENANT EVENTS *********************************/
		/*******************************************************/

		/*******************************************************/
		/*** AREAS EVENTS **************************************/
		/*******************************************************/

		/*** Area states ***/
		function stopEditAreas()
		{
    		$('form.form-horizontal > fieldset').removeAttr("disabled");
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
					tenant.addArea(new_area);
				});
			});
		});

		// Area: On Save Area
		$(document).off("click", ".btn-save-area").on("click", ".btn-save-area", function(e){
			e.preventDefault();
			debug.log("Save Area event!");

			var $fieldset = $(this).parents('fieldset').first();
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

			tenant.removeArea(token);

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
		/*** CHILDREN EVENTS ***********************************/
		/*******************************************************/

		/*** Children states ***/
		function stopEditChildren()
		{
    		$('form.form-horizontal > fieldset').removeAttr("disabled");
    		$('#fields-container > fieldset').removeAttr("disabled");
    		$('#children-form-container fieldset').attr("disabled","disabled");

    		$('#btn-stop-edit-children').hide();
    		$('#btn-new-child').hide();
    		$('#btn-start-edit-children').show();
		}

		function startEditChildren()
		{
			$('form.form-horizontal fieldset').attr("disabled","disabled");
			$('#fields-container > fieldset').attr("disabled","disabled");
			$('form.form-horizontal fieldset#children-fieldset').removeAttr("disabled");
			$('#children-form-container fieldset').removeAttr("disabled");

			$('#btn-stop-edit-children').show();
			$('#btn-new-child').show();
			$('#btn-start-edit-children').hide();
		}

		/*** End Children states ***/

		// Children: On New Child
		$(document).off("click", "#btn-new-child").on("click", "#btn-new-child", function(e, old_child, autosave){
			e.preventDefault();

			debug.log("New child event!", old_child);

			var children_count = $('#children-form-container fieldset').length + 1;

			dust.render("contacts/view/new_child", {children_count: children_count}, function(err, out) {

				var new_child = new ChildModel(old_child?old_child:undefined);
			  	$('#children-form-container').append('<fieldset id="'+new_child.fields.token()+'" style="display: none">'+out+'</fieldset>');
				if(autosave)
				{
					$('#'+new_child.fields.token()).find('.btn-save-child').trigger('click');
				}
				$('#'+new_child.fields.token()).slideDown(500, function(){
					ko.cleanNode(document.getElementById(new_child.fields.token()));
					ko.applyBindings(new_child, document.getElementById(new_child.fields.token()));
					tenant.addChild(new_child);

					// Checkbox
			  		//$("#"+new_child.fields.token()+" .child-guardianship").checkboxX({threeState: false, size:'xl', enclosedLabel: true});

				});
			});
		});

		// Child: On Save Child
		$(document).off("click", ".btn-save-child").on("click", ".btn-save-child", function(e){
			e.preventDefault();
			debug.log("Save Child event!");

			var $fieldset = $(this).parents('fieldset').first();
			$fieldset.find('.child-name').attr("disabled", "disabled");
			$fieldset.find('.child-birthdate').attr("disabled", "disabled");
			$fieldset.find('.child-guardianship').attr("disabled", "disabled");
			$fieldset.find('.input-group-addon').attr("disabled", "disabled");
			$fieldset.find('.btn-save-child').hide();
			$fieldset.find('.btn-edit-child').show();
			$fieldset.find('.btn-remove-child').hide();
			//$fieldset.find('.child-guardianship').checkboxX('refresh');
		});

		// Child: On Edit Child
		$(document).off("click", ".btn-edit-child").on("click", ".btn-edit-child", function(e){
			e.preventDefault();
			debug.log("On edit Child event!");

			var $fieldset = $(this).parents('fieldset').first();

			$fieldset.find('.child-name').removeAttr("disabled", "disabled");
			$fieldset.find('.child-birthdate').removeAttr("disabled", "disabled");
			$fieldset.find('.child-guardianship').removeAttr("disabled", "disabled");
			$fieldset.find('.input-group-addon').removeAttr("disabled", "disabled");
			$fieldset.find('.btn-save-child').show();
			$fieldset.find('.btn-edit-child').hide();
			$fieldset.find('.btn-remove-child').show();
			//$fieldset.find('.child-guardianship').checkboxX('refresh');
		});

		// Child: On Remove Child
		$(document).off("click", ".btn-remove-child").on("click", ".btn-remove-child", function(e){
			e.preventDefault();
			debug.log("Remove Child event!");

			var $fieldset = $(this).parents('fieldset').first();
			var token = $fieldset.find(".child-token").val();
			debug.log('token area',token);

			ko.cleanNode(document.getElementById(token));

			tenant.removeChild(token);

			$fieldset.remove();

			var childrenCollection = $('#children-form-container fieldset h3 span');
			var counter = 1;
			$.each(childrenCollection, function(key, item){
				$(item).html(counter);
				counter++;
			});

		});

		// Child: On STOP edit children
		$(document).off("click", "#btn-stop-edit-children").on("click", "#btn-stop-edit-children", function(e){
			e.preventDefault();
			debug.log("On STOP edit children event!!!");

			var total = $('#children-fieldset .child-name').not("[disabled|=disabled]").length;
			if(total>0)
			{
				$("#modal-message").html("");
				dust.render("common/view/modal_box", {title: "Attention.", message: "There are unsaved changes. Please, save all the children before continue."}, function(err, out) {
					$('#modal-message').html(out).modal({});

				});
			}
			else
			{
	    		stopEditChildren();
			}
		});

		// Child: On START edit children
		$(document).off("click", "#btn-start-edit-children").on("click", "#btn-start-edit-children", function(e){
			e.preventDefault();
			debug.log("On START edit children event!!!");

			startEditChildren();
		});

		/*******************************************************/
		/*** END CHILDREN EVENTS *******************************/
		/*******************************************************/

		/*******************************************************/
		/* SUPPORT EVENTS **************************************/
		/*******************************************************/

	    // Nights Support
		function needNightSupportChange(){
	    	debug.log($("input[name='contact\\[need_night_support\\]']:checked").val(), 'need_night_support change');

	    	if(parseInt($("input[name='contact\\[need_night_support\\]']:checked").val()))
	    	{
    			$("#support-fieldset div:first label.cbx-label").removeClass("disabled");
	    		$("#support-fieldset div:first label.cbx-label input").removeAttr("disabled").checkboxX("refresh");
	    	}
	    	else
	    	{
	    		tenant.fields.nights_support.removeAll();
	    		$("#support-fieldset div:first label.cbx-label").addClass("disabled");
				$("#support-fieldset div:first label.cbx-label input").attr("disabled", "disabled").checkboxX("refresh");
	    	}

	    }

	    $(document).off("change", "input[name=contact\\[need_night_support\\]]").on("change", "input[name=contact\\[need_night_support\\]]", needNightSupportChange);

	    // Support provider
		function hasAgencySupportProvider(){
	    	debug.log($("input[name='contact\\[has_agency_support_provider\\]']:checked").val(), '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ has_agency_support_provider change');

	    	if(parseInt($("input[name='contact\\[has_agency_support_provider\\]']:checked").val()))
	    	{
    			$( "#contact_agency_support_provider_combobox" ).autocomplete( "enable" ).removeAttr("disabled");
	    	}
	    	else
	    	{
	    		$( "#contact_agency_support_provider_combobox" ).autocomplete( "disable" ).attr("disabled","disabled").val("");
	    		tenant.fields.agency_support_provider("");
	    	}

	    }

	    $(document).off("change", "input[name=contact\\[has_agency_support_provider\\]]").on("change", "input[name=contact\\[has_agency_support_provider\\]]", hasAgencySupportProvider);

		/*******************************************************/
		/* END SUPPORT EVENTS **********************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.contact === 'object')
		{
			tenant = new TenantModel(static_data.contact);
			if(typeof static_data.contact.property === 'object')
				property = new PropertyModel(static_data.contact.property);
			else
				property = new PropertyModel();
		}
		else
		{
			tenant = new TenantModel();
			property = new PropertyModel();
		}

		debug.log("Loading form_tenant...");

		// Loading Template
		dust.render("contacts/view/form_tenant", {contact_statuses: static_data.contact_statuses[2],
														contact_titles: static_data.contact_titles,
														tenant_genders: static_data.tenant_genders,
														tenant_marital_statuses: static_data.tenant_marital_statuses,
														tenant_nights_support: static_data.tenant_nights_support,
														contact_methods: static_data.contact_methods,
												},
			function(err, out) {
				ko.cleanNode(document.getElementById('fields-container'));
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

			  	$('#fields-container').html(out);

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

				// Scan for dynamic other fields in support section. (Always after create chktree!!!!)
				tenant.scan(static_data.contact);
				ko.applyBindings(tenant, document.getElementById('fields-container'));

				// to fill combox (edit view only)
				if(static_data.view==='edit')
				{
					if(typeof static_data.contact.local_authority == 'object')
					{
						if(static_data.contact.local_authority.name)
						{
							$("#contact_local_authority_combobox").val(static_data.contact.local_authority.name);
						}
					}

					if(typeof static_data.contact.property == 'object')
					{
						if(property.fields.full_address())
						{
							$("#contact_property_combobox").val(property.fields.full_address());
						}
					}

					if(typeof static_data.contact.social_services_contact == 'object')
					{
						if(static_data.contact.social_services_contact.fullname)
						{
							$("#contact_social_services_contact_combobox").val(static_data.contact.social_services_contact.fullname);
						}
					}

					if(typeof static_data.contact.agency_support_provider == 'object')
					{
						debug.log("++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ agency_support_provider is an object");
						if(static_data.contact.agency_support_provider.name)
						{
							debug.log("++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++agency_support_provider has name");
							$("#contact_agency_support_provider_combobox").val(static_data.contact.agency_support_provider.name);
						}
					}

					if(typeof static_data.contact.contact_support_provider == 'object')
					{
						debug.log("----------------------------------------------------------------------contact_support_provider is an object");
						if(static_data.contact.contact_support_provider.fullname)
						{
							debug.log("------------------------------------------------------------------contact_support_provider has name");
							$("#contact_contact_support_provider_combobox").val(static_data.contact.contact_support_provider.fullname);
						}
					}

					if(typeof static_data.contact.deputy == 'object')
					{
						if(static_data.contact.deputy.fullname)
						{
							$("#contact_deputy_combobox").val(static_data.contact.deputy.fullname);
						}
					}

					if(typeof static_data.contact.lfl_contact == 'object')
					{
						if(static_data.contact.lfl_contact.fullname)
						{
							$("#contact_lfl_contact_combobox").val(static_data.contact.lfl_contact.fullname);
						}
					}

				}

				// Create empty children (only new view)
				if(static_data.view==='new')
				{
					var children_count = $('#children-form-container fieldset').length;
					if(children_count === 0)
					{
						$("#btn-new-child").trigger('click');
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
			  	$('#support-fieldset label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});
			  	$('#housing-requirements-fieldset-2 label.cbx-label input').checkboxX({threeState: false, size:'xl', enclosedLabel: true});

			  	$('#support-fieldset label.cbx-label input').on('change', function() {
				    debug.log(this, '[][][][][][][][][][][][][][][][][][][][][][] checkbox changed');
				    $('#support-fieldset label.cbx-label input').checkboxX("refresh");
				    debug.log('---tenant.fields.conditions',tenant.fields.conditions());
				});

				$('#support-fieldset label.cbx-label input').checkboxX("refresh");

			  	// Load Children in edit view
				if(typeof static_data.contact === 'object')
				{
					if(typeof static_data.contact.children === 'object')
					{
					  	if(static_data.contact.children.length > 0)
					  	{
							$.each(static_data.contact.children, function(i, item){
								debug.log('#'+i+' child: ', item);
								$("#btn-new-child").trigger('click', [item, true]);
							});
					  	}
					}
				}

				stopEditChildren();

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

				// update status in some widgets
				var need_night_support = $("input[name='contact\\[need_night_support\\]']:checked").val();
				debug.log('need_night_support', need_night_support);
				needNightSupportChange();
				hasAgencySupportProvider();

				$('#conditions-container').chktree.refresh("contact_condition_");

				// tenant details radio buttons
				var radioButtons = $("#tenant-history-fieldset input[type='radio']:checked");

				$.each(radioButtons, function(index, item){

					debug.log(item, 'radio button item');
					debug.log($(item).val(), "radio val ");

					if(parseInt($(item).val()))
					{
				    	var parents = $(item).parents('div.form-group.form-group-lg');
				    	var next = parents.first().next();
				    	next.slideDown();
					}
				});

				tenant.fields.requirements.subscribe(function(newValue){
					debug.log(newValue, "NewValue in requirements");
					debug.log(tenant.fields.requirements(), "requirements() ");
				});

				updateGardenDetails();
				updateParking();
				onChangeContactMethod();

				tenant.readyForModifications();

		});

		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	};

});
