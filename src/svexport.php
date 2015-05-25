<?php

/**
 * SVExport
 * Simple character-separated values exporter (CSV and TSV).
 *
 * @version   1.0.0
 * @author    José Luis Quintana <joseluisquintana20@gmail.com>
 * @link      https://github.com/quintana-dev/svexport
 */
class SVExport {

  const FORMAT_CSV = 1;
  const FORMAT_TSV = 2;

  /**
   * String buffer output.
   *
   * @var string
   */
  private $_buffer;

  /**
   * Export format
   *
   * @var string
   */
  private $_format;

  /**
   * Delimiter character. Default is semicolon. [;]
   *
   * @var string
   */
  private $_delimiter;

  /**
   * Default character for empty values. Default is hyphen. [-]
   *
   * @var string
   */
  private $_empty_char;

  /**
   * Wrapper character for values. Default is double quote. ["]
   *
   * @var string
   */
  private $_wrapper_char;

  /**
   * Setup the options
   *
   * @param string $delimiter       Delimiter character. Default is semicolon. [;]
   * @param string $empty_char      Default character for empty values. Default is hyphen. [-]
   * @param string $wrapper_char    Wrapper character for values. Default is double quote. ["]
   */
  function __construct($delimiter = ";", $empty_char = "-", $wrapper_char = "\"") {
    $this->_delimiter = $delimiter;
    $this->_empty_char = $empty_char;
    $this->_wrapper_char = $wrapper_char;

    $this->_buffer = "";
    $this->_format = self::FORMAT_CSV;
  }

  /**
   * Parse from array data source
   *
   * @param array $data_source    Data source array.
   * @return string               String buffer
   */
  function fromArray($data_source) {
    $rows_str = "";
    $row_str = "";

    foreach ($data_source as $data_row) {
      $row_str = "";

      foreach ($data_row as $field_value) {
        if ((!isset($field_value)) || ($field_value === "")) {
          $field_value = $this->_empty_char;
        } else {
          $field_value = str_replace(array("\"", "\;"), array("\"\"", ""), $field_value);
        }

        $row_str .= $this->_wrapper_char . $field_value . $this->_wrapper_char . $this->_delimiter;
      }

      $rows_str .= trim($row_str) . "\n";
    }

    $this->_buffer = str_replace("\r", "", $rows_str);
    return $this->_buffer;
  }
  
  /**
   * Load TVS or CVS from file to array
   * 
   * @param array $filepath    Filepath.
   * @return array
   */
  function fromFile($filepath, $load_keys = FALSE) {
    $array = array();

    if (!file_exists($filepath)) {
      return $array;
    }

    $content = file($filepath);

    for ($x = 0; $x < count($content); $x++) {
      if (trim($content[$x]) != '') {
        $line = explode($this->_delimiter, trim($content[$x]));

        if ($load_keys) {
          $key = array_shift($line);
          $array[$key] = $line;
        } else {
          $array[] = $line;
        }
      }
    }

    return $array;
  }

  /**
   * Returns string buffer.
   *
   * @return string
   */
  function getBuffer() {
    return $this->_buffer;
  }

  /**
   * Sets default format to CSV.
   *
   */
  function toCSV() {
    $this->_format = self::FORMAT_CSV;
    $this->_delimiter = ";";
  }

  /**
   * Sets default format to TSV.
   *
   */
  function toTSV() {
    $this->_format = self::FORMAT_TSV;
    $this->_delimiter = "\t";
  }

  /**
   * Save file to specific path.
   *
   * @param string $filename  Filename to save
   * @param bool $forceUTF8   Force to convert string bufer to UTF-8 ecoding. Default is FALSE.
   * @return int              Number of bytes that were written to the file, or FALSE on failure.
   */
  function save($filename, $forceUTF8 = FALSE) {
    $buffer = $this->_buffer;

    if ($forceUTF8) {
      $buffer = mb_convert_encoding($buffer, 'UTF-8', 'OLD-ENCODING');
    }

    return file_put_contents($filename, $buffer);
  }

  /**
   * Output string buffer.
   *
   */
  function output() {
    if ($this->_format === self::FORMAT_CSV) {
      header("Content-type: text/csv; charset=utf-8");
    } else {
      header("Content-type: text/tab-separated-values; charset=utf-8");
    }

    echo $this->_buffer;
  }

  /**
   * Force to download file
   *
   * @param string $filename
   */
  function download($filename) {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $this->_buffer;
  }

}
