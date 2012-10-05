<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class TownwizardTable extends JTable
{
    protected $_fieldErrors = array();
    protected $_validationRules = array();

    public function setFieldError($field, $error)
    {
        if (array_key_exists($field, $this->getProperties()))
        {
            $this->_fieldErrors[$field][] = $error;
        }
    }

    public function getErrorsByField($field)
    {
        if (array_key_exists($field, $this->_fieldErrors))
        {
            return $this->_fieldErrors[$field];
        }
        return array();
    }

    public function getReorderCondition()
    {
        return '';
    }

    public function check()
    {
        foreach ($this->getProperties() as $property => $value)
        {
            $this->$property = trim($value);
        }

        $properties = array_keys($this->getProperties());

        foreach ($this->_validationRules as $rule)
        {
            if (!is_array($rule))
            {
                throw new Exception('Validation rule should be an array');
            }

            if (count($rule) < 2 || !is_string($rule[0]) && strlen($rule[0]) || !is_string($rule[1]) && strlen($rule[1]))
            {
                throw new Exception('Validator should be specified by minimum of 2 string parameters: validator_name
                                     (e.g. "maxlength") and fieldnames (e.g. "field1, field2, field3")');
            }

            $validator = $rule[0];
            if (!method_exists($this, 'validate_' . $validator))
            {
                throw new Exception("Validator with name '{$validator}' doesn't exist");
            }
            $validator = 'validate_' . $validator;

            $fields = array_map('trim', explode(',', $rule[1]));
            $fields = array_intersect($properties, $fields);
            $params = array();
            if (isset($rule[2]))
            {
                $params = $rule[2];
            }

            foreach ($fields as $field)
            {
                $this->$validator($field, $this->$field, $params);
            }
        }

        //$this->name = htmlspecialchars(mysql_real_escape_string($this->name));
        /*
        if ($titleLen && !preg_match("/^[a-z0-9 ]+$/i", $this->name))
        {
            $this->setFieldError('title', 'Title can contain only letters, numbers and spaces');
        }
        */

        return count($this->_fieldErrors) == 0;
    }


    public function validate_required($field, $value, $params=array())
    {
        if (strlen($value) == 0)
        {
            $this->setFieldError($field, 'This field is required');
            return false;
        }
        return true;
    }

    public function validate_maxlength($field, $value, $params=array())
    {
        if (!array_key_exists('maxlength', $params) || (int)$params['maxlength'] <= 0)
        {
            throw new Exception('Maxlength validator should obtain maxlenth parameter greater then 0');
        }
        $params['maxlength'] = (int)$params['maxlength'];

        if (strlen($value) > $params['maxlength'])
        {
            $this->setFieldError($field, "Value is too long. Must be less then {$params['maxlength']} symbols");
            return false;
        }
        return true;
    }

    public function validate_url($field, $value, $params=array())
    {
        if ($this->isEmpty($value))
        {
            return true;
        }
        $pattern = '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
        if(is_string($value) && preg_match($pattern,$value))
        {
            return true;
        }
        $this->setFieldError($field, 'Is not a valid URL');
        return false;
    }

    public function validate_boolean($field, $value, $params=array())
    {
        if ($this->isEmpty($value))
        {
            return true;
        }
        if (in_array($value, array('0', '1')))
        {
            return true;
        }
        $this->setFieldError($field, 'Value must be either true or false');
        return false;
    }

    public function validate_file($field, $value, $params=array())
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $fieldName = $field;

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0)
        {
            switch ($fileError)
            {
                case 1:
                    $this->setFieldError($field, JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' ));
                    return false;

                case 2:
                    $this->setFieldError($field, JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' ));
                    return false;

                case 3:
                    $this->setFieldError($field, JText::_( 'ERROR PARTIAL UPLOAD' ));
                    return false;

                case 4:
                    //echo JText::_( 'ERROR NO FILE' );
                    return true;
            }
        }

        //check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if($fileSize > 2000000)
        {
            $this->setFieldError($field, JText::_( 'FILE BIGGER THAN 2MB' ));
        }

        //check the file extension is ok
        $fileName = $_FILES[$fieldName]['name'];
        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = array_map('trim', explode(',', $params['types']));

        $extOk = false;

        foreach($validFileExts as $key => $value)
        {
            if( preg_match("/^{$value}$/i", $uploadedFileExtension ) )
            {
                $extOk = true;
                break;
            }
        }

        if (!$extOk)
        {
            $this->setFieldError($field, 'Invalid file extension. Must be ' . $params['types']);
            return false;
        }

        //the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
        //to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);

        //we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
        //types, where we might miss one (whitelisting is always better than blacklisting)
        $okMIMETypes = $params['mimes'];
        $validFileTypes = array_map('trim', explode(",", $okMIMETypes));

        //if the temp file does not have a width or a height, or it has a non ok MIME, return
        if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
        {
            $this->setFieldError($field, 'Invalid filetype. Please upload correct ' . $params['types'] . ' file' );
            return false;
        }

        $this->$field = uniqid() . '.' . $uploadedFileExtension;
        return true;
    }

    public function uploadFile($field, $uploadPath)
    {
        $fileName = $this->$field;

        $fileTemp = $_FILES[$field]['tmp_name'];


        if (!JFolder::exists($uploadPath))
        {
            JFolder::create($uploadPath);
        }

        $uploadPath .= DS.$fileName;

        JFile::upload($fileTemp, $uploadPath);

        return true;
    }

    public function validate_numeric($field, $value, $params=array())
    {
        if ($this->isEmpty($value))
        {
            return true;
        }
        if (!is_numeric($value))
        {
            $this->setFieldError($field, 'Value must be numeric');
            return false;
        }

        if (isset($params['float']) && $params['float'])
        {
            $value = number_format(floatval($value), 15);
            $this->$field = $value;
        }
        return $value;
    }

    protected function isEmpty($value)
    {
        return $value===null || $value===array() || $value==='' || is_scalar($value) && trim($value)==='';
    }

    public function validate_in($field, $value, $params=array())
    {
        if (!array_key_exists('range', $params) || !is_array($params['range']) || empty($params['range']))
        {
            throw new Exception('In validator should obtain range parameter with array of possible values');
        }

        if (!in_array($value, $params['range']))
        {
            $this->setFieldError($field, 'Value must be in specified range ' . implode(', ', $params['range']));
            return false;
        }

        return true;
    }
}
?>