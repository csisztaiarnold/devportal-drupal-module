<?php

namespace Drupal\devportal_api_reference\Plugin;

/**
 * The OpenApiValidationException class.
 *
 * @package Drupal\devportal_api_reference\Plugin
 */
class OpenApiValidationException extends \Exception {

  /**
   * List of errors.
   *
   * @var array
   */
  protected $errors = [];

  /**
   * Returns the list of stored errors.
   *
   * @return array
   *   Error array
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * Sets the list of stored errors.
   *
   * @param array $errors
   *   Error array.
   *
   * @return OpenApiValidationException
   *   Return errors.
   */
  public function setErrors($errors: array) {
    $this->errors = $errors;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($message = "", $code = 0, \Throwable $previous = NULL, array $errors = []) {
    parent::__construct($message, $code, $previous);
    $this->errors = $errors;
  }

  /**
   * Factory method that creates an instance from a list of validation errors.
   *
   * @param array $errors
   *   Error array.
   * @param \Throwable|null $previous
   *   Message or null.
   *
   * @return static
   */
  public static function fromErrors(array $errors, \Throwable $previous = NULL) {
    return new static(implode(PHP_EOL, array_map(function ($error) {
      $msg = "";

      if ($error['property']) {
        $msg .= " [{$error['property']}]";
      }

      $msg .= " {$error['message']}";

      return $msg;
    }, $errors)), 0, $previous, $errors);
  }

}
