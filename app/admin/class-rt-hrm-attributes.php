<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rt_HRM_Attributes
 *
 * @author dipesh
 */
if( !class_exists( 'Rt_HRM_Attributes' ) ) {
    /**
     * Class Rt_HRM_Attributes
     */
    class Rt_HRM_Attributes {

        /**
         * Slug for attributes page
         * @var string
         */
        var $attributes_page_slug = 'rthrm-attributes';

		static $leave_type_tax = 'rt-leave-type';

		var $leave_type_tax_label;

        /**
         * Object initialization
         */
        public function __construct() {
			add_action( 'init', array( $this, 'init_attributes' ) );
		}

        /**
         * Create object of rt-attributes classes & Add attributes page for leave [Custom post type]
         */
        function init_attributes() {
			$this->init_leave_type();
			add_action( 'admin_menu' , array( $this, 'remove_leave_type_meta_box' ) );
		}

		function remove_leave_type_meta_box() {
			global $rt_hrm_module;
			remove_meta_box( self::$leave_type_tax . 'div', $rt_hrm_module->post_type, 'side' );
		}

		function init_leave_type() {
            $editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
            $caps = array(
				'manage_terms' => $editor_cap,
				'edit_terms'   => $editor_cap,
				'delete_terms' => $editor_cap,
				'assign_terms' => $editor_cap,
            );

			global $rt_hrm_module;
			$name = self::$leave_type_tax;
			$label = $this->leave_type_tax_label = __( 'Leave Type' );
			$post_type = $rt_hrm_module->post_type;

			register_taxonomy(
				$name,
				$post_type,
				array(
					'public'					=> false,
					'hierarchical' 				=> true,
					'update_count_callback' 	=> 'rthrm_update_post_term_count',
					'labels' => array(
							'name' 						=> $label,
							'singular_name' 			=> $label,
							'search_items' 				=> __( 'Search' ) . ' ' . $label,
							'all_items' 				=> __( 'All' ) . ' ' . $label,
							'parent_item' 				=> __( 'Parent' ) . ' ' . $label,
							'parent_item_colon' 		=> __( 'Parent' ) . ' ' . $label . ':',
							'edit_item' 				=> __( 'Edit' ) . ' ' . $label,
							'update_item' 				=> __( 'Update' ) . ' ' . $label,
							'add_new_item' 				=> __( 'Add New' ) . ' ' . $label,
							'new_item_name' 			=> __( 'New' ) . ' ' . $label,
						),
					'show_ui' 					=> true,
					'show_admin_column'			=> false,
					'query_var' 				=> true,
					'capabilities'				=> $caps,
					'show_in_nav_menus' 		=> true,
					//'rewrite' 					=> array( 'slug' => $product_attribute_base . sanitize_title( $tax->attribute_name ), 'with_front' => false, 'hierarchical' => $hierarchical ),
					'rewrite'					=> true,
				)
			);
		}

        /**
         * @param $attr
         * @param $post_id
         * @param $newLead
         * @return string
         */
        function attribute_diff( $attr, $post_id, $newLead ) {

			$diffHTML = '';
			switch ( $attr->attribute_store_as ) {
				case 'taxonomy':
					$diffHTML = $this->taxonomy_diff( $attr, $post_id, $newLead );
					break;
				case 'meta':
					$diffHTML = $this->meta_diff( $attr, $post_id, $newLead );
					break;
				default:
					$diffHTML = apply_filters( 'rthrm_attribute_diff', $diffHTML, $attr, $post_id, $newLead );
					break;
			}
			return $diffHTML;
		}

        /**
         * @param $attr
         * @param $post_id
         * @param $newLead
         * @return string
         */
        function taxonomy_diff( $attr, $post_id, $newLead ) {
			$diffHTML = '';
			switch ( $attr->attribute_render_type ) {
//				case 'autocomplete':
//					break;
				case 'dropdown':
				case 'rating-stars':
					if ( !isset( $newLead[$attr->attribute_name] ) ) {
						$newLead[$attr->attribute_name] = array();
					}
					$newVals = $newLead[$attr->attribute_name];
					$newVals = array_unique($newVals);

					$get_post_terms = wp_get_post_terms( $post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
					if ( $get_post_terms ) {
						$post_term_slug = $get_post_terms[0]->term_id;
						$post_term_name = $get_post_terms[0]->name;
					} else {
						$post_term_slug = '';
						$post_term_name = '';
					}
					if ( !empty( $newVals ) ) {
						$newTerms = get_term_by( 'id', $newVals[0], rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
						$post_new_term_slug = $newVals[0];
						$post_new_term_name = $newTerms->name;
					} else {
						$post_new_term_slug = '';
						$post_new_term_name = '';
					}
					$diff = rthrm_text_diff( $post_term_name, $post_new_term_name );
					if ( $diff ) {
						$diffHTML .= '<tr><th style="padding: .5em;border: 0;">'.$attr->attribute_label.'</th><td>' . $diff . '</td><td></td></tr>';
					}
					break;
				case 'checklist':
					if ( !isset( $newLead[$attr->attribute_name] ) ) {
						$newLead[$attr->attribute_name] = array();
					}
					$newVals = $newLead[$attr->attribute_name];
					$newVals = array_unique( $newVals );
					$oldTermString = rthrm_post_term_to_string( $post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
					$newTermString = '';
					if(!empty($newVals)) {
						$newTermArr = array();
						foreach ( $newVals as $value ) {
							$newTerm = get_term_by( 'id', $value, rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
							$newTermArr[] = $newTerm->name;
						}
						$newTermString = implode(',', $newTermArr);
					}
					$diff = rthrm_text_diff( $oldTermString, $newTermString );
					if ( $diff ) {
						$diffHTML .= '<tr><th style="padding: .5em;border: 0;">'.$attr->attribute_label.'</th><td>' . $diff . '</td><td></td></tr>';
					}
					break;
				default:
					$diffHTML = apply_filters( 'rthrm_attribute_diff', $diffHTML, $attr, $post_id, $newLead );
					break;
			}
			return $diffHTML;
		}

        /**
         * @param $attr
         * @param $post_id
         * @param $newLead
         * @return string
         */
        function meta_diff( $attr, $post_id, $newLead ) {
			$diffHTML = '';

			$oldattr = get_post_meta( $post_id, $attr->attribute_name, true );
			if ( $oldattr != $newLead[$attr->attribute_name] ) {
				$diffHTML .= '<tr><th style="padding: .5em;border: 0;">'.$attr->attribute_label.'</th><td>' . rthrm_text_diff( $oldattr, $newLead[$attr->attribute_name] ) . '</td><td></td></tr>';
			}
			update_post_meta($post_id, $attr->attribute_name, $newLead[$attr->attribute_name]);
			return $diffHTML;
		}

        /**
         * @param $attr
         * @param $post_id
         * @param $newLead
         */
        function save_attributes( $attr, $post_id, $newLead ) {
			switch ( $attr->attribute_store_as ) {
				case 'taxonomy':
					if ( !isset( $newLead[$attr->attribute_name] ) ) {
						$newLead[$attr->attribute_name] = array();
					}
					wp_set_post_terms( $post_id, implode( ',', $newLead[$attr->attribute_name] ), rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
					break;
				case 'meta':
					update_post_meta( $post_id, $attr->attribute_name, $newLead[$attr->attribute_name] );
					break;
				default:
					do_action( 'rthrm_update_attribute', $attr, $post_id, $newLead );
					break;
			}
		}

        /**
         * @param $attr
         * @param $post_id
         * @param bool $edit
         */
        function render_attribute( $attr, $post_id, $edit = true ) {
			switch ( $attr->attribute_store_as ) {
				case 'taxonomy':
					$this->render_taxonomy( $attr, $post_id, $edit );
					break;
				case 'meta':
					$this->render_meta( $attr, $post_id, $edit );
					break;
				default:
					do_action('rthrm_render_attribute', $attr, $post_id, $edit );
					break;
			}
		}

        /**
         * @param $attr
         * @param $post_id
         * @param bool $edit
         */
        function render_taxonomy( $attr, $post_id, $edit = true ) {
			switch ( $attr->attribute_render_type ) {
//				case 'autocomplete':
//					break;
				case 'dropdown':
					$options = array();
					$terms = get_terms( rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'hide_empty' => false, 'orderby' => $attr->attribute_orderby, 'order' => 'asc' ) );
					$post_term = wp_get_post_terms($post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'fields' => 'ids' ) );
					// Default Selected Term for the attribute. can beset via settings -- later on
					$selected_term = '-11111';
					if( !empty( $post_term ) ) {
						$selected_term = $post_term[0];
					}
					foreach ($terms as $term) {
						$options[] = array(
							$term->name => $term->term_id,
							'selected' => ($term->term_id == $selected_term) ? true : false,
						);
					}
					if( $edit ) {
						$this->render_dropdown( $attr, $options );
					} else {
						$term = get_term( $selected_term, rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
						echo '<span class="rthrm_view_mode">'.$term->name.'</span>';
					}
					break;
				case 'checklist':
					$options = array();
					$terms = get_terms( rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'hide_empty' => false, 'orderby' => $attr->attribute_orderby, 'order' => 'asc' ) );
					$post_terms = wp_get_post_terms($post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'fields' => 'ids' ) );
					if ( empty( $post_terms ) ) {
						$post_terms = array();
					}
					foreach ($terms as $term) {
						$options[] = array(
							$term->name => $term->term_id,
							'checked' => ( in_array( $term->term_id, $post_terms ) ) ? true : false,
						);
					}
					if( $edit ) {
						$this->render_checklist( $attr, $options );
					} else {
						$selected_terms = array();
						foreach ($terms as $term) {
							if( in_array( $term->term_id, $post_terms ) ) {
								$selected_terms[] = $term->name;
							}
						}
						echo '<span class="rthrm_view_mode">'.  implode( ',', $selected_terms ).'</span>';
					}
					break;
				case 'rating-stars':
					$options = array();
					$terms = get_terms( rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'hide_empty' => false, 'orderby' => $attr->attribute_orderby, 'order' => 'asc' ) );
					$post_term = wp_get_post_terms($post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'fields' => 'ids' ) );
					// Default Selected Term for the attribute. can beset via settings -- later on
					$selected_term = '-11111';
					if( !empty( $post_term ) ) {
						$selected_term = $post_term[0];
					}
					foreach ($terms as $term) {
						$options[] = array(
							$term->name => $term->term_id,
							'title' => $term->name,
							'checked' => ($term->term_id == $selected_term) ? true : false,
						);
					}
					if( $edit ) {
						$this->render_rating_stars( $attr, $options );
					} else {
						$term = get_term( $selected_term, rthrm_attribute_taxonomy_name( $attr->attribute_name ) );
						echo '<span class="rthrm_view_mode">'.$term->name.'</span>';
					}
					break;
				default:
					do_action( 'rthrm_render_taxonomy', $attr, $post_id, $edit );
					break;
			}
		}

        /**
         * @param $attr
         * @param $post_id
         * @param bool $edit
         */
        function render_meta( $attr, $post_id, $edit = true ) {
			switch ( $attr->attribute_render_type ) {
				case 'dropdown':
					$options = array();
					$terms = get_terms( rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'hide_empty' => false, 'orderby' => $attr->attribute_orderby, 'order' => 'asc' ) );
					$post_term = wp_get_post_terms($post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'fields' => 'ids' ) );
					// Default Selected Term for the attribute. can beset via settings -- later on
					$selected_term = '-11111';
					if( !empty( $post_term ) ) {
						$selected_term = $post_term[0];
					}
					foreach ($terms as $term) {
						$options[] = array(
							$term->name => $term->term_id,
							'selected' => ($term->term_id == $selected_term) ? true : false,
						);
					} ?>
					<div class="large-4 small-4 columns <?php echo ( ! $edit ) ? 'rthrm_attr_border' : ''; ?>">
						<span class="prefix" title="<?php echo $attr->attribute_label; ?>"><label for="post[<?php echo $attr->attribute_name; ?>]"><?php echo $attr->attribute_label; ?></label></span>
					</div>
					<div class="large-8 mobile-large-2 columns">
						<?php if( $edit ) { $this->render_dropdown( $attr, $options ); } else { $term = get_term( $selected_term, rthrm_attribute_taxonomy_name( $attr->attribute_name ) ); echo '<span class="rthrm_view_mode">'.$term->name.'</span>'; } ?>
					</div>
					<?php break;
				case 'rating-stars':
					$options = array();
					$terms = get_terms( rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'hide_empty' => false, 'orderby' => $attr->attribute_orderby, 'order' => 'asc' ) );
					$post_term = wp_get_post_terms($post_id, rthrm_attribute_taxonomy_name( $attr->attribute_name ), array( 'fields' => 'ids' ) );
					// Default Selected Term for the attribute. can beset via settings -- later on
					$selected_term = '-11111';
					if( !empty( $post_term ) ) {
						$selected_term = $post_term[0];
					}
					foreach ($terms as $term) {
						$options[] = array(
//							$term->name => $term->term_id,
							'' => $term->term_id,
							'title' => $term->name,
							'checked' => ($term->term_id == $selected_term) ? true : false,
						);
					} ?>
					<div class="large-4 small-4 columns">
						<span class="prefix" title="<?php echo $attr->attribute_label; ?>"><label for="post[<?php echo $attr->attribute_name; ?>]"><?php echo $attr->attribute_label; ?></label></span>
					</div>
					<div class="large-8 mobile-large-2 columns rthrm_attr_border">
						<?php if( $edit ) { $this->render_rating_stars( $attr, $options ); } else { $term = get_term( $selected_term, rthrm_attribute_taxonomy_name( $attr->attribute_name ) ); echo '<div class="rthrm_attr_border rthrm_view_mode">'.$term->name.'</div>'; } ?>
					</div>
					<?php break;
				case 'date':
					$value = get_post_meta( $post_id, $attr->attribute_name, true ); ?>
					<div class="large-7 mobile-large-2 columns <?php echo ( ! $edit ) ? 'rthrm_attr_border' : ''; ?>">
						<?php if( $edit ) { $this->render_date( $attr, $value ); } else { echo '<span class="rthrm_view_mode moment-from-now">'.$value.'</span>'; } ?>
					</div>
					<?php
					break;
				case 'datetime':
					$value = get_post_meta( $post_id, $attr->attribute_name, true ); ?>

					<div class="large-7 mobile-large-2 columns <?php echo ( ! $edit ) ? 'rthrm_attr_border' : ''; ?>">
						<?php if( $edit ) { $this->render_datetime( $attr, $value ); } else { echo '<span class="rthrm_view_mode moment-from-now">'.$value.'</span>'; } ?>
					</div>
					<?php
					break;
				case 'currency':
					$value = get_post_meta( $post_id, $attr->attribute_name, true ); ?>
					<div class="large-4 mobile-large-1 columns">
						<span class="prefix" title="<?php echo $attr->attribute_label; ?>"><label for="post[<?php echo $attr->attribute_name; ?>]"><?php echo $attr->attribute_label; ?></label></span>
					</div>
					<div class="large-7 mobile-large-2 columns <?php echo ( ! $edit ) ? 'rthrm_attr_border' : ''; ?>">
						<?php if( $edit ) { $this->render_currency( $attr, $value ); } else { echo '<span class="rthrm_view_mode">'.$value.'</span>'; } ?>
					</div>
					<?php if( $edit ) { ?>
					<div class="large-1 mobile-large-1 columns">
						<span class="postfix">$</span>
					</div>
				<?php }
					break;
				case 'text':
					$value = get_post_meta( $post_id, $attr->attribute_name, true ); ?>
					<div class="large-4 small-4 columns">
						<span class="prefix" title="<?php echo $attr->attribute_label; ?>"><label for="post[<?php echo $attr->attribute_name; ?>]"><?php echo $attr->attribute_label; ?></label></span>
					</div>
					<div class="large-8 mobile-large-2 columns <?php echo ( ! $edit ) ? 'rthrm_attr_border' : ''; ?>">
						<?php if( $edit ) { $this->render_text( $attr, $value ); } else { echo '<span class="rthrm_view_mode">'.$value.'</span>'; } ?>
					</div>
					<?php break;
				default:
					do_action( 'rthrm_render_meta', $attr, $post_id, $edit );
					break;
			}
		}

        /**
         * @param $attr
         * @param $options
         */
        function render_dropdown( $attr, $options ) {
			global $rt_form;
			$args = array(
				'id' => $attr->attribute_name,
				'name' => 'post['.$attr->attribute_name.'][]',
//				'class' => array('scroll-height'),
				'rtForm_options' => $options,
			);
			echo $rt_form->get_select( $args );
		}

        /**
         * @param $attr
         * @param $options
         */
        function render_rating_stars( $attr, $options ) {
			global $rt_form;
			$args = array(
				'id' => $attr->attribute_name,
				'name' => 'post['.$attr->attribute_name.'][]',
				'class' => array('rthrm-stars'),
				'misc' => array(
					'class' => 'star',
				),
				'rtForm_options' => $options,
			);
			echo $rt_form->get_radio( $args );
		}

        /**
         * @param $attr
         * @param $options
         */
        function render_checklist( $attr, $options ) {
			global $rt_form;
			$args = array(
				'id' => $attr->attribute_name,
				'name' => 'post['.$attr->attribute_name.'][]',
				'class' => array( 'scroll-height' ),
				'rtForm_options' => $options,
			);
			echo $rt_form->get_checkbox( $args );
		}

        /**
         * @param $attr
         * @param $value
         */
        function render_date( $attr, $value ) {
			global $rt_form;
			$args = array(
				'id' => $attr->attribute_name,
				'class' => array(
					'datepicker',
					'moment-from-now',
				),
				'misc' => array(
					'placeholder' => 'Select '.$attr->attribute_label,
					'readonly' => 'readonly',
					'title' => $value,
				),
				'value' => $value,
			);
			echo $rt_form->get_textbox( $args );
			$args = array(
				'name' => 'post['.$attr->attribute_name.']',
				'value' => $value,
			);
			echo $rt_form->get_hidden( $args );
		}

        /**
         * @param $attr
         * @param $value
         */
        function render_datetime( $attr, $value ) {
			global $rt_form;
			$args = array(
				'id' => $attr->attribute_name,
				'class' => array(
					'datepicker',
					'moment-from-now',
				),
				'misc' => array(
					'placeholder' => 'Select '.$attr->attribute_label,
					'readonly' => 'readonly',
					'title' => $value,
				),
				'value' => $value,
			);
			echo $rt_form->get_textbox( $args );
			$args = array(
				'name' => 'post['.$attr->attribute_name.']',
				'value' => $value,
			);
			echo $rt_form->get_hidden( $args );
		}

        /**
         * @param $attr
         * @param $value
         */
        function render_currency( $attr, $value ) {
			global $rt_form;
			$args = array(
				'name' => 'post['.$attr->attribute_name.']',
				'value' => $value,
			);
			echo $rt_form->get_textbox( $args );
		}

        /**
         * @param $attr
         * @param $value
         */
        function render_text( $attr, $value ) {
			global $rt_form;
			$args = array(
				'name' => 'post['.$attr->attribute_name.']',
				'value' => $value,
			);
			echo $rt_form->get_textbox( $args );
		}
	}
}
