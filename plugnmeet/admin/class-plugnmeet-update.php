<?php
/**
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class Plugnmeet_Update
{
    protected $current_version;
    protected $plugin_slug;
    protected $plugin_dir;


    public function __construct($current_version, $plugin_slug)
    {
        $this->current_version = $current_version;
        $this->plugin_dir = $plugin_slug;
        $this->plugin_slug = $plugin_slug . ".php";

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'check_info'), 10, 3);
    }

    public function check_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $res = $this->fetchInfoFromGithub();
        if (!$res->status) {
            return $transient;
        }

        // If a newer version is available, add the update
        if (version_compare($this->current_version, $res->version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->new_version = $res->version;
            $obj->url = $res->url;
            $obj->package = $res->package;

            $transient->response["$this->plugin_dir/$this->plugin_slug"] = $obj;
        }

        return $transient;
    }

    public function check_info($obj, $action, $arg)
    {
        if (($action == 'query_plugins' || $action == 'plugin_information') &&
            isset($arg->slug) && $arg->slug === $this->plugin_slug) {
            $res = $this->fetchInfoFromGithub();
            if (!$res->status) {
                return $obj;
            }

            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->name = "Plug-N-Meet WordPress";
            $obj->new_version = $res->version;
            $obj->url = $res->url;
            $obj->package = $res->package;
            $obj->download_link = $res->package;
            $obj->sections = array(
                'changelog' => $res->body
            );
        }

        return $obj;
    }

    private function fetchInfoFromGithub()
    {
        $header = array(
            "Accept: application/vnd.github.v3+json"
        );
        $output = new stdClass();
        $output->status = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/mynaparrot/plugNmeet-WordPress/releases");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);

        if ($response) {
            $new = json_decode($response)[0];

            $output->status = true;
            $output->version = str_replace("v", "", $new->tag_name);
            $output->package = $new->assets[0]->browser_download_url;
            $output->url = $new->html_url;
            $output->body = $new->body;
        }

        return $output;
    }
}
