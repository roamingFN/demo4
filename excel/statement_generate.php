<?php
	include '../connect.php';
	include '../session.php';
?>

<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once '../Classes/PHPExcel.php';


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");

$select_statement = mysql_query("select * from customer_statement where customer_id = $user_id order by statement_date");

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'วันที่');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'รายการ');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'เลขที่ออเดอร์');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'ประเภท');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ยอดเข้า');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'ยอดออก');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'คงเหลือ');
$i = 0;
$total = 0;
while ($row = mysql_fetch_array($select_statement)) {
	$total += $row['debit'];
	$total -= $row['credit'];
	$objPHPExcel->getActiveSheet()->setCellValue('A' . ($i+2), $row['statement_date']);
	$objPHPExcel->getActiveSheet()->setCellValue('B' . ($i+2), $row['statement_name']);
	$objPHPExcel->getActiveSheet()->setCellValue('C' . ($i+2), $row['order_id']);
	$objPHPExcel->getActiveSheet()->setCellValue('D' . ($i+2), $row['statement_detail']);
	$objPHPExcel->getActiveSheet()->setCellValue('E' . ($i+2), $row['debit']);
	$objPHPExcel->getActiveSheet()->setCellValue('F' . ($i+2), $row['credit']);
	$objPHPExcel->getActiveSheet()->setCellValue('G' . ($i+2), $total);
	$i++;
}
$objPHPExcel->getActiveSheet()->setCellValue('F' . ($i+3), 'ยอดเงินคงเหลือ');
$objPHPExcel->getActiveSheet()->setCellValue('G' . ($i+3), $total);

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('statement_generate_'.$user_id.'.xlsx');
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Save Excel5 file
// echo date('H:i:s') , " Write to Excel5 format" , EOL;
// $callStartTime = microtime(true);

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// $objWriter->save('statement_generate'.$user_id.'.xlsx');
// $callEndTime = microtime(true);
// $callTime = $callEndTime - $callStartTime;

// echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// // Echo memory usage
// echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// // Echo memory peak usage
// echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// // Echo done
// echo date('H:i:s') , " Done writing files" , EOL;
// echo 'Files have been created in ' , getcwd() , EOL;

header('Location: statement_generate_'.$user_id.'.xlsx');
