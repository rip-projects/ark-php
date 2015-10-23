<?php

/**
 * MY_Upload.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class MY_Upload extends CI_Upload {

    var $field = '';
    var $required = false;
    var $_data;

    function initialize($params = array()) {
        $CI = &get_instance();

        if (!empty($params['field'])) {
            $this->field = $params['field'];
            $params['upload_path'] = './data/' . $CI->_name . '/' . $params['field'];
            if (!file_exists($params['upload_path'])) {
                @mkdir($params['upload_path'], 0777, true);
            }
        }

        parent::initialize($params);
    }

    public function mimes_types($mime)
	{
		global $mimes;
		if (count($this->mimes) == 0)
		{
			if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php');
			}
			elseif (is_file(APPPATH.'config/mimes.php'))
			{
				include(APPPATH.'config/mimes.php');
			}
			elseif (defined('ENVIRONMENT') AND is_file(ARCHPATH.'config/'.ENVIRONMENT.'/mimes.php'))
			{
				include(ARCHPATH.'config/'.ENVIRONMENT.'/mimes.php');
			}
			elseif (is_file(ARCHPATH.'config/mimes.php'))
			{
				include(ARCHPATH.'config/mimes.php');
			}
			else
			{
				return FALSE;
			}

			$this->mimes = $mimes;
			unset($mimes);
		}

		return ( ! isset($this->mimes[$mime])) ? FALSE : $this->mimes[$mime];
	}
    /**
     * Perform the file upload
     *
     * @return  bool
     */
    public function do_upload($field = 'userfile')
    {
    // Is $_FILES[$field] set? If not, no reason to continue.
        if ( ! isset($_FILES[$field]))
        {
            $this->set_error('upload_no_file_selected');
            return FALSE;
        }

        // Is the upload path valid?
        if ( ! $this->validate_upload_path())
        {
            // errors will already be set by validate_upload_path() so just return FALSE
            return FALSE;
        }

        if (!is_array($_FILES[$field]['tmp_name'])) {
            $_FILES[$field]['name'] = array($_FILES[$field]['name']);
            $_FILES[$field]['type'] = array($_FILES[$field]['type']);
            $_FILES[$field]['tmp_name'] = array($_FILES[$field]['tmp_name']);
            $_FILES[$field]['error'] = array($_FILES[$field]['error']);
            $_FILES[$field]['size'] = array($_FILES[$field]['size']);
        }

        $names = array();
        $types = array();
        $tmp_names = array();
        $errors = array();
        $sizes = array();

        foreach($_FILES[$field]['error'] as $i => $error) {
            if (!$this->required && $error == 4) continue;
            $names[] = $_FILES[$field]['name'][$i];
            $types[] = $_FILES[$field]['type'][$i];
            $tmp_names[] = $_FILES[$field]['tmp_name'][$i];
            $errors[] = $_FILES[$field]['error'][$i];
            $sizes[] = $_FILES[$field]['size'][$i];
        }

        $_FILES[$field]['name'] = $names;
        $_FILES[$field]['type'] = $types;
        $_FILES[$field]['tmp_name'] = $tmp_names;
        $_FILES[$field]['error'] = $errors;
        $_FILES[$field]['size'] = $sizes;

        foreach($_FILES[$field]['tmp_name'] as $i => $tmp_name) {
            if (!$this->required && $tmp_name == '') continue;
            // Was the file able to be uploaded? If not, determine the reason why.
            if ( ! is_uploaded_file($tmp_name))
            {
                $error = ( ! isset($_FILES[$field]['error'][$i])) ? 4 : $_FILES[$field]['error'][$i];

                switch($error)
                {
                    case 1: // UPLOAD_ERR_INI_SIZE
                        $this->set_error('upload_file_exceeds_limit');
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE
                        $this->set_error('upload_file_exceeds_form_limit');
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL
                        $this->set_error('upload_file_partial');
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE
                        $this->set_error('upload_no_file_selected');
                        break;
                    case 6: // UPLOAD_ERR_NO_TMP_DIR
                        $this->set_error('upload_no_temp_directory');
                        break;
                    case 7: // UPLOAD_ERR_CANT_WRITE
                        $this->set_error('upload_unable_to_write_file');
                        break;
                    case 8: // UPLOAD_ERR_EXTENSION
                        $this->set_error('upload_stopped_by_extension');
                        break;
                    default :   $this->set_error('upload_no_file_selected');
                        break;
                }

                return FALSE;
            }
        }

        $this->_data = array();
        foreach($_FILES[$field]['tmp_name'] as $i => $tmp_name) {
            // Set the uploaded data as class variables
            $this->file_temp = $_FILES[$field]['tmp_name'][$i];
            $this->file_size = $_FILES[$field]['size'][$i];

            $uploaded = array(
                'name'     => $_FILES[$field]['name'][$i],
                'type'     => $_FILES[$field]['type'][$i],
                'tmp_name' => $_FILES[$field]['tmp_name'][$i],
                'error'    => $_FILES[$field]['error'][$i],
                'size'     => $_FILES[$field]['size'][$i],
            );

            $this->_file_mime_type($uploaded);
            $this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $this->file_type);
            $this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
            $this->file_name = $this->_prep_filename($_FILES[$field]['name'][$i]);
            $this->file_ext  = $this->get_extension($this->file_name);
            $this->client_name = $this->file_name;

            // Is the file type allowed to be uploaded?
            if ( ! $this->is_allowed_filetype())
            {
                $this->set_error('upload_invalid_filetype');
                return FALSE;
            }

            // if we're overriding, let's now make sure the new name and type is allowed
            if ($this->_file_name_override != '')
            {
                $this->file_name = $this->_prep_filename($this->_file_name_override);

                // If no extension was provided in the file_name config item, use the uploaded one
                if (strpos($this->_file_name_override, '.') === FALSE)
                {
                    $this->file_name .= $this->file_ext;
                }

                // An extension was provided, lets have it!
                else
                {
                    $this->file_ext  = $this->get_extension($this->_file_name_override);
                }

                if ( ! $this->is_allowed_filetype(TRUE))
                {
                    $this->set_error('upload_invalid_filetype');
                    return FALSE;
                }
            }

            // Convert the file size to kilobytes
            if ($this->file_size > 0)
            {
                $this->file_size = round($this->file_size/1024, 2);
            }

            // Is the file size within the allowed maximum?
            if ( ! $this->is_allowed_filesize())
            {
                $this->set_error('upload_invalid_filesize');
                return FALSE;
            }

            // Are the image dimensions within the allowed size?
            // Note: This can fail if the server has an open_basdir restriction.
            if ( ! $this->is_allowed_dimensions())
            {
                $this->set_error('upload_invalid_dimensions');
                return FALSE;
            }

            // Sanitize the file name for security
            $this->file_name = $this->clean_file_name($this->file_name);

            // Truncate the file name if it's too long
            if ($this->max_filename > 0)
            {
                $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
            }

            // Remove white spaces in the name
            if ($this->remove_spaces == TRUE)
            {
                $this->file_name = preg_replace("/\s+/", "_", $this->file_name);
            }

            /*
             * Validate the file name
             * This function appends an number onto the end of
             * the file if one with the same name already exists.
             * If it returns false there was a problem.
             */
            $this->orig_name = $this->file_name;

            if ($this->overwrite == FALSE)
            {
                $this->file_name = $this->set_filename($this->upload_path, $this->file_name);

                if ($this->file_name === FALSE)
                {
                    return FALSE;
                }
            }

            /*
             * Run the file through the XSS hacking filter
             * This helps prevent malicious code from being
             * embedded within a file.  Scripts can easily
             * be disguised as images or other file types.
             */
            if ($this->xss_clean)
            {
                if ($this->do_xss_clean() === FALSE)
                {
                    $this->set_error('upload_unable_to_write_file');
                    return FALSE;
                }
            }

            /*
             * Move the file to the final destination
             * To deal with different server configurations
             * we'll attempt to use copy() first.  If that fails
             * we'll use move_uploaded_file().  One of the two should
             * reliably work in most environments
             */
            if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name))
            {
                if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name))
                {
                    $this->set_error('upload_destination_error');
                    return FALSE;
                }
            }

            /*
             * Set the finalized image dimensions
             * This sets the image width/height (assuming the
             * file was an image).  We use this information
             * in the "data" function.
             */
            $this->set_image_properties($this->upload_path.$this->file_name);

            $this->_data[] = array (
                'file_name'         => $this->file_name,
                'file_type'         => $this->file_type,
                'file_path'         => $this->upload_path,
                'full_path'         => $this->upload_path.$this->file_name,
                'raw_name'          => str_replace($this->file_ext, '', $this->file_name),
                'orig_name'         => $this->orig_name,
                'client_name'       => $this->client_name,
                'file_ext'          => $this->file_ext,
                'file_size'         => $this->file_size,
                'is_image'          => $this->is_image(),
                'image_width'       => $this->image_width,
                'image_height'      => $this->image_height,
                'image_type'        => $this->image_type,
                'image_size_str'    => $this->image_size_str,
            );
        }

        return TRUE;
    }

    /**
     * Finalized Data Array
     *
     * Returns an associative array containing all of the information
     * related to the upload, allowing the developer easy access in one array.
     *
     * @return  array
     */
    public function data()
    {
        return $this->_data;
    }

    public function is_allowed_filetype($ignore_mime = FALSE)
    {
        $restricted = array(
            '.php',
            '.php5',
            '.php4',
            '.php3',
            '.phtml',
            '.pl',
            '.py',
        );
        if ($this->allowed_types == '*' && in_array($this->file_ext, $restricted))
        {
            return FALSE;
        }
        return parent::is_allowed_filetype($ignore_mime);
    }
}