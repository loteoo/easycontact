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
    public $submission = array(); // Asso. array, generally contains form POST data
    public $mailTo; // Person who will reveive the messages

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

      // Add extra data to the submission
      setlocale(LC_TIME, 'fr_CA.UTF-8');
      $this->submission["ip"] = $_SERVER['REMOTE_ADDR'];
      $this->submission["date"] = strftime("%B %e %G, %I:%M %p");

      // Send mail if toggled
      if ($this->sendMail) {
        if (!$this->sendMail($this->submission, $this->mailTo)) {
          return false;
        }
      }

      // Save JSON if toggled
      if ($this->saveJson) {
        if (!$this->saveJson($this->submission)) {
          return false;
        }
      }

      return true;
    }


    // Sends an email to a specified person, containing submitted data.
    public function sendMail($submission, $mailTo) {

      // Destination email required
      if (!$mailTo) {
        return false;
      }

      // Something to send required
      if (!is_array($submission)) {
        return false;
      }


      // Alter / cleanup form data here
      $submittedEmail = strip_tags($submission["email"]);
      $subject = strip_tags($submission["subject"]);
      $message = strip_tags($submission["message"]);


      // Here you can customise the email body.
      $emailContent = "Message from " . $submittedEmail . "\n\n";
      $emailContent .= wordwrap($message, 70);
      $emailContent .= "\n\nSent from " . $_SERVER['HTTP_HOST'];


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
      return mail($mailTo, $subject, $emailContent, $header);
    }


    // Saves submitted data to a json file.
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




// =========================
// Basic usage example here
// =========================

// Required fields
if (array_key_exists('email', $_POST) && array_key_exists('subject', $_POST) && array_key_exists('message', $_POST)) {

    // Instantiate
    $quickContact = new EasyContact($_POST, "alexlotte16@gmail.com");

    // Chose settings, defaults: sendMail = true, saveJson = true
    $quickContact->sendMail = false;

    // Launch toggled functions and check for failure
    // better error management when i'll care.. probably never
    if ($quickContact->go()) {
      echo "Success";
    } else {
      echo "Error";
    }

}


 ?>
