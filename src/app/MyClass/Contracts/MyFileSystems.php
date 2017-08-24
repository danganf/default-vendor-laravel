<?php

namespace Danganf;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

abstract class MyFileSystems
{
    protected $sessionOpen;
    protected $diskCloud;
    protected $diskLocal;
    protected $pathDefault;
    protected $pathDisk;
    protected $imageManager;
    protected $nameLogo = 'logo.png';

    public function __construct ( SessionOpen $sessionOpen, \Illuminate\Contracts\Filesystem\Factory $fs, \Intervention\Image\ImageManagerStatic $image ){
        $this->sessionOpen  = $sessionOpen;
        $this->diskLocal    = $fs->disk( 'local' );
        $this->diskCloud    = $fs->disk( getenv('APP_FILESYSTEM') );
        $this->pathDefault  = "cliente_" . $this->sessionOpen->get('cliente_id');
        $this->pathDisk     = $this->diskLocal->getDriver()->getAdapter()->getPathPrefix();
        $this->imageManager = $image;
        unset( $sessionOpen, $fs, $image );
    }

    public function saveLogo( $contentFile ){

        $pathFile   = $this->pathDefault . '/' . $this->nameLogo;
        $pathTemp   = 'temp'. '/' . $pathFile;
        $pathOrigin = $this->pathDisk . $pathTemp;

        #criando arquivo local
        $this->diskLocal->put( $pathTemp, $this->cleanContentFileImage( $contentFile ) );

        #redimencioando imagem
        $this->imageResize( $pathOrigin, 200, 150 );

        #copiando arquivo para o destino
        $this->diskCloud->put( $pathFile, $this->diskLocal->get($pathTemp) );

        #apagando pasta local
        $this->diskLocal->deleteDirectory( dirname($pathTemp) );

        return $pathFile;

    }

    public function deleteFile( $pathFile ){
        return $this->diskCloud->delete( $pathFile );
    }

    private function cleanContentFileImage( $content ){
        $image  = str_replace('data:image/png;base64,', '', $content);
        $image  = str_replace(' ', '+', $image);
        return base64_decode($image);
    }

    private function imageResize( $path, $w, $h ){$this->imageManager->make( $path )->resize($w, $h)->save();}

    public function getUrlLogo()           { return $this->getUrl( $this->nameLogo ); }
    public function getUrl( $pathFile="" ) { return $this->diskCloud->url( $pathFile ); }
    public function getPathDefault()       { return $this->pathDefault; }

    private function createImage( $originalName, $pathDestineImage, $pathTemp, $prefx ){

        $randName  = $prefx . '_' . base_convert(date('is'),20,36) . base_convert(rand(10000,99999),20,36) .'_'. base_convert(rand(1000000,9999999),20,36);
        $randName .= '_' . base_convert(date('is'),20,36) . base_convert(rand(10000,99999),20,36) .'_'. base_convert(rand(1000000,9999999),20,36);
        $randName .= '.' . File::extension( $originalName );

        $pathDestineImage .= $randName;

        #copiando arquivo para o destino
        $this->diskCloud->put( $pathDestineImage, File::get( $pathTemp ) );

        return [ 'path' => $pathDestineImage, 'nome' => $randName ];

    }

    private function interventionImgToWeb( $pathFullImg ){

        $image  = $this->imageManager->make( $pathFullImg );
        $width  = 800;
        $height = 600;

        $image->width() > $image->height() ? $width=null : $height=null;
        $image->orientate()->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image->encode();

    }

}