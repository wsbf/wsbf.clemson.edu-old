<?php
//require_once("wizbif.php");
include("connect.php");
drupal_add_js('misc/rental.js.php');


/** borrowed this function from: **/
/** http://forums.devnetwork.net/viewtopic.php?f=29&t=103478 **/
function mysql_fetch_all ($result, $result_type = MYSQL_BOTH) {
        if (!is_resource($result) || get_resource_type($result) != 'mysql result')
        {
            trigger_error(__FUNCTION__ . '(): supplied argument is not a valid MySQL result resource', E_USER_WARNING);
            return false;
        }
        if (!in_array($result_type, array(MYSQL_ASSOC, MYSQL_BOTH, MYSQL_NUM), true))
        {
            trigger_error(__FUNCTION__ . '(): result type should be MYSQL_NUM, MYSQL_ASSOC, or MYSQL_BOTH', E_USER_WARNING);
            return false;
        }
        $rows = array();
        while ($row = mysql_fetch_array($result, $result_type))
        {
            $rows[] = $row;
        }
        return $rows;
}



$query = "SELECT * FROM `equip_package`";
$result = mysql_query($query) or die("Packages Query failed : " . mysql_error());
$equip_packages = mysql_fetch_all($result);
   
$query = "SELECT * FROM `equipment`";
$result = mysql_query($query) or die("Packages Query failed : " . mysql_error());
$equipment = mysql_fetch_all($result);
?>

<center>
    <form name="equipform" method=POST action="/rentalsubmit">
        <table>
            <tr><td>Name:</td><td><input type="text" name="name"></td></tr>
            <tr><td>E-mail:</td><td><input type="text" name="email"></td></tr>
            <tr><td>Phone:</td><td><input type="text" name="phone"></td></tr>
            <tr><td>Event Name:</td><td><input type="text" name="event"></td></tr>
            <tr><td>Event Location:</td><td><input type="text" name="location"></td></tr>
        </table>
        <br />
        Date of Rental: 
        <select name="month">
            <option>January</option>
            <option>Febuary</option>
            <option>March</option>
            <option>April</option>
            <option>May</option>
            <option>June</option>
            <option>July</option>
            <option>August</option>
            <option>September</option>
            <option>October</option>
            <option>November</option>
            <option>December</option>
        </select>
        <select name="day">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
            <option>7</option>
            <option>8</option>
            <option>9</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
            <option>13</option>
            <option>14</option>
            <option>15</option>
            <option>16</option>
            <option>17</option>
            <option>18</option>
            <option>19</option>
            <option>20</option>
            <option>21</option>
            <option>22</option>
            <option>23</option>
            <option>24</option>
            <option>25</option>
            <option>26</option>
            <option>27</option>
            <option>28</option>
            <option>29</option>
            <option>30</option>
            <option>31</option>
        </select>
        <select name="year">
            <option>2010</option>
            <option>2011</option>
        </select>
        <br /><br />
        Package: 
        <select name=select1 onchange='OnChange(this.form.select1);'>
            <option selected></option>
<?php
    foreach ($equip_packages as $equip_package) {
        echo "            <option value=\"" . $equip_package['name'] . "\">" . $equip_package['fullName'] ."</option>\n";
    }
?>
        </select>
        <br /><br />
        <table>
        <tr>
            <td>Equipment</td>
            <td>Model</td>
            <td>Quantity</td>
        </tr>
<?php
    foreach ($equipment as $equip_item) {
        echo "        <tr>\n";
        echo "            <td><input type=\"checkbox\" name=\"equip\" value=\"" . $equip_item['id'] . "\">" . $equip_item['name'] . "</input><br />\n";
        echo "            <td>" . $equip_item['model'] . "</td>\n";
        echo "            <td>\n";
        echo "                <select name=\"quantity" . $equip_item['id'] . "\" id=\"quantity" . $equip_item['id'] . "\">\n";
        for ($i = 0; $i <= $equip_item['quantity']; $i++) {
            echo "                            <option value=\"" . $i . "\">" . $i . "</option>\n";
        }
        echo "                </select>\n
            </td>\n
        </tr>\n";
    }
?>
        </table>
        <br />
        <textarea name="comments" cols="60" rows="5">Enter extra notes here...</textarea>
        <br /><br />
		<b>Please type exactly 'wsbf' below, to prove you are not a robot.</b>
		<br/><br/>
		<input type='text' name='captcha' id='captcha' \>
		<br/><br/>
		
        <div id="submit"><input class="rentalbutton" type="submit" name="Submit" value="Submit"></div>
        <br /><br />
    </form>
</center>
