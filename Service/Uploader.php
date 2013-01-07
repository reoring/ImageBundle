<?php

namespace Reoring\ImageBundle\Service;

/**
 * ファイルをアップロードするクラス
 */
class Uploader
{
  /**
   * @param string $uri
   * @param string $filePath
   * @return string
   */
  public function upload($uri, $filePath)
  {
    $client = new \Zend\Http\Client();

    $client->setUri($uri);
    $client->setMethod('POST');
    $client->setFileUpload($filePath, 'file');

    return $client->send()->getBody();
  }
}