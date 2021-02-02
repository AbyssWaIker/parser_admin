<?php
// несколько получателей
require_once ('./vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class sender
{
  private $sender = 'test.to.notify.about.parser.save@gmail.com'; 
  private $sender_password = '4321artt';
  private $sender_name = "Авто-Уведомитель";
  private $reciever;
  private $reciever_name;
  private $subject;
  private $message;
  private $headers;
  private $attachment;

  function format_message( $subject,$message)
  { 
    // текст письма
    $message = '
                <html>
                <head>
                  <title>'.$subject.'</title>
                </head>
                <body>
                  '.$message.'
                </body>
                </html>
                ';
    return $message;
  }
  function __construct($reciever, $subject, $message, $attachment = null, $reciever_name = 'Админ')
  {
    $this->reciever = $reciever;
    $this->subject = $subject;
    $this->message = $this->format_message($subject, $message);
    $this->reciever_name = $reciever_name;
  }

  function send()
  {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->SMTPSecure ='ssl';
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $this->sender;                     // SMTP username
        $mail->Password   = $this->sender_password;                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


        $mail->setFrom("notifier@gmail.com", $this->sender_name);
        $mail->addAddress($this->reciever, $this->reciever_name);   

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->CharSet = "utf-8";
        $mail->Subject = $this->subject;
        $mail->Body    =  $this->message;


        // if($this->attachment !== null)
        //     $mail->addAttachment($this->attachment);

        $mail->send();
        echo 'Сообщение отправлено';
    } catch (Exception $e) {
        echo "Не удалось отправить сообщение: {$mail->ErrorInfo}";
    }
  }


}
?>
