<cfcomponent>
	<!--- GET QUOTE BY ID --->
	<cffunction name="getQuoteById">
		<cfargument name="quoteId" required="true">
 
		<cfquery name="qQuote">
			SELECT
				`fqte_ID` as `id`, 
				`fqte_originCity` as `originCity`,
				`fqte_originState` as `originState`, 
				`fqte_originPostalCode` as `originPostalCode`,
				`fqte_originAddress` as `originAddress`,
				`fqte_originCompany` as `originCompany`,
				`fqte_pickupDate` as `pickupDate`,
				`fqte_destCity` as `destCity`,
				`fqte_destState` as `destState`, 
				`fqte_destPostalCode` as `destPostalCode`,
				`fqte_destAddress` as `destAddress`,
				`fqte_destCompany` as `destCompany`,
				`dt`.`loct_Value` as `destType`,
				`ot`.`loct_Value` as `originType`
			FROM
				`FreightAdmin`.`FreightQuote`
			JOIN
				`FreightAdmin`.`FreightLocationType` as `dt`
			ON
				`fqte_destTypeID` = `dt`.`loct_ID`
			JOIN
				`FreightAdmin`.`FreightLocationType` as `ot`
			ON
				`fqte_originTypeID` = `ot`.`loct_ID`
			WHERE
				`fqte_id` = <cfqueryparam
								value="#ARGUMENTS.quoteId#"
								cfsqltype="cf_sql_integer"
							>
		</cfquery>

		<cfreturn qQuote>
	</cffunction>

	<!--- GET ITEMS BY QUOTE ID --->
	<cffunction name="getQuoteItems">
		<cfargument name="quoteId" required="true">

		<cfquery name="qQuoteItems">
			SELECT 
				`qtei_weight` as `weight`,
				`qtei_hazardous` as `hazardous`,
				`qtei_length` as `length`,
				`qtei_width` as `width`,
				`qtei_height` as `height`,
				`qtei_quantity` as `quantity`,
				`fpt`.`fpt_Value` as `packaging`,
				`fcl`.`fcl_ClassID` as `freightClass`
			FROM 
				`FreightAdmin`.`QuoteItem`
			JOIN 
				`FreightAdmin`.`FreightPackageType` as `fpt`
			ON
				`qtei_PackageID` = `fpt`.`fpt_ID`
			JOIN
				`FreightAdmin`.`FreightClass` as `fcl`
			ON
				`qtei_FreightClassId` = `fcl`.`fcl_ID`
			WHERE 
				`qtei_FreightQuoteID` = <cfqueryparam
											value="#ARGUMENTS.quoteId#"	
											cfsqltype="cf_sql_integer"
										>
		</cfquery>

		<cfreturn qQuoteItems />
	</cffunction>

	<!--- GET THE SELECTED RATE  --->
	<cffunction name="getSelectedRate">
		<cfargument name="quoteId" required="true">

		<cfquery name="qRate">
			SELECT
				`rate_paymentTerms` as `paymentTerms`,
				`rate_total` as `total`,
				`rate_ref` as `ref`,
				`rate_days` as `days`,
				`rate_serviceType` as `serviceType`,
				`rate_serviceOption` as `serviceOption`,
				`rate_time` as `time`,
				`car`.`car_Name` as `carrier`
			FROM
				`FreightAdmin`.`FreightRate`
			JOIN 
				`FreightAdmin`.`FreightCarrier` as `car`
			ON
			    `rate_CarrierId` = `car`.`car_ID`
			WHERE 
				`rate_quoteID` = <cfqueryparam
									value="#ARGUMENTS.quoteId#"	
									cfsqltype="cf_sql_integer"
								>
			AND 
				`rate_isSelected` = 1
		</cfquery>

		<cfreturn qRate>

	</cffunction>

	<!--- INSERT QUOTE --->
	<cffunction name="insertQuote">
		<cfargument name="pickupDate" required />
		<cfargument name="originPostalCode" required />
		<cfargument name="destPostalCode" required />
		<cfargument name="originType" required />
		<cfargument name="destType" required />
		<cfargument name="destCity" default="" />
		<cfargument name="destState" default="" />
		<cfargument name="destAddress" default="" />
		<cfargument name="destCompany" default="" />
		<cfargument name="originCity" default="" />
		<cfargument name="originState" default="" />
		<cfargument name="originAddress" default="" />
		<cfargument name="originCompany" default="" />

		<cfset originTypeId = "" />
		<cfloop query="#getLocationId(ARGUMENTS.originType)#">
			<cfset originTypeId = loct_ID />
		</cfloop>

		<cfset destTypeId = "" />
		<cfloop query="#getLocationId(ARGUMENTS.destType)#">
			<cfset destTypeId = loct_ID />	
		</cfloop>

		<cfquery result="qQuote">
			INSERT INTO 
				`FreightAdmin`.`FreightQuote` 
				(
					`fqte_pickupDate`, 
					`fqte_originPostalCode`, 
					`fqte_destPostalCode`, 
					`fqte_originTypeID`, 
					`fqte_destTypeID`, 
					`fqte_destCity`, 
					`fqte_destState`, 
					`fqte_destAddress`, 
					`fqte_destCompany`, 
					`fqte_originCity`, 
					`fqte_originState`, 
					`fqte_originAddress`, 
					`fqte_originCompany`
				)
			VALUES	
				(
					<cfqueryparam value="#ARGUMENTS.pickupDate#" cfsqltype="cf_sql_datetime"/>,
					<cfqueryparam value="#ARGUMENTS.originPostalCode#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.destPostalCode#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#originTypeId#" cfsqltype="cf_sql_integer" />,
					<cfqueryparam value="#destTypeId#" cfsqltype="cf_sql_integer" />,					
					<cfqueryparam value="#ARGUMENTS.destCity#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.destState#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.destAddress#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.destCompany#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.originCity#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.originState#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.originAddress#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.originCompany#" cfsqltype="cf_sql_varchar"/>
				)
		</cfquery>

		<cfreturn #qQuote.GENERATEDKEY#/>	
	</cffunction>

	<!--- INSERT CHARGES --->
	<cffunction name="insertQuoteCharges">
		<cfargument name="quoteId" required="true" />
		<cfargument name="charge" required="true" />

		<cfloop query="#getExtraId(ARGUMENTS.charge)#">
			<cfset chargeId = fext_ID />
		</cfloop>

		<cfquery result="qCharge">
			INSERT INTO 
				`FreightAdmin`.`FreightQuote_FreightExtra` 
				(
					`fext_id`, 
					`fqte_id`
				) 
			VALUES 
				(
					<cfqueryparam value="#chargeId#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.quoteId#" cfsqltype="cf_sql_integer"/>
				)
		</cfquery>

		<cfreturn #qCharge.GENERATEDKEY# />
	</cffunction>

	<!--- INSERT ITEMS --->
	<cffunction name="insertItem">
		<cfargument name="package" />
		<cfargument name="weight" />
		<cfargument name="hazardous" />
		<cfargument name="height" />
		<cfargument name="length" />
		<cfargument name="freightClass" />
		<cfargument name="width" />
		<cfargument name="quoteId" />
		<cfargument name="quantity" />

		<cfloop query="#getPackageTypeId(ARGUMENTS.package)#">
			<cfset packageId = fpt_ID />
		</cfloop>

		<cfloop query="#getFreightClassId(ARGUMENTS.freightClass)#">
			<cfset freightClassId = fcl_ID />
		</cfloop>

		<cfquery result="qItem">
			INSERT INTO 
				`FreightAdmin`.`QuoteItem` 
				(
					`qtei_weight`, 
					`qtei_height`, 
					`qtei_length`, 
					`qtei_width`, 
					`qtei_hazardous`,
					`qtei_quantity`,
					`qtei_PackageID`, 
					`qtei_FreightClassID`, 
					`qtei_FreightQuoteID`
				) 
			VALUES
				(
					<cfqueryparam value="#ARGUMENTS.weight#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.height#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.length#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.width#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.hazardous#" cfsqltype="cf_sql_bit" />,
					<cfqueryparam value="#ARGUMENTS.quantity#" cfsqltype="cf_sql_integer" />,
					<cfqueryparam value="#packageId#" cfsqltype="cf_sql_integer" />,
					<cfqueryparam value="#freightClassId#" cfsqltype="cf_sql_integer" />,
					<cfqueryparam value="#ARGUMENTS.quoteId#" cfsqltype="cf_sql_integer" />
				)
		</cfquery>

		<cfreturn #qItem.GENERATEDKEY#/>	
	</cffunction>

	<!--- INSERT RATES --->
	<cffunction name="insertRate">
		<cfargument name="carrierCode" required="true" />
		<cfargument name="status" required="true" />
		<cfargument name="quoteId" required="true" />
		<cfargument name="paymentTerms" default="" />
		<cfargument name="error" default="" />
		<cfargument name="days" default="" />
		<cfargument name="interline" default="" />
		<cfargument name="ref" default="" />
		<cfargument name="serviceType" default="" />
		<cfargument name="serviceOption" default="" />
		<cfargument name="time" default="" />
		<cfargument name="total" default="" />
		<cfargument name="isSelected" default="0" />

		<cfloop query="#getCarrierId(ARGUMENTS.carrierCode)#">
			<cfset carrierId = car_ID />
		</cfloop>

		<cfquery result="qRate">
			INSERT INTO
				`FreightAdmin`.`FreightRate`
				(
					`rate_status`,
					`rate_paymentTerms`,
					`rate_error`,
					`rate_days`,
					`rate_ref`,
					`rate_serviceType`,
					`rate_serviceOption`,
					`rate_time`,
					`rate_total`,
					`rate_CarrierID`,
					`rate_QuoteID`,
					`rate_isSelected`
				)
			VALUES 
				(
					<cfqueryparam value="#ARGUMENTS.status#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.paymentTerms#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.error#" cfsqltype="cf_sql_text"/>,
					<cfqueryparam value="#ARGUMENTS.days#" cfsqltype="cf_sql_integer" null="#!val(arguments.days)#"/>,
					<cfqueryparam value="#ARGUMENTS.ref#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.serviceType#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.serviceOption#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.time#" cfsqltype="cf_sql_varchar"/>,
					<cfqueryparam value="#ARGUMENTS.total#" cfsqltype="cf_sql_decimal" scale="2" null="#!val(arguments.total)#"/>,
					<cfqueryparam value="#carrierId#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.quoteId#" cfsqltype="cf_sql_integer"/>,
					<cfqueryparam value="#ARGUMENTS.isSelected#" cfsqltype="cf_sql_tinyint"/>
				)
		</cfquery>

		<cfreturn #qRate.GENERATEDKEY# />
	</cffunction>

	<!--- GET LOCATION ID --->
	<cffunction name="getLocationId">
		<cfargument name="value" type="string" required="true" />

		<cfquery name="qLocation">
			SELECT 
				`loct_ID` 
			FROM 
				`FreightAdmin`.`FreightLocationType` 
			WHERE 
				`loct_value` = <cfqueryparam 
									value="#ARGUMENTS.value#" 
									cfsqltype="cf_sql_varchar"
								/>
			LIMIT 1
		</cfquery>

		<cfreturn "#qLocation#">
	</cffunction>

	<!--- GET CARRIER ID --->
	<cffunction name="getCarrierId">
		<cfargument name="carrierCode" type="string" required="true" />

		<cfquery name="qCarrier">
			SELECT 
				`car_ID` 
			FROM 
				`FreightAdmin`.`FreightCarrier` 
			WHERE 
				`car_Code` = <cfqueryparam 
									value="#ARGUMENTS.carrierCode#" 
									cfsqltype="cf_sql_varchar"
								/>
			LIMIT 1
		</cfquery>

		<cfreturn "#qCarrier#">
	</cffunction>

	<!--- GET PACKAGE TYPE ID --->
	<cffunction name="getPackageTypeId">
		<cfargument name="value" type="string" required="true" />

		<cfquery name="qPackageType">
			SELECT 
				`fpt_ID` 
			FROM 
				`FreightAdmin`.`FreightPackageType` 
			WHERE 
				`fpt_value` = <cfqueryparam 
								value="#ARGUMENTS.value#" 
								cfsqltype="cf_sql_varchar"
							/>
			LIMIT 1
		</cfquery>

		<cfreturn "#qPackageType#">
	</cffunction>

	<!--- GET FREIGHT EXTRA ID --->
	<cffunction name="getExtraId">
		<cfargument name="value" type="string" required="true" />

		<cfquery name="qCharge">
			SELECT 
				`fext_ID` 
			FROM 
				`FreightAdmin`.`FreightExtra` 
			WHERE 
				`fext_value` = <cfqueryparam 
								value="#ARGUMENTS.value#" 
								cfsqltype="cf_sql_varchar"
							/>
			LIMIT 1
		</cfquery>

		<cfreturn "#qCharge#">
	</cffunction>

	<!--- GET FREIGHT CLASS ID --->
	<cffunction name="getFreightClassId">
		<cfargument name="value" type="string" required="true" />

		<cfquery name="qFreightClass">
			SELECT 
				`fcl_ID` 
			FROM 
				`FreightAdmin`.`FreightClass` 
			WHERE 
				`fcl_classID` = <cfqueryparam 
								value="#ARGUMENTS.value#" 
								cfsqltype="cf_sql_varchar"
							/>
			LIMIT 1
		</cfquery>

		<cfreturn "#qFreightClass#">
	</cffunction>
</cfcomponent>























