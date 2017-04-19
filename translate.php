<script type="text/javascript">

    onload = function ()
    {
        var from = "en", to = "es", text = "hello world";

        var s = document.createElement("script");
        s.src = "http://api.microsofttranslator.com/V2/Ajax.svc/Translate" +
            "?appId=Bearer " + encodeURIComponent("http%3a%2f%2fschemas.xmlsoap.org%2fws%2f2005%2f05%2fidentity%2fclaims%2fnameidentifier=CNEX2015&http%3a%2f%2fschemas.microsoft.com%2faccesscontrolservice%2f2010%2f07%2fclaims%2fidentityprovider=https%3a%2f%2fdatamarket.accesscontrol.windows.net%2f&Audience=http%3a%2f%2fapi.microsofttranslator.com&ExpiresOn=1443271125&Issuer=https%3a%2f%2fdatamarket.accesscontrol.windows.net%2f&HMACSHA256=eWAQVqOeDUUz%2f0EYvp2NNRTPnNOqJ%2b5hbOVOSHU%2bqRg%3d") +
            "&from=" + encodeURIComponent(from) +
            "&to=" + encodeURIComponent(to) +
            "&text=" + encodeURIComponent(text) +
            "&oncomplete=mycallback";
        alert(s.src);
        document.body.appendChild(s);
    }

    function mycallback(response)
    {
        alert(response);
    }

</script>