<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Quick Contact skeleton code</title>
  </head>
  <body>

    <?php
      // Required fields (basic validation)
      if (array_key_exists('email', $_POST) && array_key_exists('subject', $_POST) && array_key_exists('message', $_POST)) {
        if ($_POST["email"] != "" && $_POST["subject"] != "" && $_POST["message"] != "") {


            // Instantiate
            $quickContact = new EasyContact($_POST, "youremailhere@domain.com");

            // Launch toggled functions and check for failure
            // better error management when i'll care.. probably never
            if ($quickContact->go()) {
              echo "<h1>Form submitted successfully!</h1>";
            } else {
              echo "<h1>Error submitting form</h1>";
            }


        }
      }
     ?>

    <form id="basic" action="example.php" method="post">
      <input type="text" name="subject" id="subject" placeholder="Subject" required>
      <input type="email" name="email" id="email" placeholder="Your email" required>
      <textarea name="message" id="message" rows="3" cols="80" placeholder="Message" required></textarea>
      <button type="submit">Send</button>
    </form>

  </body>
</html>
