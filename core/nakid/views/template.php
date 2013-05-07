<!doctype html>
<!-- nakid.org -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $this->load->view('includes/head'); ?>
</head>

<body>
<div id="all">
    <div id="page">
      <?php $this->load->view('includes/header'); ?>
      <div id="content">
        <?php if(isset($notes) && count($notes) > 0){ ?>
        <div id="messages">
        <ul>
            <?php foreach($notes as $note){ ?>
                <li class="<?php echo $note[1]; ?>"><?php echo $note[0]; ?></li>
            <?php } ?>
        </ul>
        </div>
        <?php } ?>
        <?php $this->load->view('pages/'.$page.''); ?>
      </div>
    </div>
    <div class="push"></div>
</div>
<?php $this->load->view('includes/footer'); ?>
<?php //var_dump(get_defined_vars()); ?>
</body>
</html>