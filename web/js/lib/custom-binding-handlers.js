define(function(require, exports, module){
    "use strict";

    var ko = require("knockout");
    var debug = require("debug");
    var static_data = require("static_data");
    var error_handler = require("error_handler");
    var smartcombo = require('jquery.smartcombo');
    var OrganisationModel = require("app/model/OrganisationModel");
    var OtherModel = require("app/model/OtherModel");
    var PropertyModel = require("app/model/PropertyModel");
    var ContactModel = require("app/model/ContactModel");
    var LandlordModel = require("app/model/LandlordModel");
    var TenantModel = require("app/model/TenantModel");
    var TicketModel = require("app/model/TicketModel");
    var $ = require("jquery");
    require("fileinput");

    /*********************************************************************************************/
    /** Common
    /*********************************************************************************************/
    ko.bindingHandlers.smartComboLocalAuthorities = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboLocalAuthorities: initialised!!");

            var organisation = new OrganisationModel();
            $(element).smartcombo({id: element.id+"_combobox",
                                    ko: ko,
                                    type: 'local_authorities',
                                    sourceEntity: organisation,
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboLocalAuthorities: autocompleteselect!");
                                        var withCreate = allBindings.get('withCreate');
                                        debug.log(withCreate, "smartComboLocalAuthorities: withCreate value inside autocompleteSelect");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                        //bindingContext.$data.fields.local_authority(ui.item.id);
                                        if(allBindings.get('auto_fill_address'))
                                        {
                                            bindingContext.$data.fields.address(ui.item.address);
                                            bindingContext.$data.fields.postcode(ui.item.postcode);
                                            bindingContext.$data.fields.town(ui.item.town);
                                        }
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboOrganisation = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboOrganisation: initialised!!");

            var organisation = new OrganisationModel();
            $(element).smartcombo({id: element.id+"_combobox",
                                    ko: ko,
                                    type: 'organisations',
                                    sourceEntity: organisation,
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboOrganisation: autocompleteselect!");
                                        var withCreate = allBindings.get('withCreate');
                                        debug.log(withCreate, "smartComboOrganisation: withCreate value inside autocompleteSelect");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                        //bindingContext.$data.fields.local_authority(ui.item.id);
                                        if(allBindings.get('auto_fill_address'))
                                        {
                                            bindingContext.$data.fields.address(ui.item.address);
                                            bindingContext.$data.fields.postcode(ui.item.postcode);
                                            bindingContext.$data.fields.town(ui.item.town);
                                        }
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboOtherContact = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboOtherContact: initialised!!");

            var other = new OtherModel();
            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'others',
                                    sourceEntity: other,
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboOtherContact: autocompleteselect!");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboOtherAndLandlordContact = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboOtherAndLandlordContact: initialised!!");

            var other = new OtherModel();
            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'others_and_landlords',
                                    sourceEntity: other,
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboOtherAndLandlordContact: autocompleteselect!");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboUserContact = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboUserContact: initialised!!");

            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'users',
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboUserContact: autocompleteselect!");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboLandlord = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboLandlord: initialised!!");

            var landlord = new LandlordModel();
            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'landlords',
                                    sourceEntity: landlord,
                                    error_handler: error_handler,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboLandlord: autocompleteselect!");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboTenant = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboTenant: initialised!!");

            var tenant = new TenantModel();
            $(element).smartcombo({id:element.id+"_combobox",
                ko: ko,
                type: 'tenants',
                sourceEntity: tenant,
                autocompleteselect: function(e,ui){
                    debug.log(e, 'smartComboTenant: autocompleteselect!');
                    var value = valueAccessor();
                    value({id: ui.item.id, name: ui.item.value});
                },
                autocompletechange: function(e, ui){
                    debug.log(e, "autocompletechange event!!");
                    if(!ui.item)
                    {
                        var value = valueAccessor();
                        value("");
                    }
                },
                autocompleteresponse: function(e, ui){
                    var value = valueAccessor();
                    value("");
                }
            });
        }
    };

    ko.bindingHandlers.smartComboProperty = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboProperty: initialised!!");

            var property = new PropertyModel();

            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'properties',
                                    sourceEntity: property,
                                    error_handler: error_handler,
                                    static_data: static_data,
                                    autocompleteselect: function(e,ui){
                                        debug.log(ui.item.id, "smartComboProperty: autocompleteselect!");
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboTicket = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboTicket: initialised!!");
            var defaults = {};
            if(allBindings.get('defaults'))
            {
                defaults = allBindings.get('defaults');
            }

            var ticket = new TicketModel(defaults);
            var idsmartcombo = element.id+"_combobox";
            $(element).smartcombo({id: idsmartcombo,
                                    ko: ko,
                                    type: 'tickets',
                                    sourceEntity: ticket,
                                    error_handler: error_handler,
                                    withCreate: allBindings.get('withCreate'),
                                    autocompleteselect: function(e,ui){
                                        debug.log(e, 'smartComboTicket: autocompleteselect!');
                                        var value = valueAccessor();
                                        debug.log(value, "smartComboTicket: value");
                                        value(ui.item.id);
                                    },
                                    autocompletecreate: function(e, ui){
                                        debug.log(e, "_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~_~ autocompletecreate");
                                        if(idsmartcombo in viewModel.fields)
                                        {
                                            debug.log(viewModel.fields[idsmartcombo], 'viewModel.fields[idsmartcombo]');
                                            $(e.target).val(viewModel.fields[idsmartcombo]);
                                        }
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboContact = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "smartComboContact: initialised!!");

            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'contacts',
                                    error_handler: error_handler,
                                    autocompleteselect: function(e,ui){
                                        debug.log(e, 'smartComboContact: autocompleteselect!');
                                        var value = valueAccessor();
                                        value(ui.item.id);
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboTicketsRelatedContacts = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext){
            debug.log(element, "smartComboTicketsRelatedContacts: initialised!!");
            var contact = new ContactModel();
            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'ticket_related_contacts',
                                    sourceEntity: contact,
                                    error_handler: error_handler,
                                    autocompleteselect: function(e,ui){
                                        debug.log(e, 'smartComboTicketsRelatedContacts: autocompleteselect!');
                                        var value = valueAccessor();
                                        value({id: ui.item.id, name: ui.item.value});
                                    }, autocompletechange: function(e, ui){
                                        debug.log(e, "smartComboTicketsRelatedContacts: autocompletechange!!");
                                        if(!ui.item)
                                        {
                                            var value = valueAccessor();
                                            value("");
                                        }
                                    }, autocompleteresponse: function(e, ui){
                                        debug.log(e, "smartComboTicketsRelatedContacts: autocompleteresponse!!");
                                        var value = valueAccessor();
                                        value("");
                                    }
            });
        }
    };

    ko.bindingHandlers.smartComboTicketsRelatedProperties = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext){
            debug.log(element, "smartComboTicketsRelatedProperties: initialised!!");
            var property = new PropertyModel();

            $(element).smartcombo({id:element.id+"_combobox",
                                    ko: ko,
                                    type: 'properties',
                                    sourceEntity: property,
                                    error_handler: error_handler,
                                    autocompleteselect: function(e,ui){
                                        debug.log(e, 'smartComboTicketsRelatedProperties: autocompleteselect!!');
                                        var value = valueAccessor();
                                        value({id: ui.item.id, name: ui.item.value});
                                    }, autocompletechange: function(e, ui){
                                        debug.log(e, "smartComboTicketsRelatedProperties: autocompletechange!!");
                                        debug.log(ui, "autocompletechange ui!!");
                                        if(!ui.item)
                                        {
                                            var value = valueAccessor();
                                            value("");
                                        }
                                    }, autocompleteresponse: function(e, ui){
                                        debug.log(e, "smartComboTicketsRelatedProperties: autocompleteresponse!!");
                                        var value = valueAccessor();
                                        value("");
                                    }
            });
        }
    };

    ko.bindingHandlers.datePickerDueDate = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "datePickerDueDate: initialised!!");
            var options = {changeMonth: true,
                            changeYear: true,
                            minDate: 1, // minDate tomorrow
                            dateFormat: "dd/mm/yy",
                            firstDay: 1, // Monday
                            constrainInput: true,
            };

            $(element).datepicker(options);
            $(element).next().off('click').on('click', function(e){
                e.preventDefault();
                var is_disabled = $(element).attr('disabled');
                if(typeof is_disabled === 'undefined')
                    $(element).datepicker( "show" );
            });
        }
    };

    ko.bindingHandlers.datePickerFreeDate = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "datePickerFreeDate: initialised!!");
            var options = {changeMonth: true,
                            changeYear: true,
                            minDate: null, // Not minDate
                            dateFormat: "dd/mm/yy",
                            firstDay: 1, // Monday
                            constrainInput: true,
            };

            $(element).datepicker(options);
            $(element).next().off('click').on('click', function(e){
                e.preventDefault();
                var is_disabled = $(element).attr('disabled');
                if(typeof is_disabled === 'undefined')
                    $(element).datepicker( "show" );
            });
        },
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "ko.bindingHandlers.datePickerFreeDate new value!");
        }
    };

    ko.bindingHandlers.datePickerMaxDateToday = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(element, "datePickerMaxDateToday: initialised!!");
            var options = {changeMonth: true,
                            changeYear: true,
                            minDate: null, // Not minDate
                            dateFormat: "dd/mm/yy",
                            firstDay: 1, // Monday
                            constrainInput: true,
                            maxDate: "now",
            };

            $(element).datepicker(options);
            $(element).next().off('click').on('click', function(e){
                e.preventDefault();
                var is_disabled = $(element).attr('disabled');
                if(typeof is_disabled === 'undefined')
                    $(element).datepicker( "show" );
            });
        },
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "ko.bindingHandlers.datePickerMaxDateToday new value!");
        }
    };

    ko.bindingHandlers.uploadFile = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            debug.log(bindingContext.$parent, ">>>>>> uploadFile START");
            var target = valueAccessor();
            //target.readonly = ko.observable("");

            /* Set up configuration */
            var file_types = {};
            file_types.documents = ["application/msword",
                                          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                                          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                          "application/vnd.ms-excel",
                                          "application/pdf"];
            file_types.images = ["image/png", "image/jpeg"];
            var allowed_types = file_types.images;

            if(allBindings.get('fileType'))
            {
                switch(allBindings.get('fileType'))
                {
                    case "documents":
                        allowed_types = file_types.documents;
                    break;
                    default:
                        allowed_types = file_types.images;
                    break;
                }
            }

            target.extend({files: {element: element,
                                    max_size: 1000000,
                                    message: "Warning! The file is upper to %s MB.",
                                    allowed_types: allowed_types
                                    }});

            /* Initialising FileInput plugin */
            $(element).fileinput({
                mainClass: "input-upload-file",
                browseClass: "btn btn-warning btn-lg",
                removeClass: "btn btn-danger btn-lg",
                showCaption: true,
                showPreview: false,
                showRemove: true,
                showUpload: false,
            });

            /* Set up UI objects */
            var $parents = $(element).parents('.inputfile-container');
            var $readView = $parents.find('.read-view');
            var $editView = $parents.find('.file-input-new');

            // file_lease_agreement: Computed field for get the selected file from the user file system.
            target.selected = ko.computed(function(){
                debug.log(target, "target in ko.computed");
                var kofield = target();
                debug.log('target: ', kofield);

                if(kofield)
                {
                    // Accessing selected file using File API (HTML5)
                    debug.log($(element).get(0).files, 'selected element.files');
                    return $(element).get(0).files[0];
                }
                else
                {
                    return "";
                }
            });

            target.selected.subscribe(function(newValue){
                debug.log(newValue, "<-><-><-><-><-><-><-><-><-><-> target.selected.subscribe");
            });

            /********************/
            /* EDIT VIEW EVENTS */
            /********************/

            /* Update observable after clear file using remove button in edit view */
            $(element).off('fileclear').on('fileclear', function(e){
                var value = valueAccessor();
                debug.log(value(), 'fileclear: value');
                value("");
            });

            /********************/
            /* READ VIEW EVENTS */
            /********************/

            /* Click on Remove button */
            var $btnRemove = $parents.find('.btn-fileinput-remove');
            debug.log($btnRemove, 'btnRemove');
            $btnRemove.off('click').on('click', function(e){
                e.preventDefault();
                dust.render("common/view/modal_delete_file", {title: 'Caution!!!', filename: target.filename()}, function(err, out) {
                    $('#modal-message-file-deletion').html(out).modal({});

                    /* Click remove confirmation */
                    $(document).off('click', '#btn-confirm-file-deletion').on('click', '#btn-confirm-file-deletion', function(){
                        e.preventDefault();
                        debug.log(bindingContext.$parent, "click on #btn-confirm-file-deletion");
                        $('#modal-message-file-deletion').modal('hide');
                        bindingContext.$parent.deleteFile(function(result, textStatus, jqXHR){
                            try
                            {
                                debug.log('result', result);
                                debug.log('textStatus', textStatus);
                                debug.log('jqXHR', jqXHR);

                                if(result.code)
                                    throw {code: result.code, message: result.message, errors: result.errors};

                                target("");
                                target.filename("");
                                $readView.hide();
                                $editView.show();

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
                        }, function(){ debug.log("An error occurred!"); }, bindingContext.$parent.fields.token(), target.filename());
                    });
                });
            });



            /*************************/
            /* SET UP INITIAL STATUS */
            /*************************/

            /* Set up status depend on readonly observable */
            debug.log(target.filename(), "(1) ~_~ -- ~_~ -- ~_~ -- ~_~ -- Set up status depend on readonly observable: target()");
            if(target.filename())
            {
                $readView.show();
                $editView.hide();
                debug.log("* * * * * * * *  target.hasError to FALSE!");
                //target.hasError(false);
            }

            debug.log(">>>>>> uploadFile END");
        },
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext){
            var value = valueAccessor();
            debug.log(value.filename(), "(3) ~_~ -- ~_~ -- ~_~ -- ~_~ -- uploadFile->update");
            var $parents = $(element).parents('.inputfile-container');
            var $readView = $parents.find('.read-view');
            var $editView = $parents.find('.file-input-new');
            if(value.filename())
            {
                $readView.show();
                $editView.hide();
            }
        }
    };

    /*********************************************************************************************/
    /** TenantModel
    /*********************************************************************************************/
    ko.bindingHandlers.tenantUpdateParking = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "parking value!!!");
            bindingContext.$data.fields.requirements.remove("15");
            bindingContext.$data.fields.requirements.remove("16");
            bindingContext.$data.fields.requirements.push(value.toString());
        }
    };

    ko.bindingHandlers.tenantUpdateGarden = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "garden value!!!");
            bindingContext.$data.fields.requirements.remove("17");
            bindingContext.$data.fields.requirements.remove("18");
            bindingContext.$data.fields.requirements.remove("19");
            bindingContext.$data.fields.requirements.push(value.toString());
        }
    };

    ko.bindingHandlers.tenantUpdateAccessible = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            var value_for_willing = parseInt(value)+7;
            debug.log(value, "accessible value!!!");
            bindingContext.$data.fields.requirements.remove("26");
            bindingContext.$data.fields.requirements.remove("27");
            bindingContext.$data.fields.requirements.remove("28");
            bindingContext.$data.fields.requirements.remove("33");
            bindingContext.$data.fields.requirements.remove("34");
            bindingContext.$data.fields.requirements.remove("35");
            bindingContext.$data.fields.requirements.push(value.toString());
            bindingContext.$data.fields.requirements.push(value_for_willing.toString());
        }
    };

    ko.bindingHandlers.tenantUpdateSmoking = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "smoking value!!!");
            bindingContext.$data.fields.requirements.remove("29");
            bindingContext.$data.fields.requirements.remove("30");
            bindingContext.$data.fields.requirements.push(value.toString());
        }
    };

    ko.bindingHandlers.tenantUpdatePets = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "pets value!!!");
            bindingContext.$data.fields.requirements.remove("31");
            bindingContext.$data.fields.requirements.remove("32");
            bindingContext.$data.fields.requirements.push(value.toString());
        }
    };

    /*********************************************************************************************/
    /** PropertyModel
    /*********************************************************************************************/
    ko.bindingHandlers.propertyUpdateBuyToLetPermitted = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            if(parseInt(value))
            {
                $("#box-buy-to-let-permitted").slideDown(250);
            }
            else
            {
                $("#box-buy-to-let-permitted").slideUp(250, function(){
                    bindingContext.$data.fields.buy_to_let("0");
                });
            }
        }
    };

    ko.bindingHandlers.propertyUpdateParking = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "parking value!!!");
            debug.log(bindingContext, "bindingContext in updateParking!!!");
            bindingContext.$data.fields.features.remove("15");
            bindingContext.$data.fields.features.remove("16");
            bindingContext.$data.fields.features.push(value.toString());
        }
    };

    ko.bindingHandlers.propertyUpdateAccessible = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "accessible value!!!");
            bindingContext.$data.fields.features.remove("26");
            bindingContext.$data.fields.features.remove("27");
            bindingContext.$data.fields.features.remove("28");
            bindingContext.$data.fields.features.push(value.toString());
        }
    };

    ko.bindingHandlers.propertyUpdateWillingToAdapt = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "willing adapt value!!!");
            bindingContext.$data.fields.features.remove("33");
            bindingContext.$data.fields.features.remove("34");
            bindingContext.$data.fields.features.remove("35");
            bindingContext.$data.fields.features.push(value.toString());
        }
    };

    ko.bindingHandlers.propertyUpdateSmoking = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "smoking value!!!");
            bindingContext.$data.fields.features.remove("29");
            bindingContext.$data.fields.features.remove("30");
            bindingContext.$data.fields.features.push(value.toString());
        }
    };

    ko.bindingHandlers.propertyUpdatePets = {
        update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            var value = ko.unwrap(valueAccessor());
            debug.log(value, "pets value!!!");
            bindingContext.$data.fields.features.remove("31");
            bindingContext.$data.fields.features.remove("32");
            bindingContext.$data.fields.features.push(value.toString());
        }
    };

    return true;

});
