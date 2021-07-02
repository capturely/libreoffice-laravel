<?php

namespace Capturely\LibreOffice;

use Illuminate\Http\Client\Response;
use SplFileInfo;

class LibreOffice
{
	const AVAILABLE_CONVERSIONS = [
		'pdf' => 'application/pdf',
		'odt' => 'application/vnd.oasis.opendocument.text',
	];
	
	protected ?string $file;
	
	protected ?string $to;
	
	protected string $accept;
	
	/**
	 * @param string|SplFileInfo $path
	 * @return LibreOffice
	 */
	public static function fromFile($path)
	{
		if ($path instanceof SplFileInfo) {
			$path = $path->getPathname();
		}
		
		$file = file_get_contents($path);
		
		$base64 = base64_encode($file);
		
		return (new static())->setBase64($base64);
	}
	
	public static function fromBase64(string $base64)
	{
		return (new static())->setBase64($base64);
	}
	
	private function setBase64(string $base64) : self
	{
		$this->file = $base64;
		
		return $this;
	}
	
	public function toPdf() : LibreOffice
	{
		$this->to('pdf');
		
		return $this;
	}
	
	public function to(string $to) : LibreOffice
	{
		$to = strtolower($to);
		
		if (isset(static::AVAILABLE_CONVERSIONS[$to])) {
			$this->accept = static::AVAILABLE_CONVERSIONS[$to];
		}
		
		$this->to = $to;
		
		return $this;
	}
	
	public function serializePayload()
	{
		return [
			'file' => $this->file,
			'to' => $this->to,
		];
	}
	
	public function convert() : Response
	{
		$api = new Api($this->serializePayload());
		
		return $api->post($this->accept);
	}
	
}
