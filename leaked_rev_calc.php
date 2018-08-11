<?php

// Remove in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class LeakedRevenue extends GetResponse {

    // Percentage
    private $percentage       = ['low' => 0.15, 'high' => 0.40];

    // Leaked Revenue Lowend
    private $leakedRevenue    = [];

    // Email Address
    private $emailAddress;

    // Orders Per Month
    private $ordersPerMonth;

    // Monthly Revenue
    private $monthlyRevenue;


     // Sanitize input data using PHP filter_var().
    function __construct() {

        // Set Email Address
        $this->emailAddress   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        // Set Orders Per Month
        $this->ordersPerMonth = filter_var(trim($_POST["orders-per-month"]));
        // Set Monthly Revenue
        $this->monthlyRevenue = filter_var(trim($_POST["revenue-per-month"]));

        // Get Leaked Revenue
        $this->getLeakedRevenue();


        // Contact Data
        $data   = array (

            // Full Name
            'name'              => 'Friend',

            // Email of the contact (must be unique per campaign)
            'email'             => $this->emailAddress,

            // Day of autoresponder cycle
            'dayOfCycle'        => 0,

            // Target campaign resource that this contact should be added to.
            'campaign'          => array('campaignId' => '6fK5H'),

             // IP address of a contact (IPv4 or IPv6)
            'ipAddress'         => $_SERVER['REMOTE_ADDR'],

            // Collection of customFieldValues to be assigned to contact
            'customFieldValues' => array(

                // Monthly Revenue
                array('customFieldId' => 'S5UH9', 'value' => array($this->monthlyRevenue)),

                // Order Per Month
                array('customFieldId' => 'S5UvR', 'value' => array($this->ordersPerMonth)),

                // Store Leaked Revenue Low-end
                array('customFieldId' => 'Sid1x', 'value' => array($this->leakedRevenue['low'])),

                // Store Leaked Revenue High-end
                array('customFieldId' => 'Uyq99', 'value' => array($this->leakedRevenue['high']))
            )
        );

        parent::addContact($data);


    }

    /**
     * Calculate Leaked Revenue
     */
    function getLeakedRevenue() {
       $this->leakedRevenue['low']  = $this->monthlyRevenue * $this->percentage['low'];
       $this->leakedRevenue['high'] = $this->monthlyRevenue * $this->percentage['high'];
    }

}



class GetResponse {


    // GetResponse API Key
    private $apiKey = 'cb779d1f701ad30f05a28b5f6715b1f3';
    private $apiUrl = 'https://api.getresponse.com/v3';


    /**
     * Get Custom Fields
     */
    function getCustomFields() {

        // Get cURL resource
        $curl    = curl_init();

        // cURL requrest options
        $options = array(
            // Allow curl_exec to return result
            CURLOPT_RETURNTRANSFER => true,
            // URL to cURL
            CURLOPT_URL            => $this->apiUrl.'/custom-fields/?fields=name&sort[name]=desc&page=4&perPage=30',
            // Set Method
            CURLOPT_CUSTOMREQUEST  => 'GET',
            // Set Headers
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Auth-Token: api-key '.$this->apiKey,
            )
        );

        // Set some options
        curl_setopt_array($curl, $options);

        // Send the request & save response to $resp
        $response = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);

        echo $response;

    }


    /**
     * Add Contact to List
     * 
     * @param array $data 
     */
    function addContact($data) {

        //todo check $data

        // Encode array into JSON
        $jsonContactData  = json_encode($data);

        // Initialize a cURL session
        $curl             = curl_init();

        // POST options
        $options          = array( 
            // URL
            CURLOPT_URL            => $this->apiUrl.'/contacts',
            // Method type
            CURLOPT_CUSTOMREQUEST  => 'POST',
            // Post fields
            CURLOPT_POSTFIELDS     => $jsonContactData,
            // Return response instead of printing.
            CURLOPT_RETURNTRANSFER => true,
            // Set HTTP Headers
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Auth-Token: api-key '.$this->apiKey,
            )
        );

        // Set some options
        curl_setopt_array($curl, $options);


        // Perform a cURL session (send request)
        $response = curl_exec($curl);

        // Handle Curl Errors
        $this->handleCurlError($curl);

        // Get Response Code
        echo curl_getinfo($curl, CURLINFO_HTTP_CODE);

        echo $response;

        // Close the handle
        curl_close($curl);

    }

    /**
     * Handle cURL Errors
     * 
     * @param array $curl Returns a cURL handle on success, FALSE on errors.
     */
    function handleCurlError($curl) {

        //todo check $curl

        // Return the last error number or 0 if none found
        if($curlError = curl_errno($curl)) {
            // Return string describing the given error code
            $curlErrorMessage = curl_strerror($curlError);
            // Print Error Message
            echo "cURL error ({$curlError}):\n {$curlErrorMessage}";
        }

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
            )
        );

        die($output);
    }

    // Check $_POST vars are set, exit if any missing
    if(!isset($_POST["orders-per-month"]) || !isset($_POST["revenue-per-month"]) || !isset($_POST["email"])) {
        $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
        die($output);
    }

    new LeakedRevenue();

}

?>
