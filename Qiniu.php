<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/25
 * Time: 17:14
 */

namespace gmars\qiniu;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Cache;
use think\Config;
use think\Exception;

require 'qiniu_driver/autoload.php';

class Qiniu
{
    private $_accessKey;
    private $_secretKey;
    private $_bucket;

    private $_error;

    /**
     * Qiniu constructor.
     * @param string $accessKey
     * @param string $secretKey
     * @param string $bucketName
     * 初始化参数可以直接配置到tp的配置中
     */
    public function __construct($accessKey = "", $secretKey = "", $bucketName = "")
    {
        if (empty($accessKey) || empty($secretKey) || empty($bucketName)) {
            $qiniuConfig = Config::get('qiniu');
            if (empty($qiniuConfig['accesskey']) || empty($qiniuConfig['secretkey'])) {
                $this->_error = '你的配置信息不完整！';
                return false;
            }
            $this->_accessKey = $qiniuConfig['accesskey'];
            $this->_secretKey = $qiniuConfig['secretkey'];
        }else{
            $this->_accessKey = $accessKey;
            $this->_secretKey = $secretKey;
        }

        if (!empty($bucketName)) {
            $this->_bucket = $bucketName;
        }
    }

    /**
     * @return bool|string
     * 获取bucket
     */
    private function _getBucket()
    {
        if (!empty($this->_bucket)) {
            return $this->_bucket;
        }
        $bucket = Config::get('qiniu.bucket');
        if (empty($bucket)) {
            return false;
        }
        return $bucket;
    }

    /**
     * @param string $saveName
     * @param string $bucket
     * @return mixed
     * @throws Exception
     * @throws \Exception
     * 单文件上传，如果添加多个文件则只上传第一个
     */
    public function upload($saveName = '', $bucket = '')
    {
        $token = $this->_getUploadToken($bucket);

        $files = $_FILES;
        if (empty($files)) {
            throw new Exception('没有文件被上传', 10002);
        }
        $values = array_values($files);

        $uploadManager = new UploadManager();
        if (empty($saveName)) {
            $saveName = hash_file('sha1', $values[0]['tmp_name']).time();
        }
        $infoArr = explode('.', $values[0]['name']);
        $extension = array_pop($infoArr);
        $fileInfo = $saveName . '.' . $extension;
        list($ret, $err) = $uploadManager->putFile($token, $saveName, $values[0]['tmp_name']);
        if ($err !== null) {
            throw new Exception('上传出错'.serialize($err));
        }
        return $ret['key'];
    }

    /**
     * @param $bucketName
     * @return mixed|string
     * @throws Exception
     * 只有设置到配置的bucket才会使用缓存功能
     */
    private function _getUploadToken($bucketName)
    {
        $upToken = Cache::get('qiniu_upload_token');
        if (!empty($upToken) && empty($bucketName)) {
            return $upToken;
        }else{
            $auth = new Auth($this->_accessKey, $this->_secretKey);
            $bucket = empty($bucketName)? $this->_getBucket():$bucketName;
            if ($bucket === false) {
                throw new Exception('你还没有设置或者传入bucket', 100001);
            }
            $upToken = $auth->uploadToken($bucket);
            Cache::set('qiniu_upload_token', $upToken);
            return $upToken;
        }
    }


}