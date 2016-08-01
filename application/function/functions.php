<?php

function slash_item($item) {
    $config_item = getConfig($item);
    if (trim($config_item) === '') {
        return '';
    }

    return rtrim($config_item, '/') . '/';
}

function filter($content) {
    if (!get_magic_quotes_gpc()) {
        return addslashes($content);
    } else {
        return $content;
    }
}

//对字符串等进行过滤
function filterStr($arr) {
    if (!isset($arr)) {
        return null;
    }

    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = filter(stripSQLChars(stripHTML(trim($v), true)));
        }
    } else {
        $arr = filter(stripSQLChars(stripHTML(trim($arr), true)));
    }

    return $arr;
}

function stripHTML($content, $xss = true) {
    $search = array(
        "@<script(.*?)</script>@is",
        "@<iframe(.*?)</iframe>@is",
        "@<style(.*?)</style>@is",
        "@<(.*?)>@is"
    );

    $content = preg_replace($search, '', $content);

    if ($xss) {
        $ra1 = array(
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base'
        );

        $ra2 = array(
            'onabort',
            'onactivate',
            'onafterprint',
            'onafterupdate',
            'onbeforeactivate',
            'onbeforecopy',
            'onbeforecut',
            'onbeforedeactivate',
            'onbeforeeditfocus',
            'onbeforepaste',
            'onbeforeprint',
            'onbeforeunload',
            'onbeforeupdate',
            'onblur',
            'onbounce',
            'oncellchange',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncontrolselect',
            'oncopy',
            'oncut',
            'ondataavailable',
            'ondatasetchanged',
            'ondatasetcomplete',
            'ondblclick',
            'ondeactivate',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'onerror',
            'onerrorupdate',
            'onfilterchange',
            'onfinish',
            'onfocus',
            'onfocusin',
            'onfocusout',
            'onhelp',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onlayoutcomplete',
            'onload',
            'onlosecapture',
            'onmousedown',
            'onmouseenter',
            'onmouseleave',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onmove',
            'onmoveend',
            'onmovestart',
            'onpaste',
            'onpropertychange',
            'onreadystatechange',
            'onreset',
            'onresize',
            'onresizeend',
            'onresizestart',
            'onrowenter',
            'onrowexit',
            'onrowsdelete',
            'onrowsinserted',
            'onscroll',
            'onselect',
            'onselectionchange',
            'onselectstart',
            'onstart',
            'onstop',
            'onsubmit',
            'onunload'
        );
        $ra  = array_merge($ra1, $ra2);

        $content = str_ireplace($ra, '', $content);
    }

    return strip_tags($content);
}

function removeXSS($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <javaΘscript>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // &#x0040 @ search for the hex values
        $val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array(
        'javascript',
        'vbscript',
        'expression',
        'applet',
        'meta',
        'xml',
        'blink',
        'link',
        'style',
        'script',
        'embed',
        'object',
        'iframe',
        'frame',
        'frameset',
        'ilayer',
        'layer',
        'bgsound',
        'title',
        'base'
    );

    $ra2 = Array(
        'onabort',
        'onactivate',
        'onafterprint',
        'onafterupdate',
        'onbeforeactivate',
        'onbeforecopy',
        'onbeforecut',
        'onbeforedeactivate',
        'onbeforeeditfocus',
        'onbeforepaste',
        'onbeforeprint',
        'onbeforeunload',
        'onbeforeupdate',
        'onblur',
        'onbounce',
        'oncellchange',
        'onchange',
        'onclick',
        'oncontextmenu',
        'oncontrolselect',
        'oncopy',
        'oncut',
        'ondataavailable',
        'ondatasetchanged',
        'ondatasetcomplete',
        'ondblclick',
        'ondeactivate',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'onerror',
        'onerrorupdate',
        'onfilterchange',
        'onfinish',
        'onfocus',
        'onfocusin',
        'onfocusout',
        'onhelp',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onlayoutcomplete',
        'onload',
        'onlosecapture',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onmousewheel',
        'onmove',
        'onmoveend',
        'onmovestart',
        'onpaste',
        'onpropertychange',
        'onreadystatechange',
        'onreset',
        'onresize',
        'onresizeend',
        'onresizestart',
        'onrowenter',
        'onrowexit',
        'onrowsdelete',
        'onrowsinserted',
        'onscroll',
        'onselect',
        'onselectionchange',
        'onselectstart',
        'onstart',
        'onstop',
        'onsubmit',
        'onunload'
    );
    $ra  = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                    $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val         = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }

    return $val;
}

/**
 *  Strip specail SQL chars
 */
function stripSQLChars($str) {
    $replace = array(
        'SELECT',
        'INSERT',
        'DELETE',
        'UPDATE',
        'CREATE',
        'DROP',
        'VERSION',
        'DATABASES',
        'TRUNCATE',
        'HEX',
        'UNHEX',
        'CAST',
        'DECLARE',
        'EXEC',
        'SHOW',
        'CONCAT',
        'TABLES',
        'CHAR',
        'FILE',
        'SCHEMA',
        'DESCRIBE',
        'UNION',
        'JOIN',
        'ALTER',
        'RENAME',
        'LOAD',
        'FROM',
        'SOURCE',
        'INTO',
        'LIKE',
        'PING',
        'PASSWD'
    );

    return str_ireplace($replace, '', $str);
}

function mkdirs($dir, $mode = 0777, $recursive = true) {
    if (is_null($dir) || $dir === "") {
        return false;
    }
    if (is_dir($dir) || $dir === "/") {
        return true;
    }
    if (mkdirs(dirname($dir), $mode, $recursive)) {
        return mkdir($dir, $mode);
    }

    return false;
}

function base_url($uri = '', $params = [], $protocol = null) {
    $base_url = slash_item('base_url');

    if (isset($protocol)) {
        // For protocol-relative links
        if ($protocol === '') {
            $base_url = substr($base_url, strpos($base_url, '//'));
        } else {
            $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
        }
    }
    $url = $base_url . ltrim(uri_string($uri), '/');
    if ($params) {
        foreach ($params as $key => $value) {
            $url .= '/' . $key . '/' . $value;
        }
    }

    return $url;
}


function uri_string($uri) {
    if (getConfig('enable_query_strings') === false) {
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        return trim($uri, '/');
    } elseif (is_array($uri)) {
        return http_build_query($uri);
    }

    return $uri;
}

function getConfig($key) {
    $config = \Yaf\Application::app()->getConfig();
    if (strpos($key, '/') > -1) {
        $key_arr = explode('/', $key);
        $result  = $config;
        foreach ($key_arr as $v) {
            $result = $result[$v];
        }

        return isset($result) ? $result : null;
    }

    return (isset($config[$key])) ? $config[$key] : null;
}

/**
 * 抛出异常处理
 * @param string  $msg 异常消息
 * @param integer $code 异常代码 默认为0
 * @throws Yaf\Exception
 * @return void
 */
function E($msg, $code = 0) {
    throw new \Yaf\Exception($msg, $code);
}

function debug($arr) {
    echo '<pre>' . print_r($arr, true) . '</pre>';
    exit;
}

function memory() {
    $cache = Yaf\Registry::get('Redis');
    if (!$cache) {
        loadFile('Redis .class.php');
        $cache = new \Yboard\Redis();
        Yaf\Registry::set('Redis', $cache);
    }

    return $cache;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string  $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parseName($name, $type = 0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function fileExistsCase($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename)) {
                return false;
            }
        }

        return true;
    }

    return false;
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function isSsl() {
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }

    return false;
}

/**
 * XML编码
 * @param mixed  $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id 数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xmlEncode($data, $root = 'think', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
    if (is_array($attr)) {
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : " {$attr}";
    $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml .= "<{$root}{$attr}>";
    $xml .= dataToXml($data, $item, $id);
    $xml .= "</{$root}>";

    return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id 数字索引key转换为的属性名
 * @return string
 */
function dataToXml($data, $item = 'item', $id = 'id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = " {$id}=\"{$key}\"";
            $key = $item;
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= (is_array($val) || is_object($val)) ? dataToXml($val, $item, $id) : $val;
        $xml .= "</{$key}>";
    }

    return $xml;
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed        $value session值
 * @return mixed
 */
function getSession($key) {
    return \Yaf\Session::getInstance()->__get(getConfig('session/prefix') . $key);
}

function setSession($key, $val) {
    return \Yaf\Session::getInstance()->__set(getConfig('session/prefix') . $key, $val);
}

function clearSession($key) {
    return \Yaf\Session::getInstance()->__unset(getConfig('session/prefix') . $key);
}

// Clear cookie
function clearCookie($key) {
    setcookie(getConfig('cookie/prefix') . $key, '');
}

/**
 * Set COOKIE
 */
function saveCookie($key, $value, $expire = 3600, $path = '/', $domain = '', $httpOnly = false) {
    setcookie(getConfig('cookie/prefix') . $key, $value, NOW_TIME + $expire, $path, $domain, $httpOnly);
}

/**
 * 获取cookie
 */
function getCookie($key) {
    return trim(getConfig('cookie/prefix') . $_COOKIE[$key]);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);

    return $ip[$type];
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function sendHttpStatus($code) {
    static $_status = array(
        // Success 2xx
        200 => 'OK',
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}

// 过滤表单中的表达式
function filterExp(&$value) {
    if (in_array(strtolower($value), array('exp', 'or'))) {
        $value .= ' ';
    }
}

// 不区分大小写的in_array实现
function inArrayCase($value, $array) {
    return in_array(strtolower($value), array_map('strtolower', $array));
}

function getMenuTree($menus) {
    $html = '';
    if ($menus) {
        $html .= '<ul class="submenu">';
        foreach ($menus as $value) {
            $subs = (isset($value['subs']) && $value['subs']) ? $value['subs'] : [];
            $html .= '<li><a href="' . $value['url'] . '" ' . ($subs ? 'class="dropdown-toggle"' : '') . '><i class="menu-icon fa fa-caret-right"></i>' . $value['name'] . '</a>';
            $html .= ($subs) ? '<b class="arrow fa fa-angle-down"></b>' : '';
            $html .= getMenuTree($subs);
            $html .= '</li>';
        }
        $html .= '</ul>';
    }

    return $html;
}

function getFileUrl($file_path) {
    return $file_path ? base_url() . getConfig('upload_url') . $file_path : '';
}

function writeLog($message, $level) {
    loadFile('Log.class.php');
    \Yboard\Log::init();
    \Yboard\Log::record($message, $level);
    \Yboard\Log::save();

}

/**
 * 文件加载
 * @param $file
 */
function loadFile($files) {
    if (is_array($files) && !empty($files)) {
        foreach ($files as $file) {
            loadFile($file);
        }
    } else {
        if (strtolower(PHP_OS) == 'linux') {
            \Yaf\Loader::import($files);
        } else {
            if ((strpos($files, '/') > -1)) {
                require_once $files;
            } else {
                require_once LIB_PATH . '/' . $files;
            }
        }
    }
}

function getSetting($key) {
    global $setting;

    return isset($setting[$key]) ? $setting[$key] : '';
}