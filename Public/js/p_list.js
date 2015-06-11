(function (w){
    /*const*/
    console.log(type_path);
    var D = w.document, EACH = [].forEach, B = D.body, isLoading = false/*���ڼ���*/;
    //main
    D.addEventListener('DOMContentLoaded', function(){
        var $sa = $d('.js-s_all')[0],
            $sw = $d('.js-w_all')[0],
            $sb = $d('.js-sub')[0],
            $s = $d('dd',$sw),
            $lw = $d('.js-w_l')[0],
            $load = $d('.js-loading')[0],
            $menu = $d('.u-menu')[0],
            $sc = $d('.u-s_c')[0];
        var ZAC = 'z-active'/*const ״̬*/;
        var a_t = $s[0].getAttribute('data-id'),
            m_t = $s[2].getAttribute('data-id'),
            w_t = $s[3].getAttribute('data-id');
        //console.log(m_t, w_t);
        var types = [a_t]/*Ĭ�ϵ�һ��*/;
        $load.style.display = 'none';
        //�򿪵���
        $sa.addEventListener('touchstart', /*function(){
            $sw.style.display = 'block';
            $sb.style.display = 'block';
            $menu.style.display   = 'none';
            $sc.style.display   = 'block';
        }*/tgoT);
        $d('.js-close')[0].addEventListener('touchstart', /*function(){
            $sw.style.display = 'none';
            $sb.style.display = 'none';
            $menu.style.display   = 'block';
            $sc.style.display   = 'none';
        }*/tgcT);
        //ȷ�����
        $sb.addEventListener('touchstart', function(){
            var data = {
                type : types
            };
            //$sw.style.display = 'none';
            //$sb.style.display = 'none';
            //$menu.style.display   = 'block';
            //$sc.style.display   = 'none';
            tgcT();
            $load.style.display = 'block';
            ajax({
                url : type_path,
                method : 'POST',
                data : JSON.stringify(data)
            }, function(res){
                getSource(res, function(w, tmp){
                    w.innerHTML = tmp;
                });
            });
        });

        function tgoT(){
            $sw.style.display = 'block';
            $sb.style.display = 'block';
            $menu.style.display   = 'none';
            $sc.style.display   = 'block';
        }

        function tgcT(){
            $sw.style.display = 'none';
            $sb.style.display = 'none';
            $menu.style.display   = 'block';
            $sc.style.display   = 'none';
        }

        //ѡ�����
        EACH.call($s, function($e,i){
            $e.addEventListener('touchstart', function(){
                var $self = this, c_t = $self.getAttribute('data-id');
                var aindex = types.indexOf(a_t);
                if( $self.classList.contains(ZAC) ){
                    if( types.indexOf(c_t) !== -1 && types.length == 1 ) return false;
                    $self.classList.remove(ZAC);
                    var index = types.indexOf(c_t);
                    types.splice(index,1);
                }else{
                    if(aindex!== -1 &&  c_t !== a_t) {
                        $s[0].classList.remove(ZAC);
                        types.splice(aindex,1);
                        //if(){}
                    }
                    else if( c_t === a_t){
                        EACH.call($s, function($e,i){
                            if(i !== 0) $e.classList.remove(ZAC);
                        });
                        types.length = 0;
                    }
                    $self.classList.add(ZAC);
                    $self.classList.add(c_t);
                    types.push(c_t);
                    if(types.indexOf(m_t) !== -1 && types.indexOf(w_t) !== -1){
                        if(c_t === m_t){
                            types.splice( types.indexOf(w_t)  ,1);
                            $s[3].classList.remove(ZAC);
                        }else if(c_t === w_t){
                            types.splice( types.indexOf(m_t) ,1);
                            $s[2].classList.remove(ZAC);
                        }
                    }
                }
            });
        });

        function getSource(res, cb){
            var hps = [];
            if(typeof res !== 'object'){
                try{
                    res = JSON.parse(res);
                }catch(err){
                    console.warn(err);
                    alert('数据出现错误!!!');
                }
            }
            //console.log(res);\
            res.data.forEach(function(d){
                hps.push(
                    '<div class="u-item f-cb">' +
                    '<a href="'+ people+'?id='+d.id+'">'+
                    '<i class="s-icon" style="background-image: url('+d.photo+')"></i>'+
                    '<aside>'+
                    '<h2 class="name"><span>'+ ( !d.name ? '他还没填名字' : d.name)+'</span> <i class="'+ (d.sex == '女' ? 'i_w' : 'i_m') +'"></i></h2>'+
                    '<span class="trip">'+ d.stuid+'</span>'+
                    '<span class="trip">'+ ( !d.college ? '他还没填学院' : d.college)+'</span>'+
                    '</aside>'+
                    '</a>'+
                    '</div>'
                );
            });
            cb($lw, hps.join(''));
            $load.style.display = 'none';
            if(!res.data.length){
                alert('没有匹配到符合的人!!!');
                return false;
            }
            //console.log('success');
        }

        //滑动加载
        var PAGE = 1;
        B.addEventListener('touchmove', function(){
            var hh = $lw.offsetHeight, sh = B.scrollTop, ch = D.documentElement.clientHeight;
            if(hh == sh + ch && !isLoading){
                isLoading = true;
                $load.style.display = 'block';
                ajax({
                    url : type_path,
                    method : 'POST',
                    data : JSON.stringify({page : ++PAGE})
                }, function(res){
                    $load.style.display = 'none';
                    getSource(res, function(w, tmp){
                        var dw = document.createElement('div');
                        dw.innerHTML = tmp;
                        //console.log(dw);
                        EACH.call(dw.children, function(ele,i){
                            w.appendChild(ele);
                        });
                        isLoading = false;
                    });
                });
            }
        });
    });


    //query dom node
    function $d(s, c){
        return (c?c:D).querySelectorAll(s);
    }

    //ajax
    function ajax(config, cb){
        var xhr = new XMLHttpRequest();
        xhr.open(config.method, config.url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(config.data);
        xhr.addEventListener('readystatechange', function(){
            if(xhr.readyState == 4){
                if(xhr.status == 200) cb(xhr.responseText);
                else alert(xhr.status + '加载错误!!');
            }
        });
    }
})(window);





