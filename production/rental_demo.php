<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>WSBF Rental Page</title>
    <link rel="stylesheet" type="text/css" href="http://wsbf.net/wp-content/themes/rounded-grey-blog-10/style.css" />
    <link rel="stylesheet" type="text/css" href="wsbf.css" />
    <script type="text/javascript" src="http://wsbf.net/jquery.js"></script>
<SCRIPT LANGUAGE="javascript">
var effects = new Array("0", "1");
var microphone = new Array("1");
var staple = new Array("3");
var light = new Array("4");
var full = new Array("1", "2", "3", "4", "0", "5", "6");

Array.prototype.contains = function (element) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == element) {
            return true;
        }
    }
    return false;
};

function checkPackage(field, type) {
    for (i = 0; i < field.length; i++) {
        eval('select =  document.equipform.quantity' + field[i].value.toString() + ';');
        if(window[type].contains(field[i].value)) {
            field[i].checked = true;
            select.value = '3';
        } else {
            field[i].checked = false;
            select.value = '0';
        }
    }
}

function OnChange(dropdown) {
    var myindex  = dropdown.selectedIndex;
    var SelValue = dropdown.options[myindex].value;
    var baseURL  = SelValue;
    checkPackage(document.equipform.equip, SelValue);
}
</SCRIPT>

</head>
<body>
<center>
    <form name="equipform">
        <select name=select1 onchange='OnChange(this.form.select1);'>
            <option value="effects">Digital Effects Package</option>
            <option value="microphone">Microphone Package</option>
            <option value="staple">Stage Staple Package</option>
            <option value="light">Light Package</option>
            <option value="full">Full Stage Package</option>
        </select>
        <br />
        <table>
        <tr>
            <td>Equipment</td>
            <td>Model</td>
            <td>Quantity</td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="0">Pre-amp mic compressors</input><br />
            <td>2bx 286A</td>
            <td>
                <select name="quantity0" id="quantity0">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="1">Digital Delay Processors</input><br />
            <td>Lexicon PCM42</td>
            <td>
                <select name="quantity1">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="2">Signal Processor</input><br />
            <td>Alexis/Microverb 4</td>
            <td>
                <select name="quantity2">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="3">Kick Drum Mic</input><br />
            <td>Shure/Beta 52</td>
            <td>
                <select name="quantity3">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="4">CD Turn Tables</input><br />
            <td>Denan DN-S5000</td>
            <td>
                <select name="quantity4">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="5">DJ Mixer (4 Channel)</input><br />
            <td>Denon/DN-X1500S</td>
            <td>
                <select name="quantity5">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" name="equip" value="6">Phonograph Turn Tables</input><br />
            <td>Technics Quartz/SL1200MK2</td>
            <td>
                <select name="quantity6">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </td>
        </tr>
        </table>
        <br />
        <input class="contentbutton" type="submit" name="Submit" value="Submit">
    </form>
</center>
</body>
</html>
