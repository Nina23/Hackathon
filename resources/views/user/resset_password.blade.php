<html>
    <head>
        <title>Frendino</title>
        <!-- image logo header-->
        <link rel="icon" type="image/png" href="image/favicon.ico">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="width:75%; border: solid 1px #eeeeee; text-align:center;font-size:14px; margin: 50px auto 0px; background-color: #eeeeee; padding: 10px; color: #111111;"> 
        </div>
        <div style="width: 75%; font-size: 14px; margin: 0px auto 0px; background-color: #fff; padding: 10px; border: solid 1px #eeeeee;">
            <div style="padding:30px 30px 0px 30px;margin:0px auto;background-color:#fff;color:#111;">
                <div style="text-align:left;padding:5px 10px;min-height:100%;margin: auto;">
                    <label style="font-weight:600;">Pošiljalac poruke</label>
                    <p>info@frendino.com</p>
                </div>
                <div style="text-align:left;padding:5px 10px;width: 80%;min-height:100%;margin: auto;">
                    <label style="font-weight:600;">E pošta pošiljaoca</label>
                    <p>{{$data['email']}}</p>
                </div>
                <div style="text-align:left;padding:5px 10px;width: 80%;min-height:100%;margin: auto;">
                    <label style="font-weight:600;">Token sa kojim mozete restartovati svoju sifru:</label>
                    <p>{{$data['token']}}</p>
                </div>
            </div>
        </div>
    </body>
</html>