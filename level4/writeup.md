# Writeup level 4
In this challenge we get greeted by a message telling us that our access is disallowed because we are visiting from the wrong page. This sounds a lot like it has something to do with the HTTP Referer Request Header

## What is the HTTP Referer?

>In HTTP, "Referer" is the name of an optional HTTP header field that identifies the address of the web page, from which the resource has been requested. By checking the referrer, the server providing the new web page can see where the request originated. - https://en.wikipedia.org/wiki/HTTP_referer

## Back to the challenge
So since we can modify the request header (even from our browser by editing the GET-Request from the networkanalysis tab.), it should cause no problem to replace our current referer with the one that is needed. In this case:  
*http://natas5.natas.labs.overthewire.org/*

Sending the modified GET-Requests gives us a new page with the password.

![ ](/home/baldy/Desktop/OverTheWire-Natas/OverTheWire-Natas/level4/natas4.png  "Natas 4 Result")

So the password for Natas 5 is:  
iX6IOfmpN7AYOQGPwtn3fXpbaJVJcHfq
