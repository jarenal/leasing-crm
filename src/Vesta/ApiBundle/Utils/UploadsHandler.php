<?php

namespace App\ApiBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cocur\Slugify\Slugify;

class UploadsHandler
{
	var $paths = array();
	var $fb = null;
	var $debug = true;

	public function __construct($fire_php="")
	{
		$this->fb = $fire_php;
		$this->paths['PATH_UPLOADS'] = $_SERVER['DOCUMENT_ROOT']."/uploads";
		$this->paths['CONTACTS'] = array();
		$this->paths['CONTACTS']['DOCUMENTS'] = $this->paths['PATH_UPLOADS']."/contacts/{token}/documents/";
		$this->paths['CONTACTS']['IMAGES'] = $this->paths['PATH_UPLOADS']."/contacts/{token}/images/";
		$this->paths['PROPERTIES'] = array();
		$this->paths['PROPERTIES']['DOCUMENTS'] = $this->paths['PATH_UPLOADS']."/properties/{token}/documents/";
		$this->paths['PROPERTIES']['IMAGES'] = $this->paths['PATH_UPLOADS']."/properties/{token}/images/";
        $this->paths['LEASE_AGREEMENT'] = array();
        $this->paths['LEASE_AGREEMENT']['DOCUMENTS'] = $this->paths['PATH_UPLOADS']."/lease-agreements/{token}/documents/";
        $this->paths['TENANCIES'] = array();
        $this->paths['TENANCIES']['DOCUMENTS'] = $this->paths['PATH_UPLOADS']."/tenancies/{token}/documents/";
	}

	public function uploadFile($token, $file, &$response, $file_type, $entity_type)
	{
		try
		{
			$fs = new Filesystem();

			$full_path = str_replace("{token}", $token, $this->paths[$entity_type][$file_type]);

	        if(!$fs->exists($full_path))
	        {
	            $fs->mkdir($full_path);
	        }


        	if(!($file instanceof UploadedFile))
        	{
        		$response['errors'][] = array($file->getClientOriginalName() => "The file couldn't be uploaded");
        		throw new IOException("The file was not uploaded correctly.", 200);
        	}

        	if(!$file->isValid())
        	{
        		$upload_error = $file->getError();

        		switch ($upload_error)
        		{
        			case UPLOAD_ERR_INI_SIZE: // 1
        				$message = "The uploaded file exceeds the maximum file size.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 201);
        				break;
        			case UPLOAD_ERR_FORM_SIZE: // 2
        				$message = "The uploaded file exceeds the maximum file size specified in the HTML form.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 202);
        				break;
        			case UPLOAD_ERR_PARTIAL: // 3
        			$message = "The uploaded file was only partially uploaded.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 203);
        				break;
        			case UPLOAD_ERR_NO_FILE: // 4
        				$message = "No file was uploaded.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 204);
        				break;
        			case UPLOAD_ERR_NO_TMP_DIR: // 6
        				$message = "Missing a temporary folder.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 206);
        				break;
        			case UPLOAD_ERR_CANT_WRITE: // 7
        				$message = "Failed to write file to disk.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 207);
        				break;
        			case UPLOAD_ERR_EXTENSION: // 8
        				$message = "A PHP extension stopped the file upload.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 208);
        				break;
        			default:
        				$message = "The file isn't valid.";
        				$response['errors'][] = array($file->getClientOriginalName() => $message);
        				throw new IOException($message, 209);
        				break;
        		}
        	}

			$slugify = new Slugify();
			$pathinfo = pathinfo($full_path.$file->getClientOriginalName());
			$slug_filename = $slugify->slugify($pathinfo['filename']);
			$new_filename = $slug_filename.".".$pathinfo['extension'];
			$file->move($full_path, $new_filename);

			return $full_path.$new_filename;


		}
        catch (IOExceptionInterface $ioe)
        {
        	if($ioe->getCode())
        		$error_code = $ioe->getCode();
        	else
        		$error_code = 1;

        	if($error_code<200 && $this->debug)
        		$response['errors'][] = array($error_code => $ioe->getMessage());

            throw new HttpException($error_code, "Something went wrong trying to upload the files.");
        }
	}

	public function removeFile($token, $filename, &$response, $file_type, $entity_type)
	{
		try
		{
			$fs = new Filesystem();

			$full_path = str_replace("{token}", $token, $this->paths[$entity_type][$file_type]).$filename;


	        if(!$fs->exists($full_path))
	        {
	            throw new IOException("The file does not exist.", 190);
	        }

			$fs->remove($full_path);

			return true;

		}
        catch (IOExceptionInterface $ioe)
        {
        	if($ioe->getCode())
        		$error_code = $ioe->getCode();
        	else
        		$error_code = 1;

        	if($error_code<200 && $this->debug)
        		$response['errors'][] = array($error_code => $ioe->getMessage());

            throw new HttpException($error_code, "Something went wrong trying to remove the file.");
        }
	}
}