<?php

include ("dbconnect.php");
include ("edi_writer.php");


//$invoiceNo = $_GET['invoiceNo']; // get argument from browser

$invoiceNo = "INV2014000001"; 

$objDb = new dbConnect();
$objDb->ConnectDB();

// SAC Header query
$strQuery = "SELECT * FROM sac_header WHERE invoiceNo = '" . $invoiceNo . "';";
$resultSACHeader = mysql_query($strQuery);
$row = mysql_fetch_array($resultSACHeader);

$SACHeader_OID = $row['OID'];
if (empty($SACHeader_OID)) {
    fwrite(STDERR, "Invoice $invoiceNo does not exist.\n");
    exit(1);
}
// SAC header properties
$portOfDestination = $row['portOfDestination'];
$portOfDischarge = $row['portOfDischarge'];
$countryOfOrigin = $row['countryOfOrigin'];
$arrivalDate = str_replace("-", "", $row['arrivalDate']);
$valuationDate = str_replace("-", "", $row['valuationDate']);
$dutyDateIndicator = $row['dutyDateIndicator'];
$eftPayment = $row['eftPayment'];
$eftPaymentApproved = $row['eftPaymentApproved'];
$receiptIndicator = $row['receiptIndicator'];
$bankAccountName = $row['bankAccountName'];
$bankAccountName1 = substr($bankAccountName, 0, 35);
$bankAccountName2 = substr($bankAccountName, 35, 5);
$bankAccountNo = $row['bankAccountNo'];
$bsbNo = $row['bsbNo'];
$goodsDescription = $row['goodsDescription'];
$containerNumber = $row['containerNumber'];
$importerRef = $row['importerRef'];
$sacId = $row['sacId'];
$brokerRef = $row['brokerRef'];
$bankAccountOwner = $row['bankAccountOwner'];
$houseBillOfLading = $row['houseBillOfLading'];
$houseAirWaybill = $row['houseAirWaybill'];
$oceanBillOfLading = $row['oceanBillOfLading'];
$masterAirWaybill = $row['masterAirWaybill'];
$consignmentRef = $row['consignmentRef'];
$cargoType = $row['cargoType'];
$transportMode = $row['transportMode'];
$voyageNo = $row['voyageNo'];
$vesselId = $row['vesselId'];
$importerABN = $row['importerABN'];
$importerCAC = $row['importerCAC'];
$importerId = $row['importerId'];
$branchId = $row['branchId'];
$supplierId = $row['supplierId'];
$cvlAmount = $row['cvlAmount'];
$cvlCurrency = $row['cvlCurrency'];
$tinAmount = $row['tinAmount'];
$tinCurrency = $row['tinCurrency'];
$delivery_OID = $row['delivery_OID'];
$importer_OID = $row['importer_OID'];
$supplier_OID = $row['supplier_OID'];

// Delivery Address query
if (!empty($delivery_OID)) {
    $strQuery = "SELECT * FROM name_and_address WHERE OID = '"
            . $delivery_OID . "';";
    $resultNameAndAddress = mysql_query($strQuery);
    $row = mysql_fetch_array($resultNameAndAddress);

    // Delivery properties
    $deliveryLocality = $row['locality'];
    $deliveryLocality1 = substr($deliveryLocality, 0, 35);
    $deliveryLocality2 = substr($deliveryLocality, 35, 11);
    $deliveryName = $row['name'];
    $deliveryAddressLine1 = $row['addressLine1'];
    $deliveryAddressLine1_1 = substr($deliveryAddressLine1, 0, 35);
    $deliveryAddressLine1_2 = substr($deliveryAddressLine1, 35, 5);
    $deliveryAddressLine2 = $row['addressLine2'];
    $deliveryAddressLine2_1 = substr($deliveryAddressLine2, 0, 35);
    $deliveryAddressLine2_2 = substr($deliveryAddressLine2, 35, 5);
    $deliveryStateCode = $row['stateCode'];
    $deliveryPostcode = $row['postcode'];
    $deliveryCountryCode = $row['countryCode'];
    $deliveryContactPhone = $row['contactPhone'];
}

// Importer Address query
if (!empty($importer_OID)) {
    $strQuery = "SELECT * FROM name_and_address WHERE OID = '"
            . $importer_OID . "';";
    $resultNameAndAddress = mysql_query($strQuery);
    $row = mysql_fetch_array($resultNameAndAddress);

    // Importer properties
    $importerLocality = $row['locality'];
    $importerLocality1 = substr($importerLocality, 0, 35);
    $importerLocality2 = substr($importerLocality, 35, 11);
    $importerName = $row['name'];
    $importerName1 = substr($importerName, 0, 35);
    $importerName2 = substr($importerName, 35, 35);
    $importerAddressLine1 = $row['addressLine1'];
    $importerAddressLine1_1 = substr($importerAddressLine1, 0, 35);
    $importerAddressLine1_2 = substr($importerAddressLine1, 35, 5);
    $importerAddressLine2 = $row['addressLine2'];
    $importerAddressLine2_1 = substr($importerAddressLine2, 0, 35);
    $importerAddressLine2_2 = substr($importerAddressLine2, 35, 5);
    $importerStateCode = $row['stateCode'];
    $importerPostcode = $row['postcode'];
    $importerCountryCode = $row['countryCode'];
}

// Supplier Address query
if (!empty($supplier_OID)) {
    $strQuery = "SELECT * FROM name_and_address WHERE OID = '"
            . $supplier_OID . "';";
    $resultNameAndAddress = mysql_query($strQuery);
    $row = mysql_fetch_array($resultNameAndAddress);

    // Supplier properties
    $supplierName = $row['name'];
    $supplierName1 = substr($supplierName, 0, 35);
    $supplierName2 = substr($supplierName, 35, 5);
}

// parameters
$senderRef = EdiWriter::getSenderRef($SACHeader_OID);
$interchangeCreator = "AAA123Z";
$interchangeOwner = "AAA123Z";
$interchangeRecipientId = "AAA336C";
$interchangeReferenceNo = "00000000000001";
$acknowledgementRequest = TRUE;
$testIndicator = TRUE;
$messageReferenceNumber = "000001";
$messageFunctionCode = 9;
//$path = "C:\\wamp\\www\\CargoCloud\\";
//$file = $path . $interchangeCreator . "_" . $interchangeReferenceNo . ".edi";
$file = $interchangeCreator . "_" . $interchangeReferenceNo . ".edi";

// UNA Service String Advice
EdiWriter::writeUNA($file);

// UNB Interchange Header
// UNB+UNOC:3+AAF377E::AAF377E+AAA336C+050630:1818+16092013004++++1++1'
date_default_timezone_set("Australia/Sydney");
$date = date("ymd");
$time = date("Gi");

EdiWriter::beginSegment("UNB");
EdiWriter::appendCDE("UNOC");
EdiWriter::appendDE("3");
EdiWriter::appendCDE($interchangeCreator);
EdiWriter::appendDE("");
EdiWriter::appendDE($interchangeOwner);
EdiWriter::appendCDE($interchangeRecipientId);
EdiWriter::appendCDE($date);
EdiWriter::appendDE($time);
EdiWriter::appendCDE($interchangeReferenceNo);
EdiWriter::appendCDE("");
EdiWriter::appendCDE("");
EdiWriter::appendCDE("");
if ($acknowledgementRequest == TRUE) {
    EdiWriter::appendCDE("1");
} else {
    EdiWriter::appendCDE("");
}
EdiWriter::appendCDE("");

if ($testIndicator == TRUE) {
    EdiWriter::appendCDE("1");
} else {
    EdiWriter::appendCDE("");
}
EdiWriter::appendSegment($file);

// UNH Message Header
// UNH+000042+CUSDEC:D:99B:UN'
EdiWriter::beginSegment("UNH");
EdiWriter::appendCDE($messageReferenceNumber);
EdiWriter::appendCDE("CUSDEC");
EdiWriter::appendDE("D");
EdiWriter::appendDE("99B");
EdiWriter::appendDE("UN");
EdiWriter::appendSegment($file);

// BGM Beginning of Message
// BGM+929:::SAC+16092013004:001+9'
EdiWriter::beginSegment("BGM");
EdiWriter::appendCDE("929");
EdiWriter::appendDE("");
EdiWriter::appendDE("");
EdiWriter::appendDE("SAC");
EdiWriter::appendCDE($senderRef);
EdiWriter::appendDE("001"); // sender reference version
EdiWriter::appendCDE($messageFunctionCode);
EdiWriter::appendSegment($file);

// LOC Place/Location Identification
// LOC+8+AUSYD::6'
if (!empty($portOfDestination)) {
    EdiWriter::beginSegment("LOC");
    EdiWriter::appendCDE("8");
    EdiWriter::appendCDE($portOfDestination);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("6");
    EdiWriter::appendSegment($file);
}

// LOC+12+AUSYD::6'
if (!empty($portOfDischarge)) {
    EdiWriter::beginSegment("LOC");
    EdiWriter::appendCDE("12");
    EdiWriter::appendCDE($portOfDischarge);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("6");
    EdiWriter::appendSegment($file);
}

// LOC+27+US::5'
if (!empty($countryOfOrigin)) {
    EdiWriter::beginSegment("LOC");
    EdiWriter::appendCDE("27");
    EdiWriter::appendCDE($countryOfOrigin);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("5");
    EdiWriter::appendSegment($file);
}

// DTM Date/Time/Period
// DTM+178:20130913:102' arrivalDate='20130913'
EdiWriter::beginSegment("DTM");
EdiWriter::appendCDE("178");
EdiWriter::appendDE($arrivalDate);
EdiWriter::appendDE("102");
EdiWriter::appendSegment($file);

// DTM+260:20130913:102' valuationDate='20130913'
EdiWriter::beginSegment("DTM");
EdiWriter::appendCDE("260");
EdiWriter::appendDE($valuationDate);
EdiWriter::appendDE("102");
EdiWriter::appendSegment($file);

// GIS General Indicator
// GIS+N:153:95'
$code = ($eftPayment == 1 ? "Y" : "N");
EdiWriter::beginSegment("GIS");
EdiWriter::appendCDE($code);
EdiWriter::appendDE("153");
EdiWriter::appendDE("95");
EdiWriter::appendSegment($file);

// GIS+EPA:109:95'
if ($eftPaymentApproved == 1) {
    EdiWriter::beginSegment("GIS");
    EdiWriter::appendCDE("EPA");
    EdiWriter::appendDE("109");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// GIS+EFD:109:95'
if ($dutyDateIndicator == 1) {
    EdiWriter::beginSegment("GIS");
    EdiWriter::appendCDE("EFD");
    EdiWriter::appendDE("109");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// GIS+POR:109:95'
if ($receiptIndicator == 1) {
    EdiWriter::beginSegment("GIS");
    EdiWriter::appendCDE("POR");
    EdiWriter::appendDE("109");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// FII Financial Institution Information
// not allowed when message function is "original"
// FII+COQ+AccountNo:Name1:Name2+:::BSB::215'
if ($messageFunctionCode != 9 && !empty($bankAccountNo)) {
    EdiWriter::beginSegment("FII");
    EdiWriter::appendCDE("COQ");
    EdiWriter::appendCDE($bankAccountNo);
    EdiWriter::appendDE($bankAccountName1);
    EdiWriter::appendDE($bankAccountName2);
    EdiWriter::appendCDE("");
    EdiWriter::appendDE("");
    EdiWriter::appendDE("");
    EdiWriter::appendDE($bsbNo);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("215");
    EdiWriter::appendSegment($file);
}

// FTX Free Text
// FTX+AAA+++GOODS DESCRIPTION'
EdiWriter::beginSegment("FTX");
EdiWriter::appendCDE("AAA");
EdiWriter::appendCDE("");
EdiWriter::appendCDE("");
EdiWriter::appendCDE("$goodsDescription");
EdiWriter::appendSegment($file);

// FTX+DEL+++DELIVERY NAME'
EdiWriter::beginSegment("FTX");
EdiWriter::appendCDE("DEL");
EdiWriter::appendCDE("");
EdiWriter::appendCDE("");
EdiWriter::appendCDE("$deliveryName");
EdiWriter::appendSegment($file);

$questionId16 = FALSE;
$strQuery = "SELECT * FROM lodgement_answer WHERE declaration_OID = '"
        . $SACHeader_OID . "';";
$resultLodgementAnswer = mysql_query($strQuery);
while ($row = mysql_fetch_array($resultLodgementAnswer)) {
    $questionId = $row['questionId'];
    $answerType = $row['answerType'];
    $referralReason = $row['referralReason'];
    if ($answerType == "Y" || $answerType == "N") {
        // FTX+ACD+++questionID:answerType'
        EdiWriter::beginSegment("FTX");
        EdiWriter::appendCDE("ACD");
        EdiWriter::appendCDE("");
        EdiWriter::appendCDE("");
        EdiWriter::appendCDE($questionId);
        EdiWriter::appendDE($answerType);
        if ($answerType == "Y" && !empty($referralReason)) {
            EdiWriter::appendDE($referralReason);
        }
        EdiWriter::appendSegment($file);
    }
    if ($questionId == "16") {
        $questionId16 = TRUE;
    }
}

// RFF Reference
// RFF+AAQ:Container Number'
if (!empty($containerNumber)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("AAQ");
    EdiWriter::appendDE($containerNumber);
    EdiWriter::appendSegment($file);
}

// RFF+ABQ:Importer Reference'
if (!empty($importerRef)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("ABQ");
    EdiWriter::appendDE($importerRef);
    EdiWriter::appendSegment($file);
}

// RFF+ABT:Self Assessed Clearance Identifier'
if (!empty($sacId)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("ABT");
    EdiWriter::appendDE($sacId);
    EdiWriter::appendSegment($file);
}

// RFF+ADU:Broker Reference'
if (!empty($brokerRef)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("ADU");
    EdiWriter::appendDE($brokerRef);
    EdiWriter::appendSegment($file);
}

// RFF+AMG:Lodgement Declaration Identifier'
if ($questionId16) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("AMG");
    EdiWriter::appendDE("16");
    EdiWriter::appendSegment($file);
}

// RFF+ANU:Bank Acccount Owner Type'
EdiWriter::beginSegment("RFF");
EdiWriter::appendCDE("ANU");
EdiWriter::appendDE($bankAccountOwner);
EdiWriter::appendSegment($file);

// RFF+BH:House Bill of Lading'
if (!empty($houseBillOfLading)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("BH");
    EdiWriter::appendDE($houseBillOfLading);
    EdiWriter::appendSegment($file);
}

// RFF+HWB:House Air Waybill Number'
if (!empty($houseAirWaybill)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("HWB");
    EdiWriter::appendDE($houseAirWaybill);
    EdiWriter::appendSegment($file);
}

// RFF+MB:Ocean Bill of Lading'
if (!empty($oceanBillOfLading)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("MB");
    EdiWriter::appendDE($oceanBillOfLading);
    EdiWriter::appendSegment($file);
}

// RFF+MWB:Master Air Waybill Number'
if (!empty($masterAirWaybill)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("MWB");
    EdiWriter::appendDE($masterAirWaybill);
    EdiWriter::appendSegment($file);
}

// RFF+CNR:Consignment Reference'
if (!empty($consignmentRef)) {
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("CNR");
    EdiWriter::appendDE($consignmentRef);
    EdiWriter::appendSegment($file);
}

// PAC Package
// PAC+++Import Cargo Type:67:95'
if (!empty($cargoType)) {
    EdiWriter::beginSegment("PAC");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($cargoType);
    EdiWriter::appendDE("67");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// TDT Details of Transport
// TDT+20++Mode of Transport'
// TDT+20+Voyage No.+Mode of Transport+++++Vessel Id::11'
if ($transportMode == "S") {
    EdiWriter::beginSegment("TDT");
    EdiWriter::appendCDE("20");
    EdiWriter::appendCDE($voyageNo);
    EdiWriter::appendCDE($transportMode);
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($vesselId);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("11");
    EdiWriter::appendSegment($file);
} else {
    EdiWriter::beginSegment("TDT");
    EdiWriter::appendCDE("20");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($transportMode);
    EdiWriter::appendSegment($file);
}

// NAD Name and Address
// NAD+DP++LOC1:LOC2++ADDRESS: LINE1:ADDRESS: LINE 2++:::VIC+3011+AU'
if (!empty($deliveryLocality)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("DP");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($deliveryLocality1);
    EdiWriter::appendDE($deliveryLocality2);
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($deliveryAddressLine1_1);
    EdiWriter::appendDE($deliveryAddressLine1_2);
    EdiWriter::appendDE($deliveryAddressLine2_1);
    EdiWriter::appendDE($deliveryAddressLine1_2);
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendDE("");
    EdiWriter::appendDE("");
    EdiWriter::appendDE($deliveryStateCode);
    EdiWriter::appendCDE($deliveryPostcode);
    EdiWriter::appendCDE($deliveryCountryCode);
    EdiWriter::appendSegment($file);
}
// NAD+AT+Importer ABN::95'
if (!empty($importerABN)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("AT");
    EdiWriter::appendCDE($importerABN);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// NAD+WP+Importer CAC::95'
if (!empty($importerABN) && !empty($importerCAC)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("WP");
    EdiWriter::appendCDE($importerCAC);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);
}

// NAD+IM+IMP.ID+LOC1:LOC2+NAME1:NAME2+ADDRESS: LINE1:ADDRESS: LINE 2++:::VIC+3011+AU'
if (!empty($importerId)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("IM");
    EdiWriter::appendCDE($importerId);
    if (!empty($importerLocality)) {
        EdiWriter::appendCDE($importerLocality1);
        EdiWriter::appendDE($importerLocality2);
        EdiWriter::appendCDE($importerName1);
        EdiWriter::appendDE($importerName2);
        EdiWriter::appendCDE($importerAddressLine1_1);
        EdiWriter::appendDE($importerAddressLine1_2);
        EdiWriter::appendDE($importerAddressLine2_1);
        EdiWriter::appendDE($importerAddressLine1_2);
        EdiWriter::appendCDE("");
        EdiWriter::appendCDE("");
        EdiWriter::appendDE("");
        EdiWriter::appendDE("");
        EdiWriter::appendDE($importerStateCode);
        EdiWriter::appendCDE($importerPostcode);
        EdiWriter::appendCDE($importerCountryCode);
    }
    EdiWriter::appendSegment($file);
}

// NAD+VT+Branch Id::95'
if (!empty($branchId)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("VT");
    EdiWriter::appendCDE($branchId);
    EdiWriter::appendSegment($file);
}

// NAD+SU+SUPPLIER ID::95++SUPPLIER NAME1:NAME2'
if (!empty($supplierId)) {
    EdiWriter::beginSegment("NAD");
    EdiWriter::appendCDE("SU");
    EdiWriter::appendCDE($supplierId);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("95");
    if (!empty($supplierName)) {
        EdiWriter::appendCDE("");
        EdiWriter::appendCDE($supplierName1);
        EdiWriter::appendDE($supplierName2);        
    }
    EdiWriter::appendSegment($file);
    
}

// CTA Contact Information
// CTA+IC'
EdiWriter::beginSegment("CTA");
EdiWriter::appendCDE("IC");
EdiWriter::appendSegment($file);

// COM Communication Contact
// COM+Contact Phone:TE'
EdiWriter::beginSegment("COM");
EdiWriter::appendCDE($deliveryContactPhone);
EdiWriter::appendDE("TE");
EdiWriter::appendSegment($file);

// MOA Monetary Amount
// MOA+40:cvlAmount:cvlCurrency'
if ($cvlAmount > 0.0) {
    EdiWriter::beginSegment("MOA");
    EdiWriter::appendCDE("40");
    EdiWriter::appendDE($cvlAmount);
    EdiWriter::appendDE($cvlCurrency);
    EdiWriter::appendSegment($file);
}

// MOA+68:tinAmount:tinCurrency'
if ($tinAmount > 0.0) {
    EdiWriter::beginSegment("MOA");
    EdiWriter::appendCDE("68");
    EdiWriter::appendDE($tinAmount);
    EdiWriter::appendDE($tinCurrency);
    EdiWriter::appendSegment($file);
}

// UNS Section Control (Detail)
// UNS+D'
EdiWriter::beginSegment("UNS");
EdiWriter::appendCDE("D");
EdiWriter::appendSegment($file);

// Query SAC lines
$strQuery = "SELECT * FROM sac_line WHERE sac_header_OID = '"
        . $SACHeader_OID . "' ORDER BY lineNumber;";
$resultSACLines = mysql_query($strQuery);
while ($row = mysql_fetch_array($resultSACLines)) {
    writeSACLine($file, $row);
}

// UNS Section Control (Summary)
// UNS+S'
EdiWriter::beginSegment("UNS");
EdiWriter::appendCDE("S");
EdiWriter::appendSegment($file);

// UNT Message Trailer
EdiWriter::beginSegment("UNT");
EdiWriter::appendCDE(EdiWriter::getSegmentCount());
EdiWriter::appendCDE($messageReferenceNumber);
EdiWriter::appendSegment($file);

// UNZ Interchange Trailer
EdiWriter::beginSegment("UNZ");
EdiWriter::appendCDE("1");
EdiWriter::appendCDE($interchangeReferenceNo);
EdiWriter::appendSegment($file);


/**
 * Writes SAC line to EDIFACT file
 * @param type $file output file
 * @param type $row line to copy fields from
 */
function writeSACLine($file, $row) {
    $lineNumber = $row['lineNumber'];
    $lineAction = $row['lineAction'];
    $goodsDescription = $row['goodsDescription'];
    $quantityUnit = $row['quantityUnit'];
    $quantity = floatval($row['quantity']);
    $cvlAmount = $row['cvlAmount'];
    $cvlCurrency = $row['cvlCurrency'];
    $tariffClassification = $row['tariffClassification'];
    $statisticalCode = $row['statisticalCode'];
    $refundReasonCode = $row['refundReasonCode'];
    $gstxCode = $row['gstxCode'];
    $wetxCode = $row['wetxCode'];
    $wetInd = $row['wetInd'];

    // CST Customs Status of Goods
    // CST+Line Number+ActionCode::95'
    EdiWriter::beginSegment("CST");
    EdiWriter::appendCDE($lineNumber);
    EdiWriter::appendCDE($lineAction);
    EdiWriter::appendDE("");
    EdiWriter::appendDE("95");
    EdiWriter::appendSegment($file);

    // FTX Free Text
    // FTX+AAA+++GOODS DESCRIPTION'
    EdiWriter::beginSegment("FTX");
    EdiWriter::appendCDE("AAA");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($goodsDescription);
    EdiWriter::appendSegment($file);

    // MEA Measurements
    // MEA+AAA++Quantity Unit:Quantity Amount'
    EdiWriter::beginSegment("MEA");
    EdiWriter::appendCDE("AAA");
    EdiWriter::appendCDE("");
    EdiWriter::appendCDE($quantityUnit);
    EdiWriter::appendDE($quantity);
    EdiWriter::appendSegment($file);

    // MOA Monetary Amount
    // MOA+40:CVL Amount:CVL Currency'
    EdiWriter::beginSegment("MOA");
    EdiWriter::appendCDE("40");
    EdiWriter::appendDE($cvlAmount);
    EdiWriter::appendDE($cvlCurrency);
    EdiWriter::appendSegment($file);

    // RFF Reference
    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("ABD");
    EdiWriter::appendDE($tariffClassification);
    EdiWriter::appendSegment($file);

    EdiWriter::beginSegment("RFF");
    EdiWriter::appendCDE("AED");
    EdiWriter::appendDE($statisticalCode);
    EdiWriter::appendSegment($file);

    if (!empty($refundReasonCode)) {
        EdiWriter::beginSegment("RFF");
        EdiWriter::appendCDE("ABE");
        EdiWriter::appendDE($refundReasonCode);
        EdiWriter::appendSegment($file);
    }

    if (!empty($gstxCode)) {
        EdiWriter::beginSegment("RFF");
        EdiWriter::appendCDE("ASA");
        EdiWriter::appendDE($gstxCode);
        EdiWriter::appendSegment($file);
    }

    if (!empty($wetxCode)) {
        EdiWriter::beginSegment("RFF");
        EdiWriter::appendCDE("DA");
        EdiWriter::appendDE($wetxCode);
        EdiWriter::appendSegment($file);
    }
    
    // GIS General Indicator
    // GIS+WET:109:95'
    if ($wetInd == 1) {
        EdiWriter::beginSegment("GIS");
        EdiWriter::appendCDE("WET");
        EdiWriter::appendDE("109");
        EdiWriter::appendDE("95");
        EdiWriter::appendSegment($file);       
    }
    
}
