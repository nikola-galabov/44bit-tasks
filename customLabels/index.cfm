<!--- The overall size is to be 4” x 6” using the layout shown in either figure 2a or 2b. These labels may use bar code 128 font with suggested characteristics: 
• minimum Intercharacter Gap of 0.01 inches
• element width ratio of 2.5:1
• minimum quiet zones of 0.25 inches
• minimum character height of 0.38 inches (10mm)
• Human readable text shall be a minimum of 2mm high. (Note: Legacy graphic shows Code 39). --->

<cfset fromAddress = "12345 NY Bridge view ...">
<cfset toAddress = "54321 IL Chicago College Point">
<cfset packageCount = 1>
<cfset packageWeight = 120>
<cfset description = "NON-SPILLABLE BATTERIES NON-HAZARDOUS">
<cfset packageId = 123456789>
<cfset poNumber = 2314>
<cfset jabilPartNo = 34567>
<cfset quantity = 1>
<cfset supplierPartNo = 234352>

<cffunction name="getBarcode">
	<cfargument name="value" required="true"> 
	<cfargument name="type" default="c128a">
	<cfargument name="imageType" default="png">
	<cfargument name="width" default="">
	<cfargument name="height" default="">
	<!--- TODO file name--->
	<cfargument name="fileName" default="barcode">

	<cfset apiUrl = "http://barcodes4.me/barcode/" & 
					ARGUMENTS.type & 
					"/" & 
					ARGUMENTS.value & 
					"." & 
					ARGUMENTS.imageType
	>
	
	<cfset filePath = "/home/niki/development/Coldfusion">

	<cfhttp url="#apiUrl#" method="get" path="#filePath#" file="#fileName#.#imageType#">
		
	</cfhttp>
	

	<cfreturn fileName & "." & imageType>
</cffunction>

<!doctype html>
<html>
    <head>
        <title>Custom Label</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    	<cfoutput>
	        <div id="label">
				<div class="label-row">
					<div class="fromAddress">#fromAddress#</div>
					<div class="toAddress">#toAddress#</div>
				</div>
				<!--- PACKAGE ID --->
				<div class="label-row">
					<div class="row-title">PACKAGE ID:</div>
					<!--- getTheBarcode --->
					<cfset theImage = getBarcode(
						value = packageId,
						fileName = "packageId"
					)>
					<div class="barcode"><img src="#theImage#" alt=""></div>
					<div class="row-number">#packageId#</div>
				</div>
				<div class="label-row">
					<div class="row-title">PURCHASE ORDER NO:</div>
					<!--- getTheBarcode --->
					<cfset theImage = getBarcode(
						value = poNumber,
						fileName = "purchaseOrderId"
					)>
					<div class="barcode"><img src="#theImage#" alt=""></div>
					<div class="row-number">#poNumber#</div>
				</div>
				<div class="label-row">
					<div class="row-title">JABIL PART NO:</div>
					<cfset theImage = getBarcode(
						value = jabilPartNo,
						fileName = "jabilPartNo"
					)>
					<div class="barcode"><img src="#theImage#" alt=""></div>
					<div class="row-number">#jabilPartNo#</div>
				</div>
				<div class="label-row">
					<div class="row-title">QUANTITY:</div>
					<cfset theImage = getBarcode(
						value = quantity,
						fileName = "quantity"
					)>
					<div class="barcode"><img src="#theImage#" alt=""></div>
					<div class="row-number">#quantity#</div>
				</div>
				<div class="label-row">
					<div class="row-title">SUPPLIER PART NO:</div>
					<cfset theImage = getBarcode(
						value = supplierPartNo,
						fileName = "supplierPartNo"
					)>
					<div class="barcode"><img src="#theImage#" alt=""></div>
					<div class="row-number">#supplierPartNo#</div>
				</div>
				<!--- DESCRIPTION --->
				<div class="label-row">
					#description#
				</div>
				<!--- PACKAGE COUNT AND WEIGHT --->
				<div class="label-row">
					<div class="col-half">
						<div>PACKAGE COUNT:</div>
						<div>#packageCount#</div>
					</div><!--
				 --><div class="col-half">
						<div>PACKAGE WEIGHT:</div>
						<div>#packageWeight#</div>
					</div>
				</div>
	        </div>
        </cfoutput>
    </body>
</html>