(function($){
	$.fn.multiTenant = function(options){

		var self = this;

		var defaults = {
			input: {
				type: "hidden",
				name: "property[tenants][]",
			},
			btnAddRow: {
				class: "mf-btn-add-row"
			},
			btnRemoveRow: {
				class: "btn-remove-tenant"
			},
			afterAddTenant: function(){},
			beforeRemoveTenant: function(){},
			afterRemoveTenant: function(){},
		};

		var opts = $.extend( {}, defaults, options );

		var container = $("<div/>");
		container.attr("class", "tenants-rows-container");

		var btnAddRowTpl = '<div class="col-sm-12 col-xs-12 text-right btn-group margin-top-10" role="group">' +
            					'<a href="#" class="btn btn-success btn-sm btn-responsive '+opts.btnAddRow.class+'" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add new one</a>' +
          						'</div>';

		var btnAddRow = $(btnAddRowTpl);

		function init(){
			self.append(container);
			self.append(btnAddRow);
			attachEvents();
			self.find("."+opts.btnAddRow.class).trigger("click");
		}

		function attachEvents(){
			$(document).off('click', '#'+self.attr("id")+' .'+opts.btnAddRow.class).on('click', '#'+self.attr("id")+' .'+opts.btnAddRow.class, function(e){
				e.preventDefault();

				var container = $("<div/>").addClass("smartcombo-container");

				var input = $("<input/>")
					.attr("type",opts.input.type)
					.attr("name",opts.input.name)
					//.attr("data-bind", "event: { change: validate }")
					//.attr("data-bind", "value: fields.name")
					.addClass(opts.input.class);

				container.append(input);
				//container.append('<span class="help-block" data-bind="visible: fields.name.hasError, text: fields.name.validationMessage"></span>');

				var parent = $(this).parents(".plugin-multi-tenant");

				$(parent).find(".tenants-rows-container").append(container);

				opts.afterAddTenant(container);
			});

			$(document).off('click', '#'+self.attr("id")+' .'+opts.btnRemoveRow.class).on('click', '#'+self.attr("id")+' .'+opts.btnRemoveRow.class, function(e){
				e.preventDefault();

				var parents = $(this).parents('div');
				debug.log(parents, "parents from btn-remove-tenant");

				var container = parents[0];
				debug.log(container, "=============================================== plugin-multi-tenant remove container");

				opts.beforeRemoveTenant($(container).find('.custom-combobox-input')[0]);

				opts.afterRemoveTenant(container);
				$(container).remove();
			});
		}

		init();
		return this;
	};
}(jQuery));