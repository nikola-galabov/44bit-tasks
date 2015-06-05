component {

	remote string function test(required string name, required any pk, required numeric value) {
		Sleep(3000);
		return 'ok';
	}

	remote numeric function getValue() returnformat="JSON"{
		Sleep(3000);
		return 1;
	}
}