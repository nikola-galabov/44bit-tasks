<cfparam name="URL.type" default=""/>
<cfset cfcPickupAndDelivery = new models.PickupAndDelivery() />
<cfset cfcItem = new models.Item() />

<cfset qLocationTypes = cfcPickupAndDelivery.getLocationTypes() />
<cfset qLocations = cfcPickupAndDelivery.getLocations() />
<cfset qPackageTypes = cfcItem.getPackageTypes() />
<cfset qFreightClasses = cfcItem.getFreightClasses() />

<cfinclude template="includes/header.cfm">
<cfoutput>
	<div class="container">
		<!--- First letter of the type to upper case for example Outbound, Inbound etc. --->
		<h1>#uCase(left(URL.type, 1)) & right(URL.type, len(URL.type) - 1)#</h1>
		<form id="quote" method="post" data-type="#URL.type#">
			<div class="row">
				<div class="col-md-6 col-xs-12">
					<!--- Pickup --->
					<fieldset>
						<legend>Pickup</legend>
						<!--- Ship from --->
						<div class="form-group">
							<label for="ship-from">Ship from</label>
							<cfif URL.type EQ "outbound" OR URL.type EQ "transfer">
								<select id="ship-from" class="form-control quote-input" name="shipFrom" required>
									<cfloop query="#qLocations#">
										<option value="#ZipCode#" data-company="#company#" data-address="#address#" data-city="#city#" data-state="#State#">
											#company# - #address#, #city#, #State# #ZipCode#
										</option>
									</cfloop>
								</select>
							<cfelse>
								<select id="ship-from" class="form-control select2-ajax quote-input" name="shipFrom" required></select>
							</cfif>
						</div>
						<!--- Pick up date --->
						<div class="form-group">
							<label for="pick-up-date">Pick up on</label>
							<input class="form-control quote-input datepicker" name="pickUpDate" id="pick-up-date" required>
						</div>
						<!--- Origin type --->
						<div class="form-group">
							<label for="origin-type">Type of location</label>
							<select id="origin-type" class="form-control quote-input" name="originType">
								<cfloop query="#qLocationTypes#">
									<option value="#value#">#Text#</option>
								</cfloop>
							</select>
						</div>	
						
						<!--- Liftgate and inside pickup --->
						<div id="liftgate-origin-container">
							<div class="form-group">
								<input type="checkbox" class="quote-input" id="liftgate-origin" name="liftgateOrigin" />
								<label for="liftgate-origin">Liftgate required for pickup?</label>
							</div>
						</div>

						<div id="inside-pickup-container">	
							<div class="form-group">
								<input type="checkbox" class="quote-input" id="inside-pickup-origin" name="insidePickupOrigin" />
								<label for="inside-pickup-origin">Inside pickup required?</label>
							</div>
						</div>
							
					</fieldset>

					<!--- Delivery --->
					<fieldset>
						<legend>Delivery</legend>
						<!--- Ship to --->
						<div class="form-group">
							<label for="ship-to">Ship to</label>
							<cfif URL.type EQ "inbound" OR URL.type EQ "transfer">
								<select id="ship-to" class="form-control quote-input" name="shipTo" required>
									<cfloop query="#qLocations#">
										<option value="#ZipCode#">#company# - #address#, #city#, #State# #ZipCode#</option>
									</cfloop>
								</select>
							<cfelse>
								<select id="ship-to" class="form-control select2-ajax quote-input" name="shipTo" required></select>
							</cfif>
						</div>
						<!--- Dest type --->
						<div class="form-group">
							<label for="dest-type">Type of location</label>
							<select id="dest-type" class="form-control quote-input" name="destType">
								<cfloop query="#qLocationTypes#">
									<option value="#value#">#Text#</option>
								</cfloop>
							</select>
						</div>
						
						<!--- Liftgate and inside delivery --->
						<div id="liftgate-dest-container">
							<div class="form-group">
								<input type="checkbox" class="quote-input" id="liftgate-dest" name="liftgateDest" />
								<label for="liftgate-dest">Liftgate required for delivery?</label>
							</div>
						</div>
						
						<div id="inside-delivery-container">
							<div class="form-group">
								<input type="checkbox" class="quote-input" id="inside-delivery" name="insideDelivery" />
								<label for="inside-delivery">Inside delivery required?</label>
							</div>
						</div>	
					</fieldset>
					<!--- Add accessorials & services --->
					<button type="button" class="btn btn-default" id="toggle-accessorials">Show accessorials</button>
					<fieldset class="form-group" id="accessorials">
						<legend>Accessorials and services</legend>
						<div class="form-group">
							<input type="radio" class="quote-input" name="accessorials" id="arrival-notice" value="arrival-notice">
							<label for="arrival-notice">Notify by phone before delivery</label>
						</div>
						<div class="form-group">
							<input type="radio" class="quote-input" name="accessorials" id="arrival-schedule" value="arrival-schedule">
							<label for="arrival-schedule">Call me for an appointment</label>
						</div>
						<div class="form-group">
							<input type="radio" name="accessorials" class="quote-input" id="none" value="none" checked="checked">
							<label for="none">None</label>
						</div>

						<!--- sort and segregate --->
						<div class="form-group">
							<input type="checkbox" class="quote-input" id="sort-and-segregate" name="sortAndSegregate" />
							<label for="sort-and-segregate">Sort and segregate</label>
						</div>

						<!--- blind shipment --->
						<div class="form-group">
							<input type="checkbox" class="quote-input" id="blind-shipment" name="blindShipment" />
							<label for="blind-shipment">Blind shipment</label>
						</div>
					</fieldset>
				</div>
				<fieldset class="items col-md-6 col-xs-12">
					<legend>Shipping items</legend>
					<div id="tabs">
						<!--- Shipping items tabs--->
						<ul>
							<li>
								<a href="##item-1">Item 1</a>
							</li>
						</ul>
						<!--- Item 1 --->
						<div id="item-1" class="item">
							<div class="row">
								<!--- Packaging --->
								<div class="form-group">
									<label for="package-item-1">Packaging</label>
									<select class="form-control quote-input" name="package-item-1" id="package-item-1" required>
										<cfloop query="#qPackageTypes#">
											<option value="#Value#">#Name#</option>
										</cfloop>
									</select>
								</div>
								<!--- Quantity --->
								<div class="form-group col-md-4 col-xs-4 padding-left-none">
									<label for="quantity-item-1">Quantity</label>
									<input class="form-control calculation item-input quote-input" type="number" min=0 name="quantity-item-1" id="quantity-item-1" value="1" required>
								</div>
								<!--- Weight --->
								<div class="form-group col-md-4 col-xs-4">
									<label for="weight-item-1">Weight</label>
									<input class="form-control calculation item-input quote-input" type="number" name="weight-item-1" id="weight-item-1" required>
								</div>	
								<!--- lbs/kg --->
								<div class="form-group col-md-4 col-xs-4 padding-right-none">
									<label for="weight-unit-item-1">Lbs/Kg</label>
									<select class="form-control quote-input" name="weightUnit-item-1" id="weight-unit-item-1">
										<option value="lbs" selected="selected">Lbs</option>
										<option value="kg">Kg</option>
									</select>
								</div>
								<!--- Length --->
								<div class="form-group col-md-3 col-xs-3 padding-left-none clear-both">
									<label for="length-item-1">Length</label>
									<input class="form-control calculation item-input quote-input" type="number" name="length-item-1" id="length-item-1" required>
								</div>
								<!--- Width --->
								<div class="form-group col-md-3 col-xs-3">
									<label for="width-item-1">Width</label>
									<input class="form-control calculation item-input quote-input" type="number" name="width-item-1" id="width-item-1" required>
								</div>	
								<!--- Height --->
								<div class="form-group col-md-3 col-xs-3">
									<label for="height-item-1">Height</label>
									<input class="form-control calculation item-input quote-input" type="number" name="height-item-1" id="height-item-1" required>
								</div>

								<!--- In/cm --->
								<div class="form-group col-md-3 col-xs-3 padding-right-none">
									<label for="length-unit-item-1">In/Cm</label>
									<select class="form-control quote-input" name="lengthUnit-item-1" id="length-unit-item-1">
										<option value="in" selected="selected">In</option>
										<option value="cm">Cm</option>
									</select>
								</div>
								<!--- Freight class --->
								<div class="form-group col-md-4 col-xs-4 padding-left-none">
									<label for="freight-class-item-1">Freight class</label>
									<select class="form-control quote-input" name="freightClass-item-1" id="freight-class-item-1">
										<cfloop query="#qFreightClasses#">
											<option value="#Value#">#Name#</option>
										</cfloop>
									</select>
								</div>
								<!--- Hazmat --->
								<div class="form-group col-md-8 col-xs-8">
									<label>Hazmat?</label>
									<div>
										<div class="col-md-6 col-xs-6 col-sm-6">
											<input type="radio" name="hazmat-item-1" class="quote-input" id="yes-item-1" value="true">
											<label for="yes-item-1">Yes</label>
										</div>
										<div class="col-md-6 col-xs-6 col-sm-6">
											<input type="radio" name="hazmat-item-1" class="quote-input" id="no-item-1" value="false" checked="checked">
											<label for="no-item-1">No</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<button id="add-item" type="button" class="btn btn-default">Add new item</button>
				</fieldset>
			</div>
			<div class="button-holder">
				<input type="submit" class="btn btn-lg btn-primary" value="Show my rates">
			</div>
		</form>
	</div>
</cfoutput>
<cfinclude template="includes/footer.cfm">