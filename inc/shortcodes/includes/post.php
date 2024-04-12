<?php

// requiring WC Rest API SDK
require_once __DIR__ . '/../../../wc-api-php-trunk/vendor/autoload.php';
use Automattic\WooCommerce\Client;


// if posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // if posted certain values
  if( isset($_POST["picqer_api_product_id"]) && isset($_POST["current_url"]) ){

    // to get the options values
    require_once '../../../../../../wp-config.php';

    // assigning
    $picqer_api_product_id = picqer_secure_input($_POST["picqer_api_product_id"]);
    $current_url = picqer_secure_input($_POST["current_url"]);

    echo importSingleProduct( $picqer_api_product_id, $current_url );


  }  // if posted certain values ends







// different post
  // if posted certain values
  if( isset( $_POST["all_product"]) ){

    // to get the options values
    require_once '../../../../../../wp-config.php';

      // assigning
    $all_product = picqer_secure_input($_POST["all_product"]);

    if($all_product == 'yes'){

    // initializing
    $picqer_api_base_url = '';
    $picqer_api_username = '';
    $picqer_api_password = '';
    $picqer_api_language = '';
    $resultHTML = '';

    // assigning values got from wp options
    $picqer_api_base_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url');
    $picqer_api_username = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username');
    $picqer_api_password = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password');
    $picqer_api_language = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language');


    // picqer API Queries
    require_once('PicqerApiQueries.php');

    // instantiating
    $ApiQuery = new PicqerApiQueries;

      try {
      // sending all products API request to picqer
      $picqer_api_all_products = $ApiQuery->picqer_api_all_products($picqer_api_base_url, $picqer_api_username, $picqer_api_password);
      } catch (PDOException $e) {
        $resultHTML .= "Error: " . $e->getMessage();
      }finally{
        $picqer_api_all_products = json_decode($picqer_api_all_products, true);
      }
      // if a valid response
      if( isset($picqer_api_all_products) && count($picqer_api_all_products) > 0 ){

        $resultHTML .= '<p class="text-center">Click any items below to import: (Green ones are already imported!)</p>';

        $all_picqer_product_items = $picqer_api_all_products;

        // get all available products in WC
        $picqer_products_sku_list = get_option('picqer_products_sku_list');
        foreach( $all_picqer_product_items as $item_key => $single_product_item ){
          $single_product_ids = $single_product_item["idproduct"];
          if (in_array($single_product_ids, $picqer_products_sku_list)){
            $resultHTML .= '<span class="text-success picqer_product_ids">'.$single_product_ids.'</span>';
          }else{
            $resultHTML .= '<span class="text-danger picqer_product_ids picqer_clickable_product_ids">'.$single_product_ids.'</span>';
          }
        }

        $resultHTML .= '
        <script>
          $(".picqer_clickable_product_ids").click(function(){
            $("#woocommerce_picqer_api_product_id_field").val($(this).html());
          });
        </script>
        ';

      }else{
        $resultHTML .= '<p class="text-center">No Product IDs found!</p>';
      }

    echo $resultHTML;

  }
  // if yes

  }




// different post
  // if posted certain values
  if( isset( $_POST["import_all_product"]) && isset($_POST["current_url"]) ){

    // to get the options values
    require_once '../../../../../../wp-config.php';

    // assigning
    $import_all_product = picqer_secure_input($_POST["import_all_product"]);
    $current_url = picqer_secure_input($_POST["current_url"]);

    if($import_all_product == 'yes'){

    // initializing
    $picqer_api_base_url = '';
    $picqer_api_username = '';
    $picqer_api_password = '';
    $picqer_api_language = '';
    $resultHTML = '';
    $resultArray = [];
    $iterationNumber = 0;

    // assigning values got from wp options
    $picqer_api_base_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url');
    $picqer_api_username = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username');
    $picqer_api_password = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password');
    $picqer_api_language = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language');


    // picqer API Queries
    require_once('PicqerApiQueries.php');

    // instantiating
    $ApiQuery = new PicqerApiQueries;

      try {
      // sending single product API request to picqer
      $picqer_api_all_products = $ApiQuery->picqer_api_all_products($picqer_api_base_url, $picqer_api_username, $picqer_api_password);
      } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
      }finally{
        $picqer_api_all_products = json_decode($picqer_api_all_products, true);
      }
      // if a valid response
      if( isset($picqer_api_all_products) && count($picqer_api_all_products) > 0 ){

        $all_picqer_product_items = $picqer_api_all_products;

        foreach( $all_picqer_product_items as $item_key => $single_product_item ){
          $single_product_id = $single_product_item["idproduct"];

            array_push($resultArray, $single_product_id);

        }
      
      }

    // loop through all IDs
    foreach( $resultArray as $single_key => $single_id ){
      if( $single_id != '' ){
        $single_id = picqer_secure_input( $single_id );
        try {
          importSingleProduct($single_id, $current_url);
        } catch (PDOException $e) {
          $resultHTML .= "Error: " . $e->getMessage();
        }finally{
          $iterationNumber++;
        }
      }
    }

    echo $iterationNumber;

  }
  // if yes

  }




}   // if posted ends












function importSingleProduct($picqer_api_product_id, $current_url){

    // initializing
    $website_url = '';
    $woocommerce_api_consumer_key = '';
    $woocommerce_api_consumer_secret = '';
    $woocommerce_api_mul_val = 1;
    $picqer_api_base_url = '';
    $picqer_api_username = '';
    $picqer_api_password = '';
    $picqer_api_language = '';
    $wc_prod_tags = '';
    $open_ai_api_key = '';
    $open_ai_model = '';
    $open_ai_temperature = '';
    $open_ai_max_tokens = '';
    $open_ai_frequency_penalty = '';
    $open_ai_presence_penalty = '';
    $custom_tags_on_off = '';
    $open_ai_on_off = '';

    $resultHTML = '';

    // assigning values got from wp options
    $website_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url');
    $woocommerce_api_consumer_key = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key');
    $woocommerce_api_consumer_secret = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret');
    $woocommerce_api_mul_val = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val');
    $picqer_api_base_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url');
    $picqer_api_username = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username');
    $picqer_api_password = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password');
    $picqer_api_language = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language');
    $wc_prod_tags = picqer_secure_input(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags'));

    // open ai option values
    $open_ai_api_key = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key');
    $open_ai_model = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model');
    $open_ai_temperature = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature');
    $open_ai_max_tokens = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens');
    $open_ai_frequency_penalty = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty');
    $open_ai_presence_penalty = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty');

    $custom_tags_on_off = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off');
    $open_ai_on_off = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off');

    // assigning language
    $language_full_name = "";
    switch ($picqer_api_language) {
      case "nl-NL":
        $language_full_name = "Dutch";
        break;
      case "en-GB":
        $language_full_name = "English";
        break;
      case "de-DE":
        $language_full_name = "German";
        break;
    }



    // WC Rest API SDK instantiating
    $woocommerce = new Client(
      $website_url,
      $woocommerce_api_consumer_key,
      $woocommerce_api_consumer_secret,
      [
        'version' => 'wc/v3',
      ]
    );

    // Picqer API Queries
    require_once('PicqerApiQueries.php');

    // instantiating
    $ApiQuery = new PicqerApiQueries;

    try {

      // sending single product API request to Picqer
      $picqer_api_single_product = $ApiQuery->picqer_api_single_product($picqer_api_base_url, $picqer_api_username, $picqer_api_password, $picqer_api_product_id);

      } catch (PDOException $e) {

        $resultHTML .= "Error: " . $e->getMessage();

      }finally{

        // assigning some useful values got from Picqer API response
        $picqer_api_single_product = json_decode($picqer_api_single_product, true);

        // if a valid response
        if( isset( $picqer_api_single_product["idproduct"] ) ){

          // initializing 
          $picqer_cat_name = 'Picqer';
          $picqer_sub_cat_name = 'Picqer';
          $picqer_prod_name = '';
          $picqer_prod_brand = 'Picqer';
          $picqer_prod_sku = '';
          $picqer_prod_id = '';
          $picqer_prod_img = '';
          $picqer_prod_desc = '';
          $picqer_prod_short_desc = '';
          $picqer_regular_price = 0;
          $picqer_stock_quantity = 0;
          

          if(isset($picqer_api_single_product["categories"][0]["hcat_name"])){
            $picqer_cat_name = $picqer_api_single_product["categories"][0]["hcat_name"];
          }

          if(isset( $picqer_api_single_product["categories"][0]["cat_name"] )){
            $picqer_sub_cat_name = $picqer_api_single_product["categories"][0]["cat_name"];
          }

          if(isset( $picqer_api_single_product["name"] )){
            $picqer_prod_name = $picqer_api_single_product["name"];
          }

          if(isset( $picqer_api_single_product["productfields"][0]["value"] )){
            $picqer_prod_brand = $picqer_api_single_product["productfields"][0]["value"];
          }

          if(isset( $picqer_api_single_product["idproduct"] )){
            $picqer_prod_sku = $picqer_api_single_product["idproduct"];
          }

          if(isset( $picqer_api_single_product["idproduct"] )){
            $picqer_prod_id = $picqer_api_single_product["idproduct"];
          }

          if(isset( $picqer_api_single_product["description"] )){
            $picqer_prod_desc = $picqer_api_single_product["description"];
          }

          if(isset( $picqer_api_single_product["description"] )){
            $picqer_prod_short_desc = $picqer_api_single_product["description"];
          }

          // updated product name
          if(str_contains($picqer_prod_name, $picqer_prod_brand) && !str_contains($picqer_prod_name, $picqer_prod_sku)){
              $picqer_prod_name = $picqer_prod_name . ' #' . $picqer_prod_sku;
          }
          elseif(!str_contains($picqer_prod_name, $picqer_prod_brand) && str_contains($picqer_prod_name, $picqer_prod_sku)){
              $picqer_prod_name = $picqer_prod_brand . ' ' . $picqer_prod_name;
          }
          elseif(!str_contains($picqer_prod_name, $picqer_prod_brand) && !str_contains($picqer_prod_name, $picqer_prod_sku)){
              $picqer_prod_name = $picqer_prod_brand . ' ' . $picqer_prod_name . ' #' . $picqer_prod_sku;
          }
          // updated product name ends

          if(isset( $picqer_api_single_product["price"] )){
            $picqer_regular_price = floatval($picqer_api_single_product["price"]);
          }

          if(isset( $picqer_api_single_product["stock"] )){
            $picqer_stock_array = $picqer_api_single_product["stock"];
            foreach($picqer_stock_array as $stock_key => $single_stock){
              $picqer_stock_quantity += intval($single_stock["freestock"]);
            }
            if($picqer_stock_quantity < 0){
              $picqer_stock_quantity = 0;
            }
          }

          // used in product meta
          $product_meta_data_array = [];
          if(isset( $picqer_api_single_product["productfields"] )){
            $productfields = $picqer_api_single_product["productfields"];
            foreach($productfields as $productfield_key => $productfield_value){
              array_push($product_meta_data_array, [
                'key' => $productfield_value["title"],
                'value' => $productfield_value["value"],
              ]);
            }
          }


          // creating product images array
          $picqer_all_image_src_array = [];
          foreach( $picqer_api_single_product["images"] as $single_image_url_key => $single_image_url){
            array_push($picqer_all_image_src_array, [
              'src' => $single_image_url,
              'name' => $picqer_prod_name,
              'alt' => $picqer_prod_name,
            ]);
          }



// //  open AI starts here 

if($open_ai_api_key && $open_ai_api_key != ''){
  if( $open_ai_on_off == 'yes' ){
    $resultHTML .= '<p class="text-center">OpenAI API has been started...</p>';
    // creating better description
    $open_ai_prompt = 'create a better product description in '.$language_full_name.' from this description &bdquo;'.$picqer_prod_desc.'&bdquo;';
    try {
      // sending request to openAI
      $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
    } catch (PDOException $e) {
      $resultHTML .= "Error: " . $e->getMessage();
    }finally{
      if($open_ai_request_response != ''){
        $picqer_prod_desc = str_replace('"', '', $open_ai_request_response);
        $resultHTML .= '<p class="text-center">OpenAI API has updated the product description...</p>';
      }else{
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product description...</p>';
      }
    }

    // creating better short-description
    $open_ai_prompt = 'create a better product short description in '.$language_full_name.' from this short description &bdquo;'.$picqer_prod_short_desc.'&bdquo;';
    try {
      // sending request to openAI
      $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
    } catch (PDOException $e) {
      $resultHTML .= "Error: " . $e->getMessage();
    }finally{
      if($open_ai_request_response != ''){
        $picqer_prod_short_desc = str_replace('"', '', $open_ai_request_response);
        $resultHTML .= '<p class="text-center">OpenAI API has updated the product short-description...</p>';
      }else{
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product short-description...</p>';
      }
    }


    if( $custom_tags_on_off != 'yes' ){
      $open_ai_prompt = 'create comma separated string of product tags from this description in '.$language_full_name.' &bdquo;'.$picqer_prod_desc.'&bdquo;';
      try {
        // sending request to openAI
        $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
      } catch (PDOException $e) {
        $resultHTML .= "Error: " . $e->getMessage();
      }finally{
        if($open_ai_request_response != ''){
          $wc_prod_tags = picqer_secure_input($open_ai_request_response);
          $resultHTML .= '<p class="text-center">OpenAI API has updated the product tags...</p>';
        }else{
          $resultHTML .= '<p class="text-center">OpenAI API could not update the product tags...</p>';
        }
      }
    }
  }
}else{
  $resultHTML .= '<p class="text-center">OpenAI API Key is missing. Started Default Import...</p>';
}

// //  open AI ends here 




          // getting all WC categories
          $product_category_list = [];

          // initializing
          $page = 1;

          // infinite loop
          while(1 == 1) {

            // initializing for grabbing all categories
            $data = [
              'page' => $page,
              'per_page' => 100,
            ];

            try{
              // getting all WC categories
              $product_category_list_temp = $woocommerce->get('products/categories', $data);

            } catch (PDOException $e) {

              $resultHTML .= "Error: " . $e->getMessage();
      
            } 

            $product_category_list = array_merge($product_category_list, $product_category_list_temp);

            if( count($product_category_list_temp) < 100 ){
              break;
            }

            $page++;

          }
          // infinite loop ends here


            // creating all category names array
            $product_category_names = [];

            foreach($product_category_list as $key => $single_category){

              $product_category_names[$single_category->id] = picqer_secure_input($single_category->name);

            }

            // checking category names exist or not
            $key1 = array_search(picqer_secure_input($picqer_cat_name), $product_category_names);

            // creating WC category
            if ($key1 !== false) {

              $resultHTML .= '<p class="text-center">Category ('.$picqer_cat_name.') already exists!</p>';

            }else{

              $category = [
                  'name' => $picqer_cat_name
              ];

              try {

                $callBack1 = $woocommerce->post('products/categories', $category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Category ('.$picqer_cat_name.') has been created!</p>';

              }

            }


            // checking sub-category names exist or not
            $key2 = array_search(picqer_secure_input($picqer_sub_cat_name), $product_category_names);

            // creating WC sub-category
            if ($key2 !== false) {

              $resultHTML .= '<p class="text-center">Sub-Category ('.$picqer_sub_cat_name.') already exists!</p>';

            }else{

              $sub_category = [
                  'name' => $picqer_sub_cat_name,
                  'parent' => (isset($callBack1->id)) ? $callBack1->id : $key1
              ];

              try {

                $callBack2 = $woocommerce->post('products/categories', $sub_category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Sub-Category ('.$picqer_sub_cat_name.') has been created!</p>';

              }

            }


            // checking sub-category names exist or not
            $key5 = array_search(picqer_secure_input($picqer_prod_brand), $product_category_names);

            // creating WC sub-category
            if ($key5 !== false) {

              $resultHTML .= '<p class="text-center">Sub-Category ('.$picqer_prod_brand.') already exists!</p>';

            }else{

              $sub_category = [
                  'name' => $picqer_prod_brand,
                  'parent' => (isset($callBack1->id)) ? $callBack1->id : $key1
              ];

              try {

                $callBack5 = $woocommerce->post('products/categories', $sub_category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Sub-Category ('.$picqer_prod_brand.') has been created!</p>';

              }

            }
            // creating category and sub-category ends here

  


              // getting all WC products
              $wc_all_products = [];
              // initializing
              $page = 1;
              // infinite loop
              while(1 == 1) {
                // initializing for grabbing all products
                $data = [
                  'page' => $page,
                  'per_page' => 100,
                ];
                try{
                  // getting all WC products
                  $all_products_list_temp = $woocommerce->get('products',  $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $wc_all_products = array_merge($wc_all_products, $all_products_list_temp);
    
                if( count($all_products_list_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


          
                  // creating all products array
                  $wc_all_prod_array = [];

                  if($wc_all_products){

                    if(count($wc_all_products) != 0){

                      foreach($wc_all_products as $key => $single_wc_prod){

                        $wc_all_prod_array[$single_wc_prod->id] = $single_wc_prod->sku;

                      }
                      
                    }

                  }



              // getting all WC product tags
              $retrieved_all_tags = [];
              // initializing
              $page = 1;
              // infinite loop
              while(1 == 1) {
                // initializing for grabbing all categories
                $data = [
                  'page' => $page,
                  'per_page' => 100,
                ];
                try{
                  // getting all WC products
                  $retrieved_all_tags_temp = $woocommerce->get('products/tags', $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $retrieved_all_tags = array_merge($retrieved_all_tags, $retrieved_all_tags_temp);
    
                if( count($retrieved_all_tags_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


              // creating all tags array
              $tag_names_array = [];

              if($retrieved_all_tags){

                if(count($retrieved_all_tags) != 0){

                  foreach($retrieved_all_tags as $key => $single_tag){

                    $tag_names_array[$single_tag->id] = picqer_secure_input($single_tag->name);

                  }

                }

              }

              // creating tags array
              $tags_array = [];
              $final_tag_names_array = [];
              
              // creating the tag if not exists
              if($wc_prod_tags != ''){

                $wc_prod_tags = explode(',' , $wc_prod_tags);

                if(count($wc_prod_tags) > 0){

                  foreach($wc_prod_tags as $key => $single_tag){

                    $single_tag = picqer_secure_input($single_tag);

                    $tag_key = array_search($single_tag, $tag_names_array);

                    if ($tag_key !== false) {

                      array_push($tags_array,[
                        'id' => $tag_key,
                      ]);
                      array_push($final_tag_names_array, $single_tag);

                      $resultHTML .= '<p class="text-center">Tag '.($key + 1).' ('.$single_tag.') already exists!</p>';

                    }else{

                      $data = [
                          'name' => $single_tag
                      ];

                      try {

                        $wc_create_tag = $woocommerce->post('products/tags', $data);

                      }catch (PDOException $e) {

                        $resultHTML .= "Error: " . $e->getMessage();

                      }finally{

                        array_push($tags_array,[
                          'id' => $wc_create_tag->id,
                        ]);
                        array_push($final_tag_names_array, $wc_create_tag->name);

                        $resultHTML .= '<p class="text-center">Tag '.($key + 1).' ('.$single_tag.') created successfully!</p>';

                      }

                    }

                  }

                }

              }
          // creating the tags ends here


            // if product sku exists or not
            $key3 = array_search($picqer_prod_sku, $wc_all_prod_array);

            if ($key3 !== false) {
              // get the correct product id
              $wc_product_id = $key3;
              try {
                // retrieving the product
                $wc_retrieved_product = $woocommerce->get('products/' . strval($wc_product_id));
              }catch (PDOException $e) {
                $resultHTML .= "Error: " . $e->getMessage();
              }

              $wc_total_images = count($wc_retrieved_product->images);
              $picqer_total_images = count($picqer_all_image_src_array);
              $missed_images_array = [];

              if($wc_total_images == 0){
                $updated_images_array = [];
              }else{
                $updated_images_array = $wc_retrieved_product->images;
              }

              if($picqer_total_images > $wc_total_images){
                  for($i = $wc_total_images; $i < $picqer_total_images; $i++){

                    try {
                      $image_id = woocommerce_picqer_api_custom_image_file_upload( $picqer_all_image_src_array[$i]['src'], $picqer_all_image_src_array[$i]['name'] );
                    }catch (PDOException $e) {
                      $resultHTML .= "Error: " . $e->getMessage();
                    }finally{
                      if(is_int($image_id)){
                        array_push($updated_images_array,  [
                          'id' => $image_id,
                          'name' => $picqer_all_image_src_array[$i]['name'],
                          'alt' => $picqer_all_image_src_array[$i]['alt'],
                        ]);
                      }else{
                        array_push($missed_images_array, $i + 1);
                      }
                    }
                  }
              }

              // creating product data
              $data = [
                'name' => $picqer_prod_name,
                'regular_price' => strval($picqer_regular_price),
                'description' => $picqer_prod_desc,
                'short_description' => $picqer_prod_short_desc,
                'sku' => strval($picqer_prod_sku),
                'categories' => [
                    [
                        'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                    ],
                    [
                        'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                    ],
                ],
                'images' => $updated_images_array,
                'tags'  => $tags_array,
                'meta_data' =>  $product_meta_data_array,
                'manage_stock' =>  true,
                'stock_quantity' =>  $picqer_stock_quantity,
              ];

              try {

                // trying to update a WC product
                $update_wc_prod = $woocommerce->put('products/' . strval($key3), $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                $wc_retrieved_product = $update_wc_prod;
                $product_id = $wc_retrieved_product->id;
                $product_sku = $wc_retrieved_product->sku;

                $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_prod_name.') updated successfully!</p>';

              }

            }else{

              // initializing
              $updated_images_array = [];
              $missed_images_array = [];

              // adding images
              foreach($picqer_all_image_src_array as $image_key => $single_picqer_image){
                try {
                  $image_id = woocommerce_picqer_api_custom_image_file_upload( $single_picqer_image['src'], $single_picqer_image['name'] );
                }catch (PDOException $e) {
                  $resultHTML .= "Error: " . $e->getMessage();
                }finally{
                  if(is_int($image_id)){
                    array_push($updated_images_array,  [
                      'id' => $image_id,
                      'name' => $single_picqer_image['name'],
                      'alt' => $single_picqer_image['alt'],
                    ]);
                  }else{
                    array_push($missed_images_array, $image_key + 1);
                  }
                }
              }

              // creating product data
              $data = [
                'name' => $picqer_prod_name,
                // 'type' => 'variable',
                'regular_price' => strval($picqer_regular_price),
                'description' => $picqer_prod_desc,
                'short_description' => $picqer_prod_short_desc,
                'sku' => strval($picqer_prod_sku),
                'categories' => [
                    [
                        'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                    ],
                    [
                        'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                    ],
                ],
                'images' => $updated_images_array,
                // 'attributes'  => $attributes_array,
                'tags'  => $tags_array,
                'meta_data' =>  $product_meta_data_array,
                'manage_stock' =>  true,
                'stock_quantity' =>  $picqer_stock_quantity,
              ];

              try {

                // trying to create a WC product
                $create_wc_prod = $woocommerce->post('products', $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                // get the correct product id
                $wc_retrieved_product = $create_wc_prod;
                $wc_product_id = $wc_retrieved_product->id;

                //create or update wp-option includes list of product sku for cron 
                $product_id = $wc_retrieved_product->id;
                $product_sku = $wc_retrieved_product->sku;

                if( $product_id != '' && $product_sku != ''){

                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_prod_name.') created successfully!</p>';

                  $picqer_cron_list = get_option('picqer_cron_list');

                  if ( !in_array($product_sku, $picqer_cron_list) ){

                    $picqer_cron_list[$product_id] = $product_sku;

                    update_option('picqer_cron_list', $picqer_cron_list);
    
                    $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_prod_name.') has been inserted to the cron list successfully!</p>';

                  }

                  $picqer_products_sku_list = get_option('picqer_products_sku_list');

                  if ( !in_array($product_sku, $picqer_products_sku_list) ){

                    $picqer_products_sku_list[$product_id] = $product_sku;

                    update_option('picqer_products_sku_list', $picqer_products_sku_list);
    
                    $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_prod_name.') has been inserted to the all picqer products list successfully!</p>';

                  }

                  $picqer_sku_next_to_update = get_option('picqer_sku_next_to_update');

                  if($picqer_sku_next_to_update == ''){

                    update_option('picqer_sku_next_to_update', $product_sku );

                    $resultHTML .= '<p class="text-center">Next to update option was empty</p>';
                    $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_prod_name.') has been inserted to the next to update cron successfully!</p>';

                  }

                }else{
                  $resultHTML .= '<p class="text-center">Product ('.$picqer_prod_name.') could not be imported!</p>';
                }
              // product not created ends here

            }

          }
          // end of if-else
          // creating product ends here


    }else{
      $resultHTML .= '<p class="text-center">Got no product for importing!</p>';
    }

    }
    // try catch ends here 

  return $resultHTML;



}