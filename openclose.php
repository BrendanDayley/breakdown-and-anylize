<tr>
    <th class="tablehead" colspan="2">
        <a href="javascript: toggleView('filters')"><?php echo(__("Additional Filters"));?></a>
        <input type="button" value="<?php echo(__("Open/Close"));?>" title="<?php echo(__("Open/Close Additional Filters"));?>" onclick="toggleView('filters');">
        <input type="hidden" id="filters_visible" name="filters_visible" value="<?php echo ((isset($_SESSION["emaillist"]["POST"]["filters_visible"])) ? $_SESSION["emaillist"]["POST"]["filters_visible"] : 'false'); ?>">
    </th>
</tr>
<tr style="border:0px">
    <td colspan="6"><div id="filters" style="display:<?php echo ((isset($_SESSION["emaillist"]["POST"]["filters_visible"]) && $_SESSION["emaillist"]["POST"]["filters_visible"]=='true') ? 'block' : 'none');?>"><br /><div class="border">
        <table class="admintable" rules="rows" cellpadding="0" cellspacing="0">




        <input type="button" value="<?php echo(__("Open/Close"));?>" title="<?php echo(__("Open/Close funding Entries"));?>" onclick="toggleView('fundingEntriesDiv', null);">
        <input type="hidden" id="fundingEntriesDiv_visible" name="filters_visible" value="<?php echo ((isset($_SESSION["Consumer"][$recordIndex]["fundingEntries_visible"])) ? ["Consumer"][$recordIndex]["fundingEntries_visible"] : 'false'); ?>"
    </td>
    <td class="tablehead" style="text-align: right;">
        <input type="button" class="button" name="addFundingEntry" title="<?php echo(__("Add Entries"));?>" value="<?php echo(__("Add Entries"));?>" onclick="document.location.href='update_service_funding_entry.php?sid=<?php echo($_GET["sid"]);?>&cid=<?php echo($_GET["cid"]);?>'" />
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">
        <div id="fundingEntriesDiv" style="display:<?php echo ((isset($_SESSION["Consumer"][$recordIndex]["fundingEntries_visible"]) && $_SESSION["Consumer"][$recordIndex]["fundingEntries_visible"]=='true') ? 'block' : 'none');?>">
            