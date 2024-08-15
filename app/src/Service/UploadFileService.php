<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFileService
{
    private $targetDirectory;
    private $slugger;
    
    public function __construct(string $targetDirectory, SluggerInterface $slugger) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function uploadFile(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {

            $file->move($this->targetDirectory, $fileName);
            return $fileName;

        } catch (FileException $e) {
            return $e;
            // ... handle exception if something happens during file upload
        }
    }
}