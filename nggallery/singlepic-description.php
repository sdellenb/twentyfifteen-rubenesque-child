<?php
/**
Template Page for the single pic with caption

Follow variables are useable :

        $image : Contain all about the image
        $meta  : Contain the raw Meta data from the image
        $exif  : Contain the clean up Exif data from file
        $iptc  : Contain the clean up IPTC data from file
        $xmp   : Contain the clean up XMP data  from file
        $db    : Contain the clean up META data from the database (should be imported during upload)

Please note : A Image resize or watermarking operation will remove all meta information, exif will in this case loaded from database

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>

 This template was created from the singlepic.php
 It is NOT content of th official nextgen-galery plugin.

 The latest version can be downloaded from http://schueler.ws/?p=316
 (or search the blog, or use the tag-cloud)

Version 1.1, vom 4.12.2014

**/
?>
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<?php if (!empty ($image)) : ?>

<div class="<?php echo $image->classname ?>" style="width:<?php echo $image->size[0];?>px">

<a href="<?php echo $image->imageURL ?>" title="<?php echo $image->linktitle ?>" <?php echo $image->thumbcode ?> >
        <img src="<?php echo $image->thumbnailURL ?>" alt="<?php echo $image->alttext ?>" title="<?php echo $image->alttext ?>" />
</a>

<?php if (!empty ($image->description)) : ?>

<p class="ngg-singlepic-caption"><?php echo $image->description ?></p>

<?php endif; ?>

</div>

<?php endif; ?>


