<?php

/**
 * Template Name: Home Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package justg
 */

get_header();
?>

<div id="velocityslider" class="carousel slide mx-minus-3" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php for($x = 1; $x <= 10; $x++){
        if($x == '1'){
            $class = ' active';
        } else {
            $class = '';
        }
        $img_id = velocitytheme_option('home_slider'.$x, '');
        if($img_id){
            $img_url = aq_resize(wp_get_attachment_image_url($img_id,''),1000,400,true,true,true);
            echo '<div class="carousel-item'.$class.'">';
                if($img_url){
                    echo '<img src="'.$img_url.'" class="d-block w-100">';
                } else {
                    echo '<svg style="background-color:#ececec;width:100%;height:auto;" width="1000" height="400"></svg>';
                }
            echo '</div>';
        }
    } ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#velocityslider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#velocityslider" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<?php
    echo '<div class="row mx-minus-3">';      
        for($x = 1; $x <= 4; $x++){
            $hs_img = velocitytheme_option('hs_img'.$x, '');
            $hs_icon = velocitytheme_option('hs_icon'.$x, '');
            $hs_text = velocitytheme_option('hs_text'.$x, '');
            $hs_url = velocitytheme_option('hs_url'.$x, '');

            if($hs_icon || $hs_text){
                if($hs_img){
                    $bg = ' style="background-image:url('.wp_get_attachment_image_url($hs_img,'').')"';
                } else {
                    $bg = '';
                }
                echo '<div class="col-sm-6 col-md p-0">';
                    echo '<div class="h-100 text-center m-0 p-3 pt-4 text-white bg-dark velocity-service position-relative"'.$bg.'>';
                        if($hs_icon){
                            echo '<div class="position-relative z-index mb-2">';
                                echo '<i class="velocity-hs-icon fa '.$hs_icon.'"></i>';
                            echo '</div>';
                        } if($hs_text){
                            echo '<div class="py-2 position-relative z-index">';
                                echo $hs_text;
                            echo '</div>';
                        } if($hs_url){
                            echo '<a class="velocity-hs-link" href="'.$hs_url.'"></a>';
                        }
                    echo '</div>';
                echo '</div>';
            }
        }
    echo '</div>';
?>


<?php
$args['post_type'] = 'post';
$args['showposts'] = 3;
$home_news = velocitytheme_option('home_news', '');
if($home_news){
    $args['cat'] = $home_news;
}
$posts = get_posts($args);
if($posts){
echo '<div class="m-1 py-3">';
    echo '<h2 class="fs-4 fw-bold text-dark">'.velocitytheme_option('hn_title', '').'</h2>';
    echo '<div class="row">';
    foreach($posts as $post) {
        $trimmed_content = wp_trim_words($post->post_content,20 );
        $link = get_the_permalink($post->ID);
        echo '<div class="col-sm mb-4">';
            echo '<div class="mb-2">';
                echo do_shortcode('[resize-thumbnail width="350" height="250" linked="true" post_id="'.$post->ID.'"]');
            echo '</div>';
            echo '<small class="text-muted">'.get_the_date('',$post->ID).'</small>';
            echo '<div class="fs-6 mb-2 lh-sm fw-bold">';
                echo '<a class="text-dark" href="'.$link.'">'.$post->post_title.'</a>';
            echo '</div>';
            echo $trimmed_content;
            echo '<div class="mt-2">';
                echo '<a class="btn btn-primary btn-sm px-3" href="'.$link.'">Selengkapnya</a>';
            echo '</div>';
        echo '</div>';
    }
    echo '</div>';
echo '</div>';
}
?>



<?php
get_footer();
