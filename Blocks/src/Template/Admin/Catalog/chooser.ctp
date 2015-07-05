<div class="row-fluid">
	<div class="span12">
		<?php
		echo __d('croogo', 'Sort by:');
		echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), array('class' => 'sort'));
		?>
	</div>
</div>

<?php foreach ($items as $item): ?>
	<div class="row-fluid">
		<h2><?= $item->title; ?></h2>
		<em><strong><?= h(__d('croogo', 'Class name')); ?>: </strong> <?= $item->className; ?></em>
		<?php if (isset($region)): ?>
			<?php $regionInfo = \Croogo\Blocks\Catalog::regionInfo($item->className, $region->alias); ?>
			<h3><?= h(__d('croogo', '%1$s [%2$s]', $regionInfo['title'], $regionInfo['method'])); ?></h3>
			<p><?= $regionInfo['description']; ?></p>
			<ul>
				<?php if (isset($regionInfo['original'])): ?>
					<li><strong><?= h(__d('croogo', 'Original region')); ?></strong>: <?= $regionInfo['original']; ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<h3><?= h(__d('croogo', 'Other supported regions')); ?></h3>
		<ul>
			<?php foreach ($item->regions as $regionAlias => $otherRegion): ?>
				<li><strong><?= $regionAlias; ?></strong>: <?= $otherRegion['title']; ?> - <?= $otherRegion['description']; ?> [Aliases: <em><?= implode(', ', $otherRegion['aliases']); ?></em>]</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $this->CroogoHtml->link($item->title, '#', array(
			'class' => 'item-choose',
			'data-chooser_type' => 'Node',
			'data-chooser_id' => $item->className,
			'data-chooser_title' => $item->title,
			'rel' => $item->className,
		));
		?>
	</div>
<?php endforeach; ?>
<?php

$this->CroogoHtml->scriptBlock('$(\'.popovers\').popover().on(\'click\', function() { return false; });', ['block' => 'scriptBottom']);

$target = json_encode($this->request->query('target'));

$script =<<< EOF
$('#nodes-for-links').itemChooser({
	fields: [{ type: "Node", target: $target, attr: "rel" }]
});
$("#nodes-for-links a").click(function() {
	$("#link_choosers").modal('hide');
});
EOF;

echo $this->CroogoHtml->scriptBlock($script);
