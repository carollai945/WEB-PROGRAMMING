<?php

class AuthController extends ApplicationController
{
	public function indexAction()
	{
		$this->view->message = "";
	}
	
	public function changeaccountAction()
	{
        $request = $this->getRequest();
        $request->isPost();
        $datas = $request->getAllParams();
        $myid = ($_SESSION["loginrole"]==0)?$datas['myid']:hash('sha256', $_SESSION["loginid"]);
        $member = new Members('member');
        $sql = "select member.*,memdetail.pid from member ".
                "inner join memdetail on memdetail.mid=member.id ".
                "where SHA2(member.id, 256) = ? ";
        $data = $member->runQuery($sql,array($myid));
        $memberdata = get_object_vars($data);
        $password = $memberdata['password'];
        $account = $memberdata['username'];
        $mid = $memberdata['id'];
        $pid = $memberdata['pid'];
        $error = false;
        $msg = '';
        if($_SESSION["loginrole"]!=0 && !preg_match("/".$password."\b/i", $datas['oldPassword']) && !$_SESSION["changeAccount"] && !$_SESSION["changePassword"] )
        {
            $error = true;
            $msg .= (strlen($msg)>0)?',舊密碼錯誤':'舊密碼錯誤';            
        }
        
        //判斷需要更新帳號時，才檢查。
        if( isset($datas['newAccount']) && strlen($datas['newAccount'])>0 )
        {
            $account = $datas['newAccount'];
            if($datas['newAccount']!=$datas['confirmAccount'])
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',兩次輸入帳號不吻合':'兩次輸入帳號不吻合';
            }
            else if (!preg_match("/^[A-z][A-z0-9]{1,}$/", $datas['newAccount'])) 
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',帳號規則不符合規定':'帳號規則不符合規定';
            }
            if(preg_match("/".$pid."\b/i", $datas['newAccount']))
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',帳號不能與身分證字號相同':'帳號不能與身分證字號相同';                
            }
            //檢查帳號是否已存在
            $member = new Members('member');
            $sql = "SELECT count(*) as num FROM `member` WHERE username  = ? and SHA2(member.id, 256) <> ? ";
            $data = $member->runQuery($sql,array($datas['newAccount'],$myid));
            $memberdata = get_object_vars($data);
            if(isset($memberdata['num']) && $memberdata['num']>0 ){
                $error = true;
                $msg .= (strlen($msg)>0)?',帳號已存在':'帳號已存在';                
            }
            
            
        }
        
        //判斷需要更新密碼時，才檢查。
        if( isset($datas["newPassword"]) && strlen($datas["newPassword"])>0 )
        {
            $password = $datas['newPassword'];
            if($datas['newPassword']!=$datas['confirmPassword'])
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',兩次輸入密碼不吻合':'兩次輸入密碼不吻合';
            }
            else if (!preg_match("/^\S*(?=\S{12,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?!.*[\W])\S*$/", $datas['newPassword'])) 
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',密碼規則不符合規定':'密碼規則不符合規定';
            }
            
            if(preg_match("/".$pid."\b/i", $datas['newPassword']))
            {
                $error = true;
                $msg .= (strlen($msg)>0)?',密碼不能與身分證字號相同':'密碼不能與身分證字號相同';                
            }

        }
        
        if(preg_match("/".$account."\b/i",$password))
        {
            $error = true;
            $msg .= (strlen($msg)>0)?',密碼不能與帳號相同':'密碼不能與帳號相同';            
        }
        
        if(!$error)
        {
            $params = array();
            
            $params['id'] = $mid;
            if( isset($datas['newAccount']) && strlen($datas['newAccount'])>0 )$params['username'] = $datas['newAccount'];
            if( isset($datas["newPassword"]) && strlen($datas["newPassword"])>0 ) $params['password'] = $datas['newPassword'];
            $params['pwdupdate'] = date("Y-m-d H:i:s");
            $data = $member->save($params);
            if($data)
            {   
                $url = (isset($_SESSION['prevURL']) && ( $_SESSION["changeAccount"] || $_SESSION["changePassword"] ) )?$_SESSION['prevURL']:'';
                $result = array('status'=>'success','msg'=>'更新成功','url'=>$url); 
                if(isset($_SESSION["changeAccount"])) $_SESSION["changeAccount"]=false;
                if(isset($_SESSION["changePassword"])) $_SESSION["changePassword"]=false;
            }
            else
            {
                $result = array('status'=>'error','msg'=>'更新失敗，請聯絡系統管理者'); 
            }
            
        }
        else 
        {
            $result = array('status'=>'error','msg'=>$msg);
        }
		$this->view->renderJson($result);
	}
    

    public function getchangeaccounthtmlAction()
    {
        $myid = $this->getRequest()->getParam('myid');
        $member = new Members('member');
        $sql = "select member.*,memdetail.realname from member ".
                "inner join memdetail on memdetail.mid=member.id ".
                "where SHA2(member.id, 256) = ? ";
        $data = $member->runQuery($sql,array($myid));
        $memberdata = get_object_vars($data);
        $this->view->__set('account',$memberdata['username']);
        $this->view->__set('id',$memberdata['id']);
        $this->view->__set('realname',$memberdata['realname']);
        
    }
}
