<?php
/**
 * PDF Invoicing for WooCommerce - Functions
 *
 * @version 1.4.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_pdf_invoicing_hex_to_rgb' ) ) {
	/**
	 * alg_wc_pdf_invoicing_hex_to_rgb.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function alg_wc_pdf_invoicing_hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );
		$rgb = ( 3 === strlen( $hex ) ? sscanf( $hex, '%1x%1x%1x' ) : ( 6 === strlen( $hex ) ? sscanf( $hex, '%2x%2x%2x' ) : array( 0, 0, 0 ) ) );
		return $rgb;
	}
}

if ( ! function_exists( 'alg_wc_pdf_invoicing_number_to_words' ) ) {
	/**
	 * alg_wc_pdf_invoicing_number_to_words.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_pdf_invoicing_number_to_words( $number, $lang = 'EN' ) {
		switch ( $lang ) {
			case 'LT':
				return alg_wc_pdf_invoicing_number_to_words_lt( $number );
			default: // 'EN'
				return alg_wc_pdf_invoicing_number_to_words_en( $number );
		}
	}
}

if ( ! function_exists( 'alg_wc_pdf_invoicing_number_to_words_en' ) ) {
	/**
	 * alg_wc_pdf_invoicing_number_to_words_en.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @see     https://stackoverflow.com/questions/11500088/php-express-number-in-words
	 *
	 * @todo    [dev] (maybe) finish clean up: Yoda etc.
	 */
	function alg_wc_pdf_invoicing_number_to_words_en( $number ) {
		$number        = str_replace( array( ',', ' ' ), '' , trim( $number ) );
		if ( ! $number ) {
			return false;
		}
		$number        = ( int ) $number;
		$words         = array();
		$list1         = array( '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
			'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen' );
		$list2         = array( '', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred' );
		$list3         = array( '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
			'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
			'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion' );
		$number_length = strlen( $number );
		$levels        = ( int ) ( ( $number_length + 2 ) / 3 );
		$max_length    = $levels * 3;
		$number        = substr( '00' . $number, -$max_length );
		$number_levels = str_split( $number, 3 );
		for ( $i = 0; $i < count( $number_levels ); $i++ ) {
			$levels--;
			$hundreds = ( int ) ( $number_levels[ $i ] / 100 );
			$hundreds = ( $hundreds ? ' ' . $list1[ $hundreds ] . ' hundred' . ' ' : '' );
			$tens     = ( int ) ( $number_levels[ $i ] % 100 );
			$singles  = '';
			if ( $tens < 20 ) {
				$tens = ( $tens ? ' ' . $list1[ $tens ] . ' ' : '' );
			} else {
				$tens    = ( int ) ( $tens / 10 );
				$tens    = ' ' . $list2[ $tens ] . ' ';
				$singles = ( int ) ( $number_levels[ $i ] % 10 );
				$singles = ' ' . $list1[ $singles ] . ' ';
			}
			$words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $number_levels[ $i ] ) ) ? ' ' . $list3[ $levels ] . ' ' : '' );
		}
		$commas = count( $words );
		if ( $commas > 1 ) {
			$commas = $commas - 1;
		}
		return implode( ' ', $words );
	}
}

if ( ! function_exists( 'alg_wc_pdf_invoicing_number_to_words_lt' ) ) {
	/**
	 * alg_wc_pdf_invoicing_number_to_words_lt.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 *
	 * @return  string
	 *
	 * @todo    [dev] (maybe) finish clean up: camel-case; Yoda etc.
	 */
	function alg_wc_pdf_invoicing_number_to_words_lt( $number ) {
		$hyphen      = ' ';
		$conjunction = ' ';
		$separator   = ' ';
		$negative    = 'minus ';
		$decimal     = ' . ';
		$dictionary  = array(
			0                    => 'nulis',
			1                    => 'vienas',
			2                    => 'du',
			3                    => 'trys',
			4                    => 'keturi',
			5                    => 'penki',
			6                    => 'šeši',
			7                    => 'septyni',
			8                    => 'aštuoni',
			9                    => 'devyni',
			10                   => 'dešimt',
			11                   => 'vienuolika',
			12                   => 'dvylika',
			13                   => 'trylika',
			14                   => 'keturiolika',
			15                   => 'penkiolika',
			16                   => 'šešiolika',
			17                   => 'septyniolika',
			18                   => 'aštuoniolika',
			19                   => 'devyniolika',
			20                   => 'dvidešimt',
			30                   => 'trisdešimt',
			40                   => 'keturiasdešimt',
			50                   => 'penkiasdešimt',
			60                   => 'šešiasdešimt',
			70                   => 'septyniasdešimt',
			80                   => 'aštuoniasdešimt',
			90                   => 'devyniasdešimt',
			100                  => 'šimtas',
			200                  => 'šimtai',
			1000                 => 'tūkstantis',
			2000                 => 'tūkstančiai',
			10000                => 'tūkstančių',
			1000000              => 'milijonas',
			2000000              => 'milijonai',
			10000000             => 'milijonų',
			1000000000           => 'bilijonas',
			2000000000           => 'bilijonai',
			10000000000          => 'bilijonų',
			1000000000000        => 'trilijonas',
			2000000000000        => 'trilijonai',
			10000000000000       => 'trilijonų',
			1000000000000000     => 'kvadrilijonas',
			2000000000000000     => 'kvadrilijonai',
			10000000000000000    => 'kvadrilijonų',
			1000000000000000000  => 'kvintilijonas',
			2000000000000000000  => 'kvintilijonai',
			10000000000000000000 => 'kvintilijonų',
		);

		if ( ! is_numeric( $number ) ) {
			return false;
		}

		if ( ( $number >= 0 && ( int ) $number < 0 ) || ( int ) $number < 0 - PHP_INT_MAX ) {
			// overflow
			trigger_error(
				'alg_wc_pdf_invoicing_number_to_words_lt only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ( $number < 0 ) {
			return $negative . alg_wc_pdf_invoicing_number_to_words_lt( abs( $number ) );
		}

		$string = $fraction = null;

		if ( strpos( $number, '.' ) !== false ) {
			list( $number, $fraction ) = explode( '.', $number );
		}

		switch ( true ) {
			case $number < 21:
				$string = $dictionary[ $number ];
				break;
			case $number < 100:
				$tens   = ( ( int ) ( $number / 10 ) ) * 10;
				$units  = $number % 10;
				$string = $dictionary[ $tens ];
				if ( $units ) {
					$string .= $hyphen . $dictionary[ $units ];
				}
				break;
			case $number < 200:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[ $hundreds ] . ' ' . $dictionary[100];
				if ( $remainder ) {
					$string .= $conjunction . alg_wc_pdf_invoicing_number_to_words_lt( $remainder );
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[ $hundreds ] . ' ' . $dictionary[200];
				if ( $remainder ) {
					$string .= $conjunction . alg_wc_pdf_invoicing_number_to_words_lt( $remainder );
				}
				break;

			default:
				$baseUnit     = pow( 1000, floor( log( $number, 1000 ) ) );
				$numBaseUnits = ( int ) ( $number / $baseUnit );
				$number1      = ( string ) $number;
				if ( $numBaseUnits == 1 ) {
					$baseUnits = $baseUnit;
				} elseif ( $numBaseUnits < 10 ) {
					$baseUnits = $baseUnit * 2;
				} else {
					$baseUnits = $baseUnit * 10;
				}

				$remainder = $number % $baseUnit;
				$string    = alg_wc_pdf_invoicing_number_to_words_lt( $numBaseUnits ) . ' ' . $dictionary[ $baseUnits ];
				if ( $remainder ) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= alg_wc_pdf_invoicing_number_to_words_lt( $remainder );
				}
				break;
		}

		if ( null !== $fraction && is_numeric( $fraction ) ) {
			$string .= $decimal;
			$words   = array();
			foreach ( str_split( ( string ) $fraction ) as $number ) {
				$words[] = $dictionary[ $number ];
			}
			$string .= implode( ' ', $words );
		}

		return $string;
	}
}
