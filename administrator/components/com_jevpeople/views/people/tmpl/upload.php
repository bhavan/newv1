<?php defined('_JEXEC') or die('Restricted access'); ?>


<script language="javascript" type="text/javascript">
	var oname = "<?php echo $this->oname;?>";
	var fname = "<?php echo $this->fname;?>";
	var filename = "<?php echo $this->filename;?>";
	var filetype = "<?php echo $this->filetype;?>";
	<?php if ($this->filetype=="image"){ ?>
	window.parent.setImageFileName();
	<?php } else { ?>
	window.parent.setLinkFileHref();
	<?php } ?>
</script>
