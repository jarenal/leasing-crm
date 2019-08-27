function propertiesRowStyles(row, index){
	switch(row.idstatus)
	{
		case 1: // Available - New to LFL
			return {classes: 'success'};
		case 2: // Fully occupied - LFL tenants
			return {classes: 'danger'};
		case 3: // Ineligible
			return {classes: 'active'};
		case 4: // Available and void
			return {classes: 'success'};
		case 5: // Rooms available
			return {classes: 'success'};
		case 6: // Fully occupied - Not with LFL
			return {classes: 'danger'};
		default: // Rest of cases
			return {classes: 'active'};
	}
}

function tasksRowStyles(row, index){
	switch(row.idstatus)
	{
		case 1: // Outstanding
			return {classes: 'danger'};
		case 2: // On hold
			return {classes: 'warning'};
		case 3: // Completed
			return {classes: 'success'};
		default: // rest of cases
			return {classes: 'active'};
	}
}

/*******************************************************/
/*** FILES TABLE ***************************************/
/*******************************************************/
function operateFilesFormatter(value, row, index) {
    return [
			'<div class="btn-toolbar">',
			'<a href="#" class="btn btn-default btn-xs btn-open-file"><i class="glyphicon glyphicon-eye-open"></i></a>',
		    '</div>'
    ].join('');
}

function showImage(value, row, index){
    return '<img src="'+value+'" width="60px" />';
}

var operateFilesEvents = {
    'click .btn-open-file': function (e, value, row, index) {
    	e.preventDefault();
		var newURL = window.location.origin + row.path;
        window.open(newURL, "_blank");
    },
};

function avoidExitWithoutSave(newValue){
	var self = this;

	if(newValue)
	{
		debug.log('€€€€€€€€€€€€€€€€€€€€€€€€€€€€ avoidExitWithoutSave: We create event beforeunload €€€€€€€€€€€€€€€€€€€€€€€€€€€€');
		$(window).off('beforeunload').on('beforeunload', function(e){
			var confirmationMessage = "There are unsaved changes, are you sure you want to leave?";
			if(self.fields.isModified())
			{
				e.preventDefault();
				debug.log('beforeunload event!!');
				e.returnValue = confirmationMessage;     // Gecko, Trident, Chrome 34+
				return confirmationMessage;              // Gecko, WebKit, Chrome <34
			}
		});
	}
}

function keepAlive(){
    var url = Routing.generate('api_post_user_keepalive');
    $.ajax(url, {
        type: 'post',
        accepts: 'application/json',
        dataType: 'json',
        error: function(){
            debug.log('keepAlive error');
            window.location=Routing.generate('app_backend_dashboard_index');
        },
        success: function(){
            debug.log('keepAlive success');
        },
        cache: false,
        data: {},
        processData: true,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    });
}