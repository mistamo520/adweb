/**

 layui官网

*/

layui.define(['layer', 'code', 'form', 'element', 'util'], function(exports){
  var $ = layui.jquery
  ,layer = layui.layer
  ,form = layui.form
  ,util = layui.util
  ,device = layui.device();

  //阻止IE7以下访问
  if(device.ie && device.ie < 8){
    layer.alert('Layui最低支持ie8，您当前使用的是古老的 IE'+ device.ie + '，你丫的肯定不是程序猿！');
  }


  //搜索组件
  form.on('select(component)', function(data){
    var value = data.value;
    location.href = '/doc/'+ value;
  });
  

  //手机设备的简单适配
  var treeMobile = $('.site-tree-mobile')
  ,shadeMobile = $('.site-mobile-shade')

  treeMobile.on('click', function(){
    $('body').addClass('site-mobile');
  });

  shadeMobile.on('click', function(){
    $('body').removeClass('site-mobile');
  });

  exports('global', {});
});