<?php


class Shipping_Envia_Colvanes_Plugin_EC
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * Absolute path to plugin lib dir
     *
     * @var string
     */
    public $lib_path;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version)
    {
        $this->file = $file;
        $this->version = $version;

        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->lib_path = $this->plugin_path . trailingslashit( 'lib' );
    }

    public function run_envia_colvanes()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( 'Shipping Envia Colvanes Woocommerce can only be called once');
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                add_action('admin_notices', function() use($e) {
                    shipping_envia_colvanes_ec_notices($e->getMessage());
                });
            }
        }
    }

    private function _run()
    {
        if (!class_exists('\EnviaColvanes\Client'))
            require_once ($this->lib_path . 'vendor/autoload.php');
        require_once ($this->includes_path . 'class-method-shipping-envia-colvanes-ec.php');
        require_once ($this->includes_path . 'class-shipping-envia-colvanes-ec.php');

        add_filter( 'plugin_action_links_' . plugin_basename( $this->file), array( $this, 'plugin_action_links' ) );
        add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_envia_colvanes_ec_add_method') );

        add_action( 'woocommerce_order_status_changed', array('Shipping_Envia_Colvanes_EC', 'generate_guide_dispath'), 20, 4 );
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=shipping_envia_colvanes_ec') . '">' . 'Configuraciones' . '</a>';
        $plugin_links[] = '<a target="_blank" href="https://shop.saulmoralespa.com/shipping-envia-colvanes-woo/">' . 'Documentación' . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function shipping_envia_colvanes_ec_add_method($methods)
    {
        $methods['shipping_envia_colvanes_ec'] = 'WC_Shipping_Method_Envia_Colvanes_EC';
        return $methods;
    }

    public function log($message)
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);
        $logger = new WC_Logger();
        $logger->add('shipping-envia-colvanes', $message);
    }
}