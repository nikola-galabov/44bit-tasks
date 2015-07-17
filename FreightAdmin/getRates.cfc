component {

	// processing the rates from the services and returns a json
	remote string function getRates() returnformat="JSON" {
		// process the data

		// call the services

		// process the results

		// return the final result
		//return SerializeJson(FORM);
	}

	// functions connecting with the webservices

	remote string function freightView() returnformat="JSON" {
		// real api key 14943a6e67bc0b468ee4ef05b169a1e489c11f15ab7
		// my api key 14e247201d2950bc1ce1d7b1fa39a57d1c5278014a6
		credentials = ToBase64("14943a6e67bc0b468ee4ef05b169a1e489c11f15ab7:");
	    httpService = new http();

	    /* set attributes using implicit setters */ 
	    httpService.setMethod("post"); 
	    httpService.setUrl("https://www.freightview.com/api/v1/rates");
	    /* add httpparams using addParam() */
	    httpService.clearParams(); 
	    /* add httpparams using addParam() */ 
	    httpService.addParam(type="header",name="Authorization", value="Basic #credentials#");
	    httpService.addParam(type="header",name="Content-type",value="application/json");
	    httpService.addParam(type="body", value='#FORM.json#');
	    /* make the http call to the URL using send() */
	    result = httpService.send().getPrefix(); 
	    /* process the filecontent returned */ 
	    return result.filecontent;
	}
}