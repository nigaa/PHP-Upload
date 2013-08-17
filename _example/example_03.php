<?php

   /**
    *
    * example usage for PHP Upload Class
    * 
    * @author: Bartosz Gadomski <kontakt@bartoszgadomski.pl>
    * @date: 2013-08-17            
    * 
    */
    
    # error reporting
    ini_set( 'display_errors', true );
    error_reporting( E_ALL );
    
    # define message
    $message = ( object ) array( 'type' => '', 'content' => '' );
                
    # form send?
    if( isset( $_POST['form_send'] ) && $_POST['form_send'] == 'mhm' ) {
    
        # start having fun
        try {
        
            # require PHP Upload Class
            require_once( '../php-upload/php-upload.php' );

            # create object of PHP Upload Class
            $php_upload = new php_upload();
            
            # set language
            $php_upload -> set_language( 'pl' );
            
            # set the upload path
            $php_upload -> set_upload_path( dirname( __FILE__ ) .'/upload/' );
            
            # set unaccepted mimes
            $php_upload -> set_unaccepted_mimes( array( 'text/csv', 'text/html' ) );
            
            try {

                # insert file to class
                $php_upload -> set_new_file( $_FILES['first_file'] );
                
                # upload file
                $php_upload -> save_file();
                
                # return message
                $message -> content = 'Plik prawidłowo wgrany na serwer!';
                $message -> type = 'ok';
            }
            
            catch ( exception $e2 ) {
            
                # return exception
                $message -> content = $e2 -> getMessage();
                $message -> type = 'error';
            }
        }
        
        catch ( exception $e1 ) {
        
            # return exception
            $message -> content = $e1 -> getMessage();
            $message -> type = 'error';
        }
    }

   /**
    *
    * end of file.
    * 
    */                               

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    
    <title>PHP Upload Class</title>
    <meta charset="UTF-8" />
    
    <style type="text/css">
    
        * {
          margin: 0;
          padding: 0;
        }
        
        body {
          background: #34495e;
          font-family: 'Arial';
        }
        
        form {
          background: #fff;
          margin: 100px 200px 0 200px;
          padding: 40px;
          -webkit-border-radius: 4px;
          -moz-border-radius: 4px;
          border-radius: 4px;
        }
        
        form input[type=submit] {
          border: none;
          font-family: 'Arial';
          color: #fff;
          background: #1ABC9C;
          padding: 10px 20px;
          margin-top: 10px;
          -webkit-border-radius: 3px;
          -moz-border-radius: 3px;
          border-radius: 3px;
          cursor: pointer;
          -webkit-transition: background-color 0.2s linear;
          -moz-transition: background-color 0.2s linear;
          transition: background-color 0.2s linear;
        }
        
        form input[type=submit]:hover {
          background: #16A085;
        }
        
        .field_border {
          border: 1px solid #ddd;
          padding: 10px;
          -webkit-border-radius: 3px;
          -moz-border-radius: 3px;
          border-radius: 3px;
          margin-bottom: 10px;
          background: #fff;
        }
        
        .field_border:hover { border: 1px solid #ccc; }
        
        h1 {
          margin-bottom: 20px;
          color: #34495E;
        }
        
        footer {
          text-align: center;
          margin-top: 20px;
          font-size: 14px;
          color: #45627e;
        }
        
        footer a {
          color: #45627e;
          text-decoration: none;
          -webkit-transition: color 0.2s linear;
          -moz-transition: color 0.2s linear;
          transition: color 0.2s linear;
        }
        
        footer a:hover {
          color: #bdc3c7;
        }
        
        .message {
          color: #fff;
          font-size: 13px;
          padding: 10px;
          position: absolute;
          border-radius: 3px;
          margin-top: -45px;
          opacity: 0.95;
        }
        
        .message_arrow {
          position: absolute;
          transform: rotate(45deg);
          -webkit-transform: rotate(45deg);
          -moz-transform: rotate(45deg);
          -o-transform: rotate(45deg);
          background: #E74C3C;
          width: 8px;
          height: 8px;
          margin-top: 22px;
        }
        
        .message_error { background: #E74C3C; }
        .message_ok { background: #2ECC71; }
    
    </style>

</head>
<body>

    <form method="post" enctype="multipart/form-data">
    
        <h1>PHP Upload</h1>
        
        <?php echo ( $message -> content != '' ? '<div class="message message_'. $message -> type .'"><div class="message_arrow message_'. $message -> type .'"></div>'. $message -> content .'</div>' : '' ) ?>
        
        <div class="field_border"><input type="file" name="first_file" /></div>
        
        <input type="hidden" name="form_send" value="mhm" />
        <input type="submit" value="Upload!" />
    
    </form>
    
    <footer>2013 &copy; <a href="mailto:kontakt@bartoszgadomski.pl" target="_blank">Bartosz Gadomski</a></footer>

</body>
</html>
