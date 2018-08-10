<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class getResponse {

    private $addContactResult;

    private $curl;
    private $curlError;
    private $curlErrorMessage;

    private $jsonContactData;

    // Lowend Percentage
    private $lowPercentage     = 0.15;
    // Highend Percentage
    private $highPercentage    = 0.40;

    // Leaked Revenue Lowend
    public $leakedRevenueLowend;
    // Leaked Revenue Highend
    public $leakedRevenueHighend;

    private $orderPerMonth;


    private $addContactData;

    // Email Address
    private $emailAddress;
    // Full Name
    private $fullName = 'friend';


    // GetResponse API Key
    private $getResponseApiKey = 'cb779d1f701ad30f05a28b5f6715b1f3';
    // GetResponse ADD Contacts API URL
    private $addContactUrl     = 'https://api.getresponse.com/v3/contacts/';
    // GetResponse GET Contact API URL
    private $getContactUrl     = 'https://api.getresponse.com/v3/contact';
    // GetResponse Campaign ID
    private $campaignId        = '6fK5H';



    // Sanitize input data using PHP filter_var().
    function __constructor() {

        // Set Email Address
        $this->emailAddress   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        // Set Orders Per Month
        $this->ordersPerMonth = filter_var(trim($_POST["orders-per-month"]));
        // Set Monthly Revenue
        $this->monthlyRevenue = filter_var(trim($_POST["revenue-per-month"]));

        // Get Leaked Revenue
        $this->getLeakedRevenue();

    }

    function getLeakedRevenue() {
       $this->leakedRevenueLowend  = $this->monthlyRevenue * $this->lowPercentage;
       $this->leakedRevenueHighend = $this->monthlyRevenue * $this->highPercentage;
    }

    function addContact() {

        $this->addContactData       = array (

            // Full Name
            'name'              => $this->fullName,

            // Email of the contact (must be unique per campaign)
            'email'             => $this->emailAddress,

            // Day of autoresponder cycle
            'dayOfCycle'        => 0,

            // Target campaign resource that this contact should be added to.
            'campaign'          => array('campaignId'=>$this->campaignId),

            // Collection of customFieldValues that should be assign to contact
            'customFieldValues' => array(

                // Monthly Revenue
                array('customFieldId' => 'revenue_per_month', 'value' => $this->monthlyRevenue),

                // Order Per Month
                array('customFieldId' => 'order_per_month', 'value' => $this->ordersPerMonth),

                // Leaked Revenue Lowend
                array('customFieldId' => 'leaked_revenue_lowend', 'value' => $this->leakedRevenueLowend),

                // Leaked Revenue Highend
                array('customFieldId' => 'leaked_revenue_highend', 'value' => $this->leakedRevenueHighend)
            ),

            // IP address of a contact (IPv4 or IPv6)
            'ipAddress'         => $_SERVER['REMOTE_ADDR']

        );

        // Encode array into JSON
        $this->jsonContactData  = json_encode($this->addContactData);

        // Initialize a cURL session
        $this->curl             = curl_init($this->$addContactUrl);


        // Method type
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");

        // Post fields
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $jsonContactData);

        // Return response instead of printing.
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        // Set HTTP Headers
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Auth-Token: api-key '.$this->getResponseApiKey,
        ));

        // Perform a cURL session (send request)
        $this->addContactResult  = curl_exec($this->curl);


        // Handle Curl Errors
        $this->handleCurlError();


        // Close the handle
        curl_close($this->curl);

    }


    function handleCurlError() {
        // Return the last error number
        if($this->curlError = curl_errno($this->addContactResult)) {
            // Return string describing the given error code
            $curlErrorMessage = curl_strerror($this->curlError);
            echo "cURL error ({$this->curlError}):\n {$this->curlErrorMessage}";
        }
    }


    function showResult() {
        echo "<pre>".$this->addContactResult."</pre>";
    }


}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

        //exit script outputting json data
        $output = json_encode(
                array(
                    'type' => 'error',
                    'text' => 'Request must come from Ajax'
        ));

        die($output);
    }

    // Check $_POST vars are set, exit if any missing
    if(!isset($_POST["orders-per-month"]) || !isset($_POST["revenue-per-month"]) || !isset($_POST["email"])) {
        $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
        die($output);
    }

    new getResponse();

}

?>
