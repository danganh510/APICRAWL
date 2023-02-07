<?php
namespace Score\Utils;
use Phalcon\Mvc\User\Component;

class UserAgent extends Component {
    static $url = "https://api.whatismybrowser.com/api/v1/user_agent_parse";
    static $user_key = "3d20f8ffbb411f878fd6dc52efca17ce";

    public $hardware_type, $operating_system_name, $software_sub_type, $simple_sub_description_string, $simple_browser_string, $browser_version, $software_type, $extra_info, $operating_platform, $extra_info_table, $layout_engine_name, $operating_system_flavour_code, $detected_addons, $operating_system_flavour, $operating_system_frameworks, $browser_name_code, $simple_minor, $operating_system_version, $simple_operating_platform_string, $is_abusive, $simple_medium, $layout_engine_version, $browser_capabilities, $operating_platform_vendor_name, $operating_system, $hardware_architecture, $operating_system_version_full, $operating_platform_code, $browser_name, $operating_system_name_code, $user_agent, $simple_major, $browser_version_full, $browser;

    /**
     * @return string[]
     */
    public static function getDbKeys() {
        return array('hardware_type', 'operating_system_name', 'software_sub_type', 'simple_sub_description_string', 'simple_browser_string', 'browser_version', 'software_type', 'operating_platform', 'layout_engine_name', 'operating_system_flavour_code',  'operating_system_flavour', 'browser_name_code', 'simple_minor', 'operating_system_version', 'simple_operating_platform_string', 'is_abusive', 'simple_medium', 'layout_engine_version', 'operating_platform_vendor_name', 'operating_system', 'hardware_architecture', 'operating_system_version_full', 'operating_platform_code', 'browser_name', 'operating_system_name_code', 'user_agent', 'simple_major', 'browser_version_full', 'browser');
    }
    public static function getDbArrayKeys() {
        return array('extra_info','extra_info_table','detected_addons','operating_system_frameworks','browser_capabilities','browser_capabilities');
    }
    /**
     * Get user agent from API
     * @param string $userAgentInput
     * @return mixed|string
     */
    public static function info($userAgentInput)
    {
        $userAgentInput = trim((string)$userAgentInput);
        $userAgent = new static;

        $input_data = array(
            'user_key' => self::$user_key,
            'user_agent' => $userAgentInput
        );
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, self::$url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($input_data));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($ch);
        $dataJson = json_decode($data, true);

        if ($dataJson && isset($dataJson['parse'])) {
            foreach($dataJson['parse'] as $key => $val) {
                $userAgent->$key = $val;
            }
        }

        return $userAgent;
    }
}

