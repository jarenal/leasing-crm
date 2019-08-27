define(function(require, exports, module){
    "use strict";

    var ko = require("knockout");
    var debug = require("debug");
    var _ = require("underscore");
    var _string = require("underscore.string");

    /* Necessary for ko.bindingHandlers.uploadFile */
    ko.extenders.myInputFile = function(target, viewModel) {
        debug.log("+++++++++ myInputFile START");

        target.filename = ko.observable("");
        target.permalink = ko.observable("");

        if(typeof target.hasError !== 'function')
        {
            target.hasError = ko.observable();
            target.hasError.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ myInputFile: hasError subscribe!");
            });
        }
        if(typeof target.validationMessage !== 'function')
        {
            target.validationMessage = ko.observable();
            target.validationMessage.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ myInputFile: validationMessage subscribe!");
            });
        }

        debug.log("+++++++++ myInputFile END");
        return target;
    };

    ko.extenders.logChange = function(target, viewModel) {
        debug.log("logChange init");
        target.subscribe(function(newValue) {
            debug.log(viewModel, ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>  subscribe inside logChange executed!");
            viewModel.fields.isModified(true);
        });
        return target;
    };

    ko.extenders.required = function(target, options) {
        debug.log("----------------------------------------- ko.extenders.required START");
        //add some sub-observables to our observable
        if(typeof target.hasError !== 'function')
        {
            target.hasError = ko.observable();
            target.hasError.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ required: hasError subscribe!");
            });
        }
        if(typeof target.validationMessage !== 'function')
        {

            target.validationMessage = ko.observable();
            target.validationMessage.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ required: validationMessage subscribe!");
            });
        }

        if(typeof options==='object')
        {
            if('depend_on' in options && 'validate_if_value' in options)
            {
                target.hasError(false);
                options.depend_on.subscribe(function(newValue){

                    if(newValue==options.validate_if_value)
                    {
                        target.hasError('undefined');
                        target.valueHasMutated();
                    }
                    else
                    {
                        target.hasError(false);
                        target.valueHasMutated();
                    }

                });
            }
        }

        //define a function to do validation
        function validate(newValue)
        {
            debug.log(newValue, "newValue in validate() inside extenders.required....");

            if(typeof options==='object')
            {
                debug.log(options, "extenders.required: options");
                if('depend_on' in options && 'validate_if_value' in options)
                {
                    debug.log(options, "extenders.required: options has depend_on and validate_if_value properties.");
                    if(ko.unwrap(options.depend_on()) == options.validate_if_value)
                    {
                        debug.log(ko.unwrap(options.depend_on()), "extenders.required: options.depend_on() is equal to validate_if_value");
                        target.hasError(newValue ? false : true);
                        target.validationMessage(newValue ? "" : options.message || "This field is required");
                    }
                    else
                    {
                        debug.log(options.depend_on(), "extenders.required: options.depend_on() is NOT equal to validate_if_value");
                        target.hasError(false);
                    }
                }
                else
                {
                    debug.log(options, "extenders.required: options does NOT has depend_on and validate_if_value properties.");

                    if(typeof target() === 'object'){
                        debug.log(target(), "-----------------------++++------------++++----------------------- target() is an object");
                        if(target().length>0){
                            target.hasError(false);
                            target.validationMessage("");
                        } else {
                            target.hasError(true);
                            target.validationMessage(options.message || "This field is required");
                        }
                    }
                    else
                    {
                        debug.log(target(), "-----------------------++++------------++++----------------------- target() is NOT an object");
                        target.hasError(newValue ? false : true);
                        target.validationMessage(newValue ? "" : options.message || "This field is required");
                    }

                }
            }
        }

        //initial validation
        //validate(target());

        //validate whenever the value changes
        target.subscribe(validate);

        debug.log("----------------------------------------- ko.extenders.required END");
        //return the original observable
        return target;
    };

    ko.extenders.files = function(target, options) {

        //add some sub-observables to our observable
        if(typeof target.hasError !== 'function')
        {
            target.hasError = ko.observable();
            target.hasError.subscribe(function(newValue){
                debug.log(newValue, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ files: hasError subscribe!");
            });
        }
        if(typeof target.validationMessage !== 'function')
        {

            target.validationMessage = ko.observable();
            target.validationMessage.subscribe(function(newValue){
                debug.log(newValue, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ files: validationMessage subscribe!");
            });
        }

        //define a function to do validation
        function validate(newValue)
        {
            var self = this;
            if(typeof self !== 'undefined')
            {
                if(self.element.files.length > 0)
                {
                    if(typeof self.element.files[0] === 'object')
                    {
                        // File size validation
                        if(self.element.files[0].size > self.max_size)
                        {
                            var max_size_megas = self.max_size / 1000000; // from bytes to megas.
                            var default_message = "The file is upper to %s MB.";
                            target.hasError(true);
                            target.validationMessage(self.message ? _string.sprintf(self.message, max_size_megas) : _string.sprintf(default_message, max_size_megas));
                            return true;
                        }

                        // Mime-type validation
                        if(typeof _.find(self.allowed_types, function(type){ return type==self.element.files[0].type; }) === 'undefined')
                        {
                            target.hasError(true);
                            target.validationMessage("Warning! file type not allowed. Allowed file types: '"+self.allowed_types.join(', ')+"'");
                            return true;
                        }
                    }
                }
            }

            target.validationMessage("");
            target.hasError(false);

        }

        //initial validation
        validate(target());

        //validate whenever the value changes
        target.subscribe(validate, options);

        //return the original observable
        return target;
    };

    ko.extenders.numeric = function(target, precision) {
        //create a writable computed observable to intercept writes to our observable
        var result = ko.pureComputed({
            read: target,  //always return the original observables value
            write: function(newValue) {
                var current = target(),
                    roundingMultiplier = Math.pow(10, precision),
                    newValueAsNum = isNaN(newValue) ? 0 : parseFloat(+newValue),
                    valueToWrite = Math.round(newValueAsNum * roundingMultiplier) / roundingMultiplier;

                //only write if it changed
                if (valueToWrite !== current) {
                    target(valueToWrite);
                } else {
                    //if the rounded value is the same, but a different value was written, force a notification for the current field
                    if (newValue !== current) {
                        target.notifySubscribers(valueToWrite);
                    }
                }
            }
        }).extend({ notify: 'always' });

        //initialize with current value to make sure it is rounded appropriately
        result(target());

        //return the new computed observable
        return result;
    };

    ko.extenders.isUniqueEmail = function(target, viewModel){

        debug.log('***===***===***===***===***=== ko.extenders.isUniqueEmail: Start');
        function validate(newValue)
        {
            if(newValue && !viewModel.fields.id())
            {
                debug.log(viewModel, "ko.extenders.isUniqueEmail: We have email for to check!");

                viewModel.isUniqueEmail();

            }
        }

        validate(target());
        target.subscribe(validate);

        return target;
    };

    ko.extenders.isUniqueFullname = function(target, viewModel){

        debug.log('***===***===***===***===***=== ko.extenders.isUniqueFullname: Start');
        function validate(newValue)
        {
            if(newValue && !viewModel.fields.id())
            {
                debug.log(viewModel, "ko.extenders.isUniqueFullname: We have fullname for to check!");

                viewModel.isUniqueFullname();

            }
        }

        validate(target());
        target.subscribe(validate);

        return target;
    };

    ko.extenders.matchValues = function(target, options){

        debug.log('***===***===***===***===***=== ko.extenders.matchValues: Start');

        if(typeof target.hasError !== 'function')
        {
            target.hasError = ko.observable();
            target.hasError.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ matchValues: hasError subscribe!");
            });
        }
        if(typeof target.validationMessage !== 'function')
        {

            target.validationMessage = ko.observable();
            target.validationMessage.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ matchValues: validationMessage subscribe!");
            });
        }

        function validate(newValue)
        {
            var observable = options.viewModel.fields[options.targetField];
            debug.log(newValue, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ matchValues: validate: newValue");
            debug.log(options, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ matchValues: validate: options");

            if(typeof observable === 'function')
            {
                debug.log(observable(), "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ matchValues: validate: options.observable()");

                if(newValue && observable())
                {
                    if(newValue !== observable())
                    {
                        target.hasError(true);
                        target.validationMessage(options.message || "The fields doesn't match");
                    }
                    else
                    {
                        target.hasError(false);
                        target.validationMessage("");

                        if(observable.hasError())
                            observable.valueHasMutated();
                    }
                }
            }


        }

        validate(target());
        target.subscribe(validate, options);

        return target;
    };

    ko.extenders.minValidator = function(target, options){

        debug.log('***===***===***===***===***=== ko.extenders.minValidator: Start');

        if(typeof target.hasError !== 'function')
        {
            target.hasError = ko.observable();
            target.hasError.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ minValidator: hasError subscribe!");
            });
        }
        if(typeof target.validationMessage !== 'function')
        {

            target.validationMessage = ko.observable();
            target.validationMessage.subscribe(function(){
                debug.log("¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ minValidator: validationMessage subscribe!");
            });
        }

        function validate(newValue)
        {
            debug.log(newValue, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ minValidator: validate: newValue");
            debug.log(options, "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦ minValidator: validate: options");

            if(newValue)
            {
                if(newValue.length < options.min)
                {
                    target.hasError(true);
                    target.validationMessage(options.message || "This field must be at least "+options.min+" characters long");
                }
                else
                {
                    target.hasError(false);
                    target.validationMessage("");
                }
            }

        }

        validate(target());
        target.subscribe(validate, options);

        return target;
    };

    return true;

});
