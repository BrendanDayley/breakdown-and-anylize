<?php

    function getServiceCount($serv_id, $areas, $post, $filters){
        $sdate = date("Y-m-d",strtotime($post["startdate"]));
        $edate = date("Y-m-d",strtotime($post["enddate"]));
        $array = array();
        $wheres = [];
    }

?>