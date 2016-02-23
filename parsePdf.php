<?php
 
// Include Composer autoloader if not already done.
include 'pdfparser-master/vendor/autoload.php';
$result = getPickupNumber('bol.pdf');

var_dump($result); die;

function getPickupNumber($filePath) {
	// Parse pdf file and build necessary objects.
	$parser = new \Smalot\PdfParser\Parser();
	$pdf    = $parser->parseFile($filePath);
	 
	$text = $pdf->getText();

	preg_match("/pu[\d,-]*/i", $text, $pickupNumber);

	if(count($pickupNumber) != 0) {
		return $pickupNumber[0];
	}
}

?>