<?php
/**
 * @package   HierarchicalGroupsForBP
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

/**
 * Output the permalink breadcrumbs for the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 * @param string      $separator String to place between group links.
 */
function hgbp_group_permalink_breadcrumbs( $group = false, $separator = ' / ' ) {
	echo hgbp_get_group_permalink_breadcrumbs( $group, $separator );
}

	/**
	 * Return the permalink breadcrumbs for the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
     * @param string      $separator String to place between group links.
     *
	 * @return string
	 */
	function hgbp_get_group_permalink_breadcrumbs( $group = false, $separator = ' / ' ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group = $groups_template->group;
		}
		$user_id = bp_loggedin_user_id();

		// Create the base group's entry.
		$item        = '<a href="' . bp_get_group_permalink( $group ) . '">' . bp_get_group_name( $group ) . '</a>';
		$breadcrumbs = array( $item );
		$parent_id   = hgbp_get_parent_group_id( $group->id, $user_id );

		// Add breadcrumbs for the ancestors.
		while ( $parent_id ) {
			$parent_group  = groups_get_group( $parent_id );
			$breadcrumbs[] = '<a href="' . bp_get_group_permalink( $parent_group ) . '">' . bp_get_group_name( $parent_group ) . '</a>';
			$parent_id     = hgbp_get_parent_group_id( $parent_group->id, $user_id );
		}

		$breadcrumbs = implode( $separator, array_reverse( $breadcrumbs ) );

		/**
		 * Filters the breadcrumb trail for the current group in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string          $breadcrumb String of breadcrumb links.
		 * @param BP_Groups_Group $group      Group object.
		 */
		return apply_filters( 'hgbp_get_group_permalink_breadcrumbs', $breadcrumbs, $group );
	}

/**
 * Output the URL of the hierarchy page of the current group in the loop.
 *
 * @since 1.0.0
 */
function hgbp_group_hierarchy_permalink( $group = false ) {
	echo hgbp_get_group_hierarchy_permalink( $group );
}

	/**
	 * Generate the URL of the hierarchy page of the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function hgbp_get_group_hierarchy_permalink( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		// Filter the slug via the 'hgbp_screen_slug' filter.
		return trailingslashit( bp_get_group_permalink( $group ) . hgbp_get_hierarchy_screen_slug() );
	}

/**
 * Output the upper pagination block for a group directory list.
 *
 * @since 1.0.0
 */
function hgbp_groups_loop_pagination_top() {
	return hgbp_groups_loop_pagination( 'top' );
}

/**
 * Output the lower pagination block for a group directory list.
 *
 * @since 1.0.0
 */
function hgbp_groups_loop_pagination_bottom() {
	return hgbp_groups_loop_pagination( 'bottom' );
}

	/**
	 * Output the pagination block for a group directory list.
	 *
	 * @param string $location Which pagination block to produce.
	 *
	 * @since 1.0.0
	 */
	function hgbp_groups_loop_pagination( $location = 'top' ) {
		if ( 'top' != $location ) {
			$location = 'bottom';
		}

		// Pagination needs to be "no-ajax" on the hierarchy screen.
		$class = '';
		if ( hgbp_is_hierarchy_screen() ) {
			$class = ' no-ajax';
		}

		/*
		 * Return typical pagination on the main group directory first load and the
		 * hierarchy screen for a single group. However, when expanding the tree,
		 * we need to not use pagination, because it conflicts with the main list's
		 * pagination. Instead, show the first 20 and provide a link to the rest.
		 */
		?>
		<div id="pag-<?php echo $location; ?>" class="pagination<?php echo $class; ?>">

			<div class="pag-count" id="group-dir-count-<?php echo $location; ?>">

				<?php bp_groups_pagination_count(); ?>

			</div>

			<?php
			// Check for AJAX requests for the child groups toggle.
			if ( isset( $_REQUEST['action'] ) && 'hgbp_get_child_groups' == $_REQUEST['action'] ) :

				// Provide a link to the parent group's hierarchy screen.
				if ( ! empty( $_REQUEST['parent_id'] ) ) :
					$parent_group = groups_get_group( (int) $_REQUEST['parent_id'] );
				?>
					<a href="<?php hgbp_group_hierarchy_permalink( $parent_group ); ?>" class="view-all-child-groups-link"><?php
					printf( __( 'View all child groups of %s.', 'hierarchical-groups-for-bp' ), bp_get_group_name( $parent_group ) ); ?></a>
				<?php endif;

			else : ?>

					<div class="pagination-links" id="group-dir-pag-<?php echo $location; ?>">

						<?php bp_groups_pagination_links(); ?>

					</div>

				<?php
			endif; ?>
		</div>
		<?php
	}
