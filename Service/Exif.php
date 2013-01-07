<?php

namespace Reoring\ImageBundle\Service;

class Exif
{
  private $exifData = false;

  /**
   * @param string $filePath
   * @return Exif
   * @throws \Exception
   */
  public function read($filePath)
  {
    if (!is_readable($filePath)) {
      throw new \Exception('file not found: path=' . $filePath);
    }
    
    $exif = exif_read_data($filePath, 'IFD0');
    
    if ($exif === false) {
      $this->exifData = false;
      return $this;
    }

    foreach ($exif as $key => $section) {
      if (is_array($section)) {
        foreach ($section as $name => $val) {
          if (!isset($this->exifData[$key])) {
            $this->exifData[$key] = array();
          }
        
          $this->exifData[$key.'.'.$name] = $val;
        }  
      } else {
        $this->exifData[$key] = $section;
      }
    }

    return $this;
  }

  /**
   * @param string $name
   * @return null
   */
  public function __get($name)
  {
    if ($this->has($name)) {
      return $this->exifData[$name];
    }
    
    return null;
  }

  /**
   * @param string $name
   * @return bool
   */
  public function has($name)
  {
    return isset($this->exifData[$name]);
  }

  /**
   * @return bool
   */
  public function isExist()
  {
    return ($this->exifData !== false);
  }
}