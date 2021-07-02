<?php

namespace Capturely\LibreOffice\Tests;

use Capturely\LibreOffice\Api;
use Capturely\LibreOffice\CapturelyLibreOfficeServiceProvider;
use Capturely\LibreOffice\LibreOffice;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class CapturelyTest extends TestCase
{
	protected function getPackageProviders($app)
	{
		return [CapturelyLibreOfficeServiceProvider::class];
	}
	
	public function test_spl_file_is_read_and_is_set_up_to_convert_a_pdf() : void
	{
		$path = __DIR__.'/Fixtures/test.html';
		$base64 = base64_encode(file_get_contents($path));
		
		$file = new \SplFileInfo($path);
		
		$libreoffice = LibreOffice::fromFile($file)->toPdf();
		
		$this->assertEquals([
			'to' => 'pdf',
			'file' => $base64,
		], $libreoffice->serializePayload());
	}
	
	public function test_when_a_path_is_given_it_is_also_converted_to_base64() : void
	{
		$path = __DIR__.'/Fixtures/test.html';
		$base64 = base64_encode(file_get_contents($path));
		
		$libreoffice = LibreOffice::fromFile($path)->toPdf();
		
		$this->assertEquals([
			'to' => 'pdf',
			'file' => $base64,
		], $libreoffice->serializePayload());
	}
	
	public function test_when_giving_a_base64_string_it_is_properly_set() : void
	{
		$path = __DIR__.'/Fixtures/test.html';
		$base64 = base64_encode(file_get_contents($path));
		
		$libreoffice = LibreOffice::fromBase64($base64)->toPdf();
		
		$this->assertEquals([
			'to' => 'pdf',
			'file' => $base64,
		], $libreoffice->serializePayload());
	}
	
	public function test_when_a_user_asks_for_pdf_proper_accept_headers_are_set() : void
	{
		Http::fake([
			Api::ENDPOINT => Http::response('data'),
		]);
		
		$path = __DIR__.'/Fixtures/test.html';
		$base64 = base64_encode(file_get_contents($path));
		
		LibreOffice::fromBase64($base64)->toPdf()->convert();
		
		Http::assertSent(function(Request $request) {
			return $request->hasHeader('Accept', 'application/pdf');
		});
	}
	
}
