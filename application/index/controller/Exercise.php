<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\model\Comment;
use app\index\model\Course;
use app\index\model\Coursedetail;
use think\Db;
use think\Session;
class Exercise extends Controller
{
    public function exam()
    {
        $user = Problem::get(1);
        echo $user->key;
        $this->assign('title',$user->key);
        $this->assign('user_name',$user->key);
        return $this->fetch('web');
    }
    public function web()
    {
        if(Session::has('name'))//判断是否登录
        {

            //得到session里的用户名
            $username=Session::get('name');
            $this->assign('user_name',$username);//替换输出用户名
            $where=array(
                "username"=>$username,
            );                                      //查询条件
            $member=Db::table('think_course')
            ->where($where)
            ->select();                             //查询过程
            $this->assign('list',$member);          //查询结果赋给list
            return $this->fetch();
        }
        else
        {
            echo "<script>alert('请先登录哦！')</script>";
            return $this->redirect('index/index');
        }
    }
    public function logout()
    {
        Session::delete('name');
        return $this->redirect('index/index');
    }

    public function addtable()
    {
        if(Session::has('name'))
        {
            if($_POST)
            {
                $username=Session::get('name');
                $coursetable = $_POST['coursetable'];
                $course=new Course;

                $where=array(
                    "username"=>$username,
                    "termname"=>$coursetable
                );
                $member=Db::table('think_course')
                ->where($where)
                ->select();
                if(!$member)
                {
                    $course->save([

                        'username' => $username,
                        'termname' => $coursetable
                    ]);
                    $this->success('添加课程成功！');
                    return $this->fetch('exercise/web');
                }
                else{
                    $this->error('这个课程已经添加过了哦！');
                }

            }
            else{
                return $this->fetch('exercise/web');
            }
        }
        else{
            echo("请先登录哦!");
            return $this->redirect('index');
        }

    }
    public function deletetable()
    {
        $username=Session::get('name');
        if(!empty($_POST['coursetable'])){
            $coursetable = $_POST['coursetable'];
            Course::where('termname','=',$coursetable)->where('username','=',$username)->delete();
            Coursedetail::where('termname','=',$coursetable)->where('username','=',$username)->delete();
            $this->success('删除课程表成功！');
        }
        else $this->error('没有课程表了，删除失败！');
    }
    public function addcourse()
    {/* dump($_POST); */
        if(Session::has('name'))
        {
            
            if($_POST)
            {
                $username=Session::get('name');//用户名
                $coursetable = $_POST['termname'];//接收学期名
                $coursename = $_POST['coursename'];//接收课程名
                $place = $_POST['classroom'];//上课教室
                $teacher = $_POST['teacher'];//上课老师
                $start = (int)$_POST['start'];//开始节数
                $end = (int)$_POST['end'];//结束节数
                if(!empty($_POST['options']))
                $selectweek = $_POST['options'];//单双周
                else $selectweek=0;
                if(!empty($_POST['checkbox']))
                $chooseweek = $_POST['checkbox'];//选择的周数
                else $chooseweek=0;
                $dayofweek = (int)$_POST['dayofweek'];//周几的课

                $course=new Coursedetail;//实例化模型

                $where=array(
                    "username"=>$username,
                    "termname"=>$coursetable,
                    "dayofweek"=>$dayofweek
                );
                $member=Db::table('think_coursedetail')
                ->where($where)
                ->select();//选择这个用户在这个学期名下，每个星期的这天，上的课，为了后面判断课程是否冲突
                    //构建上课周数
                    for($i=0;$i<20;$i++)
                    {
                        if(($i%2==0&&$selectweek=="single")||($i%2!=0&&$selectweek=="double")||($selectweek=="all"))
                            $timearry[$i]=1;
                        else
                            $timearry[$i]=0;
                    }
                for($i=0;$i<count($chooseweek)&&$selectweek!="all";$i++)
                {
                    $k=(int)$chooseweek[$i];
                    $timearry[$k-1]=1;
                }
                if($member)//如果不存在上述的课的话
                {
                   //判断时间是否冲突

                   for($i=0;$i<count($member);$i++)
                   {
                       $weekarry=explode(',',$member[$i]['week']);
                       /* dump($weekarry); */
                       for($k=0;$k<20;$k++)
                       {
                           if($weekarry[$k]=='1'&&$timearry[$k]=='1')
                           {
                               if(($member[$i]['start']<=$end&&$member[$i]['start']>=$start)
                               ||($start<=$member[$i]['end']&&$start>=$member[$i]['start']))
                               {
                                   $this->error('添加的课程与之前的课程时间有冲突！');
                                   return 0;
                               }
                           }
                       }
                   }
                }
                if($start>$end)
                {
                    $mid=$end;
                    $end=$start;
                    $start=$mid;
                }
                $timestr=implode(",",$timearry); 
                $course->data([
                    'username' => $username,
                    'course' =>$coursename,
                    'place' => $place,
                    'teacher' => $teacher ,
                    'termname' => $coursetable,
                    'week' =>$timestr,
                    'start' => $start,
                    'end' => $end,
                    'dayofweek'=>$dayofweek 
                ]);
                $course->save();
                $this->success('添加课程成功！');
                return $this->fetch('exercise/web'); 
            }
            else{
                echo "<script>alert('请不要输入空值哦！')</script>";
                return $this->fetch('exercise/web');
            }
        }
        else{
            echo "<script>alert('请先登录哦！')</script>";
            return $this->fetch('index/index');
        }
    }
    public function deletecourse()
    {
        $username=Session::get('name');
        $coursetable = $_POST['coursetable'];
        $coursename = $_POST['coursename'];
        $where=array(
            "username"=>$username,
            "termname"=>$coursetable,
            "course"=>$coursename
        );
        $member=Db::table('think_coursedetail')
        ->where($where)
        ->select();
        if($member)
        {
            Db::table('think_coursedetail')
            ->where($where)
            ->delete();
            $this->success('删除课程成功！');
        }
        else 
        {
            $this->error('没找到这个课程哦');
        }
    }
    public function updatecourse()
    {
        $username=Session::get('name');
        $coursetable = $_POST['coursetable'];
        $newcoursename = $_POST['newcoursename'];
        $oldcoursename=$_POST['oldcoursename'];
        $teacher = $_POST['teacher'];
        $place = $_POST['place'];
        $start=$_POST['start'];
        $end=$_POST['end'];
        $dayofweek=$_POST['dayofweek'];
        $course = new Coursedetail;
        $where=array(
            "username"=>$username,
            "termname"=>$coursetable,
            "course"=>$oldcoursename
        );
        $member=Db::table('think_coursedetail')
        ->where($where)
        ->select();
        if($member)
        {
            $course->where('termname', $coursetable)
            ->where('username',$username)
            ->where("course",$oldcoursename)
            ->update(['course'=>$newcoursename,'teacher' => $teacher,'place'=>$place,'start'=>$start,'end'=>$end,'dayofweek'=>$dayofweek]);
            $this->success('修改成功');
        }
        else 
        {
            $this->error('没找到这个课程哦');
        }
    }

}