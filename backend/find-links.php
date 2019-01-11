<!DOCTYPE html>
<html>
<head>
  <title>Find-Links</title>
</head>
<body>
  <pre>
  <?php
  session_start();

  if (!($_POST['mysqlpw'] === "RussianentryintoWWIIwasdecisive")) {
    // making sure it's really an admin here
    header('location: savelinks');
    exit();
  }

  // preventing information leakage
  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';
  require '../vendor/autoload.php';

  // Authentication things above
  /*
  * Decode the body.
  * @param : encoded body  - or null
  * @return : the body if found, else FALSE;
  */
  function decodeBody($body) {
    $rawData = $body;
    $sanitizedData = strtr($rawData,'-_', '+/');
    $decodedMessage = base64_decode($sanitizedData);
    if(!$decodedMessage){
      $decodedMessage = FALSE;
    }
    return $decodedMessage;
  }

  function searchForHeader($header, $names, $vals) {
    for ($i = 0; $i < count($names); $i++) {
      if ($names[$i] === $header) {
        return $vals[$i];
      }
    }

    return "";
  }

  $mysqli = public_connect(1);

  // create client
  $client = new Google_Client();
  $client->setApplicationName('Gmail Links Getter');
  $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
  $client->setAuthConfig('/client_secrets.json');
  $client->setAccessType('offline');
  $client->setPrompt('consent');
  $client->setRedirectUri('https://www.warmglue.com/backend/email-verification-queue');

  // get access token from mysql database
  $stmt = $mysqli->prepare("SELECT access_token FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($accessTokenJson);
  $stmt->fetch();
  $stmt->close();

  $client->setAccessToken(json_decode($accessTokenJson, true));

  if ($client->isAccessTokenExpired()) {
    // if previous token is expired
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
  }

  // get list of user's messages
  $service = new Google_Service_Gmail($client);
  $list = $service->users_messages->listUsersMessages($_POST['email']);

  try {
    while ($list->getMessages() != null) {

      foreach ($list->getMessages() as $mlist) {

          $message_id = $mlist->id;
          $optParamsGet2['format'] = 'full';
          $single_message = $service->users_messages->get('me', $message_id, $optParamsGet2);
          $payload = $single_message->getPayload();

          // for printing headers
          $headers = $payload->getHeaders();
          $names = array_column($headers, 'name');
          $vals = array_column($headers, 'value');
          $headers_printable = "";
          $headers_printable .= "Subject: ".searchForHeader("Subject", $names, $vals);
          $headers_printable .= "\nFrom: ".searchForHeader("From", $names, $vals);
          $headers_printable .= "\nTo: ".searchForHeader("To", $names, $vals);
          $headers_printable .= "\nCc: ".searchForHeader("Cc", $names, $vals);
          $received = searchForHeader("Received", $names, $vals);
          $headers_printable .= "\nReceived: ".trim(substr($received, strpos($received, ";") + 1));
          $headers_printable = htmlentities($headers_printable);

          $parts = $payload->getParts();
          // With no attachment, the payload might be directly in the body, encoded.
          $body = $payload->getBody();
          $FOUND_BODY = FALSE;
          // If we didn't find a body, let's look for the parts
          if(!$FOUND_BODY) {
              foreach ($parts  as $part) {
                  if($part['parts'] && !$FOUND_BODY) {
                      foreach ($part['parts'] as $p) {
                          if($p['parts'] && count($p['parts']) > 0){
                              foreach ($p['parts'] as $y) {
                                  if(($y['mimeType'] === 'text/html') && $y['body']) {
                                      $FOUND_BODY = decodeBody($y['body']->data);
                                      break;
                                  }
                              }
                          } else if(($p['mimeType'] === 'text/html') && $p['body']) {
                              $FOUND_BODY = decodeBody($p['body']->data);
                              break;
                          }
                      }
                  }
                  if($FOUND_BODY) {
                      break;
                  }
              }
          }
          // let's save all the images linked to the mail's body:
          if($FOUND_BODY && count($parts) > 1){
              $images_linked = array();
              foreach ($parts  as $part) {
                  if($part['filename']){
                      array_push($images_linked, $part);
                  } else{
                      if($part['parts']) {
                          foreach ($part['parts'] as $p) {
                              if($p['parts'] && count($p['parts']) > 0){
                                  foreach ($p['parts'] as $y) {
                                      if(($y['mimeType'] === 'text/html') && $y['body']) {
                                          array_push($images_linked, $y);
                                      }
                                  }
                              } else if(($p['mimeType'] !== 'text/html') && $p['body']) {
                                  array_push($images_linked, $p);
                              }
                          }
                      }
                  }
              }
              // special case for the wdcid...
              preg_match_all('/wdcid(.*)"/Uims', $FOUND_BODY, $wdmatches);
              if(count($wdmatches)) {
                  $z = 0;
                  foreach($wdmatches[0] as $match) {
                      $z++;
                      if($z > 9){
                          $FOUND_BODY = str_replace($match, 'image0' . $z . '@', $FOUND_BODY);
                      } else {
                          $FOUND_BODY = str_replace($match, 'image00' . $z . '@', $FOUND_BODY);
                      }
                  }
              }
              preg_match_all('/src="cid:(.*)"/Uims', $FOUND_BODY, $matches);
              if(count($matches)) {
                  $search = array();
                  $replace = array();
                  // let's trasnform the CIDs as base64 attachements
                  foreach($matches[1] as $match) {
                      foreach($images_linked as $img_linked) {
                          foreach($img_linked['headers'] as $img_lnk) {
                              if( $img_lnk['name'] === 'Content-ID' || $img_lnk['name'] === 'Content-Id' || $img_lnk['name'] === 'X-Attachment-Id'){
                                  if ($match === str_replace('>', '', str_replace('<', '', $img_lnk->value))
                                          || explode("@", $match)[0] === explode(".", $img_linked->filename)[0]
                                          || explode("@", $match)[0] === $img_linked->filename){
                                      $search = "src=\"cid:$match\"";
                                      $mimetype = $img_linked->mimeType;
                                      $attachment = $service->users_messages_attachments->get('me', $mlist->id, $img_linked['body']->attachmentId);
                                      $data64 = strtr($attachment->getData(), array('-' => '+', '_' => '/'));
                                      $replace = "src=\"data:" . $mimetype . ";base64," . $data64 . "\"";
                                      $FOUND_BODY = str_replace($search, $replace, $FOUND_BODY);
                                  }
                              }
                          }
                      }
                  }
              }
          }
          // If we didn't find the body in the last parts,
          // let's loop for the first parts (text-html only)
          if(!$FOUND_BODY) {
              foreach ($parts  as $part) {
                  if($part['body'] && $part['mimeType'] === 'text/html') {
                      $FOUND_BODY = decodeBody($part['body']->data);
                      break;
                  }
              }
          }
          // With no attachment, the payload might be directly in the body, encoded.
          if(!$FOUND_BODY) {
              $FOUND_BODY = decodeBody($body['data']);
          }
          // Last try: if we didn't find the body in the last parts,
          // let's loop for the first parts (text-plain only)
          if(!$FOUND_BODY) {
              foreach ($parts  as $part) {
                  if($part['body']) {
                      $FOUND_BODY = decodeBody($part['body']->data);
                      break;
                  }
              }
          }
          if(!$FOUND_BODY) {
              $FOUND_BODY = '(No message)';
          }
          // Finally, print the message ID and the body
          print_r($message_id ."\n\n" . $headers_printable . ": \n\n\n" . $FOUND_BODY . "\n\n------------------\n\n");
      }

      if ($list->getNextPageToken() != null) {
          $pageToken = $list->getNextPageToken();
          $list = $service->users_messages->listUsersMessages('me', ['pageToken' => $pageToken]);
      } else {
          break;
      }
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }

 ?>
  </pre>
</body>
</html>
