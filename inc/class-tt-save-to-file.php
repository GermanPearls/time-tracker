<?php
/**
 * Class Create File
 *
 * Get projects from db and create table to display on front end
 * 
 * @since 2.2.0
 * @since 3.0.13 Updated to allow for specifying delimiter.
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Tracker_Export_To_File_And_Download' ) ) {


    /**
     * Class
     * 
     * @since 2.2.0
     */
    class Time_Tracker_Export_To_File_And_Download
    {


        /**
         * Class Variables
         * 
         * @since 2.2.0
         * @since 3.0.13 Added file delimiter.
         **/
        private $file_name;
        private $file_extension;
        private $file_path;
        private $file_content;
        private $file_delimiter;
        private $full_file_name_path;
        private $file_created;


        /**
         * Constructor
         * 
         * @since 2.2.0
         * 
         * @param string $fpath File path.
         * @param string $fname File name.
         * @param string $fext File extension.
         * @param string $fcontent File content.
         * @param string optional $fdelim File delimiter.
         **/
        public function __construct($fpath, $fname, $fext, $fcontent, $fdelim=",") {
            $this->set_name($fname);
            $this->set_file_extension($fext);
            $this->set_path($fpath);
            $this->set_content($fcontent);
            $this->set_delimiter($fdelim);
            $this->file_created['fname'] = "";
            $this->file_created['fcontent'] = "";
        }


        /**
         * Set File Name
         * 
         * @since 2.2.0
         * 
         * @param string $fname File name.
        **/
       private function set_name($fname) {
           $this->file_name = $fname; 
       }


        /**
         * Set File Delimiter
         * 
         * @since 3.0.13
         * 
         * @param string $fdelim File delimiter.
         **/
        private function set_delimiter($fdelim) {
            if ($fdelim == "" || is_null($fdelim)) {
                $this->file_delimiter = ",";   
            } elseif (strtolower($fdelim) == "tab") {
                $this->file_delimiter = "\t"; 
            } else {
                $this->file_delimiter = ",";  
            }
        }

        
        /**
         * Set File Extension, with preceeding period
         * 
         * @since 2.2.0
         * 
         * @param string $fext File extension.
         **/
        private function set_file_extension($fext) {
            if (substr(trim($fext), 0,1) == ".") {
                $this->file_extension = $fext;
            } else {
                $this->file_extension = "." . $fext;
            }
        }


        /**
         * Set File Path - with trailing slash
         * 
         * @since 2.2.0
         * 
         * @param string $fpath File path.
         **/
        private function set_path($fpath) {
            $fpath = $this->verify_trailing_slash($fpath);
            if ($this->verify_path_exists($fpath)) {
                $this->file_path = $fpath;
            } else {
                //error! couldn't create path
                log_tt_misc('Export to ' . $this->file_name . ' failed. Path (' . $this->file_path . ') does not exist and could not be created.');
            }
        }


        /**
         * Verify trailing slash
         * 
         * @since 2.2.0
         * 
         * @param string $path File path.
         * 
         * @return string File path.
         **/
        private function verify_trailing_slash($path) {
            if ( (substr($path, -1) != "/") && (substr($path, -1) != "\\") ) {
                if (str_contains($path, "/")) {
                    $path = $path . "/";
                } else {
                    $path = $path . "\\";
                }
            }
            return $path;
        }


        /**
         * Verify Path Exists
         * 
         * @since 2.2.0
         * 
         * @param string $path File path.
         * 
         * @return boolean True if path exists or we were able to make directory, false if make directory failed.
         */
        private function verify_path_exists($path) {
            if (file_exists($path)) {
                return true;
            } else {
                return mkdir($path);
            }
        }
        
        
         /**
         * Set File Content
         * 
         * @since 2.2.0
         * 
         * @param string $fcontent File content.
         **/
        private function set_content($fcontent) {
            $this->file_content = $fcontent;
        }


        /**
         * Save Content to File
         * 
         * @since 2.2.0
         * 
         * @return array Array of details of file created with key-value pairs.
         **/
        public function save_to_file() {
            $this->full_file_name_path = $this->file_path . $this->file_name . $this->file_extension;
            $this->save_content();
            return $this->file_created;
        }


        /**
         * Save to File - Private
         * 
         * @since 2.2.0
         * @since 3.0.13 Included delimiter and end of line character option for csv.
         * @since 3.0.13 Returned full path and file name separately.
         * 
         * @return null
         */
        private function save_content() {
            //array save as csv
            if (is_array($this->file_content)) {
                if (count($this->file_content) != 0) {
                    $csv_file = fopen($this->full_file_name_path, "w");
                    foreach ($this->file_content as $item) {
                        fputcsv($csv_file, $item, $separator=$this->file_delimiter);
                    }
                    fclose($csv_file);
                    $this->file_created['fpath'] = $this->full_file_name_path;
                    $this->file_created['fname'] = $this->file_name . $this->file_extension;
                    $this->file_created['fcontent'] = file_get_contents($this->full_file_name_path);
                }
            //or save as string
            } else {
                file_put_contents($this->full_file_name_path, $this->file_content);
                $this->file_created['fpath'] = $this->full_file_name_path;
                $this->file_created['fname'] = $this->file_name . $this->file_extension;
                $this->file_created['fcontent'] = file_get_contents($this->full_file_name_path);
            }
            return;
        }


        /**
         * Download File - Public
         * 
         * @since 2.2.0
         **/
        public function download_file() {
            $this->download_created_file();
        }


         /**
         * Download File - Private
         * 
         * @since 2.2.0
         **/
        private function download_created_file() {
            //cannot get this to work - probably because modern browsers do not support download by ajax
            /**https://www.php.net/manual/en/function.readfile.php**/
            //only download csv files for security
            if ((file_exists($this->full_file_name_path)) and (str_ends_with(trim($this->full_file_name_path), ".csv"))) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="'. $this->file_name . $this->file_extension . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: no-cache');
                header('Content-Length: ' . filesize($this->full_file_name_path));
                //$csv_file = fopen($this->full_file_name_path, "r");
                //fpassthru($csv_file);
                //rewind($csv_file);
                //fread($csv_file, filesize($this->full_file_name_path));
                readfile($this->full_file_name_path);
                exit;
            }            
        }


    } //close class
} //close if class exists