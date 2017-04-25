qiniu-tp5

基于tp5框架的七牛云存储实现，可以方便的实现文件上传，文件管理功能。只需两行代码就能完成一次文件上传
安装方式。使用composer安装，在项目根目录下运行

composer require gmars/tp5-qiniu

如果该方法安装不成功，请在项目根目录下的composer.json的require中添加

"gmars/tp5-qiniu": "dev-master"

然后使用cmd进入项目根目录下运行composer update

使用方式：

一、配置使用

1.1配置：

在tp5的配置文件config.php中配置七牛云的配置参数，当然此插件支持实例化时再传入配置参数

'qiniu' => [

        'accesskey' => '你自己的七牛云accesskey',
        'secretkey' => '你自己的七牛云secretkey',
        'bucket' => '你自己创建的bucket',
 ]

1.2使用

 try{
 
      $qiniu = new Qiniu();
      $result = $qiniu->upload();
      
 }catch (Exception $e){
 
      dump($e);
 }
 
 如果上传成功则返回的是key值也就是文件对应的key使用你自己的域名拼接key就可以直接访问了</p>


二、直接使用

  try{
  
      $qiniu = new Qiniu('你自己的七牛云accesskey','你自己的七牛云secretkey','你自己创建的bucket');
      $result = $qiniu->upload();
      
 }catch (Exception $e){
 
      dump($e);
 }

如果修改了配置参数请先清除一下缓存文件，在runtime/cache目录下，因为本插件考虑到了文件上传的性能，对上传token进行了缓存，缓存时间为3600秒，如果配置参数改变后不清除缓存则可能会出现错误。
 同时，upload()方法支持参数传入。可传入第一个参数为要上传文件保存的名称，第二个参数为bucket名称。
 
 如果第一个参数不填写则上传后的文件名默认取文件的hash串拼接时间戳time()的方式。
 
 如果第二个参数是支持不同的文件上传到不同的bucket的参数，也就是可以再文件上传时重新传bucket让文件上传到不同的bucket中


如果使用中有任何错误或者疑问可以给我发邮件：weiyongqiang@weiyongqiang.com

