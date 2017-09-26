<?php
  /**
   *  Quick contact class to complement basic ajax contact forms.
   *  Uses basic php mail() & saves submissions to JSON file.
   *  There is no validation here. This isn't your all in one solution, just a quick skeleton.
   *
   *  @author Alexandre Lotte <alexlotte16@gmail.com>
   *
   */
  class EasyContact {

    public $sendMail = true; // Toggles mail sending function
    public $saveJson = true; // Toggles json saving function
    public $mailTo; // Person who will reveive the messages
    public $submission = array(); // Asso. array, generally contains form POST data

    function __construct($submission, $mailTo) {
      if (is_array($submission)) {
        $this->submission = $submission;
      }
      if (isset($mailTo)) {
        $this->mailTo = $mailTo;
      }
    }

    // Launch toggled functions
    public function go() {

      // Add data to the submission
      setlocale(LC_TIME, 'fr_CA.UTF-8');
      $this->submission["ip"] = $_SERVER['REMOTE_ADDR'];
      $this->submission["date"] = strftime("%B %e %G, %I:%M %p");


      if ($this->sendMail) {
        if (!$this->sendMail($this->submission, $this->mailTo)) {
          return false;
        }
      }

      if ($this->saveJson) {
        if (!$this->saveJson($this->submission)) {
          return false;
        }
      }

      return true;
    }



    public function sendMail($submission, $mailTo) {
      if (!$mailTo) {
        return false;
      }
      if (!is_array($submission)) {
        return false;
      }
      $submittedEmail = strip_tags($submission["email"]);
      $subject = strip_tags($submission["subject"]);

      // Here you can customise the email body.
      $msg = "Message from " . $submittedEmail . "\n\n";
      $msg .= wordwrap(strip_tags($submission["message"]), 70);
      $msg .= "\n\nSent from " . $_SERVER['HTTP_HOST'];


      $encoding = "utf-8";
       // Preferences for Subject field
       $subject_preferences = array(
           "input-charset" => $encoding,
           "output-charset" => $encoding,
           "line-length" => 76,
           "line-break-chars" => "\r\n"
       );
       // Mail header
       $header = "Content-type: text/html; charset=".$encoding." \r\n";
       $header .= "From: $submittedEmail <$submittedEmail> \r\n";
       $header .= "MIME-Version: 1.0 \r\n";
       $header .= "Content-Transfer-Encoding: 8bit \r\n";
       $header .= "Date: ".date("r (T)")." \r\n";
       $header .= iconv_mime_encode("Subject", $submission["subject"], $subject_preferences);


      // Send the email using basic mail() function.
      // You should whitelist this domain in your mailbox so messages dont go in SPAM folder,
      // or use another (proper) method of mailing.
      return mail($mailTo, $subject, $msg, $header);
    }


    public function saveJson($submission) {
      // Some data is obviously required if you want to save it
      if (!is_array($submission)) {
        return false;
      }


      // If this is the first saved submission
      if (!file_exists("submissions.json")) {
        $newFile = fopen("submissions.json", "w");
        fwrite($newFile, "");
        fclose($newFile);
      }


      // Build an array of submissions
      $jsonString = file_get_contents('submissions.json');
      $submissions = json_decode($jsonString, true);

      // Add some data to it
      $submissions[] = $submission;

      // Save it back to the file
      $fp = fopen('submissions.json', 'w');
      fwrite($fp, json_encode($submissions));
      fclose($fp);


      // It worked!
      return true;
    }
  }


// Required fields
if (array_key_exists('email', $_POST) && array_key_exists('subject', $_POST) && array_key_exists('message', $_POST)) {


    $quickContact = new EasyContact($_POST, "alexlotte16@gmail.com");
    $quickContact->sendMail = false;

    if ($quickContact->go()) {
      echo "Success";
    } else {
      echo "Error";
    }


}


 ?>
