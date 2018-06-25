<?php
if(isset($filters["status"]) && $filters["status"] != "" && $filters["status"] != "all") {
            $cols[] = "(SELECT cs.StatusTypes_id "
                        ."FROM CIL_ConsumerStatus cs "
                        ."WHERE cs.date=(SELECT MAX(cs2.date) FROM CIL_ConsumerStatus cs2 WHERE cs.Consumers_id=cs2.Consumers_id AND cs2.date < :start AND cs2.deleted=0) "
                        ."AND cs.Consumers_id=c.id "
                        ."AND cs.$conType "
                        ."AND cs.deleted=0 "
                        ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS startStatus ";
            if($filters["labelscope"] == 1) {
                //If endStatus is blank the the 'current' status is the same as the startStatus
                $cols[] = "(SELECT cs.StatusTypes_id "
                            ."FROM CIL_ConsumerStatus cs "
                            ."WHERE cs.date=(SELECT MAX(cs2.date) FROM CIL_ConsumerStatus cs2 WHERE cs.Consumers_id=cs2.Consumers_id AND cs2.date >= :start AND cs2.date <= :end AND cs2.deleted=0) "
                            ."AND cs.Consumers_id=c.id "
                            ."AND cs.$conType "
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
                                ."AND cs.$conType "
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
                                ."AND cs.$conType "
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
                            ."AND cs.$conType "
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


                    $cols[] = "(SELECT cs.StatusTypes_id "
                        ."FROM CIL_ConsumerStatus cs "
                        ."AND cs.Consumers_id=c.id "
                        ."AND cs.$conType "
                        ."AND cs.deleted=0 "
                        ."ORDER BY cs.date, cs.id DESC LIMIT 1) AS status";
    ?>