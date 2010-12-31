<?php
defined('_JEXEC') or die;
?>

<div id="cpanel">
	<?php for ($i=0; $i<count($this->views); $i++) : 
			$view = $this->views[$i];
			$link = 'index.php?option='.$this->option.'&view='.$view['view'];
			$count	= isset($this->modified[$view['view']]) ? $this->modified[$view['view']] : '';
	?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link;?>">
					<img src='templates/khepri/images/header/icon-48-generic.png' alt='<?php echo $view['name'];?>' />
					<span><?php echo $view['name'];?></span>
					<?php if ($count) : ?>
						<span class="modified_count"><?php echo $count; ?></span>
					<?php endif; ?>
				</a>
			</div>
		</div>
	<?php endfor; ?>
</div>