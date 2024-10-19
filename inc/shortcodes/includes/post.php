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
  if( isset( $_POST["picqer_api_offset_number"]) && isset($_POST["picqer_filter_brands"]) ){

    // initializing
    $picqer_api_base_url = '';
    $picqer_api_username = '';
    $picqer_api_password = '';
    $picqer_api_language = '';

    $picqer_api_offset_number = 0;

    $resultHTML = '';

    // to get the options values
    require_once '../../../../../../wp-config.php';

      // assigning
    $picqer_api_offset_number = picqer_secure_input( $_POST["picqer_api_offset_number"] );
    $picqer_filter_brands = picqer_secure_input( $_POST["picqer_filter_brands"] );


    // assigning values got from wp options
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


    // picqer API Queries
    require_once('PicqerApiQueries.php');

    // instantiating
    $ApiQuery = new PicqerApiQueries;

      try {
      // sending all products API request to picqer
      $picqer_api_all_products = $ApiQuery->picqer_api_all_products($picqer_api_base_url, $picqer_api_username, $picqer_api_password, $picqer_filter_brands, $picqer_api_offset_number);
      } catch (PDOException $e) {
        $resultHTML .= "Error: " . $e->getMessage();
      }finally{
        $picqer_api_all_products = json_decode($picqer_api_all_products, true);
      }
      // if a valid response
      if( isset($picqer_api_all_products) && count($picqer_api_all_products) > 0 ){

      $all_picqer_product_items = $picqer_api_all_products;
      $all_picqer_product_ids = [];
      $all_picqer_brands = [];
      $all_picqer_model_names = [];
      foreach( $all_picqer_product_items as $item_key => $single_product_item ){
        array_push($all_picqer_product_ids, $single_product_item["idproduct"]);
        foreach( $single_product_item["productfields"] as $productfield_key => $productfield ){
          if( $productfield["title"] == "Merk" || $productfield["title"] == "merk" || $productfield["title"] == "Brand" || $productfield["title"] == "brand" ){
            array_push($all_picqer_brands, $productfield["value"]);
          }
          if( $productfield["title"] == "Model" || $productfield["title"] == "model" ){
            array_push($all_picqer_model_names, $productfield["value"]);
          }

        }
      }

      $resultHTML .= '<p class="text-center">Click any items below to import: (Green ones are already imported!)</p>';
      $resultHTML .= '<style>
          .picqer_clickable_product_ids{
              cursor: pointer;
          }
          .picqer_product_ids{
              margin-right: 10px;
              display: inline-block;
          }
          .picqer_product_ids:last-child{
              margin-right: 0;
          }
      </style>';


      // import all button 
      $resultHTML .= '<div class="selected_button_section">';
      $resultHTML .= '<p class="total_iteration text-center"></p>';
      $resultHTML .= '<button type="submit" id="woocommerce_picqer_api_import_all_button" class="woocommerce_picqer_api_import_all_button btn btn-warning" name="woocommerce_picqer_api_import_all_button">Import All Selected</button>';
      $resultHTML .= '</div>';


      $resultHTML .= '<div class="table-responsive p-3">';
      $resultHTML .= '<table class="table app-table-hover mb-0 text-left">';
      $resultHTML .= '<thead>';
      $resultHTML .= '<tr>
          <th><input type="checkbox" id="select_all"></th>
          <th>Product ID</th>
          <th>Brand Name</th>
          <th>Model Name</th>
      </tr>';
      $resultHTML .= '</thead>';
      $resultHTML .= '<tbody>';

      // get all available products in WC
      $picqer_products_sku_list = get_option('picqer_products_sku_list');
      $total_iteration = 0;
      foreach( $all_picqer_product_ids as $single_id_key => $single_product_id ){
        if (in_array($single_product_id, $picqer_products_sku_list)){
          // $resultHTML .= '<span class="text-success picqer_product_ids">';
          // $resultHTML .= $single_product_id;
          // $resultHTML .= ' ('.$all_picqer_brands[$single_id_key].')';
          // $resultHTML .= ' ('.$all_picqer_model_names[$single_id_key].')';
          // $resultHTML .= '</span>';
          $resultHTML .= '<tr class="text-success">
            <td class="text-success"><input type="checkbox" name="select_row[]" class="select_row" value="'.$single_product_id.'"></td>
            <td class="text-success">'.$single_product_id.'</td>
            <td class="text-success">'.$all_picqer_brands[$single_id_key].'</td>
            <td class="text-success">'.$all_picqer_model_names[$single_id_key].'</td>
          </tr>';
          $total_iteration++;
        }else{
          // $resultHTML .= '<span class="text-danger picqer_product_ids picqer_clickable_product_ids" data-value="'.$single_product_id.'">';
          // $resultHTML .= $single_product_id;
          // $resultHTML .= ' ('.$all_picqer_brands[$single_id_key].')';
          // $resultHTML .= ' ('.$all_picqer_model_names[$single_id_key].')';
          // $resultHTML .= '</span>';
          $resultHTML .= '<tr class="text-danger picqer_clickable_product_ids" data-value="'.$single_product_id.'">
            <td class="text-danger"><input type="checkbox" name="select_row[]" class="select_row" value="'.$single_product_id.'"></td>
            <td class="text-danger">'.$single_product_id.'</td>
            <td class="text-danger">'.$all_picqer_brands[$single_id_key].'</td>
            <td class="text-danger">'.$all_picqer_model_names[$single_id_key].'</td>
          </tr>';
          $total_iteration++;
        }
      }

      $resultHTML .= '</tbody>';
      $resultHTML .= '</table>';
      $resultHTML .= '</div>';
      // end of table


      $resultHTML .= '
      <script>

        $(".total_iteration").html(
          "Showing total '.$total_iteration.' Products"
        );

        $(".picqer_clickable_product_ids").click(function(){
          $("#woocommerce_picqer_api_product_id_field").val($(this).data("value"));
        });

        $(".woocommerce_picqer_api_import_all_button").click(async function(event){
          event.preventDefault();
          let selected_rows = $(".select_row:checked");
          if( selected_rows.length <= 0 ){
            alert("Please select at least one row!");
            return;
          }
          let all_picqer_product_ids = [];
          for (let i = 0; i < selected_rows.length; i++) {
            all_picqer_product_ids.push(selected_rows[i].value);
          }
          let picqer_api_product_id;
          let post_url = "' . WOOCOMMERCE_PICQER_API_PLUGIN_URL . 'inc/shortcodes/includes/post.php";
          let current_url = $(location).attr("href");
          let picqer_submit_all_ids_result_button_text = $("#picqer_submit_all_ids_result").html();
          let woocommerce_picqer_api_submit_button_text = $("#woocommerce_picqer_api_submit_button").html();
          $("#picqer_submit_all_ids_result").attr("disabled", true);
          $("#picqer_submit_all_ids_result").html("Importing...");
          $("#woocommerce_picqer_api_submit_button").attr("disabled", true);
          $("#woocommerce_picqer_api_submit_button").html("Importing...");
          $("#result").html("<h6>Please do not refresh or close this window while importing...</h6>");
          $("#result h6").addClass("text-center text-danger");
          
          for (let index = 0; index < all_picqer_product_ids.length; index++) {
            picqer_api_product_id = all_picqer_product_ids[index];
            await $.ajax({
                type: "POST",
                url: post_url,
                data: {picqer_api_product_id, current_url}, 
                success: function(result){
                    $("#result").html(result);
                    if(index < all_picqer_product_ids.length){
                      $("#picqer_submit_all_ids_result").attr("disabled", true);
                      $("#picqer_submit_all_ids_result").html("Importing...");
                      $("#woocommerce_picqer_api_submit_button").attr("disabled", true);
                      $("#woocommerce_picqer_api_submit_button").html("Importing...");
                      $("#result").prepend("<h6>"+(index+1)+" Products Have been Imported or Updated So Far...</h6>");
                      $("#result").prepend("<h6>Please do not refresh or close this window while importing...</h6>");
                      $("#result h6").addClass("text-center text-danger");
                    }
                }
            });
          }

          $("#result").prepend("<h6>All The Selected "+all_picqer_product_ids.length+" Products Have Been Successfully Imported or Updated!</h6>");
          $("#result h6").addClass("text-center text-success");
          $("#picqer_submit_all_ids_result").attr("disabled", false);
          $("#picqer_submit_all_ids_result").html(picqer_submit_all_ids_result_button_text);
          $("#woocommerce_picqer_api_submit_button").attr("disabled", false);
          $("#woocommerce_picqer_api_submit_button").html(woocommerce_picqer_api_submit_button_text);

        });


        // checked function
        $("#select_all").on("change", function() {
            let isChecked = this.checked;

            // Select or deselect all checkboxes
            $(".select_row").each(function() {
                $(this).prop("checked", isChecked);
            });

            // Optionally, you can send an AJAX request to process the selected rows
            let selectedIds = [];
            if (isChecked) {
                $(".select_row").each(function() {
                    selectedIds.push($(this).val()); // Collect all user IDs
                });
            }
        });


      </script>
      ';

      }else{
        $resultHTML .= '<p class="text-center">No Product IDs found!</p>';
      }

    echo $resultHTML;

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
    $wc_prod_tags = 'picqer, eccommerce';

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



    // open ai option values
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key')){
      $open_ai_api_key = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_api_key');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model')){
      $open_ai_model = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_model');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature')){
      $open_ai_temperature = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_temperature');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens')){
      $open_ai_max_tokens = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_max_tokens');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty')){
      $open_ai_frequency_penalty = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_frequency_penalty');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty')){
      $open_ai_presence_penalty = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_presence_penalty');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off')){
      $custom_tags_on_off = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_custom_tags_on_off');
    }
    if(get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off')){
      $open_ai_on_off = get_option( WOOCOMMERCE_PICQER_API_PLUGIN_NAME . '_open_ai_on_off');
    }


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

        // return upon no stock 
        if( isset( $picqer_api_single_product["free_stock"] ) ){
          if( $picqer_api_single_product["free_stock"] <= 0 ){
            $resultHTML .= '<p class="text-center text-danger">Sorry, This product is out of stock.</p>';
            return $resultHTML;
          }
        }else{
          $resultHTML .= '<p class="text-center text-danger">Sorry, This product has no stock information.</p>';
          return $resultHTML;
        }
        // return upon no stock ends

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
          

          if(isset($picqer_api_single_product["barcode"])){
            if( $picqer_api_single_product["barcode"] == "" ){
              $picqer_barcode = substr(str_shuffle("0123456789"), 0, 13);
            }else{
              $picqer_barcode = $picqer_api_single_product["barcode"];
            }
          }else{
            $picqer_barcode = substr(str_shuffle("0123456789"), 0, 13);
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
            $wc_prod_tags = 'picqer, ' . $picqer_prod_brand;
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


          // updating product model name 
          if(isset($picqer_api_single_product["model_name"])){
            if( $picqer_api_single_product["model_name"] == ''){
              $picqer_model_name = $picqer_prod_name;
            }else{
              $picqer_model_name = $picqer_api_single_product["model_name"];
            }
          }else{
            $picqer_model_name = $picqer_prod_name;
          }
          // updating product model name ends



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
      $open_ai_request_response = json_decode($open_ai_request_response, true);
      if(isset($open_ai_request_response['error'])){
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product description...</p>';
        $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
      }else{
        if(isset($open_ai_request_response["choices"][0]["text"])){
          $resultText = $open_ai_request_response["choices"][0]["text"];
          $picqer_prod_desc = str_replace('"', '', $resultText);
          $resultHTML .= '<p class="text-center">OpenAI API has updated the product description...</p>';
        }else{
          $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product description. Please contact the developer.</p>';
        }
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
      $open_ai_request_response = json_decode($open_ai_request_response, true);
      if(isset($open_ai_request_response['error'])){
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product short-description...</p>';
        $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
      }else{
        if(isset($open_ai_request_response["choices"][0]["text"])){
          $resultText = $open_ai_request_response["choices"][0]["text"];
          $picqer_prod_short_desc = str_replace('"', '', $resultText);
          $resultHTML .= '<p class="text-center">OpenAI API has updated the product short-description...</p>';
        }else{
          $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product short-description. Please contact the developer.</p>';
        }
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
        $open_ai_request_response = json_decode($open_ai_request_response, true);
        if(isset($open_ai_request_response['error'])){
          $resultHTML .= '<p class="text-center">OpenAI API could not update the product tags...</p>';
          $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
        }else{
          if(isset($open_ai_request_response["choices"][0]["text"])){
            $resultText = $open_ai_request_response["choices"][0]["text"];
            $wc_prod_tags = picqer_secure_input($resultText);
            $resultHTML .= '<p class="text-center">OpenAI API has updated the product tags...</p>';
          }else{
            $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product tags. Please contact the developer.</p>';
          }
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





            try {

              // getting all WC attributes
              $wc_all_attributes = $woocommerce->get('products/attributes');

            }catch (PDOException $e) {

              $resultHTML .= "Error: " . $e->getMessage();

            }finally{

              // creating all attributes array
              $wc_all_attributes_array = [];

              if(count($wc_all_attributes) != 0){

                foreach($wc_all_attributes as $id => $single_attribute){

                  $wc_all_attributes_array[$single_attribute->id] = $single_attribute->name;

                }

              }


              // loop through all attributes & create if not exists
              foreach($picqer_atrributes as $key => $single_attribute){

                if(count($wc_all_attributes_array) != 0){
                  
                  if(!in_array($single_attribute, $wc_all_attributes_array)){

                    $data = [
                        'name' => $single_attribute,
                        'slug' => str_replace(' ', '_', $single_attribute),
                        'type' => 'select',
                        'order_by' => 'menu_order',
                        'has_archives' => true
                    ];

                    try {

                      $wc_create_attribute = $woocommerce->post('products/attributes', $data);

                    } catch (PDOException $e) {

                      $resultHTML .= "Error: " . $e->getMessage();
              
                    }finally{

                      $resultHTML .= '<p class="text-center">Attribute '.($key + 1).' ('.$single_attribute.') created successfully!</p>';

                    }
                    
                  }else{
                    $resultHTML .= '<p class="text-center">Attribute '.($key + 1).' ('.$single_attribute.') already exists!</p>';
                  }

                }else{

                $data = [
                    'name' => $single_attribute,
                    'slug' => str_replace(' ', '_', $single_attribute),
                    'type' => 'select',
                    'order_by' => 'menu_order',
                    'has_archives' => true
                ];

                try {

                  $wc_create_attribute = $woocommerce->post('products/attributes', $data);

                } catch (PDOException $e) {

                  $resultHTML .= "Error: " . $e->getMessage();
          
                }finally{

                  $resultHTML .= '<p class="text-center">Attribute '.($key + 1).' ('.$single_attribute.') created successfully!</p>';

                }

              }

            }
            }
            // create attribute ends here

      
            try {
              // getting all attributes again
              $wc_all_attributes = $woocommerce->get('products/attributes');
            }catch (PDOException $e) {
              $resultHTML .= "Error: " . $e->getMessage();
            }finally{
              // creating avilable attributes array
              $wc_all_attributes_array = [];
              if(count($wc_all_attributes) != 0){
                foreach($wc_all_attributes as $id => $single_attribute){
                  $wc_all_attributes_array[$single_attribute->id] = $single_attribute->name;
                }
              }
            }


  


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



          // creating product data
          $data = [
            'name' => $picqer_model_name,
            'type' => 'variable',
            'description' => $picqer_prod_desc,
            'short_description' => $picqer_prod_short_desc,
            'sku' => strval($picqer_barcode),
            'categories' => [
                [
                    'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                ],
                [
                    'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                ],
            ],
            // 'images' => $updated_images_array,
            'tags'  => $tags_array,
            'meta_data' =>  $product_meta_data_array,
          ];
                


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


            }else{

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

                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$picqer_model_name.') created successfully!</p>';


                }else{
                  $resultHTML .= '<p class="text-center">Product ('.$picqer_model_name.') could not be imported!</p>';
                }
              // product not created ends here


              // initializing
              $updated_images_array = [];
              $missed_images_array = [];

              // adding images
              foreach($picqer_all_image_src_array as $image_key => $single_picqer_image){
                try {
                  $image_id = woocommerce_picqer_api_custom_image_file_upload( $single_picqer_image['src'], $single_picqer_image['name'], $wc_product_id );
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
                'images' => $updated_images_array
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

          }
          // end of if-else
          // creating product ends here




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
                    'tags'  => $tags_array,
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
                    $image_id = woocommerce_picqer_api_custom_image_file_upload( $picqer_all_image_src_array[0]['src'], $picqer_all_image_src_array[0]['name'], $wc_product_id );

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



    }else{
      $resultHTML .= '<p class="text-center">Got no product for importing!</p>';
    }

    }
    // try catch ends here 

  return $resultHTML;



}