<cfcomponent>
	<cfset THIS.datasource = "FreightAdmin" />

	<cffunction name="getLocationTypes">
		<cfquery name="qLocationTypes">
			SELECT 
				`loct_Text` as `Text`, `loct_Value` as `Value`
			FROM
				`#THIS.datasource#`.`FreightLocationType`
		</cfquery>

		<cfreturn "#qLocationTypes#"/>
	</cffunction>

	<cffunction name="getLocations">
		<cfargument name="zipCode" default="">
		<cfquery name="qLocations">
			SELECT 
				`loc_City` as `City`, `loc_ZipCode` as `ZipCode`,`loc_address` as `address` ,`loc_company` as `company`, `loc_State` as `State`
			FROM `Location`
			WHERE `loc_typeID` = 1 
			
			<cfif ARGUMENTS.zipCode NEQ "">
				AND `loc_ZipCode` = <cfqueryparam value="#zipCode#" cfsqltype="cf_sql_varchar">
			</cfif>
		</cfquery>

		<cfreturn "#qLocations#"/>
	</cffunction>

	<cffunction name="getCityByZipCode" datasource="OrderManager">
		<cfargument name="zip" required="true"/>
		
		<cfquery name="qCity">
			SELECT 
				`zipCode`, `City`, `State`
			FROM
				`ZipCodes`
			WHERE 
				`ZipCode` = <cfqueryparam value="#zip#" cfsqltype="cf_sql_char">
		</cfquery>

		<cfreturn #qCity#/>
	</cffunction>

	<cffunction name="getCities" access="remote" returntype="String" returnformat="JSON">
		<cfargument name="input" required="true">

		<cfquery name="qCities" datasource="OrderManager">
			SELECT
				`zipCode`, `City`, `State`
			FROM 
				`ZipCodes`
			WHERE
				`city` LIKE '%#ARGUMENTS.input#%' OR CAST(`zipCode` as CHAR) LIKE '%#ARGUMENTS.input#%'
			ORDER BY `city`
			LIMIT 20
		</cfquery>

		<cfset sResult = {}>
		<cfset aItems = arrayNew(1)>

		<cfset sResult['total_count'] = qCities.RecordCount>
		<cfset sResult['incomplete_results'] = false>
		<cfloop query="qCities">
			<cfset object = {}>
			<cfset object['id'] = "#qCities.zipCode#">
			<cfset object['city'] = "#qCities.City#">
			<cfset object['zipCode'] = "#qCities.zipCode#">
			<cfset object['state'] = "#qCities.state#">
			<cfset object['fullAddress'] = object['city'] & ', ' & object['state'] & ' ' & object['zipCode']>
			<cfset arrayAppend(aItems, object) >
		</cfloop>

		<cfset sResult['items'] = aItems>
		<cfreturn serializeJSON(sResult)>
	</cffunction>

</cfcomponent>