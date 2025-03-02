<?php
class PHP_Email_Form {
    public $to = '';
    public $from_name = '';
    public $from_email = '';
    public $subject = '';
    public $smtp = array();
    public $messages = array();
    public $ajax = false;

    public function add_message($content, $label = '', $priority = 10) {
        $this->messages[] = array("content" => $content, "label" => $label, "priority" => $priority);
    }

    public function send() {
        if(empty($this->to) || empty($this->from_email) || empty($this->from_name)) {
            return "Error: Required fields are missing.";
        }

        $message_body = "";
        foreach($this->messages as $message) {
            $message_body .= $message['label'] . ": " . $message['content'] . "\n";
        }

        $headers = "From: ".$this->from_name." <".$this->from_email.">\r\n";
        $headers .= "Reply-To: ".$this->from_email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Check if SMTP is set
        if (!empty($this->smtp)) {
            return $this->send_smtp($message_body);
        } else {
            if(mail($this->to, $this->subject, $message_body, $headers)) {
                return "OK";
            } else {
                return "Error: Mail could not be sent.";
            }
        }
    }

    private function send_smtp($message_body) {
        require 'PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->smtp['port'];

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $message_body;

        if(!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'OK';
        }
    }
}
?>
