<?php
 
define('CFG_SERVICE_INSTANCEKEY','07a0be71-d84e-45ea-bc5f-9e4fa5bc53b4');
define('CFG_REQUEST_LANGUAGE', 'en');
 
$itemId = (isset($_REQUEST['itemId'])) ? $_REQUEST['itemId'] : 45844545906;
 
$url = 'http://otapi.net/OtapiWebService2.asmx/BatchGetItemFullInfo?instanceKey=' . CFG_SERVICE_INSTANCEKEY
        . '&language=' . CFG_REQUEST_LANGUAGE
        . '&itemId=' . $itemId
        . '&sessionId=&blockList=';
 
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 0);
 
$result = curl_exec($curl);
if ($result === FALSE) {
    echo "cURL Error: " . curl_error($curl); die();
}
$xmlObject = simplexml_load_string($result);
 
curl_close($curl);
 
if ((string)$xmlObject->ErrorCode !== 'Ok') {
    echo "Error: " . $xmlObject->ErrorDescription; die();
}
 
$itemInfo = $xmlObject->Result->Item;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
 
<div class="container">
    <div class="row-fluid" style="margin-top: 50px; text-align: center;">
        <div class="span12">
            <h2><?=(string)$itemInfo->Title?></h2>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span5">
            <div style="margin: 10px;">
                <img id="main-img" src="<?=(string)$itemInfo->MainPictureUrl?>" class="img-polaroid" width="310" />
            </div>
        </div>
        <div class="span7">
            <div style="margin-top: 10px;">
                <?php foreach ($itemInfo->Pictures->ItemPicture as $ItemPicture) { ?>
                    <a href="javascript:void(0)">
                        <img src="<?=(string)$ItemPicture->Url?>" class="ItemPicture img-polaroid" width="70" />
                    </a>
                <?php } ?>
            </div>
            <table class="table table-striped" style="width: 100%; margin-top: 30px;">
                <tbody>
                    <?php foreach ($itemInfo->Price->ConvertedPriceList->DisplayedMoneys->Money as $Money) { ?>
                        <tr>
                            <td>
                                <h2><?=(string)$Money?> <?=(string)$Money['Sign']?></h2>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
 
<?php
// prepare item attributes
$itemAttributes = array();
if (isset($itemInfo->Attributes->ItemAttribute)) {
    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
        $itemAttributes[(string)$ItemAttribute['Pid']]['PropertyName'] = (string)$ItemAttribute->PropertyName;
        $itemAttributes[(string)$ItemAttribute['Pid']]['IsConfigurator'] = ((string)$ItemAttribute->IsConfigurator === 'true');
        $itemAttributes[(string)$ItemAttribute['Pid']]['Values'][(string)$ItemAttribute['Vid']]['Value'] = (string)$ItemAttribute->Value;
    }
}
?>
    <?php if (count($itemAttributes) > 0) { ?>
        <div class="row-fluid">
            <div class="span12">
                <table class="table table-bordered table-hover" style="width: 100%; margin-top: 20px;">
                    <tbody>
                        <?php foreach ($itemAttributes as $attribute) { ?>
                            <tr>
                                <td><?=$attribute['PropertyName']?></td>
                                <td>
                                    <?php foreach ($attribute['Values'] as $value) { ?>
                                        - <?=$value['Value']?><br />
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</div>
 
<script type="text/javascript">
$('.ItemPicture').click(function(e){
    $('#main-img').attr('src', $(this).attr('src'));
});
</script>
 
</body>
</html>