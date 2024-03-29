<?php

/*
 * ### CKFinder : Configuration File - Basic Instructions
 *
 * In a generic usage case, the following tasks must be done to configure
 * CKFinder:
 *     1. Check the $baseUrl and $baseDir variables;
 *     2. If available, paste your license key in the "LicenseKey" setting;
 *     3. Create the CheckAuthentication() function that enables CKFinder for authenticated users;
 *
 * Other settings may be left with their default values, or used to control
 * advanced features of CKFinder.
 */
if(!defined("ROOT_DIR")){
    //基本ファイルのインクルード
    if($_REQUEST["command"]!="Init" && $_REQUEST["command"]!="GetFiles"){
        $lite_mode = true; //軽量動作モード
    }
    require_once("default.php");
}

if($_REQUEST["command"]=="Init"){
    $Mcache = new McacheClass();
    if(!$Mcache->get("CKFINDER_PHOTO_CATEGORY_FLG".SITE_ID)){

        $strSQL = "select season from t_photo_header where dflg = 0 group by season" ;
        $result = selectQuery($cn, $strSQL);
        $photo_category_list = Photo::getCategoryTreeList($cn);

        while($row = getRow($result)){
            make_dir(PHOTO_SAVE_DIR."/".$row["season"]);
            
            //カテゴリ名の変更とかがあるので、たまに消す
            if(mt_rand(0, 10)==1 && isNumber($row["season"])){
                system("rm -Rf ".PHOTO_SAVE_DIR."/".$row["season"]."/*");
            }
            foreach($photo_category_list as $val2){
                if(!$val2["parent_id"]){
                    make_dir(PHOTO_SAVE_DIR."/".$row["season"]."/".$val2["category_nm"]);
                    foreach($photo_category_list as $val3){
                        if($val2["photo_category_id"] == $val3["parent_id"]){
                            make_dir(PHOTO_SAVE_DIR."/".$row["season"]."/".$val2["category_nm"]."/".$val3["category_nm"]);
                        }
                    }
                }
            }

        }
        $Mcache->set("CKFINDER_PHOTO_CATEGORY_FLG".SITE_ID,1);//1時間キャッシュ作成

    }
}

/**
 * This function must check the user session to be sure that he/she is 
 * authorized to upload and access files in the File Browser. 
 *
 * @return boolean
 */
function CheckAuthentication()
{    
  //WARNING : DO NOT simply return "true". By doing so, you are allowing
  //"anyone" to upload and list the files in your server. You must implement
  //some kind of session validation here. Even something very simple as...

  // return isset($_SESSION['IsAuthorized']) && $_SESSION['IsAuthorized'];

  //... where $_SESSION['IsAuthorized'] is set to "true" as soon as the
  //user logs in your system.
  //return 1;
  if($_SESSION["user_img_flg"] == "0" || $_SESSION["user_img_flg"] == "1" || $_SESSION["super_flg"] || $_SESSION["rcms_master"]){
    return true;
  }else{
    return false;
  }

}

// LicenseKey : Paste your license key here. If left blank, CKFinder will be
// fully functional, in demo mode.
$config['LicenseName'] = 'RCMS';
$config['LicenseKey'] = 'QN32-7649-J9HR-9A87-2DR2-NZJ1-PFCD';

/*
To make it easy to configure CKFinder, the $baseUrl and $baseDir can be used.
Those are helper variables used later in this config file.
*/

/*
$baseUrl : the base path used to build the final URL for the resources handled
in CKFinder. If empty, the default value (/userfiles/) is used.

Examples:
  $baseUrl = 'http://example.com/ckfinder/files/';
  $baseUrl = '/userfiles/';

ATTENTION: The trailing slash is required.
*/
$baseUrl = '/files';

/*
$baseDir : the path to the local directory (in the server) which points to the
above $baseUrl URL. This is the path used by CKFinder to handle the files in
the server. Full write permissions must be granted to this directory.

Examples:
  // You may point it to a directory directly:
  $baseDir = '/home/login/public_html/ckfinder/files/';
  $baseDir = 'C:/SiteDir/CKFinder/userfiles/';

  // Or you may let CKFinder discover the path, based on $baseUrl:
  $baseDir = resolveUrl($baseUrl);

ATTENTION: The trailing slash is required.
*/
//$baseDir = resolveUrl($baseUrl);
$baseDir = ROOT_DIR.'/html/files';

/*
 * ### Advanced Settings
 */

/*
Thumbnails : thumbnails settings. All thumbnails will end up in the same
directory, no matter the resource type.
*/
$config['Thumbnails'] = Array(
    'url' => $baseUrl . '/_thumbs',
    'directory' => $baseDir . '/_thumbs',
    'enabled' => true,
    'directAccess' => false,
    'maxWidth' => 100,
    'maxHeight' => 100,
    'bmpSupported' => false,
    'quality' => 80);

/*
Set the maximum size of uploaded images. If an uploaded image is larger, it
gets scaled down proportionally. Set to 0 to disable this feature.
*/
$config['Images'] = Array(
    'maxWidth' => 0,
    'maxHeight' => 0,
    'quality' => 80);    
    
/*
RoleSessionVar : the session variable name that CKFinder must use to retrieve
the "role" of the current user. The "role", can be used in the "AccessControl"
settings (bellow in this page).

To be able to use this feature, you must initialize the session data by
uncommenting the following "session_start()" call.
*/
$config['RoleSessionVar'] = 'CKFinder_UserRole';
//session_start();

/*
AccessControl : used to restrict access or features to specific folders.

Many "AccessControl" entries can be added. All attributes are optional.
Subfolders inherit their default settings from their parents' definitions.

  - The "role" attribute accepts the special '*' value, which means
    "everybody".
  - The "resourceType" attribute accepts the special value '*', which
    means "all resource types".
*/

$config['AccessControl'][] = Array(
    'role' => '*',
    'resourceType' => '*',
    'folder' => '/',

    'folderView' => true,
    'folderCreate' => true,
    'folderRename' => true,
    'folderDelete' => true,

    'fileView' => true,
    'fileUpload' => true,
    'fileRename' => true,
    'fileDelete' => true);

$config['AccessControl'][] = Array(
    'role' => '*',
    'resourceType' => '画像モジュール',
    'folder' => '/',

    'folderView' => true,
    'folderCreate' => false,
    'folderRename' => false,
    'folderDelete' => false,

    'fileView' => true,
    'fileUpload' => false,
    'fileRename' => false,
    'fileDelete' => false);


$config['AccessControl'][] = Array(
    'role' => '*',
    'resourceType' => '画像モジュール',
    'folder' => '/s/',

    'folderView' => false,
    'folderCreate' => false,
    'folderRename' => false,
    'folderDelete' => false,

    'fileView' => false,
    'fileUpload' => false,
    'fileRename' => false,
    'fileDelete' => false);

if($module_installed["api_flickr"]){
$config['AccessControl'][] = Array(
    'role' => '*',
    'resourceType' => 'Flickr',
    'folder' => '/',

    'folderView' => true,
    'folderCreate' => false,
    'folderRename' => false,
    'folderDelete' => false,

    'fileView' => true,
    'fileUpload' => false,
    'fileRename' => false,
    'fileDelete' => false);

}

/*
For example, if you want to restrict the upload, rename or delete of files in
the "Logos" folder of the resource type "Images", you may uncomment the
following definition, leaving the above one:

$config['AccessControl'][] = Array(
    'role' => '*',
    'resourceType' => 'Images',
    'folder' => '/Logos',

    'fileUpload' => false,
    'fileRename' => false,
    'fileDelete' => false);
*/

/*
ResourceType : defines the "resource types" handled in CKFinder. A resource
type is nothing more than a way to group files under different paths, each one
having different configuration settings.

Each resource type name must be unique.

When loading CKFinder, the "type" querystring parameter can be used to display
a specific type only. If "type" is omitted in the URL, the
"DefaultResourceTypes" settings is used (may contain the resource type names
separated by a comma). If left empty, all types are loaded.

maxSize is defined in bytes, but shorthand notation may be also used.
Available options are: G, M, K (case insensitive).
1M equals 1048576 bytes (one Megabyte), 1K equals 1024 bytes (one Kilobyte), 1G equals one Gigabyte.
Example: 'maxSize' => "8M",
*/

$config['DefaultResourceTypes'] = '';

$ckf_ref = parse_url($_SERVER["HTTP_REFERER"]);

if($_SESSION["super_flg"] 
      && $ckf_ref['path'] == "/wysiwyg/ckfinder/standalone.php"
      && substr($ckf_ref['query'],0,14) == "Resource_path="
      && substr($ckf_ref['query'],14,1) == "/"
      ){

    $Resource_path = substr($ckf_ref['query'],14);

    //フォルダ指定時
    if(is_public_file(ROOT_DIR."/html/files",ROOT_DIR."/html/files".$_REQUEST["Resource_path"])){
      $config['ResourceType'][] = Array(
        'name' => '/files'.$Resource_path,        // Single quotes not allowed
        'url' => $baseUrl . $Resource_path,
        'directory' => $baseDir . $Resource_path,
        'maxSize' => 0,
        'allowedExtensions' => $allowedExtensions,
        'deniedExtensions' => '');
    }
    
}else{
    //通常時
    if($_SESSION["user_img_flg"]==0 || $_SESSION["super_flg"]){
      $config['ResourceType'][] = Array(
        'name' => '/files/user/',        // Single quotes not allowed
        'url' => $baseUrl . '/user',
        'directory' => $baseDir . '/user',
        'maxSize' => 0,
        'allowedExtensions' => $allowedExtensions,
        'deniedExtensions' => '');
    }

    if($_SESSION["auth"]["photo"]["select"] || $_SESSION["super_flg"]){
      $config['ResourceType'][] = Array(
        'type' => 'rcms_photo',        // Single quotes not allowed
        'name' => '画像モジュール',        // Single quotes not allowed
        'url' => $baseUrl . '/photo',
        'directory' => $baseDir . '/photo',
        'maxSize' => 0,
        'allowedExtensions' => $allowedExtensions,
        'deniedExtensions' => '');
    }

    //RCMS用に設定するディレクトリ
    //imgフォルダなければ作成する
    if($_SESSION["user_img_flg"]==1 || $_SESSION["rcms_master"]){

        make_dir($baseDir."user_img");
        make_dir($baseDir."user_img/".$_SESSION["member_id"]);

        $config['ResourceType'][] = Array(
            'name' => '自分専用のフォルダ',        // Single quotes not allowed
            'url' => $baseUrl . '/user_img/'.$_SESSION["member_id"],
            'directory' => $baseDir . '/user_img/'.$_SESSION["member_id"],
            'maxSize' => 0,
            'allowedExtensions' => $allowedExtensions,
            'deniedExtensions' => '');

        if($_SESSION["super_flg"]){

          $config['ResourceType'][] = Array(
            'name' => '各ユーザー専用のフォルダ',        // Single quotes not allowed
            'url' => $baseUrl . '/user_img/',
            'directory' => $baseDir . '/user_img/',
            'maxSize' => 0,
            'allowedExtensions' => $allowedExtensions,
            'deniedExtensions' => '');

        }
    }


    if($module_installed["api_flickr"]){
    $config['ResourceType'][] = Array(
        'type' => 'rcms_flickr',        // Single quotes not allowed
        'name' => 'Flickr',        // Single quotes not allowed
        'url' => $baseUrl . '/flickr',
        'directory' => $baseDir . '/flickr',
        'maxSize' => 0,
        'allowedExtensions' => $allowedExtensions,
        'deniedExtensions' => '');
    }

}

/*
 Due to security issues with Apache modules, it is recommended to leave the
 following setting enabled.

 How does it work? Suppose the following:

  - If "php" is on the denied extensions list, a file named foo.php cannot be
    uploaded.
  - If "rar" (or any other) extension is allowed, one can upload a file named
    foo.rar.
  - The file foo.php.rar has "rar" extension so, in theory, it can be also
    uploaded.

In some conditions Apache can treat the foo.php.rar file just like any PHP
script and execute it.

If CheckDoubleExtension is enabled, each part of the file name after a dot is
checked, not only the last part. In this way, uploading foo.php.rar would be
denied, because "php" is on the denied extensions list.
*/
$config['CheckDoubleExtension'] = false;

/*
If you have iconv enabled (visit http://php.net/iconv for more information),
you can use this directive to specify the encoding of file names in your
system. Acceptable values can be found at:
  http://www.gnu.org/software/libiconv/

Examples:
  $config['FilesystemEncoding'] = 'CP1250';
  $config['FilesystemEncoding'] = 'ISO-8859-2';
*/
$config['FilesystemEncoding'] = 'UTF-8';

/*
Perform additional checks for image files
if set to true, validate image size
*/
$config['SecureImageUploads'] = true;

/*
Indicates that the file size (maxSize) for images must be checked only 
after scaling them. Otherwise, it is checked right after uploading. 
*/
$config['CheckSizeAfterScaling'] = true;

/* 
For security, HTML is allowed in the first Kb of data for files having the
following extensions only.
*/
//$config['HtmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['HtmlExtensions'] = array('html', 'htm', 'bml', 'js');

/*
Folders to not display in CKFinder, no matter their location.
No paths are accepted, only the folder name.
The * and ? wildcards are accepted.
*/
$config['HideFolders'] = Array(".svn", "CVS");

/*
Files to not display in CKFinder, no matter their location.
No paths are accepted, only the file name, including extension.
The * and ? wildcards are accepted.
*/
$config['HideFiles'] = Array(".*");
 
/*
After file is uploaded, sometimes it is required to change its permissions
so that it was possible to access it at the later time.
If possible, it is recommended to set more restrictive permissions, like 0755.
Set to 0 to disable this feature.
Note: not needed on Windows-based servers.
*/
$config['ChmodFiles'] = 0777 ;

/*
See comments above.
Used when creating folders that does not exist.
*/
$config['ChmodFolders'] = 0755 ;

/*
Force ASCII names for files and folders.
If enabled, characters with diactric marks, like å, ä, ö, ć, č, đ, š
will be automatically converted to ASCII letters.
*/
$config['ForceAscii'] = false;


include_once "plugins/imageresize/plugin.php";
include_once "plugins/fileeditor/plugin.php";
include_once "plugins/zip/plugin.php";

$config['plugin_imageresize']['smallThumb'] = '90x90';
$config['plugin_imageresize']['mediumThumb'] = '120x120';
$config['plugin_imageresize']['largeThumb'] = '180x180';

$config['Thumbnails']['directAccess'] = true;

define( 'CKFINDER_DEFAULT_BASEPATH', '/wysiwyg/ckfinder/' ) ;
