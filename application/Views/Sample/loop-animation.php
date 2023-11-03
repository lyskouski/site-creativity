<html>
    <head>
        <style>
.content{
    height: 400px !important;
    /*margin: auto !important;*/
    overflow: hidden !important;
    width: 780px !important;
}

.imgholder{
    height: 400px;
    margin: auto;
    width: 780px;
}

.photo1{
    opacity: 0;
            animation: fadeinphoto 7s 1; 
       -moz-animation: fadeinphoto 7s 1; 
    -webkit-animation: fadeinphoto 7s 1; 
         -o-animation: fadeinphoto 7s 1; 
    float: left;
    position: relative;
    top: 0px;
    z-index: 1;
}

.photo2 {
    opacity: 0;
            animation: fadeinphoto 7s 5s 1;
       -moz-animation: fadeinphoto 7s 5s 1;
    -webkit-animation: fadeinphoto 7s 5s 1;
         -o-animation: fadeinphoto 7s 5s 1;
    float: left;
    position: relative;
    top: -400px;
    z-index: 1;
}
.photo3 {
    opacity:0;
            animation: fadeinphoto 7s 10s 1;
       -moz-animation: fadeinphoto 7s 10s 1;
    -webkit-animation: fadeinphoto 7s 10s 1;
         -o-animation: fadeinphoto 7s 10s 1;
    float: left;
    position: relative;
    top: -800px;
    z-index: 1;
}

.photo4 {
    opacity: 0;
    animation: fadeinphoto 7s 15s 1;
    -moz-animation: fadeinphoto 7s 15s 1;
    -webkit-animation: fadeinphoto 7s 15s 1;
    -o-animation: fadeinphoto 7s 15s 1;
    float: left;
    position: relative;
    top: -1200px;
    z-index: 1;
}

.photo5 {
    opacity: 0;
            animation: fadeinphoto 7s 20s 1;
       -moz-animation: fadeinphoto 7s 20s 1;
    -webkit-animation: fadeinphoto 7s 20s 1;
         -o-animation: fadeinphoto 7s 20s 1;
    float: left;
    position: relative;
    top: -1600px;
    z-index: 1;
}

/* Animation Keyframes*/
@keyframes fadeinphoto {
    0% { opacity: 0; }
    50% { opacity: 1; }
    100% { opacity: 0; }
}

@-moz-keyframes fadeinphoto {
    0% { opacity: 0; }
    50% { opacity: 1; }
    A100% { opacity: 0; }
}

@-webkit-keyframes fadeinphoto {
    0% { opacity: 0; }
    50% { opacity: 1; }
    100% { opacity: 0; }
}

@-o-keyframes fadeinphoto {
    0% { opacity: 0; }
    50% { opacity: 1; }
    100% { opacity: 0; }
}
        </style>
        <body>
            <div class="content">
                <div class="imgholder">
                    <img src="/1.jpg" width="780" height="400" class="photo1"/>
                    <img src="/2.jpg" width="780" height="400" class="photo2"/>
                    <img src="/3.jpg" width="780" height="400" class="photo3"/>
                    <img src="/4.jpg" width="780" height="400" class="photo4"/>
                    <img src="/5.jpg" width="780" height="400" class="photo5"/>
                </div>
            </div>
        </body>
    </head>
</html>