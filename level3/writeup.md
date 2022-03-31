# Writeup level 3

By checking out the source file again, we get a clue:  

	<!-- No more information leaks!! Not even Google will find it this time... -->

"Not even Google will find it this time..." this signals that we should check out a file named **robots.txt**.  

## What is a robots.txt file?
The robots.txt file is used to tell search engine crawlers which URLs they have access to. This means that the content of robots.txt are some rules for the search engine crawlers. There might be a rule that says which locations the crawler should ignore ;).

## Back to the challenge
We can view the robots.txt file from our browser by navigating to it.  
 *http://natas3.natas.labs.overthewire.org/**robots.txt***  
 Content of robots.txt:  
```
User-agent: *
Disallow: /s3cr3t/
```
Nice! We can see that there is a folder named **/s3cr3t/** on the server and we can even navigate to it. In that folder we got a file named **users.txt** and like in the last challenge we have the user for the next challenge in there.

	natas4:Z9tkRkWmpt9Qr7XrR5jWRkgOU901swEZ

