# Writeup level 23
In this challenge we have to simply provide a password that fulfills the condition. 

```php
<?php
    if(array_key_exists("passwd",$_REQUEST)){
        if(strstr($_REQUEST["passwd"],"iloveyou") && ($_REQUEST["passwd"] > 10 )){
            echo "<br>The credentials for the next level are:<br>";
            echo "<pre>Username: natas24 Password: <censored></pre>";
        }
        else{
            echo "<br>Wrong!<br>";
        }
    }
    // morla / 10111
?>  
```
**Exploit**  
strstr(string haystack, string needle):  
> Returns part of haystack string starting from and including the first occurrence of needle to the end of haystack. - https://www.php.net/manual/en/function.strstr.php

Okay so as long as we provide the String "iloveyou" the first part of the condition will result into true. The second part of the condition is also quite easy to fulfill. In php you can compare a String with a int meaning when we have something like "11" > 10 it will result in true.

Our input for passwd:  
11iloveyou

The password is:  
OsRmXFguozKpTZZ5X14zNO43379LZveg
