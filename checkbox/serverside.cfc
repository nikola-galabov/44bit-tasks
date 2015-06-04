component {

	remote string function test(required string name, required any pk, required numeric value) {
		Sleep(3000);
		return 'ok';
	}
}