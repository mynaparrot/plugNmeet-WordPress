<?php

/**
 * To list rooms.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Plugnmeet_Rooms_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Room', 'plugnmeet' ),
			'plural'   => __( 'Rooms', 'plugnmeet' ),
			'ajax'     => false
		] );
	}

	public static function get_rooms( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}plugnmeet_rooms";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE room_title LIKE \'%' . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . '%\'';
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= ' ORDER BY id DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}plugnmeet_rooms";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE room_title LIKE \'%' . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . '%\'';
		}

		return $wpdb->get_var( $sql );
	}

	public function no_items() {
		_e( 'No rooms found.', 'plugnmeet' );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'moderator_pass':
			case 'attendee_pass':
			case 'room_id':
				return $item[ $column_name ];
			case 'published':
				return $item[ $column_name ] ? __( 'Published', 'plugnmeet' ) : '<span style="color: red;">' . __( 'Unpublished', 'plugnmeet' ) . '</span>';
			case 'shortcode':
				return '[plugnmeet_room_view id="' . esc_attr( $item['id'] ) . '"]';
			default:
				return '';
		}
	}

	function get_columns() {
		$columns = [
			'id'             => __( 'ID', 'plugnmeet' ),
			'room_title'     => __( 'Room Title', 'plugnmeet' ),
			'room_id'        => __( 'Room Id', 'plugnmeet' ),
			'moderator_pass' => __( 'Moderator Password', 'plugnmeet' ),
			'attendee_pass'  => __( 'Attendee Password', 'plugnmeet' ),
			'published'      => __( 'Status', 'plugnmeet' ),
			'shortcode'      => __( 'Shortcode', 'plugnmeet' ),
		];

		return $columns;
	}

	function column_room_title( $item ) {
		$title   = sprintf( '<strong><a class="row-title" href="admin.php?page=plugnmeet&task=edit&id=%s">%s</a></strong>', $item['id'], $item['room_title'] );
		$actions = [
			'edit'       => sprintf( '<a href="admin.php?page=plugnmeet&task=edit&id=%s">%s</a>', absint( $item['id'] ), __( 'Edit', 'plugnmeet' ) ),
			'artifacts'  => sprintf( '<a href="admin.php?page=plugnmeet-artifacts&room_id=%s">%s</a>', esc_attr( $item['room_id'] ), __( 'Artifacts', 'plugnmeet' ) ),
			'recordings' => sprintf( '<a href="admin.php?page=plugnmeet-recordings&room_id=%s">%s</a>', esc_attr( $item['room_id'] ), __( 'Recordings', 'plugnmeet' ) ),
			'delete'     => sprintf( '<a href="#" class="pnm-delete-room" data-id="%s">%s</a>', absint( $item['id'] ), __( 'Delete', 'plugnmeet' ) )
		];

		return $title . $this->row_actions( $actions );
	}

	public function get_sortable_columns() {
		return [
			'room_title' => array( 'room_title', true ),
			'id'         => array( 'id', true ),
		];
	}

	protected function get_primary_column_name() {
		return 'room_title';
	}

	public function get_bulk_actions() {
		return [];
	}

	public function prepare_items() {
		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns(),
			$this->get_primary_column_name()
		];

		$per_page     = $this->get_items_per_page( 'rooms_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$this->items = self::get_rooms( $per_page, $current_page );
	}
}
