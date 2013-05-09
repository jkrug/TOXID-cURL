<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
]]></content><sidebar><![CDATA[
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php endif; ?>
]]></sidebar><footer><![CDATA[
    <footer id="colophon" role="contentinfo">
        <div class="site-info">
            <?php do_action( 'twentytwelve_credits' ); ?>
            <a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentytwelve' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentytwelve' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentytwelve' ), 'WordPress' ); ?></a>
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
<?php wp_footer(); ?>
]]></footer></toxid>