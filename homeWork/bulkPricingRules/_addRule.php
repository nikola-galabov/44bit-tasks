<style>
	#marketingManager div.button-wrapper {
		text-align: center;
		margin: 250px 0;
	}
	
	#marketingManager .bigButton {
		height: 100px;
		font-size: 20px;
	}
</style>

<div id="marketingManager">
	<div class="button-wrapper">
		<form class="typeSelect" action="" method="get">
			<div>
				<button name="single" class="bigButton">Add Bulk Pricing Rule for a single product</button>
			</div>
			<div>
				<button name="group" class="bigButton">Add Bulk Pricing Rule for a group of products</button>
			</div>
		</form>	
	</div>
</div>

<script>
	$('form').submit(function(e) {
		e.preventDefault();
		console.log($('form.typeSelect').serializeArray());
	});
</script>
