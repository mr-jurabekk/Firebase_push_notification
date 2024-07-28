<?php

require 'vendor/autoload.php';


use Google\Auth\Credentials\ServiceAccountCredentials;

// use GuzzleHttp\Client;

function getAccessToken()
{
  $keyFilePath = __DIR__ . '/YOUR-AUTH-JSON-FILE'; // Use relative path

  if (!file_exists($keyFilePath)) {
    die('Service account key file not found: ' . $keyFilePath);
  }

  $credentials = new ServiceAccountCredentials(
    'https://www.googleapis.com/auth/firebase.messaging',
    $keyFilePath
  );

  $accessTokenArray = $credentials->fetchAuthToken();
  if (isset($accessTokenArray['access_token'])) {
    return $accessTokenArray['access_token'];
  } else {
    die('Failed to retrieve access token.');
  }
}
function sendNotification($title, $message, $token)
{
  $url = "https://fcm.googleapis.com/v1/projects/PROJECT-ID/messages:send";

  $fields = [
    "message" => [
      "token" => $token,
      "notification" => [
        "body" => $message,
        "title" => $title,

      ],
      "data" => [
        "click_action" => "https://google.com",
        "custom_key" => "custom_value"

      ]
    ]
  ];

  $accessToken = getAccessToken();

  $headers = [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable detailed debugging output

  $result = curl_exec($ch);

  if ($result === FALSE) {
    die('Curl failed: ' . curl_error($ch));
  }

  curl_close($ch);
  // echo $result;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = $_POST['title'];
  $message = $_POST['message'];
  $token = $_POST['token'];
  sendNotification($title, $message, $token);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Title<input type="text" name="title">
    Message<input type="text" name="message">
    Token<input type="text" name="token">
    <input type="submit" value="Send notification">
  </form>

</body>

</html>