<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wordpress.org/plugins/treepress
 * @since      1.0.0
 *
 * @package    Treepress
 * @subpackage Treepress/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Treepress
 * @subpackage Treepress/includes
 * @author     Md Kabir Uddin <bd.kabiruddin@gmail.com>
 */
class Treepress
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Treepress_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The options that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Treepress_Loader    $options    Maintains and registers all hooks for the plugin.
     */
    public  $options ;
    /**
     * The options that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Treepress_Loader    $options    Maintains and registers all hooks for the plugin.
     */
    public  $nood ;
    /**
     * The options that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Treepress_Loader    $options    Maintains and registers all hooks for the plugin.
     */
    public  $tree ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected  $plugin_name ;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        
        if ( defined( 'TREEPRESS_VERSION' ) ) {
            $this->version = TREEPRESS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        
        $this->plugin_name = 'treepress';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Treepress_Loader. Orchestrates the hooks of the plugin.
     * - Treepress_i18n. Defines internationalization functionality.
     * - Treepress_Admin. Defines all hooks for the admin area.
     * - Treepress_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-treepress-loader.php';
        /**
         *
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-treepress-options.php';
        /**
         *
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-treepress-node.php';
        /**
         *
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-treepress-tree.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-treepress-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-treepress-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-treepress-public.php';
        $this->loader = new Treepress_Loader();
        $this->options = new Treepress_Options();
        $this->node = new Treepress_Node();
        $this->tree = new Treepress_Tree();
    }
    
    public function options()
    {
        return $this->options;
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Treepress_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Treepress_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Treepress_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
        $this->loader->add_action( 'init', $plugin_admin, 'init_post_type_member' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_member' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'save_post_member' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'treepress_gallery_save_custom_meta' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'featured_image_save_postdata' );
        $this->loader->add_action( 'wp_head', $plugin_admin, 'treepress_featured_image' );
        $this->loader->add_filter( 'parent_file', $plugin_admin, 'set_current_menu' );
        $this->loader->add_filter( 'manage_edit-member_columns', $plugin_admin, 'member_columns' );
        $this->loader->add_filter( 'manage_edit-member_sortable_columns', $plugin_admin, 'member_sortable_columns' );
        $this->loader->add_action(
            'manage_member_posts_custom_column',
            $plugin_admin,
            'member_posts_born_column',
            10,
            2
        );
        $this->loader->add_action(
            'pre_get_posts',
            $plugin_admin,
            'member_born_orderby',
            10,
            2
        );
        $this->loader->add_filter( 'enter_title_here', $plugin_admin, 'member_change_title_text' );
        $this->loader->add_filter( 'manage_edit-family_columns', $plugin_admin, 'add_family_columns' );
        $this->loader->add_filter(
            'manage_family_custom_column',
            $plugin_admin,
            'add_family_column_content',
            10,
            3
        );
        $this->loader->add_filter( 'plugin_action_links_treepress/treepress.php', $plugin_admin, 'treepress_paypal_link' );
        // Add the fields to the "presenters" taxonomy, using our callback function
        $this->loader->add_action(
            'family_edit_form_fields',
            $plugin_admin,
            'family_custom_fields',
            10,
            2
        );
        // Save the changes made on the "presenters" taxonomy, using our callback function
        $this->loader->add_action(
            'edited_family',
            $plugin_admin,
            'save_family_custom_fields',
            10,
            2
        );
        $this->loader->add_action( 'create_family', $plugin_admin, 'create_family_free' );
        $this->loader->add_action( 'rest_api_init', $plugin_admin, 'treepress_connect' );
        $this->loader->add_action( 'rest_api_init', $plugin_admin, 'treepress_export' );
        $this->loader->add_action( 'rest_api_init', $plugin_admin, 'treepress_import' );
        $this->loader->add_action( 'wp_ajax_delete_fact_key', $plugin_admin, 'delete_fact_key' );
        $this->loader->add_action( 'wp_ajax_nopriv_delete_fact_key', $plugin_admin, 'delete_fact_key' );
        $this->loader->add_action( 'wp_ajax_add_new_fact', $plugin_admin, 'add_new_fact' );
        $this->loader->add_action( 'wp_ajax_nopriv_add_new_fact', $plugin_admin, 'add_new_fact' );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Treepress_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_filter( 'the_content', $plugin_public, 'treepress_family_list_insert' );
        $this->loader->add_filter( 'the_content', $plugin_public, 'treepress_insert' );
        $this->loader->add_filter( 'the_content', $plugin_public, 'bio_data_insert_in_single_page' );
        add_shortcode( 'family-tree', array( $plugin_public, 'treepress_treepress_shortcode' ) );
        add_shortcode( 'family-members', array( $plugin_public, 'treepress_family_members_shortcode' ) );
        add_shortcode( 'members', array( $plugin_public, 'treepress_members_shortcode' ) );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Treepress_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}