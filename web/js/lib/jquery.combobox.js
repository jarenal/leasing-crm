(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox input-group" )
          .insertAfter( this.element );

        //this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },

      _createAutocomplete: function() {
        //var selected = this.element.children( ":selected" ),
        //  value = selected.val() ? selected.text() : "";

        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .attr("id", this.options.id)
          .val( "" )
          .attr( "title", "" )
          .attr("placeholder", this.options.placeholder?this.options.placeholder:"Search by name...")
          .addClass( "custom-combobox-input form-control" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: this.options.source,
            autoFocus: true,
            create: this.options.autocompletecreate
            //source: $.proxy( this, "_source" )
          })
          .uitooltip({
            tooltipClass: "ui-state-highlight"
          });

        this._on( this.input, {
          autocompleteselect: function( event, ui ) {

            //ui.item.option.selected = true;
            /*
            this._trigger( "select", event, {
              item: ui.item.option
            });*/
            // No necessary, now I manage this event externally.
            //this.options.viewModel.fields.organisation(ui.item.id);
          },
          autocompletechange: "_removeIfInvalid"
        });
      },
/* ORIGINAL SHOW ALL BUTTON
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;

        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .uibutton({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();

            // Close if already visible
            if ( wasOpen ) {
              return;
            }

            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },*/

      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;

          $container = $("<span/>");
          $container.addClass("input-group-btn");

          $button = $("<button/>");
          $button.addClass("btn btn-default btn-lg");

          $button.mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          });
          $button.click(function(e) {
            e.preventDefault();
            input.focus();

            // Close if already visible
            if ( wasOpen ) {
              return;
            }

            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });

          $icon = $("<span/>");
          $icon.addClass("glyphicon glyphicon-triangle-bottom");

          $button.append($icon);
          $container.append($button);

          if(this.options.showRemoveButton)
          {
            $icon_remove = $("<span/>");
            $icon_remove.addClass("glyphicon glyphicon-remove");

            $button_remove = $("<button/>");
            $button_remove.addClass("btn btn-danger btn-lg btn-remove-tenant");

            $button_remove.append($icon_remove);
            $container.append($button_remove);
          }

          $container.appendTo(this.wrapper);

      },
/*
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },*/

      _removeIfInvalid: function( event, ui ) {

        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }

        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });

        // Found a match, nothing to do
        if ( valid ) {
          return;
        }

        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .uitooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.uitooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },

      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( $ );