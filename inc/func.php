<?php
function redirect($sURL)
{
    header("Location: " . $sURL); /* Redirect browser */
    exit;
    // TODO: be sure buffering is on above
} // end redirect


function getCurrentSchoolSeason()
{
    // determines current season, schoolyear-wise
    switch(date("n"))
    {
    case 1:
    case 2:
    case 3:
    case 4:
        $s = "Spring ";
        break;
    case 5:
    case 6:
    case 7:
    case 8:
        $s = "Summer ";
        break;
    case 9:
    case 10:
    case 11:
    case 12:
        $s = "Fall ";
        break;
    default:
        //this should never happen but if so it will just show the year
        $s = "";
        break;
    } // end switch
    
    $s .= date("Y");
    
    return($s);
} // end getCurrentSchoolSeason

function getDJName($showID)
{
    global $dbConn;
    
//    $dbSQL = "SELECT * FROM (Schedule SCH INNER JOIN Staff S ON SCH.id = S.schID) WHERE Hour = " . $h . " AND Day = " . $d . ";";
    $dbSQL = "SELECT * FROM (Schedule SCH INNER JOIN Staff S ON SCH.id = S.schID) WHERE SCH.id = " . $showID . ";";
    $dbRS = mysql_query($dbSQL, $dbConn) or die(mysql_error());
    
    $numRows = mysql_num_rows($dbRS);
//    echo("($h,$d): $numRows<br>");
    switch($numRows)
    {
        case 0:
            $sDJName = "";
            break;
        case 1:
            $result = mysql_fetch_assoc($dbRS);
            
            if(!is_null($result['DJName']))
            {
               $sDJName = trim($result['DJName']);
            }
            else
            {
               $sDJName = trim($result['FirstName'] . " " . $result['LastName']);
            } // end if(!is_null($result['DJName']))
            
            break;
        case 2:
            $result = mysql_fetch_assoc($dbRS);
            if(!is_null($result['DJName']))
            {
               $sDJName = trim($result['DJName']) . " &amp; ";
            }
            else
            {
               $sDJName = trim($result['FirstName']) . " &amp; ";
            } // end if(!is_null($result['DJName']))
            
            $result = mysql_fetch_assoc($dbRS);
            if(!is_null($result['DJName']))
            {
               $sDJName .= trim($result['DJName']);
            }
            else
            {
               $sDJName .= trim($result['FirstName']);
            } // end if(!is_null($result['DJName']))
            break;
        default:
            // 3 or more
            for($i = 0; $i < $numRows - 2; $i++)
            {
               $result = mysql_fetch_assoc($dbRS);
               if(!is_null($result['DJName']))
               {
                  $sDJName .= trim($result['DJName']) . ", ";
               }
               else
               {
                  $sDJName .= trim($result['FirstName']) . ", ";
               } // end if(!is_null($result['DJName']))
            } // end for
            
            $result = mysql_fetch_assoc($dbRS);
            if(!is_null($result['DJName']))
            {
               $sDJName .= trim($result['DJName']) . " &amp; ";
            }
            else
            {
               $sDJName .= trim($result['FirstName']) . " &amp; ";
            } // end if(!is_null($result['DJName']))
            
            $result = mysql_fetch_assoc($dbRS);
            if(!is_null($result['DJName']))
            {
               $sDJName .= trim($result['DJName']);
            }
            else
            {
               $sDJName .= trim($result['FirstName']);
            } // end if(!is_null($result['DJName']))
            break;
    } // end switch
    
    return($sDJName);
} // end getDJName

function nextFullStaffMeeting()
{
   return "<b>August 24th</b> at <b>7 PM</b> in one of the Hendrix Center 2nd floor Meeting Rooms";
} // end nextFullStaffMeeting
?>
