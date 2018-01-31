document.write("<script src='\/public\/js\/mui.min.js'><\/script>");


document.addEventListener('plusready', function() {
		var quitbtn = document.querySelector('.quitBtn1992');
        var webview = plus.webview.currentWebview();
//      plus.key.removeEventListener('backbutton',function(){},true)
        plus.key.addEventListener('backbutton', function() {
        	if(quitbtn){
                			 mui.confirm("是否确认退出","独需商城", ["确定", "取消"], function(e){if(e.index == 0){plus.runtime.quit()}});
                			 return;
                		}
            webview.canBack(function(e) {
                if(e.canBack) {
                    webview.back();
                    return;
                } else {
//              	alert(e.canBack)
                    //webview.close(); //hide,quit
                    //plus.runtime.quit();
//                  mui.plusReady(function() {
//                      //首页返回键处理
//                      //处理逻辑：1秒内，连续两次按返回键，则退出应用；
//                      var first = null;
//                      plus.key.addEventListener('backbutton', function() {
//                          //首次按键，提示‘再按一次退出应用’
//
//                          if (!first) {
//                              first = new Date().getTime();
//
//
////								mui.toast('再按一次退出应用22222');
////mui.alert('是否确认退出', null, ['是', '否'], null, 'div');
//                              setTimeout(function() {
//                                  first = null;
//                              }, 2000);
//                          } else {
//
//                              if (new Date().getTime() - first < 1500) {
//                              	alert('本来可以退出的，被我屏蔽了')
////                                  plus.runtime.quit();
//                              }
//                          }
//                      },false);
//                  });
                }
            })
        });
    });
