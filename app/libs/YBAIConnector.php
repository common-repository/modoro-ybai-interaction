<?php

class YBAIConnector
{
    protected $url;
    protected $access_key;
    protected $secret_key;
    protected $access_token;
    protected $action = array(
        'connect' => '/v1/connect',
        'order' => '/v1/orders/store',
        'order_update' => '/v1/orders/{key}/update',
        'order_status' => '/v1/orders/{key}/status',
        'synchronize' => '/v1/products/imports',
        'import' => '/v1/products/import',
    );

    function __construct()
    {
        $this->url = YBAI_API;
        $this->access_key = get_option('ybai_access_key');
        $this->secret_key = get_option('ybai_secret_key');
        $this->access_token = get_option('ybai_access_token');
    }

    function request($method, $action, $data = false)
    {
        $http_header = array(
            'Content-type' => 'application/json',
            'charset' => 'UTF-8',
            'Authorization' => 'Bearer ' . $this->access_token
        );
        $url = $this->url . $action;
        $result = $this->callback($method, $url, $http_header, $data);

        $this->errors_log($url, [$result['body']],$result['body']['message'], $result['code']);

        if ($result['code'] == 200) {
            return array(
                'success' => true,
                'message' => $result['body']['message'],
                'data' => $result['body']['data']??''
            );
        } elseif ($result['code'] == 499 || $result['code'] == 498) {
            $retry_count = 3;
            while ($retry_count > 0) {
                $retry_count--;
                $check = $this->connect();
                if ($check === true) {
                    $result = $this->callback($method, $url, $http_header, $data);
                    if ($result['code'] == 200) {
                        return array(
                            'success' => true,
                            'message' => $result['body']['message'],
                            'data' => $result['body']['data']
                        );
                    }
                }
                sleep(1);
            }
        }

        return array(
            'success' => false,
            'message' => $result['body']['message'],
        );
    }


    function callback($method, $url, $http_header, $data = false)
    {
        try {
            $args = ['timeout' => '5', 'redirection' => '5', 'httpversion' => '1.0'];
            $args['method'] = $method;
            $args['body'] = $data;
            $args['headers'] = $http_header;

            $response = wp_remote_request($url, $args);
            $code = wp_remote_retrieve_response_code($response);

            $body = wp_remote_retrieve_body($response);
            if (is_string($body)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
            }

            $headers = wp_remote_retrieve_headers($response);
            if (is_string($headers)) {
                $headers = json_decode(wp_remote_retrieve_headers($response), true);
            }

            return array(
                'header' => $headers,
                'code' => $code,
                'body' => $body,
            );

        } catch (Exception $e) {
            return array(
                'header' => null,
                'code' => $e->getCode(),
                'body' => array(
                    'message' => $e->getMessage()
                ),
            );
        }
    }

    function register($user)
    {
        return $this->request('POST',$this->action['register'], $user);
    }

    function login($user)
    {
        return $this->request('POST',$this->action['login'], $user);
    }

    function order($order)
    {
        return $this->request('POST',$this->action['order'], json_encode($order));
    }

    function update_order($id,$order)
    {
        $action = str_ireplace('{key}',$id,$this->action['order_update']);
        return $this->request('PUT',$action, json_encode($order));
    }


    function change_order_status($id, $status)
    {
        $action = str_ireplace('{key}',$id,$this->action['order_status']);
        return $this->request('PUT',$action, json_encode(['status_id' => $status]));
    }

    function synchronize($data)
    {
        return $this->request('POST',$this->action['synchronize'], json_encode($data));
    }

    function import($data)
    {
        return $this->request('POST',$this->action['import'], json_encode($data));
    }

    function connect()
    {
        $http_header = array();
        $url = $this->url . $this->action['connect'];
        $form_data = array(
            'access_key' => $this->access_key,
            'secret_key' => $this->secret_key
        );

        $result = $this->callback('POST', $url, $http_header, $form_data);

        if ($result['code'] == 200) {
            $this->access_token = $result['body']['data']['access_token'];
            update_option('ybai_access_token', $result['body']['data']['access_token']);
        }
        return $result['body'];
    }

    function _unicode_decode($str)
    {
        $str = str_ireplace("\\", "", $str);
        $str = str_ireplace("u0001", "?", $str);
        $str = str_ireplace("u0002", "?", $str);
        $str = str_ireplace("u0003", "?", $str);
        $str = str_ireplace("u0004", "?", $str);
        $str = str_ireplace("u0005", "?", $str);
        $str = str_ireplace("u0006", "?", $str);
        $str = str_ireplace("u0007", "•", $str);
        $str = str_ireplace("u0008", "?", $str);
        $str = str_ireplace("u0009", "?", $str);
        $str = str_ireplace("u000A", "?", $str);
        $str = str_ireplace("u000B", "?", $str);
        $str = str_ireplace("u000C", "?", $str);
        $str = str_ireplace("u000D", "?", $str);
        $str = str_ireplace("u000E", "?", $str);
        $str = str_ireplace("u000F", "¤", $str);
        $str = str_ireplace("u0010", "?", $str);
        $str = str_ireplace("u0011", "?", $str);
        $str = str_ireplace("u0012", "?", $str);
        $str = str_ireplace("u0013", "?", $str);
        $str = str_ireplace("u0014", "¶", $str);
        $str = str_ireplace("u0015", "§", $str);
        $str = str_ireplace("u0016", "?", $str);
        $str = str_ireplace("u0017", "?", $str);
        $str = str_ireplace("u0018", "?", $str);
        $str = str_ireplace("u0019", "?", $str);
        $str = str_ireplace("u001A", "?", $str);
        $str = str_ireplace("u001B", "?", $str);
        $str = str_ireplace("u001C", "?", $str);
        $str = str_ireplace("u001D", "?", $str);
        $str = str_ireplace("u001E", "?", $str);
        $str = str_ireplace("u001F", "?", $str);
        $str = str_ireplace("u0020", " ", $str);
        $str = str_ireplace("u0021", "!", $str);
        $str = str_ireplace("u0022", "\"", $str);
        $str = str_ireplace("u0023", "#", $str);
        $str = str_ireplace("u0024", "$", $str);
        $str = str_ireplace("u0025", "%", $str);
        $str = str_ireplace("u0026", "&", $str);
        $str = str_ireplace("u0027", "'", $str);
        $str = str_ireplace("u0028", "(", $str);
        $str = str_ireplace("u0029", ")", $str);
        $str = str_ireplace("u002A", "*", $str);
        $str = str_ireplace("u002B", "+", $str);
        $str = str_ireplace("u002C", ",", $str);
        $str = str_ireplace("u002D", "-", $str);
        $str = str_ireplace("u002E", ".", $str);
        $str = str_ireplace("u2026", "…", $str);
        $str = str_ireplace("u002F", "/", $str);
        $str = str_ireplace("u0030", "0", $str);
        $str = str_ireplace("u0031", "1", $str);
        $str = str_ireplace("u0032", "2", $str);
        $str = str_ireplace("u0033", "3", $str);
        $str = str_ireplace("u0034", "4", $str);
        $str = str_ireplace("u0035", "5", $str);
        $str = str_ireplace("u0036", "6", $str);
        $str = str_ireplace("u0037", "7", $str);
        $str = str_ireplace("u0038", "8", $str);
        $str = str_ireplace("u0039", "9", $str);
        $str = str_ireplace("u003A", ":", $str);
        $str = str_ireplace("u003B", ";", $str);
        $str = str_ireplace("u003C", "<", $str);
        $str = str_ireplace("u003D", "=", $str);
        $str = str_ireplace("u003E", ">", $str);
        $str = str_ireplace("u2264", "=", $str);
        $str = str_ireplace("u2265", "=", $str);
        $str = str_ireplace("u003F", "?", $str);
        $str = str_ireplace("u0040", "@", $str);
        $str = str_ireplace("u0041", "A", $str);
        $str = str_ireplace("u0042", "B", $str);
        $str = str_ireplace("u0043", "C", $str);
        $str = str_ireplace("u0044", "D", $str);
        $str = str_ireplace("u0045", "E", $str);
        $str = str_ireplace("u0046", "F", $str);
        $str = str_ireplace("u0047", "G", $str);
        $str = str_ireplace("u0048", "H", $str);
        $str = str_ireplace("u0049", "I", $str);
        $str = str_ireplace("u004A", "J", $str);
        $str = str_ireplace("u004B", "K", $str);
        $str = str_ireplace("u004C", "L", $str);
        $str = str_ireplace("u004D", "M", $str);
        $str = str_ireplace("u004E", "N", $str);
        $str = str_ireplace("u004F", "O", $str);
        $str = str_ireplace("u0050", "P", $str);
        $str = str_ireplace("u0051", "Q", $str);
        $str = str_ireplace("u0052", "R", $str);
        $str = str_ireplace("u0053", "S", $str);
        $str = str_ireplace("u0054", "T", $str);
        $str = str_ireplace("u0055", "U", $str);
        $str = str_ireplace("u0056", "V", $str);
        $str = str_ireplace("u0057", "W", $str);
        $str = str_ireplace("u0058", "X", $str);
        $str = str_ireplace("u0059", "Y", $str);
        $str = str_ireplace("u005A", "Z", $str);
        $str = str_ireplace("u005B", "[", $str);
        $str = str_ireplace("u005C", "\\", $str);
        $str = str_ireplace("u005D", "]", $str);
        $str = str_ireplace("u005E", "^", $str);
        $str = str_ireplace("u005F", "_", $str);
        $str = str_ireplace("u0060", "`", $str);
        $str = str_ireplace("u0061", "a", $str);
        $str = str_ireplace("u0062", "b", $str);
        $str = str_ireplace("u0063", "c", $str);
        $str = str_ireplace("u0064", "d", $str);
        $str = str_ireplace("u0065", "e", $str);
        $str = str_ireplace("u0066", "f", $str);
        $str = str_ireplace("u0067", "g", $str);
        $str = str_ireplace("u0068", "h", $str);
        $str = str_ireplace("u0069", "i", $str);
        $str = str_ireplace("u006A", "j", $str);
        $str = str_ireplace("u006B", "k", $str);
        $str = str_ireplace("u006C", "l", $str);
        $str = str_ireplace("u006D", "m", $str);
        $str = str_ireplace("u006E", "n", $str);
        $str = str_ireplace("u006F", "o", $str);
        $str = str_ireplace("u0070", "p", $str);
        $str = str_ireplace("u0071", "q", $str);
        $str = str_ireplace("u0072", "r", $str);
        $str = str_ireplace("u0073", "s", $str);
        $str = str_ireplace("u0074", "t", $str);
        $str = str_ireplace("u0075", "u", $str);
        $str = str_ireplace("u0076", "v", $str);
        $str = str_ireplace("u0077", "w", $str);
        $str = str_ireplace("u0078", "x", $str);
        $str = str_ireplace("u0079", "y", $str);
        $str = str_ireplace("u007A", "z", $str);
        $str = str_ireplace("u007B", "{", $str);
        $str = str_ireplace("u007C", "|", $str);
        $str = str_ireplace("u007D", "}", $str);
        $str = str_ireplace("u02DC", "˜", $str);
        $str = str_ireplace("u007E", "~", $str);
        $str = str_ireplace("u007F", "", $str);
        $str = str_ireplace("u00A2", "¢", $str);
        $str = str_ireplace("u00A3", "£", $str);
        $str = str_ireplace("u00A4", "¤", $str);
        $str = str_ireplace("u20AC", "€", $str);
        $str = str_ireplace("u00A5", "¥", $str);
        $str = str_ireplace("u0026quot;", "\"", $str);
        $str = str_ireplace("u0026gt;", ">", $str);
        $str = str_ireplace("u0026lt;", ">", $str);
        return $str;
    }

    function errors_log($url, $data, $error, $http_status = 'unknow')
    {
        $path_name = str_ireplace('{date}',date("Y-m-d"),YBAI_ERROR_LOG);
        $handle = fopen($path_name, 'a');
        $time = date("Y-m-d H:i:s");
        $error_data = '[' . $time . ']' . $http_status . '  |  ' . $url . '-' . json_encode($data) . '  |  ' . $error . "\n";

        fwrite($handle, $error_data);
        fclose($handle);
    }
}
