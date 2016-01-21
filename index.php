<!DOCTYPE html>
<html id="drop" lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Graffiti.Buzz</title>
  <meta name="Description" content='Because earth without art is just "eh".'>
  <meta name="Keywords" content="graffiti street art map arte rua mapa">
  <link rel="stylesheet" href="css/styles.css" >
  <link rel="shortcut icon" href="img/favicon.ico" >
  <script src="js/analytics.js"></script>
</head>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '441905576015354',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<header class="header">
  <a class="logo" href="http://www.graffiti.buzz"></a>
  <nav class="main-nav">
    <div class="main-menu">
      <a href="">The project</a>
      <a href="">Graffiti Map</a>
    </div>
    <div class="login-signup">
      <a href="">Login</a>
      <a href="">Sign Up</a>
    </div>
  </nav>
</header>
<section class="banner">
  <div class="texts-banner">
    <div class="text-1">
      <p>SIMPLE AS THAT. SPOT, TAKE A PIC, UPLOAD.</p>
    </div>
    <a href="#" class="getstarted">GET STARTED</a>
  </div>
</section>
<section class="slogan">
  <h1 class="h1-slogan">STREET ART IS EVERYWHERE</h1>
  <h2 class="h2-slogan"><a href="#">#supportyourlocalartists</a></h2>
</section>
<div
  class="fb-like"
  data-share="true"
  data-width="450"
  data-show-faces="true">
</div>

<br>
  <form enctype="multipart/form-data" id="file-form" action="server/upload_handler.php" method="POST">
    <!-- MAX_FILE_SIZE deve preceder o campo input -->
    <input type="hidden" name="MAX_FILE_SIZE" value="4404019" />
    <input type="button" id="fs-button" value="Selecionar fotos para envio" />
    <input type="file" id="file-select" name="photos[]" style="display: none;" multiple />
    <input type="submit" id="upload-button" style="display: none;" value="Enviar" />
  </form>
  <br>
  <div id="status" style="display: none;">
    <div id="statusLbl">Status:</div>
    <progress id="pgBar" value=0 max=100>Progresso:</progress>
    <div id="approvedLbl"></div>
    <div id="errorLbl"></div>
  </div>

<div id="map-wrap" style="width:1200px;height:500px"><p>Pesquisando as artes mais próximas de você...</p></div>

<script src="js/exif.js"></script>
<script src="js/upload.js"></script>
<script src="js/cluster_maps.js"></script>
<script src="js/map.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRtWqPFIfu2Jepv8uVF2LLGToTHnSXRAM&signed_in=true&callback=initMap"></script>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Bloco 1 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6306326367836656"
     data-ad-slot="6827085058"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</body>
</html>
