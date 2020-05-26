<?php

/**
 * Access terms associated with a taxonomy
 */
class APPMAKER_WP_WC_REST_BACKEND_Terms_Controller extends APPMAKER_WP_WC_REST_Controller {

	protected $taxonomy = 'category';

	/**
	 * @param string $taxonomy
	 */
	public function __construct() {
		parent::__construct();

		$this->rest_base = 'backend/category';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );
		register_rest_route( $this->namespace, '/backend/attributes', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_attributes_list' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();
		$taxonomy     = get_taxonomy( $this->taxonomy );

		$query_params['context']['default'] = 'view';

		$query_params['exclude'] = array(
			'description'       => __( 'Ensure result set excludes specific ids.' ),
			'type'              => 'array',
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$query_params['include'] = array(
			'description'       => __( 'Limit result set to specific ids.' ),
			'type'              => 'array',
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		if ( ! $taxonomy->hierarchical ) {
			$query_params['offset'] = array(
				'description'       => __( 'Offset the result set by a specific number of items.' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			);
		}
		$query_params['order']      = array(
			'description'       => __( 'Order sort attribute ascending or descending.' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'default'           => 'asc',
			'enum'              => array(
				'asc',
				'desc',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['orderby']    = array(
			'description'       => __( 'Sort collection by resource attribute.' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'default'           => 'name',
			'enum'              => array(
				'id',
				'include',
				'name',
				'slug',
				'term_group',
				'description',
				'count',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['hide_empty'] = array(
			'description'       => __( 'Whether to hide resources not assigned to any posts.' ),
			'type'              => 'boolean',
			'default'           => false,
			'validate_callback' => 'rest_validate_request_arg',
		);
		if ( $taxonomy->hierarchical ) {
			$query_params['parent'] = array(
				'description'       => __( 'Limit result set to resources assigned to a specific parent.' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			);
		}
		$query_params['post'] = array(
			'description'       => __( 'Limit result set to resources assigned to a specific post.' ),
			'type'              => 'integer',
			'default'           => null,
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['slug'] = array(
			'description'       => __( 'Limit result set to resources with a specific slug.' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 0,
			'minimum'           => 0,
			'maximum'           => 1000,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}

	// Get attributes list

	public function get_attributes_list(){

		$response = array();
		$data = wc_get_attribute_taxonomies();
		if(!empty($data)){

			foreach($data as $key){
				$attribute_label = isset($key->attribute_label)?$key->attribute_label:'';
				$attribute_id =   wc_attribute_taxonomy_name($key->attribute_label);
				$response[] =  array(
					'id'    => $attribute_id,
					'label' => $attribute_label,
		
				);
			}	

		}
			
		return $response;		

	}

	/**
	 * Get terms associated with a taxonomy
	 *
	 * @param WP_REST_Request $request Full details about the request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$prepared_args = array(
			'exclude'    => $request['exclude'],
			'include'    => $request['include'],
			'order'      => $request['order'],
			'orderby'    => $request['orderby'],
			'post'       => $request['post'],
			'hide_empty' => $request['hide_empty'],
			'number'     => $request['per_page'],
			'search'     => $request['search'],
			'slug'       => $request['slug'],
		);

		if ( ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}

		if ( $request['taxonomy'] ) {
			$this->taxonomy = $request['taxonomy'];
		}

		$taxonomy_obj = get_taxonomy( $this->taxonomy );

		if ( $taxonomy_obj->hierarchical && isset( $request['parent'] ) ) {
			if ( 0 === $request['parent'] ) {
				// Only query top-level terms.
				$prepared_args['parent'] = 0;
			} else {
				if ( $request['parent'] ) {
					$prepared_args['parent'] = $request['parent'];
				}
			}
		}

		/**
		 * Filter the query arguments, before passing them to `get_terms()`.
		 *
		 * Enables adding extra arguments or setting defaults for a terms
		 * collection request.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_terms/
		 *
		 * @param array $prepared_args Array of arguments to be
		 *                                       passed to get_terms.
		 * @param WP_REST_Request $request The current request.
		 */
		$prepared_args = apply_filters( "rest_{$this->taxonomy}_query", $prepared_args, $request );

		// Can we use the cached call?
		$use_cache = ! empty( $prepared_args['post'] )
		             && empty( $prepared_args['include'] )
		             && empty( $prepared_args['exclude'] )
		             && empty( $prepared_args['hide_empty'] )
		             && empty( $prepared_args['search'] )
		             && empty( $prepared_args['slug'] );

		if ( ! empty( $prepared_args['post'] ) ) {
			$query_result = $this->get_terms_for_post( $prepared_args );
			$total_terms  = $this->total_terms;
		} else {
			$query_result = get_terms( $this->taxonomy, $prepared_args );

			$count_args = $prepared_args;
			unset( $count_args['number'] );
			unset( $count_args['offset'] );
			$total_terms = wp_count_terms( $this->taxonomy, $count_args );

			// Ensure we don't return results when offset is out of bounds
			// see https://core.trac.wordpress.org/ticket/35935
			if ( $prepared_args['offset'] >= $total_terms ) {
				$query_result = array();
			}

			// wp_count_terms can return a falsy value when the term has no children
			if ( ! $total_terms ) {
				$total_terms = 0;
			}
		}
		$response = array();
		foreach ( $query_result as $term ) {
			$data       = $this->prepare_item_for_response( $term, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		// Store pagation values for headers then unset for count query.
		$per_page = (int) $prepared_args['number'];
		if ( $per_page != 0 ) {
			$page = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );
			$max_pages = ceil( $total_terms / $per_page );
		} else {
			$page = 0;
			$max_pages = ceil( 1 );
		}
		$response->header( 'X-WP-Total', (int) $total_terms );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( '/' . $this->namespace . '/' . $this->rest_base ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Get the terms attached to a post.
	 *
	 * This is an alternative to `get_terms()` that uses `get_the_terms()`
	 * instead, which hits the object cache. There are a few things not
	 * supported, notably `include`, `exclude`. In `self::get_items()` these
	 * are instead treated as a full query.
	 *
	 * @param array $prepared_args Arguments for `get_terms()`
	 *
	 * @return array List of term objects. (Total count in `$this->total_terms`)
	 */
	protected function get_terms_for_post( $prepared_args ) {
		$query_result = get_the_terms( $prepared_args['post'], $this->taxonomy );
		if ( empty( $query_result ) ) {
			$this->total_terms = 0;

			return array();
		}

		// get_items() verifies that we don't have `include` set, and default
		// ordering is by `name`
		if ( ! in_array( $prepared_args['orderby'], array( 'name', 'none', 'include' ) ) ) {
			switch ( $prepared_args['orderby'] ) {
				case 'id':
					$this->sort_column = 'term_id';
					break;

				case 'slug':
				case 'term_group':
				case 'description':
				case 'count':
					$this->sort_column = $prepared_args['orderby'];
					break;
			}
			usort( $query_result, array( $this, 'compare_terms' ) );
		}
		if ( strtolower( $prepared_args['order'] ) !== 'asc' ) {
			$query_result = array_reverse( $query_result );
		}

		// Pagination
		$this->total_terms = count( $query_result );
		$query_result      = array_slice( $query_result, $prepared_args['offset'], $prepared_args['number'] );

		return $query_result;
	}

	/**
	 * Prepare a single term output for response
	 *
	 * @param obj $item Term object
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $item, $request ) {

		$data = array(
			'id'    => (int) $item->term_id,
			'label' => $item->name,

		);

		return $data;
	}

	/**
	 * Get a single term from a taxonomy
	 *
	 * @param WP_REST_Request $request Full details about the request
	 *
	 * @return WP_REST_Request|WP_Error
	 */
	public function get_item( $request ) {

		$term = get_term( (int) $request['id'], $this->taxonomy );
		if ( ! $term || $term->taxonomy !== $this->taxonomy ) {
			return new WP_Error( 'rest_term_invalid', __( "Resource doesn't exist." ), array( 'status' => 404 ) );
		}
		if ( is_wp_error( $term ) ) {
			return $term;
		}

		$response = $this->prepare_item_for_response( $term, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Get the Term's schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema   = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'post_tag' === $this->taxonomy ? 'tag' : $this->taxonomy,
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'Unique identifier for the resource.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'count'       => array(
					'description' => __( 'Number of published posts for the resource.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'HTML description of the resource.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'wp_filter_post_kses',
					),
				),
				'link'        => array(
					'description' => __( 'URL to the resource.' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'name'        => array(
					'description' => __( 'HTML title for the resource.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'slug'        => array(
					'description' => __( 'An alphanumeric identifier for the resource unique to its type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_title',
					),
				),
				'taxonomy'    => array(
					'description' => __( 'Type attribution for the resource.' ),
					'type'        => 'string',
					'enum'        => array_keys( get_taxonomies() ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
			),
		);
		$taxonomy = get_taxonomy( $this->taxonomy );
		if ( $taxonomy->hierarchical ) {
			$schema['properties']['parent'] = array(
				'description' => __( 'The id for the parent of the resource.' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			);
		}

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Comparison function for sorting terms by a column.
	 *
	 * Uses `$this->sort_column` to determine field to sort by.
	 *
	 * @param stdClass $left Term object.
	 * @param stdClass $right Term object.
	 *
	 * @return int <0 if left is higher "priority" than right, 0 if equal, >0 if right is higher "priority" than left.
	 */
	protected function compare_terms( $left, $right ) {
		$col       = $this->sort_column;
		$left_val  = $left->$col;
		$right_val = $right->$col;

		if ( is_int( $left_val ) && is_int( $right_val ) ) {
			return $left_val - $right_val;
		}

		return strcmp( $left_val, $right_val );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object $term Term object.
	 *
	 * @return array Links for the given term.
	 */
	protected function prepare_links( $term ) {
		$base  = '/' . $this->namespace . '/' . $this->rest_base;
		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $term->term_id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
			'about'      => array(
				'href' => rest_url( sprintf( 'wp/v1/taxonomies/%s', $this->taxonomy ) ),
			),
		);

		if ( $term->parent ) {
			$parent_term = get_term( (int) $term->parent, $term->taxonomy );
			if ( $parent_term ) {
				$links['up'] = array(
					'href'       => rest_url( trailingslashit( $base ) . $parent_term->term_id ),
					'embeddable' => true,
				);
			}
		}

		$taxonomy_obj = get_taxonomy( $term->taxonomy );
		if ( empty( $taxonomy_obj->object_type ) ) {
			return $links;
		}

		$post_type_links = array();
		foreach ( $taxonomy_obj->object_type as $type ) {
			$post_type_object = get_post_type_object( $type );
			if ( empty( $post_type_object->show_in_rest ) ) {
				continue;
			}
			$rest_base         = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
			$post_type_links[] = array(
				'href' => add_query_arg( $this->rest_base, $term->term_id, rest_url( sprintf( 'wp/v1/%s', $rest_base ) ) ),
			);
		}
		if ( ! empty( $post_type_links ) ) {
			$links['https://api.w.org/post_type'] = $post_type_links;
		}

		return $links;
	}

	/**
	 * Check that the taxonomy is valid
	 *
	 * @param string
	 *
	 * @return WP_Error|boolean
	 */
	protected function check_is_taxonomy_allowed( $taxonomy ) {
		$taxonomy_obj = get_taxonomy( $taxonomy );
		if ( $taxonomy_obj && ! empty( $taxonomy_obj->show_in_rest ) ) {
			return true;
		}

		return false;
	}
}
