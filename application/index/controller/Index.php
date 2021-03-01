<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\model\Course;
use app\index\model\Coursedetail;
use app\index\model\Comment;
use think\Db;
use think\Session;

class Index extends Controller
{


    public function vcode()
    {
        Header("Content-type: image/PNG"); 
        srand((double)microtime()*1000000);//播下一个生成随机数字的种子，以方便下面随机数生成的使用
        
        //session_start();//将随机数存入session中
        $im = imagecreate(70,20) or die("Cant's initialize new GD image stream!"); //制定图片背景大小
        $black = ImageColorAllocate($im, 0,0,0); //设定三种颜色
        $white = ImageColorAllocate($im, 255,255,255); 
        $gray = ImageColorAllocate($im, 200,200,200);
        
        imagefill($im,0,0,$gray); //采用区域填充法，设定（0,0）
        $authnum='';
        
        //生成数字和字母混合的验证码方法
        $ychar="0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        $list=explode(",",$ychar);
        for($i=0;$i<4;$i++){
        $randnum=rand(0,35);
        $authnum.= $list[$randnum];
        }
        //echo $authnum;

        //while(($authnum=rand()%100000)<10000); //生成随机的五们数
        //将五位整数验证码绘入图片 
        Session::set('authnum',$authnum);
        //echo Session::get('authnum');
        imagestring($im, 5, 10, 3, $authnum, $black);
        // 用 col 颜色将字符串 s 画到 image 所代表的图像的 x，y 座标处（图像的左上角为 0, 0）。
        //如果 font 是 1，2，3，4 或 5，则使用内置字体
  
        for($i=0;$i<200;$i++) //加入干扰象素 
        {
        $randcolor = ImageColorallocate($im,rand(0,255),rand(0,255),rand(0,255));
        imagesetpixel($im, rand()%70 , rand()%30 , $randcolor); 
        } 
        imagepng($im);
        ImageDestroy($im);

    }
    public function index()
    {
        $username=Session::get('name');
        $this->assign('user_name',$username);
        return $this->fetch();
    }
    public function signin()
    {
        $username=Session::get('name');
        $this->assign('user_name',$username);
        return $this->fetch();
    }

    public function team()
    {

        $username=Session::get('name');
        $this->assign('user_name',$username);
        return $this->fetch();
    }
    public function search()
    {
        $result=0; 
        $this->assign('list',$result);
        $username=Session::get('name');
        $this->assign('user_name',$username);
        if(Session::has('name'))
        {
            return $this->fetch();
        }
        
        else
        {
            echo "<script>alert('请先登录哦！')</script>";
            return $this->fetch('index');
        }
    }
    public function searchcourse()
    {
        $username=Session::get('name');
        $this->assign('user_name',$username);
        if(Session::has('name'))
        {
            $where=array(
                "username"=>$username,
            );                                      //查询条件
            $member=Db::table('think_course')
            ->where($where)
            ->select();                             //查询过程
            $this->assign('list',$member); 
            $user1=new User;
            $userCount = $user1->count();

            $this->assign('num1',$userCount);
        }
        /* Session::set('search',$username);*/
        $this->assign('user_name',$username);
        $theweek=(int)$_POST['week'];
        $coursetable=$_POST['coursetable'];
        if($theweek<1||$theweek>20)
        {
            $this->error('请输入1~20之间的一个数哦！');
            return $this->fetch('index/main');
        }
        $where=array(
            "username"=>$username,
            "termname"=>$coursetable
        );                                      //查询条件
        $member=Db::table('think_coursedetail')
        ->where($where)
        ->order('dayofweek')
        ->select();                               //查询过程
        for($j=0;$j<7;$j++)
        {
            $result1[$j]=0;
            $result2[$j]=0;
            $result3[$j]=0;
            $result4[$j]=0;
            $result5[$j]=0;
            $result6[$j]=0;
            $result7[$j]=0;
            $result8[$j]=0;
            $result9[$j]=0;
            $result10[$j]=0;
        }
        for($i=0;$i<count($member);$i++)
        {
            /* dump($weekarry); */
                $weekarry=explode(',',$member[$i]['week']);
                if($weekarry[$theweek-1]=="1")
                { 
                    
                    for($j=0;$j<7;$j++)
                    {
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=1&&$member[$i]['end']>=1))
                            $result1[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=2&&$member[$i]['end']>=2))
                            $result2[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=3&&$member[$i]['end']>=3))
                            $result3[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=4&&$member[$i]['end']>=4))
                            $result4[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=5&&$member[$i]['end']>=5))
                            $result5[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=6&&$member[$i]['end']>=6))
                            $result6[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=7&&$member[$i]['end']>=7))
                            $result7[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=8&&$member[$i]['end']>=8))
                            $result8[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=9&&$member[$i]['end']>=9))
                            $result9[$j]=$member[$i];
                        if($member[$i]['dayofweek']==($j+1)&&($member[$i]['start']<=10&&$member[$i]['end']>=10))
                            $result10[$j]=$member[$i];
                    }
                }
            
        }
        $this->assign('list1',$result1);
        $this->assign('list2',$result2);
        $this->assign('list3',$result3); 
        $this->assign('list4',$result4); 
        $this->assign('list5',$result5);  
        $this->assign('list6',$result6);  
        $this->assign('list7',$result7);
        $this->assign('list8',$result8);
        $this->assign('list9',$result9);
        $this->assign('list10',$result10); 

       return $this->fetch('index/main');
    }
    public function comment()
    {
        $do = new Comment;
        $username=Session::get('name');
        $this->assign('user_name',$username);
        $member=Db::table('think_comment')
        ->order('time desc')
        ->select();
        $this->assign('list',$member);
        return $this->fetch();
    }
    public function add()
    {
        if($_POST)
        {
            $userModel=new User;

            $name  =  $_POST['username'];
            $pass  =  $_POST['password'];
            $email  =  $_POST['email'];
        if(strcasecmp($_POST['vcode'],Session::get('authnum')))
        {
            echo "<script>alert('验证码错误！');
            </script>";
            return $this->fetch('signin');
        }
        else if(!$_POST||!$name||!$pass||!$email)
        {
            echo "<script>alert('请不要输入空值哦！');
            </script>";
            return $this->fetch('signin');
        }
        else if($userModel->get($name)){
            echo "<script>alert('对不起，这个用户名已经被注册了哦！');
            </script>";
        return $this->fetch('signin');
        }
        else{
        $userModel->save([

            'username' => $name,
            'password' => md5($pass),
            'email' => $email
        ]);

        Session::set('name',$name);
        $username=Session::get('name');
        $this->assign('user_name',$username);
        return $this->redirect('main');
        }
    }
        else return $this->fetch('index');

}
    public function login()
    {
    $userModel=new User;
     $name  =  $_POST['username'];
     $pass  =  $_POST['password'];
     $email  =  $_POST['email'];
     $dbname =$userModel->get($name);
     if($email)
     {
         echo "<script>alert('OK! 你已经完成了找回密码的第一步了。找回密码的第二步是联系管理员，你现在可以联系管理员重置密码了，他的QQ是：750295736');
         </script>";
         return $this->fetch('index');
     }
     else if(strcasecmp($_POST['vcode'],Session::get('authnum')))
     {
         echo "<script>alert('验证码错误！');
         </script>";
         return $this->fetch('index');
     }
     else if(!$name||!$pass)
     {
         echo "<script>alert('请不要输入空值哦！');
         </script>";
         return $this->fetch('index');
         
     }
     
     else if(!$dbname)
     {
        echo "<script>alert('这个用户不存在哦！')</script>";
        return $this->fetch('index');
     }
     else
     {
       if(md5($pass)==$dbname->password)
        {
            Session::set('name',$name);
            $username=Session::get('name');
            $this->assign('user_name',$username);
            return $this->redirect('main');
        }
        else {
         echo "<script>alert('密码错误！请再次输入密码！')</script>";
        return $this->fetch('index');
         }
     }
    }
    public function main()
    {
            $result1=0;
            $result2=0;
            $result3=0;
            $result4=0;
            $result5=0;
            $result6=0;
            $result7=0;
            $result8=0;
            $result9=0;
            $result10=0; 
            $this->assign('list1',$result1);
            $this->assign('list2',$result2);
            $this->assign('list3',$result3);
            $this->assign('list4',$result4);
            $this->assign('list5',$result5);
            $this->assign('list6',$result6);
            $this->assign('list7',$result7);
            $this->assign('list8',$result8);
            $this->assign('list9',$result9);  
            $this->assign('list10',$result10);
               /*  $where=array(
                    "username"=>$username,
                    "termname"=>$coursetable
                );                                      //查询条件
                $result1=Db::table('think_coursedetail')
                ->where($where)
                ->select();   */

        $username=Session::get('name');
        $this->assign('user_name',$username);
        if(Session::has('name'))
        {
            $where=array(
                "username"=>$username,
            );                                      //查询条件
            $member=Db::table('think_course')
            ->where($where)
            ->select();                             //查询过程
            $this->assign('list',$member); 
            $user1=new User;
            $userCount = $user1->count();

            $this->assign('num1',$userCount);
            return $this->fetch();
        }
        
        else
        {
            echo "<script>alert('请先登录哦！')</script>";
            return $this->fetch('index');
        }
    }
    public function logout()
    {
        Session::delete('name');
        return $this->fetch('index');
    }
    public function personrange()
    {
        $username=Session::get('name');
        $this->assign('user_name',$username);
        if(Session::has('name'))
        $member=Db::table('think_user')
        ->order('score desc')
        ->select();
        $this->assign('list',$member);
        return $this->fetch();
    }


    public function chpass()
    {
    $username=Session::get('name');
    $userModel=new User;
     $old  =  $_POST['old'];
     $new1  =  $_POST['new1'];
     $new2  =  $_POST['new2'];
     $dbname = $userModel->get($username);
    /*  echo($name);
      echo($pass);
      echo($email); */
       if(md5($old)==$dbname->password)
        {
            $dbname->password=md5($new1);
            $dbname->save();
            $this->success('修改成功！');
            return $this->redirect('index/team');
        }
        else {
            $this->error('密码错误，再试试吧！');
            return $this->redirect('index/team');
        }
    }
    
    public function addcomment()
    {
        $username=Session::get('name');
        $str  =  $_POST['message'];
        $content = htmlspecialchars($str);
        $showtime=date("Y-m-d H:i:s");
        if($_POST)
        {
            $do = new Comment;
            $do->data([
                'username' => $username,
                'content' => $content,
                'time' => $showtime
            ]);
            $do->save();
            return $this->redirect('index/comment');
        }
    }
    public function searchbyroom()
    {
        $username=Session::get('name');
        $this->assign('user_name',$username);
        $room  =  $_POST['room'];
        $coursemodel=new Coursedetail;
        $where=array(
            "username"=>$username,
            "place"=>$room
        );                                      //查询条件
        $member=Db::table('think_coursedetail')
        ->where($where)
        ->select();
        if(!$member)
        {
            $this->error('没找到记录。。。');
            return $this->redirect('index/search');
        }
        else{
            $this->assign('num',count($member));
            $this->assign('list',$member);
            return $this->fetch('index/search');
        }

    }
    public function searchbyteacher()
    {

        $username=Session::get('name');
        $this->assign('user_name',$username);
        $room  =  $_POST['teacher'];
        $coursemodel=new Coursedetail;
        $where=array(
            "username"=>$username,
            "teacher"=>$room
        );                                      //查询条件
        $member=Db::table('think_coursedetail')
        ->where($where)
        ->select();
        if(!$member)
        {
            return $this->redirect('index/search');
        }
        else{
            $this->assign('num',count($member));
            $this->assign('list',$member);
            return $this->fetch('index/search');
        }
    }

}