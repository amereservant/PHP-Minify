<?php
/**
 * PHP-Minify - YUI Compressor
 * 
 * A PHP interface for the YUI Compressor.
 * This provides the ability to minify CSS and Javascript via PHP and execute the YUI
 * compressor via PHP's exec() function.
 * This class is a slightly modified version of Minify YUICompressor class originally found at
 * {@link http://code.google.com/p/minify/source/browse/trunk/min/lib/Minify/YUICompressor.php}
 * written by Stephen Clay made to better accomodate Ubuntu installs of YUI Compressor.
 *
 * If the original version is desired instead, such as the need to to call the jar command,
 * then it can easily be swapped out without breaking the code since I stuck to the
 * author's way of executing the code with the exception being the need to specify the
 * $jarFile property and $tempDir property in the original class.
 * 
 * <code>
 *  $compressed_code = PHP_Minify::minifyJs( $code, 
 *      array('nomunge' => true, 'line-break' => 1000)
 *  );
 * </code>
 * 
 * @package PHP-Minify
 * @author  Stephen Clay    <steve@mrclay.org>
 * @author  David Miles     <amereservant@gmail.com>
 */
class PHP_Minify
{
   /**
    * Temp Directory (MUST be writable!)
    * 
    * This must be set before calling minifyJs() || minifyCss() if system temp directory
    * default isn't desired.
    *
    * @var      string
    * @access   public
    */
    public static $temp_dir = null;
    
   /**
    * Minify a Javascript string
    *
    * Minifies a javascript string and returns the minified string.
    * Valid option parameters are:
    *   ['charset']                 = ''
    *   ['line-break']              = {integer}
    *   ['nomunge']                 = false | true
    *   ['preserve-semi']           = false | true
    *   ['disable-optimizations']   = false | true
    * 
    * @see      http://www.julienlecomte.net/yuicompressor/README
    * @param    string  $js         String containing javascript code to compress.
    * @param    array   $options    (optional) An array of compression options.
    * @return   string              Compressed javascript code.
    * @access   public
    */
    public static function minifyJs( $js, $options=array() )
    {
        return self::_minify('js', $js, $options);
    }
    
   /**
    * Minify a CSS string
    *
    * Valid option parameters are:
    *   ['charset']                 = ''
    *   ['line-break']              = {integer}
    * 
    * @see      http://www.julienlecomte.net/yuicompressor/README
    * @param    string  $css        The CSS code to compress
    * @param    array   $options    (No options currently used for CSS comrpession)
    * @return   string              Compressed CSS code
    * @access   public
    */
    public static function minifyCss( $css, $options=array() )
    {
        return self::_minify('css', $css, $options);
    }
   
   /**
    * Minify Code
    *
    * This gets called by the other methods in this class to minify the code.
    * It does all of the 'heavy-lifting'.
    *
    * @param    string  $type       The type to process the given code as: 'js' for Javascript, 'css' for CSS
    * @param    string  $code       The css or js code to minify
    * @param    array   $options    An array of options to apply before calling YUI.
    * @return   string              Minified css or js code
    * @access   private
    */
    private static function _minify( $type, $code, $options )
    {
        if( is_null(self::$temp_dir) ) self::$temp_dir = sys_get_temp_dir();
        
        if( !( $tmp_file = tempnam(self::$temp_dir, 'yuic_') ) )
        {
            throw new Exception('Minify_YUICompressor : could not create temp file.');
        }
        // Check to make sure the temp file can be written
        self::_prepare( $tmp_file );
        
        // Write the input data in a temporary file
        file_put_contents($tmp_file, $code);
        
        // Shell execute the YUI Compressor on the server
        exec(self::_getCmd($options, $type, $tmp_file), $output);
        
        // Delete the temporary file
        unlink($tmp_file);
        
        return implode("\n", $output);
    }
   
   /**
    * Get Execution Command
    *
    * This takes the given options, file type, and the temporary file containing the
    * submitted data.
    *
    * @param    array   $user_options   The YUI options to apply
    * @param    string  $type           'js' or 'css' for the type of data being compressed
    * @param    string  $temp_file      The name/path of the temporary file containing the
    *                                   submitted code to compress
    * @return   string                  The compression command to execute
    * @access   private
    */
    private static function _getCmd( $user_options, $type, $temp_file )
    {
        $o = array_merge( array(
            'charset'           => '',
            'line-break'        => 5000,
            'type'              => $type,
            'nomunge'           => false,
            'preserve-semi'     => false,
            'disable-optimizations' => false
            ), $user_options
        );
        
        $cmd = "yui-compressor" .
               " --type '{$type}'" .
               (strlen($o['charset']) > 1 ? " --charset {$o['charset']}" : '') .
               (is_numeric($o['line-break']) && $o['line-break'] >= 0 ? ' --line-break '. 
                    (int)$o['line-break'] :'');
        
        if($type === 'js')
        {
            foreach (array('nomunge', 'preserve-semi', 'disable-optimizations') as $opt)
            {
                $cmd .= $o[$opt] ? " --{$opt}" : '';
            }
        }
        return $cmd . ' ' . escapeshellarg($temp_file); // .' 2>&1';
    }
    
   /**
    * Perform Checks Before Execution
    *
    * This is called just before the shell command is executed to do any checks necessary
    * to make sure everything is good to go and if not, it will throw an Exception that
    * is uncaught so script execution is haulted.
    *
    * Currently it only checks if the temporary directory specified is writable.
    *
    * @param    void
    * @return   void
    * @access   private
    */
    private static function _prepare()
    {
        if( !is_writable(self::$temp_dir) )
        {
            throw new Exception('Minify_YUICompressor : $temp_dir must be set and writable.');
        }
    }
}

