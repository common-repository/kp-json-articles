<?php
/** 
 * Default Article Single
 * 
 * This is the default article single template. 
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

?>

<section class="section section-lg">
	<div class="container">
		<div class="row">
            <div class="col-lg-4">
                <?php

                    // get our sidebar
                    dynamic_sidebar( 'kp_ja_sidebar' );
                ?>
			</div>			
            <div class="col-lg-8">
                <?php 

                    // while he have posts here, loop them
                    while( have_posts( ) ) : the_post( );

                        // setup a couple variables
                        $_link = get_the_permalink( );
                        $_pid = $post -> ID;

                        // get the post thumbnail
                        $_thumb = get_the_post_thumbnail_url( $_pid, 'thumbnail' );

                    ?>
                        <div class="container">
                            <article class="row clearfix">
                                <header class="article-header">
                                    <h2 class="article-title">
                                        <?php echo $post -> post_title; ?>
                                    </h2>
                                    <div class="article-meta">
                                        <time pubdate datetime="<?php _e( get_the_date( 'c' ) ); ?>" title="<?php _e( get_the_date( ) ); ?>">
                                            Posted On: <?php _e( get_the_date( ) ); ?>
                                        </time>
                                        <dl class="article-categories">
                                            <dt>Categories</dt>
                                            <?php

                                                // get the categories
                                                $_cats = wp_get_post_terms( $_pid, 'kp_json_art_cats' );

                                                // loop them
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

                                                // get the tags
                                                $_tags = wp_get_post_terms( $_pid, 'kp_json_art_tags' );

                                                // loop them
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

                                        // throw the image in here floated right to the content
                                        _e( '<img src="' . $_thumb . '" style="float:right;margin:15px;" />' );

                                        // echo out our article
                                        _e( $post -> post_content );

                                    ?>
                                </section>
                            </article>
                        </div> 
                    <?php 
                    endwhile;
                ?>
                <footer class="article-footer">
                    <nav class="article-navigation">
                        <ul class="pager">
                            <li class="previous text-left">
                                <?php

                                // get and render our previous post link
                                $prev_post = get_previous_post( );
                                if ( ! empty( $prev_post ) ) {
                                    echo '<a class="" href="' . __( get_permalink( $prev_post -> ID ) ) . '" title="' . __( $prev_post -> post_title ) . '"><i class="fa fa-angle-double-left"></i> ';
                                    echo '<span class="post_title">' . substr( strip_tags( __( $prev_post -> post_title ) ), 0, 30 ) . ' ...</span></a>';
                                }
                                ?>
                            </li>
                            <li class="next text-right">
                                <?php

                                // get and render our next post link
                                $next_post = get_next_post( );
                                if ( ! empty( $next_post ) ) {
                                    echo '<a class="" href="' . __( get_permalink( $next_post -> ID ) ) . '" title="' . __( $next_post -> post_title ) . '"><span class="post_title">' . substr( strip_tags( __( $next_post -> post_title ) ), 0, 30 ) . ' ...</span> ';
                                    echo '<i class="fa fa-angle-double-right"></i></a> ';
                                }
                                ?>
                            </li>
                        </ul>
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
