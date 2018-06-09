<?php
/**
 * 辅助函数
 *
 * @author jip 2016-06-29
 */
namespace app\libraries;
use Phalcon\Logger\Adapter\Syslog;

class Dfunc
{
    /**
     * 输出
     * code 200 成功  400请求格式错误  500服务器错误
     * msg  错误信息
     * data 输出数据
     */
    public static function output($errorCode = 200, $message = '', $data = array(), $httpStatusCode = 200)
    {
        $return_data = Dfunc::returnJson($errorCode, $message, $data);

        if (!in_array($errorCode, [200, 40009]) && $httpStatusCode == 200) {
            $httpStatusCode = 400;
        }

//        self::_accessLog($httpStatusCode, $return_data);

        return self::returnJson($httpStatusCode, $return_data);


    }
    private function _response($httpStatusCode, $content)
    {
        if (PRO_ENV !== 'production') {
            $this->response
                ->setStatusCode($httpStatusCode)
                ->setHeader('Content-Type', 'application/json; charset=UTF-8')
                ->setHeader('Content-Length', strlen($content))
                ->setHeader("Access-Control-Allow-Origin", "http://10.0.0.243")
                ->setHeader('Content-Length', strlen($content))
                ->setContent($content)
                ->send();
        } else {
            $this->response
                ->setStatusCode($httpStatusCode)
                ->setHeader('Content-Type', 'application/json; charset=UTF-8')
                ->setContent($content)
                ->send();
        }
        exit;
    }
    /**
     * 返回JSON数据
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return string
     */
    public static function returnJson($code = 200, $msg = '', $data = array())
    {
        $return_data = array("code" => $code, "msg" => $msg);
        $data && $return_data['data'] = $data;
        if ($code == 200) {
            unset($return_data['msg']);
        }
        return json_encode($return_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    /**
     * 重新组装二维数组
     * @param array $arr
     * @param string $key
     * @param bool $unique 是否保证唯一key值
     * @return array
     */
    public static function arrayGroup($arr, $keystr , $unique = FALSE)
    {
        if (empty($arr))
            return $arr;

        $_result = array();
        foreach ($arr as $key => $item) {
            if (isset($item[$keystr])) {
                $_result[$item[$keystr]][] = $item;
            } else {
                $_result[count($_result)][] = $item;
            }
        }

        $result = array();
        if ($unique) {
            foreach ($_result as $key => $item) {
                $result[$key] = $item[0];
            }
        } else {
            $result = $_result;
        }
        return $result;
    }

    /**
     * 记录日志
     * @param $message
     * @param string $level
     */
    public static function logMessage($message,$level = 'debug')
    {
        static $logger;

        $config = self::loadIni('config.ini');
        if($config && $config->app_log != 'on')
            return ;

        if($config && $config->log_level!= 'all' && !in_array($level,explode('|',$config->log_level)))
            return ;

        if(empty($logger)){
            $logger = new Syslog($config && $config->base->logTag,
                [
                    'option' => LOG_NDELAY,
                    'facility' => LOG_LOCAL0
                ]
            );
        }
        return $logger->$level('['.$level.'] --> '.$message);
    }

    /**
     * 记录异常
     * @param $e
     * @param string $message
     */
    public static function logException($e,$message = '')
    {
        $error_msg = ' '.$message.' ' . $e->getCode(). ' ' .$e->getMessage().' '.$e->getFile().' '.$e->getLine();

        return self::logMessage($error_msg,'error');
    }

    /**
     * 二维数组排序
     * @param $arr
     * @param $keys
     * @param string $type
     * @return array|bool
     */
    public static function multiArraySort($arr, $keys, $type = "asc")
    {
        if (!is_array($arr)) {
            return false;
        }
        $keysvalue = array ();
        foreach ($arr as $key => $val) {
            $keysvalue[$key] = $val[$keys];
        }
        if ($type == "asc") {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        $keysort = array();
        foreach ($keysvalue as $key => $vals) {
            $keysort[$key] = $key;
        }
        $new_array = array ();
        foreach ($keysort as $key => $val) {
            $new_array[$key] = $arr[$val];
        }
        return $new_array;
    }


    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    public static function safeReplace($string) {
        $string = str_replace('%20','',$string);
        $string = str_replace('%27','',$string);
        $string = str_replace('%2527','',$string);
        $string = str_replace('*','',$string);
        $string = str_replace('"','&quot;',$string);
        $string = str_replace("'",'',$string);
        $string = str_replace('"','',$string);
        $string = str_replace(';','',$string);
        $string = str_replace('<','&lt;',$string);
        $string = str_replace('>','&gt;',$string);
        $string = str_replace("{",'',$string);
        $string = str_replace('}','',$string);
        $string = str_replace('\\','',$string);
        return $string;
    }


}
