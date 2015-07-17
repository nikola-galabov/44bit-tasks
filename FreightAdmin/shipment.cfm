<!--- 
TODO LIST:
* toggle address inputs on click
* toggle Send copy 

--->
<param name="#FORM.originaddress#" value="">
<cfset cfcItem = new models.Item() />
<cfset cfcPickupAndDelivery = new models.PickupAndDelivery() />

<cfset qPackageTypes = cfcItem.getPackageTypes() />
<cfset isOther = false />

<cfif !structIsEmpty(FORM)>
	<cfset isOther = true />
</cfif>

<cfdump var="#FORM#" abort="false" />

<cfinclude template="includes/header.cfm" />
<cfoutput>
	<div class="container">
		<cfif isOther>
			<div id="quote-info" class="row">
				<div class="col-md-2">
					<span class="carrier-name">#FORM.carrierName#</span>
				</div>
				<div class="col-md-2">
					<span class="total">#FORM.total#</span>
					<span class="service-type">#FORM.serviceType#</span>
				</div>
				<div class="col-md-4">#FORM.originCity#, #FORM.originState# -> </div>
				<div class="col-md-2">
					<span class="days">#FORM.days# days</span>
				</div>
				<div class="col-md-2">Quote number?!!</div>
			</div>	
		</cfif>
		
		<!--- THE FORM --->
		<div class="row">
			<form class="container" action="" method="post">
				<!--- FROM --->
				<fieldset class="col-md-6">
					<legend>From</legend>
					<!--- PICKUP COMPANY --->
					<div class="form-group col-md-12">
						<label for="pickup-company">Company</label>
						<input id="pickup-company" class="form-control" type="text" name="pickup-company" value="#FORM.originCompany#" />
					</div>
					<!--- PICKUP ADDRESS --->
					<div id="pickup-address" class="form-group col-md-12">
						<input class="form-control" type="text" value="#FORM.originAddress#, #FORM.originCity#, #FORM.originState# #FORM.originZipCode#" disabled="disabled"/>
					</div>
					<div id="pickup-address-container" class="hidden">
						<!--- STREET ADDRESS --->
						<div class="form-group col-md-12">
							<label for="pickup-street-address">Street address</label>
							<input id="pickup-street-address" class="form-control" type="text" name="pickup-street-address" required/>
						</div>
						<!--- CITY STATE AND ZIPCODE --->
						<div class="form-group col-md-12">
							<label for="city-state-zip">City, state & zip code</label>
							<input id="pickup-city-state-zip" class="form-control" type="text" name="city-state-zip" required/>
						</div>
						<!--- LOCATION TYPE --->
						<div class="form-group col-md-12">
							<select name="pickup-location-type" class="form-control">
								<!--- TODO LOOP --->
								<option value="">Business w/ dock or forklift</option>
							</select>
						</div>
					</div>
					<!--- PICKUP CONTACT NAME --->
					<div class="form-group col-md-8">
						<label for="pickup-contact-name">Contact name</label>
						<input id="pickup-contact-name" class="form-control" type="text" name="pickup-contact-name" required/>
					</div>
					<!--- PICKUP CONTACT PHONE --->
					<div class="form-group col-md-4">
						<label for="pickup-contact-phone">Contact phone</label>
						<input id="pickup-contact-phone" class="form-control" type="text" name="pickup-contact-phone" required/>
					</div>
					<!--- READY TIME --->
					<div class="form-group col-md-4">
						<label for="pickup-ready-time">Ready time</label>
						<input id="pickup-ready-time" class="form-control" type="text" name="pickup-ready-time" required />
					</div>
					<!--- CLOSE TIME --->
					<div class="form-group col-md-4">
						<label for="pickup-close-time">Close time</label>
						<input id="pickup-close-time" class="form-control" type="text" name="pickup-close-time">
					</div>
					<!--- SHIPPER'S #--->
					<div class="form-group col-md-4">
						<label for="shipper-number">Shipper's ##</label>
						<input id="shipper-number" class="form-control" type="text" name="shipper-number">
					</div>
					<!--- SEND COPY --->
					<div class="form-group col-md-12">
						<input id="pickup-send-copy-option" type="checkbox" name="pickup-send-copy-option" />
						<label for="pickup-send-copy-option">Send copy of bill of landing</label>
					</div>
					<div id="pickup-send-copy" class="hidden">
						<div class="form-group col-md-12">
							<input id="pickup-send-copy-mail" class="form-control" type="email" name="pickup-send-copy-mail" />
						</div>
					</div>
					<!--- OTHER INSTRUCTIONS --->
					<div class="form-group col-md-12">
						<label for="pickup-other-instructions">Other pickup instructions</label>
						<textarea id="pickup-other-instructions" class="form-control" type="input" name="pickup-other-instructions"></textarea>
					</div>
				</fieldset>			
				<!--- TO --->
				<fieldset class="col-md-6">
					<legend>To</legend>
					<!--- DELIVERY COMPANY --->
					<div class="form-group col-md-12">
						<label for="delivery-company">Company</label>
						<input id="delivery-company" class="form-control" type="text" name="delivery-company"/>
					</div>
					<!--- DELIVERY ADDRESS --->
					<div id="delivery-address" class="form-group col-md-12">
						<input class="form-control" type="text" value="asdasdasd" disabled="disabled"/>
					</div>
					<div id="delivery-address-container" class="hidden">
						<!--- STREET ADDRESS --->
						<div class="form-group col-md-12">
							<label for="delivery-street-address">Street address</label>
							<input id="delivery-street-address" class="form-control" type="text" name="delivery-street-address" required/>
						</div>
						<!--- CITY STATE AND ZIPCODE --->
						<div class="form-group col-md-12">
							<label for="city-state-zip">City, state & zip code</label>
							<input id="delivery-city-state-zip" class="form-control" type="text" name="city-state-zip" required/>
						</div>
						<!--- LOCATION TYPE --->
						<div class="form-group col-md-12">
							<select name="delivery-location-type" class="form-control">
								<!--- TODO LOOP --->
								<option value="">Business w/ dock or forklift</option>
							</select>
						</div>
					</div>
					<!--- DELIVERY CONTACT NAME --->
					<div class="form-group col-md-8">
						<label for="delivery-contact-name">Contact name</label>
						<input id="delivery-contact-name" class="form-control" type="text" name="delivery-contact-name" required/>
					</div>
					<!--- DELIVERY CONTACT PHONE --->
					<div class="form-group col-md-4">
						<label for="delivery-contact-phone">Contact phone</label>
						<input id="delivery-contact-phone" class="form-control" type="text" name="delivery-contact-phone" required/>
					</div>
					<!--- READY TIME --->
					<div class="form-group col-md-4">
						<label for="delivery-open-time">Open time</label>
						<input id="delivery-open-time" class="form-control" type="text" name="delivery-open-time" required />
					</div>
					<!--- CLOSE TIME --->
					<div class="form-group col-md-4">
						<label for="delivery-close-time">Close time</label>
						<input id="delivery-close-time" class="form-control" type="text" name="delivery-close-time">
					</div>
					<!--- SHIPPER'S #--->
					<div class="form-group col-md-4">
						<label for="purchase-order-number">Purchase order ##</label>
						<input id="purchase-order-number" class="form-control" type="text" name="purchase-order-number">
					</div>
					<!--- SEND COPY --->
					<div class="form-group col-md-12">
						<input id="delivery-send-copy-option" type="checkbox" name="delivery-send-copy-option" />
						<label for="delivery-send-copy-option">Send copy of bill of landing</label>
					</div>
					<div id="delivery-send-copy" class="hidden">
						<div class="form-group col-md-12">
							<input id="delivery-send-copy-mail" class="form-control" type="email" name="delivery-send-copy-mail" />
						</div>
					</div>
					<!--- OTHER INSTRUCTIONS --->
					<div class="form-group col-md-12">
						<label for="delivery-other-instructions">Other delivery instructions</label>
						<textarea id="delivery-other-instructions" class="form-control" type="input" name="delivery-other-instructions"></textarea>
					</div>
				</fieldset>
				<!--- Items --->
				<fieldset id="shipment-items" class="col-md-12">
					<div class="shipment-item container">
						<div class="row">
							<div class="col-md-2">
								<button type="button">Edit</button>
							</div>
							<div class="col-md-10">
								num of pallets | Dimensions | Freight class | Total weight				
							</div>				
						</div>
						<div class="row">
							<div class="col-md-2 col-xs-12 col-sm-12">
								<span>Item</span>
							</div>
							<div class="col-md-10">
								<div class="col-md-2 col-xs-12 col-sm-12">
									<!--- Packaging --->
									<div class="form-group">
										<label for="package-item-1">Packaging</label>
										<select class="form-control" name="package-item-1" id="package-item-1" required>
											<cfloop query="#qPackageTypes#">
												<option value="#Value#">#Name#</option>
											</cfloop>
										</select>
									</div>
								</div>
								<div class="col-md-6 col-xs-12 col-sm-12">
									<!--- Description --->
									<div class="form-group">
										<label for="description-item-1">Description</label>
										<input type="text" id="description-item-1" class="form-control" name="description-item-1">
									</div>
								</div>
								<div class="col-md-2 col-xs-12 col-sm-12">
									<!--- NMFC Item # --->
									<div class="form-group">
										<label for="nmfc-item-1">NMFC Item ##</label>
										<input type="text" id="nmfc-item-1" class="form-control" name="nmfc-item-1">
									</div>
								</div>
								<div class="col-md-2">
									<!--- SAID TO CONTAIN --->
									<div class="form-group">
										<label for="said-to-contain">Said to contain</label>
										<input type="text" id="said-to-contain" class="form-control" name="said-to-contain">
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>

				<!--- ASK FOR SEND THE BILL OF LANDING TO MORE PEOPLE --->
			</form>
		</div>
	</div>
</cfoutput>
<cfinclude template="includes/footer.cfm" />