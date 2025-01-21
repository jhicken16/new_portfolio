<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
require 'config.php';

header("Content-Type: application/json");
// Get the raw POST data 
$input = file_get_contents("php://input"); 
 
// Decode the JSON data 
$data = json_decode($input, true);

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
error_reporting(0);
if($_SERVER["REQUEST_METHOD"] === "POST"){

    if(!isset($data["name"]) || !is_string($data["name"])){
        $error["status"]["code"] = 400;
        $error["status"]["name"] = "Bad Request";
        $error["message"] = "Must Provide name." . $data["name"];
        echo json_encode($error);
        exit();
    }
    if(!isset($data["email"]) || !is_string($data["email"])){
        $error["status"]["code"] = 400;
        $error["status"]["name"] = "Bad Request";
        $error["message"] = "Must Provide email.";
        echo json_encode($error);
        exit();
    }
    if(!isset($data["subject"]) || !is_string($data["subject"])){
        $error["status"]["code"] = 400;
        $error["status"]["name"] = "Bad Request";
        $error["message"] = "Must Provide subject.";
        echo json_encode($error);
        exit();
    }
    if(!isset($data["message"]) || !is_string($data["message"])){
        $error["status"]["code"] = 400;
        $error["status"]["name"] = "Bad Request";
        $error["message"] = "Must Provide message.";
        echo json_encode($error);
        exit();
    }

    $name = $data["name"];
    $email = $data["email"];
    $subject = $data["subject"];
    $message = $data["message"];

    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = MAILHOST;                               //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = USERNAME;                               //SMTP username
        $mail->Password   = PASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(ADDRESS_FROM, $name);
        $mail->addAddress(ADDRESS_TO, 'ME');     //Add a recipient
        $mail->addReplyTo($email, 'Information');


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message . "<br>" . $email . "<br>" . $name ;
        $mail->AltBody = $message . " " . $email . " " . $name;

        $mail->send();
        $output['status'] = "success";
        $output['message'] = "'Message has been sent'";	
        echo json_encode( $output );
    } catch (Exception $e) {

        $output['status'] = "error";
        $output['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";	

        echo json_encode($output); 
    }
} else {
    $error["status"]["code"] = 400;
    $error["status"]["name"] = "Bad Request";
    $error["message"] = "Incorrect Request Method";

    echo json_encode($error);
}


?>