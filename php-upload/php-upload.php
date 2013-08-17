<?php

   /**
    *
    * PHP Upload Class
    * 
    * @author: Bartosz Gadomski <kontakt@bartoszgadomski.pl>
    * @date: 2013-08-17
    * @version: 0.1
    * 
    */
    
    class php_upload {
    
       /**
        *
        * upload path
        * 
        * @var string
        * @since 0.1                        
        * 
        */     
        
        private $upload_path = '';
        
       /**
        *
        * accepted mimes
        * 
        * @var array
        * @since 0.1                        
        * 
        */
        
        private $accepted_mimes = array( '*' );
       
       /**
        *
        * unaccepted mimes
        * 
        * @var array
        * @since 0.1                         
        * 
        */                               
       
        private $unaccepted_mimes = array();
        
       /**
        *
        * uploaded file data array
        * 
        * @var array
        * @since 0.1
        * 
        */
        
        private $uploaded_file = array();
        
       /**
        *
        * is file or not?
        * 
        * @var bool
        * @since 0.1
        * 
        */
        
        private $is_file = false;                                                                                                                                              
        
       /**
        *
        * size types and their factors
        * 
        * @var array  
        * @since 0.1                      
        * 
        */
        
        private $types = array( 'b'  => 1, # bytes
                                'kb' => 1024, # kilobytes
                                'mb' => 1048576, # megabytes
                                'gb' => 1073741824 ); # gigabytes                               
       
       /**
        *
        * max uploaded file size
        * "0" means "no limit"        
        * 
        * @var integer
        * @since 0.1
        * 
        */
        
        private $max_filesize = 0;
        
       /**
        *
        * language prefix
        * 
        * @var string
        * @since 0.1
        * 
        */
        
        private $language_prefix = 'default';
        
       /**
        *
        * language data
        * 
        * @var array
        * @since 0.1
        * 
        */
        
        private $language_data = array();                                                                                                                
       
       /**
        *
        * set language
        * 
        * @param string lang
        * @since 0.1                        
        * 
        */
        
        public function set_language( $lang ) {
        
            # language pack path
            $path = dirname( __FILE__ ) .'/languages/lang-'. $lang .'.php';
            
            # language pack not exist
            if( !file_exists( $path ) ) throw new exception( 'There is not language pack for "'. $lang .'"' );
            else {
                
                # safe language prefix
                $this -> language_prefix = $lang;
                
                # load language pack
                require_once( $path );
                
                # save language pack
                if( !isset( $_LANGUAGE ) || !is_array( $_LANGUAGE ) ) throw new exception( 'There is not language pack for "'. $lang .'"' );
                else {
                
                    $this -> language_data = $_LANGUAGE;
                    return true;
                }
            }
        }     
        
       /**
        *
        * translate messages to chosen language
        * 
        * @param string message
        * @return string        
        * @since 0.1                        
        * 
        */   
        
        private function translate( $message ) {
        
            return( $this -> is_message_translated( $message ) ? $this -> language_data[$message] : $message );
        }   
        
       /**
        *
        * check is message translated in chosen language
        * 
        * @param string message
        * @return bool        
        * @since 0.1                        
        * 
        */ 
        
        private function is_message_translated( $message ) {
        
            return( $this -> language_prefix != 'default' && isset( $this -> language_data[$message] ) && $this -> language_data[$message] != '' );
        }                                                  
       
       /**
        *
        * set the upload path
        * 
        * @param string path 
        * @return bool
        * @since 0.1        
        *                       
        */
        
        public function set_upload_path( $path ) {

            # path validate
            if( $this -> is_upload_path_valid( $path ) ) {
            
                # set the upload path to global
                $this -> upload_path = $path;
                
                # end
                return true;
            }
            
            # exception
            else throw new exception( $this -> translate( 'The upload path is invalid' ) );
        }
        
       /**
        *
        * get the upload path
        * 
        * @return false or upload_path
        * @since 0.1                       
        * 
        */    
        
        public function get_upload_path() {
        
            # return the upload path
            return( $this -> upload_path );
        }     
        
       /**
        *
        * validate upload path
        * 
        * @param string path
        * @return bool        
        * @since 0.1               
        * 
        */
        
        public function is_upload_path_valid( $path ) {

            # check for directory
            if( !is_dir( $path ) ) return false;
            
            # check for writable
            if( !is_writable( $path ) ) return false;

            # all is ok
            return true;
        }   
        
       /**
        *
        * accepted mime types
        * 
        * @param array mimes
        * @since 0.1                       
        * 
        */
        
        public function set_accepted_mimes( array $mimes ) {
        
            # save accepted mime types
            $this -> accepted_mimes = $mimes;
            
            # clear unaccepted mime types
            $this -> unaccepted_mimes = array();
        }            
        
       /**
        *
        * accepted mime types
        * 
        * @param array mimes
        * @since 0.1                        
        * 
        */
        
        public function set_unaccepted_mimes( array $mimes ) {
        
            # save unaccepted mime types
            $this -> unaccepted_mimes = $mimes;
            
            # all other mime types are accepted
            $this -> accepted_mimes = array();
        } 
        
       /**
        *
        * set file data to class
        * 
        * @param array file
        * @return bool        
        * @since 0.1                        
        * 
        */ 
        
        public function set_new_file( array $file ) {

            # check file data
            if( $this -> is_uploaded_file_ok( $file ) ) {

                # set file data
                $this -> uploaded_file = $file;
                $this -> is_file = true;
                
                # exit
                return true;
            }
            
            else throw new exception( $this -> translate( 'Uploaded file is not valid' ) );
        }   
        
       /**
        *
        * check uploaded file
        * 
        * @param array file
        * @return bool        
        * @since 0.1                       
        * 
        */

        public function is_uploaded_file_ok( array $file ) {
        
            # are uploaded files?
            if( $file == array() ) throw new exception( $this -> translate( 'There is no uploaded file' ) );
            else {
            
                # check file data structure
                if( !$this -> has_file_correct_structure( $file ) ) throw new exception( $this -> translate( 'The file data are incorrect' ) );
                else {

                    # check for upload errors
                    if( $file['error'] != '0' ) throw new exception( $this -> translate( 'Upload error' ) .' - '. $this -> read_upload_error( $file['error'] ) );
                    else {
                    
                        # check for mime type
                        if( !$this -> is_mime_type_ok( $file['type'] ) ) throw new exception( $this -> translate( 'The mime type is forbidden' ) );
                        else {
                        
                            # check for file size
                            if( !$this -> is_file_size_ok( $file['size'] ) ) throw new exception( $this -> translate( 'The file is too big' ) );
                            else {
                            
                                # ok
                                return true;
                            }
                        }
                    }
                }
            }
        } 
        
       /**
        *
        * check file array structure 
        * 
        * @param array file
        * @return bool        
        * @since 0.1
        * 
        */
        
        private function has_file_correct_structure( array $file ) {

            # check array structure
            $correct_structure = array( 'name', 'type', 'tmp_name', 'error', 'size' );
            
            # check array count
            if( count( $file ) != count( $correct_structure ) ) return false;

            # check array keys
            $pos = 0;
            foreach( $file as $key => $value ) {

                if( $key != $correct_structure[$pos] ) return false;
                $pos++;
            }
            
            # ok!
            return true;
        }    
        
       /**
        *
        * read upload error
        * 
        * @param error_no
        * @return string - Error name
        * @since 0.1
        * 
        */
        
        public function read_upload_error( $error_no ) {
        
            # read upload errors - from number to string
            switch( $error_no ) {
            
                case '1': return( $this -> translate( 'The uploaded file exceeds the upload_max_filesize directive in php.ini' ) );
                case '2': return( $this -> translate( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form' ) );
                case '3': return( $this -> translate( 'The uploaded file was only partially uploaded' ) );
                case '4': return( $this -> translate( 'No file was uploaded' ) );
                case '6': return( $this -> translate( 'Missing a temporary folder' ) );
                case '7': return( $this -> translate( 'Failed to write file to disk' ) );
                case '8': return( $this -> translate( 'A PHP extension stopped the file upload' ) );
            }
        } 
        
       /**
        *
        * check for mime type
        * 
        * @param string mime
        * @return bool
        * @since 0.1                                
        * 
        */
        
        public function is_mime_type_ok( $mime ) {
        
            # check accepted mimes
            if( $this -> accepted_mimes != array() ) {

                # all mimes accepted
                if( $this -> accepted_mimes[0] == '*' ) return true;
                else {
                
                    # looking for our mime type
                    return( in_array( $mime, $this -> accepted_mimes ) ? true : false );
                }
            }
            
            # check unaccepted mimes
            else {

                # looking for our mime type
                return( in_array( $mime, $this -> unaccepted_mimes ) ? false : true );
            }
        }                               
        
       /**
        *
        * set max file size
        * 
        * @param integer size - uploaded file size
        * @param string type - type from types array
        * @since 0.1                                
        * 
        */
        
        public function set_max_filesize( $size, $type = 'b' ) {

            # is type ok?
            if( !array_key_exists( $type, $this -> types ) ) throw new exception( $this -> translate( 'Unknown size type' ) );
            else {
            
                # max file size in bytes
                $size_in_bytes = $size * $this -> types[$type];
                
                # save
                $this -> max_filesize = $size_in_bytes;
            }
        }     
        
       /**
        *
        * get max file size
        * 
        * @param string type - type from types array
        * @since 0.1                
        * 
        */
        
        public function get_max_filesize( $type = 'b' ) {
        
            # is type ok?
            if( !array_key_exists( $type, $this -> types ) ) throw new exception( $this -> translate( 'Unknown size type' ) );
            else {
            
                # get max file type
                $size = $this -> max_filesize / $this -> types[$type];

                # return
                return( $size );
            }
        }       
        
       /**
        *
        * is the file size ok (less than max_filesize)?
        * 
        * @param integer size - uploaded file size
        * @since 0.1                        
        * 
        */
        
        public function is_file_size_ok( $size ) {
        
            # no limited file size?
            if( $this -> max_filesize === 0 ) return true;
            else {
            
                # compare
                return( $this -> max_filesize > $size ? true : false );
            }
        }   
        
       /**
        *
        * set uploaded file name
        * 
        * @param string filename
        * @return bool
        * @since 0.1
        * 
        */
        
        public function set_filename( $filename ) {
        
            # is file?
            if( $this -> is_file == false ) throw new exception( $this -> translate( 'Can not change file name - there is no uploaded file' ) );
            else {
            
                # change filename
                $this -> uploaded_file['name'] = trim( $filename );
            }
        } 
        
       /**
        *
        * get uploaded file name

        * @return string filename
        * @since 0.1
        * 
        */
        
        public function get_filename() {
        
            # return
            return( $this -> is_file == false ? false : $this -> uploaded_file['name'] );
        }                                                                
       
       /**
        *
        * save uploaded file in upload folder
        * 
        * @return bool
        * @since 0.1                        
        * 
        */
        
        public function save_file() {
                                           
            # is file?
            if( $this -> is_file == false ) throw new exception( $this -> translate( 'There is no file to upload' ) );
            else {
            
                # move uploaded_file
                if( move_uploaded_file( $this -> uploaded_file['tmp_name'], $this -> upload_path . $this -> uploaded_file['name'] ) ) return true;
                else throw new exception( $this -> translate( 'Can not move uploaded file - please try again later' ) );
            }
        }                                                                                                               
       
       /**
        *
        * clear uploaded file data
        * 
        * @since 0.1
        * 
        */   
        
        public function clear_file_data() {
            
            # clear upload data
            $this -> uploaded_file = array();
        }                                                                                                                                                               
                       
       /**
        *
        * end of methods.
        * 
        */                               
    }
    
   /**
    *
    * end of class.
    * 
    */                                               

?>
