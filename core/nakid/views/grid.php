<?php header("Content-type: text/xml;charset=utf-8"); ?>
<?php if(!empty($error)){ ?>
<error>
	<?php echo($error);  ?>
</error>
<?php }else{ ?>
<rows>
    <page><?php echo($page); ?></page>
    <total><?php echo($total_pages); ?></total>
    <records><?php echo($count); ?></records>
    <?php foreach($grid as $rid=>$row){ ?>
    <row id="<?php echo($rid); ?>">
        <?php foreach($row as $cell){ ?>
        <cell><![CDATA[<?php echo $cell; ?>]]></cell>
        <?php } ?>
    </row>
    <?php } ?>
</rows>
<?php } ?>