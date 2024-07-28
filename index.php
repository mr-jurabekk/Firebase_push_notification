<?php

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  $data = json_decode(file_get_contents('php://input'), true);

  if (isset($data['token'])) {
    $token = $data['token'];

    $db_name = "test";
    $dbcnx = @mysqli_connect("localhost", "root", "") or die(json_encode(['success' => false, 'error' => "No connection with server MySQL"]));
    @mysqli_select_db($dbcnx, $db_name) or die(json_encode(['success' => false, 'error' => "This database not found"]));


    $check = mysqli_query($dbcnx, "SELECT * FROM user_token");
    if (!$check) {
      die(json_encode(['success' => false, 'error' => "SQL query error: " . mysqli_error($dbcnx)]));
    }

    $datas_d = [];
    while ($row = mysqli_fetch_assoc($check)) {
      $datas_d[$row['id']] = $row['token'];
      // $datas_d = json_encode($datas_d);
    }

    $n = 0;
    foreach ($datas_d as $key => $value) {
      if ($value == $token) {
        $n++;
      }
    }
    if ($n == 0) {
      $query = "INSERT INTO user_token (token) VALUES ('$token')";
      $result = mysqli_query($dbcnx, $query);
      // echo 'done';
    } else {
      echo "Datas have already recorded";
    }


    if ($result) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'error' => mysqli_error($dbcnx)]);
    }
    mysqli_close($dbcnx);
  } else {
    echo json_encode(['success' => false, 'error' => 'Token not provided']);
  }

  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notification</title>
</head>

<body>

  <section>
    <div class="container">
      <h2>Xabarnomaga ruxsat bering!</h2>
      <div class="gif">
        <img src="https://media1.tenor.com/m/BMkCv-UCFIgAAAAC/bear-byebear.gif" alt="">
      </div>
    </div>
  </section>

  <style>
    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 80vh;
    }

    img {
      width: 300px;
    }
  </style>

  <script type="module">
    import {
      initializeApp
    } from "https://www.gstatic.com/firebasejs/10.12.4/firebase-app.js";
    import {
      getMessaging,
      getToken
    } from "https://www.gstatic.com/firebasejs/10.12.4/firebase-messaging.js";

    const firebaseConfig = {
      apiKey: "YOUR-DATA",
      authDomain: "YOUR-DATA",
      projectId: "YOUR-DATA",
      storageBucket: "YOUR-DATA",
      messagingSenderId: "YOUR-DATA",
      appId: "YOUR-DATA",
      measurementId: "G-YOUR-DATA"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    navigator.serviceWorker.register('sw.js').then(registration => {

      getToken(messaging, {
        serviceWorkerRegistration: registration,
        vapidKey: 'YOUR-VAPIDKEY'
      }).then((currentToken) => {
        if (currentToken) {

          console.log('Token is: ' + currentToken);

          fetch('', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              token: currentToken
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // console.log('Token inserted into database successfully.');
              } else {
                // console.error('Error inserting token into database:', data.error);
              }
            })
            .catch(error => {
              // console.error('Error with fetch request:');
            });

        } else {
          console.log('No registration token available. Request permission to generate one.');
        }
      }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
      });
    })
  </script>

</body>

</html>