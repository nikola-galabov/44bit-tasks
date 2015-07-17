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

	<cffunction name="getFreightClasses">
		<cfquery name="qFreightClasses">
			SELECT 
				`fcl_Name` as `Name`, `fcl_ClassID` as `Value`
			FROM
				`#THIS.datasource#`.`FreightClass`
		</cfquery>

		<cfreturn qFreightClasses/>
	</cffunction>
</cfcomponent>