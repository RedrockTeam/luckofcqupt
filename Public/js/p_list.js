(function (w){
    /*const*/
    var D = w.document, EACH = [].forEach, B = D.body;
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
        var types = [a_t]/*Ĭ�ϵ�һ��*/;
        //滑动加载 参数
        var PAGE = 1, isLoading = false, sy = 0/*保存touch pageY*/;
        $load.style.display = 'none';
        //打开选择框
        $sa.addEventListener('touchstart', tgoT);
        $d('.js-close')[0].addEventListener('touchstart', tgcT);
        //关闭选择框并加载
        $sb.addEventListener('touchstart', function(){
            var data = {
                type : types
            };
            tgcT();
            $load.style.display = 'block';
            ajax({
                url : type_path,
                method : 'POST',
                data : JSON.stringify(data)
            }, function(res){
                getSource(res, '未找到匹配的数据!!!', function(w, tmp){
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

        //选择type加载
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

        function getSource(res, einfo, cb){
            var hps = [];
            if(typeof res !== 'object'){
                try{
                    res = JSON.parse(res);
                }catch(err){
                    console.warn(err);
                    alert('数据出现错误!!!');
                }
            }
            res.data.forEach(function(d){
                if (d.is_dog == 0) {
                    hps.push(
                        '<div class="u-item f-cb">' +
                        '<a href="'+ people+'?id='+d.id+'">'+
                        '<i class="s-icon" style="background-image: url('+d.photo+')"></i>'+
                        '<aside>'+
                        '<h2 class="name"><span>'+ ( !d.name ? '他还没填名字' : d.name)+'</span> <i class="'+ (d.sex == '女' ? 'i_w' : 'i_m') +' iconfont">' + (d.sex == '男' ? '&#xe604;' : '&#xe603;') + '</i><span class="s-sl">距离   '+d.distance+'米<!--空格不要省略--></span></h2>'+
                        '<span class="trip">'+ d.stuid+'</span>'+
                        '<span class="trip">'+ ( !d.college ? '他还没填学院' : d.college)+'</span>'+
                        '</aside>'+
                        '</a>'+
                        '</div>'
                    );
                } else {
                    hps.push(
                        '<div class="u-item f-cb">' +
                        '<a href="'+ people+'?id='+d.id+'">'+
                        '<i class="s-icon" style="background-image: url('+d.photo+')"></i>'+
                        '<aside>'+
                        '<h2 class="name"><span>'+ ( !d.name ? '他还没填名字' : d.name)+'</span> <i class="'+ (d.sex == '女' ? 'i_w' : 'i_m') +' iconfont">' + (d.sex == '男' ? '&#xe604;' : '&#xe603;') + '</i><i class="i_dog">未脱单</i><span class="s-sl">距离   '+d.distance+'米<!--空格不要省略--></span></h2>'+
                        '<span class="trip">'+ d.stuid+'</span>'+
                        '<span class="trip">'+ ( !d.college ? '他还没填学院' : d.college)+'</span>'+
                        '</aside>'+
                        '</a>'+
                        '</div>'
                    );
                }
                
            });
            cb($lw, hps.join(''));
            $load.style.display = 'none';
            if(!res.data.length){
                alert(/*'没有匹配到符合的人!!!'*/einfo);
                return false;
            }
        }
        //滑动加载
        window.addEventListener('scroll', function(ev){
            if( isb() && !isLoading ){
                isLoading = true;
                $load.style.display = 'block';
                ajax({
                    url : type_path,
                    method : 'POST',
                    data : JSON.stringify({page : ++PAGE, is_dog : isDog})
                }, function(res){
                    $load.style.display = 'none';
                    getSource(res, '已经加载完了哦!!!!!',function(w, tmp){
                        var dw = document.createElement('div');
                        dw.innerHTML = tmp;
                        EACH.call(dw.children, function(ele,i){
                            w.appendChild(ele);
                        });
                        isLoading = false;
                    });
                });
            }
        });

        //判断是否滑到底
        function isb(){
            var hh = $lw.offsetHeight, sh = B.scrollTop, ch = D.documentElement.clientHeight;
            return hh === (sh + ch);
        }
    });


    //query dom node
    function $d(s, c){
        return (c?c:D).querySelectorAll(s);
    }


    //ajax
    function ajax(config, cb){
        var xhr = new XMLHttpRequest();
        xhr.open(config.method, config.url);
        xhr.setRequestHeader('Content-Type', 'application/json');/*重置header*/
        xhr.send(config.data);
        xhr.addEventListener('readystatechange', function(){
            if(xhr.readyState == 4){
                if(xhr.status == 200) cb(xhr.responseText);
                // else alert(xhr.status + '加载错误!!');
            }
        });
    }
})(window);





