/* version 1.0 */
(function($){
	$.fn.smartcombo = function(options){

		var self = this;

		var defaults = {
			id: "",
			autocompleteselect: null,
			autocompletechange: null,
			autocompleteresponse: null,
			autocompletecreate: null,
			sourceEntity: null,
			ko: null,
			error_handler: null,
			type: null,
			static_data: null,
			withCreate: false,
			showRemoveButton: false
		};

		var opts = $.extend( {}, defaults, options);

		//var $container = $("#"+self.attr("id")).parents("div:first");
		//console.log(self[0], "************************************************************** smartcombo: self");
		var $container = $(self[0]).parents("div:first");
		var $parent = $(self[0]).parents("div").eq(1);
		//console.log($container, "************************************************************* smartcombo: $container");

		function init(){

			switch(opts.type)
			{
				case 'landlords':
					opts.route = 'api_get_landlords_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by name or surname...";
				break;
				case 'others':
					opts.route = 'api_get_others_combobox';
					opts.form = {};
					opts.form.template = 'contacts/view/modal_new_other_contact';
					opts.form.title = 'Create new contact';
					opts.placeholder = "Search by name or surname...";
				break;
				case 'others_and_landlords':
					opts.route = 'api_get_others_and_landlords_combobox';
					opts.form = {};
					opts.form.template = 'contacts/view/modal_new_other_contact';
					opts.form.title = 'Create new contact';
					opts.placeholder = "Search by name or surname...";
				break;
				case 'users':
					opts.route = 'api_get_users_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by name or surname...";
				break;
				case 'tenants':
					opts.route = 'api_get_tenants_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by name or surname...";
				break;
				case 'properties':
					opts.route = 'api_get_properties_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by address, postcode, town, landlord's name or surname...";
				break;
				case 'ticket_related_contacts':
					opts.route = 'api_get_contacts_related_contacts_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by name, surname or type...";
				break;
				case 'tickets':
					opts.route = 'api_get_tickets_combobox';
					opts.form = {};
					opts.form.template = 'common/view/modal_new_ticket';
					opts.form.title = 'Create new task';
					opts.placeholder = "Search by id, title, or type...";
				break;
				case 'contacts':
					opts.route = 'api_get_contacts_combobox';
					opts.form = {};
					opts.form.template = '';
					opts.form.title = '';
					opts.placeholder = "Search by name, surname or type...";
				break;
				case 'local_authorities':
					opts.route = 'api_get_organisations_local_authority_combobox';
					opts.form = {};
					opts.form.template = 'common/view/modal_new_organisation';
					opts.form.title = 'Create new organisation';
					opts.placeholder = "Search by name...";
				break;
				default:
					opts.route = 'api_get_organisations_combobox';
					opts.form = {};
					opts.form.template = 'common/view/modal_new_organisation';
					opts.form.title = 'Create new organisation';
					opts.placeholder = "Search by name...";
				break;
			}

			// Creamos combobox dinamico
			$(self[0]).combobox({id: opts.id, source: Routing.generate(opts.route), showRemoveButton: opts.showRemoveButton, placeholder: opts.placeholder, autocompletecreate: opts.autocompletecreate});


			// Asociamos el evento que se ejecutara cuando se elija una organizacion
			$("#"+opts.id).off("autocompleteselect").on("autocompleteselect", function(e, ui){
				opts.autocompleteselect(e, ui);
			});

			if(opts.autocompletechange)
			{
				$("#"+opts.id).off("autocompletechange").on("autocompletechange", function(e, ui){
					opts.autocompletechange(e, ui);
				});
			}

			if(opts.autocompleteresponse)
			{
				$("#"+opts.id).off("autocompleteresponse").on("autocompleteresponse", function(e, ui){
					opts.autocompleteresponse(e, ui);
				});
			}

			// Plantilla para los botones crear y editar
			var buttons = '<div style="clear:both"></div><div class="col-xs-12 text-right btn-group" role="group">' +
							'<a href="#" class="btn btn-warning btn-sm btn-responsive" role="button" style="display:none;"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit</a>' +
							'<a href="#" class="btn btn-default btn-sm btn-responsive" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create</a>' +
							'</div>';

			// Insertamos los botones en el DOM
			if(opts.withCreate)
			{
				$container.append(buttons);
				//$parent.addClass('smartcombo-with-create'); not necessary, now I use smartcombo-container
			}

			// Asociamos los eventos de los botones
			$container.off("click", "div.btn-group a").on("click", "div.btn-group a", btnGroupHandler);

		}

		/* Gestor de eventos para los botones crear y editar */
		function btnGroupHandler(e){
			e.preventDefault();

			$("#modal-smartcombo").html("");

			dust.render(opts.form.template, {modal_title: opts.form.title, static_data: opts.static_data}, function(err, out) {
				$('#modal-smartcombo').html(out).modal();

				var form_modal = $('#modal-smartcombo .form-modal-popup').get(0);
				// ko binding
				opts.ko.cleanNode(form_modal);
				opts.ko.applyBindings(opts.sourceEntity, form_modal);

				// Save sourceEntity
			    $(document).off('click', '#modal-smartcombo .btn-success').on('click', '#modal-smartcombo .btn-success', function(e){
			    	e.preventDefault();

			    	opts.sourceEntity.save(function(result, textStatus, jqXHR){
			    		//debug.log('result organisation.save()', result);
			    		try
			    		{
							if(result.code)
								throw {code: result.code, message: result.message, errors: result.errors};

							// We reset organisation form and come back to search organisation.
							opts.sourceEntity.reset();

							$('#modal-smartcombo').modal('hide');

							// auto seleccionamos la nueva organizacion creada
					    	if(typeof result.data === "object")
					    	{
					    		$instance = $("#"+opts.id).autocomplete( "instance" );
					    		$instance.menu.option("focus", function(e, ui){
					    			$instance.menu.select();
					    			$instance.menu.option("focus", function(e, ui){});
					    		});

					    		var search_term = "";

					    		if('name' in result.data)
					    			search_term = result.data.name;
					    		else if('title' in result.data)
					    			search_term = result.data.title;
					    		else
					    			search_term = "EMPTY-SEARCH-TERM";

					    		$("#"+opts.id).autocomplete( "search", search_term );
					    	}

			    		}
			    		catch(ex)
			    		{
							opts.error_handler.display(ex, $("#alert-modal-messages"), $('#modal-smartcombo'));
			    		}
			    	});
				});
			});
		}

		function attachEvents(){

		}



		init();

		return this;


	};
}(jQuery));