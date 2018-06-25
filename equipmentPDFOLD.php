<?php
    include_once("security.php");
    include_once("CIL_functions.php");
    if(!api_IfACL("CILReport") && !api_IfACL("SILCReport")){
       header("location: /index.php");
       exit;
    }
    $ageRangePass = 0;
    if(!empty($_SESSION['ReportPrint']['post']['age_range'][1])){
        $ageRangePass = 1;
    }

    $show_depreciation = true;
    
    include('class.label.php');

    $pdf = new Cezpdf('LETTER');
    $pdf->selectFont('../CIL_fonts/Helvetica.afm');
    $align = array();
    $align["justification"] = "center";
    $pdf->ezText("<b>Equipment Report</b>", 22, $align);
    $pdf->ezText("from ".$_SESSION["ReportPrint"]["filters"]["startdate"]." to ".$_SESSION["ReportPrint"]["filters"]["enddate"], 16, $align);

    $include_rollup = $_SESSION['ReportPrint']['post']['include_rollup'];

    if($_SESSION["ReportPrint"]["filters"]["staff_member"]!='all') {
        switch($_SESSION["ReportPrint"]["filters"]["staff_limit"]) {
            case 1:
                $pdf->ezText("where ".api_GetUserName($_SESSION["ReportPrint"]["filters"]["staff_member"])." is primary staff", 16, $align);
                break;
            case 2:
                $pdf->ezText("where ".api_GetUserName($_SESSION["ReportPrint"]["filters"]["staff_member"])." is allowed staff", 16, $align);
                break;
            case 3:
                $pdf->ezText("where ".api_GetUserName($_SESSION["ReportPrint"]["filters"]["staff_member"])." entered the actual data", 16, $align);
                break;
        }        
    }
    $pdf->ezText("\n", 12, $align);

    if(CheckArray($_SESSION["ReportPrint"]["counties"])) {
        if($include_rollup == 1){

            IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["counties"],'counties',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
        } else {
           AreaPrint($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["counties"],'counties',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
           if($include_rollup == 3){
                IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["counties"],'counties',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
            }
        }
    }

    if(CheckArray($_SESSION["ReportPrint"]["counties"]) && CheckArray($_SESSION["ReportPrint"]["cities"])) {
        $pdf->ezNewPage();
    }

    if(CheckArray($_SESSION["ReportPrint"]["cities"])) {
        if($include_rollup == 1){
            IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["cities"],'cities',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
        } else {
           AreaPrint($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["cities"],'cities',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
           if($include_rollup == 3){
                IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["cities"],'cities',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
            }
        }
        
    }

    if((CheckArray($_SESSION["ReportPrint"]["cities"]) && CheckArray($_SESSION["ReportPrint"]["zipcodes"])) || (CheckArray($_SESSION["ReportPrint"]["counties"]) && CheckArray($_SESSION["ReportPrint"]["zipcodes"]))) {
        $pdf->ezNewPage();
    }

    if(CheckArray($_SESSION["ReportPrint"]["zipcodes"])) {
        if($include_rollup == 1){
            IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["zipcodes"],'zipCodes',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
        } else {
           AreaPrint($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["zipcodes"],'zipCodes',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
           if($include_rollup == 3){
                IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["zipcodes"],'zipCodes',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
            }
        }
    }

    if((CheckArray($_SESSION["ReportPrint"]["counties"]) != 0 && CheckArray($_SESSION["ReportPrint"]["taxing"]) != 0) || (CheckArray($_SESSION["ReportPrint"]["cities"]) != 0 && CheckArray($_SESSION["ReportPrint"]["taxing"]) != 0) || (CheckArray($_SESSION["ReportPrint"]["zipcodes"]) != 0 && CheckArray($_SESSION["ReportPrint"]["taxing"]) != 0)) {
        $pdf->ezNewPage();
    }
    if(CheckArray($_SESSION["ReportPrint"]["taxing"])) {
        if($include_rollup == 1){
            IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["taxing"],'taxing',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
        } else {
           AreaPrint($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["taxing"],'taxing',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
           if($include_rollup == 3){
                IncludeRollup1($_SESSION["ReportPrint"]["equips"],$_SESSION["ReportPrint"]["taxing"],'taxing',$_SESSION["ReportPrint"]["post"],$pdf,$ageRangePass,$show_depreciation);
            }
        }
    }
    
    function AreaPrint($equipArray,$areaArray,$areaType,$post,$pdf,$ageRangePassFunc,$show_depreciation) {
        $normal = 0;
        $align = array();
        $align["justification"] = "center";
        $loanCount = 0;
        $tableoptions = array("showLines" => 1, "showHeadings" => 1, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("name" => array("width" => 100))); 
        $tableoptionsTotal = array("showLines" => 1, "showHeadings" => 0, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("name" => array("width" => 400)));
        $tableoptionsAgeRange = array("showLines" => 1, "showHeadings" => 1, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("areaname" => array("width" => 55),"ageRange" => array("width" => 45),"PurchaseCost" => array("width" => 75),"CurrentCost" => array("width" => 75))); 
        $tableoptionsEquipName = array("showLines" => 1, "showHeadings" => 1, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("equipName" => array("width" => 400))); 
        $depreciation = '';
        if($show_depreciation){
            array_push($tableoptions, array("name" => "equip name", "type" => "type", "purchaseCost" => "purchase cost", "currentCost" => "current cost", "depreciation" => "depreciation", "condition" => "condition", "loans" => "loans"));
            array_push($tableoptionsAgeRange, array("areaname" => "area name","ageRange" => "age range","name"=>"Name","purchaseCost"=>"purchase cost","currentCost"=>"current cost", "depreciation" => "depreciation","condition"=>"condition","total" => "total loans"));
            
        } else {
            array_push($tableoptions, array("name" => "equip name", "type" => "type", "purchaseCost" => "purchase cost", "currentCost" => "current cost", "condition" => "condition", "loans" => "loans"));
            array_push($tableoptionsAgeRange, array("ageRange" => "age range","name"=>"Name","purchaseCost"=>"purchase cost","currentCost"=>"current cost","condition"=>"condition","total" => "total loans"));
        }

        array_push($tableoptionsTotal, array("name" => "equip name","total" => "total loans"));
        array_push($tableoptionsEquipName, array("equipName" => "equip name"));

        switch($areaType){
            case 'counties':
                $location = 'counties';
                $LocationName = 'Counties';
                break;
            case 'cities':
                $location = 'cities';
                $LocationName = 'Cities';
                break;
            case 'zipCodes':
                $location = 'zipCodes';
                $LocationName = 'Zip Codes';
                break;
            case 'taxing':
                $location = 'taxing';
                $LocationName = 'Taxing Authorities';
                break;

        }
        foreach ($areaArray as $area) {
            $table = array();
            $tableTotal = array();
            $tableAgeRange = array();
            $tableEquipName = array();
            $totalEquipCount = 0;

            $notEmpty = 0;
            foreach ($equipArray as $equip) {
                $count = GetEquipmentCount($equip,$area, $location, $post);

                $locationEquipment = $count;
                if(!empty($count)){
                    if($ageRangePassFunc){
                        $notEmpty = 1;
                        if(!empty($count['AgeRange'])){
                            $flag = 1;
                            $ageRangeCount = count($post["age_range"])/2;
                            for($i=0; $i < $ageRangeCount; $i++) { 
                                $ageDisplay[$post["age_range"][$flag].'_'.$post["age_range"][$flag+1]] = $post["age_range"][$flag].' - '.$post["age_range"][$flag+1]; 
                                $flag += 2; 
                            }
                            $ageRangeArray = $count['AgeRange'];
                            if(isset($count['summary'])){
                                $summaryArray = $count['summary'];                            
                            }
                            $total =0;
                            foreach ($ageRangeArray as $AgeRangeKey => $AgeRangeValue) {
                                if($AgeRangeKey != 'summary'){
                                    foreach ($ageDisplay as $dataAgeKey => $dataAgeValue) {
                                        if(isset($AgeRangeValue[$dataAgeKey])){
                                            $depreciation = ',';
                                            $depreciation .= $AgeRangeValue[$dataAgeKey]['purchaseCost'] - $AgeRangeValue[$dataAgeKey]['currentCost'].',';
                                            $loanCount += $AgeRangeValue[$dataAgeKey]['loans'];  
                                            $totalEquipCount += $AgeRangeValue[$dataAgeKey]['loans'];

                                            if($post['sum_names'] != 'summary'){
                                                array_push($table, array(
                                                                            "areaname" => $AgeRangeKey,
                                                                            "ageRange" => $dataAgeValue,
                                                                            "name"=>$AgeRangeValue[$dataAgeKey]['type'], 
                                                                            "purchaseCost"=> $AgeRangeValue[$dataAgeKey]['purchaseCost'],
                                                                            "currentCost"=> $AgeRangeValue[$dataAgeKey]['currentCost'],
                                                                            "depreciation"=> $AgeRangeValue[$dataAgeKey]['purchaseCost'] - $AgeRangeValue[$dataAgeKey]['currentCost'],
                                                                            "condition"=> $AgeRangeValue[$dataAgeKey]['condition'], 
                                                                            "loans" => $AgeRangeValue[$dataAgeKey]['loans']));
                                            }
                                            $name = array();
                                            if($_SESSION['sum_names'] == 'show'){
                                                if(!empty($summaryArray)){
                                                    foreach ($summaryArray as $equipDataSummary) {
                                                        if($equipDataSummary['Equipment_id'] == $AgeRangeValue[$dataAgeKey]['Equipment_id']){
                                                            if(is_array($AgeRangeValue[$dataAgeKey]['loan_date'])){
                                                                if(in_array($equipDataSummary['loan_date'], $AgeRangeValue[$dataAgeKey]['loan_date'])){
                                                                    if(!in_array($equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'],$name)){
                                                                        $name[$equipDataSummary['consumerId']] = $equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'];
                                                                    }   
                                                                }
                                                            }else{
                                                                if($equipDataSummary['loan_date'] == $AgeRangeValue[$dataAgeKey]['loan_date']){
                                                                    if(!in_array($equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'],$name)){
                                                                        $name[$equipDataSummary['consumerId']] = $equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'];
                                                                    }   
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $fullname = '';
                                                    foreach ($name as $nameKey => $nameValue) {
                                                        array_push($table, array(
                                                                            "areaname" => $nameValue,
                                                                            "ageRange" => '',
                                                                            "name"=>'', 
                                                                            "purchaseCost"=> '',
                                                                            "currentCost"=> '',
                                                                            "depreciation"=>'',
                                                                            "condition"=>'', 
                                                                            "loans" => ''));
                                                    }
                                                }
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                    } else {
                        if($post['sum_names'] == 'show'){
                            if(!empty($count['summary'])){
                                $notEmpty = 1;
                                foreach ($count as $equipDataKey => $equipDataValue) {
                                    if(is_numeric($equipDataKey)){
                                        $loanCount += $equipDataValue['loans'];
                                        $totalEquipCount += $equipDataValue['loans'];
                                        array_push($table, array("name" => $equipDataValue['name'], "type" => $equipDataValue['type'], "purchaseCost" => $equipDataValue['purchaseCost'], "currentCost" => $equipDataValue['currentCost'],"depreciation"=>$equipDataValue['purchaseCost'] - $equipDataValue['currentCost'], "condition" => $equipDataValue['condition'], "loans" => $equipDataValue['loans']));
                                        if($_SESSION['sum_names'] == 'show'){
                                            $arrayNames =  array();
                                            foreach ($count['summary'] as $cntsum) {
                                                if($cntsum['Equipment_id'] == $equipDataValue['Equipment_id']){
                                                    if(!in_array($cntsum['fname'],$arrayNames)){
                                                        $name = $cntsum['fname'].' '.$cntsum['midname'].' '.$cntsum['lname'];
                                                        array_push($table, array("name" => '- '.$name, "type" => '', "purchaseCost" => '', "currentCost" => '', "depreciation" => '', "condition" => '', "loans" => ''));
                                                    }

                                                    array_push($arrayNames,$cntsum['fname']);
                                                }
                                            }
                                        }
                                    }
                                }    
                            }
                        } else if($post['sum_names'] == 'summary'){
                            $normal += 1;
                            $notEmpty = 1;
                            foreach ($count as $equipData) {
                                $loanCount += $equipData['loans'];
                                $totalEquipCount += $equipData['loans'];
                            }
                        } else {
                            $normal += 1;
                            $notEmpty = 1;
                            foreach ($count as $equipData) {
                                if($show_depreciation){
                                    $depreciationValue = number_format($equipData['purchaseCost'] - $equipData['currentCost'], 2);
                                }
                                $loanCount += $equipData['loans'];
                                $totalEquipCount += $equipData['loans'];
                                if($show_depreciation){
                                    array_push($table, array("name" => $equipData['name'], "type" => $equipData['type'], "purchaseCost" => $equipData['purchaseCost'], "currentCost" => $equipData['currentCost'],"depreciation" => $depreciationValue, "condition" => $equipData['condition'], "loans" => $equipData['loans']));
                                } else {
                                    array_push($table, array("name" => $equipData['name'], "type" => $equipData['type'], "purchaseCost" => $equipData['purchaseCost'], "currentCost" => $equipData['currentCost'], "condition" => $equipData['condition'], "loans" => $equipData['loans']));
                                }
                            }
                        }
                    }
                }
            }
            $areaName = $area;
            if($areaType == 'counties'){
                $county = GetCounty($area);
                $areaName = str_replace(", ", " ", $county[0]['name']);    
            }

            if($areaType == 'taxing'){
                $taxingName = api_DoSQL("SELECT name FROM CIL_TaxingAuthority WHERE id=:id",array('id' => $area));
                $areaName = $taxingName[0]['name'];
            }

            if($normal != 0 && $notEmpty == 1){

                if($show_depreciation){
                    array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '',"depreciation" => '', "condition" => '', "loans" => $totalEquipCount));
                    $pdf->ezTable($table, array("name" => '<b>'.$areaName.'</b>', "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>","depreciation" => "<b>Depreciation</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);
                } else {
                    array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '', "condition" => '', "loans" => $totalEquipCount));
                    $pdf->ezTable($table, array("name" => '<b>'.$areaName.'</b>', "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);
                }
                $pdf->ezText("\n", 12, $align);
                $normal = 0;
            }

            if($ageRangePassFunc && $notEmpty == 1){
                    
                    if($totalEquipCount > 0){
                        array_push($table, array("areaname" => '<b>Total</b>',"ageRange" => "","name"=>"","purchaseCost"=>"","currentCost"=>"","depreciation"=>"","condition"=>"", "loans" => $totalEquipCount));
                        $pdf->ezTable($table, array("areaname"=>"<b>".$areaName."</b>","ageRange" => "<b>Age Range</b>","name"=>"<b>Name</b>","purchaseCost"=>"<b>Purchase cost</b>","currentCost"=>"<b>Current cost</b>","condition"=>"<b>Condition</b>","depreciation" => "<b>Depreciation</b>", "loans" => "<b>Loans</b>"), '', $tableoptionsAgeRange);    
                        $pdf->ezText("\n", 12, $align);
                    }
            }

            if($_SESSION['sum_names'] == 'show' && $notEmpty == 1 && $ageRangePassFunc != 1){
                if($show_depreciation){
                    array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '',"depreciation" => '', "condition" => '', "loans" => $totalEquipCount));
                    $pdf->ezTable($table, array("name" => $areaName, "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>","depreciation" => "<b>Depreciation</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);       
                } else {
                    array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '', "condition" => '', "loans" => $totalEquipCount));
                    $pdf->ezTable($table, array("name" => $areaName, "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);       
                }
                $pdf->ezText("\n", 12, $align);
            }
            
        }
        // exit;
        array_push($tableTotal, array("name" => '<b>Total of all '.$LocationName.'</b>', "total"=> $loanCount));
        $pdf->ezTable($tableTotal, array("name" => "<b></b>", "total" => "<b>Total</b>"), '', $tableoptionsTotal);
        $pdf->ezText("\n", 12, $align);
    }

    function IncludeRollup1($equipArray,$areaArray,$areaType,$post,$pdf,$ageRangePassFunc,$show_depreciation){
        $filtered = array();
        $align = array();
        $tableTotal = array();
        $align["justification"] = "center";


        switch($areaType){
            case 'counties':
                $location = 'counties';
                $LocationName = 'Counties';
                break;
            case 'cities':
                $location = 'cities';
                $LocationName = 'Cities';
                break;
            case 'zipCodes':
                $location = 'zipCodes';
                $LocationName = 'Zip Codes';
                break;
            case 'taxing':
                $location = 'taxing';
                $LocationName = 'Taxing Authorities';
                break;

        }


        $tableoptions = array("showLines" => 1, "showHeadings" => 1, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("name" => array("width" => 75),"type" => array("width" => 75),"purchaseCost" => array("width" => 75),"currentCost" => array("width" => 75))); 
        $tableoptionsTotal = array("showLines" => 1, "showHeadings" => 0, "shaded" => 1, "protectRows" => 10, "xPos" => 300, "width" => 475, "cols" => array("name" => array("width" => 400)));
        if($show_depreciation){
            array_push($tableoptions, array("name" => "equip name", "type" => "type", "purchaseCost" => "purchase cost", "currentCost" => "current cost","depreciation" => "depreciation", "condition" => "condition", "loans" => "loans"));            
        } else {
            array_push($tableoptions, array("name" => "equip name", "type" => "type", "purchaseCost" => "purchase cost", "currentCost" => "current cost", "condition" => "condition", "loans" => "loans"));            
        }
        array_push($tableoptionsTotal, array("name" => "equip name","total" => "total loans"));
        
        $table = array();
        $dataSummary = array();
        $summary = getSumdata($areaArray,$equipArray,$post,$areaType);
        $total = 0;

        if(!empty($post['age_range'][1])){
            $flag = 1;
            $ageRangeCount = count($post["age_range"])/2;
            for($i=0; $i < $ageRangeCount; $i++) { 
                $ageDisplay[$post["age_range"][$flag].' - '.$post["age_range"][$flag+1]] = $post["age_range"][$flag].' - '.$post["age_range"][$flag+1]; 
                $flag += 2; 
            }
            foreach ($summary as $summaryKey => $summaryValue) {
                if($summaryKey != 'summary' && $summaryKey != 'equipmentList'){
                    array_push($table, array("name" => '</b>'.$summaryKey.'</b>', "type" => '', "purchaseCost" => '', "currentCost" => '',"depreciation" => '', "condition" => '', "loans" =>''));
                    foreach ($ageDisplay as $ageDisplayKey => $ageDisplayValue) {
                        if(isset($summaryValue[$ageDisplayValue])){
                            $total += $summaryValue[$ageDisplayValue]['loans'];
                            array_push($table, array("name" => $ageDisplayValue, "type" => $summaryValue[$ageDisplayValue]['type'][0], "purchaseCost" => $summaryValue[$ageDisplayValue]['purchaseCost'][0], "currentCost" =>$summaryValue[$ageDisplayValue]['currentCost'][0],"depreciation" => $summaryValue[$ageDisplayValue]['purchaseCost'][0] - $summaryValue[$ageDisplayValue]['currentCost'][0], "condition" => $summaryValue[$ageDisplayValue]['condition'][0], "loans" =>$summaryValue[$ageDisplayValue]['loans']));
                            if($post['sum_names'] == 'show'){
                                $name = array();
                                foreach ($summary['summary'] as $equipDataSummary) {
                                    foreach ($equipDataSummary as $equipDataSumKey => $equipDataSumValue) {
                                        if($equipDataSumValue['Equipment_id'] == $summary['equipmentList'][$summaryKey]){
                                            if(in_array($equipDataSumValue['loan_date'], $summaryValue[$ageDisplayValue]['loanDate'])){
                                                if(!in_array($equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'],$name)){
                                                    $name[$equipDataSumValue['consumerId']] = $equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'];
                                                }

                                            }
                                        }
                                    }

                                }
                                foreach ($name as $nameKey => $nameValue) {
                                    array_push($table, array("name" => $nameValue, "type" => '', "purchaseCost" => '', "currentCost" =>'',"depreciation" => '', "condition" => '', "loans" =>''));
                                }
                            }
                        }else{
                            array_push($table, array("name" => $ageDisplayKey, "type" => '', "purchaseCost" => '', "currentCost" => '',"depreciation" => '', "condition" => '', "loans" =>''));
                        }
                    }
                }
            }

        }else{
            foreach ($summary as $summaryKey => $summaryValue) {
                if($summaryKey != 'summary' && $summaryKey != 'equipmentList'){
                    array_push($table, array("name" => $summaryKey, "type" => $summaryValue['type'][0], "purchaseCost" => $summaryValue['purchaseCost'][0], "currentCost" =>$summaryValue['currentCost'][0],"depreciation" => $summaryValue['purchaseCost'][0] - $summaryValue['currentCost'][0], "condition" => $summaryValue['condition'][0], "loans" =>$summaryValue['loans']));
                    $total += $summaryValue['loans'];

                    if($post['sum_names'] == 'show'){
                        $name = array();
                        foreach ($summary['summary'] as $equipDataSummary) {
                            foreach ($equipDataSummary as $equipDataSumKey => $equipDataSumValue) {
                                if($equipDataSumValue['Equipment_id'] == $summaryValue['equipmentId'][0]){
                                    if(!in_array($equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'],$name)){
                                        $name[$equipDataSumValue['consumerId']] = $equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'];
                                    }
                                }
                            }

                        }
                        foreach ($name as $nameKey => $nameValue) {
                            array_push($table, array("name" => $nameValue, "type" => '', "purchaseCost" => '', "currentCost" =>'',"depreciation" => '', "condition" => '', "loans" =>''));
                        }
                    }
                }   
            }  
        }


        if($show_depreciation){
            array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '',"depreciation" => '', "condition" => '', "loans" => $total));
            $pdf->ezTable($table, array("name" => '<b>Name</b>', "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>","depreciation" => "<b>Depreciation</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);
        } else {
            array_push($table, array("name" => '<b>Total</b>', "type" => '', "purchaseCost" => '', "currentCost" => '', "condition" => '', "loans" => $total));
            $pdf->ezTable($table, array("name" => '<b>Name</b>', "type" => "<b>Type</b>", "purchaseCost" => "<b>Purchase Cost/Value</b>", "currentCost" => "<b>Current Cost/Value</b>", "condition" => "<b>Condition</b>", "loans" => "<b>Loans</b>"), '', $tableoptions);
        }

        $pdf->ezText("\n", 12, $align);
    }

    function getSumdata($locationArray,$equipArray,$post,$location){
        $dataSummary = array();
        $totalLoans = 0;
        $consumerSummary = array();
        foreach ($locationArray as $locationArrayKey => $locationArrayValue) {
            $equipTypeName = array();
            foreach ($equipArray as $equipArrayyKey => $equipArrayyValue) {
                $count = GetEquipmentCount($equipArrayyValue,$locationArrayValue,$location, $post);
                if(!empty($count['summary'])){
                    foreach ($count['summary'] as $key => $value) {
                        if(!isset($consumerSummary[$value['equipmentTypeId']])){
                            $consumerSummary[$value['equipmentTypeId']] = array();
                        }
                        array_push($consumerSummary[$value['equipmentTypeId']],$value);
                    }
                }
                if($count){
                    $filtered = '';
                    if(isset($post['age_range'])){
                        $filtered = array_filter($post["age_range"]);
                    }
                    if(empty($filtered)){
                        foreach ($count as $countKey => $countValue) {
                            if($countKey !== 'summary'){
                                if(!empty($countValue)){
                                    $name = GetEquipName($countValue['Equipment_id']);
                                    if(isset($dataSummary[$countValue['name']])){
                                        
                                        $dataSummary[$countValue['name']]['loans'] += $countValue['loans'];

                                        if(!in_array($countValue['purchaseCost'],$dataSummary[$countValue['name']]['purchaseCost'])){
                                            array_push($dataSummary[$countValue['name']]['purchaseCost'],$countValue['purchaseCost']);
                                        }
                                        if(!in_array($countValue['currentCost'],$dataSummary[$countValue['name']]['currentCost'])){
                                            array_push($dataSummary[$countValue['name']]['currentCost'],$countValue['currentCost']);
                                        }
                                        if(!in_array($countValue['condition'],$dataSummary[$countValue['name']]['condition'])){
                                            array_push($dataSummary[$countValue['name']]['condition'],$countValue['condition']);
                                        }
                                        if(!in_array($countValue['type'],$dataSummary[$countValue['name']]['type'])){
                                            array_push($dataSummary[$countValue['name']]['type'],$countValue['type']);
                                        }
                                        
                                    }else{
     
                                        $dataSummary[$countValue['name']]['loans'] = $countValue['loans'];
                                        $dataSummary[$countValue['name']]['type'] = array($countValue['type']);
                                        $dataSummary[$countValue['name']]['purchaseCost'] = array($countValue['purchaseCost']);
                                        $dataSummary[$countValue['name']]['currentCost'] = array($countValue['currentCost']);
                                        $dataSummary[$countValue['name']]['condition'] = array($countValue['condition']);

                                    }
                                    $dataSummary[$countValue['name']]['equipmentId'] = array($countValue['Equipment_id']);
                                    
                                }
                            }
                        }
                    } else {
                        foreach($count as $equipType => $ageRangeDatas){
                            if($equipType !== 'summary' && $equipType != 'AgeRange'){
                                $ageRangeFilter = array_filter($ageRangeDatas);
                                if(!empty($ageRangeFilter)){
                                    foreach ($ageRangeDatas as $ageRange => $data) {
                                        $ageRangeDate = explode("_",$ageRange);
                                        $ageRangeOnce = $ageRangeDate[0].' - '.$ageRangeDate[1];
                                        $ageLoan = 0;
                                        if(!empty($data)){
                                            foreach ($data as $loans) {
                                                $equipmentList[$loans['name']] = $loans['Equipment_id'];
                                                $totalLoans += $loans["loans"];     
                                                
                                                if(isset($dataSummary[$loans['name']][$ageRangeOnce])){
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['loans'] += $loans['loans'];
                                                    if(!in_array($loans['purchaseCost'],$dataSummary[$loans['name']][$ageRangeOnce]['purchaseCost'])){
                                                        array_push($dataSummary[$loans['name']][$ageRangeOnce]['purchaseCost'],$loans['purchaseCost']);
                                                    }
                                                    if(!in_array($loans['currentCost'],$dataSummary[$loans['name']][$ageRangeOnce]['currentCost'])){
                                                        array_push($dataSummary[$loans['name']][$ageRangeOnce]['currentCost'],$loans['currentCost']);
                                                    }
                                                    if(!in_array($loans['condition'],$dataSummary[$loans['name']][$ageRangeOnce]['condition'])){
                                                        array_push($dataSummary[$loans['name']][$ageRangeOnce]['condition'],$loans['condition']);
                                                    }
                                                    if(!in_array($loans['type'],$dataSummary[$loans['name']][$ageRangeOnce]['type'])){
                                                        array_push($dataSummary[$loans['name']][$ageRangeOnce]['type'],$loans['type']);
                                                    }
                                                    array_push($dataSummary[$loans['name']][$ageRangeOnce]['loanDate'],$loans['loan_date']);
                                                }else{
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['loans'] = $loans['loans'];
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['type'] = array($loans['type']);
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['purchaseCost'] = array($loans['purchaseCost']);
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['currentCost'] = array($loans['currentCost']);
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['condition'] = array($loans['condition']);
                                                    $dataSummary[$loans['name']][$ageRangeOnce]['loanDate'] = array($loans['loan_date']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $dataSummary['summary'] = $consumerSummary;
        $dataSummary['equipmentList'] = !empty($equipmentList)? $equipmentList : '';
        return $dataSummary;
      
    }
    $pdf->ezStream(array("Content-Disposition"=>"EquipmentReport.pdf"));
?>