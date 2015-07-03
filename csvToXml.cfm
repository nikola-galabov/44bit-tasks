<cffunction
    name="CSVToArray"
    access="public"
    returntype="array"
    output="false"
    hint="Takes a delimited text data file or chunk of delimited data and converts it to an array of arrays.">
 
    <!--- Define the arguments. --->
    <cfargument
        name="CSVData"
        type="string"
        required="false"
        default=""
        hint="This is the raw CSV data. This can be used if instead of a file path."
        />
 
    <cfargument
        name="CSVFilePath"
        type="string"
        required="false"
        default=""
        hint="This is the file path to a CSV data file. This can be used instead of a text data blob."
        />
 
    <cfargument
        name="Delimiter"
        type="string"
        required="false"
        default=","
        hint="The character that separate fields in the CSV."
        />
 
    <cfargument
        name="Qualifier"
        type="string"
        required="false"
        default=""""
        hint="The field qualifier used in conjunction with fields that have delimiters (not used as delimiters ex: 1,344,343.00 where [,] is the delimiter)."
        />
 
 
    <!--- Define the local scope. --->
    <cfset var LOCAL = StructNew() />
 
    <!---
        Check to see if we are dealing with a file. If we are,
        then we will use the data from the file to overwrite
        any csv data blob that was passed in.
    --->
    <cfif (
        Len( ARGUMENTS.CSVFilePath ) AND
        FileExists( ARGUMENTS.CSVFilePath )
        )>
 
        <!---
            Read the data file directly into the arguments scope
            where it can override the blod data.
        --->
        <cffile
            action="READ"
            file="#ARGUMENTS.CSVFilePath#"
            variable="ARGUMENTS.CSVData"
            />
 
    </cfif>
 
 
    <!---
        ASSERT: At this point, whether we got the CSV data
        passed in as a data blob or we read it in from a
        file on the server, we now have our raw CSV data in
        the ARGUMENTS.CSVData variable.
    --->
 
 
    <!---
        Make sure that we only have a one character delimiter.
        I am not going traditional ColdFusion style here and
        allowing multiple delimiters. I am trying to keep
        it simple.
    --->
    <cfif NOT Len( ARGUMENTS.Delimiter )>
 
        <!---
            Since no delimiter was passed it, use thd default
            delimiter which is the comma.
        --->
        <cfset ARGUMENTS.Delimiter = "," />
 
    <cfelseif (Len( ARGUMENTS.Delimiter ) GT 1)>
 
        <!---
            Since multicharacter delimiter was passed, just
            grab the first character as the true delimiter.
        --->
        <cfset ARGUMENTS.Delimiter = Left(
            ARGUMENTS.Delimiter,
            1
            ) />
 
    </cfif>
 
 
    <!---
        Make sure that we only have a one character qualifier.
        I am not going traditional ColdFusion style here and
        allowing multiple qualifiers. I am trying to keep
        it simple.
    --->
    <cfif NOT Len( ARGUMENTS.Qualifier )>
 
        <!---
            Since no qualifier was passed it, use thd default
            qualifier which is the quote.
        --->
        <cfset ARGUMENTS.Qualifier = """" />
 
    <cfelseif (Len( ARGUMENTS.Qualifier ) GT 1)>
 
        <!---
            Since multicharacter qualifier was passed, just
            grab the first character as the true qualifier.
        --->
        <cfset ARGUMENTS.Qualifier = Left(
            ARGUMENTS.Qualifier,
            1
            ) />
 
    </cfif>
 
 
    <!--- Create an array to handel the rows of data. --->
    <cfset LOCAL.Rows = ArrayNew( 1 ) />
 
    <!---
        Split the CSV data into rows of raw data. We are going
        to assume that each row is delimited by a return and
        / or a new line character.
    --->
    <cfset LOCAL.RawRows = ARGUMENTS.CSVData.Split(
        "\r\n?"
        ) />
 
 
    <!--- Loop over the raw rows to parse out the data. --->
    <cfloop
        index="LOCAL.RowIndex"
        from="1"
        to="#ArrayLen( LOCAL.RawRows )#"
        step="1">
 
 
        <!--- Create a new array for this row of data. --->
        <cfset ArrayAppend( LOCAL.Rows, ArrayNew( 1 ) ) />
 
 
        <!--- Get the raw data for this row. --->
        <cfset LOCAL.RowData = LOCAL.RawRows[ LOCAL.RowIndex ] />
 
 
        <!---
            Replace out the double qualifiers. Two qualifiers in
            a row acts as a qualifier literal (OR an empty
            field). Replace these with a single character to
            make them easier to deal with. This is risky, but I
            figure that Chr( 1000 ) is something that no one
            is going to use (or is it????).
        --->
        <cfset LOCAL.RowData = LOCAL.RowData.ReplaceAll(
            "[\#ARGUMENTS.Qualifier#]{2}",
            Chr( 1000 )
            ) />
 
        <!--- Create a new string buffer to hold the value. --->
        <cfset LOCAL.Value = CreateObject(
            "java",
            "java.lang.StringBuffer"
            ).Init()
            />
 
 
        <!---
            Set an initial flag to determine if we are in the
            middle of building a value that is contained within
            quotes. This will alter the way we handle
            delimiters - as delimiters or just character
            literals.
        --->
        <cfset LOCAL.IsInField = false />
 
 
        <!--- Loop over all the characters in this row. --->
        <cfloop
            index="LOCAL.CharIndex"
            from="1"
            to="#LOCAL.RowData.Length()#"
            step="1">
 
 
            <!---
                Get the current character. Remember, since Java
                is zero-based, we have to subtract one from out
                index when getting the character at a
                given position.
            --->
            <cfset LOCAL.ThisChar = LOCAL.RowData.CharAt(
                JavaCast( "int", (LOCAL.CharIndex - 1))
                ) />
 
 
            <!---
                Check to see what character we are dealing with.
                We are interested in special characters. If we
                are not dealing with special characters, then we
                just want to add the char data to the ongoing
                value buffer.
            --->
            <cfif (LOCAL.ThisChar EQ ARGUMENTS.Delimiter)>
 
                <!---
                    Check to see if we are in the middle of
                    building a value. If we are, then this is a
                    character literal, not an actual delimiter.
                    If we are NOT buildling a value, then this
                    denotes the end of a value.
                --->
                <cfif LOCAL.IsInField>
 
                    <!--- Append char to current value. --->
                    <cfset LOCAL.Value.Append(
                        LOCAL.ThisChar.ToString()
                        ) />
 
 
                <!---
                    Check to see if we are dealing with an
                    empty field. We will know this if the value
                    in the field is equal to our "escaped"
                    double field qualifier (see above).
                --->
                <cfelseif (
                    (LOCAL.Value.Length() EQ 1) AND
                    (LOCAL.Value.ToString() EQ Chr( 1000 ))
                    )>
 
                    <!---
                        We are dealing with an empty field so
                        just append an empty string directly to
                        this row data.
                    --->
                    <cfset ArrayAppend(
                        LOCAL.Rows[ LOCAL.RowIndex ],
                        ""
                        ) />
 
 
                    <!---
                        Start new value buffer for the next
                        row value.
                    --->
                    <cfset LOCAL.Value = CreateObject(
                        "java",
                        "java.lang.StringBuffer"
                        ).Init()
                        />
 
                <cfelse>
 
                    <!---
                        Since we are not in the middle of
                        building a value, we have reached the
                        end of the field. Add the current value
                        to row array and start a new value.

                        Be careful that when we add the new
                        value, we replace out any "escaped"
                        qualifiers with an actual qualifier
                        character.
                    --->
                    <cfset ArrayAppend(
                        LOCAL.Rows[ LOCAL.RowIndex ],
                        LOCAL.Value.ToString().ReplaceAll(
                            "#Chr( 1000 )#{1}",
                            ARGUMENTS.Qualifier
                            )
                        ) />
 
 
                    <!---
                        Start new value buffer for the next
                        row value.
                    --->
                    <cfset LOCAL.Value = CreateObject(
                        "java",
                        "java.lang.StringBuffer"
                        ).Init()
                        />
 
                </cfif>
 
 
            <!---
                Check to see if we are dealing with a field
                qualifier being used as a literal character.
                We just have to be careful that this is NOT
                an empty field (double qualifier).
            --->
            <cfelseif (LOCAL.ThisChar EQ ARGUMENTS.Qualifier)>
 
                <!---
                    Toggle the field flag. This will signal that
                    future characters are part of a single value
                    despite and delimiters that might show up.
                --->
                <cfset LOCAL.IsInField = (NOT LOCAL.IsInField) />
 
 
            <!---
                We just have a non-special character. Add it
                to the current value buffer.
            --->
            <cfelse>
 
                <cfset LOCAL.Value.Append(
                    LOCAL.ThisChar.ToString()
                    ) />
 
            </cfif>
 
 
            <!---
                If we have no more characters left then we can't
                ignore the current value. We need to add this
                value to the row array.
            --->
            <cfif (LOCAL.CharIndex EQ LOCAL.RowData.Length())>
 
                <!---
                    Check to see if the current value is equal
                    to the empty field. If so, then we just
                    want to add an empty string to the row.
                --->
                <cfif (
                    (LOCAL.Value.Length() EQ 1) AND
                    (LOCAL.Value.ToString() EQ Chr( 1000 ))
                    )>
 
                    <!---
                        We are dealing with an empty field.
                        Just add the empty string.
                    --->
                    <cfset ArrayAppend(
                        LOCAL.Rows[ LOCAL.RowIndex ],
                        ""
                        ) />
 
                <cfelse>
 
                    <!---
                        Nothing special about the value. Just
                        add it to the row data.
                    --->
                    <cfset ArrayAppend(
                        LOCAL.Rows[ LOCAL.RowIndex ],
                        LOCAL.Value.ToString().ReplaceAll(
                            "#Chr( 1000 )#{1}",
                            ARGUMENTS.Qualifier
                            )
                        ) />
 
                </cfif>
 
            </cfif>
 
        </cfloop>
 
    </cfloop>
 
    <!--- Return the row data. --->
    <cfreturn( LOCAL.Rows ) />
 
</cffunction>

<cffile action="read" file="/Users/nikolag/Development/tasks/Products.csv" variable="test" />
<!--- <cfset xmlDoc = XMLParse(#test#)> --->

<cfset arrCSV = CSVToArray(
    CSVData = test,
    Delimiter = ",",
    Qualifier = """"
    ) />
 
<!--- Dump out array. --->
<cfdump var="#arrCSV#" label="CSV Data" />

<!--- <cfset query = #queryNew("ProductID, ProductName,	Manufacturer, Description, ProductURL, ImageURL")# /> --->
<!--- <cfspreadsheet  
    action="read" 
    src = "/Users/nikolag/Development/tasks/Products.csv" 
    excludeHeaderRow = "true" 
    name = "test"> --->

<!--- <cfspreadsheet action="read"
 src="/Users/nikolag/Development/tasks/Products2.csv" 
 format="csv" 
 query="query"> 

<cfdump var="#queryData#" /> --->



