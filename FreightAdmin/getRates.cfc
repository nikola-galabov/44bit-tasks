component {
	cfcQuote = new models.Quote();
	cfcPickupAndDelivery = new models.PickupAndDelivery();
	// processing the rates from the services and returns a json
	remote string function saveQuote() returnformat="JSON" {
		
		data = deserializeJSON(FORM.json);

		// qOrigin = cfcPickupAndDelivery.getCityByZipCode(data.destPostalCode);
		// qDest = cfcPickupAndDelivery.getLocations(data.originPostalCode);
		
		// writeDump(qOrigin);
		// writeDump(qDest); abort;

		originCity = "";
		originState = "";
		originZipCode = "";
		originAddress = "";
		originCompany = "";
		destCity = "";
		destState = "";
		destZipCode = "";
		destAddress = "";
		destCompany = "";

		// writeDump(data); abort;

		// outbound 
		if(data['typeOfQuote'] == 'outbound') {
			qDest = cfcPickupAndDelivery.getCityByZipCode(data.destPostalCode);

			for(row = 1; row <= qDest.RecordCount; row++) {
				destCity = uCase(left(qDest['city'][row], 1)) & lCase(right(qDest['city'][row], len(qDest['city'][row])-1));
				destState = qDest['state'][row];
				destZipCode = qDest['zipcode'][row];
			}

			qOrigin = cfcPickupAndDelivery.getLocations(data.originPostalCode);

			for(row = 1; row <= qOrigin.RecordCount; row++) {
				originCity = uCase(left(qOrigin['city'][row], 1)) & lCase(right(qOrigin['city'][row], len(qOrigin['city'][row])-1));
				originState = qOrigin['state'][row];
				originZipCode = qOrigin['zipcode'][row];
				originAddress = qOrigin['address'][row];
				originCompany = qOrigin['company'][row];
			}
		}

		if(data['typeOfQuote'] == 'inbound') {
			qOrigin = cfcPickupAndDelivery.getCityByZipCode(data.originPostalCode);
			// writeDump(qOrigin);
			qDest = cfcPickupAndDelivery.getLocations(data.destPostalCode);
			// writeDump(qDest); abort;

			for(row = 1; row <= qOrigin.RecordCount; row++) {
				originCity = uCase(left(qOrigin['city'][row], 1)) & lCase(right(qOrigin['city'][row], len(qOrigin['city'][row]) - 1));
				originState = qOrigin['state'][row];
				originZipCode = qOrigin['zipcode'][row];
			}

			for(row = 1; row <= qDest.RecordCount; row++) {
				destCity = uCase(left(qDest['city'][row], 1)) & lCase(right(qDest['city'][row], len(qDest['city'][row]) - 1));
				destState = qDest['state'][row];
				destZipCode = qDest['zipcode'][row];
				destAddress = qDest['address'][row];
				destCompany = qDest['company'][row];
			}
		}

		if(data['typeOfQuote'] == 'transfer') {
			qOrigin = cfcPickupAndDelivery.getLocations(data.originPostalCode);

			for(row = 1; row <= qOrigin.RecordCount; row++) {
				originCity = uCase(left(qOrigin['city'][row], 1)) & lCase(right(qOrigin['city'][row], len(qOrigin['city'][row])-1));
				originState = qOrigin['state'][row];
				originZipCode = qOrigin['zipcode'][row];
				originAddress = qOrigin['address'][row];
				originCompany = qOrigin['company'][row];
			}

			qDest = cfcPickupAndDelivery.getLocations(data.destPostalCode);

			for(row = 1; row <= qDest.RecordCount; row++) {
				destCity = uCase(left(qDest['city'][row], 1)) & lCase(right(qDest['city'][row], len(qDest['city'][row]) - 1));
				destState = qDest['state'][row];
				destZipCode = qDest['zipcode'][row];
				destAddress = qDest['address'][row];
				destCompany = qDest['company'][row];
			}
		}

			// writeDump(destState); 
			// writeDump(destAddress);
			// writeDump(destCompany);
			// writeDump(originCity);
			// writeDump(originState);
			// writeDump(originAddress);
			// writeDump(originCompany); abort;

		// insert quote and get its id
		quoteId = cfcQuote.InsertQuote(
			pickupDate = data['pickupDate'],
			originPostalCode = data['originPostalCode'],
			destPostalCode = data['destPostalCode'],
			originType = data['originType'],
			destType = data['destType'],
			destCity = destCity,
			destState = destState,
			destAddress = destAddress,
			destCompany = destCompany,
			originCity = originCity,
			originState = originState,
			originAddress = originAddress,
			originCompany = originCompany
		);

		// insert quote items of the quote
		for(var item in data['items']) {
			cfcQuote.insertItem(
				package = item['package'],
				weight = item['weight'],
				hazardous = item['hazardous'],
				height = item['height'],
				length = item['length'],
				freightClass = item['freightClass'],
				width = item['width'],
				quoteId = quoteId,
				quantity = item['pieces']
			);
		}

		// insert quote charges(freight extra) of the quote
		for(var prop in data['charges']){
			cfcQuote.insertQuoteCharges(
				quoteId = quoteId, 
				charge = prop
			);
		}

		// insert quote rates of the quote
		for(var rate in data['rates']) {
			ref = "";
			serviceOption = "";
			interline = "";
			isSelected = 0;

			if(structKeyExists(rate, "ref")) {
				ref = rate['ref'];
			}

			if(structKeyExists(rate, "serviceOption")) {
				serviceOption = rate['serviceOption'];
			}

			if(structKeyExists(rate, "interline")) {
				interline = rate['interline'];
			}

			if(structKeyExists(rate, "selected")) {
				isSelected = 1;
			}

			if(rate['status'] == 'error') {
				cfcQuote.insertRate(
					carrier = rate['carrier'],
					carrierCode = rate['carrierCode'],
					status = rate['status'],
					paymentTerms = rate['paymentTerms'],
					error = rate['error'],
					quoteId = quoteId
				);
			} else {
				cfcQuote.insertRate(
					carrierCode = rate['carrierCode'],
					status = rate['status'],
					paymentTerms = rate['paymentTerms'],
					days = rate['days'],
					interline = interline,
					ref = ref,
					serviceType = rate['serviceType'],
					serviceOption = serviceOption,
					time = rate['time'],
					total = rate['total'],
					quoteId = quoteId,
					isSelected = isSelected
				);
			}
		}

		sResult = { 'quoteId' = quoteId, 'quoteType' = data['typeOfQuote'] };

		return serializeJson(sResult);
	}

	remote string function freightView() returnformat="JSON" {

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