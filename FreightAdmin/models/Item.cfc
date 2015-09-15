<cfcomponent>
	<cfset THIS.datasource = "FreightAdmin" />

	<cffunction name="getPackageTypes">
		<cfquery name="qPackageType">
			SELECT 
				`fpt_Name` as `Name`, `fpt_Value` as `Value`
			FROM
				`#THIS.datasource#`.`FreightPackageType`
		</cfquery>

		<cfreturn qPackageType/>
	</cffunction>

	<cffunction access="remote" name="getPackageTypesRemote" returntype="String" returnformat="JSON">
		<cfset qPackageTypes = getPackageTypes() />
		<cfset aResult = arrayNew(1)>
		
		<cfloop query="qPackageTypes">
			<cfset object = {}>
			<cfset object['value'] = "#qPackageTypes.value#">
			<cfset object['name'] = "#qPackageTypes.name#">
			<cfset arrayAppend(aResult, object) >
		</cfloop>

		<cfreturn serializeJSON(aResult)>
	</cffunction>

	<cffunction name="getFreightClasses">
		<cfquery name="qFreightClasses">
			SELECT 
				`fcl_Name` as `Name`, `fcl_ClassID` as `Value`
			FROM
				`#THIS.datasource#`.`FreightClass`
		</cfquery>

		<cfreturn qFreightClasses/>
	</cffunction>

	<cffunction access="remote" name="getFreightClassesRemote" returntype="String" returnformat="JSON">
		<cfset qFreightClasses = getFreightClasses() />
		
		<cfset aResult = arrayNew(1)>
		
		<cfloop query="qFreightClasses">
			<cfset object = {}>
			<cfset object['value'] = "#qFreightClasses.value#">
			<cfset object['name'] = "#qFreightClasses.name#">
			<cfset arrayAppend(aResult, object) >
		</cfloop>

		<cfreturn serializeJSON(aResult)>
	</cffunction>
</cfcomponent>