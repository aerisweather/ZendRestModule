<?php


namespace Aeris\ZendRestModule\Service\Serializer\Handler\PhoneNumber;


class PhoneNumberFormatter {

	/**
	 * @var array
	 */
	private $templates;

	public function __construct() {
		$this->templates = array(
			'usa' => array(__CLASS__, 'defaultUSATemplate'),
			'intl' => array(__CLASS__, 'defaultIntlTemplate')
		);
	}


	public function format($phoneNumber) {
		$phoneNumberPattern = '/([0-9]+)x*([0-9]*)/';
		preg_match($phoneNumberPattern, $phoneNumber, $matches);

		$number = $matches[1];
		$ext = isset($matches[2]) ? $matches[2] : null;
		$isUSNumber = $number{0} === '1' && strlen($number) === 11;

		if ($isUSNumber) {
			$template = $this->templates['usa'];
			$parts = array(
				$areaCode = substr($number, 1, 3),
				$firstThree = substr($number, 4, 3),
				$lastFour = substr($number, 7, 4),
				$ext
			);
		}
		else {
			$template = $this->templates['intl'];
			$parts = array(
				$number,
				$ext,
			);
		}

		return call_user_func($template, $parts);
	}

	public function setUSATemplate($template){
		$this->templates['usa'] = $template;
	}

	public function setIntlTemplate($template) {
		$this->templates['intl'] = $template;
	}


	static function defaultUSATemplate($parts) {
		$areaCode = $parts[0];
		$firstThree = $parts[1];
		$lastFour = $parts[2];
		$ext = $parts[3];

		$formatted = "($areaCode) $firstThree $lastFour";

		if ($ext) {
			$formatted .= " ext$ext";
		}

		return $formatted;
	}

	static function defaultIntlTemplate($parts) {
		$number = $parts[0];
		$ext = $parts[1];

		$formatted = "+$number";

		if ($ext) {
			$formatted .= " ext$ext";
		}

		return $formatted;
	}
}