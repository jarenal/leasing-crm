define(function(require, exports, module){

	debug_mode = false;

	debug = {
		log: function(obj, message){
			if(debug_mode)
			{
				if(message)
					console.log(obj, message);
				else
					console.log(obj);
			}

		},
		error: function(obj, message){
			if(debug_mode)
			{
				if(message)
					console.error(obj, message);
				else
					console.error(obj);
			}

		},
		warn: function(obj, message){
			if(debug_mode)
			{
				if(message)
					console.warn(obj, message);
				else
					console.warn(obj);
			}

		}
	}

	return debug;
	
	
});
