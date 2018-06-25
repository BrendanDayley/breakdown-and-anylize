<?php
function updateComplainantPersonType($complainantType, $elemId, $type, $complaintOther = null, $complaintId = null){
        $complainantType = complaintTypeToString($complainantType);          
        $sql = '';
        $selected = '';
        $original = '';
        $class = '';
        $html = '';
        $objResponse = new xajaxResponse();
        $html .= '<td width="15%"  style="text-align: right; vertical-align: top;" class="tablecell">
            <label for="'.$type.'SrchCon">
            '.__($type." Name").':
            ';
            if(api_IfACL('keyboardNavigation')) {
                $html .= '<br />'.__("required");
            };
        $html .= '</label>
        </td>';
        if($complainantType == 'Other'){
            $html .= '<td width="85%" class="tablecell" style="vertical-align: top;">
                        <input type="text" id="'.$type.'Other" title="'.__(''.$type.' Name').'" name="'.$type.'Other" size="20"';
            if($complaintOther){
                 $html .= ' value="'. $complaintOther .'"';
            }
            $html .= '/>';
        }
        if($complainantType == 'Consumer'){
            $html .= '<td width="85%" class="tablecell">
                    <input type="text" id="'.$type.'SrchCon"
                        title="'. __($type.' Search') .'" size="20" value=""
                    />
                    <input type="button" id="'.$type.'BttnCon"
                        value="'. __('Submit Search') .'"
                        title="'. __($type.' Search Submit').'>" onclick="PersonSearch(\''.$type.'\', \'Con\');"                    />
                    <br />
                    <select name="'.$type.'Id" id="'.$type.'IdCon"
                        title="'. __($type." Name").' required"';
                        $html .= 'size="4">';
            $sql = 'SELECT c.id, CONCAT_WS(" ", fname, midname, lname) AS `name`, CONCAT_WS(" ", addr1, addr2, city, p.`name`, zip, email) AS additional
                    FROM CIL_Consumers c
                    LEFT JOIN Framework_2_0_Shared.provinces p ON c.province_id=p.id
                    WHERE is_consumer=1
                    ORDER BY fname, midname, lname ';


        }
        if($complainantType == 'I&R'){
            $html .= '<td width="85%" class="tablecell">
                    <input type="text" id="'.$type.'SrchIr" title="'. __($type.' Search').'" size="20" value="" >
                    <input type="button" id="'.$type.'BttnIr" value="'. __('Submit Search') .'" title="'. __($type.' Search Submit').'"
                    onclick="PersonSearch(\''.$type.'\',\'I&R\');">
                    <br />
                    <select name="'.$type.'Id" id="'.$type.'IdIr" title="'. __($type." Name") .' required" size="4">';

            $sql = 'SELECT c.id, CONCAT_WS(" ", fname, midname, lname) AS `name`, CONCAT_WS(" ", addr1, addr2, city, p.`name`, zip, email) AS additional
                                FROM CIL_Consumers c
                                LEFT JOIN Framework_2_0_Shared.provinces p ON c.province_id=p.id
                                WHERE is_consumer=2
                                ORDER BY fname, midname, lname ';
        }
        if($complainantType == 'Alt Contact'){

            $html .= '<td width="85%" class="tablecell">
                        <input type="text" id="'.$type.'SrchAlt" title="'. __($type.' Search').'" size="20" value=""/>
                        <input type="button" id="'.$type.'BttnAlt"
                            value="'. __('Submit Search').'"
                            title="'. __($type.' Search Submit').'" onclick="PersonSearch(\''.$type.'\',\'Alt\');"
                        />
                        <br /><select name="'.$type.'Id" id="'.$type.'IdAlt" title="'. __($type." Name").' required" size="4">';

                        $sql = 'SELECT id, `name`, CONCAT_WS(" ", addr, addr2, city, state, zip, email) AS additional FROM CIL_ConsumerContacts';

        }
        if($complainantType == 'Attendant'){
            $html .= '<td width="85%" class="tablecell">
                    <input type="text" id="'.$type.'SrchPca" title="'.__($type.' Search').'" size="20" value="" />
                    <input type="button" id="'.$type.'BttnPca"
                        value="'. __('Submit Search').'"
                         title="'. __($type.' Search Submit').'" onclick="PersonSearch(\''.$type.'\',\'Pca\');"
                    />
                    <br /><select name="'.$type.'Id" id="'.$type.'IdPca" title="'. __($type." Name").' required"
                    size="4">';

            $sql = 'SELECT id, `name`, CONCAT_WS(" ", addr, addr2, city, state, zip, email) AS additional FROM CIL_PCAs';

        }
        if($complainantType == 'Staff'){
            $html .= '<td width="85%" class="tablecell">
                    <input type="text" id="'.$type.'SrchStaff" title="'.__($type.' Search').'" size="20" value="" />
                    <input type="button" id="'.$type.'BttnStaff"
                        value="'. __('Submit Search').'"
                        title="'. __($type.' Search Submit').'" onclick="PersonSearch(\''.$type.'\',\'Staff\');"
                    />
                    <br /><select name="'.$type.'Id" id="'.$type.'IdStaff" title="'. __($type." Name").' required" size="4">';
            $sql = 'SELECT id, `name`, email AS additional FROM api_users';

        }
        if($sql != ''){
            $list = api_DoSql($sql, null);
            foreach($list as $row){
                if($row['id']==$complaintId) {
                    $selected = 'selected="yes"';
                    $original = 'yes';
                    $class = '';
                } elseif (isset($_GET['personId']) && $_GET['personId'] == $row['id']) {
                    $selected = 'selected="yes"';
                    $original = 'yes';
                    $class = '';
                } else {
                    $selected = '';
                    $original = 'no';
                    $class = 'ui-helper-hidden';
                }
               $html .= '<option value="'.$row['id'].'" '
                        .$selected.'
                        title="'.$row['name'].': '.$row['additional'].'"'
                        .'data-search="'.strtolower($row['name'].' '.$row['additional'])
                        .'"data-original="'.$original.'">'.$row['name'].'</option>';
            }
        }
        if($complainantType != 'Other'){
            $html .= '</select>';
        }
         $html .= '&nbsp;&nbsp;&nbsp;('. __("required").')</td>';
        $objResponse->addAssign($elemId,"innerHTML", $html);
        return $objResponse->getXML();
    }
?>