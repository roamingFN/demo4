<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set("Asia/Bangkok");

        /** PHPExcel */
        require_once 'Classes/PHPExcel.php';

        include 'connect.php';
        include 'session.php';
        include 'inc/php/functions_statusConvert.php';

        //config------------------------------------------------------------------------------------------------------------------------
        $order_id = $_GET['order_id'];
        // Create new PHPExcel object
        echo date('H:i:s') . " Create new PHPExcel object\n";
        $objPHPExcel = new PHPExcel();
        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("China_express")
                ->setLastModifiedBy("China_express")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("order confirm")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //header------------------------------------------------------------------------------------------------------------------------------
        $select_order = mysql_query("select * from customer_order o, customer_order_shipping s, customer c 
                                where o.customer_id = '$user_id'
                                and c.customer_id = o.customer_id
                                and o.order_id = '$order_id'
                                and o.order_id = s.order_id ", $connection);
        if (mysql_num_rows($select_order) > 0) {
            $order_row = mysql_fetch_array($select_order);
            $order_status_code = $order_row['order_status_code'];
            $customer_note = $order_row['customer_note'];
            $time = strtotime($order_row['date_order_created']);
            $newFormat = date('d-m-Y',$time);
            $now = date('d-m-Y');

            echo date('H:i:s') . " Add some data\n";
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('E1', 'ใบรายการสั่งซื้อ')
                ->setCellValue('A2', 'เลขที่ออร์เดอร์ : '.$order_row['order_number'])
                ->setCellValue('A3', 'รหัสลูกค้า : '.$order_row['customer_code'])
                ->setCellValue('A4', 'อีเมลล์ : '.$order_row['customer_email'])
                ->setCellValue('E2', 'บริการขนส่งในประเทศ : '.convertTransportName($order_row['order_shipping_th_option']))
                ->setCellValue('E3', 'Rate : '.number_format($order_row['order_rate'],2)." @ ".date("d/m/Y G:i:s", strtotime($order_row['order_rate_date'])))
                ->setCellValue('E4', 'ค่าสินค้า : '.number_format($order_row['order_price'],2))
                ->setCellValue('I2', 'สั่งซื้อวันที่ : '.$newFormat)
                ->setCellValue('I3', 'พิมพ์วันที่ : '.$now);
        }

        //set header
        // $objPHPExcel->getActiveSheet()->getStyle("A4:H4")->getFont()->setBold(true);
        // //set number
        // $objPHPExcel->getActiveSheet()->getStyle("G")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        // //set auto width
        // PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        // foreach(range('A','H') as $columnID) {
        //      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        // }
        
        //Write data from MySQL result
  //       if($stmt = $con->prepare($sql)) {
  //       $stmt->execute();
		// $stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat);		
  //               $i = 5;
  //               while($stmt->fetch()) {     
  //                       //formated datetime otime
  //                       $oDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
  //                       $oTime=substr($datetime,10,9);
                        
  //                       $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $order_number);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $fname.' '.$lname);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $oDate.' '.$oTime);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $totalShop);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $totalLink);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $quatity);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $price);
  //                       $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $_codes[$status]);
  //                       $i++;
  //               }
  //       }

        //start table
        $select_shop_group = mysql_query("  select shop_name
                                            from customer_order_product c, customer_order o, product p 
                                            where o.order_id = c.order_id 
                                            and c.product_id = p.product_id
                                            and o.customer_id = '$user_id'
                                            and o.order_id = '$order_id'
                                            group by p.shop_name", $connection);

        if (mysql_num_rows($select_shop_group) > 0) {
            $sum_amount = 0;
            $sum_amount_success = 0;
            $sum_price_cn = 0;
            $sum_transfer_price_cn = 0;
            $sum_transfer_price_cn_th = 0;
            $sum_price_thb_all = 0;
            $sum_received_amount = 0;
            $sum_return_money = 0;
            
            //start row
            $i = 6;
            $j = 0;

            while ($shop_row = mysql_fetch_array($select_shop_group)) {
                $shop_name = $shop_row['shop_name'];
                
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'ลำดับ');
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, 'ร้าน '.$shop_name);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, "ไซด์/สี");
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, 'จำนวนที่สั่ง');
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, 'จำนวนที่สั่งได้');
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, 'ราคา (หยวน)');
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, 'ค่าขนส่งในจีน (หยวน)');
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, 'ทั้งหมด (บาท)');
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, 'จำนวนที่ได้รับ');
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, 'คืนเงิน');
                $i++;

                //query product
                $select_item = mysql_query("select * , c.received_amount as current_received
                                        from customer_order_product c, customer_order o, product p, order_remark r 
                                        where o.order_id = c.order_id 
                                        and c.product_id = p.product_id
                                        and c.remark_id = r.remark_id 
                                        and o.customer_id = '$user_id'
                                        and o.order_id = '$order_id'
                                        and p.shop_name = '$shop_name'", $connection);
                if(mysql_num_rows($select_item) > 0) {    
                    while($row = mysql_fetch_array($select_item)) {
                        if ($row['order_status']=="2") {
                            $detail = "<span style='color:red'>สั่งไม่ได้ : ".$row["remark_tha"]."</span>";
                            $table_style = "style='background-color:lightgray'";
                        }
                        else{
                            $detail = convertOrderProductStatus($row['current_status']);
                            $table_style = "";
                        }

                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, ++$j);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['product_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['product_size']." ".$row['product_color']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, convertStatementZero($row['first_unitquantity']));
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, convertStatementZero($row['quantity']));
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, number_format($row['unitprice'],2));
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, number_format($row['order_shipping_cn_cost'],2));
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, number_format($row['order_product_totalprice'],2));
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($row['current_received'],0)." ".$detail." ".$row['current_updatetime']);
                        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, number_format($row['return_baht'],2)." ".convertRecievedStatus($row['return_baht'],$row['return_status'],$row['order_status_code']));
                        $i++;

                        $sum_amount += $row['first_unitquantity'];
                        $sum_amount_success += $row['quantity'];
                        $sum_price_cn += $row['unitprice']*$row['quantity'];
                        $sum_transfer_price_cn += $row['order_shipping_cn_cost'];
                        $sum_price_thb_all += $row['order_product_totalprice'];
                        $sum_received_amount += $row['current_received'];
                        $sum_return_money += $row['return_baht'];
                    }
                }   //end product
            } // end shop

            //trailer
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'ทั้งหมด');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sum_amount);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $sum_amount_success);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, number_format($sum_transfer_price_cn,2));
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, number_format($sum_price_thb_all,2));
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, number_format($sum_received_amount,0));
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, number_format($sum_return_money,2));
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order_detail');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "order_detail.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");
        // Write file to the browser
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
        // Echo memory peak usage
        //echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

        // Echo done
        //echo date('H:i:s') . " Done writing file.\r\n"; 
        
        
    ?>
