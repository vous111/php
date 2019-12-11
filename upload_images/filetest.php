<?php
// 1.搜索public/static/icon/*.png 得到每一个文件名的路径，用数组将其保存
// print_r(my_scandir("c:/Users/zy/Desktop/php/jr_website/public/static/icon")); //电脑的文件路径即可
$icon_all_dirs = my_scandir("c:/Users/zy/Desktop/php/jr_website/public/static/icon");

// 2.遍历图片名数组，搜索文件名有没有在view1/web/的文件中使用，有的话用数组保存被使用的文件地址，没有则开始下一个图片
$view_dirs = my_scandir("c:/Users/zy/Desktop/php/jr_website/view3/web");

$icon_dirs=get_replace_path_name($icon_all_dirs);
replace_multiple_file($icon_dirs, $view_dirs);
// print_r($icon_dirs);
/**
 * 替换多个文件
 *
 * @param [type] $img_dirs 查找的内容数组
 * @param [type] $view_dirs 被替换的文件数组
 * @param [type] $replace 被替换的内容
 * @return void
 */
function replace_multiple_file($img_dirs,$view_dirs,$replace=null){
    foreach ($img_dirs as $key => $value) {
        if(is_array($value)){
            replace_multiple_file($value,$view_dirs,$replace);
        }else{
            replace_single_file($view_dirs,$value, $replace ? $replace : 'static'.strstr($value,"/icon"));
        }
    }
}

/**
 * 替换文件名
 *
 * @param Array $all_path
 * @param Array $deal_path
 * @return Array
 */
function get_replace_path_name($all_path)
{
    $deal_path = [];
    foreach ($all_path as $key => $value) {
        if(is_array($value)){
            // $deal_path[$key] = [];
            $deal_path[$key] = get_replace_path_name($value);
        }
        else{
            $deal_path[$key] = '..'.strstr($value,"/icon");
        }
    }
    return $deal_path;
}


/**
 * 替换文件
 *
 * @param Array $view_dir_array 要被替换的文件数组
 * @param String $find 查找的内容
 * @param String $replace 被替换的内容
 * @return void
 */
function replace_single_file($view_dirs,$find,$replace){
    foreach ($view_dirs as $key => $value) {
        if(is_array($value)){
            replace_single_file($value,$find,$replace);
        }else{
            $str = file_get_contents($value);
            if(strpos($str, $find)){
                $re_str = str_replace($find,$replace,$str);
                file_put_contents($value,$re_str);
            }
        }
    }
}

function my_scandir($dir)
{
    //定义一个数组
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
                        $files[$file] = my_scandir($dir . "/" . $file);
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