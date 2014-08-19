<?php
session_start();
error_reporting(0);
// Include the main TCPDF library (search for installation path).
require_once('lib/tcpdf/tcpdf.php');
require_once('lib/tcpdf/examples/tcpdf_include.php');

//Including Twitter library
require_once('lib/twitteroauth/twitteroauth/twitteroauth.php');
require_once('twitterappconfig.php');

//Including functions file which are mendatory 
require_once('required_functions.php');

$access_token=$_SESSION['access_token'];
$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);

if(isset($_POST['selected_follower'])){
	
	/*****************************
	 *	  GETTING A TWEETS       *
	 *    DISPLAYED ON A PAGE    *
	 *    OF A SELECTED FOLLOWER *
	******************************/
		
	$_SESSION['selected_follower']=$_POST['selected_follower'];
	$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','screen_name' => $_POST['selected_follower']));	
	echo json_encode($val);	
	
}else if(isset($_GET['p'])){
		
	/****************************
	 *	  PDF FILE CREATION    *
	****************************/
	
	//Calling a function to get all tweets
	$val=getAllTweets();
	
	// Include the main TCPDF library (search for installation path).
	
	require_once('lib/tcpdf/examples/tcpdf_include.php');
	require_once('lib/tcpdf/tcpdf.php');	
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	// set document information
	//$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Jay Shah');
	$pdf->SetTitle('All Tweets of '.ucfirst(encryptDecrypt('decrypt',$_GET['p'])));
	//$pdf->SetSubject('TCPDF Tutorial');
	//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, ucfirst(encryptDecrypt('decrypt', $_GET['p']))."'s All Tweets", "by: Jay Shah");
	
	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}
	
	// add a page
	$pdf->AddPage();
	
	// create some HTML content
	$html='<table>';
	$html.='<tr>';
	$html.='<th>When did you tweet ? </th>';
	$html.='<th>What did you tweet ? </th>';
	//$html.='<th>How many retweeted ? </th>';
	//$html.='<th>How many marked it as Favorite ? </th>';
	$html.='</tr>';
	for($tweets_counter=0;$tweets_counter<count($val);$tweets_counter++){
		$html.="<tr>";
		$html.="<td>".$val[$tweets_counter]->created_at."</td>";
		$html.="<td>".$val[$tweets_counter]->text."</td>";
		//$html.="<td align='right'>".(empty($val[$tweets_counter]->retweeted))?'You got duck number of retweets for this tweet. :D ':$val[$tweets_counter]->retweet."</td>";
		//$html.="<td align='right'>".(empty($val[$tweets_counter]->favorited))?'Oops! Looks like no one has favorited this tweet.':$val[$tweets_counter]->favorited."</td>";
		$html.="</tr>";
	}
	$html.='</table>';
	
	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');
	
	
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	
	//Close and output PDF document
	$pdf->Output(ucfirst(encryptDecrypt('decrypt',$_GET['p'])).'.pdf', 'I');
	
}else if(isset($_GET['j'])){
	
	/****************************
	 *	  JSON FILE CREATION    *
	 ****************************/
	
	//Calling a function to get all tweets
	$val=getAllTweets();
	
	//Setting up a header for JSON file
	header('Content-disposition: attachment; filename='.encryptDecrypt('decrypt',$_GET['j']).'.json');
	header('Content-type: application/json');
			
	//Encoding PHP Array to JSON
	$json = json_encode($val);

	//Printing out Encoded JSON Array
	echo($json);
		
		
}else if(isset($_GET['c'])){
	
	/****************************
	 *	  CSV FILE CREATION    *
	****************************/
	
	//Calling a function to get all tweets
	$val=getAllTweets();
	
	//Setting up a headers for CSV file
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.encryptDecrypt('decrypt', $_GET['c']).'.csv');
	header("Pragma: no-cache");
	header("Expires: 0");
	
	$output = fopen('php://output', 'w');
	
	//Writing first row as a Header
	fputcsv($output, array('created_at','tweet'));
	
	//Iterating through an array "$var" to write all tweets row by row
	for($csv_counter=0;$csv_counter<count($val);$csv_counter++){
		fputcsv($output, array($val[$csv_counter]->created_at,$val[$csv_counter]->text));
	}			
	
}else if(isset($_GET['e'])){
	
	/****************************
	 *	  XLS FILE CREATION    *
	****************************/
	
	/** Include PHPExcel */
	require_once 'lib/PHPExcel179/Classes/PHPExcel.php';
	
	//Calling a function to get all tweets
	$val=getAllTweets();
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Jay Shah")
	->setLastModifiedBy("Jay Shah")
	->setTitle("All tweets of".encryptDecrypt('decrypt', $_GET['e']))
	->setSubject("Tweets")
	->setDescription("Excel is created using PHPExcel Library")
	->setKeywords("xls tweeter tweet")
	->setCategory("Tweet");
	
	
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', 'When did you tweet?')
	->setCellValue('B1', 'What did you tweet?');
	
	// Miscellaneous glyphs, UTF-8
	// Createing a new array which will only contain created_at,text Value from Main Array
	$new_val="";
	
	//Iterating through an array "$var" and adding it in new row as the loop continues..
	for($ATXcounter=0;$ATXcounter<count($val);$ATXcounter++){
		$new_val[$ATXcounter][0]=$val[$ATXcounter]->created_at;
		$new_val[$ATXcounter][1]=$val[$ATXcounter]->text;
	}
	
	//Transfering Array to Excel Sheet from Second Row "A2" just because we've already used First row for our headers
	$objPHPExcel->getActiveSheet()->fromArray($new_val, null, 'A2');
	
	//$objPHPExcel->setActiveSheetIndex(0)	->setCellValue('A4', 'Miscellaneous glyphs')	->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Tweets');
	
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='.encryptDecrypt('decrypt', $_GET['e']).'.xls');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
	
}else if(isset($_GET['x'])){
	
	/****************************
	 *	  XML FILE CREATION    *
	****************************/
	
	//Calling a function to get all tweets
	$val=getAllTweets();

	//Setting up a header for XML file
	header("Content-Type: application/force-download; name=\"test.xml");
	header("Content-type: text/xml");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=\"test.xml");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");

	//Creating a new XML Object
	$xml = new SimpleXMLElement('<root/>');
	
	//Iterating through an array "$var" and creating new child as the loop continues..
	for($ATXMLcounter=0;$ATXMLcounter<count($val);$ATXMLcounter++){
		$tweet= $xml->addChild('tweet');
		$tweet->addAttribute('id', $val[$ATXMLcounter]->id);
	    $tweet->addChild('created_at', $val[$ATXMLcounter]->created_at);
	    $tweet->addChild('text', $val[$ATXMLcounter]->text);
	}	
	
	print($xml->asXML());

}

?>