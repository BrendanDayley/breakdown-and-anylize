<?php
    function GetConsumerEmails($filters) {
        $ar = array();
        $cols = array();
        $havings = array();
        $joins = array();
        $wheres = array();
        $startyear = date("Y-m-d",strtotime($filters["startdate"]));
        $endyear = date("Y-m-d",strtotime($filters["enddate"]));

        $joins[] = "CIL_Consumers as c ";
        $joins[] = "LEFT OUTER JOIN api_users as u on (c.api_users_id = u.id) ";
        $cols[] = "c.id";
        $cols[] = "CONCAT_WS(' ',c.fname, c.midname, c.lname) AS name";
        $wheres[] = "c.deleted=0";
        $wheres[] = "c.exclude_from_maillists=0";
        $wheres[] = "c.email!=''";

        $conType = getConType($filters["printNonCons"], $filters["printIr"], $filters["printCons"]);

        if(isset($filters["status"]) && $filters["status"] != "" && $filters["status"] != "all") {
            $cols[] = "(SELECT cs.StatusTypes_id "
                        ."FROM CIL_ConsumerStatus cs "
                        ."WHERE cs.date=(SELECT MAX(cs2.date) FROM CIL_ConsumerStatus cs2 WHERE cs.Consumers_id=cs2.Consumers_id AND cs2.date < :start AND cs2.deleted=0) "
                        ."AND cs.Consumers_id=c.id "
                        ."AND cs.deleted=0 "
                        ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS startStatus ";
            if($filters["labelscope"] == 1) {
                //If endStatus is blank the the 'current' status is the same as the startStatus
                $cols[] = "(SELECT cs.StatusTypes_id  "
                            ."FROM CIL_ConsumerStatus cs "
                            ."WHERE cs.date=(SELECT MAX(cs2.date) FROM CIL_ConsumerStatus cs2 WHERE cs.Consumers_id=cs2.Consumers_id AND cs2.date >= :start AND cs2.date <= :end AND cs2.deleted=0) "
                            ."AND cs.Consumers_id=c.id "
                            ."AND cs.deleted=0 "
                            ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS endStatus ";
                if($filters["status"]=='allClosed') {
                    $havings[] = "IF(endStatus IS NULL,startStatus!=1,endStatus!=1)";
                    $havings[] = "IF(endStatus IS NULL,startStatus!=4,endStatus!=4)";
                }else {
                    $havings[] = "IF(endStatus IS NULL,startStatus=:status,endStatus=:status)";
                    $ar['status'] = $filters["status"];
                }
            }elseif($filters["labelscope"] == 2) {
                //If endStatus is blank the the 'current' status is the same as the startStatus
                if($filters["status"]=='allClosed') {
                    $cols[] = "(SELECT cs.StatusTypes_id  "
                                ."FROM CIL_ConsumerStatus cs "
                                ."WHERE cs.date >= :start "
                                ."AND cs.date <= :end "
                                ."AND cs.Consumers_id=c.id "
                                ."AND cs.deleted=0 "
                                ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS endStatus ";
                    $havings[] = "endStatus IS NOT NULL";
                    $havings[] = "endStatus!=1";
                    $havings[] = "endStatus!=4";
                }else {
                    $cols[] = "(SELECT cs.StatusTypes_id  "
                                ."FROM CIL_ConsumerStatus cs "
                                ."WHERE cs.date >= :start "
                                ."AND cs.date <= :end "
                                ."AND cs.Consumers_id=c.id "
                                ."AND cs.StatusTypes_id=:status "
                                ."AND cs.deleted=0 "
                                ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS endStatus ";
                    $ar['status'] = $filters["status"];
                    $havings[] = "endStatus=:status";
                }
            }elseif($filters["labelscope"] == 3) {
                //If endStatus is blank the the 'current' status is the same as the startStatus
                $cols[] = "(SELECT cs.StatusTypes_id  "
                            ."FROM CIL_ConsumerStatus cs "
                            ."WHERE cs.date >= :start "
                            ."AND cs.date <= :end "
                            ."AND cs.Consumers_id=c.id "
                            ."AND cs.StatusTypes_id=:status "
                            ."AND cs.deleted=0 "
                            ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS endStatus ";
                $ar['status'] = $filters["status"];
                if($filters["status"]=='allClosed') {
                    $havings[] = "(startStatus!=1 AND startStatus!=4) OR (endStatus!=1 AND endStatus!=4)";
                }else {
                    $havings[] = "startStatus=:status OR endStatus=:status";
                }
            }

            $ar['start'] = $startyear;
            $ar['end'] = $endyear;
        }

        if(!$filters["countyall"] && isset($filters["county"]) && CheckArray($filters["county"])) {
            $string = '';
            foreach($filters["county"] as $value) {
                $string .= mysql_escape_string($value).',';
            }
            $string = substr($string,0,strlen($string)-1);
            $wheres[] = "c.mailingCounty_id IN (".$string.") ";
        }


        if(!$filters["disabilitiesall"] && isset($filters["disabilities"]) && CheckArray($filters["disabilities"])) {
            if(count($filters["disabilities"])==1) {
                $string = 'Disabiliies_id=:disability';
                $ar['disability'] = $filters["disabilities"][0];
            }else {
                $string = 'Disabilities_id IN (';
                foreach($filters["disabilities"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_ConsumerDisabilities WHERE ".$string." AND deleted=0) ";
        }

        if(!$filters["tableGoalsall"] && isset($filters["goals"]) && CheckArray($filters["goals"])) {
            if(count($filters["goals"])==1) {
                $string = 'GoalTypes_id=:goal';
                $ar['goal'] = $filters["goals"][0];
            }else {
                $string = 'GoalTypes_id IN (';
                foreach($filters["goals"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_Goals WHERE ".$string." AND deleted=0  AND ( set_date >= :start1 AND set_date <= :end1 ) ) ";
            $ar["start1"] = $startyear;
            $ar["end1"] = $endyear;
        }

        if(!$filters["typesall"] && isset($filters["services"]) && CheckArray($filters["services"])) {
            if(count($filters["services"])==1) {
                $string = 'ServiceTypes_id=:service';
                $ar['service'] = $filters["services"][0];
            }else {
                $string = 'ServiceTypes_id IN (';
                foreach($filters["services"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_IRLog WHERE ".$string." AND deleted=0 AND ( DATE(datetime) >= :start2 AND DATE(datetime) <= :end2 ) )";
            $ar["start2"] = $startyear;
            $ar["end2"] = $endyear;
        }

        if(!$filters["groupsall"] && isset($filters["programs"]) && CheckArray($filters["programs"])) {
            if(count($filters["programs"])==1) {
                $string = 'ConsumerGroups_id=:program';
                $ar['program'] = $filters["programs"][0];
            }else {
                $string = 'ConsumerGroups_id IN (';
                foreach($filters["programs"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_ConsumerGroupLog WHERE ".$string." AND deleted=0 AND ( enterdate <= :end3 AND ( exitdate = '0000-00-00' OR exitdate >= :start3)) ) ";
            $ar["start3"] = $startyear;
            $ar["end3"] = $endyear;
        }

        if(!$filters["usersall"] && isset($filters["staff"]) && CheckArray($filters["staff"])) {
            if(count($filters["staff"])==1) {
                $string = 'api_users_id=:staff';
                $ar['staff'] = $filters["staff"][0];
            }else {
                $string = 'api_users_id IN (';
                foreach($filters["staff"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }

            if($filters["primary"]==1) {
                $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_ConsumerStaff WHERE ".$string." AND primary_staff=1 GROUP BY Consumers_id)";
            }else {
                $wheres[] = "c.id IN (SELECT Consumers_id FROM CIL_ConsumerStaff WHERE ".$string." GROUP BY Consumers_id)";
            }
        }

        if(!$filters["taxingall"] && isset($filters["taxing"]) && CheckArray($filters["taxing"])) {
            if(count($filters["taxing"])==1) {
                $string = 'c.TaxingAuthority_id=:taxing';
                $ar['taxing'] = $filters["taxing"][0];
            }else {
                $string = 'c.TaxingAuthority_id IN (';
                foreach($filters["taxing"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = $string;
        }

        if(!$filters["methodall"] && isset($filters["contactMethod"]) && CheckArray($filters["contactMethod"])) {
            if(count($filters["contactMethod"])==1) {
                $string = 'c.ContactMethods_id=:contactMethod';
                $ar['contactMethod'] = $filters["contactMethod"][0];
            }else {
                $string = 'c.ContactMethods_id IN (';
                foreach($filters["contactMethod"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = $string;
        }

        $orderBy = 'zips.zipCode, c.lname ASC, c.fname ASC';

        //$joins[] = "LEFT OUTER JOIN CIL_ConsumerCounties cc ON (cc.Consumers_id=c.id AND cc.curr_county=1 AND cc.deleted=0)";
        $joins[] = "LEFT OUTER JOIN Framework_2_0_Shared.counties sc ON (c.mailingCounty_id=sc.id)";
        $joins[] = "LEFT OUTER JOIN Framework_2_0_Shared.provinces p ON (sc.province_id=p.id)";
        $joins[] = "INNER JOIN CIL_Cities AS city ON (c.mailingCities_id=city.id)";
        $joins[] = "INNER JOIN CIL_ZipCodes AS zips ON (c.mailingZipCodes_id=zips.id)";
        $cols[] = "c.phone1";
        $cols[] = "c.phone2";
        $cols[] = "c.email";

        if(!$filters["cityall"] && isset($filters["city"]) && CheckArray($filters["city"])) {
            if(count($filters["city"])==1) {
                $string = 'c.mailingCities_id=:city';
                $ar['city'] = $filters["city"][0];
            }else {
                $string = 'c.mailingCities_id IN (';
                foreach($filters["city"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = $string;
        }

        if(!$filters["zipall"] && isset($filters["zipcodes"] ) && CheckArray($filters["zipcodes"])) {
            if(count($filters["zipcodes"])==1) {
                $string = 'c.mailingZipCodes_id=:zip';
                $ar['zip'] = $filters["zipcodes"][0];
            }else {
                $string = 'c.mailingZipCodes_id IN (';
                foreach($filters["zipcodes"] as $value) {
                    $string .= mysql_escape_string($value).',';
                }
                $string = substr($string,0,strlen($string)-1).') ';
            }
            $wheres[] = $string;
        }

        $sql = sprintf("SELECT %s FROM %s WHERE %s GROUP BY c.id %s ORDER BY %s",
                implode(", ", $cols),
                implode(" ", $joins),
                implode(" AND ", $wheres),
                (count($havings)) ? sprintf("HAVING %s", implode(" AND ", $havings)) : "",
                $orderBy);
        $consumers = api_DoSQL($sql, $ar);
                                            
        //var_dump(MergeSql($sql, $ar, true));
        var_dump($consumers[0]);
        exit;

        return $consumers;
    }

?>