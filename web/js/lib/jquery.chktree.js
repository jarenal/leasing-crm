/* version 1.0 */
(function($){
	$.fn.chktree = function(options){

		var self = this;

		var defaults = {
			container: {
				class: "row",
			},
			li_parent: {
				class: "col-md-12 margin-top-20",
			},
			li_child: {
				class: "col-md-4 margin-top-10",
			},
			li_other: {
				class: "col-md-12 margin-top-10",
			},
			label: {
				class: "",
			},
			input_chk: {
				id: "chktree_box_",
				type: "checkbox",
				name: "chktree[box]",
				data_bind: "checked: fields.conditions"
			},
			input_txt: {
				id: "other_",
				type: "text",
				name: "other",
				disabled: "disabled",
				class: "form-control form-control-50 margin-left-20",
				data_bind: "value: fields.other_"
			},
			chklist: [],
		};

		var opts = $.extend( {}, defaults, options);

		var container = $("<div/>")
							.addClass(opts.container.class);

		function init(){

			var list_container_root = $("<ul/>");

			$.each(opts.chklist, function(index, item){

				var list_item_root = $("<li/>")
										.addClass(opts.li_parent.class);

				var checkbox_root = createCheckbox(item);

				list_item_root.append(checkbox_root);


				if(item.children.length>0)
				{
					var list_container_child = $("<ul/>");
					var list_item_child = $("<li/>");

					$.each(item.children, function(c_index, c_item){
						list_item_child = $("<li/>")
											.addClass(c_item.is_other?opts.li_other.class:opts.li_child.class);
						var checkbox_child = createCheckbox(c_item, true);
						list_item_child.append(checkbox_child);
						list_container_child.append(list_item_child);
					});

					list_item_root.append(list_container_child);

				}


				list_container_root.append(list_item_root);


			});

			container.append(list_container_root);

			self.append(container);
		}

		function createCheckbox(item, is_disabled){

			var idcheckbox = opts.input_chk.id+item.id;
			var container = $("<div/>");
			var label = $("<label/>").addClass(opts.label.class);
			var input_chk = $("<input/>")
							.attr("id", idcheckbox)
							.attr("type", opts.input_chk.type)
							.attr("name", opts.input_chk.name)
							.attr("value", item.id)
							.attr("data-bind", opts.input_chk.data_bind);
			if(is_disabled)
			{
				label.addClass("disabled");
				input_chk.attr("disabled","disabled");
			}
			label.append(input_chk);
			label.append(item.name);

			container.append(label);

			if(item.is_other)
			{
				var input_txt = $("<input/>")
								.attr("id", opts.input_txt.id+item.id)
								.attr("type", opts.input_txt.type)
								.attr("name", opts.input_txt.name+"["+item.id+"]")
								.attr("disabled", opts.input_txt.disabled)
								.addClass(opts.input_txt.class)
								.attr("data-bind", opts.input_txt.data_bind+item.id);
				if(is_disabled)
					input_txt.attr("disabled","disabled");
				container.append(input_txt);
			}

			$(document).off("click", "#"+idcheckbox).on("click", "#"+idcheckbox, $.fn.chktree.clickHandler);

			return container;

		}

		init();

		return this;


	};

	$.fn.chktree.refresh = function(target){

		var elements = $('input[id^="'+target+'"]');
		$.each(elements, function(index, item){
			$.fn.chktree.clickHandler.call(item);
			$('#support-fieldset label.cbx-label input').checkboxX("refresh");
		});

	};

	$.fn.chktree.clickHandler = function(e){

		var children_container = $(this).parents("li:first").find("ul");

		var is_checked = $(this).is(':checked');

		// to manage children behaviour
		if(children_container.length)
		{
			if(is_checked)
			{
				children_container.find("input:checkbox").removeAttr("disabled");
				children_container.find("label").removeClass("disabled");
			}
			else
			{
				children_container.find("input:checkbox:checked").trigger("click");
				children_container.find("input").attr("disabled","disabled");
				children_container.find("input:text").val("");
				children_container.find("label").addClass("disabled","disabled");
			}
		}

		// to manage other input behaviour
		var my_container = $(this).parents("div:eq(1)");
		if(my_container.find("input:text").length)
		{
			// is checked?
			if(is_checked)
			{
				my_container.find("input:text").removeAttr("disabled");
			}
			else
			{
				my_container.find("input:text").attr("disabled","disabled");
				my_container.find("input:text").val("");
			}
		}
	};

}(jQuery));