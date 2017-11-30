<?php
$title_for_layout = $product->name;
$description = $product->name;
$keywords = $product->name;
$this->set(compact('title_for_layout', 'description', 'keywords'));
?>

<?php $this->Html->addCrumb('Shop', ['controller' => 'products', 'action' => 'index', '_full' => true]); ?>
<?php $this->Html->addCrumb($product->category->name, ['controller' => 'categories', 'action' => 'view', $product->category->slug, '_full' => true]); ?>
<?php $this->Html->addCrumb($product->name, ['controller' => 'products', 'action' => 'view', $product->slug, '_full' => true]); ?>

<br />
<br />


<div itemscope itemtype="http://schema.org/Product">
    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-12">
            <?php echo $this->Html->image('/images/large/' . $product->image, ['class' => 'img-fluid', 'alt' => $product->name, 'itemprop' => 'image']); ?>
            <br />
            <br />
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12">
            <h1 itemprop="name"><?php echo $product->name; ?></h1>
            <h4><?php echo $this->Html->link($product->category->name, ['controller' => 'categories',  'action' => 'view', $product->category->slug]); ?></h4>

            <span itemprop="description"><?php echo $product->description; ?></span>

            <br />
            <br />

            <?php echo $this->Form->create(NULL, ['url' => ['controller' => 'products', 'action' => 'add']]); ?>
            <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $product->id)); ?>

            <?php // print_r($productoptions); ?>

            <?php if(!empty($productoptionlists)): ?>
                <?php if(!empty($attribute->name)): ?>
                    <?php echo $attribute->name; ?>
                <?php endif; ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if(!empty($shorts[0])): ?>
                            <small>
                            <?php foreach($shorts as $k => $v): ?>
                                <button type="button" class="shorts btn btn-secondary btn-sm" data-target="#productoptionlist" value="<?php echo $v; ?>"><?php echo $v; ?></button>
                            <?php endforeach; ?>
                            <br />
                            <br />
                            </small>
                        <?php endif; ?>
                        <?php if(!empty($weights[0])): ?>
                            <small>
                            <?php foreach($weights as $k => $v): ?>
                                <button type="button" class="weights btn btn-secondary btn-sm" data-target="#productoptionlist" value="<?php echo $v; ?>"><?php echo $v; ?></button>
                            <?php endforeach; ?>
                            <br />
                            <br />
                            </small>
                        <?php endif; ?>
                        <?php if(!empty($productoptions)): ?>
                            <?php echo $this->Form->input('productoptionlist', ['label' => false, 'empty' => '- Please Select', 'class' => 'form-control']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <br />
                <input type="hidden" id="modselected" value="" />

            <?php endif;?>

            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

            <strong><span itemprop="priceCurrency" id="pricecurrency" content="USD">$</span><span id="price" itemprop="price" content="<?php echo number_format($product->price, 2); ?>"><?php echo number_format($product->price, 2); ?></span></strong>

            <link itemprop="availability" href="http://schema.org/InStock" />

            </div>

            <br />

            <?php echo $this->Form->button('<i class="fa fa-cart-plus"></i> &nbsp; Add to Cart', ['class' => 'btn btn-success btn-sm', 'id' => 'addtocart', 'escape' => false]);?>
            <?php echo $this->Form->end(); ?>

            <br />
            <small><?php echo $product->name; ?></small>

            <br />
            <br />

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <small>

            <?php if(!empty($productoptionlists)): ?>

            <table class="table-bordered table-condensed table-hover">
            <?php foreach($productoptionlists as $key => $value): ?>
            <tr>
                <td>
                    <?php echo $this->Form->create(NULL, ['url' => ['controller' => 'products', 'action' => 'add']]); ?>
                    <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $product->id)); ?>
                    <?php echo $this->Form->input('productoptionlist', array('type' => 'hidden', 'value' => $key)); ?>
                    <?php echo $value; ?>
                </td>
                <td>
                    <?php echo $this->Form->button('<i class="fa fa-cart-plus"></i> &nbsp; Add to Cart', ['class' => 'btn btn-success btn-xs', 'id' => 'addtocart', 'escape' => false]);?>
                    <?php echo $this->Form->end(); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </table>
            <?php endif; ?>

            </small>
        </div>
    </div>
</div>

<input type="hidden" id="product_price" name="product_price" value="<?php echo sprintf('%01.2f', $product->price); ?>" />

<br />