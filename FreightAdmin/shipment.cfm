<cfparam name="URL.quoteId" default="" />
<cfparam name="URL.quoteType" default="" />

<cfset cfcItem = new models.Item() />
<cfset cfcPickupAndDelivery = new models.PickupAndDelivery() />
<cfset cfcQuote = new models.Quote() />

<cfset qQuoteItems = cfcQuote.getQuoteItems(URL.quoteId) />
<cfset qSelectedRate = cfcQuote.getSelectedRate(URL.quoteId) />
<cfset qPackageTypes = cfcItem.getPackageTypes() />
<cfset qLocationTypes = cfcPickupAndDelivery.getLocationTypes() />
<cfset qFreightClasses = cfcItem.getFreightClasses() />
<cfset qLocations = cfcPickupAndDelivery.getLocations() />

<cfinclude template="includes/header.cfm" />

<cfset qQuote = cfcQuote.getQuoteById(URL.quoteId) />
<!--- <cfdump var="#qQuote#" /> --->
<cfloop query="#qQuote#">
	<cfset destFullAddress = (destAddress NEQ "") ? destAddress & ', ' & destCity & ', ' & destState & ' ' & destPostalCode : destCity & ', ' & destState & ' ' & destPostalCode />
	<cfset originFullAddress = (originAddress NEQ "") ? originAddress & ', ' & originCity & ', ' & originState & ' ' & originPostalCode : originCity & ', ' & originState & ' ' & originPostalCode />

<!--- 	<cfdump var="#qQuoteItems#" abort="false" />
	<cfdump var="#qSelectedRate#" abort="false" />
	<cfdump var="#qPackageTypes#" />
	<cfdump var="#qLocationTypes#" />
	<cfdump var="#qFreightClasses#" />
	<cfdump var="#qLocations#" />
	<cfdump var="#cfcQuote.getQuoteById(URL.quoteId)#" /> --->

	<cfoutput>		
		<div class="container">
			<h3>
				Schedule a pickup > #pickupDate#
			</h3>
			<!--- <cfif isOther> --->
				<div id="quote-info" class="row">
					<div class="col-md-2">
						<span class="carrier-name">#qSelectedRate.carrier#</span>
					</div>
					<div class="col-md-2">					
						<span class="glyphicon glyphicon-usd" aria-hidden="true"></span><span class="total">#qSelectedRate.total#</span>
						<span class="service-type">#qSelectedRate.serviceType#</span>
					</div>
					<div class="col-md-4"> <span class="glyphicon glyphicon-stop origin"></span> #originCity#, #originState# -> <span class="glyphicon glyphicon-stop dest"></span> #destCity#, #destState#</div>
					<div class="col-md-2">
						<span class="days">#qSelectedRate.days# days</span>
					</div>
					<cfset quoteNumber = (qSelectedRate.ref NEQ 'undefined') ? 'Quote ## ' & qSelectedRate.ref : '{No quote ##}' />
					<div class="col-md-2">#quoteNumber# </div>
				</div>	
			<!--- </cfif> --->
			
			<!--- THE FORM --->
			<div class="row">
				<form class="container" id="shipment-form" action="hidden" method="post">
					<!--- FROM --->
					<fieldset class="col-md-6">
						<legend>From</legend>
						<!--- PICKUP COMPANY --->
						<div class="form-group col-md-12">
							<label for="pickup-company">Company</label>
							<input id="pickup-company" class="form-control" type="text" name="pickup-company" value="#originCompany#" />
						</div>
						
						<cfif #originAddress# EQ "">
							<!--- STREET ADDRESS --->
							<div class="form-group col-md-12">
								<label for="pickup-street-address">Street address</label>
								<input id="pickup-street-address" class="form-control" type="text" name="pickup-street-address" value="#originAddress#" required/>
							</div>
						</cfif>

						<!--- PICKUP ADDRESS --->
						<div id="pickup-address" class="form-group col-md-12 edit-address" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Warning" data-content="Changing this may affect price of the shipment!">
							<input class="form-control origin" type="text" value="#originFullAddress#" disabled="disabled"/>
						</div>
						<div id="pickup-address-container" class="hidden">
							<cfif #originAddress# NEQ "">
								<!--- STREET ADDRESS --->
								<div class="form-group col-md-12">
									<label for="pickup-street-address">Street address</label>
									<input id="pickup-street-address" class="form-control" type="text" name="pickup-street-address" value="#originAddress#" required/>
								</div>
							</cfif>	
							<!--- CITY STATE AND ZIPCODE --->
							<div class="form-group col-md-12">
								<label for="pickup-city-state-zip">City, state & zip code</label>
								<!--- <input id="pickup-city-state-zip" class="form-control" type="text" name="pickup-city-state-zip" value="#originCity#, #originState#, #originPostalCode#" required /> --->
								<cfif URL.quoteType EQ "outbound" OR URL.quoteType EQ "transfer">
									<select id="pickup-city-state-zip" class="form-control" name="pickup-city-state-zip" required>
										
										<cfloop query="#qLocations#">
											<cfset selected="" />
											<cfif qQuote.originPostalCode EQ zipCode>
												<cfset selected="selected=selected" />
											</cfif>
											<option value="#city#, #State#, #ZipCode#" #selected#>
												#city#, #State#, #ZipCode#
											</option>
										</cfloop>
									</select>
								<cfelse>
									<select id="pickup-city-state-zip" class="form-control select2-ajax" name="pickup-city-state-zip" required>
										<option value="#originCity#, #originState#, #originPostalCode#">#originCity#, #originState#, #originPostalCode#</option>
									</select>
								</cfif>
							</div>
							<!--- LOCATION TYPE --->
							<div class="form-group col-md-12">
								<select id="pickup-location-type" name="pickup-location-type" class="form-control">
									<cfloop query="#qLocationTypes#">
										<cfset selected = "" />
										<cfif value EQ qQuote.originType>
											<cfset selected = "selected=selected" />
										</cfif>
										<option value="#value#" #selected#>#text#</option>
									</cfloop>
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
							<input id="pickup-ready-time" class="form-control" type="text" name="pickup-ready-time" />
						</div>
						<!--- CLOSE TIME --->
						<div class="form-group col-md-4">
							<label for="pickup-close-time">Close time</label>
							<input id="pickup-close-time" class="form-control" type="text" name="pickup-close-time" />
						</div>
						<!--- SHIPPER'S #--->
						<div class="form-group col-md-4">
							<label for="shipper-number">Shipper's ##</label>
							<input id="shipper-number" class="form-control" type="text" name="shipper-number" />
						</div>
						<!--- SEND COPY --->
						<div class="form-group col-md-12">
							<input id="pickup-send-copy-option" type="checkbox" name="pickup-send-copy-option" />
							<label for="pickup-send-copy-option">Send copy of bill of landing</label>
						</div>
						<div id="pickup-send-copy">
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
							<input id="delivery-company" class="form-control" type="text" name="delivery-company" value="#destCompany#"/>
						</div>

						<!--- DELIVERY ADDRESS --->
						<cfif destAddress EQ "">
							<!--- STREET ADDRESS --->
							<div class="form-group col-md-12">
								<label for="delivery-street-address">Street address</label>
								<input id="delivery-street-address" class="form-control" type="text" name="delivery-street-address" required value="#destAddress#"/>
							</div>
						</cfif>

						<div id="delivery-address" class="form-group col-md-12 edit-address" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Warning" data-content="Changing this may affect price of the shipment!">
							<input class="form-control dest" type="text" disabled="disabled" value="#destFullAddress#" />
						</div>
						<div id="delivery-address-container" class="hidden">
							<cfif destAddress NEQ "">
								<!--- STREET ADDRESS --->
								<div class="form-group col-md-12">
									<label for="delivery-street-address">Street address</label>
									<input id="delivery-street-address" class="form-control" type="text" name="delivery-street-address" required value="#destAddress#"/>
								</div>
							</cfif>
							<!--- CITY STATE AND ZIPCODE --->
							<div class="form-group col-md-12">
								<label for="delivery-city-state-zip">City, state & zip code</label>
								<!--- <input id="delivery-city-state-zip" class="form-control" type="text" name="delivery-city-state-zip" required value="#destCity#, #destState# #destZipCode#" /> --->
								<cfif URL.quoteType EQ "inbound" OR URL.quoteType EQ "transfer">
									<select id="delivery-city-state-zip" class="form-control" name="delivery-city-state-zip" required>
										<cfloop query="#qLocations#">
											<cfset selected="" />
											<cfif qQuote.destPostalCode EQ zipCode>
												<cfset selected="selected=selected" />
											</cfif>
											<option value="#city#, #State#, #ZipCode#" #selected#>
												#city#, #State#, #ZipCode#
											</option>
										</cfloop>
									</select>
								<cfelse>
									<select id="delivery-city-state-zip" class="form-control select2-ajax" name="delivery-city-state-zip" required>
										<option value="#qQuote.destCity#, #qQuote.destState# #qQuote.destPostalCode#">
											#qQuote.destCity#, #qQuote.destState# #qQuote.destPostalCode#
										</option>
									</select>
								</cfif>
							</div>
							<!--- LOCATION TYPE --->
							<div class="form-group col-md-12">
								<select id="delivery-location-type" name="delivery-location-type">
									<cfloop query="qLocationTypes">
										<cfset selected = "" />
										<cfif value EQ qQuote.destType>
											<cfset selected = "selected=selected" />
										</cfif>
										<option value="#value#" #selected#>#text#</option>
									</cfloop>
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
							<input id="delivery-open-time" class="form-control" type="text" name="delivery-open-time" />
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
						<div id="delivery-send-copy">
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
					<fieldset class="col-md-12">
						<div id="shipment-items">
							<cfloop query="#qQuoteItems#">
								<div id="shipment-item-#CURRENTROW#" class="shipment-item container-fluid">
									<div class="row">
										<button type="button" id="edit-item-#CURRENTROW#" data-item-number="#CURRENTROW#" class="col-md-1 edit-item-btn" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Warning" data-content="Changing this may affect price of the shipment!">Edit</button>
										<div class="col-md-11 shipment-item-info">
											<span class="info-span info-span-quantity">
												<span class="value">
													#quantity#
												</span> Pallets
											</span>
											<span class="info-span info-span-dimensions">
												Dimensions
												<span class="value">
													#length# x #width# x #height#
												</span>
											</span>
											<span class="info-span info-span-freight-class">
												Freight class
												<span class="value">
													#freightClass#
												</span>
											</span>
											<span class="info-span info-span-weight">
												Total weight
												<span class="value">
													#weight#
												</span>
												<span class="info-span-weight-unit">
												lbs
												</span>	
											</span>
										</div>				
									</div>
									<div class="row item-second-row">
										<div class="col-md-1 item-number-container">
											<span>Item #CURRENTROW#</span>
										</div>
										<div class="col-md-11 item-data-container">
											<div class="row">
												<!--- Packaging --->
												<div class="form-group col-md-3">
													<label for="package-item-#CURRENTROW#">Packaging</label>
													<select class="form-control" name="package-item-#CURRENTROW#" id="package-item-#CURRENTROW#" required>
														<cfloop query="#qPackageTypes#">
															<cfset selected = "" />
															<cfif qQuoteItems.packaging EQ value>
																<cfset selected = 'selected="selected"' />
															</cfif>
															<option #selected# value="#value#">#Name#</option>
														</cfloop>
													</select>
												</div>
																				
												<!--- Description --->
												<div class="form-group col-md-3">
													<label for="description-item-#CURRENTROW#">Description</label>
													<input type="text" id="description-item-#CURRENTROW#" class="form-control" name="description-item-#CURRENTROW#">
												</div>
											
												<!--- NMFC Item # --->
												<div class="form-group col-md-3">
													<label for="nmfc-item-#CURRENTROW#">NMFC Item ##</label>
													<input type="text" id="nmfc-item-#CURRENTROW#" class="form-control" name="nmfc-item-#CURRENTROW#">
												</div>
											
												<!--- SAID TO CONTAIN --->
												<div class="form-group col-md-3">
													<label for="said-to-contain">Said to contain</label>
													<input type="text" id="said-to-contain" class="form-control" name="said-to-contain">
												</div>
											</div>
											<div class="item-details hidden">
												<div class="row">
													<!--- Quantity --->
													<div class="form-group col-md-1">
														<label for="quantity-item-#CURRENTROW#">Quantity</label>
														<input class="form-control calculation item-input" type="number" name="quantity-item-#CURRENTROW#" id="quantity-item-#CURRENTROW#" value="#quantity#" required>
													</div>

													<!--- Weight --->
													<div class="form-group col-md-2">
														<label for="weight-item-#CURRENTROW#">Weight</label>
														<input class="form-control calculation item-input" type="number" name="weight-item-#CURRENTROW#" id="weight-item-#CURRENTROW#" value="#weight#" required>
													</div>	

													<!--- lbs/kg --->
													<div class="form-group col-md-2">
														<label for="weight-unit-item-#CURRENTROW#">Lbs/Kg</label>
														<select class="form-control" name="weightUnit-item-#CURRENTROW#" id="weight-unit-item-#CURRENTROW#">
															<option value="lbs" selected="selected">Lbs</option>
															<option value="kg">Kg</option>
														</select>
													</div>
													<!--- Length --->
													<div class="form-group col-md-2">
														<label for="length-item-#CURRENTROW#">Length</label>
														<input class="form-control calculation item-input" type="number" name="length-item-#CURRENTROW#" id="length-item-#CURRENTROW#" value="#length#" required>
													</div>

													<!--- Width --->
													<div class="form-group col-md-2">
														<label for="width-item-#CURRENTROW#">Width</label>
														<input class="form-control calculation item-input" type="number" name="width-item-#CURRENTROW#" id="width-item-#CURRENTROW#" value="#width#" required>
													</div>

													<!--- Height --->
													<div class="form-group col-md-2">
														<label for="height-item-#CURRENTROW#">Height</label>
														<input class="form-control calculation item-input" type="number" name="height-item-#CURRENTROW#" id="height-item-#CURRENTROW#" value="#height#" required>
													</div>

													<!--- In/cm --->
													<div class="form-group col-md-1">
														<label for="length-unit-item-#CURRENTROW#">In/Cm</label>
														<select class="form-control" name="lengthUnit-item-#CURRENTROW#" id="length-unit-item-#CURRENTROW#">
															<option value="in" selected="selected">In</option>
															<option value="cm">Cm</option>
														</select>
													</div>
												</div>

												<div class="row">											
													<!--- Freight class --->
													<div class="form-group col-md-2">
														<label for="freight-class-item-#CURRENTROW#">Freight class</label>
														<select class="form-control" name="freightClass-item-#CURRENTROW#" id="freight-class-item-#CURRENTROW#">
															<cfloop query="#qFreightClasses#">
																<cfset selected=""/>
																<cfif qQuoteItems.freightClass EQ value>
																	<cfset selected="selected=selected"/>
																</cfif>
																<option value="#Value#" #selected#>#Name#</option>
															</cfloop>
														</select>
													</div>
													<!--- Hazmat --->
													<div class="form-group col-md-2">
														<label>Hazmat?</label>
														<div>
															<cfset hazmatChecked = ""/>
															<cfset noHazmatChecked = "checked=checked"/>

															<cfif qQuoteItems.hazardous EQ true>
																<cfset hazmatChecked = "checked=checked"/>
																<cfset noHazmatChecked = ""/>
															</cfif>
															<input type="radio" name="hazmat-item-#CURRENTROW#" id="yes-item-#CURRENTROW#" value="true" #hazmatChecked#>
															<label for="yes-item-#CURRENTROW#">Yes</label>
															<input type="radio" name="hazmat-item-#CURRENTROW#" id="no-item-#CURRENTROW#" value="false" #noHazmatChecked#>
															<label for="no-item-#CURRENTROW#">No</label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</cfloop>
						</div>
						<button id="add-shipment-item" class="btn btn-default" type="button">Add item</button>
					</fieldset>
					<!--- ASK FOR SEND THE BILL OF LANDING TO MORE PEOPLE --->
					<input type="submit" class="btn btn-lg btn-primary pull-right">
				</form>
			</div>
		</div>
	</cfoutput>
</cfloop>
<cfinclude template="includes/footer.cfm" />