<?php
/**
 * PDF Invoicing for WooCommerce - Document Settings - Page Formats
 *
 * @version 1.5.0
 * @since   1.5.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PDF_Invoicing_Settings_Doc_Page_Formats' ) ) :

class Alg_WC_PDF_Invoicing_Settings_Doc_Page_Formats {

	/**
	 * Constructor.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_page_formats.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] (dev) reduce number of page formats?
	 */
	static function get_page_formats() {
		return array(
			'CUSTOM'                 => __( 'Custom', 'pdf-invoicing-for-woocommerce' ),
			'A0'                     => 'A0 (841 x 1189 mm / 33.11 x 46.81 in)',
			'A1'                     => 'A1 (594 x 841 mm / 23.39 x 33.11 in)',
			'A2'                     => 'A2 (420 x 594 mm / 16.54 x 23.39 in)',
			'A3'                     => 'A3 (297 x 420 mm / 11.69 x 16.54 in)',
			'A4'                     => 'A4 (210 x 297 mm / 8.27 x 11.69 in)',
			'A5'                     => 'A5 (148 x 210 mm / 5.83 x 8.27 in)',
			'A6'                     => 'A6 (105 x 148 mm / 4.13 x 5.83 in)',
			'A7'                     => 'A7 (74 x 105 mm / 2.91 x 4.13 in)',
			'A8'                     => 'A8 (52 x 74 mm / 2.05 x 2.91 in)',
			'A9'                     => 'A9 (37 x 52 mm / 1.46 x 2.05 in)',
			'A10'                    => 'A10 (26 x 37 mm / 1.02 x 1.46 in)',
			'A11'                    => 'A11 (18 x 26 mm / 0.71 x 1.02 in)',
			'A12'                    => 'A12 (13 x 18 mm / 0.51 x 0.71 in)',
			'B0'                     => 'B0 (1000 x 1414 mm / 39.37 x 55.67 in)',
			'B1'                     => 'B1 (707 x 1000 mm / 27.83 x 39.37 in)',
			'B2'                     => 'B2 (500 x 707 mm / 19.69 x 27.83 in)',
			'B3'                     => 'B3 (353 x 500 mm / 13.90 x 19.69 in)',
			'B4'                     => 'B4 (250 x 353 mm / 9.84 x 13.90 in)',
			'B5'                     => 'B5 (176 x 250 mm / 6.93 x 9.84 in)',
			'B6'                     => 'B6 (125 x 176 mm / 4.92 x 6.93 in)',
			'B7'                     => 'B7 (88 x 125 mm / 3.46 x 4.92 in)',
			'B8'                     => 'B8 (62 x 88 mm / 2.44 x 3.46 in)',
			'B9'                     => 'B9 (44 x 62 mm / 1.73 x 2.44 in)',
			'B10'                    => 'B10 (31 x 44 mm / 1.22 x 1.73 in)',
			'B11'                    => 'B11 (22 x 31 mm / 0.87 x 1.22 in)',
			'B12'                    => 'B12 (15 x 22 mm / 0.59 x 0.87 in)',
			'C0'                     => 'C0 (917 x 1297 mm / 36.10 x 51.06 in)',
			'C1'                     => 'C1 (648 x 917 mm / 25.51 x 36.10 in)',
			'C2'                     => 'C2 (458 x 648 mm / 18.03 x 25.51 in)',
			'C3'                     => 'C3 (324 x 458 mm / 12.76 x 18.03 in)',
			'C4'                     => 'C4 (229 x 324 mm / 9.02 x 12.76 in)',
			'C5'                     => 'C5 (162 x 229 mm / 6.38 x 9.02 in)',
			'C6'                     => 'C6 (114 x 162 mm / 4.49 x 6.38 in)',
			'C7'                     => 'C7 (81 x 114 mm / 3.19 x 4.49 in)',
			'C8'                     => 'C8 (57 x 81 mm / 2.24 x 3.19 in)',
			'C9'                     => 'C9 (40 x 57 mm / 1.57 x 2.24 in)',
			'C10'                    => 'C10 (28 x 40 mm / 1.10 x 1.57 in)',
			'C11'                    => 'C11 (20 x 28 mm / 0.79 x 1.10 in)',
			'C12'                    => 'C12 (14 x 20 mm / 0.55 x 0.79 in)',
			'C76'                    => 'C76 (81 x 162 mm / 3.19 x 6.38 in)',
			'DL'                     => 'DL (110 x 220 mm / 4.33 x 8.66 in)',
			'DLE'                    => 'DLE (114 x 225 mm / 4.49 x 8.86 in)',
			'DLX'                    => 'DLX (120 x 235 mm / 4.72 x 9.25 in)',
			'DLP'                    => 'DLP (99 x 210 mm / 3.90 x 8.27 in)',
			'E0'                     => 'E0 (879 x 1241 mm / 34.61 x 48.86 in)',
			'E1'                     => 'E1 (620 x 879 mm / 24.41 x 34.61 in)',
			'E2'                     => 'E2 (440 x 620 mm / 17.32 x 24.41 in)',
			'E3'                     => 'E3 (310 x 440 mm / 12.20 x 17.32 in)',
			'E4'                     => 'E4 (220 x 310 mm / 8.66 x 12.20 in)',
			'E5'                     => 'E5 (155 x 220 mm / 6.10 x 8.66 in)',
			'E6'                     => 'E6 (110 x 155 mm / 4.33 x 6.10 in)',
			'E7'                     => 'E7 (78 x 110 mm / 3.07 x 4.33 in)',
			'E8'                     => 'E8 (55 x 78 mm / 2.17 x 3.07 in)',
			'E9'                     => 'E9 (39 x 55 mm / 1.54 x 2.17 in)',
			'E10'                    => 'E10 (27 x 39 mm / 1.06 x 1.54 in)',
			'E11'                    => 'E11 (19 x 27 mm / 0.75 x 1.06 in)',
			'E12'                    => 'E12 (13 x 19 mm / 0.51 x 0.75 in)',
			'G0'                     => 'G0 (958 x 1354 mm / 37.72 x 53.31 in)',
			'G1'                     => 'G1 (677 x 958 mm / 26.65 x 37.72 in)',
			'G2'                     => 'G2 (479 x 677 mm / 18.86 x 26.65 in)',
			'G3'                     => 'G3 (338 x 479 mm / 13.31 x 18.86 in)',
			'G4'                     => 'G4 (239 x 338 mm / 9.41 x 13.31 in)',
			'G5'                     => 'G5 (169 x 239 mm / 6.65 x 9.41 in)',
			'G6'                     => 'G6 (119 x 169 mm / 4.69 x 6.65 in)',
			'G7'                     => 'G7 (84 x 119 mm / 3.31 x 4.69 in)',
			'G8'                     => 'G8 (59 x 84 mm / 2.32 x 3.31 in)',
			'G9'                     => 'G9 (42 x 59 mm / 1.65 x 2.32 in)',
			'G10'                    => 'G10 (29 x 42 mm / 1.14 x 1.65 in)',
			'G11'                    => 'G11 (21 x 29 mm / 0.83 x 1.14 in)',
			'G12'                    => 'G12 (14 x 21 mm / 0.55 x 0.83 in)',
			'RA0'                    => 'RA0 (860 x 1220 mm / 33.86 x 48.03 in)',
			'RA1'                    => 'RA1 (610 x 860 mm / 24.02 x 33.86 in)',
			'RA2'                    => 'RA2 (430 x 610 mm / 16.93 x 24.02 in)',
			'RA3'                    => 'RA3 (305 x 430 mm / 12.01 x 16.93 in)',
			'RA4'                    => 'RA4 (215 x 305 mm / 8.46 x 12.01 in)',
			'SRA0'                   => 'SRA0 (900 x 1280 mm / 35.43 x 50.39 in)',
			'SRA1'                   => 'SRA1 (640 x 900 mm / 25.20 x 35.43 in)',
			'SRA2'                   => 'SRA2 (450 x 640 mm / 17.72 x 25.20 in)',
			'SRA3'                   => 'SRA3 (320 x 450 mm / 12.60 x 17.72 in)',
			'SRA4'                   => 'SRA4 (225 x 320 mm / 8.86 x 12.60 in)',
			'4A0'                    => '4A0 (1682 x 2378 mm / 66.22 x 93.62 in)',
			'2A0'                    => '2A0 (1189 x 1682 mm / 46.81 x 66.22 in)',
			'A2_EXTRA'               => 'A2_EXTRA (445 x 619 mm / 17.52 x 24.37 in)',
			'A3+'                    => 'A3+ (329 x 483 mm / 12.95 x 19.02 in)',
			'A3_EXTRA'               => 'A3_EXTRA (322 x 445 mm / 12.68 x 17.52 in)',
			'A3_SUPER'               => 'A3_SUPER (305 x 508 mm / 12.01 x 20.00 in)',
			'SUPER_A3'               => 'SUPER_A3 (305 x 487 mm / 12.01 x 19.17 in)',
			'A4_EXTRA'               => 'A4_EXTRA (235 x 322 mm / 9.25 x 12.68 in)',
			'A4_SUPER'               => 'A4_SUPER (229 x 322 mm / 9.02 x 12.68 in)',
			'SUPER_A4'               => 'SUPER_A4 (227 x 356 mm / 8.94 x 14.02 in)',
			'A4_LONG'                => 'A4_LONG (210 x 348 mm / 8.27 x 13.70 in)',
			'F4'                     => 'F4 (210 x 330 mm / 8.27 x 12.99 in)',
			'SO_B5_EXTRA'            => 'SO_B5_EXTRA (202 x 276 mm / 7.95 x 10.87 in)',
			'A5_EXTRA'               => 'A5_EXTRA (173 x 235 mm / 6.81 x 9.25 in)',
			'ANSI_E'                 => 'ANSI_E (864 x 1118 mm / 34.00 x 44.00 in)',
			'ANSI_D'                 => 'ANSI_D (559 x 864 mm / 22.00 x 34.00 in)',
			'ANSI_C'                 => 'ANSI_C (432 x 559 mm / 17.00 x 22.00 in)',
			'ANSI_B'                 => 'ANSI_B (279 x 432 mm / 11.00 x 17.00 in)',
			'ANSI_A'                 => 'ANSI_A (216 x 279 mm / 8.50 x 11.00 in)',
			'USLEDGER'               => 'USLEDGER (432 x 279 mm / 17.00 x 11.00 in)',
			'LEDGER'                 => 'LEDGER (432 x 279 mm / 17.00 x 11.00 in)',
			'ORGANIZERK'             => 'ORGANIZERK (279 x 432 mm / 11.00 x 17.00 in)',
			'BIBLE'                  => 'BIBLE (279 x 432 mm / 11.00 x 17.00 in)',
			'USTABLOID'              => 'USTABLOID (279 x 432 mm / 11.00 x 17.00 in)',
			'TABLOID'                => 'TABLOID (279 x 432 mm / 11.00 x 17.00 in)',
			'ORGANIZERM'             => 'ORGANIZERM (216 x 279 mm / 8.50 x 11.00 in)',
			'USLETTER'               => 'USLETTER (216 x 279 mm / 8.50 x 11.00 in)',
			'LETTER'                 => 'LETTER (216 x 279 mm / 8.50 x 11.00 in)',
			'USLEGAL'                => 'USLEGAL (216 x 356 mm / 8.50 x 14.00 in)',
			'LEGAL'                  => 'LEGAL (216 x 356 mm / 8.50 x 14.00 in)',
			'GOVERNMENTLETTER'       => 'GOVERNMENTLETTER (203 x 267 mm / 8.00 x 10.50 in)',
			'GLETTER'                => 'GLETTER (203 x 267 mm / 8.00 x 10.50 in)',
			'JUNIORLEGAL'            => 'JUNIORLEGAL (203 x 127 mm / 8.00 x 5.00 in)',
			'JLEGAL'                 => 'JLEGAL (203 x 127 mm / 8.00 x 5.00 in)',
			'QUADDEMY'               => 'QUADDEMY (889 x 1143 mm / 35.00 x 45.00 in)',
			'SUPER_B'                => 'SUPER_B (330 x 483 mm / 13.00 x 19.00 in)',
			'QUARTO'                 => 'QUARTO (229 x 279 mm / 9.00 x 11.00 in)',
			'GOVERNMENTLEGAL'        => 'GOVERNMENTLEGAL (216 x 330 mm / 8.50 x 13.00 in)',
			'FOLIO'                  => 'FOLIO (216 x 330 mm / 8.50 x 13.00 in)',
			'MONARCH'                => 'MONARCH (184 x 267 mm / 7.25 x 10.50 in)',
			'EXECUTIVE'              => 'EXECUTIVE (184 x 267 mm / 7.25 x 10.50 in)',
			'ORGANIZERL'             => 'ORGANIZERL (140 x 216 mm / 5.50 x 8.50 in)',
			'STATEMENT'              => 'STATEMENT (140 x 216 mm / 5.50 x 8.50 in)',
			'MEMO'                   => 'MEMO (140 x 216 mm / 5.50 x 8.50 in)',
			'FOOLSCAP'               => 'FOOLSCAP (210 x 330 mm / 8.27 x 13.00 in)',
			'COMPACT'                => 'COMPACT (108 x 171 mm / 4.25 x 6.75 in)',
			'ORGANIZERJ'             => 'ORGANIZERJ (70 x 127 mm / 2.75 x 5.00 in)',
			'P1'                     => 'P1 (560 x 860 mm / 22.05 x 33.86 in)',
			'P2'                     => 'P2 (430 x 560 mm / 16.93 x 22.05 in)',
			'P3'                     => 'P3 (280 x 430 mm / 11.02 x 16.93 in)',
			'P4'                     => 'P4 (215 x 280 mm / 8.46 x 11.02 in)',
			'P5'                     => 'P5 (140 x 215 mm / 5.51 x 8.46 in)',
			'P6'                     => 'P6 (107 x 140 mm / 4.21 x 5.51 in)',
			'ARCH_E'                 => 'ARCH_E (914 x 1219 mm / 36.00 x 48.00 in)',
			'ARCH_E1'                => 'ARCH_E1 (762 x 1067 mm / 30.00 x 42.00 in)',
			'ARCH_D'                 => 'ARCH_D (610 x 914 mm / 24.00 x 36.00 in)',
			'BROADSHEET'             => 'BROADSHEET (457 x 610 mm / 18.00 x 24.00 in)',
			'ARCH_C'                 => 'ARCH_C (457 x 610 mm / 18.00 x 24.00 in)',
			'ARCH_B'                 => 'ARCH_B (305 x 457 mm / 12.00 x 18.00 in)',
			'ARCH_A'                 => 'ARCH_A (229 x 305 mm / 9.00 x 12.00 in)',
			'ANNENV_A2'              => 'ANNENV_A2 (111 x 146 mm / 4.37 x 5.75 in)',
			'ANNENV_A6'              => 'ANNENV_A6 (121 x 165 mm / 4.75 x 6.50 in)',
			'ANNENV_A7'              => 'ANNENV_A7 (133 x 184 mm / 5.25 x 7.25 in)',
			'ANNENV_A8'              => 'ANNENV_A8 (140 x 206 mm / 5.50 x 8.12 in)',
			'ANNENV_A10'             => 'ANNENV_A10 (159 x 244 mm / 6.25 x 9.62 in)',
			'ANNENV_SLIM'            => 'ANNENV_SLIM (98 x 225 mm / 3.87 x 8.87 in)',
			'COMMENV_N6_1/4'         => 'COMMENV_N6_1/4 (89 x 152 mm / 3.50 x 6.00 in)',
			'COMMENV_N6_3/4'         => 'COMMENV_N6_3/4 (92 x 165 mm / 3.62 x 6.50 in)',
			'COMMENV_N8'             => 'COMMENV_N8 (98 x 191 mm / 3.87 x 7.50 in)',
			'COMMENV_N9'             => 'COMMENV_N9 (98 x 225 mm / 3.87 x 8.87 in)',
			'COMMENV_N10'            => 'COMMENV_N10 (105 x 241 mm / 4.12 x 9.50 in)',
			'COMMENV_N11'            => 'COMMENV_N11 (114 x 263 mm / 4.50 x 10.37 in)',
			'COMMENV_N12'            => 'COMMENV_N12 (121 x 279 mm / 4.75 x 11.00 in)',
			'COMMENV_N14'            => 'COMMENV_N14 (127 x 292 mm / 5.00 x 11.50 in)',
			'CATENV_N1'              => 'CATENV_N1 (152 x 229 mm / 6.00 x 9.00 in)',
			'CATENV_N1_3/4'          => 'CATENV_N1_3/4 (165 x 241 mm / 6.50 x 9.50 in)',
			'CATENV_N2'              => 'CATENV_N2 (165 x 254 mm / 6.50 x 10.00 in)',
			'CATENV_N3'              => 'CATENV_N3 (178 x 254 mm / 7.00 x 10.00 in)',
			'CATENV_N6'              => 'CATENV_N6 (191 x 267 mm / 7.50 x 10.50 in)',
			'CATENV_N7'              => 'CATENV_N7 (203 x 279 mm / 8.00 x 11.00 in)',
			'CATENV_N8'              => 'CATENV_N8 (210 x 286 mm / 8.25 x 11.25 in)',
			'CATENV_N9_1/2'          => 'CATENV_N9_1/2 (216 x 267 mm / 8.50 x 10.50 in)',
			'CATENV_N9_3/4'          => 'CATENV_N9_3/4 (222 x 286 mm / 8.75 x 11.25 in)',
			'CATENV_N10_1/2'         => 'CATENV_N10_1/2 (229 x 305 mm / 9.00 x 12.00 in)',
			'CATENV_N12_1/2'         => 'CATENV_N12_1/2 (241 x 318 mm / 9.50 x 12.50 in)',
			'CATENV_N13_1/2'         => 'CATENV_N13_1/2 (254 x 330 mm / 10.00 x 13.00 in)',
			'CATENV_N14_1/4'         => 'CATENV_N14_1/4 (286 x 311 mm / 11.25 x 12.25 in)',
			'CATENV_N14_1/2'         => 'CATENV_N14_1/2 (292 x 368 mm / 11.50 x 14.50 in)',
			'JIS_B0'                 => 'JIS_B0 (1030 x 1456 mm / 40.55 x 57.32 in)',
			'JIS_B1'                 => 'JIS_B1 (728 x 1030 mm / 28.66 x 40.55 in)',
			'JIS_B2'                 => 'JIS_B2 (515 x 728 mm / 20.28 x 28.66 in)',
			'JIS_B3'                 => 'JIS_B3 (364 x 515 mm / 14.33 x 20.28 in)',
			'JIS_B4'                 => 'JIS_B4 (257 x 364 mm / 10.12 x 14.33 in)',
			'JIS_B5'                 => 'JIS_B5 (182 x 257 mm / 7.17 x 10.12 in)',
			'JIS_B6'                 => 'JIS_B6 (128 x 182 mm / 5.04 x 7.17 in)',
			'JIS_B7'                 => 'JIS_B7 (91 x 128 mm / 3.58 x 5.04 in)',
			'JIS_B8'                 => 'JIS_B8 (64 x 91 mm / 2.52 x 3.58 in)',
			'JIS_B9'                 => 'JIS_B9 (45 x 64 mm / 1.77 x 2.52 in)',
			'JIS_B10'                => 'JIS_B10 (32 x 45 mm / 1.26 x 1.77 in)',
			'JIS_B11'                => 'JIS_B11 (22 x 32 mm / 0.87 x 1.26 in)',
			'JIS_B12'                => 'JIS_B12 (16 x 22 mm / 0.63 x 0.87 in)',
			'PA0'                    => 'PA0 (840 x 1120 mm / 33.07 x 44.09 in)',
			'PA1'                    => 'PA1 (560 x 840 mm / 22.05 x 33.07 in)',
			'PA2'                    => 'PA2 (420 x 560 mm / 16.54 x 22.05 in)',
			'PA3'                    => 'PA3 (280 x 420 mm / 11.02 x 16.54 in)',
			'PA4'                    => 'PA4 (210 x 280 mm / 8.27 x 11.02 in)',
			'PA5'                    => 'PA5 (140 x 210 mm / 5.51 x 8.27 in)',
			'PA6'                    => 'PA6 (105 x 140 mm / 4.13 x 5.51 in)',
			'PA7'                    => 'PA7 (70 x 105 mm / 2.76 x 4.13 in)',
			'PA8'                    => 'PA8 (52 x 70 mm / 2.05 x 2.76 in)',
			'PA9'                    => 'PA9 (35 x 52 mm / 1.38 x 2.05 in)',
			'PA10'                   => 'PA10 (26 x 35 mm / 1.02 x 1.38 in)',
			'PASSPORT_PHOTO'         => 'PASSPORT_PHOTO (35 x 45 mm / 1.38 x 1.77 in)',
			'E'                      => 'E (82 x 120 mm / 3.25 x 4.72 in)',
			'L'                      => 'L (89 x 127 mm / 3.50 x 5.00 in)',
			'3R'                     => '3R (89 x 127 mm / 3.50 x 5.00 in)',
			'KG'                     => 'KG (102 x 152 mm / 4.02 x 5.98 in)',
			'4R'                     => '4R (102 x 152 mm / 4.02 x 5.98 in)',
			'4D'                     => '4D (120 x 152 mm / 4.72 x 5.98 in)',
			'2L'                     => '2L (127 x 178 mm / 5.00 x 7.01 in)',
			'5R'                     => '5R (127 x 178 mm / 5.00 x 7.01 in)',
			'8P'                     => '8P (152 x 203 mm / 5.98 x 7.99 in)',
			'6R'                     => '6R (152 x 203 mm / 5.98 x 7.99 in)',
			'6P'                     => '6P (203 x 254 mm / 7.99 x 10.00 in)',
			'8R'                     => '8R (203 x 254 mm / 7.99 x 10.00 in)',
			'6PW'                    => '6PW (203 x 305 mm / 7.99 x 12.01 in)',
			'S8R'                    => 'S8R (203 x 305 mm / 7.99 x 12.01 in)',
			'4P'                     => '4P (254 x 305 mm / 10.00 x 12.01 in)',
			'10R'                    => '10R (254 x 305 mm / 10.00 x 12.01 in)',
			'4PW'                    => '4PW (254 x 381 mm / 10.00 x 15.00 in)',
			'S10R'                   => 'S10R (254 x 381 mm / 10.00 x 15.00 in)',
			'11R'                    => '11R (279 x 356 mm / 10.98 x 14.02 in)',
			'S11R'                   => 'S11R (279 x 432 mm / 10.98 x 17.01 in)',
			'12R'                    => '12R (305 x 381 mm / 12.01 x 15.00 in)',
			'S12R'                   => 'S12R (305 x 456 mm / 12.01 x 17.95 in)',
			'NEWSPAPER_BROADSHEET'   => 'NEWSPAPER_BROADSHEET (750 x 600 mm / 29.53 x 23.62 in)',
			'NEWSPAPER_BERLINER'     => 'NEWSPAPER_BERLINER (470 x 315 mm / 18.50 x 12.40 in)',
			'NEWSPAPER_TABLOID'      => 'NEWSPAPER_TABLOID (430 x 280 mm / 16.93 x 11.02 in)',
			'NEWSPAPER_COMPACT'      => 'NEWSPAPER_COMPACT (430 x 280 mm / 16.93 x 11.02 in)',
			'CREDIT_CARD'            => 'CREDIT_CARD (54 x 86 mm / 2.13 x 3.37 in)',
			'BUSINESS_CARD'          => 'BUSINESS_CARD (54 x 86 mm / 2.13 x 3.37 in)',
			'BUSINESS_CARD_ISO7810'  => 'BUSINESS_CARD_ISO7810 (54 x 86 mm / 2.13 x 3.37 in)',
			'BUSINESS_CARD_ISO216'   => 'BUSINESS_CARD_ISO216 (52 x 74 mm / 2.05 x 2.91 in)',
			'BUSINESS_CARD_IT'       => 'BUSINESS_CARD_IT (55 x 85 mm / 2.17 x 3.35 in)',
			'BUSINESS_CARD_UK'       => 'BUSINESS_CARD_UK (55 x 85 mm / 2.17 x 3.35 in)',
			'BUSINESS_CARD_FR'       => 'BUSINESS_CARD_FR (55 x 85 mm / 2.17 x 3.35 in)',
			'BUSINESS_CARD_DE'       => 'BUSINESS_CARD_DE (55 x 85 mm / 2.17 x 3.35 in)',
			'BUSINESS_CARD_ES'       => 'BUSINESS_CARD_ES (55 x 85 mm / 2.17 x 3.35 in)',
			'BUSINESS_CARD_CA'       => 'BUSINESS_CARD_CA (51 x 89 mm / 2.01 x 3.50 in)',
			'BUSINESS_CARD_US'       => 'BUSINESS_CARD_US (51 x 89 mm / 2.01 x 3.50 in)',
			'BUSINESS_CARD_JP'       => 'BUSINESS_CARD_JP (55 x 91 mm / 2.17 x 3.58 in)',
			'BUSINESS_CARD_HK'       => 'BUSINESS_CARD_HK (54 x 90 mm / 2.13 x 3.54 in)',
			'BUSINESS_CARD_AU'       => 'BUSINESS_CARD_AU (55 x 90 mm / 2.17 x 3.54 in)',
			'BUSINESS_CARD_DK'       => 'BUSINESS_CARD_DK (55 x 90 mm / 2.17 x 3.54 in)',
			'BUSINESS_CARD_SE'       => 'BUSINESS_CARD_SE (55 x 90 mm / 2.17 x 3.54 in)',
			'BUSINESS_CARD_RU'       => 'BUSINESS_CARD_RU (50 x 90 mm / 1.97 x 3.54 in)',
			'BUSINESS_CARD_CZ'       => 'BUSINESS_CARD_CZ (50 x 90 mm / 1.97 x 3.54 in)',
			'BUSINESS_CARD_FI'       => 'BUSINESS_CARD_FI (50 x 90 mm / 1.97 x 3.54 in)',
			'BUSINESS_CARD_HU'       => 'BUSINESS_CARD_HU (50 x 90 mm / 1.97 x 3.54 in)',
			'BUSINESS_CARD_IL'       => 'BUSINESS_CARD_IL (50 x 90 mm / 1.97 x 3.54 in)',
			'4SHEET'                 => '4SHEET (1016 x 1524 mm / 40.00 x 60.00 in)',
			'6SHEET'                 => '6SHEET (1200 x 1800 mm / 47.24 x 70.87 in)',
			'12SHEET'                => '12SHEET (3048 x 1524 mm / 120.00 x 60.00 in)',
			'16SHEET'                => '16SHEET (2032 x 3048 mm / 80.00 x 120.00)',
			'32SHEET'                => '32SHEET (4064 x 3048 mm / 160.00 x 120.00)',
			'48SHEET'                => '48SHEET (6096 x 3048 mm / 240.00 x 120.00)',
			'64SHEET'                => '64SHEET (8128 x 3048 mm / 320.00 x 120.00)',
			'96SHEET'                => '96SHEET (12192 x 3048 mm / 480.00 x 120.00)',
			'EN_EMPEROR'             => 'EN_EMPEROR (1219 x 1829 mm / 48.00 x 72.00 in)',
			'EN_ANTIQUARIAN'         => 'EN_ANTIQUARIAN (787 x 1346 mm / 31.00 x 53.00 in)',
			'EN_GRAND_EAGLE'         => 'EN_GRAND_EAGLE (730 x 1067 mm / 28.75 x 42.00 in)',
			'EN_DOUBLE_ELEPHANT'     => 'EN_DOUBLE_ELEPHANT (679 x 1016 mm / 26.75 x 40.00 in)',
			'EN_ATLAS'               => 'EN_ATLAS (660 x 864 mm / 26.00 x 34.00 in)',
			'EN_COLOMBIER'           => 'EN_COLOMBIER (597 x 876 mm / 23.50 x 34.50 in)',
			'EN_ELEPHANT'            => 'EN_ELEPHANT (584 x 711 mm / 23.00 x 28.00 in)',
			'EN_DOUBLE_DEMY'         => 'EN_DOUBLE_DEMY (572 x 902 mm / 22.50 x 35.50 in)',
			'EN_IMPERIAL'            => 'EN_IMPERIAL (559 x 762 mm / 22.00 x 30.00 in)',
			'EN_PRINCESS'            => 'EN_PRINCESS (546 x 711 mm / 21.50 x 28.00 in)',
			'EN_CARTRIDGE'           => 'EN_CARTRIDGE (533 x 660 mm / 21.00 x 26.00 in)',
			'EN_DOUBLE_LARGE_POST'   => 'EN_DOUBLE_LARGE_POST (533 x 838 mm / 21.00 x 33.00 in)',
			'EN_ROYAL'               => 'EN_ROYAL (508 x 635 mm / 20.00 x 25.00 in)',
			'EN_SHEET'               => 'EN_SHEET (495 x 597 mm / 19.50 x 23.50 in)',
			'EN_HALF_POST'           => 'EN_HALF_POST (495 x 597 mm / 19.50 x 23.50 in)',
			'EN_SUPER_ROYAL'         => 'EN_SUPER_ROYAL (483 x 686 mm / 19.00 x 27.00 in)',
			'EN_DOUBLE_POST'         => 'EN_DOUBLE_POST (483 x 775 mm / 19.00 x 30.50 in)',
			'EN_MEDIUM'              => 'EN_MEDIUM (445 x 584 mm / 17.50 x 23.00 in)',
			'EN_DEMY'                => 'EN_DEMY (445 x 572 mm / 17.50 x 22.50 in)',
			'EN_LARGE_POST'          => 'EN_LARGE_POST (419 x 533 mm / 16.50 x 21.00 in)',
			'EN_COPY_DRAUGHT'        => 'EN_COPY_DRAUGHT (406 x 508 mm / 16.00 x 20.00 in)',
			'EN_POST'                => 'EN_POST (394 x 489 mm / 15.50 x 19.25 in)',
			'EN_CROWN'               => 'EN_CROWN (381 x 508 mm / 15.00 x 20.00 in)',
			'EN_PINCHED_POST'        => 'EN_PINCHED_POST (375 x 470 mm / 14.75 x 18.50 in)',
			'EN_BRIEF'               => 'EN_BRIEF (343 x 406 mm / 13.50 x 16.00 in)',
			'EN_FOOLSCAP'            => 'EN_FOOLSCAP (343 x 432 mm / 13.50 x 17.00 in)',
			'EN_SMALL_FOOLSCAP'      => 'EN_SMALL_FOOLSCAP (337 x 419 mm / 13.25 x 16.50 in)',
			'EN_POTT'                => 'EN_POTT (318 x 381 mm / 12.50 x 15.00 in)',
			'BE_GRAND_AIGLE'         => 'BE_GRAND_AIGLE (700 x 1040 mm / 27.56 x 40.94 in)',
			'BE_COLOMBIER'           => 'BE_COLOMBIER (620 x 850 mm / 24.41 x 33.46 in)',
			'BE_DOUBLE_CARRE'        => 'BE_DOUBLE_CARRE (620 x 920 mm / 24.41 x 36.22 in)',
			'BE_ELEPHANT'            => 'BE_ELEPHANT (616 x 770 mm / 24.25 x 30.31 in)',
			'BE_PETIT_AIGLE'         => 'BE_PETIT_AIGLE (600 x 840 mm / 23.62 x 33.07 in)',
			'BE_GRAND_JESUS'         => 'BE_GRAND_JESUS (550 x 730 mm / 21.65 x 28.74 in)',
			'BE_JESUS'               => 'BE_JESUS (540 x 730 mm / 21.26 x 28.74 in)',
			'BE_RAISIN'              => 'BE_RAISIN (500 x 650 mm / 19.69 x 25.59 in)',
			'BE_GRAND_MEDIAN'        => 'BE_GRAND_MEDIAN (460 x 605 mm / 18.11 x 23.82 in)',
			'BE_DOUBLE_POSTE'        => 'BE_DOUBLE_POSTE (435 x 565 mm / 17.13 x 22.24 in)',
			'BE_COQUILLE'            => 'BE_COQUILLE (430 x 560 mm / 16.93 x 22.05 in)',
			'BE_PETIT_MEDIAN'        => 'BE_PETIT_MEDIAN (415 x 530 mm / 16.34 x 20.87 in)',
			'BE_RUCHE'               => 'BE_RUCHE (360 x 460 mm / 14.17 x 18.11 in)',
			'BE_PROPATRIA'           => 'BE_PROPATRIA (345 x 430 mm / 13.58 x 16.93 in)',
			'BE_LYS'                 => 'BE_LYS (317 x 397 mm / 12.48 x 15.63 in)',
			'BE_POT'                 => 'BE_POT (307 x 384 mm / 12.09 x 15.12 in)',
			'BE_ROSETTE'             => 'BE_ROSETTE (270 x 347 mm / 10.63 x 13.66 in)',
			'FR_UNIVERS'             => 'FR_UNIVERS (1000 x 1300 mm / 39.37 x 51.18 in)',
			'FR_DOUBLE_COLOMBIER'    => 'FR_DOUBLE_COLOMBIER (900 x 1260 mm / 35.43 x 49.61 in)',
			'FR_GRANDE_MONDE'        => 'FR_GRANDE_MONDE (900 x 1260 mm / 35.43 x 49.61 in)',
			'FR_DOUBLE_SOLEIL'       => 'FR_DOUBLE_SOLEIL (800 x 1200 mm / 31.50 x 47.24 in)',
			'FR_DOUBLE_JESUS'        => 'FR_DOUBLE_JESUS (760 x 1120 mm / 29.92 x 44.09 in)',
			'FR_GRAND_AIGLE'         => 'FR_GRAND_AIGLE (750 x 1060 mm / 29.53 x 41.73 in)',
			'FR_PETIT_AIGLE'         => 'FR_PETIT_AIGLE (700 x 940 mm / 27.56 x 37.01 in)',
			'FR_DOUBLE_RAISIN'       => 'FR_DOUBLE_RAISIN (650 x 1000 mm / 25.59 x 39.37 in)',
			'FR_JOURNAL'             => 'FR_JOURNAL (650 x 940 mm / 25.59 x 37.01 in)',
			'FR_COLOMBIER_AFFICHE'   => 'FR_COLOMBIER_AFFICHE (630 x 900 mm / 24.80 x 35.43 in)',
			'FR_DOUBLE_CAVALIER'     => 'FR_DOUBLE_CAVALIER (620 x 920 mm / 24.41 x 36.22 in)',
			'FR_CLOCHE'              => 'FR_CLOCHE (600 x 800 mm / 23.62 x 31.50 in)',
			'FR_SOLEIL'              => 'FR_SOLEIL (600 x 800 mm / 23.62 x 31.50 in)',
			'FR_DOUBLE_CARRE'        => 'FR_DOUBLE_CARRE (560 x 900 mm / 22.05 x 35.43 in)',
			'FR_DOUBLE_COQUILLE'     => 'FR_DOUBLE_COQUILLE (560 x 880 mm / 22.05 x 34.65 in)',
			'FR_JESUS'               => 'FR_JESUS (560 x 760 mm / 22.05 x 29.92 in)',
			'FR_RAISIN'              => 'FR_RAISIN (500 x 650 mm / 19.69 x 25.59 in)',
			'FR_CAVALIER'            => 'FR_CAVALIER (460 x 620 mm / 18.11 x 24.41 in)',
			'FR_DOUBLE_COURONNE'     => 'FR_DOUBLE_COURONNE (460 x 720 mm / 18.11 x 28.35 in)',
			'FR_CARRE'               => 'FR_CARRE (450 x 560 mm / 17.72 x 22.05 in)',
			'FR_COQUILLE'            => 'FR_COQUILLE (440 x 560 mm / 17.32 x 22.05 in)',
			'FR_DOUBLE_TELLIERE'     => 'FR_DOUBLE_TELLIERE (440 x 680 mm / 17.32 x 26.77 in)',
			'FR_DOUBLE_CLOCHE'       => 'FR_DOUBLE_CLOCHE (400 x 600 mm / 15.75 x 23.62 in)',
			'FR_DOUBLE_POT'          => 'FR_DOUBLE_POT (400 x 620 mm / 15.75 x 24.41 in)',
			'FR_ECU'                 => 'FR_ECU (400 x 520 mm / 15.75 x 20.47 in)',
			'FR_COURONNE'            => 'FR_COURONNE (360 x 460 mm / 14.17 x 18.11 in)',
			'FR_TELLIERE'            => 'FR_TELLIERE (340 x 440 mm / 13.39 x 17.32 in)',
			'FR_POT'                 => 'FR_POT (310 x 400 mm / 12.20 x 15.75 in)',
		);
	}

}

endif;
