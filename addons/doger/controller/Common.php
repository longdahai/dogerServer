<?php
/**
 * Created by PhpStorm.
 * User: Lazytech
 * Date: 2019/1/6
 * Time: 6:22
 */

namespace addons\doger\controller;


use addons\doger\library\TemplateHelper;
use addons\doger\library\Token;
use addons\doger\model\Banner;
use addons\doger\model\Tag;
use addons\doger\model\Userqrcode;
use app\common\model\Area;
use app\common\model\Attachment;
use app\common\model\Version;
use EasyWeChat\Factory;
use fast\Http;
use fast\Random;
use think\Cache;
use think\Config;
/**
 * 公共接口
 */
class Common extends Api
{

    protected $noNeedLogin = ['init','banner','refreshToken','qrcode','upload','tag','getUpToken'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 加载初始化
     *
     * @param string $version 版本号
     * @param string $lng 经度
     * @param string $lat 纬度
     */
    public function init()
    {
        if ($version = $this->request->request('version')) {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');
            $content = [
                'citydata'    => Area::getCityFromLngLat($lng, $lat),
                'versiondata' => Version::check($version),
                'uploaddata'  => Config::get('upload'),
                'coverdata'   => Config::get("cover"),
            ];
            $this->success('', $content);
        } else {
            $this->error(__('Invalid parameters'));
        }
    }

    /**
     * 上传文件
     * @ApiMethod (POST)
     * @param File $file 文件流
     */
    public function upload()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $upload['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            $this->error(__('Uploaded file format is limited'));
        }
        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'admin_id'    => 0,
                'user_id'     => (int)$this->auth->id,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
            );
            $attachment = new Attachment();
            $attachment->data(array_filter($params));
            $attachment->save();
            \think\Hook::listen("upload_after", $attachment);
            $this->success(__('Upload successful'), [
                'url' => $uploadDir . $splInfo->getSaveName()
            ]);
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    /**
     * 获取banner
     */
    public function banner(){
        $banners = Banner::all(function($query){
            $query->where('status',1)->field('name,reduceimage,position,jumplink')->order('weigh','desc');
        });
        $this->success('success',$banners);
    }

    /**
     * 获取标签数据
     */
    public function tag(){
        $tag = Tag::all();
        $data = $tag;
//        if($tag) {
//            foreach ($tag as $k => $v){
//                $data[$v['type']][] = $v;
//            }
//        }
        $this->success('success',$data);
    }

    /**
     * 刷新用户token 开发用 之后会废弃
     */
    public function refreshToken(){
        $token = Random::uuid();
        $user_id = $this->request->param('userid');
        if(Token::set($token,$user_id,2592000)){
            $this->success('success',$token);
        }
    }

    /**
     * 获取微信后端accesstoken
     */
    private function getAccessToken(){
        if(Cache::get('accessToken')){
           return Cache::get('accessToken');
        } else {
            return $this->refreshAccessToken();
        }
    }

    private function refreshAccessToken(){
        $config = get_addon_config('doger');
        $params = [
            'appid' => $config['wxappid'],
            'secret' => $config['wxappsecret'],
            'grant_type' => 'client_credential'
        ];

        $result = Http::sendRequest("https://api.weixin.qq.com/cgi-bin/token", $params, 'GET');
        if(isset($result['errcode'])){
            return false;
        } else {
            $result = json_decode($result['msg']);
            Cache::set('accessToken',$result->access_token,7000);
            return $result->access_token;
        }
    }

    /**
     * 获取用户二维码
     */
    public function qrcode(){
        $accessToken = $this->getAccessToken();
        if(!$accessToken){
            $this->error('failed');
        }
        $uid = $this->request->param('uid');
        $fileName=$uid."-qrcode.png";
        $qrcode = Userqrcode::get(['lover_user_id'=>$uid]);
        if($qrcode) {
            $qrcodeUrl = $qrcode->qrcodeimage;
            $this->success('success',$qrcodeUrl);
        } else {
            $params = [
                'scene' => '?uid='.$uid,
                'page' => 'pages/start/index',
                'width' => 280,
                'is_hyaline' => true
            ];
            $params = json_encode($params);
            $result = Http::sendRequest("https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$accessToken, $params, 'POST');

            if (!$result) {
                $this->error('failed');
            }

            file_put_contents(ROOT_PATH . '/public/qrcode' , $fileName);
            $data = [
                'lover_user_id'=>$uid,
                'qrcodeimage'=>$fileName,
                'scene'=>'?uid='+ $uid,
                'page'=>'pages/start/index'
            ];
            Userqrcode::create($data,true);
            $this->success('success',$fileName);
        }

    }

    public function sendTest(){
        $uid = $this->auth->__get('id');
        $th = New TemplateHelper();
        $th->sendMessage($uid);
    }

    /**
     * 刷新七牛云token
     */
    public function refreshUpToken(){
        $accessKey = "6DD3VaLJ_SQgOy86t0CyTV_YWaDdjnL2n8EGx7kG";
        $secretKey = "qgZa5qrvJBkqKN8KStE1yCjZ9nB7AKvJUs_34SIm";
        $bucket = "doger";
        $expires = 7200;

        $auth = new \Qiniu\Auth($accessKey,$secretKey);
        $policy = null;
        $upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);

        Cache::set('upToken',$upToken,7000);
        $data = [
            "uptoken" => $upToken
        ];
        return json_encode($data);

    }

    /**
     * 获取七牛云token
     */
    public function getUpToken(){
        if(Cache::get('upToken')){
            $data = [
                "uptoken" => Cache::get('upToken')
            ];
            return json_encode($data);
        } else {
            return $this->refreshUpToken();
        }
    }


}