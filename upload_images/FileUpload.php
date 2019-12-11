<?php
namespace app\controller;

use think\facade\Env;
use think\Db;
use think\Request;
use Qiniu\Auth;
use Qiniu\Zone;
use Qiniu\Config;
use Qiniu\Storage\UploadManager;
use app\BaseController;

//七牛云图片上传接口 
class FileUpload
{
    public function upload()
    {
        $time1 = getUnixTimestamp();
        $data = request()->post();

        $imageFloaderPath = $data['imageFloaderPath']; // 图片文件夹完整路径
        $viewFloaderPath = $data['viewFloaderPath']; // 模板文件夹完整路径
        $staticName = isset($data['staticName'])?$data['staticName']:'static'; // 静态文件夹名字
        $floderName = isset($data['floderName'])?$data['floderName']:'icon'; // 图片文件夹名字
        $prefixName = isset($data['prefixName'])?$data['prefixName']:'jirui-website'; // 上传后的前缀，用于区分项目

        // 所有图片文件路径
        $imageFlienamesPath = get_filename_list($imageFloaderPath);
        // 所有模板文件路径
        $ViewFlienamesPath = get_filename_list($viewFloaderPath);
        // 图片在view中的url
        $imageViewUrls = get_replace_path_name($imageFlienamesPath,$staticName,$floderName);

        $config = Env::get('qiniu');
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = Env::get('qiniu.accessKey');
        $secretKey = Env::get('qiniu.secretKey');
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 要上传的空间
        $bucket = Env::get('qiniu.bucket');
        //图片域名
        $domain = Env::get('qiniu.domain');
        // 生成上传 Token
        $token = $auth->uploadToken($bucket,null,3600);
        $params = [
            'filePath'=>'',
            'token'=>$token,
            'domain'=>$domain,
            'staticName'=>$staticName,
            'prefixName'=>$prefixName
        ];
        $fcName = 'get_upload_image_url';
        replace_multiple_file($imageFlienamesPath,$ViewFlienamesPath,$imageViewUrls,$fcName,$params);
        $time2 = getUnixTimestamp();
        return json(['耗时'=>floatval(($time2-$time1)/1000).'秒']);
    }


}  