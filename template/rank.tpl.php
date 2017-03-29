<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>趣做CEO</title>
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="full-screen" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="/src/dist/css/style.css" />
    <script>
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?d4893c0a3a8c267a269be255da13beac";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(hm, s);
    })();
    </script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script src="/src/dist/js/rank_all.min.js"></script>
</head>
<body class="page-home">
<div class="preload">
    <div class="v-content"></div>
</div>
<div id="orientLayer" class="mod-orient-layer">
    <div class="mod-orient-layer__content">
        <i class="icon mod-orient-layer__icon-orient"></i>
        <div class="mod-orient-layer__desc">请在解锁模式下使用竖屏浏览</div>
    </div>
</div>
<!--main content-->
<div class="wrapper animate">
    <div class="bg">
        <!--我是背景-->
        <img src="/src/images/loading/loading_00001.jpg" alt="lynkco"/>
    </div>
    <!--<div class="show-animate">-->

    <!--</div>-->
    <div class="logo">
        <img src="/src/images/logo.png" alt="lynkco"/>
    </div>
    <div class="container">
       <!-- 点击上传照片-->
        <div class="pin pin-8" id="pin-upload">
            <div class="v-content">
                <div class="upload-wrap">
                    <img src="/src/images/upload-bg.jpg" alt="upload"/>
                </div>
                <div class="buttons">
                    <?php if($ismy != 1) {
                        echo '<span ><a href="/" class="btn-play">我也要测</a></span>';
                    }else {
                        echo '<span class="btn-scorelists">排行榜</span><span class="btn-share">趣秀自己</span><span class="btn-playagain">再测一次</span>';
                    }?>
                </div>
            </div>
        </div>
        <!-- CEO 排行榜-->
        <div class="pin pin-9" id="pin-result-lists">
            <div class="v-content">
                <div class="btn-close">close</div>
                <div class="result-wrap">
                    <img src="/src/images/bg-3.jpg" alt="upload"/>
                    <ul class="result-lists">

                    </ul>
                    <form id="form-contact">
                        <p class="des">
                            <img src="/src/images/p8-t1.png" alt="upload"/>
                        </p>
                        <p class="tips-success">
                            <img src="/src/images/btn-submit-success.png" alt="upload"/>
                        </p>
                        <div class="form-information">
                            <div class="input-box input-box-name">
                                <!--<label for="input-name">姓名:</label>-->
                                <input type="text" id="input-name" placeholder="留下你的名字"/>
                            </div>
                            <div class="input-box input-box-mobile">
                                <!--<label for="input-mobile">手机:</label>-->
                                <input type="text" id="input-mobile" placeholder="留下手机号码或邮箱地址"/>
                            </div>
                            <div class="btn-submit">提 交</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="popup share-pop">
        <img src="/src/images/final-share.png" alt="upload"/>
    </div>
</div>
</body>
</html>
