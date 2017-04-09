<?php

require_once("connect.php");

$alternator = 0;
$query = "SELECT * from `picks` ORDER BY name DESC";
$result = mysql_query($query) or die("Query failed : " . mysql_error());
while ($picks = mysql_fetch_array($result)) {
   if ($alternator == 0) {
      echo '<tr><td valign="top" width="50%">';
   } else {
      echo '<td valign="top" width="50%">';
   }

   echo '<h2>' . $picks['name'] . '</h2><ol type="1">';

   for ($i = 1; $i <= 5; $i++)
      echo '<li>' . $picks['pick' . $i] . '</li>';

   echo '</ol></td>';

   if ($alternator == 1) {
      echo '</tr>';
   }
   $alternator = 1 - $alternator;
   echo "<br/>";
} 
?>
</table>
