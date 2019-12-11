<?php
use Qiniu\Storage\UploadManager;

/**
 * 替换多个文件
 *
 * @param Array $img_dirs 所有图片文件路径
 * @param Array $view_dirs 所有模板文件路径
 * @param Array $img_view_urls 图片在view中的url
 * @param String $fcName 回调函数名
 * @param Array $params 回调函数参数
 * @return void
 */
function replace_multiple_file($img_dirs,$view_dirs,$img_view_urls,$fcName,$params){
    foreach ($img_view_urls as $key => $value) {
        if(is_array($value)){
            replace_multiple_file($img_dirs[$key],$view_dirs,$value,$fcName,$params);
        }else{
            $params['filePath'] = $img_dirs[$key];
            replace_single_file($view_dirs,$value,$fcName,$params);
        }
    }
}

/**
 * 替换文件
 *
 * @param Array $view_dir_array 要被替换的文件数组
 * @param String $find 查找的内容
 * @param String $replace 被替换的内容
 * @return void
 */
function replace_single_file($view_dirs,$find,$fcName,$params){
    foreach ($view_dirs as $key => $value) {
        if(is_array($value)){
            replace_single_file($value,$find,$fcName,$params);
        }else{
            $str = file_get_contents($value);
            if(strpos($str, $find)){
                $res = call_user_func_array($fcName,$params);
                // $res = "测试";
                if($res){
                    $re_str = str_replace($find,$res,$str);
                    file_put_contents($value,$re_str);
                }
            }
        }
    }
}

/**
 * 替换文件名
 *
 * @param Array $all_path
 * @param String $static_name
 * @param String $floder_name
 * @return Array
 */
function get_replace_path_name($all_path,$static_name,$floder_name)
{
    $deal_path = [];
    foreach ($all_path as $key => $value) {
        if(is_array($value)){
            // $deal_path[$key] = [];
            $deal_path[$key] = get_replace_path_name($value,$static_name,$floder_name);
        }
        else{
            // $deal_path[$key] = '..'.strstr($value,"/icon");
            $deal_path[$key] = $static_name.'/'.strstr($value,$floder_name);

        }
    }
    return $deal_path;
}

/**
 * 获取文件下面的所有文件名
 *
 * @param [type] $dir
 * @return Array
 */
function get_filename_list($dir)
{
    $files = array();
    //检测是否存在文件
    if (is_dir($dir)) {
        //打开目录
        if ($handle = opendir($dir)) {
            //返回当前文件的条目
            while (($file = readdir($handle)) !== false) {
                //去除特殊目录
                if ($file != "." && $file != "..") {
                    //判断子目录是否还存在子目录
                    if (is_dir($dir . "/" . $file)) {
                        //递归调用本函数，再次获取目录
                        $files[$file] = get_filename_list($dir . "/" . $file);
                    } else {
                        //获取目录数组
                        $files[] = $dir . "/" . $file;
                    }
                }
            }
            //关闭文件夹
            closedir($handle);
            //返回文件夹数组
            return $files;
        }
    }
}

/**
 * 上传图片到七牛云，并获取图片url
 *
 * @param String $filePath
 * @param String $token
 * @param String $domain
 * @param String $staticName
 * @param String $prefixName
 * @return String
 */
function get_upload_image_url($filePath,$token,$domain,$staticName,$prefixName)
{
    // $filePath = 'c:/Users/zy/Desktop/php/jr_website/public/static/icon/icon_kf.png';
    $ext = explode('.',$filePath)[1];  //后缀
    $pre = $prefixName.'/'.strstr(explode('.',$filePath)[0],$staticName);
    // 上传到七牛后保存的文件名
    $key =$pre.'_'.date('YmdHis').'_'.substr(md5(time()) , 0, 5) . '.'.$ext;
    // $zone = new Zone(array('up-z2.qiniup.com'));
    // $cfg = new Config($zone);
    // $uploadMgr = new UploadManager($cfg);
    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new UploadManager();
    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
    if ($err !== null) return flase;
    return $domain.'/'.$ret['key'];
}

/**
 * 获取毫秒时间戳
 */
function getUnixTimestamp ()
{

    list($s1, $s2) = explode(' ', microtime());

    return (float)sprintf('%.0f',(floatval($s1) + floatval($s2)) * 1000);

}