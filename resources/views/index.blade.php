<!DOCTYPE HTML>
<html style="-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;">
<head>
    <title>Artemy - Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Portfolio">
    <!--[if lte IE 8]><script src="{{ asset('js/html5shiv.js') }}"></script><![endif]-->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <!--[if lte IE 9]><link rel="stylesheet" href="{{ asset('css/ie9.css') }}" /><![endif]-->
    <!--[if lte IE 8]><link rel="stylesheet" href="{{ asset('css/ie8.css') }}" /><![endif]-->
    <noscript><link rel="stylesheet" href="{{ asset("css/noscript.css") }}" /></noscript>
    <meta name="theme-color" content="rgba(0, 228, 255, 0.35)">
</head>
<body class="is-loading">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Main -->
    <section id="main">
        <header>
            <span class="avatar"><img width="150px" src="https://media.licdn.com/mpr/mpr/shrinknp_400_400/AAEAAQAAAAAAAAdOAAAAJGRhZDJjYTFhLWQxZWMtNDkzMS04YzEyLTM0NGY4M2RkNGJmYw.jpg" alt="" /></span>
            <h1>Artemy</h1>
            <p>Middle PHP Backend Developer</p>
        </header>
        <footer>
            <ul class="icons">
                <li><a target="_blank" href="https://telegram.me/lartie" class="fa-paper-plane">Telegram</a></li>
                <li><a target="_blank" href="https://ru.linkedin.com/in/lartie" class="fa-linkedin">LinkedIn</a></li>
                <li><a target="_blank" href="https://github.com/lartie" class="fa-github">Github</a></li>
                <li><a target="_blank" href="mailto:log.wil.log+artie.su@gmail.com?Subject=Hello." class="fa-envelope">Mail</a></li>
            </ul>
        </footer>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <ul class="copyright">
            <li>&copy; Artemy</li>
            <li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
            <li>BTC: 1GnA1EDRpNBztZx4M7y7hrzpRSsw3pVFuV</li>
        </ul>
    </footer>

</div>

<!-- Scripts -->
<!--[if lte IE 8]><script src="{{ asset('js/respond.min.js') }}"></script><![endif]-->
<script>
    if ('addEventListener' in window) {
        window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
        document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
    }
</script>

</body>
</html>