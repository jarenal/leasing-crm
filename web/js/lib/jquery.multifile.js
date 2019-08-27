(function($){
	$.fn.multiFile = function(options){

		var self = this;

		var defaults = {
			input: {
				type: "file",
				name: "zoom[fotos][]",
				class: "margin-top-10",
			},
			btnAddRow: {
				class: "mf-btn-add-row"
			},
			btnRemoveRow: {
				class: "mf-btn-remove-row"
			},
			afterAddFile: function(){},
			afterRemoveFile: function(){},
		};

		var opts = $.extend( {}, defaults, options );

		var container = $("<div/>");
		container.attr("class", "files-rows-container");

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

				var container = $("<div/>")
						.attr("data-bind", "css: { 'has-error': fields.name.hasError }");

				var input = $("<input/>")
					.attr("type",opts.input.type)
					.attr("name",opts.input.name)
					//.attr("data-bind", "event: { change: validate }")
					.attr("data-bind", "value: fields.name")
					.addClass(opts.input.class);

				container.append(input);
				container.append('<span class="help-block" data-bind="visible: fields.name.hasError, text: fields.name.validationMessage"></span>');

				var parent = $(this).parents(".plugin-multi-file");

				$(parent).find(".files-rows-container").append(container);

				opts.afterAddFile(container);
			});

			$(document).off('click', '#'+self.attr("id")+' .'+opts.btnRemoveRow.class).on('click', '#'+self.attr("id")+' .'+opts.btnRemoveRow.class, function(e){
				e.preventDefault();
				var parents = $(this).parents('div');
				debug.log(parents, "parents from mf-btn-remove-row");

				var container = parents[3];
				var element = $(container).find('div.btn-file input');
				debug.log("element to remove", element);				
				opts.afterRemoveFile(container, element);
				//$(this).parents('.file-input.file-input-new').remove();
				$(container).remove();
			});
		}

		init();
		return this;
	};
}(jQuery));