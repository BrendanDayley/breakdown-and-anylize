<?php if(isset($_POST["county"]) && count($_POST["county"])>0) { ?>
    <tr>
        <td>
            <?php
            if($include_rollup==2 || $include_rollup==3) {

                $a_cnt = count($_POST["county"]);
                $totalcounty = 0;

                for($x=0;$x<$a_cnt;$x++) {
                    $totalLoans = 0;
                    $gotTotalResults = $showBlank;
                    $showHeader = 0;
                    $countyHeader = '';
                    if($_POST["county"][$x] == 0) {
                        $county[0]["name"] = "Unknown County";
                    } else {
                        $county = GetCounty($_POST["county"][$x]);
                    }

                    $headerString1 = 'Name';
                    $headerString2 = 'Type';
                    if(isset($_POST['age_display'])){
                        $headerString1 = 'Type';
                        $headerString2 = 'Name';
                    }

                    $locationHeader = getHeader(0,$county[0]["name"],'County',$headerString1,$headerString2);
                    
                    $e_cnt = count($equip);
                    for($y=0;$y<$e_cnt;$y++) {
                        $gotResults = $showBlank;
                        $count = GetEquipmentCount($equip[$y],$_POST["county"][$x],'counties', $_POST); 
                        if(!$gotResults) {
                            continue;
                        } elseif(!$showHeader) {
                            $showHeader = 1;
                            echo($locationHeader);
                            $gotTotalResults = 1;
                        }

                        if($count) {
                            if($count > 1) {
                                if(!isset($_POST["age_display"])){
                                    foreach($count as $key => $cnt) {
                                        if(is_numeric($key)){
                                            if($_SESSION['sum_names'] != 'summary'){
                                                printRowWithLink(
                                                    $cnt["Equipment_id"], 
                                                    $cnt["name"], 
                                                    $cnt["type"], 
                                                    $cnt["purchaseCost"], 
                                                    $cnt["currentCost"], 
                                                    $cnt["purchaseCost"] - $cnt["currentCost"], 
                                                    $cnt["condition"], 
                                                    $cnt["loans"]);

                                                if($_SESSION['sum_names'] == 'show'){
                                                    $arrayNames =  array(); 
                                                    echo '<tr><td colspan="7" class="tablecell">';
                                                    foreach ($count['summary'] as $cntsum) {
                                                        if($cntsum['Equipment_id'] == $cnt['Equipment_id']){
                                                            if(!in_array($cntsum['fname'],$arrayNames)){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$cntsum['consumerId'].'&tab=equip_loans " target="_blank">'.$cntsum['fname'].' '.$cntsum['midname'].' '.$cntsum['lname'].'</a><br>';        
                                                            }
                                                            array_push($arrayNames,$cntsum['fname']);
                                                        }
                                                    }
                                                    echo '</td></tr>';   
                                                }
                                            }
                                            $totalLoans += $cnt["loans"];
                                        }
                                    }
                                } else {

                                    if(!empty($count['AgeRange'])){
                                        if($_SESSION['sum_names'] != 'summary'){
                                            if(isset($count['summary'])){
                                                $count['AgeRange']['summary'] = $count['summary'];
                                            }
                                            $totalLoans += printAgeRange($count['AgeRange'],$_POST);
                                        }else{
                                            $totalLoans += CountSummary($count['AgeRange']);
                                        }
                                    }else{
                                        $totalLoans += 0;
                                    }
                                    
                                }
                            } else {
                                  printRowWithLink(
                                                    $count[0]["Equipment_id"], 
                                                    $count[0]["name"], 
                                                    $count[0]["type"], 
                                                    $count[0]["purchaseCost"], 
                                                    $count[0]["currentCost"], 
                                                    $count[0]["purchaseCost"] - $count[0]["currentCost"], 
                                                    $count[0]["condition"], 
                                                    $count[0]["loans"]);

                                $totalLoans += $count[0]["loans"];
                            }
                        } 
                    }
     
                    if($gotTotalResults) {
                        printf('<tr>
                                    <td class="tablehead" style="text-align: left;">%s %s</td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;">%s</td>
                        </tr></table></div><br /><br />',$county[0]["name"],__("Totals"),$totalLoans);
                        $totalcounty += $totalLoans;
                    }
                }
            ?>

            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $totalcounty; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br />
           
      <?php } else { 

          $total = printSummary('Counties',$_POST["county"],$equip,$_POST,'counties');
      ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
            <?php } ?>
        <br /><br />
        <?php 
            if($include_rollup == 3){
                $total = printSummary('Counties',$_POST["county"],$equip,$_POST,'counties');
            ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
          <?php  }
         ?>
        </td>
    </tr>
<?php } ?>
<?php if(isset($_POST["city"]) && count($_POST["city"])>0) { ?>
    <tr>
        <td>
            <?php
            if($include_rollup==2 || $include_rollup==3) {
                $c_cnt = count($_POST["city"]);
                $totalcity = 0;

                for($x=0;$x<$c_cnt;$x++) {
                    $totalLoans = 0;
                    $gotTotalResults = $showBlank;
                    $showHeader = 0;
                    $cityHeader = '';

                    $headerString1 = 'Name';
                    $headerString2 = 'Type';
                    if(isset($_POST['age_display'])){
                        $headerString1 = 'Type';
                        $headerString2 = 'Name';
                    }

                    $cityHeader = getHeader(0,$_POST["city"][$x],'City',$headerString1,$headerString2);

                    $e_cnt = count($equip);
                        echo($cityHeader);

                    for($y=0;$y<$e_cnt;$y++) {
                        $gotResults = $showBlank;
                        $count = GetEquipmentCount($equip[$y],$_POST["city"][$x],'cities', $_POST);
                            
                        if($count) {
                            if($count > 1) {
                                if(!isset($_POST["age_display"])){
                                    foreach($count as $key => $cnt) {
                                        if(is_numeric($key)){
                                            if($_SESSION['sum_names'] != 'summary'){
                                                printRowWithLink(
                                                    $cnt["Equipment_id"], 
                                                    $cnt["name"], 
                                                    $cnt["type"], 
                                                    $cnt["purchaseCost"], 
                                                    $cnt["currentCost"], 
                                                    $cnt["purchaseCost"] - $cnt["currentCost"], 
                                                    $cnt["condition"], 
                                                    $cnt["loans"]);
                                                

                                                if($_SESSION['sum_names'] == 'show'){
                                                    $arrayNames =  array(); 
                                                    echo '<tr><td colspan="7" class="tablecell">';
                                                    foreach ($count['summary'] as $cntsum) {
                                                        if($cntsum['Equipment_id'] == $cnt['Equipment_id']){
                                                            if(!in_array($cntsum['fname'],$arrayNames)){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$cntsum['consumerId'].'&tab=equip_loans " target="_blank">'.$cntsum['fname'].' '.$cntsum['midname'].' '.$cntsum['lname'].'</a><br>';        
                                                            }
                                                            array_push($arrayNames,$cntsum['fname']);
                                                        }
                                                    }
                                                    echo '</td></tr>';   
                                                }
                                            }
                                            $totalLoans += $cnt["loans"];
                                        }
                                    }
                                } else {
                                    
                                    if(!empty($count['AgeRange'])){
                                        if($_SESSION['sum_names'] != 'summary'){
                                            if(isset($count['summary'])){
                                                $count['AgeRange']['summary'] = $count['summary'];
                                            }
                                            $totalLoans += printAgeRange($count['AgeRange'],$_POST);
                                        }else{
                                            $totalLoans += CountSummary($count['AgeRange']);
                                        }
                                    }else{
                                        $totalLoans += 0;
                                    }
                                }
                            } else {
                                  printRowWithLink(
                                                    $count[0]["Equipment_id"], 
                                                    $count[0]["name"], 
                                                    $count[0]["type"], 
                                                    $count[0]["purchaseCost"], 
                                                    $count[0]["currentCost"], 
                                                    $count[0]["purchaseCost"] - $count[0]["currentCost"], 
                                                    $count[0]["condition"], 
                                                    $count[0]["loans"]);
                                
                                $totalLoans += $count[0]["loans"];
                            }
                        } 
                    }
                    printf('<tr>
                                    <td class="tablehead" style="text-align: left;">%s %s</td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;"></td>
                                    <td class="tablehead" style="text-align: center;">%s</td>
                        </tr></table></div><br /><br />', $_POST["city"][$x],__("Totals"),$totalLoans);
                    $totalcity += $totalLoans;
                }
            ?><div class="border">
            <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tablehead" width="95%">
                        <?php echo(__("Total of all Cities")); ?>
                    </td>
                    <td style="text-align: center;" class="tablehead" width="5%">
                        <?php echo($totalcity); ?>
                    </td>
                </tr>
            </table></div><br /><br />
        <?php } else { 
                $total = printSummary('Cities',$_POST["city"],$equip,$_POST,'cities');
        ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Cities")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
            <?php } ?>
        <br /><br />
        <?php 
            if($include_rollup == 3){
                $total = printSummary('Cities',$_POST["city"],$equip,$_POST,'cities');
            ?>
                           <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
          <?php  }
         ?>
        </td>
    </tr>
<?php } ?>
<?php if(isset($_POST["zipcodes"]) && count($_POST["zipcodes"])>0) { ?>
    <tr>
        <td>
            <?php
            if($include_rollup==2 || $include_rollup==3) {
                $z_cnt = count($_POST["zipcodes"]);
                $totalzip = 0;

                for($x=0;$x<$z_cnt;$x++) {
                    $totalLoans = 0;
                    $gotTotalResults = $showBlank;
                    $showHeader = 0;
                    $zipHeader = '';

                    $headerString1 = 'Name';
                    $headerString2 = 'Type';
                    if(isset($_POST['age_display'])){
                        $headerString1 = 'Type';
                        $headerString2 = 'Name';
                    }

                    $zipHeader = getHeader(0,$_POST["zipcodes"][$x],'ZipCodes',$headerString1,$headerString2);

                    $e_cnt = count($equip);
                        echo($zipHeader);

                    for($y=0;$y<$e_cnt;$y++) {
                        $gotResults = $showBlank;
                        $count = GetEquipmentCount($equip[$y],$_POST["zipcodes"][$x],'zipCodes',$_POST);
                                
                        if($count) {
                            if($count > 1) {
                                if(!isset($_POST["age_display"])){
                                    foreach($count as $key => $cnt) {
                                        if(is_numeric($key)){
                                            if($_SESSION['sum_names'] != 'summary'){
                                                printRowWithLink(
                                                    $cnt["Equipment_id"], 
                                                    $cnt["name"], 
                                                    $cnt["type"], 
                                                    $cnt["purchaseCost"], 
                                                    $cnt["currentCost"], 
                                                    $cnt["purchaseCost"] - $cnt["currentCost"], 
                                                    $cnt["condition"], 
                                                    $cnt["loans"]);

                                                if($_SESSION['sum_names'] == 'show'){
                                                    $arrayNames =  array(); 
                                                    echo '<tr><td colspan="7" class="tablecell">';
                                                    foreach ($count['summary'] as $cntsum) {
                                                        if($cntsum['Equipment_id'] == $cnt['Equipment_id']){
                                                            if(!in_array($cntsum['fname'],$arrayNames)){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$cntsum['consumerId'].'&tab=equip_loans" target="_blank">'.$cntsum['fname'].' '.$cntsum['midname'].' '.$cntsum['lname'].'</a><br>';        
                                                            }
                                                            array_push($arrayNames,$cntsum['fname']);
                                                        }
                                                    }
                                                    echo '</td></tr>';   
                                                }
                                            }
                                            $totalLoans += $cnt["loans"];
                                        }
                                    }
                                } else {
                                    
                                    if(!empty($count['AgeRange'])){
                                        if($_SESSION['sum_names'] != 'summary'){
                                            if(isset($count['summary'])){
                                                $count['AgeRange']['summary'] = $count['summary'];
                                            }
                                            $totalLoans += printAgeRange($count['AgeRange'],$_POST);
                                        }else{
                                            $totalLoans += CountSummary($count['AgeRange']);
                                        }
                                    }else{
                                        $totalLoans += 0;
                                    }
                                }
                            } else {
                                printRowWithLink(
                                                    $count[0]["Equipment_id"], 
                                                    $count[0]["name"], 
                                                    $count[0]["type"], 
                                                    $count[0]["purchaseCost"], 
                                                    $count[0]["currentCost"], 
                                                    $count[0]["purchaseCost"] - $count[0]["currentCost"], 
                                                    $count[0]["condition"], 
                                                    $count[0]["loans"]);
                                
                                $totalLoans += $count[0]["loans"];
                            }
                        }
                    }
                    printf('<tr>
                        <td class="tablehead" style="text-align: left;">%s %s</td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;">%s</td>
                    </tr></table></div><br /><br />',$_POST["zipcodes"][$x],__("Totals"),$totalLoans);
                    $totalzip += $totalLoans;
                }
            ?><div class="border">
            <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tablehead" width="95%">
                        <?php echo(__("Total of all Zip Codes")); ?>
                    </td>
                    <td  style="text-align: center;" class="tablehead" width="5%">
                        <?php echo($totalzip); ?>
                    </td>
                </tr>
            </table></div>
            <?php } else {
                $total = printSummary('zipCodes',$_POST["zipcodes"],$equip,$_POST,'zipCodes');
        ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Zip Codes")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
            <?php } ?>
            <br /><br />
        <?php 
            if($include_rollup == 3){
                $total = printSummary('zipCodes',$_POST["zipcodes"],$equip,$_POST,'zipCodes');
            ?> 
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
          <?php  }
         ?>
        </td>
    </tr>
<?php } ?>
<?php if(isset($_POST["taxing"]) && count($_POST["taxing"])>0) { ?>
    <tr>
        <td>
            <?php
            if($include_rollup==2 || $include_rollup==3) {
                $tcnt = count($_POST["taxing"]);
                $totaltax = 0;

                for($x=0;$x<$tcnt;$x++) {
                    $totalLoans = 0;
                    $gotTotalResults = $showBlank;
                    $showHeader = 0;
                    $taxHeader = '';
                    
                    if($_POST["taxing"][$x]) {
                        $taxingName = api_DoSQL("SELECT name FROM CIL_TaxingAuthority WHERE id=:id",array('id' => $_POST["taxing"][$x]));
                    } else {
                        $taxingName[0]['name'] = 'Unknown';
                    }

                    $headerString1 = 'Name';
                    $headerString2 = 'Type';
                    if(isset($_POST['age_display'])){
                        $headerString1 = 'Type';
                        $headerString2 = 'Name';
                    }

                    $taxHeader = getHeader(0,$taxingName[0]['name'],'Taxing Authorities',$headerString1,$headerString2);

                    $e_cnt = count($equip);
                        echo($taxHeader);

                    $e_cnt = count($equip);
                    

                    for($y=0;$y<$e_cnt;$y++) {
                        $gotResults = $showBlank;
                        $count = GetEquipmentCount($equip[$y],$_POST["taxing"][$x],'taxing',$_POST);
                        
                        if($count) {
                            if($count > 1) {
                                if(!isset($_POST["age_display"])){
                                    foreach($count as $key => $cnt) {
                                        if(is_numeric($key)){
                                            if($_SESSION['sum_names'] != 'summary'){
                                                printRowWithLink(
                                                    $cnt["Equipment_id"], 
                                                    $cnt["name"], 
                                                    $cnt["type"], 
                                                    $cnt["purchaseCost"], 
                                                    $cnt["currentCost"], 
                                                    $cnt["purchaseCost"] - $cnt["currentCost"], 
                                                    $cnt["condition"], 
                                                    $cnt["loans"]);

                                                if($_SESSION['sum_names'] == 'show'){
                                                    $arrayNames =  array(); 
                                                    echo '<tr><td colspan="7" class="tablecell">';
                                                    foreach ($count['summary'] as $cntsum) {
                                                        if($cntsum['Equipment_id'] == $cnt['Equipment_id']){
                                                            if(!in_array($cntsum['fname'],$arrayNames)){
                                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$cntsum['consumerId'].'&tab=equip_loans " target="_blank">'.$cntsum['fname'].' '.$cntsum['midname'].' '.$cntsum['lname'].'</a><br>';        
                                                            }
                                                            array_push($arrayNames,$cntsum['fname']);
                                                        }
                                                    }
                                                    echo '</td></tr>';   
                                                }
                                            }
                                            $totalLoans += $cnt["loans"];
                                        }
                                    }
                                } else {
                                    
                                    if(!empty($count['AgeRange'])){
                                        if($_SESSION['sum_names'] != 'summary'){
                                            if(isset($count['summary'])){
                                                $count['AgeRange']['summary'] = $count['summary'];
                                            }
                                            $totalLoans += printAgeRange($count['AgeRange'],$_POST);
                                        }else{
                                            $totalLoans += CountSummary($count['AgeRange']);
                                        }
                                    }else{
                                        $totalLoans += 0;
                                    }
                                }
                            } else {
                                  printRowWithLink(
                                                    $count[0]["Equipment_id"], 
                                                    $count[0]["name"], 
                                                    $count[0]["type"], 
                                                    $count[0]["purchaseCost"], 
                                                    $count[0]["currentCost"], 
                                                    $count[0]["purchaseCost"] - $count[0]["currentCost"], 
                                                    $count[0]["condition"], 
                                                    $count[0]["loans"]);
                                
                                $totalLoans += $count[0]["loans"];
                            }
                        }
                    }
                    printf('<tr>
                        <td class="tablehead" style="text-align: left;">%s %s</td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;"></td>
                        <td class="tablehead" style="text-align: center;">%s</td>
                    </tr></table></div><br /><br />',$taxingName[0]['name'],__("Totals"),$totalLoans);
                    $totaltax += $totalLoans;
                }
            ?><div class="border">
            <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tablehead" width="95%">
                        <?php echo(__("Total of all Taxing Authorities")); ?>
                    </td>
                    <td  style="text-align: center;" class="tablehead" width="5%">
                        <?php echo($totaltax); ?>
                    </td>
                </tr>
            </table></div>
            <?php } else {
                $total = printSummary('Taxing Authorities',$_POST["taxing"],$equip,$_POST,'taxing');
        ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Taxing Authorities")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
            <?php } ?><br /><br />
            
        <?php 
            if($include_rollup == 3){
                $total = printSummary('Taxing Authorities',$_POST["taxing"],$equip,$_POST,'taxing');
            ?>
                           <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="95%">
                            <?php echo(__("Total of all Counties")); ?>
                        </td>
                        <td  style="text-align: center;" class="tablehead" width="5%">
                            <?php echo $total; ?>
                        </td>
                    </tr>
                </table>
            </div><br /><br /> 
          <?php  }
         ?>
        </td>
    </tr>    
<?php } ?>

<?php 
    function getHeader($dataHeader = null,$locationName = null, $location = null,$headerString1 = null, $headerString2 = null){
        if($dataHeader == 1){
            $header = sprintf('<div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" colspan="7">%s</td>
                    </tr>
                     <tr>
                        <td class="tablehead">%s</td>
                        <td class="tablehead">%s</td>
                        <td class="tablehead">%s</td>
                        <td class="tablehead">%s</td>
                        <td class="tablehead">%s</td>
                        <td class="tablehead">%s</td>
                        <td class="tablehead" width="5%%">%s</td>
                    </tr>',
                    __('Totals by Equipment Type of all '.$location),
                    __("Name"),
                    __('Type'),
                    __('Purchase Cost/Value'),
                    __('Current Cost/Value'),
                    __('Depreciation'),
                    __('Condition'),
                    __('# of loans'));
        } else {
            $header = sprintf('<div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr><td class="tablehead">%s</td><td class="tablehead"></td><td class="tablehead"></td><td class="tablehead"></td><td class="tablehead"></td><td class="tablehead"></td><td class="tablehead"></td></tr>
                    <tr>
                        <td class="tablehead" width="20%%">%s</td>
                        <td class="tablehead" width="20%%">%s</td>
                        <td class="tablehead" width="20%%">%s</td>
                        <td class="tablehead" width="20%%">%s</td>
                        <td class="tablehead" width="7%%">%s</td>
                        <td class="tablehead" width="7%%">%s</td>
                        <td class="tablehead" width="5%%">%s</td>
                    </tr>',
                    $locationName,
                    __($headerString1),
                    __($headerString2),
                    __('Purchase Cost/Value'),
                    __('Current Cost/Value'),
                    __('Depreciation'),
                    __('Condition'),
                    __('# of loans'));
        }
        return $header;
    }

    function printRow($ageRangeOnce, $type, $purchaseCost, $currentCost, $depreciation, $condition, $loans){
        $depreciation =  !empty($depreciation)? number_format($depreciation) : '';
        printf('<tr>
            <td class="tablecell" >%s</td>
            <td class="tablecell" >%s</td>
            <td class="tablecell" >%s</td>
            <td class="tablecell" >%s</td>
            <td class="tablecell" >%s</td>
            <td class="tablecell" >%s</td>
            <td style="text-align: center;" class="tablecell">%d</td>
        </tr>',
        $ageRangeOnce,
        $type,
        $purchaseCost,
        $currentCost,
        $depreciation,
        $condition, 
        $loans);
    }

    function printRowWithLink($id,$name, $type, $purchaseCost, $currentCost, $depreciation, $condition, $loans){
        $depreciation =  number_format($depreciation,2);
        printf('<tr>
            <td class="tablecell"><a href="/CIL_equipment/view_equipment.php?eid=%d" target="_blank">%s</a></td>
            <td class="tablecell">%s</td>
            <td class="tablecell">%s</td>
            <td class="tablecell">%s</td>
            <td class="tablecell">%s</td>
            <td class="tablecell">%s</td>
            <td style="text-align: center;" class="tablecell">%d</td>
        </tr>',
        $id,
        $name,
        $type,
        $purchaseCost,
        $currentCost,
        $depreciation,
        $condition, 
        $loans);      
    }

    function getSumdata($locationArray, $equip, $post, $location){
        $dataSummary = array();
        $totalLoans = 0;
        $consumerSummary = array();
        $equipmentList = array();
        foreach ($locationArray as $locationArrayKey => $locationArrayValue) {
            $equipTypeName = array();
            foreach ($equip as $equipArrayyKey => $equipArrayyValue) {
                $count = GetEquipmentCount($equipArrayyValue, $locationArrayValue, $location, $post);
                if(!empty($count['summary'])){
                    foreach ($count['summary'] as $key => $value) {
                        if(!isset($consumerSummary[$value['Equipment_id']])){
                            $consumerSummary[$value['Equipment_id']] = array();
                        }
                        array_push($consumerSummary[$value['Equipment_id']],$value);
                    }
                }
                if($count){
                    if(!isset($_POST["age_display"])){
                        foreach ($count as $countKey => $countValue) {
                            if($countKey !== 'summary'){
                                if(!empty($countValue)){

                                    $name = GetEquipName($countValue['Equipment_id']);

                                    if(isset($dataSummary[$name[0]['name']])){

                                        if(in_array($dataSummary[$name[0]['name']]['type'],$equipTypeName)){
                                            $dataSummary[$name[0]['name']]['type'] .= $countValue['type'].'<br>';

                                        }
                                        if(in_array($dataSummary[$name[0]['name']]['purchaseCost'],$equipTypeName)){
                                            $dataSummary[$name[0]['name']]['purchaseCost'] .= $countValue['purchaseCost'].'<br>';

                                        }
                                        if(in_array($dataSummary[$name[0]['name']]['currentCost'],$equipTypeName)){
                                            $dataSummary[$name[0]['name']]['currentCost'] .= $countValue['currentCost'].'<br>';

                                        }
                                        if(in_array($dataSummary[$name[0]['name']]['condition'],$equipTypeName)){
                                            $dataSummary[$name[0]['name']]['condition'] .= $countValue['condition'].'<br>';

                                        }

                                        array_push($dataSummary[$name[0]['name']]['loanDate'],$countValue['loan_date']);
                                        $dataSummary[$name[0]['name']]['loans'] += $countValue['loans'];
                                        $dataSummary[$name[0]['name']]['count'] += 1;

                                    } else {
                                        $dataSummary[$name[0]['name']]['loans'] = $countValue['loans'];
                                        $dataSummary[$name[0]['name']]['equipmentId'] = $countValue['Equipment_id'];
                                        $dataSummary[$name[0]['name']]['type'] = $countValue['type'].'<br>';
                                        $dataSummary[$name[0]['name']]['purchaseCost'] = $countValue['purchaseCost'].'<br>';
                                        $dataSummary[$name[0]['name']]['currentCost'] = $countValue['currentCost'].'<br>';
                                        $dataSummary[$name[0]['name']]['condition'] = $countValue['condition'].'<br>';
                                        $dataSummary[$name[0]['name']]['loanDate'] = array($countValue['loan_date']);
                                        $dataSummary[$name[0]['name']]['count'] = 0;
                                        
                                    }
                                }
                            }
                        }  

                    } else {
                        
                        foreach($count as $equipType => $ageRangeDatas) {

                            if($equipType !== 'summary' && $equipType != 'AgeRange'){
                                $ageData = array_filter($ageRangeDatas);
                                if(!empty($ageData)){
                                    foreach ($ageRangeDatas as $ageRange => $data) {
                                        $ageRangeDate = explode("_",$ageRange);
                                        $ageRangeOnce = $ageRangeDate[0].' - '.$ageRangeDate[1];
                                        $ageLoan = 0;
                                        if(!empty($data)){
                                            foreach ($data as $loans) {
                                                $name = GetEquipName($loans['Equipment_id']);
                                                $equipmentList[$name[0]['name']] = $loans['Equipment_id'];
                                                $totalLoans += $loans["loans"];     
                                                
                                                if(isset($dataSummary[$name[0]['name']][$ageRangeOnce])){
                                                     if(in_array($dataSummary[$name[0]['name']][$ageRangeOnce]['type'],$equipTypeName)){
                                                         $dataSummary[$name[0]['name']][$ageRangeOnce]['type'] .= $loans["type"].'<br>';

                                                     }
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['loans'] += $loans["loans"];
                                                    array_push($equipTypeName,$dataSummary[$name[0]['name']][$ageRangeOnce]['type']);
                                                    array_push($dataSummary[$name[0]['name']][$ageRangeOnce]['loanDate'],$loans['loan_date']);
                                                } else {
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['loans'] = $loans["loans"];
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['type'] = $loans["type"].'<br>';
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['purchaseCost'] = $loans['purchaseCost'].'<br>';
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['currentCost'] = $loans['currentCost'].'<br>';
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['condition'] = $loans['condition'].'<br>';
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['loanDate'] = array($loans['loan_date']);
                                                }
                                                    $dataSummary[$name[0]['name']][$ageRangeOnce]['equipmentId'] = array($loans['Equipment_id']);
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

    function printSummary($location, $locationArray, $equip, $postData, $locationSearch){
        $total = 0;
        $locationHeader = getHeader(1,'',$location,'','');
        echo $locationHeader;
        $dataSummary = array();
        foreach ($equip as $equipArrayyKey => $equipArrayyValue) {
            $count2 = GetEquipmentCount($equipArrayyValue, $locationArray, $locationSearch, $postData);
            if(isset($_POST['age_display'])){
            }
            else{
                if(!empty($count2['summary'])){
                    $name = GetEquipName($count2['0']['Equipment_id']);
                    printRow( 
                            '<a href="/CIL_equipment/view_equipment.php?eid='.$count2['0']['Equipment_id'].'" target="_blank">'.$name[0]['name'].'</a>',
                            $count2[0]['type'],
                            $count2[0]['purchaseCost'],
                            $count2[0]['currentCost'],
                            $count2[0]['purchaseCost'] - $count2[0]['currentCost'],
                            $count2[0]['condition'],
                            $count2[0]['loans']
                    );  
                    $total += $count2[0]['loans'];
                    $fullname = '';
                    $consumerIdCheck = array();
                    foreach ($count2['summary'] as $key => $value) {
                        if(!in_array($value['consumerId'], $consumerIdCheck)){
                            $fullname .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$value['consumerId'].'&tab=equip_loans " target="_blank">'.$value['name'].'</a><br>';
                        }
                        array_push($consumerIdCheck, $value['consumerId']);
                    }
                    printRow($fullname,'','','','','','');
                }
            }
        }
        $equipmentData = getSumdata($locationArray,$equip,$postData,$locationSearch);
        

        if(isset($_POST['age_display'])){
            foreach ($equipmentData as $equipDataKey => $equipDataValue) {
                if($equipDataKey != 'summary' && $equipDataKey != 'equipmentList'){
                    echo '<tr><td class="tablecell" colspan="7"><b><a href="/CIL_equipment/view_equipment.php?eid='.$equipmentData['equipmentList'][$equipDataKey].'" target="_blank">'.$equipDataKey.'</a></b></td></tr>';  
                    unset($postData['age_display']['Rollup']);
                    foreach ($postData['age_display'] as $postAgeKey => $postAgeValue) {

                        if(isset($equipDataValue[$postAgeValue])){
                            $total += $equipDataValue[$postAgeValue]['loans'];
                            printRow( 
                                            $postAgeValue,
                                            $equipDataValue[$postAgeValue]['type'],
                                            $equipDataValue[$postAgeValue]['purchaseCost'],
                                            $equipDataValue[$postAgeValue]['currentCost'],
                                            $equipDataValue[$postAgeValue]['purchaseCost'] - $equipDataValue[$postAgeValue]['currentCost'],
                                            $equipDataValue[$postAgeValue]['condition'],
                                            $equipDataValue[$postAgeValue]['loans']
                                        ); 
                            $name = array();
                            if($_SESSION['sum_names'] == 'show'){
                                foreach ($equipmentData['summary'] as $equipDataSummary) {
                                    foreach ($equipDataSummary as $equipDataSumKey => $equipDataSumValue) {
                                        
                                        if($equipDataSumValue['Equipment_id'] == $equipmentData['equipmentList'][$equipDataKey]){
                                            if(in_array($equipDataSumValue['loan_date'], $equipDataValue[$postAgeValue]['loanDate'])){
                                                if(!in_array($equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'],$name)){
                                                    $name[$equipDataSumValue['consumerId']] = $equipDataSumValue['fname'].' '.$equipDataSumValue['lname'].' '.$equipDataSumValue['midname'];
                                                }           
                                            }
                                        }
                                    }
                                }
                                $fullname = '';
                                foreach ($name as $nameKey => $nameValue) {
                                    $fullname .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$nameKey.'&tab=equip_loans " target="_blank">'.$nameValue.'</a><br>';
                                }
                                printRow($fullname,'','','','','','');
                            }

                        }else{
                            printRow($postAgeValue,'','','','','','');
                        }
                    }   
                }
                $printEquipmentName = 0;
            }
        }

        return $total;
    }

    function printAgeRange($ageRangeArray,$post){
        unset($post['age_display']['Rollup']);
        $total = 0;
        foreach ($ageRangeArray as $AgeRangeKey => $AgeRangeValue) {
            if($AgeRangeKey != 'summary'){
                echo '<tr><td class="tablecell" colspan="7"><b><a href="/CIL_equipment/view_equipment.php?eid='.$AgeRangeValue['equipmentId'].'" target="_blank">'.$AgeRangeKey.'</a></b></td></tr>';  
                foreach ($post['age_display'] as $postAgeKey => $postAgeValue) {
                    if(isset($AgeRangeValue[$postAgeKey])){
                        $total += $AgeRangeValue[$postAgeKey]['loans'];
                            printRow( 
                                        $postAgeValue,
                                        $AgeRangeValue[$postAgeKey]['type'],
                                        $AgeRangeValue[$postAgeKey]['purchaseCost'],
                                        $AgeRangeValue[$postAgeKey]['currentCost'],
                                        $AgeRangeValue[$postAgeKey]['purchaseCost'] - $AgeRangeValue[$postAgeKey]['currentCost'],
                                        $AgeRangeValue[$postAgeKey]['condition'],
                                        $AgeRangeValue[$postAgeKey]['loans']
                                    ); 
                        $name = array();
                        if($_SESSION['sum_names'] == 'show'){
                            if(!empty($ageRangeArray['summary'])){
                                foreach ($ageRangeArray['summary'] as $equipDataSummary) {
                                    if($equipDataSummary['Equipment_id'] == $AgeRangeValue[$postAgeKey]['Equipment_id']){;
                                        $loanDates = getLoanDates($equipDataSummary['Equipment_id'],$post);
                                        if(is_array($loanDates[$equipDataSummary['Equipment_id']])){
                                                if(!in_array($equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'],$name)){
                                                    $name[$equipDataSummary['consumerId']] = $equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'];
                                                }
                                        }else{
                                            if($equipDataSummary['loan_date'] == $AgeRangeValue[$postAgeKey]['loan_date']){
                                                if(!in_array($equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'],$name)){
                                                    $name[$equipDataSummary['consumerId']] = $equipDataSummary['fname'].' '.$equipDataSummary['lname'].' '.$equipDataSummary['midname'];
                                                }   
                                            }
                                        }
                                    }
                                }

                                $fullname = '';
                                foreach ($name as $nameKey => $nameValue) {
                                    $fullname .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="../CIL_consumers/view_consumer.php?cid='.$nameKey.'&tab=equip_loans " target="_blank">'.$nameValue.'</a><br>';
                                }
                                printRow($fullname,'','','','','','');
                            }
                        }
                    }else{
                        printRow($postAgeValue,'','','','','','');
                    }
                }
                
            }
        }
        return $total;
    }

    function CountSummary($loansArray){
        $loans = 0;
        foreach ($loansArray as $loansArrayKey => $loansArrayValue) {
            foreach ($loansArrayValue as $loanKey => $loanValue) {
                if(isset($loanValue['loans'])){
                    $loans += $loanValue['loans'];
                }
            }
        }
        return $loans;
    }
?>