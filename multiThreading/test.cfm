<!--- ThreadA loops to simulate an activity that might take time. ---> 
<cfthread name="threadA" action="run"> 
        <cfset thread.j=1> 
        <cfloop index="i" from="1" to="1000000"> 
         <cfset thread.j=thread.j+1>  
    </cfloop> 
</cfthread> 
 
<!--- ThreadB loops, waiting until threadA finishes looping 40000 times.  
            the loop code sleeps 1/2 second each time. --->  
<cfthread name="threadB" action="run"> 
    <cfscript> 
        thread.sleepTimes=0; 
        thread.initialized=false; 
        while ((threadA.Status != "TERMINATED") && (threadA.j < 400000)) { 
            sleep(500); 
            thread.sleeptimes++; 
        } 
        // Don't continue processing if threadA terminated abnormally. 
        If (threadA.Status != "TERMINATED") { 
            thread.initialized=true; 
            // Do additional processing here. 
        } 
    </cfscript> 
</cfthread> 
 
<!--Join the page thread to thread B. Don't join to thread A.---> 
<cfthread action="join" name="threadB" timeout="10000" /> 
 
<!--- Display the thread information. ---> 
<cfoutput> 
    current threadA index value: #threadA.j#<br /> 
    threadA status: #threadA.Status#<br> 
    threadB status: #threadB.Status#<br> 
    threadB sleepTimes: #threadB.sleepTimes#<br> 
    Is threadB initialized: #threadB.initialized#<br> 
</cfoutput>