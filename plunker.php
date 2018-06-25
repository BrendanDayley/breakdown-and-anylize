<?php

if(count($countyarray)>0) { ?>
    <tr>
        <td>
            <?php
            if($include_rollup==2 || $include_rollup==3) {
                $county_cnt = count($countyarray);
                $countyNum = 0;
                $totalReqCounty = 0;
                $totalDistinctCounty = 0;
                $totalReceivedCounty = 0;
                printf("\n");
                foreach($countyarray as $posted_county){
                    $countyNum++;
                    $count = GetServiceCount_County($servicearray, $posted_county, $_POST);
                    if($_SESSION["sum_names"] == 'show') {
                        $serv_info = GetServiceInfo_report_County($servicearray, $posted_county,$_POST);
                    }
                    $totalReq = 0;
                    $totalDistinct = 0;
                    $totalReceived = 0;
                    if($posted_county) {
                        $county = GetCounty($posted_county);
                    }else {
                        $county[0] = array('name' => 'Unknown', 'id' => $posted_county);
                    }
                    $chartLabels = array();
                    $chartValues = array();
                    $chartValues[] = array();
                    $chartValues[] = array();
                    $chartValues[] = array();
                    $bars = array();
                    $bars[] = __("Request Total");
                    $bars[] = __("Distinct Received");
                    $bars[] = __("Total Received");
                    printf("<div class='border' style='margin-bottom:10px'>\n\t<table width='100%%' class='admintable' rules='rows' cellpadding='0' cellspacing='0'>\n");
                    printf("\t\t<tr><td class='tablehead' style='width: 75%%; text-align: left;'>%s</td><td class='tablehead'>%s</td><td class='tablehead'>%s</td><td class='tablehead'>%s</td></tr>\n",(($county[0]['name'] == 'Unknown') ? 'County Unknown' : $county[0]['name']),__("Request Total"),__("Distinct Received"),__("Total Received"));

                    foreach($servicearray as $posted_service) {
                        $serv = GetServiceType($posted_service);
                        $name = $serv[0]["type"];
                        if($serv[0]["704_type"]) {
                            $name .= ' ('.GetServiceTypeName($serv[0]["704_type"]).')';
                        }
                        $chartLabels[] = $name;

                        if(isset($_POST["age_range"]) && CheckArray($_POST["age_range"])) {
                            printf('<tr>
                                        <td class="tablecell" colspan="4">
                                            <b>%s</b>
                                        </td>
                                    </tr>',$name);

                            $ageReqCnt = 0;
                            $ageDistinctCnt = 0;
                            $ageMultiCnt = 0;
                            foreach($_POST["age_display"] as $key => $value) {
                                $reqCnt = isset($count["request"][$posted_service][$key]) ? $count["request"][$posted_service][$key] : 0;
                                $distinctCnt = isset($count["distinctReceived"][$posted_service][$key]) ? $count["distinctReceived"][$posted_service][$key] : 0;
                                $multiCnt = isset($count["multiReceived"][$posted_service][$key]["cnt"]) ? (($count["multiReceived"][$posted_service][$key]["cnt"]-$count["multiReceived"][$posted_service][$key]["distCnt"])+$distinctCnt) : 0;

                                if($key!='Rollup') {
                                    printf('<tr>
                                                <td class="tablecell">
                                                    %s
                                                </td>
                                                <td class="tablecell">
                                                    %d
                                                </td>
                                                <td class="tablecell">
                                                    %d
                                                </td>
                                                <td class="tablecell">
                                                    %d
                                                </td>
                                            </tr>',($_SESSION["sum_names"] == 'show') ? "<b>".$value."</b>" : $value,$reqCnt,$distinctCnt,$multiCnt);

                                    if(($reqCnt || $distinctCnt || $multiCnt) && $_SESSION["sum_names"] == 'show') {
                                        $shown = array();
                                        printf("\t\t<tr><td colspan='4' class='tablecell'>\n\t\t\t<table width='100%%'>\n\t\t\t\t<tr><td width='5%%'></td><td width='90%%'></td></tr>\n");
                                        $serv_info[$posted_service][$key] = SortArrayByField($serv_info[$posted_service][$key],'consumer_name','ASC',false);
                                        foreach($serv_info[$posted_service][$key] as $serv_datum) {
                                            if(!array_key_exists($serv_datum["Consumers_id"],$shown)) {
                                                printf("\t\t\t\t<tr><td></td><td><a href='/CIL_consumers/view_consumer.php?cid=%d&tab=services' target='_blank'>%s</a></td></tr>\n",$serv_datum["Consumers_id"], $serv_datum["consumer_name"]);
                                                $shown[$serv_datum["Consumers_id"]] = 1;
                                            }
                                        }
                                        printf("\t\t\t</table>\n\t\t</td></tr>\n");
                                    }
                                    $ageReqCnt += $reqCnt;
                                    $ageDistinctCnt += $distinctCnt;
                                    $ageMultiCnt += $multiCnt;
                                    $chartValues[0][] = $multiCnt;
                                    $chartValues[1][] = $distinctCnt;
                                    $chartValues[2][] = $reqCnt;
                                }else {
                                    //I can't use the rollup numbers because they include results that do not occur in any range asked for.
                                    printf('<tr>
                                                <td class="tablehead">
                                                    <b>%s %s</b>
                                                </td>
                                                <td class="tablehead">%d</td>
                                                <td class="tablehead">%d</td>
                                                <td class="tablehead">%d</td>
                                            </tr>',$name, __("Totals"),$ageReqCnt,$ageDistinctCnt,$ageMultiCnt);
                                    $totalReq += $ageReqCnt;
                                    $totalDistinct += $ageDistinctCnt;
                                    $totalReceived += $ageMultiCnt;
                                    $chartValues[0][] = $ageMultiCnt;
                                    $chartValues[1][] = $ageDistinctCnt;
                                    $chartValues[2][] = $ageReqCnt;
                                }
                            }
                        }else {
                            $reqCnt = (isset($count["request"][$posted_service]["cnt"])) ? $count["request"][$posted_service]["cnt"] : 0;
                            $distinctCnt = (isset($count["distinctReceived"][$posted_service]["cnt"])) ? $count["distinctReceived"][$posted_service]["cnt"] : 0;
                            $multiCnt = 0;
                            if(isset($count["multiReceived"][$posted_service]["cnt"]) || $distinctCnt) {
                                if(isset($count["multiReceived"][$posted_service]["cnt"])) {
                                    $multiCnt = (($count["multiReceived"][$posted_service]["cnt"]-$count["multiReceived"][$posted_service]["distCnt"])+$distinctCnt);
                                }elseif($distinctCnt) {
                                    $multiCnt = $distinctCnt;
                                }
                            }

                            if (DidUserSelectChart()) {
                                $chartValues[0][] = $multiCnt;
                                $chartValues[1][] = $distinctCnt;
                                $chartValues[2][] = $reqCnt;
                            } else {
                                printf("\t\t<tr><td class='tablecell'>%s</td><td class='tablecell'>%d</td><td class='tablecell'>%d</td><td class='tablecell'>%d</td></tr>\n",($_SESSION["sum_names"] == 'show') ? "<b>".$name."</b>" : $name,$reqCnt,$distinctCnt,$multiCnt);
                            }
                            if(($reqCnt || $distinctCnt || $multiCnt) && $_SESSION["sum_names"] == 'show' && !DidUserSelectChart()) {
                                $shown = array();
                                printf("\t\t<tr><td colspan='4' class='tablecell'>\n\t\t\t<table width='100%%'>\n\t\t\t\t<tr><td width='5%%'></td><td width='90%%'></td></tr>\n");

                                if(isset($serv_info[$posted_service]) && CheckArray($serv_info[$posted_service])){
                                    foreach($serv_info[$posted_service] as $serv_datum) {
                                        if(!array_key_exists($serv_datum["Consumers_id"],$shown)) {
                                            printf("\t\t\t\t<tr><td></td><td><a href='/CIL_consumers/view_consumer.php?cid=%d&tab=services' target='_blank'>%s</a></td></tr>\n",$serv_datum["Consumers_id"], $serv_datum["consumer_name"]);
                                            $shown[$serv_datum["Consumers_id"]] = 1;
                                        }
                                    }
                                }
                                printf("\t\t\t</table>\n\t\t</td></tr>\n");
                            }
                            $totalReq += $reqCnt;
                            $totalDistinct += $distinctCnt;
                            $totalReceived += $multiCnt;
                        }
                    }
                    if (DidUserSelectChart()) {
                        GenerateHorizontalBarChart($countyNum, $chartLabels, $chartValues, $bars);
                    }
                    printf("\t\t<tr><td class='tablehead' style='text-align: left;'>%s</td><td class='tablehead' style='text-align: left;'>%s</td><td class='tablehead' style='text-align: left;'>%s</td><td class='tablehead' style='text-align: left;'>%s</td></tr>\n",__("Totals"),$totalReq,$totalDistinct,$totalReceived);
                    printf("\t</table>\n</div>\n");
                    $totalReqCounty += $totalReq;
                    $totalDistinctCounty += $totalDistinct;
                    $totalReceivedCounty += $totalReceived;
                }
            ?>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="75%%">
                            <?php echo(__("Total of all Counties"));?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalReqCounty);?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalDistinctCounty);?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalReceivedCounty);?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <?php } if($include_rollup==1 || $include_rollup==3) { ?><br/>
    <tr>
        <td>
            <div class="border">
                <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tablehead" width="75%%">
                            <?php echo(__("Totals by Service Type of all Counties"));?>
                        </td>
                        <td class="tablehead">
                            <?php echo(__("Request Total")); ?>
                        </td>
                        <td class="tablehead">
                            <?php echo(__("Distinct Received")); ?>
                        </td>
                        <td class="tablehead">
                            <?php echo(__("Total Received")); ?>
                        </td>
                    </tr>
                        <?php
                            $totalReqCounty = 0;
                            $totalDistinctCounty = 0;
                            $totalReceivedCounty = 0;
                            $totalReq = 0;
                            $totalDistinct = 0;
                            $totalReceived = 0;
                            $chartLabels = array();
                            $chartValues = array();
                            $chartValues[] = array();
                            $chartValues[] = array();
                            $chartValues[] = array();
                            $bars = array();
                            $bars[] = __("Request Total");
                            $bars[] = __("Distinct Received");
                            $bars[] = __("Total Received");
                            
                            $count = GetServiceCount_County($servicearray, $countyarray, $_POST);
                            if($_SESSION["sum_names"] == 'show') {
                                $serv_info = GetServiceInfo_report_County($servicearray, $countyarray,$_POST);
                            }
                            foreach($servicearray as $posted_service){
                                $serv = GetServiceType($posted_service);
                                $name = $serv[0]["type"];
                                if($serv[0]["704_type"]) {
                                    $name .= ' ('.GetServiceTypeName($serv[0]["704_type"]).')';
                                }
                                $chartLabels[] = $name;

                                if(isset($_POST["age_range"]) && CheckArray($_POST["age_range"])) {
                                    if (!DidUserSelectChart()) {
                                        printf('<tr>
                                                    <td class="tablecell" colspan="4">
                                                        <b>%s</b>
                                                    </td>
                                                </tr>',$name);
                                    }
                                    $ageReqCnt = 0;
                                    $ageDistinctCnt = 0;
                                    $ageMultiCnt = 0;
                                    foreach($_POST["age_display"] as $key => $value) {
                                        $reqCnt = isset($count["request"][$posted_service][$key]) ? $count["request"][$posted_service][$key] : 0;
                                        $distinctCnt = isset($count["distinctReceived"][$posted_service][$key]) ? $count["distinctReceived"][$posted_service][$key] : 0;
                                        $multiCnt = isset($count["multiReceived"][$posted_service][$key]["cnt"]) ? (($count["multiReceived"][$posted_service][$key]["cnt"]-$count["multiReceived"][$posted_service][$key]["distCnt"])+$distinctCnt) : 0; 

                                        if($key!='Rollup') {
                                            if (!DidUserSelectChart()) {
                                                printf('<tr>
                                                            <td class="tablecell">
                                                                %s
                                                            </td>
                                                            <td class="tablecell">
                                                                %d
                                                            </td>
                                                            <td class="tablecell">
                                                                %d
                                                            </td>
                                                            <td class="tablecell">
                                                                %d
                                                            </td>
                                                        </tr>',($_SESSION["sum_names"] == 'show') ? "<b>".$value."</b>" : $value,$reqCnt,$distinctCnt,$multiCnt);
                                            }
                                            if(($reqCnt || $distinctCnt || $multiCnt) && $_SESSION["sum_names"] == 'show' && !DidUserSelectChart()) {
                                                $shown = array();
                                                printf("\t\t<tr><td colspan='4' class='tablecell'>\n\t\t\t<table width='100%%'>\n\t\t\t\t<tr><td width='5%%'></td><td width='90%%'></td></tr>\n");
                                                foreach($serv_info[$posted_service][$key] as $serv_datum) {
                                                    if(!array_key_exists($serv_datum["Consumers_id"],$shown)) {
                                                        printf("\t\t\t\t<tr><td></td><td><a href='/CIL_consumers/view_consumer.php?cid=%d&tab=services' target='_blank'>%s</a></td></tr>\n",$serv_datum["Consumers_id"], $serv_datum["consumer_name"]);
                                                        $shown[$serv_datum["Consumers_id"]] = 1;
                                                    }
                                                }
                                                printf("\t\t\t</table>\n\t\t</td></tr>\n");
                                            }
                                            $ageReqCnt += $reqCnt;
                                            $ageDistinctCnt += $distinctCnt;
                                            $ageMultiCnt += $multiCnt;
                                            $chartValues[0][] = $multiCnt;
                                            $chartValues[1][] = $distinctCnt;
                                            $chartValues[2][] = $reqCnt;
                                        }else {
                                            //I can't use the rollup numbers because they include results that do not occur in any range asked for.
                                            if (!DidUserSelectChart()) {
                                                printf('<tr>
                                                            <td class="tablehead">
                                                                <b>%s %s</b>
                                                            </td>
                                                            <td class="tablehead">%d</td>
                                                            <td class="tablehead">%d</td>
                                                            <td class="tablehead">%d</td>
                                                        </tr>',$name, __("Totals"),$ageReqCnt,$ageDistinctCnt,$ageMultiCnt);
                                            }
                                            $totalReq += $ageReqCnt;
                                            $totalDistinct += $ageDistinctCnt;
                                            $totalReceived += $ageMultiCnt;
                                            $chartValues[0][] = $ageMultiCnt;
                                            $chartValues[1][] = $ageDistinctCnt;
                                            $chartValues[2][] = $ageReqCnt;
                                        }
                                    }
                                }else {
                                    $reqCnt = isset($count["request"][$posted_service]["cnt"]) ? $count["request"][$posted_service]["cnt"] : 0;
                                    $distinctCnt = isset($count["distinctReceived"][$posted_service]["cnt"]) ? $count["distinctReceived"][$posted_service]["cnt"] : 0;
                                    $multiCnt = isset($count["multiReceived"][$posted_service]["cnt"]) ? (($count["multiReceived"][$posted_service]["cnt"]-$count["multiReceived"][$posted_service]["distCnt"])+$distinctCnt) : 0;

                                    if (!DidUserSelectChart()) {
                                        printf("\t\t<tr><td class='tablecell'>%s</td><td class='tablecell'>%d</td><td class='tablecell'>%d</td><td class='tablecell'>%d</td></tr>\n",($_SESSION["sum_names"] == 'show') ? "<b>".$name."</b>" : $name,$reqCnt,$distinctCnt,$multiCnt);
                                    }
                                    if(($reqCnt || $distinctCnt || $multiCnt) && $_SESSION["sum_names"] == 'show' && !DidUserSelectChart()) {
                                        $shown = array();
                                        printf("\t\t<tr><td colspan='4' class='tablecell'>\n\t\t\t<table width='100%%'>\n\t\t\t\t<tr><td width='5%%'></td><td width='90%%'></td></tr>\n");
                                        foreach($serv_info[$posted_service] as $serv_datum) {
                                            if(!array_key_exists($serv_datum["Consumers_id"],$shown)) {
                                                printf("\t\t\t\t<tr><td></td><td><a href='/CIL_consumers/view_consumer.php?cid=%d&tab=services' target='_blank'>%s</a></td></tr>\n",$serv_datum["Consumers_id"], $serv_datum["consumer_name"]);
                                                $shown[$serv_datum["Consumers_id"]] = 1;
                                            }
                                        }
                                        printf("\t\t\t</table>\n\t\t</td></tr>\n");
                                    }
                                    $totalReq += $reqCnt;
                                    $totalDistinct += $distinctCnt;
                                    $totalReceived += $multiCnt;
                                    $chartValues[0][] = $multiCnt;
                                    $chartValues[1][] = $distinctCnt;
                                    $chartValues[2][] = $reqCnt;
                                }
                            }
                            $totalReqCounty += $totalReq;
                            $totalDistinctCounty += $totalDistinct;
                            $totalReceivedCounty += $totalReceived;

                            if (DidUserSelectChart()) {
                                GenerateHorizontalBarChart(9999, $chartLabels, $chartValues, $bars);
                            }
                        ?>
                    <tr>
                        <td class="tablehead" width="75%%">
                            <?php echo(__("Total of all Services"));?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalReqCounty);?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalDistinctCounty);?>
                        </td>
                        <td class="tablehead">
                            <?php echo($totalReceivedCounty);?>
                        </td>
                    </tr>
                </table>
            </div>
            <br />
            <br />
        </td>
    </tr><?php }//end rollup ?>
<?php } //end counties ?>

?>