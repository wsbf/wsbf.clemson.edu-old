<?php

echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import a Record</h3>\n";
echo "<p>This page imports <b>one record at a time.</b> Or go <a href='import_main.php'>back</a>...</p>\n";
echo "<div id='contents'>";

echo "<form method='POST' action='record_submit.php'>";
echo "<table><tr><th></th><th></th>";
echo "<tr><td>Artist</td><td><input type='text' name='artist' value=\"\" /></td></tr>";
echo "<tr><td>Album</td><td><input type='text' name='album' value=\"\" /></td></tr>";
echo "<tr><td>Label</td><td><input type='text' name='label' value=\"\" /></td></tr>";

echo "<tr><td>Genre</td><td><input type='text' name='genre' value=\"\" /></td></tr>";
echo "<tr><td>Year</td><td><input type='text' name='year' value=\"\" /></td></tr>";
echo "</table>";

echo "<table>\n";
echo "<tr><th>Track #</th><th>Song Title</th></tr>";
?>
<script language="javascript">
        function addRow(tableID) {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            var track = row.insertCell(0);
            var trackNo = document.createElement("input");
            trackNo.name = (rowCount + 1) + '_trnum';
			trackNo.type = 'text';
			trackNo.size ='4';
			trackNo.value = rowCount + 1;
            track.appendChild(trackNo);
            var name = row.insertCell(1);
            var trackName = document.createElement("input");
            trackName.type = 'text';
			trackName.size = '75';
			trackName.name = (rowCount + 1) + '_trname';
            name.appendChild(trackName);
 
        }
 
        function deleteRow(tableID) {
            try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            table.deleteRow(rowCount-1);
            
            }
			catch(e) {
                alert(e);
            }
        }
    </script>
	
	<input type="button" value="Add Row" onclick="addRow('songData')" />
 
    <input type="button" value="Delete Row" onclick="deleteRow('songData')" />
 
    <table id='songData'>
        <tr>
            <td> <input type='text' size='4' value="1" name="1_trnum" /> </td>
            <td> <input type='text' size='75' name="1_trname"/> </td>
        </tr>
    </table>
<?php
echo "<p><input type='submit' name='submit' value='Submit' /></form>";
?>