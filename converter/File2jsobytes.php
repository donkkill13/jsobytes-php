<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
class File2jsobytes
{
	protected $jsobyte = ['jsobyte' => ['version' => '1.0']];
	
	protected $filedir;
	
	public function __construct($filedir, $filename = null)
	{
		$this->filedir = $filedir;
		$this->jsobyte['file_info'] = $this->getFileInfo($filedir, $filename);
	}
	
	public function getJSON()
	{
		$filedata['trends'] = [];
		$filedata['data'] = [];
		
		$fp = @fopen($this->filedir, 'rb');
		
		$end = filesize($this->filedir) - 1;
		
		$buffer = 1;
		
		while (!feof($fp) && ($p = ftell($fp)) <= $end)
		{
			if ($p + $buffer > $end)
			{
				$buffer = $end - $p + 1;
			}
			$filedata['data'][] = (string) fread($fp, $buffer);
		}
		
		$filedata = $this->findTrends($filedata['data']);

		$this->jsobyte['file_data'] = $filedata;
		
		$filedir = __DIR__ . time() . "-{$file_data['file_name']}.json";
		if (file_put_contents($filedir, $data))
		{
			echo "Success, saved at {$filedir}";
			return $filedir;
		}
		else
		{
			echo "Error saving file.";
			return false;
		}
	}
	
	protected function findTrends(array $input, array $trends = null)
	{
		$trends = $this->getTrends($input);
		
		foreach ($input as $key => $char)
		{
			foreach ($trends as $trend)
			{
				if ($char === current($trend))
				{
					end($trend);
					$lastTrendKey = key($trend);
					reset($trend);
					foreach ($trend as $StringTrendKey => $StringTrend)
					{
						if ($input[($key + $StringTrendKey)] === $StringTrend)
						{
							if ($StringTrendKey === $lastTrendKey)
							{
								foreach ($trend as $StringTrendKey => $StringTrend)
								{
									if ($StringTrendKey === $lastTrendKey)
									{
										$input[($key + $StringTrendKey)] = (array) array_search($trend, $trends);
										break;
									}
									unset($input[($key + $StringTrendKey)]);
								}
								break;
							}
						}
						else
						{
							break;
						}
					}
				}
			}
		}
		
		foreach ($trends as &$trend)
		{
			foreach ($trend as &$char)
			{
				$char = ord($char);
			}
		}
		
		foreach ($input as &$data)
		{
			$data = is_array($data) ? (string) current($data) : ord($data);
		}
		
		$input = array_values($input);
		
		return ['trends' => $trends, 'data' => $input];
	}
	
	protected function getTrends(array $input, array $trends = [])
	{
		$longestSubString = $this->getLongestSubset($input);
		if (!$longestSubString && empty($trends))
		{
			$longestSubString[] = current($input);
		}
		if (empty($longestSubString))
		{
			return $trends;
		}
		
		end($longestSubString);
		$lastSub = key($longestSubString);
		reset($longestSubString);
		
		$trends[] = $longestSubString;
		
		end($trends);
		$lastTrend = key($trends);
		reset($lastTrend);
		
		foreach ($input as $key => $char)
		{
			if ($char === $longestSubString[0])
			{
				foreach ($longestSubString as $SubStringKey => $SubStringChar)
				{
					if ($input[($key + $SubStringKey)] === $SubStringChar)
					{
						if ($SubStringKey === $lastSub)
						{
							foreach ($longestSubString as $SubStringKey => $SubStringChar)
							{
								unset($input[($key + $SubStringKey)]);
							}
							break;
						}
						next;
					}
					else
					{
						break;
					}
				}
			}
		}
		
		$input = array_values($input);
		
		return $this->getTrends($input, $trends);
	}
	
	protected function getLongestSubset(array $input)
	{
		$longestSubstring = "";
		$inputString = implode("", $input);
		for( $i=0; $i < strlen($inputString); $i++)
		{
			for( $j=0; $j < strlen($inputString); $j++)
			{
				$length = abs($j-$i);
				$substring = substr($inputString, $i, $length);
				$doesSubstringRepeat = strrpos($inputString, $substring) > $i;
				$substringLongerThanLongest = strlen($substring) > strlen($longestSubstring);
				if ($doesSubstringRepeat && $substringLongerThanLongest)
				{
					$longestSubstring = $substring;
				}
			}
		}
		
		return strlen($longestSubstring) > 1 ? str_split($longestSubstring) : [];
	}
	
	protected function getFileInfo($filedir, $filename = null)
	{
		$filename = !empty($filename) ? $filename : pathinfo($filedir, PATHINFO_BASENAME);
		
		$fileinfo['file_name'] = pathinfo($filename, PATHINFO_FILENAME);
		$fileinfo['file_ext'] = pathinfo($filename, PATHINFO_EXTENSION);
		$fileinfo['mime_type'] = (new finfo(FILEINFO_MIME_TYPE))->file($filedir);
		
		return $fileinfo;
	}
}
?>
