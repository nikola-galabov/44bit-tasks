var $inputs = $('.autofill');
var autofillValues = {
	some1: 'companyName',
	some2: 'companyAddress1',
	some3: 'companyPhone',
	some4: 'companyContact',
	some5: 'companyZip'
};
var initialValues = {};

$('#autofill-company').change(function(){
	var currentSelected = $(this).val();
	$.ajax({
		url: 'values.json',
		type: 'GET',
		data: {companyId: 'NationalBattery 11356'},
	})
	.done(function(data) {
		data = JSON.parse(data);
		data = data[currentSelected];
		$inputs.each(function(index, el) {
			var inputId = $(el).attr('id');
			var inputValue = data[autofillValues[inputId]];
			$(el).val(inputValue);
			initialValues[inputId] = inputValue;
		});

		$inputs.change(function(){
			var currentValues = getInputValues();
			if(JSON.stringify(currentValues) !== JSON.stringify(initialValues)){
				suggestUpdate();
				return;
			}

			$('#suggest-update').remove();
		});
	});
});

function suggestUpdate(){
	var $el = $('<p>')
		.attr('id', 'suggest-update')
		.html('Do you want to save the changes? <button id="yes">Yes</button>');

	$('body').append($el);
	$('#yes').click(function(){
		var newValues = getInputValues();
		updateAutofill(newValues);
		$('#suggest-update').remove();
	});
}

function getInputValues() {
	var inputValues = {};
	$inputs.each(function(index, el) {
		var inputId = $(el).attr('id');
		var inputValue = $(el).val();
		inputValues[inputId] = inputValue; 
	});

	return inputValues;
}

function updateAutofill(newValues) {
	initialValues = newValues;
}