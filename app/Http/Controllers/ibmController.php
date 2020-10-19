<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ibmController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if(request('message') !== null){

            // INSERT WATSON ASSISTANT DATA HERE

            // ID of Watson Assistant Workspace
            $workspace_id = 'https://api.us-south.assistant.watson.cloud.ibm.com/instances/17fcb9e3-f383-406b-a6ef-89e1337b9e2e/v1/workspaces/0615d365-5277-4cd8-a37f-e6e64b3e1f86/message';
            // Release date of the API version in YYYY-MM-DD format (*)
            $release_date = '2018-09-20';
            // Username of the service credentials
            $username = 'put-watson-assistant-username-here';
            // Password of the service credentials
            $password = 'put-watson-assistant-password-here';

            // (*) Release date allows you to use an older version of the Watson Assistant API, to have compatibility cross releases

    // Make a request message for Watson API in json
    $data['input']['text'] = $_POST['message'];
    if(isset($_POST['context']) && $_POST['context']){
      $data['context'] = json_decode($_POST['context'], JSON_UNESCAPED_UNICODE); // Encode multibyte Unicode characters literally (default is to escape as \uXXXX)
    }
    $data['alternate_intents'] = false;

    // Encode json data
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    // Post the json to Watson Assistant API via cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, 'https://gateway.watsonplatform.net/assistant/api/v1/workspaces/'.$workspace_id.'/message?version='.$release_date); // Instructions here: https://www.ibm.com/watson/developercloud/assistant/api/v1/curl.html?curl#message
    curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password); // Set cURL Watson Assistant credentials
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Set cURL headers
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    // Prepare response, close cURL and send response to front-end
    $result = trim(curl_exec($ch)); // Prepare response
    curl_close($ch); // Close
    echo json_encode($result, JSON_UNESCAPED_UNICODE); // Send response
  


        }
    }
}
