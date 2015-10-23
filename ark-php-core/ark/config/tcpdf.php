<?php

/**
 * tcpdf.php
 *
 * @package     ark-php
 * @author      Jonathon <Jonathon@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   Jonathon <Jonathon@xinix.co.id>
 *
 *
 */

/***************************************************************************
 * PATH CONFIGURATION PARAMETERS
 **************************************************************************/


	/************************************************************
	 * TCPDF installation directory
	 * ----------------------------------------------------------
	 * This is the base installation directory for your TCPDF
	 * package (the folder that contains tcpdf.php).
	 *
	 * ADD TRAILING SLASH!
	 ***********************************************************/

	$config['tcpdf']['base_directory'] = APPPATH.'third_party/tcpdf/';


	/************************************************************
	 * TCPDF installation directory URL
	 * ----------------------------------------------------------
	 * This is the URL path to the TCPDF base installation
	 * directory (the URL equivalent to the 'base_directory'
	 * option above).
	 *
	 * ADD TRAILING SLASH!
	 ***********************************************************/

	$config['tcpdf']['base_url'] = 'http://myaccount.goyoders.dev/app/3rdparty/tcpdf/';


	/************************************************************
	 * TCPDF fonts directory
	 * ----------------------------------------------------------
	 * This is the directory of the TCPDF fonts folder.
	 * Use $config['tcpdf']['base_directory'].'fonts/old/' for old non-UTF8
	 * fonts.
	 *
	 * ADD TRAILING SLASH!
	 ***********************************************************/

	$config['tcpdf']['fonts_directory'] = $config['tcpdf']['base_directory'].'fonts/';


	/************************************************************
	 * TCPDF disk cache settings
	 * ----------------------------------------------------------
	 * Enable caching; Cache directory for TCPDF (make sure that
	 * it is writable by the webserver).
	 *
	 * ADD TRAILING SLASH!
	 ***********************************************************/

	$config['tcpdf']['enable_disk_cache'] = FALSE;
	$config['tcpdf']['cache_directory'] = $config['tcpdf']['base_directory'].'cache/';


	/************************************************************
	 * TCPDF image directory
	 * ----------------------------------------------------------
	 * This is the image directory for TCPDF. This is where you
	 * can store images to use in your PDF files.
	 *
	 * ADD TRAILING SLASH!
	 ***********************************************************/

	$config['tcpdf']['image_directory'] = $config['tcpdf']['base_directory'].'images/';


	/************************************************************
	 * TCPDF default (blank) image
	 * ----------------------------------------------------------
	 * This is the path and filename to the default (blank)
	 * image.
	 ***********************************************************/

	$config['tcpdf']['blank_image'] = $config['tcpdf']['image_directory'].'_blank.png';


	/************************************************************
	 * TCPDF language settings file
	 * ----------------------------------------------------------
	 * Directory and filename of the language settings file
	 ***********************************************************/

	$config['tcpdf']['language_file'] = $config['tcpdf']['base_directory'].'config/lang/eng.php';



/***************************************************************************
 * DOCUMENT CONFIGURATION PARAMETERS
 **************************************************************************/


	/************************************************************
	 * TCPDF default page format
	 * ----------------------------------------------------------
	 * This is the default page size. Supported formats include:
	 *
	 * 4A0, 2A0, A0, A1, A2, A3, A4, A5, A6, A7, A8, A9, A10, B0,
	 * B1, B2, B3, B4, B5, B6, B7, B8, B9, B10, C0, C1, C2, C3,
	 * C4, C5, C6, C7, C8, C9, C10, RA0, RA1, RA2, RA3, RA4,
	 * SRA0, SRA1, SRA2, SRA3, SRA4, LETTER, LEGAL, EXECUTIVE,
	 * FOLIO
	 *
	 * Or, you can optionally specify a custom format in the form
	 * of a two-element array containing the width and the height.
	 ************************************************************/

	$config['tcpdf']['page_format'] = 'LETTER';


	/************************************************************
	 * TCPDF default page orientation
	 * ----------------------------------------------------------
	 * Default page layout.
	 * P = portrait, L = landscape
	 ***********************************************************/

	$config['tcpdf']['page_orientation'] = 'P';


	/************************************************************
	 * TCPDF default unit of measure
	 * ----------------------------------------------------------
	 * Unit of measure.
	 * mm = millimeters, cm = centimeters,
	 * pt = points, in = inches
	 *
	 * 1 point = 1/72 in = ~0.35 mm
	 * 1 inch = 2.54 cm
	 ***********************************************************/

	$config['tcpdf']['page_unit'] = 'mm';


	/************************************************************
	 * TCPDF auto page break
	 * ----------------------------------------------------------
	 * Enables automatic flowing of content to the next page if
	 * you run out of room on the current page.
	 ***********************************************************/

	$config['tcpdf']['page_break_auto'] = TRUE;


	/************************************************************
	 * TCPDF text encoding
	 * ----------------------------------------------------------
	 * Specify TRUE if the input text you will be using is
	 * unicode, and specify the default encoding.
	 ***********************************************************/

	$config['tcpdf']['unicode'] = TRUE;
	$config['tcpdf']['encoding'] = 'UTF-8';


	/************************************************************
	 * TCPDF default document creator and author strings
	 ***********************************************************/

	$config['tcpdf']['creator'] = 'TCPDF';
	$config['tcpdf']['author'] = 'TCPDF';


	/************************************************************
	 * TCPDF default page margin
	 * ----------------------------------------------------------
	 * Top, bottom, left, right, header, and footer margin
	 * settings in the default unit of measure.
	 ***********************************************************/

	$config['tcpdf']['margin_top']    = 27;
	$config['tcpdf']['margin_bottom'] = 27;
	$config['tcpdf']['margin_left']   = 15;
	$config['tcpdf']['margin_right']  = 15;


	/************************************************************
	 * TCPDF default font settings
	 * ----------------------------------------------------------
	 * Page font, font size, header and footer fonts,
	 * HTML <small> font size ratio
	 ***********************************************************/

	$config['tcpdf']['page_font'] = 'helvetica';
	$config['tcpdf']['page_font_size'] = 10;

	$config['tcpdf']['small_font_ratio'] = 2/3;


	/************************************************************
	 * TCPDF header settings
	 * ----------------------------------------------------------
	 * Enable the header, set the font, default text, margin,
	 * description string, and logo
	 ***********************************************************/

	$config['tcpdf']['header_on'] = TRUE;
	$config['tcpdf']['header_font'] = $config['tcpdf']['page_font'];
	$config['tcpdf']['header_font_size'] = 10;
	$config['tcpdf']['header_margin'] = 5;
	$config['tcpdf']['header_title'] = 'TCPDF Example';
	$config['tcpdf']['header_string'] = "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org";
	$config['tcpdf']['header_logo'] = 'tcpdf_logo.jpg';
	$config['tcpdf']['header_logo_width'] = 30;


	/************************************************************
	 * TCPDF footer settings
	 * ----------------------------------------------------------
	 * Enable the header, set the font, default text, and margin
	 ***********************************************************/

	$config['tcpdf']['footer_on'] = TRUE;
	$config['tcpdf']['footer_font'] = $config['tcpdf']['page_font'];
	$config['tcpdf']['footer_font_size'] = 8;
	$config['tcpdf']['footer_margin'] = 10;


	/************************************************************
	 * TCPDF image scale ratio
	 * ----------------------------------------------------------
	 * Image scale ratio (decimal format).
	 ***********************************************************/

	$config['tcpdf']['image_scale'] = 4;


	/************************************************************
	 * TCPDF cell settings
	 * ----------------------------------------------------------
	 * Fontsize-to-height ratio, cell padding
	 ***********************************************************/

	$config['tcpdf']['cell_height_ratio'] = 1.25;
	$config['tcpdf']['cell_padding'] = 0;



