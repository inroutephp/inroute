class Dependencies {
private $container;

public function __construct($container){
    $this->container = $container;
}

<?php foreach ($factories as $name => $injections) { ?>
function <?php echo str_replace('\\', '_', $name);?>(){
    
    <?php foreach ($injections as $counter => $inject) { ?>

    $p<?php echo $counter;?> = $this->container['<?php echo $inject['factory'];?>']();

    <?php if ($inject['class']) { ?>

    if (!$p<?php echo $counter;?> instanceof \<?php echo $inject['class'];?>) {
        $msg = 'DI-container method <?php echo $inject['factory'];?> must return a <?php echo $inject['class'];?> instance.';
        throw new DependencyExpection($msg);
    }

    <?php } ?>
    <?php } ?>

    return new <?php echo $name;?>(<?php foreach ($injections as $counter => $void) echo '$p' . $counter . ', '; ?>);
}
<?php } ?>

}