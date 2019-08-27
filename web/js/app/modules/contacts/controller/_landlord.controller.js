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
		var LandlordModel = require("app/model/LandlordModel");
		var landlord;
		var InvestmentModel = require("app/model/InvestmentModel");
		$.widget.bridge('uitooltip', $.ui.tooltip);
		$.widget.bridge('uibutton', $.ui.button);
		var bootstrap = require("bootstrap");
		var dust = require("dust.core");
		var error_handler = require("error_handler");

		/*******************************************************/
		/*** LANDLORD EVENTS ***********************************/
		/*******************************************************/

		// Load organisation events handler.
		//organisationController();

	    // On save Landlord
	    $(document).off("click", "#btn-save-landlord").on("click", "#btn-save-landlord", function(e){
	    	e.preventDefault();
	    	debug.log("Click on save Landlord!!!");

			// If the landlord is not investor, we will remove the investments.
			if(parseInt(landlord.fields.is_investor())===0)
			{
				// Remove knockout bindings
				$("#investments-form-container fieldset").each(function(index){
					debug.log('id elemento fieldset unbinding', this.id);
					ko.cleanNode(document.getElementById(this.id));
				});

				// Remove investments on DOM
				$('#investments-form-container').empty();

				// Remove investments on model
				if(landlord.fields.investments().length > 0)
				{
					landlord.fields.investments.removeAll();
				}

				//

			}

	    	landlord.save(function(result, textStatus, jqXHR){
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
	    });

	    // Investment: On choose Yes or No
	    $(document).off("change", "input[name=landlord\\[is_investor\\]]").on("change", "input[name=landlord\\[is_investor\\]]", function(){
	    	debug.log(this, '++++++++++++++++++++++++++++++++++++++++++++++++++++++++is_investor change');

	    	// If is investor...
	    	if($(this).val()==1)
	    	{
	    		$('form.form-horizontal > fieldset').attr("disabled","disabled");
	    		$('#fields-container > fieldset').attr("disabled","disabled");
	    		$('form.form-horizontal fieldset#investment-fieldset').removeAttr("disabled");

				$('#investments-container').slideDown(500);
				if($('#investments-form-container fieldset').length === 0)
				{
					$("#btn-new-investment").trigger('click');
				}
	    	}
	    	else // No investor
	    	{
	    		$('form.form-horizontal fieldset').removeAttr("disabled");
	    		$('#fields-container > fieldset').removeAttr("disabled");
	    		$('#investments-container').slideUp();
	    	}
	    });

		// Investment: On New Investment
		// autosave: Used only for view edit
		$(document).off("click", "#btn-new-investment").on("click", "#btn-new-investment", function(e, old_investment, autosave){
			e.preventDefault();

			debug.log("New investment event!", old_investment);

			dust.render("contacts/view/new_investment", {}, function(err, out) {
				debug.log(err, 'err');
				var tiempo = new Date().getTime();
				debug.log('out-'+tiempo);
				if(err)
			  		debug.error(err);

				var new_investment = new InvestmentModel(old_investment?old_investment:undefined);
			  	$('#investments-form-container').append('<fieldset id="'+new_investment.investment.token()+'" style="display: none">'+out+'</fieldset>');
				if(autosave)
				{
					$('#'+new_investment.investment.token()).find('.btn-save-investment').trigger('click');
				}
				$('#'+new_investment.investment.token()).slideDown(500, function(){
					ko.cleanNode(document.getElementById(new_investment.investment.token()));
					ko.applyBindings(new_investment, document.getElementById(new_investment.investment.token()));
					landlord.addInvestment(new_investment);
				});
			});
		});

		// Investment: On Save Investment
		// NOTE: Only visual efects
		$(document).off("click", ".btn-save-investment").on("click", ".btn-save-investment", function(e){
			e.preventDefault();
			debug.log("Save Investment event!");

			var $fieldset = $(this).parents('fieldset').first();
			$fieldset.find('.investment-amount').attr("disabled", "disabled");
			$fieldset.find('.investment-desired-return').attr("disabled", "disabled");
			$fieldset.find('.investment-distance').attr("disabled", "disabled");
			$fieldset.find('.investment-postcode').attr("disabled", "disabled");
			$fieldset.find('.btn-save-investment').hide();
			$fieldset.find('.btn-edit-investment').show();
			$fieldset.find('.btn-remove-investment').hide();
		});

		// Investment: On Edit Investment
		$(document).off("click", ".btn-edit-investment").on("click", ".btn-edit-investment", function(e){
			e.preventDefault();
			debug.log("On edit Investment event!");

			var $fieldset = $(this).parents('fieldset').first();

			$fieldset.find('.investment-amount').removeAttr("disabled", "disabled");
			$fieldset.find('.investment-desired-return').removeAttr("disabled", "disabled");
			$fieldset.find('.investment-distance').removeAttr("disabled", "disabled");
			$fieldset.find('.investment-postcode').removeAttr("disabled", "disabled");
			$fieldset.find('.btn-save-investment').show();
			$fieldset.find('.btn-edit-investment').hide();
			$fieldset.find('.btn-remove-investment').show();
		});

		// Investment: On Remove Investment
		$(document).off("click", ".btn-remove-investment").on("click", ".btn-remove-investment", function(e){
			e.preventDefault();
			debug.log("Remove Investment event!");

			var $fieldset = $(this).parents('fieldset').first();
			var token = $fieldset.find(".investment-token").val();
			debug.log('token investment',token);

			ko.cleanNode(document.getElementById(token));

			landlord.removeInvestment(token);

			$fieldset.remove();

		});

		// Investment: On STOP edit investments
		$(document).off("click", "#btn-stop-edit-investments").on("click", "#btn-stop-edit-investments", function(e){
			e.preventDefault();
			debug.log("On STOP edit investments event!!!");

			var total = $('#investment-fieldset .investment-amount').not("[disabled|=disabled]").length;
			if(total>0)
			{
				$("#modal-message").html("");
				dust.render("common/view/modal_box", {title: "Attention.", message: "There are unsaved changes. Please, save all the investments before continue."}, function(err, out) {
					$('#modal-message').html(out).modal({});

				});
			}
			else
			{
	    		$('form.form-horizontal fieldset').removeAttr("disabled");
	    		$('#fields-container > fieldset').removeAttr("disabled");
	    		$('#investments-form-container fieldset').attr("disabled","disabled");

	    		$('#btn-stop-edit-investments').hide();
	    		$('#btn-new-investment').hide();
	    		$('#btn-start-edit-investments').show();
			}
		});

		// Investment: On START edit investments
		$(document).off("click", "#btn-start-edit-investments").on("click", "#btn-start-edit-investments", function(e){
			e.preventDefault();
			debug.log("On START edit investments event!!!");

			$('form.form-horizontal fieldset').attr("disabled","disabled");
			$('#fields-container > fieldset').attr("disabled","disabled");
			$('form.form-horizontal fieldset#investment-fieldset').removeAttr("disabled");
			$('#investments-form-container fieldset').removeAttr("disabled");

			$('#btn-stop-edit-investments').show();
			$('#btn-new-investment').show();
			$('#btn-start-edit-investments').hide();
		});

		// On choose contact method
		function onChangeContactMethod(e)
		{
			var idmethod = parseInt($('#contact_method').val());
			debug.log(idmethod, 'On choose contact_method');

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
		/*** END LANDLORD EVENTS *******************************/
		/*******************************************************/

		/*******************************************************/
		/*** INIT **********************************************/
		/*******************************************************/

		// Loading Model
		if(typeof static_data.contact === 'object')
		{
			landlord = new LandlordModel(static_data.contact);
		}
		else
		{
			landlord = new LandlordModel();
		}

		debug.log("Loading form_landlord...");

		// Loading Template
		dust.render("contacts/view/form_landlord", {contact_statuses: static_data.contact_statuses[1],
														landlord_accreditations: static_data.landlord_accreditations,
														contact_titles: static_data.contact_titles,
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

				ko.applyBindings(landlord, document.getElementById('fields-container'));

				// Seleccionamos la organizacion (solo en vista edit)
				if(static_data.view==='edit')
				{
					if(typeof static_data.contact.organisation == 'object')
					{
						if(static_data.contact.organisation.name)
						{
							$(".custom-combobox-input").val(static_data.contact.organisation.name);
						}
					}

				}

				landlord.readyForModifications();

		});

		// Si es investor abrimos el panel investments (solo en vista edit)
		if(static_data.view==='edit')
		{
			if(static_data.contact.is_investor)
			{
				$.each(static_data.contact.investments, function(i, item){
					debug.log('#'+i+' investment: ', item);
					$("#btn-new-investment").trigger('click', [item, true]);
				});
				debug.log('call to landlord[is_investor] change event!!');
				$("input[name=landlord\\[is_investor\\]]").trigger('change');
				$("#btn-stop-edit-investments").trigger('click');
			}
		}

		onChangeContactMethod();

		/*******************************************************/
		/*** END INIT ******************************************/
		/*******************************************************/
	};

});
