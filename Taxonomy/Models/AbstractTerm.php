<?php
namespace AsasVirtuaisWP\Taxonomy\Models;

abstract class AbstractTerm {

    protected \WP_Term $wp_term;

    public function __construct( \WP_Term $wp_term ) {
        $this->wp_term = $wp_term;
    }

    // Getters
    public function get_id() {
        return $this->wp_term->id;
    }
    public function get_name() {
        return $this->wp_term->name;
    }
    public function get_slug() {
        return $this->wp_term->slug;
    }
    public function get_parent() {
        return $this->wp_term->parent;
    }
    public function get_description() {
        return $this->wp_term->parent;
    }
    public function get_parent_slug() {
        $parent = $this->get_parent();
        if( ! $parent ) {
            throw new \Exception('No parent for Term: ' . $this->get_slug() );
        }
        return instance_from_id( $parent );
    }

    // Instance
    public static function instance_from_id( $id ) {
        $taxonomy = static::get_taxonomy();
        $wp_term = get_term( $id, $taxonomy );
        $wp_term = static::validate_get_term_result( $wp_term, $id );
        return new static( $wp_term );
    }
    public static function instance_from_slug( string $slug ) {
        $taxonomy = static::get_taxonomy();
        $wp_term = get_term_by( 'slug', $slug, $taxonomy );
        $wp_term = static::validate_get_term_result( $wp_term, $slug );
        return new static( $wp_term );
    }
    protected static function validate_get_term_result( $result, $identifier ) {
        if( is_array( $result ) ) {
            $result = $result[0];
        }
        if( ! $result ) {
            throw new \Exception( "Term $identifier of taxonomy $taxonomy not found" );
        }
        return $result;
    }

    // Import
    static array  $essential_import_args = [ 'name', 'slug' ];
    protected static function import( mixed $data ) {
        // Validate existance of necessary data
        static::validate_import_data( $data );

        $slug = $data['slug'];

        // Check for existing index
        $existing_index = get_term_by( 'slug', $slug, static::get_taxonomy() );

        if( $existing_index ) {
            av_import_admin_notice( "Existing index found for Term $slug, you may delete it and try again." );
            $term_id = $existing_index->term_id;
        } else {
            $term_id = static::insert_from_import_data( $data, false );
        }
    }
    protected static function validate_import_data( mixed $data ) {
        foreach( $essential_import_args as $arg ) {
            if( ! isset( $data[$arg] ) ) {
                throw new \Exception("Empty $arg in " . static::$type . " import");
            }
            if( empty( $data[$arg] ) ) {
                throw new \Exception("Empty $arg in " . static::$type . " import");
            }
        }
    }
    protected static function insert_from_import_data( mixed $data, bool $validate = true ) {

        if( $validate ) {
            static::validate_import_data( $data );
        }

        $name = $data['name'];
        $slug = $data['slug'];

        // Insert object
        $term_insert = wp_insert_term( $name, static::get_taxonomy(), [
            // 'alias_of'
            'slug' => $slug,
            'description' => $data['description'] ?? '',
            'parent' => $data['parent'] ?? 0
        ] );

        // Validate inserted object
        if( is_wp_error( $term_insert ) ) {
            throw new \Exception("Failed to insert term $slug.\n" . gsg_wp_error_message( $wp_error ) );
        }

        // Get the ID
        $term_id = $term_insert['term_id'];

        av_import_admin_notice( "Term $slug added with ID: $term_id" );

        return $term_id;
    }

    // Export
    protected function export_array() {
        return [
            'name' => $this->get_name(),
            'slug' => $this->get_slug(),
            'parent' => $this->get_parent(),
            'description' => $this->get_description(),
        ];
    }

    // Abstract
    abstract static function get_taxonomy();


}
