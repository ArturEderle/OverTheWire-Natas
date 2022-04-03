# Writeup level 24
In this challenge we have to deal with the weirdness of php again.

**Source Code**  

```php
<?php
    if(array_key_exists("passwd",$_REQUEST)){
        if(!strcmp($_REQUEST["passwd"],"<censored>")){
            echo "<br>The credentials for the next level are:<br>";
            echo "<pre>Username: natas25 Password: <censored></pre>";
        }
        else{
            echo "<br>Wrong!<br>";
        }
    }
    // morla / 10111
?> 
```
As we can see the function **strcmp()** is being used to compare two Strings. When both Strings are the same, the function returns **0**. That's why the result is being inversed so the 0 becomes 1, since in php 0 = false and 1 = true.

**Exploit**  
In php not only 0 is treated as false but also NULL. So when we manage to manipulate the strcmp() function to result into NULL the final expression will look like **if(!NULL)** and by being that it's going to result into true. But how can we do that?  
Well strcmp expects two Strings. In this case the value of passwd and the password for the next challenge. So what if we won't give it a String but an Array? Exactly! It's going to break and we can do that by changing the GET-Parameter **?passwd=somepw** into **?passwd[]=**.

The password is:  
GHF6X7YwACaYYssHVY05cFq83hRktl4c
