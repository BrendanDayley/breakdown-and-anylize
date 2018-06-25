<?php
function GetAllEquipment($start, $count, $filters, &$total, $field='', $value='') {
        $ar = array();
        $limit = ($count != "" && $count != "all") ? sprintf(" LIMIT %s, %s", mysql_escape_string($start), mysql_escape_string($count)) : "";

        $ar['deleted'] = $filters['showDeleted'];

        $where = "";
        if($filters["type"] != '' && $filters["type"] != 'all' && is_int($filters["type"]+0)) {
            $where .= " AND e.EquipmentTypes_id=:show";
            $ar["show"] = $filters["type"];
        }

        if($filters["status"] != '' && $filters["status"] != 'all' && is_int($filters["status"]+0)) {
            $where .= " AND e.EquipmentStatus_id=:show2";
            $ar["show2"] = $filters["status"];
        }

        if($filters["excludeGiven"] != '' && $filters["excludeGiven"] != 'all' && $filters["excludeGiven"] && (isset($filters["status"]) && $filters["status"]!=4)) {
            $where .= " AND e.EquipmentStatus_id!=4";
        }

        if($filters["cond"] != '' && $filters["cond"] != 'all' && is_int($filters["cond"]+0)) {
            $where .= " AND e.EquipmentCondition_id=:show3";
            $ar["show3"] = $filters["cond"];
        }

        if($filters["source"] != '' && $filters["source"] != 'all') {
            $sourceJoin = 'INNER JOIN CIL_EquipmentSource s ON (e.EquipmentSource_id=s.id) ';
            $where .= " AND e.EquipmentSource_id=:source";
            $ar["source"] = $filters["source"];
        }else {
            $sourceJoin = 'LEFT JOIN CIL_EquipmentSource s ON (e.EquipmentSource_id=s.id) ';
        }

        if($filters["showloc"] != 'all') {
            if($filters["showloc"]=='') {
                $where .= " AND e.location=''";
            }else {
                $where .= " AND e.location=:showloc";
                $ar["showloc"] = $filters["showloc"];
            }
        }

        if($value != "") {
            $where .= " AND e.".mysql_escape_string($field)." LIKE :value";
            $ar["value"] = "%$value%";
        }

        $sql = sprintf("SELECT SQL_CALC_FOUND_ROWS e.id, e.name, es.status, et.type, ec.condition, e.location, e.asset_tag, s.name AS source
                        FROM CIL_Equipment e
                        INNER JOIN CIL_EquipmentStatus es ON (e.EquipmentStatus_id=es.id)
                        INNER JOIN CIL_EquipmentTypes et ON (e.EquipmentTypes_id=et.id)
                        INNER JOIN CIL_EquipmentCondition ec ON (e.EquipmentCondition_id=ec.id)
                        $sourceJoin
                        WHERE e.deleted=:deleted %s ORDER BY e.name ASC%s",
                        $where,
                        $limit);

        $equip = api_DoSQL($sql, $ar);
        $t = api_DoSQL("SELECT FOUND_ROWS() AS cnt", null);
        $total = $t[0]["cnt"];

        return $equip;
    }
?>