<?php
/*
 * 
 * .cfg 文件配置
 *	username 用户名
 *  password 密码
 * 
 * access_token 不需要填写  自动生成 
 * access_token_time  不需要填写自动生成 
 * access_token_maxtime  3600*(12-1)  12个小时有效 第11个小时的时候进行替换
 * 			
 * 需要使用curl库
 * windows下开启方法：
 * 拷贝PHP目录中的libeay32.dll, ssleay32.dll, php5ts.dll, php_curl.dll文件到 system32 目录。(php_curl.dl在ext文件下)
 * 修改php.ini：配置好extension_dir，去掉extension = php_curl.dll前面的分号。
 * 重起apache或者IIS。
 * linux下开启方法：
 * 安装cURL
 * # wgethttp://curl.haxx.se/download/curl-7.17.1.tar.gz
 * # tar -zxf curl-7.17.1.tar.gz
 * # ./configure --prefix=/usr/local/curl
 * # make; make install
 * 安装php
 * 打开开关 --with-curl=/usr/local/curl
 * 
 */


class zeye {
	public $authtoken;
	public static $error =null;
	public static $instance=null;
	private function __construct(){
		self::$error = NUll; 
		$authinfo = json_decode( file_get_contents(".cfg"),TRUE);		 
		if($authinfo == null){			
			die(".cfg配置文件出错 ");
		}
		
		if(	$authinfo["access_token"] == ""  || $authinfo["access_token_time"]  +$authinfo["access_token_maxtime"] <time()){

			$url = 'http://api.zoomeye.org/user/login';
			$data = json_encode( array("username"=>$authinfo["username"],"password"=>$authinfo["password"]) );
			$authres = zeye::curlRequest(array(), $data, $url,TRUE);
						
			if($authres){							
				$authres = self::handleJsonAndError($authres,$url);
			}
			
			if($authres ){
				if($authres["access_token"] != ""){
					$authinfo["access_token"] = $authres["access_token"] ;
					$authinfo["access_token_time"] = time() ;
					$authinfo["access_token_maxtime"] = 3600*(12-1);
					file_put_contents(".cfg", json_encode($authinfo));
					$this->authtoken = $authinfo;					
				}else{
					self::setLastError("Token Null","UnKnowError,raw data:".json_encode($authres),$authres["url"]); 
				}			
			}
		}		
		$this->authtoken = $authinfo;
	}
	public static function getInstance(){
		self::$error = NUll;
		if(is_null(self::$instance)){
			self::$instance = new zeye;
		}
		return self::$instance;
	}

	public static function setLastError($error,$msg,$url){		
		self::$error["error"] = $error;
		self::$error["msg"] = $msg;
		self::$error["url"] = $url;
	}
	
	public static function getLastError(){
		return self::$error;
	}

	public  function getResourcesInfo(){
			self::$error = NUll; 
			$url = "http://api.zoomeye.org/resources-info";		
			$requestres = self::curlRequest($this->getHeader(), "", $url);
			if($requestres){
				$requestres = self::handleJsonAndError($requestres,$url);						
			}

			if($requestres){				
				 return($requestres);				
			}				
			return 0;				
	}


	/*查询
	 * 
	 * $query:查询语句 
	 * app: 组件名
	 * ver: 组件版本
	 * country: 国家或者地区代码
	 * city: 城市名称
	 * port: 开放端口
	 * device: 设备类型
	 * os: 操作系统
	 * service: 服务名
	 * hostname: 主机名
	 * ip: IP 地址
	 * site: 网站域名
	 * headers: HTTP 头
	 * title: 网页标题
	 * 
	 * 
	 * 
	 * $facets:摘要里面显示的字段 
	 * webapp 
	 * component	
	 * framework
	 * frontend	
	 * server	
	 * waf	
	 * os 
	 * country	
	 * city	
	 * 
	 * $page:分页 
	 */  
	
	public function searchHost($query,$facets=array(),$page = 1){
			self::$error = NUll; 
			$url = "http://api.zoomeye.org/host/search?";			
			$url = $url."query=".$query."";
			if(!empty($facets)){
				$url = $url."&facets=". implode(",", $facets) ."";
				//$url = $url."&facets=portz";
				
				
			}
			$url = $url."&page=". intval($page) ."";
			
			//echo $url;
			//die();
			 
			$requestres = self::curlRequest($this->getHeader(), "", $url);
			 
			if($requestres){
				$requestres = self::handleJsonAndError($requestres,$url);						
			}

			if($requestres){				
				 return($requestres);				
			}		
			return 0;
	}
	
	public function searchWeb($query,$facets=array(),$page = 1){
			self::$error = NUll; 
			$url = "http://api.zoomeye.org/web/search?";			
			$url = $url."query=".$query."";
			if(!empty($facets)){
				$url = $url."&facets=". implode(",", $facets) ."";
			}
			$url = $url."&page=". intval($page) ."";
			
			$requestres = self::curlRequest($this->getHeader(), "", $url);
			 
			if($requestres){
				$requestres = self::handleJsonAndError($requestres,$url);						
			}

			if($requestres){				
				 return($requestres);				
			}		
			return 0;
	}	
	
	public function getHeader(){
		$header[] = "Authorization: JWT ".$this->authtoken["access_token"];
		return $header;
	}

	public static function handleJsonAndError($jsonstring,$url){
		 
		//var_dump($jsonstring);
		$jsonobj = json_decode($jsonstring,TRUE);				
		if($jsonobj == NULL){
			self::setLastError("Json Parse Fail",'can not parse the json',$url); 
			return 0;
		}
		
		if($jsonobj["error"] != ""){
			self::setLastError($jsonobj["error"],$jsonobj["message"],$jsonobj["url"]); 
			return 0;			
		}
		
		
		return $jsonobj;
	}
/*	
 *  提交请求
 *  @param $host array 需要配置的域名 array("cookik: ssss:dddd");
 *  @param $data string 需要提交的数据 'user=xxx&qq=xxx&id=xxx&post=xxx'....
 *  @param $url string 要提交的url 'http://192.168.1.12/xxx/xxx/api/';
 */	
	public static function  curlRequest($hearder,$data,$url,$ispost = False)
    {
       $ch = curl_init();
       $res= curl_setopt ($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
       
	   if($ispost){
       	curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	   }

       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$hearder);
 
       $result = curl_exec ($ch);
       curl_close($ch);
       if ($result == NULL) {
       	 self::setLastError("Bad Request", 'request invalid, validate usage and try again',$url);
           return 0;
       }       
       return $result;
    }
 
	
	
	
}

?>
