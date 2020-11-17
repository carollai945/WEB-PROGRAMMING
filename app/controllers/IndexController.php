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
		$this->view->message = "";
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
