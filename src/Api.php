<?php

namespace Capturely\LibreOffice;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Api
{
	public const ENDPOINT = 'https://libreoffice.capturely.app';
	
	protected array $data = [];
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * @return Response
	 */
	public function post(string $accept)
	{
		$post = Http::withHeaders([
			'Accept' => $accept,
			'Content-Type' => 'application/json',
			'Authorization' => 'abc',
		])
			->post(static::ENDPOINT, $this->data);
		
		if (!$post->successful()) {
			throw new RuntimeException($post->body());
		}
		
		return $post;
	}
	
}
