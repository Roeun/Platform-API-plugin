<?php
/**
 * Exception containing validation errors from the model. Useful for API
 * responses where you need an error code in response
 *
 **/
class ValidationException extends CakeException {

/**
 * Default exception message
 *
 * @var string
 **/
	protected $message = 'Some validation errors occurred';

/**
 * List of validation errors that occurred in the model
 *
 * @var array
 **/
	protected $validationErrors = array();

/**
 * Constructor
 *
 * @param array $error list of validation errors
 * @param int $code code to report to client
 * @return void
 **/
	public function __construct($errors, $code = 412) {
		$this->validationErrors = $errors;
		$flat = Hash::flatten($this->validationErrors);
		if (count($flat) === 1) {
			$model = key($errors);
			$field = key($errors[$model]);
			$error = $errors[$model][$field][0];

			$instance = ClassRegistry::getObject($model);
			foreach($instance->validate[$field] as $key => $rule) {
				$matchesMessage = (isset($rule['message']) && $error === $rule['message']);
				if ($key !== $error && !$matchesMessage) {
					continue;
				}

				$this->message = sprintf('%s.%s : %s', $model, $field, $error);
				if (!empty($rule['code'])) {
					$code = $rule['code'];
				}
				break;
			}
		}
		parent::__construct($this->message, $code);
	}

/**
 * Returns the list of validation errors
 *
 * @return array
 **/
	public function getValidationErrors() {
		return $this->validationErrors;
	}

}
