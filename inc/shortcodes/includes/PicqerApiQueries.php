<?php

require_once __DIR__ . '/../../../open-ai/vendor/autoload.php';

use Orhanerday\OpenAi\OpenAi;

//  no direct access 
if( !defined('ABSPATH') ) : exit(); endif;

class PicqerApiQueries
{

    // grabs all products from picqer
    public function picqer_api_all_products($picqer_api_base_url, $picqer_api_username, $picqer_api_password)
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $picqer_api_base_url . 'products',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("$picqer_api_username:$picqer_api_password"),
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }

    // grabs single product info from picqer
    public function picqer_api_single_product($picqer_api_base_url, $picqer_api_username, $picqer_api_password, $picqer_api_product_id)
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $picqer_api_base_url . 'products/' . $picqer_api_product_id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("$picqer_api_username:$picqer_api_password"),
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }



    // creating a order on picqer
    public function picqer_api_create_order( $picqer_api_base_url, $picqer_api_username, $picqer_api_password, $ProductLines, $Company, $Name, $Address, $PostalCode, $Locality, $Reference, $Country )
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $picqer_api_base_url . 'orders',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_POSTFIELDS =>'{
            "idcustomer": null,
            "deliveryname": "'.$Name.'",
            "reference": "'.$Reference.'",
            "deliveryaddress": "'.$Address.'",
            "deliveryzipcode": "'.$PostalCode.'",
            "deliverycity": "'.$Locality.'",
            "deliverycountry": "'.$Country.'",
            "invoicename": "'.$Name.'",
            "invoiceaddress": "'.$Address.'",
            "invoicezipcode": "'.$PostalCode.'",
            "invoicecity": "'.$Locality.'",
            "invoicecountry": "'.$Country.'",
            "products": '.$ProductLines.'
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$picqer_api_username:$picqer_api_password"),
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


    // creating a request to OpenAI
    public function open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty ){

      $result = '';

      if(!$open_ai_model || $open_ai_model == ''){
        $open_ai_model = 'text-davinci-003';
      }
      if(!$open_ai_temperature || $open_ai_temperature == ''){
        $open_ai_temperature = 0.9;
      }
      if(!$open_ai_max_tokens || $open_ai_max_tokens == ''){
        $open_ai_max_tokens = 500;
      }
      if(!$open_ai_frequency_penalty || $open_ai_frequency_penalty == ''){
        $open_ai_frequency_penalty = 0;
      }
      if(!$open_ai_presence_penalty || $open_ai_presence_penalty == ''){
        $open_ai_presence_penalty = 0.6;
      }

      try {

        $open_ai = new OpenAi($open_ai_api_key);

        $response = $open_ai->completion([
            'model' => $open_ai_model,
            'prompt' => $open_ai_prompt,
            'temperature' => $open_ai_temperature,
            'max_tokens' => $open_ai_max_tokens,
            'frequency_penalty' => $open_ai_frequency_penalty,
            'presence_penalty' => $open_ai_presence_penalty,
        ]);

    } catch (PDOException $e) {

        $response = "Error: " . $e->getMessage();

    }finally{

      $response = json_decode($response, true);

      // if no error
      if(!isset($response['error'])){
        if(isset($response["choices"][0]["text"])){
          $result = $response["choices"][0]["text"];
        }
      }

    }

      return $result;

    }
    // end of public function open_ai_request_response 


}