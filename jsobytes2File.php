<?php

class jsobytes2File
{
	protected $jsonError;
	protected $text;
	
	public function __construct($text)
	{
		$this->text = $text;
	}
	
	public function getFile()
	{
		$decoded = $this->jsonDecode($this->text);
		
		if ($decoded)
		{
			$jsobyte = $decoded['jsobyte'];
			$file_info = $decoded['file_info'];
			$file_data = $decoded['file_data'];
			$jsobytes = [];
			foreach ($file_data['data'] as $key => $data)
			{
				if (is_string($data) && array_key_exists(intval($data), $file_data['trends']))
				{
					foreach ($file_data['trends'][intval($data)] as $trendKey => $trend)
					{
						$jsobytes[] = chr($trend);
					}
					next();
				}
				$jsobytes[] = chr($data);
			}
			$file = implode('', $jsobytes);
			$file = preg_replace('/[\x00-\x1F\x7F]/', '', $file);
			$filedir = __DIR__ . time() . "-{$file_data['file_name']}.{$file_data['file_ext']}";
			if (file_put_contents($filedir, $data))
			{
				echo "Success, saved at {$filedir}";
				die();
			}
			else
			{
				echo "Error saving file.";
				die();
			}
		}
		else
		{
			echo $this->jsonError();
			die();
		}
	}
	
	protected function jsonDecode($text)
	{
		$text = json_decode($text, true);
		if ($this->switchError())
		{
			if (
				isset($text['jsobyte']) &&
				isset($text['file_info']) &&
				isset($text['file_data'])
				)
			{
				return $text;
			}
		}
		return false;
	}
	
	protected function switchError()
	{
		switch (json_last_error())
		{
			case JSON_ERROR_NONE:
				$this->jsonError = ' - No errors';
				return true;
			break;
			case JSON_ERROR_DEPTH:
				$this->jsonError = ' - Maximum stack depth exceeded';
				return false;
			break;
			case JSON_ERROR_STATE_MISMATCH:
				$this->jsonError = ' - Underflow or the modes mismatch';
				return false;
			break;
			case JSON_ERROR_CTRL_CHAR:
				$this->jsonError = ' - Unexpected control character found';
				return false;
			break;
			case JSON_ERROR_SYNTAX:
				$this->jsonError = ' - Syntax error, malformed JSON';
				return false;
			break;
			case JSON_ERROR_UTF8:
				$this->jsonError = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
				return false;
			break;
			default:
				$this->jsonError = ' - Unknown error';
				return false;
			break;
		}
	}
}
?>
