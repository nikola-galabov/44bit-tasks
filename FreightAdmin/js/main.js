// TODO List:
// - I have to make descriptive comments
// - Find solution - how to pass the data of the remote select2 requests to the new form (items)
// - Remote request to build the packaging and freight class, a lot of bugs in add new item
// ------------------------------

$(document).ready(function(){	
	// init libraries, attach events, setup for the page's elements
	generalSetup();
	// custom values of the fields
	fillWithCustomValues();
	// jQuery validate
	validation();
});


// !!!!!!!!!!!!VERY IMPORTANT - I have to disable the option to click on the rate's button if one of the input's value is changed!

function generalSetup() {
	var $originType = $('select[name=originType]');
	var $destType = $('select[name=destType]');
	var $liftGateOriginContainer = $('#liftgate-origin-container');
	var $liftGateDestContainer = $('#liftgate-dest-container');

	// add new item
	$('#add-item').click(addNewItem);

	// jquery tabs
	$( "#tabs" ).tabs();

	// Show/hide Liftgate and inside pickup options 
	$originType.change(function() {
		if($originType.val() !== 'business no dock') {
			$liftGateOriginContainer.hide();
		} else {
			$liftGateOriginContainer.show();
		}
	});

	$destType.change(function() {
		if($destType.val() !== 'business no dock') {
			$liftGateDestContainer.hide();
		} else {
			$liftGateDestContainer.show();
		}
	});

	// items validation
	$( "#tabs" ).tabs({
		beforeActivate: function(event) {
            var valid = true;
            var currentActive = $('li.ui-state-active').attr('aria-controls');

            $('#' + currentActive + ' .item-input').each(function() {
            	if( ! $(this).valid() ) {
            		valid = false;
            	}
            });

            // If the form isn't valid, prevent the tab from changing
            if(!valid) {
               event.preventDefault();
            }
        }
	});

	// Select2
	$('select').select2();
	function formatResult (item) {
		if (item.loading) {
			return item.text;
		}

		var markup = item.city + ', ' + item.state + ' ' + item.zipCode;

		return markup;
	}

	function formatSelection (item) {
		return item.fullAddress || item.text;
	}

	$('.select2-ajax').select2({
		ajax: {
			url: "/secure/freightadmin/models/pickupanddelivery.cfc?method=getCities",
			dataType: 'json',
			delay: 250,
			width: '100%',
			data: function (params) {
				return {
					input: params.term, // search term
				};
			},
			processResults: function (data, params) {
				return {
				  	results: data.items,
				};
			},
			cache: true
		},
		escapeMarkup: function (markup) { 
			return markup; 
		},
		minimumInputLength: 1,
		templateResult: formatResult,
		templateSelection: formatSelection
	});

	// Toggle Accessorials and services
	$('#toggle-accessorials').click(function() {
		if($(this).text() === 'Hide accessorials') {
			$(this).text('Show accessorials');
		} else {
			$(this).text('Hide accessorials');
		}
		
		$('#accessorials').toggle();
	});

	// Remove tabs
	$('.btn-remove').click(function() {
		console.log($(this).attr('id'));
	});

	// calculator
	$('.calculation').on('input', function() {
    	buildCalculator(1);
	});
}

function fillWithCustomValues() {
	// Set pick up date to today
	Date.prototype.toDateInputValue = (function() {
	    var local = new Date(this);
	    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
	    return local.toJSON().slice(0,10);
	});

    $('input[type=date]').val(new Date().toDateInputValue());

    // Set values to lenght width and height
    fillLengthWidtAndHeight(1);
    $('#package-item-1').change(function() {
    	fillLengthWidtAndHeight(1)
    });

   	// Change packaging on length/width change
    $('#length-item-1').change(function() {
    	changePackaging(1);
    });

    $('#width-item-1').change(function() {
    	changePackaging(1);
    });
}

function fillLengthWidtAndHeight(index) {
	var packagingValue = $('#package-item-' + index).val();
	var $lenght = $('#length-item-' + index);
	var $width = $('#width-item-' + index);
	var $height = $('#height-item-' + index);

	switch(packagingValue) {
		case "Pallets_42x42":
			$lenght.val('42');
			$width.val('42');
			$height.val('48');
		break;
		case "Pallets_other":
			$lenght.val('');
			$width.val('');
			$height.val('');
		break;
		case "45/45":
			$lenght.val('45');
			$width.val('45'); 
		break;
		case "Pallets_48x40":
			$lenght.val('48');
			$width.val('40'); 
		break;
		case "Pallets_48x48":
			$lenght.val('48');
			$width.val('48'); 
		break;
		case "Pallets_60x48":
			$lenght.val('60');
			$width.val('48'); 
		break;
	}
}

function changePackaging(index) {
	var $packaging = $('#package-item-' + index);
	var lengthVal = parseInt( $('#length-item-' + index).val() );
	var widthVal = parseInt( $('#width-item-' + index).val() );
	var value = "Pallets_42x42";

	if(lengthVal === 42 && widthVal === 42) {
		value = "Pallets_42x42";
	} else if(lengthVal === 48 && widthVal === 40) {
		value = "Pallets_48x40";
	} else if(lengthVal === 48 && widthVal === 48) {
		value = "Pallets_48x48";
	} else if(lengthVal === 60 && widthVal === 48) {
		value = "Pallets_60x48";
	} else {
		value = "Pallets_other";
	}
	
	$packaging.val(value);
	$('#s2id_package-item-1').find('span.select2-chosen').text(value);
}

function addNewItem() {
	var	valid = true;

	$('.item-input').each(function() {
    	if( ! $(this).valid() ) {
    		valid = false;
    	}
    });

    if(!valid) {
    	return;
    }

	var counter = $('.item').length + 1;
	var postFix = '-item-' + counter;
	var $itemContainer = $('<div class="item" id="item-' + counter + '">');
	var $row = $('<div class="row">');

	// Starting to build the html of the new item's form
	$itemContainer.append($row);

	// Packaging
	var $packaging = $('<div class="form-group">')
		.append(
			'<label for="package' + postFix + '">Packaging</label>' +
			'<select class="form-control" name="package' + postFix + '" id="package' + postFix + '" required>' +
				'<option value="42/42" selected="selected">Pallets (42x42)</option>' +
				'<option value="45/45">Pallets (45x45)</option>' +
				'<option value="48/40">Pallets (48x40)</option>' +
				'<option value="48/48">Pallets (48x48)</option>' +
				'<option value="60/48">Pallets (60x48)</option>' +
				'<option value="bags">Bags</option>' +
				'<option value="bales">Bales</option>' +
				'<option value="boxes">Boxes</option>' +
				'<option value="bundles">Bundles</option>' +
			'</select>'
			);

	$row.append($packaging);

	// Quantity
	var $quantity = $('<div class="form-group col-md-4 col-xs-4 padding-left-none">')
		.append(
			'<label for="quantity' + postFix + '">Quantity</label>' +
			'<input class="form-control calculation item-input" type="number" min=0 name="quantity' + postFix + '" id="quantity' + postFix + '" value="1" required>'
		);
	$quantity.appendTo($row);	

	// Weight
	var $weight = $('<div class="form-group col-md-4 col-xs-4">')
		.append(
			'<label for="weight' + postFix + '">Weight</label>' +
			'<input class="form-control calculation item-input" type="number" name="weight' + postFix + '" id="weight' + postFix + '" required>'
		);
	$weight.appendTo($row);

	// Lbs/Kg
	var $weightUnit = $('<div class="form-group col-md-4 col-xs-4 padding-right-none">')
		.append(
			'<label for="weight-unit' + postFix + '">Lbs/Kg</label>' +
			'<select class="form-control" name="weightUnit' + postFix + '" id="weight-unit' + postFix + '">' +
				'<option value="lbs">Lbs</option>' +
				'<option value="kg">Kg</option>' +
			'</select>'
		);
	$weightUnit.appendTo($row);

	// length
	var $length = $('<div class="form-group col-md-3 col-xs-3 padding-left-none clear-both">')
		.append(
			'<label for="length' + postFix + '">Length</label>' +
			'<input class="form-control calculation item-input" type="number" name="length' + postFix + '" id="length' + postFix + '" required>'
		);
	$length.appendTo($row);

	// width
	var $width = $('<div class="form-group col-md-3 col-xs-3">')
		.append(
			'<label for="width' + postFix + '">Width</label>' +
			'<input class="form-control calculation item-input" type="number" name="width' + postFix + '" id="width' + postFix + '" required>'
		);
	$width.appendTo($row);

	// height
	var $height = $('<div class="form-group col-md-3 col-xs-3">')
		.append(
			'<label for="height' + postFix + '">Height</label>' +
			'<input class="form-control calculation item-input" type="number" name="height' + postFix + '" id="height' + postFix + '" required>'
		);
	$height.appendTo($row);

	// In/cm
	var $lengthUnit = $('<div class="form-group col-md-3 col-xs-3 padding-right-none">')
		.append(
			'<label for="length-unit' + postFix + '">In/Cm</label>' +
			'<select class="form-control" name="lengthUnit' + postFix + '" id="length-unit' + postFix + '" required>' +
				'<option value="in" selected="selected">in</option>' +
				'<option value="cm">cm</option>' +
			'</select>'
		);
	$lengthUnit.appendTo($row);

	// Freight class
	var $freightClass = $('<div class="form-group col-md-4 col-xs-4 padding-left-none">')
		.append(
			'<label for="freight-class' + postFix + '">Freight class</label>' +
			'<select class="form-control" name="freightClass' + postFix + '" id="freight-class' + postFix + '">' +
				'<option value="55" selected="selected">55</option>' +
				'<option value="50">50</option>' +
				'<option value="60">60</option>' +
				'<option value="65">65</option>' +
				'<option value="70">70</option>' +
				'<option value="77.5">77.5</option>' +
				'<option value="85">85</option>' +
				'<option value="92.5">92.5</option>' +
				'<option value="100">100</option>' +
				'<option value="110">110</option>' +
				'<option value="125">125</option>' +
				'<option value="150">150</option>' +
				'<option value="175">175</option>' +
				'<option value="200">200</option>' +
				'<option value="250">250</option>' +
				'<option value="300">300</option>' +
				'<option value="400">400</option>' +
				'<option value="500">500</option>' +
			'</select>'
		);
	$freightClass.appendTo($row);

	// Hazmat?
	var $hazmat = $('<div class="form-group col-md-6 col-xs-6">')
		.append(
			'<label>Hazmat?</label>' +
			'<div>' +
				'<div class="col-md-6 col-xs-6 col-sm-6">' +
					'<input type="radio" name="hazmat' + postFix + '" id="yes' + postFix + '" value="true">' +
					' <label for="yes' + postFix + '">Yes</label>' +
				'</div>' +
				'<div class="col-md-6 col-xs-6 col-sm-6">' +
					'<input type="radio" name="hazmat' + postFix + '" id="no' + postFix + '" value="false" checked="checked">' +
					' <label for="no' + postFix + '">No</label>' +
				'</div>' +
			'</div>'
		);
	$hazmat.appendTo($row);

	$itemContainer.appendTo('#tabs');

	// refresh select2 and validate
	$('select').not('.select2-ajax').select2();
	validation(counter);

	// Append tab
	var $li = $('<li>');	
	
	$li.append($('<a href="#item-' + counter + '">Item ' + counter + '</a>'));
	$li.append(
			$('<button id="remove-item-' + counter + '" type="button" class="btn-remove">' +
					'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' +
				'</button>'));

	$('#tabs ul').append($li);

	$('#tabs').tabs('refresh');
	$('#tabs').tabs('option', 'active', counter - 1);
	
	// remove tab event
	$('#tabs').tabs().delegate('#remove-item-' + counter, "click", function() {
		var panelId = $( this ).closest('li').remove().attr("aria-controls");
		$('#' + panelId ).remove();
		$('#tabs').tabs('refresh');
	});

	$('.calculation').on('input', function() {
    	buildCalculator(counter);
	});

	// Default values of length width and height
	fillLengthWidtAndHeight(counter);
    $('#package-item-' + counter).change(function() {
    	fillLengthWidtAndHeight(counter);
    });

    // Change packaging on length/width change
    $('#length-item-' + counter).change(function() {
    	changePackaging(counter);
    });

    $('#width-item-' + counter).change(function() {
    	changePackaging(counter);
    });
}

function buildCalculator(counter) {
	var INCHES_TO_FEET = 0.083333;
	var width = INCHES_TO_FEET * getInchesFromInput('width', counter);
	var height = INCHES_TO_FEET * getInchesFromInput('height', counter);
	var length = INCHES_TO_FEET * getInchesFromInput('length', counter);
	var weight = getPoundsFromInput('weight', counter);
	var quantity = parseInt($('input[name=quantity-item-' + counter + ']').val());
	var feet = quantity * Math.round(width * height * length * 100) / 100;
	var poundsPerCubic = Math.round( (weight * quantity / (quantity * feet)) * 100) / 100;

	var $calculatorContainer = $('<div class="calculator-container">');
	var $cubicFeet = $('<div class="cubic-feet">');
	var $poundsPerCubic = $('<div class="pounds-per-cubic">');

	// if there was a calculator it will be removed
	$('#item-' + counter + ' .calculator-container').remove();
	if(feet > 0) {
		$cubicFeet.text(feet.toFixed(2) + ' cubic feet');
		$poundsPerCubic.text(poundsPerCubic.toFixed(2) + ' pounds per cubic feet');
		$calculatorContainer.append($('<img src="/secure/FreightAdmin/images/calculator.png">'))
		$calculatorContainer.append($cubicFeet);
		$calculatorContainer.append($poundsPerCubic);
		$('#item-' + counter).append($calculatorContainer);
	} 
}

function validation(counter) {
	// default value for the counter is 1
	counter = typeof counter !== 'undefined' ? counter : 1;
	var messages = {
		shipFrom: {
			required: 'The origin is required!'
		},
		pickUpDate: {
			required: 'The pick up date is required!'
		},
		shipTo: {
			required: 'The destination is required!'
		}
	};

	var rulesObject = {};

	rulesObject['quantity-item-' + counter] = {
		required: true,
		min: 0,
	};

	rulesObject['weight-item-' + counter] = {
		required: true,
		min: 0,
	};

	rulesObject['length-item-' + counter] = {
		required: true,
		min: 0,
	};

	rulesObject['width-item-' + counter] = {
		required: true,
		min: 0,
	};

	rulesObject['height-item-' + counter] = {
		required: true,
		min: 0,
	};

	for(prop in rulesObject) {
		messages[prop] = {};

		if(rulesObject[prop]['required']) {
			messages[prop]['required'] = $('label[for=' + prop + ']').text() + " is required!";
		}

		if(rulesObject[prop]['min'] != undefined) {
			messages[prop]['min'] = $('label[for=' + prop + ']').text() + " has to be a positive integer!";
		}

		if(counter !== 1) {
			rulesObject[prop]['messages'] = { 
				required: $('label[for=' + prop + ']').text() + " is required!",
				min: $('label[for=' + prop + ']').text() + " has to be a positive integer!"
			}

			$('#' + prop).rules("add", rulesObject[prop]);
		}
	}

	$('#quote').validate({
		rules: rulesObject,
		messages: messages,
		submitHandler: function() {
			// remove the rate containers, disable the submit button and display the loading image
			$('#rates').remove();
			$('#rates-error').remove();
			$('input[type=submit]').attr('disabled', 'disabled');
			$('#content').append($('<img id="loading-image" src="/secure/freightadmin/images/loading.gif">'));

			// test
			var exampleData = '{"pickupDate":"2015-07-15","originPostalCode":"95403","destPostalCode":"89011","origintType":"business no dock","destType":"business dock","items":[{"weight":2000,"freightClass":55,"length":42,"width":42,"height":48,"package":"Pallets_other","pieces":3,"hazardous":false}],"charges":["liftgate pickup"],"rates":[{"carrierCode":"odfl","carrier":"Old Dominion Freight Line","paymentTerms":"Inbound Collect","serviceType":"standard","status":"error","ref":"0","interline":false,"error":"Customer not autorated","time":462},{"carrierCode":"odfl","carrier":"Old Dominion Freight Line","paymentTerms":"Inbound Collect","serviceType":"guaranteed","status":"error","ref":"0","interline":false,"error":"Customer not autorated","time":462},{"carrierCode":"wtva","carrier":"Wilson Trucking","paymentTerms":"Outbound Prepaid","status":"error","error":"Unable to quote jointline rates.  Please call the Wilson Trucking Corporation General Office at 1-540-949-3200 for a rate quote.  Thank you."},{"carrierCode":"abfs","carrier":"ABF Freight System","paymentTerms":"Inbound Collect","serviceType":"standard","status":"error","interline":true,"error":"Our quote system was not able to complete your quote automatically.","time":589},{"carrierCode":"pyle","carrier":"A Duie Pyle","paymentTerms":"Third Party Prepaid","serviceType":"standard","status":"error","error":"Your pickup location is not in our service area.","time":722},{"carrierCode":"aact","carrier":"AAA Cooper Transportation","paymentTerms":"Third Party Prepaid","serviceType":"standard","status":"error","interline":false,"error":"No Service Available for Specified Points","time":879},{"total":515.77,"carrierCode":"rlca","carrier":"R+L Carriers","paymentTerms":"Inbound Collect","serviceType":"standard","status":"ok","ref":"15543043","days":2,"time":1332},{"total":596.22,"carrierCode":"rlca","carrier":"R+L Carriers","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"17","status":"ok","ref":"15543043","days":2,"time":1333},{"total":636.45,"carrierCode":"rlca","carrier":"R+L Carriers","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"17","status":"ok","ref":"15543043","days":2,"time":1333},{"total":676.68,"carrierCode":"rlca","carrier":"R+L Carriers","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"17","status":"ok","ref":"15543043","days":2,"time":1333},{"total":536.99,"carrierCode":"exla","carrier":"Estes Express Lines","paymentTerms":"Inbound Collect","serviceType":"standard","status":"ok","ref":"5067611","days":2,"time":1417},{"total":706.47,"carrierCode":"exla","carrier":"Estes Express Lines","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"17","status":"ok","ref":"5067611","days":2,"time":1417},{"total":1045.43,"carrierCode":"exla","carrier":"Estes Express Lines","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"10","status":"ok","ref":"5067611","days":2,"time":1417},{"total":875.95,"carrierCode":"exla","carrier":"Estes Express Lines","paymentTerms":"Inbound Collect","serviceType":"guaranteed","serviceOption":"12","status":"ok","ref":"5067611","days":2,"time":1417},{"total":445.26,"carrierCode":"upgf","carrier":"UPS Freight","paymentTerms":"Third Party Prepaid","serviceType":"guaranteed","serviceOption":"17","status":"ok","days":2,"time":1675},{"total":531.22,"carrierCode":"upgf","carrier":"UPS Freight","paymentTerms":"Third Party Prepaid","serviceType":"guaranteed","serviceOption":"12","status":"ok","days":2,"time":1675},{"carrierCode":"rdfs","carrier":"Roadrunner Transportation Services","paymentTerms":"Third Party Prepaid","serviceType":"standard","status":"error","error":"There is no standard service from 95403 to 89011","time":1776},{"total":310.97,"carrierCode":"ctii","carrier":"Central Transport","paymentTerms":"Inbound Collect","serviceType":"standard","status":"ok","days":2,"interline":false,"time":2174},{"total":1714.5,"carrierCode":"cenf","carrier":"Central Freight Lines","paymentTerms":"Inbound Collect","serviceType":"standard","status":"ok","ref":"12060485","days":2,"time":2992},{"total":348,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"standard","status":"ok","ref":"37186926","days":3,"time":6415},{"total":363,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"guaranteed","serviceOption":"17","status":"ok","ref":"37186925","days":3,"time":6415},{"total":404.6,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"17","status":"ok","days":2,"time":6415},{"total":854.6,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"17","status":"ok","days":3,"time":6415},{"total":854.6,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"17","status":"ok","days":4,"time":6415},{"total":404.6,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"17","status":"ok","days":5,"time":6415},{"total":404.6,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"17","status":"ok","days":6,"time":6415},{"total":418.75,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"12","status":"ok","days":2,"time":6415},{"total":868.75,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"12","status":"ok","days":3,"time":6415},{"total":868.75,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"12","status":"ok","days":4,"time":6415},{"total":418.75,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"12","status":"ok","days":5,"time":6415},{"total":418.75,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"12","status":"ok","days":6,"time":6415},{"total":432.9,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"0","status":"ok","days":2,"time":6415},{"total":882.9,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"0","status":"ok","days":3,"time":6415},{"total":882.9,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"0","status":"ok","days":4,"time":6416},{"total":432.9,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"0","status":"ok","days":5,"time":6416},{"total":432.9,"carrierCode":"rdwy","carrier":"YRC Freight","paymentTerms":"Third Party Prepaid","serviceType":"expedited","serviceOption":"0","status":"ok","days":6,"time":6416}],"originCity":"Santa Rosa","originState":"CA","originCountryCode":"US","destCity":"Henderson","destState":"NV","destCountryCode":"US"}';
			
			// setTimeout(function() {
				renderRates(exampleData);
			// }, 10000);

			// end of test


			// //var url = "getRates.php";
			// var url = "getRates.cfc?method=freightView";
			// var data = collectData();
			// // TODO error function
			// $.ajax({
			// 	type: "POST",
			// 	url: url,
			// 	data: { 'json' : JSON.stringify(data) },
			// 	success: function(data) {
			// 		renderRates(data);
			// 	},
			// 	error: function() {
			// 		// remove loading image and enable the submit button
			// 		$('#loading-image').remove();
			// 		$('input[type=submit]').removeAttr('disabled');
			// 	}
			// });

		    return false; // avoid to execute the actual submit of the form.		
		},
		// invalidHandler: function() {
		// 	// 'this' refers to the form
		// 	var errors = $('#quote').validate().numberOfInvalids();
		// 	if (errors) {
		// 		$('h1').after($('<div class="errorTxt"></div>'));
		// 		$("div.errorTxt").show();
		// 	} else {
		// 		$("div.errorTxt").hide();
		// 	}
		// },
		errorElement : 'div',
    	errorLabelContainer: '.errorTxt'
	});
}

function renderRates(data) {
	var dataObj = JSON.parse(data);
	var rates = dataObj['rates'];
	var $ratesContainer = $('<div id="rates">');
	var $ratesHeader = $('<div id="rates-header" class="container-fluid no-padding">');
	var $errorsContainer = $('<div id="rates-error" class="container-fluid">');
	var $errorHeader = $('<div class="error-header">');
	var $showErrorsBtn = $('<button class="btn btn-warning" id="show-error-cariers">');
	var numberOfErrors = 0;
	var lowest = Number.POSITIVE_INFINITY;
	var fastestDelivery = {};

	// remove loading image and enable the submit button
	$('#loading-image').remove();
	$('input[type=submit]').removeAttr('disabled');

	// error container
	$errorHeader.html('<span id="number-of-errors"></span> carriers did not return rates');
	$errorsContainer.append($errorHeader);
	$showErrorsBtn.text('Show carriers');
	$showErrorsBtn.appendTo($errorsContainer);

	// rates container
	$ratesHeader.html(
		'<div id="featured-rates" class="col-md-9 no-padding">' +
			'<h2>Featured rates</h2>' +
		'</div>' +
		'<div id="fastest-delivery" class="col-md-3">' +
			'<h2>Fastest delivery</h2>' +
		'</div>'
	);

	$ratesContainer.append($ratesHeader);

	// sort the rates by price and date
	rates.sort(function(a,b){
		// if the price is undefined(status is not "ok")
		if(! a.total) {
			return -1;
		}

		if(! b.total) {
			return 1;
		}

		if(a.total < b.total) {
			return -1;
		}

		if(a.total > b.total) {
			return 1;
		}
		// if the price are equal sort by days
		if(a.total == b.total) {
			if(a.days < b.days) {
				return -1;
			}

			if(a.days > b.days) {
				return 1;
			}

			return 0;
		}
	});

	for (var i = 0; i <= rates.length - 1; i++) {
	    if (rates[i].days < lowest) { 
	    	lowest = rates[i].days;
	    	fastestDelivery = rates[i];
	    }
	}
	
	// this object will be filled with the cheapest offer from each carrier
	var displayedCarriers = {};

	$('.container').append($ratesContainer);

	for (var i = 0; i <= rates.length - 1; i++) {

		// append each of the rates to the rates container
		if(rates[i]['status'] === 'ok') {
			var $currentRate = $('<div class="rate container-fluid alert alert-info">');
			var $carrierName = $('<div class="col-md-4 col-xs-12 carrier-name">');
			var $serviceType = $('<div class="col-md-4 col-xs-12 service-type">');
			var $days = $('<div class="col-md-2 col-xs-6 days">');
			var $price = $('<div class="col-md-2 col-xs-6 price">');
			var $serviceOption = $('<span class="service-option">');
			var originAddress = $('select[name=shipFrom] option:selected').attr('data-address');
			var originState = $('select[name=shipFrom] option:selected').attr('data-state');
			var originCompany = $('select[name=shipFrom] option:selected').attr('data-company');
			var originCity = $('select[name=shipFrom] option:selected').attr('data-city');
			var originZipCode = $('select[name=shipFrom]').val();

			$carrierName.text(rates[i]['carrier']);
			$currentRate.append($carrierName);

			// Text of the service type
			if(rates[i]['serviceType'] === 'standard' || rates[i]['serviceType'] === 'guaranteed') {
				$serviceType.text(cFirst(rates[i]['serviceType']));
				
				if(rates[i]['serviceOption']) {
					$serviceOption.text(timeConvert(rates[i]['serviceOption']));
					$serviceOption.appendTo($serviceType);
				}
			} else {
				if(rates[i]['serviceOption'] !== "0") {
					$serviceType.text('Time Critical by ' + timeConvert(rates[i]['serviceOption']));
				} else {
					$serviceType.text('Time Critical Hour Window');
				}

				$serviceOption.text(getDate(rates[i]['days']));
				$serviceOption.appendTo($serviceType);
			}

			$currentRate.append($serviceType);

			$days.html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>' + rates[i]['days'] + ' <span>days</span>');
			$currentRate.append($days);
			
			$price.html(
				'<form method="post" action="/secure/freightAdmin/shipment.cfm">' +
					'<input type="hidden" name="carrierCode" value="' + rates[i]['carrierCode'] + '"/>' +
					'<input type="hidden" name="carrierName" value="' + rates[i]['carrier'] + '"/>' +
					'<input type="hidden" name="total" value="' + rates[i]['total'] + '"/>' +
					'<input type="hidden" name="paymentTerms" value="' + rates[i]['paymentTerms'] + '"/>' +
					'<input type="hidden" name="serviceType" value="' + rates[i]['serviceType'] + '"/>' +
					'<input type="hidden" name="ref" value="' + rates[i]['ref'] + '"/>' +
					'<input type="hidden" name="days" value="' + rates[i]['days'] + '"/>' +
					'<input type="hidden" name="time" value="' + rates[i]['time'] + '"/>' +
					'<input type="hidden" name="serviceOption" value="' + rates[i]['serviceOption'] + '"/>' +
					'<input type="hidden" name="originAddress" value="' + originAddress + '"/>' +
					'<input type="hidden" name="originState" value="' + originState + '"/>' +
					'<input type="hidden" name="originCompany" value="' + originCompany + '"/>' +
					'<input type="hidden" name="originZipCode" value="' + originZipCode + '"/>' +
					'<input type="hidden" name="originCity" value="' + originCity + '"/>' +
					'<input type="hidden" name="test" value="' + JSON.stringify($(".items").serializeArray()) + '"/>' + 
					// '<input type="hidden" name="destAddress" value="' + destAddress + '"/>' +
					'<button class="price-btn" type="submit">' +
						'<span class="glyphicon glyphicon-usd" aria-hidden="true"></span>' + rates[i]['total'].toFixed(2) +
					'</button>' +
				'</form>'
			);
			$currentRate.append($price);

			$currentRate.attr('data-code', rates[i]['carrierCode']);
			
			// if there is already a rate from a carrier, displayed in the the html, the other rates from this carrier will be hidden
			if(displayedCarriers.hasOwnProperty(rates[i]['carrier'])) {
				$currentRate.addClass('hidden-rate');
				$('*[data-code=' + rates[i]['carrierCode'] + ']').last().after($currentRate);
				$currentRate.css('display', 'none');
				var $firstOfType = $('*[data-code=' + rates[i]['carrierCode'] + ']').first();
				$firstOfType.addClass('show-hidden-rates');
				var $serviceDiv = $firstOfType.find('.service-type');
				if($serviceDiv.find('.glyphicon-download').length == 0) {
					$serviceDiv.html($serviceDiv.html() + '<span class="glyphicon glyphicon-download"></span>');
				}
		 	} else {
		 		displayedCarriers[rates[i]['carrier']] = 1;
		 		$currentRate.appendTo($ratesContainer);
		 	}

		} else {
			// append errors to the errors container
			var $currentError = $('<div class="rate-error container-fluid alert alert-warning">');
			var $carrierName = $('<div class="col-md-3 error-carrier">');
			var $errorMessage = $('<div class="col-md-9 error-message">');
			
			numberOfErrors++;
			$carrierName.text(rates[i]['carrier']);
			$currentError.append($carrierName);

			$errorMessage.text(rates[i]['error']);
			$currentError.append($errorMessage);

			$currentError.appendTo($errorsContainer);
		}
	}

	// if there is an errors, the error container will be appended to the content
	if(numberOfErrors > 0) {
		$('.container').append($errorsContainer);
		$('#number-of-errors').text(numberOfErrors);

		// toggle the errors on button click
		$showErrorsBtn.click(function(){
			var $statusErrors = $('.rate-error');
			var display = $statusErrors.css('display');

			if(display === 'none') {
				$(this).text('Hide carriers');
			} else {
				$(this).text('Show carriers');
			}

			$statusErrors.toggle();
		});
	}

	// get the featured rates
	$('.rate:not(.hidden-rate)').each(function(index) {
		if(index > 2) {
			return;
		}

		var $thisCloned = $(this).clone();
		var $featuredWrapper = $('<div class="col-md-4">');
		$thisCloned.removeClass();
		$thisCloned.addClass('rate container-fluid no-padding');
		$featuredWrapper.append($thisCloned);
		$featuredWrapper.appendTo('#featured-rates');

		var $carrier = $thisCloned.find('.carrier-name');
		$carrier.removeClass('col-md-4');
		$carrier.addClass('col-md-12');

		var $serviceType = $thisCloned.find('.service-type');
		$serviceType.removeClass('col-md-4');
		$serviceType.addClass('col-md-12');

		var $days = $thisCloned.find('.days');
		$days.removeClass('col-md-2');
		$days.addClass('col-md-6');

		var $price = $thisCloned.find('.price');
		$price.removeClass('col-md-2');
		$price.addClass('col-md-6');
	});

	// render the fastest delivery
	var $fastestDelivery = $('<div>');
	$('#fastest-delivery').append($fastestDelivery);
	$fastestDelivery.html(
		'<div class="rate container-fluid">' +
			'<div class="col-md-12 col-xs-12 carrier-name">' + fastestDelivery['carrier'] + '</div>' +
			'<div class="col-md-12 col-xs-12 service-type">' + fastestDelivery['serviceType'] + '</div>' +
			'<div class="col-md-6 col-xs-6 days">' + 
				'<span class="glyphicon glyphicon-time" aria-hidden="true"></span>' + fastestDelivery['days'] + ' <span>days</span>' +
			'</div>' +
			'<div class="col-md-6 col-xs-6 price">' + 
				'<button class="price-btn" type="button">' +
					'<span class="glyphicon glyphicon-usd" aria-hidden="true"></span>' + fastestDelivery['total'].toFixed(2) +
				'</button>' +
			'</div>' +
		'</div>'
	);

	// toggle the hidden rates from a carrier on click
	$('.show-hidden-rates').click(function() {
		var $this = $(this);
		var carrierCode = $this.attr('data-code');
		var borderBottom = $this.css('border-bottom-width')[0];

		if(parseInt(borderBottom) > 0) {
			$this.css('border-bottom-width', '0');
			$this.css('border-radius', '5px 5px 0 0');
		} else {
			$this.css('border-bottom-width', '2px');
			$this.css('border-radius', '5px 5px 5px 5px');
		}

		$('.hidden-rate[data-code=' + carrierCode + ']').last().css('border-bottom', '2px solid silver');
		$('.hidden-rate[data-code=' + carrierCode + ']').toggle();
	});

	// TODO refactor this shit
	$('.hidden-rate').click(function(){
		var carrierCode = $(this).attr('data-code');
		var $this = $('.show-hidden-rates[data-code=' + carrierCode + ']');
		var borderBottom = $this.css('border-bottom-width')[0];

		if(parseInt(borderBottom) > 0) {
			$this.css('border-bottom-width', '0');
			$this.css('border-radius', '5px 5px 0 0');
		} else {
			$this.css('border-bottom-width', '2px');
			$this.css('border-radius', '5px 5px 5px 5px');
		}

		$('.hidden-rate[data-code=' + carrierCode + ']').last().css('border-bottom', '2px solid silver');
		$('.hidden-rate[data-code=' + carrierCode + ']').toggle();
	});

	// Click on price button
	$('.price-btn').click(function(event){
		event.stopPropagation();
	});

	// focus on the rates div
	$('html, body').animate({ scrollTop: $('#rates').offset().top }, 'slow');
}

function collectData() {
	var items = [];

	$('.item').each(function(index){
		var counter = index + 1;
		var hazardous = ( $('input[name=hazmat-item-' + counter + ']:checked').val() === 'true' ) ? true : false;
		var item = {
			// Integer in pounds
			weight : getPoundsFromInput('weight', counter),
			// double
			freightClass : parseFloat($('select[name=freightClass-item-' + counter + ']').val()),
			// Integer in inches
			length : getInchesFromInput('length', counter),
			width : getInchesFromInput('width', counter),
			height : getInchesFromInput('height', counter),
			//'package' : 'Pallets_other',
			// Int
			pieces : parseInt($('input[name=quantity-item-' + counter + ']').val()),
			// bool
			hazardous : hazardous
		};

		if($('select[name=package-item-' + counter + ']').val() === 'Pallets_42x42') {
			item['package'] = "Pallets_other";
		} else {
			item['package'] = $('select[name=package-item-' + counter + ']').val();
		}

		items.push(item);
	});

	var charges = [];
	
	switch($('input[name=accessorials]:checked').val()) {
		case 'arrival-notice': 
			charges.push('arrival notice'); 
			break;

		case 'arrival-schedule':
			charges.push('arrival schedule'); 
			break;
	}

	if($('input[name=sortAndSegregate]').is(':checked')) {
		charges.push('sort and segregate');
	}

	if($('input[name=blindShipment]').is(':checked')) {
		charges.push('blind shipment');
	}

	if( $('select[name=originType]').val() === "business no dock") {
		if($('input[name=liftgateOrigin]').is(':checked')) {
			charges.push('liftgate pickup');
		}

		if($('input[name=insidePickupOrigin]').is(':checked')) {
			charges.push('inside pickup');
		}
	}

	if($('select[name=destType]').val() === "business no dock") {
		if($('input[name=liftgateDest]').is(':checked')) {
			charges.push('liftgate delivery');
		}

		if($('input[name=insideDelivery]').is(':checked')) {
			charges.push('inside delivery');
		}
	}

	var result = {
		pickupDate : $('input[name=pickUpDate]').val(),
		originPostalCode : $('*[name=shipFrom]').val(),
		destPostalCode : $('*[name=shipTo]').val(),
		origintType : $('select[name=originType]').val(),
		destType : $('select[name=destType]').val(),
		items : items
	};

	if(charges.length != 0) {
		result['charges'] = charges;
	}

	return result;
}

// HELPERS

function getPoundsFromInput(inputVar, index) {
	var KG_TO_LB = 2.20462262;
	var value = $('input[name=' + inputVar + '-item-' + index + ']').val()
	var result = parseInt(value);

	if($('select[name=weightUnit-item-' + index + ']').val() == 'kg') {
		result = Math.round(KG_TO_LB * result);
	}

	return result;
}

function getInchesFromInput(inputVar, index) {
	var CM_TO_IN = 0.393700787;
	var value = $('input[name=' + inputVar + '-item-' + index + ']').val()
	var result = parseInt(value);

	if($('select[name=lengthUnit-item-' + index + ']').val() == 'cm') {
		result = Math.round(CM_TO_IN * result);
	}

	return result;
}

function getDate(days) {
	var date = new Date();
	var numberOfDaysToAdd = parseInt(days);
	var dd;
	var mm;
	var result;
	var monthNames = ["Jan", "Febr", "March", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

	date.setDate(date.getDate() + numberOfDaysToAdd);
	dd = date.getDate();
	mm = date.getMonth();
	result = monthNames[mm] + ' ' + dd;

	return result;
}

function timeConvert (time) {
	var time = parseInt(time);
	var h = time % 12 || 12;
	var ampm = time < 12 ? "AM" : "PM";
	var	timeString = h + ampm;

	return timeString;
}

function cFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}