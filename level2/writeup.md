# Writeup Level-2
Checking out the source file leads to a file called pixel.png which is in a folder called files.

	<img src="files/pixel.png">


Using the directory traversal attack by simply modifying the URL
from:  
*http://natas2.natas.labs.overthewire.org*  
to:  
*http://natas2.natas.labs.overthewire.org/files/*

In this folder we can see the pixel.png and a text file called **users.txt**.  
We can view the file in our browser by navigating to it.  
Content of **users.txt**:  
```
# username:password
alice:BYNdCesZqW
bob:jw2ueICLvT
charlie:G5vCxkVV3m
natas3:sJIJNW6ucpu6HPZ1ZAchaDtwd7oGrD14
eve:zo4mJWyNj2
mallory:9urtcpzBmH
```


