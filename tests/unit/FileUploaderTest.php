<?php namespace App\Tests;

use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testUpload()
    {

        $fileUploader= new FileUploader("/home/saiful/Pictures/test/");
        $file= new UploadedFile("/home/saiful/Pictures/4.jpg","4.jpg");
        //dump($file); die;
        $filename=$fileUploader->upload($file);
        $this->assertTrue(1);
    }
}