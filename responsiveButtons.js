function responsiveButtons2() {
	var buttonsContainerSelector = '#action-buttons';	
	var $buttonsContainer = $(buttonsContainerSelector);
	var buttons = $buttonsContainer.children();
	var isDropup = false;

	//preventing unresponsive behaviour
	var buffer = 10;

	// margin between the buttons is 5px
	var margin = 4;

	// This is the minimal screen size needed for all buttons to be shown on a same line
	var windowMinWidth = margin * buttons.length + buffer;

	// The meaning of "skipped button" is a button that will not be added in the dropup
	var skipButtonsNumber = 4;
	var $skipButtonsArr = [];
	var dropupButtonWidth = 25;
	var thereIsHiddenItem = false;
	skipButtonsWidth = margin * skipButtonsNumber + buffer + dropupButtonWidth;

	// This buttons will be added in the dropup
	var $buttonsInDropupArr = [];
	var dropupButtonsWidth = margin * skipButtonsNumber + buffer;

	// fill the skipButtonsArr with the elements and calculating the windowMinwidth and the width of skipButtons
	for(var index = 0; index <= buttons.length-1; index++) {
		var $currentButton = $(buttons[index]);

		if(index <= skipButtonsNumber - 1) {
			$skipButtonsArr.push($currentButton.clone(true));
			skipButtonsWidth += $currentButton.outerWidth(true);
		} else {
			$buttonsInDropupArr.push($currentButton.clone(true));
		}

		windowMinWidth += $currentButton.outerWidth(true);
	}

	$(window).resize(function() {
		var windowCurrentWidth = $(document).width();
		var $nav = $('#left-panel');

		// if the navigation(left sidebar) is not hidden its width will be substracted from the window's width, because we need the width where the buttons will be placed
		if($nav.css('left') != "-220px") {
			windowCurrentWidth -= $nav.outerWidth(true);
		}

		// window's width is smaller than all buttons' size
		if(windowCurrentWidth < windowMinWidth && isDropup == false) {
			renderButtonsInDropup();
			isDropup = true;
		}

		// window's width is enough for all buttons to be on a single row
		if(windowCurrentWidth > windowMinWidth && isDropup) {
			renderButtonsWithoutDropup();
			isDropup = false;
		}

		// window's width is enough for on of skidden skip buttons
		if(windowCurrentWidth > skipButtonsWidth + $buttonsInDropupArr[$buttonsInDropupArr.length - 1].outerWidth(true) && isDropup && thereIsHiddenItem) {
			var $button = $buttonsInDropupArr.pop();
			$skipButtonsArr.push($button);
			skipButtonsWidth += $button.outerWidth(true);
			renderButtonsInDropup();
		}

		// window's width is not enough for all skip buttons 
		if(windowCurrentWidth < skipButtonsWidth && isDropup) {
			var $button = $skipButtonsArr.pop();
			$buttonsInDropupArr.push($button);
			skipButtonsWidth -= $button.outerWidth(true);
			renderButtonsInDropup();
			thereIsHiddenItem = true;
		}
	});

	function renderButtonsInDropup() {
		var $btnGroup = $('<div class="btn-group dropup">');
		var $dropUpButton = $('<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>');
		var $ul = $('<ul class="dropdown-menu" aria-labeldby="dropdownMenu">');
		var $li;

		console.log('vliza');

		$buttonsContainer.empty();


		for(var i = 0; i < $skipButtonsArr.length - 1; i++) {
			var $button = $skipButtonsArr[i];
			$button.removeClass('dropup-button');
			$button.css('display', 'inline');
			$button.css('margin-right', '3.5px');
			$buttonsContainer.append($button);
		}

		$buttonsContainer.addClass('dropup');
		$skipButtonsArr[$skipButtonsArr.length - 1].css('display', 'inline');
		$btnGroup.append($skipButtonsArr[$skipButtonsArr.length - 1]);
		$btnGroup.append($dropUpButton);
		$btnGroup.append($ul);
		$buttonsContainer.append($btnGroup);

		for (var index = 0; index <= $buttonsInDropupArr.length-1; index++) {
			var $button = $(buttons[index]);
			$li = $('<li role="presentation">');

			$button.attr('role', 'menu-item');
			$button.addClass('dropup-button');
			$li.append($button);
			$li.appendTo($ul);
		}
	}

	function renderButtonsWithoutDropup() {
		console.log('vliza without');
		$buttonsContainer.empty();
		$buttonsContainer.removeClass('dropup');

		for(var i = 0; i <= buttons.length - 1; i++) {
			$(buttons[i]).removeClass('dropup-button');
			$(buttons[i]).css('margin-right', '3.5px');
			$buttonsContainer.append(buttons[i]);
		}
	}
}