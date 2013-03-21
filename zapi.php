<?php

// configure your ID here
$zws_id = "";

function invoke_rest_api ($endpoint, $method = 'GET', $data = null, $username = '', $password = '') {
    $url = $endpoint;
    $ch  = curl_init();
    if ($data) {
        $postdata = array();
        foreach ($data as $key=>$value)
            array_push($postdata, $key . '=' . urlencode($value));

        if ($method = 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $postdata));
        } else {
            $url .= '?' . implode('&', $postdata);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    } else {
        curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($username && $password) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$url = 'http://www.zillow.com/webservice/GetSearchResults.htm?';
$data['zws-id'] = $zwd_id;
$data['address'] = $_REQUEST['address'];
$data['citystatezip'] = $_REQUEST['address'];

$response = invoke_rest_api($url, 'GET', $data);

$xml = simplexml_load_string($response);

if (0 == $xml->message->code) {

    $res = $xml->response->results->result;
    //$addy = $res->address->street . "\n" . $res->address->city . ', ' . 
    //  $res->address->state . ' ' . $res->address->zipcode . "\n\n" .
    //  'Zestimate: $' . $res->zestimate->amount;
    //echo $addy;
    $json = <<<EOF
{
    address:'{$res->address->street}\\n{$res->address->city}, {$res->address->state} {$res->address->zipcode}',
    value:{$res->zestimate->amount}
}
EOF;

} else {
    //print_r($response);
    //die($xml->message->text);
    $json = <<<EOF
{
    error:'{$xml->message->text}',
    value:0
}
EOF;
}
echo $json;
?>
