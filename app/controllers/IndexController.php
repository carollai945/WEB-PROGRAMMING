<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class IndexController extends Controller 
{
	public function indexAction()
	{
		$this->view->message = "";
	}
    
	public function report1Action()
	{
        //只要加上$this->view->message ，程式會自動抓取app/views/scripts/controller相同名稱資料夾/action相同的檔名.phml
        //例如app/views/scripts/index/report1.phtml
		$this->view->message = "";
        
        //透過models的物件，讀取資料庫資料
        $borrowing = new Borrowing();
        $data = $borrowing->getReport1();
        
        //設定app/views/scripts/index/report1.phtml需要的變數，phtml檔案的用法 $this->_data['reportData'];
        $this->view->__set('reportData',$data);

	}
    
	public function borrowingAction()
	{
		$this->view->message = "";
	}
    
    public function getAllBorrowingDataAction()
    {
        // $request = $this->getRequest();
        // $request->isPost();
        // $datas = $request->getAllParams();
        // $keyword = (isset($datas['keyword']))?$datas['keyword']:'';
        $borrowing = new Borrowing();
        $data = $borrowing->getAllBorrowingData();
        
        $this->view->renderJson($data);
    }
    
    public function getQueryBorrowingDataAction()
    {
        $request = $this->getRequest();
        $request->isPost();
        $datas = $request->getAllParams();
        $keyword = (isset($datas['keyword']))?$datas['keyword']:'';
        
        $borrowing = new Borrowing();
        $data = $borrowing->getQueryBorrowingData($keyword);
        
        $this->view->renderJson($data);
    }
    
    public function getReport1Action()
    {
        
        $borrowing = new Borrowing();
        $data = $borrowing->getReport1();

        $this->view->renderJson($data);
    }
}
