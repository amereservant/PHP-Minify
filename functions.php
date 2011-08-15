<?php
/**
 * Get Human-readable File Size
 *
 * This formats the filesize into a human readable string and returns the corresponding
 * value.
 *
 * @link    http://snipplr.com/view/4633/convert-size-in-kb-mb-gb-/
 * @param   integer     $size   The file size in bytes to format.
 * @return  string              The formatted file size.
 */
function file_size($size)
{
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

/**
 * Check For Submitted Value
 *
 * This function plays multiple roles:
 *  - If $checked_selected is set to 'checked' or 'selected', it will return the
 *    checked or selected attribute if the value matches.
 *  - If $checked_selected is not set, it will return the input value if it has been
 *    set and isn't empty.
 *
 * @param   string  $key                The field's name attribute value to check
 * @param   string  $value              The value to check for if $checked_selected is set
 * @param   string  $checked_selected   Either 'checked'||'selected' to add the given attribute
 * @return  string                      The value or checked="checked"/selected="selected"
 */
function value_exists( $key, $value=null, $checked_selected=null )
{
    if( !isset($_POST[$key]) || strlen($_POST[$key]) < 1 ) return;
    
    if($checked_selected == 'checked' && $_POST[$key] == $value)
        echo ' checked="checked"';
    elseif( $checked_selected == 'selected' && $_POST[$key] == $value )
        echo ' selected="selected"';
    elseif(!is_null($checked_selected))
    {
        trigger_error('Invalid value set for $checked_selected parameter!', E_USER_WARNING);
        return false;
    }
    echo $_POST[$key];
}

/**
 * Minify Submitted Code
 *
 * This processes the submitted data by setting defaults for any missing parameters
 * and also checks that a non-empty string of code has been submitted before calling
 * the class to process the code.
 *
 * @param   void
 * @return  string  The compressed line of code if sucessful or an empty string if
 *                  compression was unsucessful.
 */
function minify_code()
{
    $defaults = array(
        'code'          => '',
        'line-break'    => 0,
        'type'          => 'js', // 'js' or 'css'
        'nomunge'       => false, //  Minify only, do not obfuscate
        'preserve-semi' => false, // Preserve unnecessary semicolons
        'disable-optimizations' => false, // Disable micro optimizations
    );
    
    $input = array();
    foreach($defaults as $k => $v)
    {
        $input[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
        
        // Make sure the submitted value is an integer or converted to one
        if($k != 'code' && $k != 'type')
            $input[$k] = is_bool($input[$k]) ? $input[$k] : (intval($input[$k]) == 0 ? false:true);
    }
    
    // Return if the code string is empty.
    if(strlen($input['code']) < 1) return false;
    
    $output['original_size']   = strlen($input['code']);
    if($input['type'] == 'js')
        $output['compressed']  = PHP_Minify::minifyJs($input['code'], $input);
    else
        $output['compressed']  = PHP_Minify::minifyCss($input['code'], $input);
    
    $output['compressed_size'] = strlen($output['compressed']);
    $output['difference']      = round( (
        ($output['original_size'] - $output['compressed_size']) / ($output['original_size']) )
        * 100, 2 );
    
    return $output;
}

/**
 * AJAX Response
 *
 * Provides a consistent response method to AJAX requests and will send a 200 header
 * for successful response or 500 header for an error, which triggers the error
 * method in jQuery ajax.
 *
 * @param   mixed   $data       The data to return in the AJAX request.
 * @param   string  $message    A message explaining the response, mainly used to describe
 *                              an error.
 * @param   bool    $error      Is the response an error response?
 * @return  void
 */
function ajax_response( $data, $message='', $error=false )
{
    header('HTTP/1.1 '. ($error ? '500 AJAX Request Error' : '200 OK'));
    
    echo json_encode(array('data' => $data, 'msg' => $message));
    exit();
}

/**
 * Process AJAX Request
 *
 * This processes an AJAX request and allows for compression to be handled via AJAX.
 *
 * @param   void
 * @return  void
 */
function ajax_process()
{
    error_reporting(0);
    // Check if required parameters are set
    if( !isset($_POST['code']) || !isset($_POST['type']) )
    {
        ajax_response('', 'Invalid AJAX request!  Missing required '. 
            (isset($_POST['type']) ? 'parameters.' : '`type`.  Please choose js or css.'), true);
    }
    // Check if the submitted code is longer than 0 chars and a valid type has been specified
    if( ($cl = strlen($_POST['code']) < 1) || ($_POST['type'] != 'js' && $_POST['type'] != 'css') )
    {
        if( $cl )
            ajax_response('', 'Code cannot be empty!', true);
        else
            ajax_response('', 'Please select an input type!', true);
    }
    // Minify code and check if the value returned false (meaning it failed)
    if( ($output = minify_code()) === false )
        ajax_response('', 'Compression failed.  Check input data.', true);
    
    // Convert filesizes to human readable
    $output['original_size']    = file_size($output['original_size']);
    $output['compressed_size']  = file_size($output['compressed_size']);
    // Send completed response
    ajax_response($output);
}

/* AJAX check - http://davidwalsh.name/detect-ajax  */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    ajax_process();
}

