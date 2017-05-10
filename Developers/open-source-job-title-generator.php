<?php 

    // TODO - FILL IN THE NAME OF THIS FILE HERE:
    $sRelativeFilePathAndName = '/open-source-job-title-generator.php';
    
    // TODO - FILL IN THE DATABASE SERVER NAME, THE DATABASE NAME, THE DATABASE USER AND PASSWORD
    // The user should have Execute privileges on the database
    $dbhost  = 'Database Server Name';
    $dbname  = 'thesaurus_of_job_titles';
    $dbuser  = 'Database User';
    $dbpass  = 'Database Password';
    
    $sStoredProcedureToCall = 'spEvaluateGivenJobTitle';
    
    // TROUBLESHOOTING
    //    
    // If the response says, "There was a technical error processing the request" 
    //    1. Make sure the user has Execute privileges on the database.
    //    2. Connect to server using your database user and run the call to the stored procedure with the given job title.
    

    
    // initialize variables
    $sFormattedSearchString = '';
    $bErrorFree = true;


    // check the given search string
    if (isset($_GET['q'])) {
        // this is the web server check to remove punctuation
        // the thesaurus should not use punctuation as a safety precaution against sql injection attacks.
        $sFormattedSearchString = preg_replace('/[^A-Za-z0-9 ]/', '', html_entity_decode($_GET['q']));
        if (strlen($sFormattedSearchString) > 100) {
            $sFormattedSearchString = substr($sFormattedSearchString,0,100);
        }
    }

    // check if anything was submitted to be checked
    if ( $sFormattedSearchString != '') {
        
        // initialize variables
        $dbsCleanedJobTitle = $dbiCleanedJobTitleWordCount = $dbsExperienceLevel = $dbsShift = $dbsFormattedJobTitle = $dbsAssignedRole = $dbiAssignedRoleCount = $dbiAssignedRoleWordCount = $dbsRoleKeyword = $dbiRoleKeywordCount = $dbiRoleKeywordWordCount = $dbsRoleModifier = $dbiRoleModifierCount = $dbiRoleModifierWordCount = $dbsPhraseChecksFound = $dbsResultCode = $dbsSuggestedJobTitle = $dbsOutputText =  $dbsSuggestedKeywords =  '';

        
        // fix the time zone - see http://php.net/manual/en/timezones.php
        date_default_timezone_set ('America/New_York');

        // open the connection to the mysql database
        $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        if ($connection->connect_error) {
            $bErrorFree = false;
        }
        
        // if it connected to the database correctly
        if ($bErrorFree == true) {
            //prepare the statement and bind the parameters
            $bBoundResult = false;
            
            // update query as necessary
            $stmt = $connection->prepare("Call $dbname.$sStoredProcedureToCall( ?, true );");
            
            if (!$stmt) {
                $sErrorString = "Preparing Statement failed: (" . $stmt->errno . ") " . $stmt->error;
                $bErrorFree = false;
            }
            else {
                // update parameters to pass into query
                if (!$stmt->bind_param("s", $sFormattedSearchString)) {
                    $sErrorString = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                    $bErrorFree = false;
                }
                else {
                    if (!$stmt->execute()) {
                        $sErrorString = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                        $bErrorFree = false;
                    }
                    else {
                        // bind the results to the php variables
                        if (!$stmt->bind_result($dbsCleanedJobTitle,$dbiCleanedJobTitleWordCount,$dbsExperienceLevel,$dbsShift,$dbsFormattedJobTitle,$dbsAssignedRole,$dbiAssignedRoleCount,$dbiAssignedRoleWordCount,$dbsRoleKeyword,$dbiRoleKeywordCount,$dbiRoleKeywordWordCount,$dbsRoleModifier,$dbiRoleModifierCount,$dbiRoleModifierWordCount,$dbsPhraseChecksFound,$dbsResultCode,$dbsSuggestedJobTitle,$dbsOutputText,$dbsSuggestedKeywords)) {
                            $sErrorString = "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                            $bErrorFree = false;
                        }
                        else {
                            $bBoundResult = true;
                        }
                    }
                }
            }
            
                            
            // if it successfully bound the result of the prepared statement
            $bGotResults = false;
            if ($bBoundResult == true) {
                
                while ($stmt->fetch()) {
                    
                    $bGotResults = true;
                            
                } // end of while loop

                $stmt->close();
            }

            // EXPORT THE HTML
            echo "<!DOCTYPE HTML>\n";
            echo "<HTML>\n";
            echo "<HEAD>\n";
            echo "<title>Generic Job Title Generator</title>\n";
            echo "<meta name=\"robots\" content=\"NOINDEX, NOFOLLOW\">\n";
            echo "<meta name=\"description\" content=\"Recruiters can use this open source Job Title Generator to see if the job title from their job posting can be improved.\">\n";
            echo "<meta name=\"keywords\" content=\"open source job title generator\">\n";
            echo "<meta name=viewport content=\"width=device-width, initial-scale=1\">\n";
            echo "</HEAD>\n";
            echo "<BODY>\n";
            

            
            echo "\n";
            echo "  <H1>Job Title Generator</H1>\n";
            echo "\n";
            echo "    <H2>Results</H2>\n";

            If ($bGotResults == true) {
                // $dbsCleanedJobTitle,$dbiCleanedJobTitleWordCount,$dbsExperienceLevel,$dbsShift,$dbsFormattedJobTitle,$dbsAssignedRole,$dbiAssignedRoleCount,$dbiAssignedRoleWordCount,$dbsRoleKeyword,$dbiRoleKeywordCount,$dbiRoleKeywordWordCount,$dbsRoleModifier,$dbiRoleModifierCount,$dbiRoleModifierWordCount,$dbsPhraseChecksFound,$dbsResultCode
                echo "    Tagged Job Title:<br><br>\n    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " . trim($dbsExperienceLevel . ' ' . str_replace(">", "&gt;", str_replace("<", "&lt;", $dbsFormattedJobTitle)) ) . "<br><br>\n";
                
                If ($dbsSuggestedJobTitle != '') {
                    echo "    Suggested Job Title:<br><br>\n    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " . $dbsSuggestedJobTitle . "<br><br>\n";
                }
                
                If ($dbsSuggestedKeywords != '') {
                    echo "    Suggested Keywords:<br><br>\n    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " . $dbsSuggestedKeywords . "<br><br>\n";
                }
                
                If ($dbsOutputText != '') {
                    echo "    Notes:<br>\n" . $dbsOutputText . "<br><br>\n";
                }
                
                switch ($dbsResultCode) {
                    case '100x':
                        #  Has 1 Assigned Role without a Role Keyword, Role Modifier, or any unrecognized text.
                        echo "    Result Code: 100x - Congratulations! The Job Title is listed as an Assigned Role in the Dictionary.<br>\n";
                        echo "    Either (1) the dictionary is accurate and you should use the most-used synonym,\n";
                        echo "    or (2) the dictionary is wrong (too generic, etc.) and you should keep what you are using.\n";
                        echo "    <br>\n";
                        break;
                    case '101':
                        #Has Assigned Role with unrecognized text, No Role Modifier or Role Keyword
                        echo "    Result Code: 101 - The Job Title contains an Assigned Role, but it also has some text we do not recognize.<br>\n";
                        echo "    Consider using the most-used synonym in place of the recognized word or phrase.\n";
                        echo "    For the text that is not recognized, try a different form of the word.\n";
                        echo "    For example, if the text is in a plural form, switch it to a singular form.\n";
                        echo "    If it is an abbreviation, expand it to its original form.\n";
                        echo "    <br>\n";
                        break;
                    case '102':
                        # Has Assigned Role (3+ words) + Role Modifier (1+) without a Role Keyword
                        echo "    Result Code: 102 - The Job Title contains an Assigned Role and 1 or more Role Modifiers.<br>\n";
                        echo "    Consider using the most-used synonym in place of the phrase that is recognized as a specific role.\n";
                        echo "    If the modifier should be a part the title, try searching for the modifier in the thesaurus.\n";
                        echo "    <br>\n";
                        break;
                    case '200':
                        # Assigned Role Match (1+ words) and Role Modifier (1+) without a Role Keyword
                        echo "    Result Code: 200 - The Job Title contains an Assigned Role and 1 or more Role Modifiers.<br>\n";
                        echo "    This is a complicated job title, because it has a recognized Assigned Role, but it also contains a Role Modifier.\n";
                        echo "    It is hard to tell if the Assigned Role is accurate.\n";
                        echo "    If the modifier should be a part the title, try searching for the modifier in the thesaurus.\n";
                        echo "    <br>\n";
                        break;
                    case '300x':
                        # Role Keyword and Role Modifier (1 or more) without assigned role
                        echo "    Result Code: 300x - System recognized a Role Keyword and a Role Modifier, but not an Assigned Role.<br>\n";
                        echo "    Try searching for the job title in the thesaurus. Consider changing the order of the words or \n";
                        echo "    changing the form of the words, such as switching plural form to singular form or expanding abbreviations.\n";
                        echo "    <br>\n";
                        break;
                    case '301':
                        # Role Keyword and Role Modifier (1 or more) with unrecognized text without assigned role
                        echo "    Result Code: 301 - System recognized a Role Keyword and a Role Modifier, but not an Assigned Role<br>\n";
                        echo "    Try searching for the job title in the thesaurus. Consider changing the order of the words or \n";
                        echo "    changing the form of the words, such as switching plural form to singular form or expanding abbreviations.\n";
                        echo "    <br>\n";
                        break;
                    case '302x':
                        # Multiple Role Keywords, without Assigned Role, Role Modifier or unrecognized text
                        echo "    Result Code: 302x - System recognized multiple Role Keywords.<br>\n";
                        echo "    The Role Keywords by themselves are not specific enough to note an Assigned Role.\n";
                        echo "    Include a Role Modifier in the job title. Try searching for the job title in the thesaurus for ideas. \n";
                        echo "    <br>\n";
                        break;
                    case '303':
                        # Multiple Role Keywords with unrecognized text, without Assigned Role or Role Modifier
                        echo "    Result Code: 303 - System recognized multiple Role Keywords.<br>\n";
                        echo "    The Role Keywords by themselves are not specific enough to note an Assigned Role.\n";
                        echo "    Include a Role Modifier in the job title. Try searching for the job title in the thesaurus for ideas. \n";
                        echo "    <br>\n";
                        break;
                    case '304': # MORE LIKE RESULT CODE 301
                        # Multiple Role Keywords with Role Modifier (1+), without Assigned Role
                        echo "    Result Code: 304 - System recognized multiple Role Keywords and 1+ Role Modifiers, but not an Assigned Role.<br>\n";
                        echo "    Try searching for the job title in the thesaurus. Consider changing the order of the words or \n";
                        echo "    changing the form of the words, such as switching plural form to singular form or expanding abbreviations.\n";
                        echo "    <br>\n";
                        break;
                    case '400':
                        # Multiple Assigned Roles
                        echo "    Result Code: 400 - Multiple Assigned Roles<br>\n";
                        echo "    The system recognized multiple Assigned Roles.  If you do not want the job title to be tagged with both assigned roles,\n";
                        echo "    remove or change the text of the incorrect Assigned Role.\n";
                        echo "    <br>\n";
                        break;
                    case '500':
                        # Assigned Role and a Role Keyword, no role modifier
                        echo "    Result Code: 500 - System recognized an Assigned Role and a Role Keyword.<br>\n";
                        echo "    If the Assigned Role is incorrect, consider changing the order of the words or removing the words that are \n";
                        echo "    causing the confusion.\n";
                        echo "    <br>\n";
                        break;
                    case '501':
                        # Assigned Role and a Role Keyword and role modifier (1+)
                        echo "    Result Code: 501 - System recognized an Assigned Role, a Role Keyword, and 1+ Role Modifiers.<br>\n";
                        echo "    If the Assigned Role is incorrect, try searching for the job title in the thesaurus.\n";
                        echo "    <br>\n";
                        break;
                    case '502':
                        # An Assigned Role with multiple Role Keywords
                        echo "    Result Code: 502 - An Assigned Role with multiple Role Keywords<br>\n";
                        echo "    If the Assigned Role is incorrect, try searching for the job title in the thesaurus.\n";
                        echo "    <br>\n";
                        break;
                    case '800x':
                        # Role Keyword without an assigned role or role modifier
                        echo "    Result Code: 800x - Job Title needs a Role Modifier.<br>\n";
                        echo "    The recognized phrase is not specific enough to match it to a detailed occupation.\n";
                        echo "    Please add a word or phrase to make your job title more specific.\n";
                        echo "    <br>\n";
                        break;
                    case '801':
                        #echo "Result Code: 801 - Just a Role Keyword with some unrecognized text, No assigned role or role modifier<br>\n";
                        echo "    Result Code: 801 - Job Title needs a Role Modifier.<br>\n";
                        echo "    The recognized phrase is not specific enough to match it to a detailed occupation.\n";
                        echo "    Please add a word or phrase to make your job title more specific.\n";
                        echo "    <br>\n";
                        break;
                    case '900':
                        echo "    Result Code: 900 - Job Title is missing a Role Keyword.<br>\n";
                        echo "    Every job title should have at least one word or phrase that denotes the position, such as worker, specialist, or manager.\n";
                        echo "    We did not recognize any of the words in the job title as a role keyword.\n";
                        echo "    Either (1) we need to add a role keyword to our system or (2) you need to change the job title to include a role keyword.\n";
                        echo "    <br>\n";
                        break;
                }

                
                
            } else {
                echo "    There was a technical error processing the request.<br><br>\n";
            }

            
            echo "\n";
            echo "    <H2>My Terminology and Tagging Syntax</H2>\n";
            echo "    <ul>\n";
            echo "    <li>[Assigned Role]</li>\n";
            echo "    <li>{Role Keyword}</li>\n";
            echo "    <li>&lt;Role Modifier&gt;</li>\n";
            echo "    <li>#Ambiguous/Noise#</li>\n";
            echo "    </ul>\n";
            echo "    [Assigned Role] - refers to a detailed occupation.  The detailed occupation will have a preferred term and may have multiple synonyms for the detailed occupation.\n";
            echo "    I use square brackets to tag Assigned Roles.\n";
            echo "    <br><br>\n";
            echo "    {Role Keyword} - refers to a word or phrase that notes the position.  It is not specific enough by itself to denote an Assigned Role.\n";
            echo "    Examples include: Worker, Specialist, Representative, Manager.\n";
            echo "    I use curly brackets to tag Role Keywords.\n";
            echo "    <br><br>\n";
            echo "    &lt;Role Modifier&gt; - refers to a word or phrase that is added to a Role Keyword to create an Assigned Role. Examples include: Human Resources, Financial, Marketing.\n";
            echo "    I use angle brackets to tag Role Keywords.\n";
            echo "    <br><br>\n";
            echo "    #Ambiguous/Noise# - refers to a word or phrase that has multiple meanings or is considered to be noise in the job title.\n";
            echo "    I use a hash tag to mark Ambiguous/Noise.\n";
            echo "    <br><br>\n";
            
            echo "\n";
            echo "    <H2>Technical Limitations</H2>\n";
            echo "    The job title is limited to 100 characters and the system only checks the first six words.\n";

            echo "\n";
            echo "    <H2>Check Your Job Title</H2>\n";
            echo "    <br>\n";
            echo "    <form method=\"get\" action=\"$sRelativeFilePathAndName\">\n";
            echo "    <input type=\"text\" name=\"q\" placeholder=\"Enter Job Title\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
            echo "    <input type=\"submit\" value=\"Submit\"></form>\n";
            echo "    <br><br>\n";

            
            echo "</BODY>\n";
            echo "</HTML>\n";
            
        } else {
            // there was an error connecting to the database
            echo "There was an error connecting to the database.<br>\n";
            die($connection->connect_error);
            
        }
        
        
    } else {
        // nothing was submitted, so return the default page
        echo "<!DOCTYPE HTML>\n";
        echo "<HTML>\n";
        echo "<HEAD>\n";
        echo "<title>Generic Job Title Generator</title>\n";
        echo "<meta name=\"description\" content=\"Recruiters can use this open source Job Title Generator to see if the job title from their job posting can be improved.\">\n";
        echo "<meta name=\"keywords\" content=\"open source job title generator\">\n";
        echo "<meta name=viewport content=\"width=device-width, initial-scale=1\">\n";
        echo "</HEAD>\n";
        echo "<BODY>\n";
        echo "  <H1>Job Title Generator</H1>\n";
        echo "  Recruiters can run a quick check on a job title on a job posting to see \n";
        echo "  if the job title can be improved.\n";
        echo "  <br><br>\n";
        echo "  \n";
        echo "  <H2>Check Your Job Title</H2>\n";
        echo "  <br>\n";
        echo "  <form method=\"get\" action=\"$sRelativeFilePathAndName\">\n";
        echo "  <input type=\"text\" name=\"q\" placeholder=\"Enter Job Title\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        echo "  <input type=\"submit\" value=\"Submit\"></form>\n";
        echo "  <br><br><br><br>\n";
        echo "  \n";
        echo "  <H2>Technical Limitations</H2>\n";
        echo "  The job title is limited to 100 characters and the system only checks the first six words.\n";
        echo "</BODY>\n";
        echo "</HTML>\n";

    }

?>
