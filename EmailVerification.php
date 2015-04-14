<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	if(isset($_REQUEST)){
	$MyEmail = new Email();
	if(isset($_GET["email"])){
		if(isset($_GET["name"]))
			$name = $_GET["name"];
		if(isset($_GET["phone"]))
			$phone = $_GET["phone"];
		$email = $_GET["email"];
		$confirmation = $MyEmail->generateRandomCode(5);
		$MyEmail->sendVerificationEmail($email,$confirmation);
		$SQL = new MySQL();
		$SQL->InsertConfirmationTable($email,$confirmation);
	}
	}
	class Email {
		public $headers; 
		public function __construct(){
			$this->headers  = "MIME-Version: 1.0\r\n";
			$this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$this->headers .= 'From: EmailVerification@findavor.com' . "\r\n";
		}
		function sendEmailHtml($my_email, $message,$subject) { 
			mail($my_email, $subject, $message,$this->headers);
		}
		function sendVerificationEmail($email,$confirmation) {
			$message = "Confirmation code is: $confirmation \n the code is valid for 24 hours";
			$this->sendEmailHtml($email,"Email Verification",$message);			
		}
		// generate random verification number according with $digit of digits 
		function generateRandomCode($digit){
			return $random_hash = substr(md5(uniqid(rand(), true)), $digit, $digit); 
		}
	}
	class MySQL{
		public $con;
		function __construct(){
			// Create connection
      $this->con = mysqli_connect("localhost","root","","order_system");
      mysqli_set_charset($this->con, "utf8");
      // Check connection
      if (mysqli_connect_errno())
      {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }
    }
		// Name: insert to table
		// Input: $table the table that will be inserted, $variables, values that will be inserted in format 'value1','value2'
		function MySQLInsert($table,$variables){
			$values = join(",",$variables);
			$query = "insert  into $table values($values)";
      echo $query;
			if($stm = $this->con->prepare($query)){
				if($stm->execute()){
					$stm->close();
					return true;
				}
			}		
		}
		// insert email and generated confirmation code to database
		function InsertConfirmationTable($email,$confirmation){
			$confirmationTable = "confirmationCode";
			if($this->MySQLInsert($confirmationTable,array("'$email'","'$confirmation'","now()"))){
				return true;
			}
			return false;
		}
	}
?>