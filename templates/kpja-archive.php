<?php
/** 
 * Default Article Archive
 * 
 * This is the default article archive template. 
 * Copy this to your theme to override it
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// get header
get_header( );

// pull our setting for the number of articles to display per page
$_art_per_age = ( empty( get_ja_option( 'article_display_count' ) ) ) ? 5 : get_ja_option( 'article_display_count' );

// run our querying to get the proper CPT
$_qry = new WP_Query( array( 'post_type' => 'kp_json_art', 'posts_per_page' => $_art_per_age, 'paged' => $paged, 'orderby' => 'date','order' => 'DESC' ) );

?>

<section class="section section-lg">
	<div class="container">
		<div class="row">
            <div class="col-lg-4">
                <?php

                    // pull in our sidebar
                    dynamic_sidebar( 'kp_ja_sidebar' );
                ?>
			</div>			
            <div class="col-lg-8">
                <?php 

                    // get the max number of pages
                    $pg_ct = $_qry -> max_num_pages;

                    // loop the posts if there are any
                    while( $_qry -> have_posts( ) ) : $_qry -> the_post( );

                        // setup a couple of variables
                        $_link = get_the_permalink( );
                        $_pid = $post -> ID;
                    ?>
                        <div class="container">
                            <article class="row clearfix">
                                <header class="article-header">
                                    <h2 class="article-title">
                                        <a href="<?php _e( $_link ); ?>" title="<?php _e( $post -> post_title ); ?>"><?php _e( $post -> post_title ); ?></a>
                                    </h2>
                                    <div class="article-meta">
                                        <time pubdate datetime="<?php _e( get_the_date( 'c' ) ); ?>" title="<?php _e( get_the_date( ) ); ?>">
                                            Posted On: <?php _e( get_the_date( ) ); ?>
                                        </time>
                                        <dl class="article-categories">
                                            <dt>Categories</dt>
                                            <?php
                                                $_cats = wp_get_post_terms( $_pid, 'kp_json_art_cats' );
                                                foreach( $_cats as $_cat ) {
                                                    ?>
                                                    <dd class="article-category">
                                                        <a href="<?php _e( get_term_link( $_cat, 'kp_json_art_cats' ) ); ?>" title="<?php _e( $_cat -> name ); ?>"><?php _e( $_cat -> name ); ?></a>
                                                    </dd>
                                                    <?php
                                                }
                                            ?>
                                        </dl>
                                        <dl class="article-tags">
                                            <dt>Tags</dt>
                                            <?php
                                                $_tags = wp_get_post_terms( $_pid, 'kp_json_art_tags' );
                                                foreach( $_tags as $_tag ) {
                                                    ?>
                                                    <dd class="article-tag">
                                                        <a href="<?php _e( get_term_link( $_tag, 'kp_json_art_tags' ) ); ?>" title="<?php _e( $_tag -> name ); ?>"><?php _e( $_tag -> name ); ?></a>
                                                    </dd>
                                                    <?php
                                                }
                                            ?>
                                        </dl>
                                    </div>
                                </header>
                                <section class="section article-content">
                                    <?php

                                        // write out the content, but make sure to trim to and exceprt
                                        _e( wp_trim_words( $post -> post_content, 25 ) );
                                    ?>
                                    <a href="<?php _e( $_link ); ?>" title="<?php _e( $post -> post_title ); ?>" class="btn btn-primary">Read More</a>
                                </section>
                            </article>
                        </div> 
                    <?php 
                    endwhile;
                ?>  
                <footer class="article-footer">
                    <nav class="article-navigation">
                        <?php

                            // echo out our pagination
                            echo paginate_links( array(
                                'prev_text'=>' Previous ', 
                                'next_text'=>' Next ', 
                                'current' => max( 1, $paged ), 
                                'total' => $pg_ct, 
                                'type' => 'plain', 
                                'paged' => $paged, ) 
                            );
                        ?>
                    </nav>
                </footer>
            </div>
		</div>
	</div>
</section>

<?php

// reset our query
wp_reset_query( );

// get footer
get_footer( );
