<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//check if its an ajax request, exit if not
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

    //exit script outputting json data
    $output = json_encode(
            array(
                'type' => 'error',
                'text' => 'Request must come from Ajax'
    ));

    die($output);
}

//check $_POST vars are set, exit if any missing
if (!isset($_POST["orders-per-month"]) || !isset($_POST["revenue-per-month"]) || !isset($_POST["email"])) {
    $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
    die($output);
}

//Sanitize input data using PHP filter_var().
$orderpermonth = filter_var(trim($_POST["orders-per-month"]));
$revpermonth = filter_var(trim($_POST["revenue-per-month"]));
$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL));
$lowendpercentage = 0.15;
$highendpercentage = 0.40;
$leakedrevlowend = $revpermonth * $lowendpercentage;
$leakedrevhighend = $revpermonth * $highendpercentage;


$apikeyfnm = 'cb779d1f701ad30f05a28b5f6715b1f3'; // Get From GETresponse API 32 characters 
$emailmmm = $email; // Your Valid Email e.g. phoenix@phoenixpeth.com
$getfullname = 'Friend'; // Your Name e.g. Raju Harry
/*$mobilecode = '+xx'; // Your Country code e.g. +91
$mobile = 'xxxxxxxxxx'; // Your 10 digit mobile code e.g. 9999999999*/
$addcontacturl = 'https://api.getresponse.com/v3/contacts/';
$getcontacturl = 'https://api.getresponse.com/v3/contacts?query[email]='.$emailmmm;
$data = array (
'name' => $getfullname,
'email' => $emailmmm,
'dayOfCycle' => 0,
'campaign' => array('campaignId'=>'6fK5H'),  // Your Valid Email e.g. ThwHa
'ipAddress'=>  $_SERVER['REMOTE_ADDR'], // $_SERVER['REMOTE_ADDR'] 
);  
$data_string = json_encode($data); 
$ch = curl_init($addcontacturl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'X-Auth-Token: api-key '.$apikeyfnm,
)           
);                                                                                                                   
                                                                                                                     
$result = curl_exec($ch); // Print this If you want to verfify
$chmmn = curl_init($getcontacturl );
curl_setopt($chmmn, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
curl_setopt($chmmn, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($chmmn, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'X-Auth-Token: api-key '.$apikeyfnm,
)           
);                                                                                                                   
$resultmn = curl_exec($chmmn);
$resultmn = array_shift(json_decode($resultmn,true)); // Print this If you want to verfify
$contactId = trim($resultmn['contactId']);
$customfld1 = $orderpermonth;
$customfld2 = $revpermonth;
$customfld3 = $leakedrevlowend;
$customfld4 = $leakedrevhighend;
$datamn = array (
'contactId' => $contactId,
'customFieldValues'=> array(
        array(
        'customFieldId'=> $customfld1, // e.g. OiBzq use this to get Custom field id - https://api.getresponse.com/v3/custom-fields/
        'href'=> 'https://api.getresponse.com/v3/custom-fields/'.$customfld1,                        
        'name'=> 'order_per_month',
        'fieldType'=> 'number',
        'format'=> 'text',
        'valueType'=> 'string',
        'type'=> 'number',
        'hidden'=> 'false',
        'values'=> []
        ),
        array(
         'customFieldId'=> $customfld2, // e.g. OiBzq use this to get Custom field id - https://api.getresponse.com/v3/custom-fields/
        'href'=> 'https://api.getresponse.com/v3/custom-fields/'.$customfld2,                        
        'name'=> 'revenue_per_month',
        'fieldType'=> 'number',
        'format'=> 'text',
        'valueType'=> 'string',
        'type'=> 'number',
        'hidden'=> 'false',
        'values'=> []
        ),
        array(
         'customFieldId'=> $customfld3, // e.g. OiBzq use this to get Custom field id - https://api.getresponse.com/v3/custom-fields/
        'href'=> 'https://api.getresponse.com/v3/custom-fields/'.$customfld3,                        
        'name'=> 'leaked_revenue_lowend',
        'fieldType'=> 'number',
        'format'=> 'text',
        'valueType'=> 'string',
        'type'=> 'number',
        'hidden'=> 'false',
        'values'=> []
        ),
         array(
         'customFieldId'=> $customfld4, // e.g. OiBzq use this to get Custom field id - https://api.getresponse.com/v3/custom-fields/
        'href'=> 'https://api.getresponse.com/v3/custom-fields/'.$customfld4,                        
        'name'=> 'leaked_revenue_highend',
        'fieldType'=> 'number',
        'format'=> 'text',
        'valueType'=> 'string',
        'type'=> 'number',
        'hidden'=> 'false',
        'values'=> []
        ),

    )
); 
$data_stringmn = json_encode($datamn);                                                                                   
//echo '<pre>';print_r($data_stringmn);exit;   
$mnurl = 'https://api.getresponse.com/v3/contacts/'.$contactId.'/custom-fields/';  
//echo $mnurl;exit;
$chfeld = curl_init($mnurl);
curl_setopt($chfeld, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($chfeld, CURLOPT_POSTFIELDS, $data_stringmn);                                                                  
curl_setopt($chfeld, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($chfeld, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'X-Auth-Token: api-key '.$apikeyfnm,
)           
);                                                                                                                   
                                                                                                                     
$resultcustomfld = curl_exec($chfeld); // Print this If you want to verfify
curl_close($ch);
curl_close($chmmn);
curl_close($chfeld);

?>
