<?php

// requiring WC Rest API SDK
require_once  'wc-api-php-trunk/vendor/autoload.php';
use Automattic\WooCommerce\Client;

// to get the options values
require_once '../../../wp-config.php';

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

$resultHTML = '';

// get option value
$picqer_cron_list = get_option('picqer_cron_list');

if( $picqer_cron_list ){

    if( is_array($picqer_cron_list) ){

        if( count($picqer_cron_list) > 0 ){

            //create or update wp-option includes most recently updated product sku for cron 
            $picqer_sku_next_to_update = get_option('picqer_sku_next_to_update');

            if( $picqer_sku_next_to_update ){

                if( $picqer_sku_next_to_update == '' ){

                    update_option('picqer_sku_next_to_update', $picqer_cron_list[array_keys($picqer_cron_list)[0]] );

                }
            
            }else{

                update_option('picqer_sku_next_to_update', $picqer_cron_list[array_keys($picqer_cron_list)[0]] );

            }

            // updated value
            $picqer_sku_next_to_update = get_option('picqer_sku_next_to_update');

            // if not empty
            if( $picqer_sku_next_to_update != ''){

            // assigning values got from wp options
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url')){
              $website_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_website_url');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key')){
              $woocommerce_api_consumer_key = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_key');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret')){
              $woocommerce_api_consumer_secret = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val')){
              $woocommerce_api_mul_val = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_woocommerce_api_mul_val');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url')){
              $picqer_api_base_url = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_base_url');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username')){
              $picqer_api_username = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_username');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password')){
              $picqer_api_password = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_password');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language')){
              $picqer_api_language = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_picqer_api_language');
            }
            if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags')){
              $wc_prod_tags = picqer_secure_input(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_wc_prod_tags'));
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
            require_once( WOOCOMMERCE_PICQER_API_PLUGIN_PATH . 'inc/shortcodes/includes/PicqerApiQueries.php');

            // instantiating
            $ApiQuery = new PicqerApiQueries;

            try {

                // sending single product API request to Picqer
                $picqer_api_single_product = $ApiQuery->picqer_api_single_product($picqer_api_base_url, $picqer_api_username, $picqer_api_password, $picqer_sku_next_to_update);
            
            } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
            }finally{

                // assigning some useful values got from Picqer API response
                $picqer_api_single_product = json_decode($picqer_api_single_product, true);

                // if a valid response
                if( isset( $picqer_api_single_product["idproduct"] ) ){

          // initializing 
          $picqer_model_name = 'Picqer';
          $picqer_barcode = '';
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

          $Picqer_Kleur = 'Black';
          $Picqer_Maat = 'XL';
          $picqer_atrributes = ["Color", "Size"];
          

          if(isset($picqer_api_single_product["model_name"])){
            $picqer_model_name = $picqer_api_single_product["model_name"];
          }

          if(isset($picqer_api_single_product["barcode"])){
            $picqer_barcode = $picqer_api_single_product["barcode"];
          }

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
            $picqer_regular_price = round(( floatval($woocommerce_api_mul_val) * floatval($picqer_api_single_product["price"]) ), 2);
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
              if( $productfield_value["title"] == "Kleur" ){
                $Picqer_Kleur = $productfield_value["value"];
              }
              if( $productfield_value["title"] == "Maat" ){
                $Picqer_Maat = $productfield_value["value"];
              }
              array_push($product_meta_data_array, [
                'key' => $productfield_value["title"],
                'value' => $productfield_value["value"],
              ]);
            }
          }



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

                        // $wc_all_prod_array[$single_wc_prod->id] = $single_wc_prod->sku;
                        $wc_all_prod_array[$single_wc_prod->id] = $single_wc_prod->name;

                      }
                      
                    }

                  }



                // if product sku exists or not
                $key3 = array_search($picqer_model_name, $wc_all_prod_array);

                if ($key3 !== false) {

                  // get the correct product id
                  $wc_product_id = $key3;
                  
                  try {
                    // retrieving the product
                    $wc_retrieved_product = $woocommerce->get('products/' . strval($wc_product_id));
                  }catch (PDOException $e) {
                    $resultHTML .= "Error: " . $e->getMessage();
                  }

                  $product_id = $wc_retrieved_product->id;
                  $product_sku = $wc_retrieved_product->sku;

                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_model_name.') already exists!</p>';




            // if product exists
            if ($wc_product_id != ''){

              // getting all WC product variations
              $wc_all_product_variations = [];
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
                  $wc_all_product_variations_temp = $woocommerce->get('products/'.strval($wc_product_id).'/variations', $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $wc_all_product_variations = array_merge($wc_all_product_variations, $wc_all_product_variations_temp);
    
                if( count($wc_all_product_variations_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


              // creating WC variations sku array
              $wc_variations_sku_array = [];

              if($wc_all_product_variations){

                if(count($wc_all_product_variations) != 0){

                  foreach($wc_all_product_variations as $key => $single_variation){

                    $single_variation_id = $single_variation->id;
                    $single_variation_sku = $single_variation->sku;

                    $wc_variations_sku_array[$single_variation_id] = $single_variation_sku;

                  }

                }

              }


            // if variation sku exists or not
            $key4 = array_search($picqer_prod_sku, $wc_variations_sku_array);

            if ($key4 !== false) {

              $resultHTML .= '<p class="text-center">Variant '.$key4.' ('.$picqer_prod_name.') already exists!</p>';

              // if the variation is the first variation 
              if( $picqer_prod_sku == $wc_all_product_variations[ count($wc_all_product_variations) - 1 ]->sku ){
                
                $resultHTML .= '<p class="text-center">Variant '.$key4.' ('.$picqer_prod_name.') is the first one. So, updating the product itself...</p>';

                // updating product data
                $data = [
                  'name' => $picqer_model_name,
                  'description' => $picqer_prod_desc,
                  'short_description' => $picqer_prod_short_desc,
                  'categories' => [
                      [
                          'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                      ],
                      [
                          'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                      ],
                  ],
                  'meta_data' =>  $product_meta_data_array,
                ];

                try {

                  // trying to update a WC product
                  $update_wc_prod = $woocommerce->put('products/' . strval($wc_product_id), $data);
  
                }catch (PDOException $e) {
  
                  $resultHTML .= "Error: " . $e->getMessage();
  
                }finally{
  
                  $wc_retrieved_product = $update_wc_prod;
                  $product_id = $wc_retrieved_product->id;
                  $product_sku = $wc_retrieved_product->sku;
  
                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_model_name.') updated successfully!</p>';
  
                }

              }

              // creating variation data
              $variation_data = [
                'regular_price' => strval($picqer_regular_price),
                'description' => $picqer_prod_name,
                'sku' => strval($picqer_prod_sku),
                'meta_data' =>  $product_meta_data_array,
                'manage_stock' =>  true,
                'stock_quantity' =>  $picqer_stock_quantity,
              ];


              try {

                // updating a variant
                $wc_create_or_update_variant = $woocommerce->put('products/'.$wc_product_id.'/variations/' . strval($key4), $variation_data);

              } catch (PDOException $e) {
        
                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Variant '.$key4.' ('.$picqer_prod_name.') updated successfully!</p>';

              }


            }else{


                try {
                  // retrieving the product
                  $wc_retrieved_product = $woocommerce->get('products/' . strval($wc_product_id));
                }catch (PDOException $e) {
                  $resultHTML .= "Error: " . $e->getMessage();
                }


                $updated_images_array = $wc_retrieved_product->images;
                $image_id = $updated_images_array[0]->id;

                if ($wc_retrieved_product->sku != strval($picqer_barcode) ) {
                  $image_id = woocommerce_picqer_api_custom_image_file_upload( $picqer_all_image_src_array[0]['src'], $picqer_all_image_src_array[0]['name'] );

                  array_push($updated_images_array, [
                    'id' => $image_id,
                    'name' => $picqer_all_image_src_array[0]['name'],
                    'alt' => $picqer_all_image_src_array[0]['alt'],
                  ]);
                }

                  // creating attributes array
                  $color_attribute_options = [];
                  $size_attribute_options = [];
                  $attributes_array = $wc_retrieved_product->attributes;

                  foreach( $attributes_array as $attributes_key => $attributes_value ){
                    if( $attributes_value->name == "Color" ){
                      $color_attribute_options = $attributes_value->options;
                    }
                    else if( $attributes_value->name == "Size" ){
                      $size_attribute_options = $attributes_value->options;
                    }
                  }

                  if ( !in_array( $Picqer_Kleur, $color_attribute_options ) ){
                    array_push( $color_attribute_options, $Picqer_Kleur );
                  }
                  if ( !in_array( $Picqer_Maat, $size_attribute_options ) ){
                    array_push( $size_attribute_options, $Picqer_Maat );
                  }

                  array_push($attributes_array,[
                    'id'        => array_search("Color", $wc_all_attributes_array),
                    'variation' => true,
                    'visible'   => true,
                    'options'   => $color_attribute_options,
                  ]);

                  array_push($attributes_array,[
                    'id'        => array_search("Size", $wc_all_attributes_array),
                    'variation' => true,
                    'visible'   => true,
                    'options'   => $size_attribute_options,
                  ]);
                  // creating attributes array ends here

                // creating product data
                $data = [
                  'images' => $updated_images_array,
                  'attributes' => $attributes_array,
                ];


                try {

                  // trying to update a WC product
                  $update_wc_prod = $woocommerce->put('products/' . strval($wc_product_id), $data);
  
                }catch (PDOException $e) {
  
                  $resultHTML .= "Error: " . $e->getMessage();
  
                }finally{
  
                  $wc_retrieved_product = $update_wc_prod;
                  $product_id = $wc_retrieved_product->id;
                  $product_sku = $wc_retrieved_product->sku;
  
                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_model_name.') updated successfully!</p>';
  
                }


                // creating variation attributes array
                $variation_attributes_array = [];
                array_push($variation_attributes_array,[
                  'id' => array_search("Color", $wc_all_attributes_array),
                  'option' => $Picqer_Kleur,
                ]);
                array_push($variation_attributes_array,[
                  'id' => array_search("Size", $wc_all_attributes_array),
                  'option' => $Picqer_Maat,
                ]);


              // creating variation data
              $variation_data = [
                'regular_price' => strval($picqer_regular_price),
                'description' => $picqer_prod_name,
                'sku' => strval($picqer_prod_sku),
                'image' => [
                  'id' => $image_id,
                ],
                'attributes' => $variation_attributes_array,
                'meta_data' =>  $product_meta_data_array,
                'manage_stock' =>  true,
                'stock_quantity' =>  $picqer_stock_quantity,
              ];

              try {

                // creating a variant
                $wc_create_or_update_variant = $woocommerce->post('products/'.$wc_product_id.'/variations', $variation_data);

              } catch (PDOException $e) {
        
                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $variation_id = $wc_create_or_update_variant->id;

                $resultHTML .= '<p class="text-center">Variant '.$wc_create_or_update_variant->id.' ('.$picqer_prod_name.') created successfully!</p>';

              }



              $picqer_cron_list = get_option('picqer_cron_list');

              if ( !in_array($picqer_prod_sku, $picqer_cron_list) ){

                $picqer_cron_list[$variation_id] = $picqer_prod_sku;

                update_option('picqer_cron_list', $picqer_cron_list);

                $resultHTML .= '<p class="text-center">Product variant ('.$variation_id.') => ('.$picqer_prod_sku.') => ('.$picqer_model_name.') has been inserted to the cron list successfully!</p>';

              }

              $picqer_products_sku_list = get_option('picqer_products_sku_list');

              if ( !in_array($picqer_prod_sku, $picqer_products_sku_list) ){

                $picqer_products_sku_list[$variation_id] = $picqer_prod_sku;

                update_option('picqer_products_sku_list', $picqer_products_sku_list);

                $resultHTML .= '<p class="text-center">Product variant ('.$variation_id.') => ('.$picqer_prod_sku.') => ('.$picqer_model_name.') has been inserted to the all picqer products list successfully!</p>';

              }

              $picqer_sku_next_to_update = get_option('picqer_sku_next_to_update');

              if($picqer_sku_next_to_update == ''){

                update_option('picqer_sku_next_to_update', $picqer_prod_sku );

                $resultHTML .= '<p class="text-center">Next to update option was empty</p>';
                $resultHTML .= '<p class="text-center">Product variant ('.$variation_id.') => ('.$picqer_prod_sku.') => ('.$picqer_model_name.') has been inserted to the next to update cron successfully!</p>';

              }


            }




            }




            // update the wp option for next to update sku

            $key6 = array_search($picqer_sku_next_to_update, $picqer_cron_list);

            $keys = array_keys($picqer_cron_list);

            $key7 =  array_search($key6, $keys);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$picqer_sku_next_to_update.' updated successfully!</p>';

            if( $key7 == (count($keys) - 1) ){

                $next_item = 0;

                $next_key = $keys[$next_item];

            }else{

                $next_item = $key7 + 1;

                $next_key = $keys[$next_item];

            }

            // if empty
            if($picqer_cron_list[$next_key] == ''){
                // remove the empty item 
                unset($picqer_cron_list[$next_key]);
                // update option
                update_option('picqer_sku_next_to_update', '' );
                $resultHTML .= '<p class="text-center">Next to update product has been emptied!</p>';
            }else{
                // update option
                update_option('picqer_sku_next_to_update', $picqer_cron_list[$next_key] );
                $resultHTML .= '<p class="text-center">Next to update product SKU: '.($next_item + 1).' => '.$next_key.' =>  '.$picqer_cron_list[$next_key].'</p>';
            }



            // if product found ends here
            // if product not found starts here
        }else{

            $key6 = array_search($picqer_sku_next_to_update, $picqer_cron_list);

            $keys = array_keys($picqer_cron_list);

            $key7 =  array_search($key6, $keys);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$picqer_sku_next_to_update.' could not be found!</p>';

            $resultHTML .= '<p class="text-center">Please Manually import: Product Name  =>  '.$picqer_prod_name.' , Product SKU =>  '.$picqer_prod_sku.'</p>';

            if( $key7 >= (count($keys) - 1) ){
                $next_item = 0;
                $next_key = $keys[$next_item];
            }else{
                $next_item = $key7 + 1;
                if(array_key_exists($next_item, $keys)){
                    $next_key = $keys[$next_item];
                }else{
                    $next_item = 0;
                    $next_key = $keys[$next_item];
                }
            }

            // update option
            update_option('picqer_sku_next_to_update', $picqer_cron_list[$next_key] );

            // remove the unfound item 
            unset($picqer_cron_list[$key6]);

            // update the option
            update_option('picqer_cron_list', $picqer_cron_list);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$picqer_sku_next_to_update.' has been removed from the cron list!</p>';

            // get option value
            $picqer_cron_list = get_option('picqer_cron_list');

            if(count($picqer_cron_list) > 0){
                $resultHTML .= '<p class="text-center">Next to update product SKU: '.($next_item + 1).' => '.$next_key.' =>  '.$picqer_cron_list[$next_key].'</p>';
            }else{
                $resultHTML .= '<p class="text-center">Cron List Has Been Emptied!</p>';
            }
        }

}else{

    $resultHTML .= '<p class="text-center">Got no results from Picqer!</p>';

}

}
// end of finally

            }else{

                $resultHTML .= '<p class="text-center">No Picqer Products To Update!</p>';
    
            }

        }else{

            // clean the option
            update_option('picqer_sku_next_to_update', '' );

            $resultHTML .= '<p class="text-center">No Picqer Products Found!</p>';

        }

    }else{

        // clean the option
        update_option('picqer_sku_next_to_update', '' );

        $resultHTML .= '<p class="text-center">No Picqer Products Found!</p>';

    }

}else{

    // clean the option
    update_option('picqer_sku_next_to_update', '' );

    $resultHTML .= '<p class="text-center">No Picqer Products Found!</p>';

}


// return results
echo $resultHTML;