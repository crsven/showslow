<?php
function do_post_request($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    throw new Exception("Problem with $url");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception("Problem reading data from $url");
  }
  return $response;
}

if (array_key_exists('url', $_POST)) {
  $url = $_POST['url'];
  $har_filename = date('m-d-y_h:m:s').".har";
  $returns = passthru("phantomjs ../automation/phantomjs/netsniff.js $url > ../automation/phantomjs/har/$har_filename");
  $har_data = file_get_contents("../automation/phantomjs/har/$har_filename");
  $response = do_post_request("http://local.showslow.com/beacon/har/?url=$url", $har_data,
    "Content-type: text/html\r\n"
  );
  if ($response) {
    echo $response;
  } else {
    echo "no can do";
  }
} else {
  echo "error: needs URL";
}
?>
